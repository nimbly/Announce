<?php

namespace Announce\Tests;

use Nimbly\Announce\Dispatcher;
use Nimbly\Announce\Tests\Mock\Events\ObjectEvent;
use Nimbly\Announce\Tests\Mock\Events\UnnamedEvent;
use Nimbly\Announce\Tests\Mock\Subject;
use Nimbly\Announce\Tests\Mock\Subscribers\TestSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * @covers Nimbly\Announce\Dispatcher
 * @covers Nimbly\Announce\Event
 * @covers Nimbly\Announce\Subscribe
 */
class DispatcherTest extends TestCase
{
    public function test_subscriber()
    {
        $dispatcher = new Dispatcher([TestSubscriber::class]);

        $test = new Subject;
        $this->assertFalse($test->flag);

        $dispatcher->dispatch(new UnnamedEvent($test));

        $this->assertTrue($test->flag);
    }

    public function test_stop_event_propagation()
    {
        $dispatcher = new Dispatcher([TestSubscriber::class]);

        $test = new Subject;
        $this->assertFalse($test->flag);

        $event = new UnnamedEvent($test);

		$dispatcher->dispatch($event);

        $this->assertTrue($event->isPropagationStopped());
        $this->assertEquals("Joe Tester", $test->name);
    }

    public function test_generic_object_as_event()
    {
        $dispatcher = new Dispatcher([

		]);

        $test = false;
        $dispatcher->listen(
            ObjectEvent::class,
            function(ObjectEvent $event) use (&$test) {

                $test = true;

            }
        );

        $dispatcher->dispatch(new ObjectEvent);

        $this->assertTrue($test);
    }
}