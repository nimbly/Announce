<?php

namespace Announce\Tests;

use Announce\Dispatcher;
use Announce\Tests\Mock\Events\BroadcastEvent;
use Announce\Tests\Mock\Subject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Announce\Dispatcher
 * @covers Announce\Event
 */
class BroadcastableEventTest extends TestCase
{
    public function test_event_broadcast()
    {
        $dispatcher = new Dispatcher;

        $this->expectException(\Exception::class);
        $dispatcher->dispatch(new BroadcastEvent(new Subject));       
    }
}