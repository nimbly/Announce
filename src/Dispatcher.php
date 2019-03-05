<?php declare(strict_types=1);

namespace Announce;

use Announce\Event;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * @package Announce
 */
class Dispatcher implements EventDispatcherInterface, ListenerProviderInterface
{
    /**
     * Registered subscribers, indexed by event name.
     *
     * @var array<string, array>
     */
    protected $subscriptions = [];

    /**
     * Register subscribers.
     *
     * @param array<string> $subscribers
     * @return void
     */
    public function register(array $subscribers): void
    {
        foreach( $subscribers as $subscriber ){
            call_user_func([new $subscriber, 'register'], $this);
        }
    }

    /**
     * Register an event name(s) to a listener.
     *
     * @param string|array<string> $eventName
     * @param callable $handler
     */
    public function listen($eventName, callable $handler): void
    {
        if( !is_array($eventName) ){
            $eventName = [$eventName];
        }

        foreach( $eventName as $event ){
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

            call_user_func($handler, $event);
        }

        $this->broadcastEvent($event);

        return $event;
    }

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        if( $event instanceof Event ){
            $eventName = $event->getName();
        }
        else {
            $eventName = get_class($event);
        }

        return $this->subscriptions[$eventName] ?? [];
    }

    /**
     * Broadcast the event, if applicable.
     *
     * @param object $event
     * @return void
     */
    protected function broadcastEvent(object $event): void
    {
        if( $event instanceof BroadcastableEvent ){
            call_user_func([$event, 'broadcast']);
        }
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