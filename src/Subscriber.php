<?php declare(strict_types=1);

namespace Announce;

use Announce\Dispatcher;

abstract class Subscriber
{
    /**
     * Register Subscriber handlers with Dispatcher.
     * 
     * $dispatcher->listen(
     *      SomeEvent::class,
     *      [$this, "someEventHandlerMethod"]
     * );
     * 
     * Or register multiple events to a single handler.
     * 
     * $dispatcher->listen(
     *      [SomeEvent::class, SomeOtherEvent::class],
     *      [$this, "someEventHandlerMethod"]
     * );
     *
     * @param Dispatcher $dispatcher
     * @return void
     */
    abstract public function register(Dispatcher $dispatcher): void;
}