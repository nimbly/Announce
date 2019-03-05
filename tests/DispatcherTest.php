<?php

namespace Announce\Tests;

use Announce\Dispatcher;
use Announce\Tests\src\Events\TestEvent;
use Announce\Tests\src\Subject;
use Announce\Tests\src\Subscribers\TestSubscriber;
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

        $dispatcher->trigger(new TestEvent($test));

        $this->assertTrue($test->flag);
    }

    public function test_closure_listener()
    {
        $dispatcher = new Dispatcher;

        $test = false;

        $dispatcher->listen(TestEvent::class, function(TestEvent $testEvent) use (&$test) {

            $test = true;

        });

        $dispatcher->trigger(new TestEvent(new Subject));

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

        $event = new TestEvent($test);

        $dispatcher->trigger($event);

        $this->assertFalse($event->shouldPropagate());
        $this->assertEquals("Joe Tester", $test->name);
    }
}