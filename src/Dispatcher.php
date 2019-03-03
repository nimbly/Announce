<?php

namespace Announce;


class Dispatcher
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
     * Trigger an event.
     *
     * @param Event $event
     */
    public function trigger(Event $event): void
    {
        if( array_key_exists($event->getName(), $this->subscriptions) &&
            is_array($this->subscriptions[$event->getName()]) ){

            foreach( $this->subscriptions[$event->getName()] as $handler ){

                if( !$event->shouldPropagate() ){
                    break;
                }

                if( !is_callable($handler) ){
                    throw new \Exception("Event handler is not callable.");
                }

                call_user_func($handler, $event);

            }
        }
    }
}