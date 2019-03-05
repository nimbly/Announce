<?php

namespace Announce\Tests;

use Announce\Dispatcher;
use Announce\Tests\Mock\Events\UnnamedEvent;
use Announce\Tests\Mock\Subject;
use Announce\Tests\Mock\Subscribers\TestSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * @covers Announce\Dispatcher
 * @covers Announce\Event
 */
class DispatcherTest extends TestCase
{
    public function test_subscriber()
    {
        $dispatcher = new Dispatcher;
        $dispatcher->register([
            TestSubscriber::class,
        ]);

        $test = new Subject;
        $this->assertFalse($test->flag);

        $dispatcher->dispatch(new UnnamedEvent($test));

        $this->assertTrue($test->flag);
    }

    public function test_closure_listener()
    {
        $dispatcher = new Dispatcher;

        $test = false;

        $dispatcher->listen(UnnamedEvent::class, function(UnnamedEvent $testEvent) use (&$test) {

            $test = true;

        });

        $dispatcher->dispatch(new UnnamedEvent(new Subject));

        $this->assertTrue($test);
    }

    public function test_stop_event_propagation()
    {
        $dispatcher = new Dispatcher;
        $dispatcher->register([
            TestSubscriber::class,
        ]);

        $test = new Subject;
        $this->assertFalse($test->flag);

        $event = new UnnamedEvent($test);

        $dispatcher->dispatch($event);

        $this->assertTrue($event->isPropagationStopped());
        $this->assertEquals("Joe Tester", $test->name);
    }
}