<?php

namespace Announce;

use Announce\Dispatcher;

abstract class Subscriber
{
    /**
     * Register handlers with Dispatcher.
     * 
     * $dispatcher->listen(
     *      SomeEvent::class,
     *      [$this, "someEventHandler"]
     * );
     *
     * @param Dispatcher $dispatcher
     * @return void
     */
    abstract public function subscribe(Dispatcher $dispatcher);
}