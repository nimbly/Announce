<?php

namespace Announce;


class Dispatcher
{
    /**
     * Registered subscribers, indexed by event name
     *
     * @var array
     */
    protected $subscriptions = [];

    /**
     * Register subscriptions
     *
     * @param array $subscriptions
     * @return void
     */
    public function register(array $subscribers)
    {
        foreach( $subscribers as $subscriber ){
            call_user_func([new $subscriber, 'subscribe'], $this);
        }
    }

    /**
     * Register a listener to an event name(s)
     *
     * @param string|array $eventName
     * @param callable $handler
     */
    public function listen($eventName, $handler)
    {
        if( !is_array($eventName) ){
            $eventName = [$eventName];
        }

        foreach( $eventName as $event ){
            $this->subscriptions[$event][] = $handler;
        }
    }

    /**
     * Trigger an event
     *
     * @param Event $event
     */
    public function trigger(Event $event)
    {
        if( array_key_exists($event->getName(), $this->subscriptions) &&
            is_array($this->subscriptions[$event->getName()]) ){

            foreach( $this->subscriptions[$event->getName()] as $handler ){

                if( !$event->shouldPropagate() ){
                    break;
                }

                // Closure/invokable
                if( is_callable($handler) ){
                    $handler($event);
                }

                // Class@method
                if( ($callable = class_method($handler)) ){
                    call_user_func($callable, $event);
                }
            }
        }
    }
}