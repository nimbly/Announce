<?php

namespace Announce\Tests\Mock\Subscribers;

use Announce\Dispatcher;
use Announce\Subscriber;
use Announce\Tests\Mock\Events\UnnamedEvent;

class TestSubscriber extends Subscriber
{
    public function shouldStopPropagation(UnnamedEvent $testEvent)
    {
        $testEvent->subject->flag = true;
        $testEvent->stop();
    }

    public function convertNameToUppercase(UnnamedEvent $testEvent)
    {
        $testEvent->subject->name = strtoupper($testEvent->subject->name);
    }

    public function register(Dispatcher $dispatcher): void
    {
        $dispatcher->listen(
            UnnamedEvent::class,
            [$this, 'shouldStopPropagation']
        );

        $dispatcher->listen(
            UnnamedEvent::class,
            [$this, 'convertNameToUppercase']
        );
    }
}