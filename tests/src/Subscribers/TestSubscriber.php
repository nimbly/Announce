<?php

namespace Announce\Tests\src\Subscribers;

use Announce\Dispatcher;
use Announce\Subscriber;
use Announce\Tests\src\Events\TestEvent;

class TestSubscriber extends Subscriber
{
    public function shouldStopPropagation(TestEvent $testEvent)
    {
        $testEvent->subject->flag = true;
        $testEvent->stopPropagation();
    }

    public function convertNameToUppercase(TestEvent $testEvent)
    {
        $testEvent->subject->name = strtoupper($testEvent->subject->name);
    }

    public function register(Dispatcher $dispatcher): void
    {
        $dispatcher->listen(
            TestEvent::class,
            [$this, 'shouldStopPropagation']
        );

        $dispatcher->listen(
            TestEvent::class,
            [$this, 'convertNameToUppercase']
        );
    }
}