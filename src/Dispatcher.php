<?php declare(strict_types=1);

namespace Nimbly\Announce;

use DomainException;
use Nimbly\Announce\Event;
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
	/**
	 * Registered subscriptions.
	 *
	 * @var array<string,array<callable>>
	 */
	protected array $subscriptions;

	/**
	 * Resolve dependency resolution library.
	 */
	protected Resolve $resolve;

	/**
	 * Dispatcher constructor.
	 *
	 * @param array<class-string|object> $subscribers
	 * @param ContainerInterface|null $container
	 */
	public function __construct(
		array $subscribers,
		?ContainerInterface $container = null)
	{
		$this->resolve = new Resolve($container);
		$this->subscriptions = [];

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
			$subscriberInstance = $this->resolve->make($reflectionClass->getName());

			$reflectionMethods = $reflectionClass->getMethods();

			foreach( $reflectionMethods as $reflectionMethod ){

				$reflectionAttributes = $reflectionMethod->getAttributes(Subscribe::class);

				if( empty($reflectionAttributes) ){
					throw new DomainException("A subscriber was given with no Subscribe attributes.");
				}

				foreach( $reflectionAttributes as $reflectionAttribute ) {

					/**
					 * @var Subscribe $subscription
					 */
					$subscription = $reflectionAttribute->newInstance();

					foreach( $subscription->getEvents() as $event ){
						$this->subscriptions[$event][] = [
							$subscriberInstance,
							$reflectionMethod->getName()
						];
					}
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
	 */
	public function dispatch(object $event): object
	{
		foreach( $this->getListenersForEvent($event) as $handler ){

			if( $this->shouldPropagationStop($event) ){
				break;
			}

			$this->resolve->call(
				$handler,
				[\get_class($event) => $event]
			);
		}

		return $event;
	}

	/**
	 * @inheritDoc
	 * @return array<callable>
	 */
	public function getListenersForEvent(object $event): iterable
	{
		if( $event instanceof Event ){
			$eventName = $event->getName();
		}
		else {
			$eventName = \get_class($event);
		}

		$listeners = \array_merge(
			$this->subscriptions[$eventName] ?? [],
			$this->subscriptions["*"] ?? []
		);

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
}