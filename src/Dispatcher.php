<?php declare(strict_types=1);

namespace Nimbly\Announce;

use Nimbly\Resolve\Resolve;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

class Dispatcher implements EventDispatcherInterface, ListenerProviderInterface
{
	use Resolve;

	/**
	 * Registered subscriptions.
	 *
	 * @var array<string,array<callable>>
	 */
	protected array $subscriptions = [];

	/**
	 * Dispatcher constructor.
	 *
	 * @param array<class-string|object> $subscribers
	 * @param ContainerInterface|null $container
	 */
	public function __construct(
		array $subscribers = [],
		protected ?ContainerInterface $container = null)
	{
		foreach( $subscribers as $subscriber ){

			try {

				$reflectionClass = new ReflectionClass($subscriber);
			}
			catch( ReflectionException $reflectionException ){
				throw new UnexpectedValueException(
					"Subscriber must be a class string or object.",
					0,
					$reflectionException
				);
			}

			/**
			 * Create an instance of the subscriber to use.
			 */
			$subscriberInstance = $this->make($reflectionClass->getName(), $this->container);

			$reflectionMethods = $reflectionClass->getMethods();

			if( empty($reflectionMethods) ){
				throw new UnexpectedValueException("Given subscriber has no methods: " . $reflectionClass->getName());
			}

			foreach( $reflectionMethods as $reflectionMethod ){

				$reflectionAttributes = $reflectionMethod->getAttributes(Subscribe::class);

				foreach( $reflectionAttributes as $reflectionAttribute ) {

					if( !$reflectionMethod->isPublic() ){
						throw new UnexpectedValueException("Event handler methods must be public.");
					}

					/**
					 * @var Subscribe $subscription
					 */
					$subscription = $reflectionAttribute->newInstance();

					$this->listen(
						$subscription->getEvents(),
						[$subscriberInstance, $reflectionMethod->getName()]
					);
				}
			}
		}
	}

	/**
	 * Register an event name(s) to a callable handler.
	 *
	 * @param string|array<string> $event_name An event name or array of event names.
	 * @param callable $handler
	 */
	public function listen(string|array $event_name, callable $handler): void
	{
		if( !\is_array($event_name) ){
			$event_name = [$event_name];
		}

		foreach( $event_name as $event ){
			$this->subscriptions[$event][] = $handler;
		}
	}

	/**
	 * @inheritDoc
	 * @return array<callable>
	 */
	public function getListenersForEvent(object $event): iterable
	{
		$listeners = [];
		foreach( $this->subscriptions as $event_name => $subscribers ){
			if( $event_name === "*" ||
				$event instanceof $event_name ) {
				$listeners = \array_merge(
					$listeners,
					$subscribers
				);
			}
		}

		return $listeners;
	}

	/**
	 * Should event propagation stop?
	 *
	 * @param object $event
	 * @return boolean
	 */
	protected function shouldPropagationStop(object $event): bool
	{
		if( $event instanceof StoppableEventInterface ) {
			return $event->isPropagationStopped();
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch(object $event): object
	{
		foreach( $this->getListenersForEvent($event) as $handler ){

			if( $this->shouldPropagationStop($event) ){
				break;
			}

			$this->call($handler, $this->container,	[\get_class($event) => $event]);
		}

		return $event;
	}
}