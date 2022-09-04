<?php

namespace Announce\Tests;

use Nimbly\Announce\Tests\Mock\Events\NamedEvent;
use Nimbly\Announce\Tests\Mock\Events\UnnamedEvent;
use Nimbly\Announce\Tests\Mock\Subject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Nimbly\Announce\Event
 */
class EventTest extends TestCase
{
    public function test_default_event_name()
    {
        $event = new UnnamedEvent(new Subject);
        $this->assertEquals(UnnamedEvent::class, $event->getName());
    }

    public function test_custom_event_name()
    {
        $event = new NamedEvent;
        $this->assertEquals("named.event", $event->getName());
    }

	public function test_stop(): void
	{
		$event = new NamedEvent;
		$event->stop();

		$this->assertTrue($event->isPropagationStopped());
	}

	public function test_is_propagation_stopped_false_by_default(): void
	{
		$event = new NamedEvent;
		$this->assertFalse($event->isPropagationStopped());
	}
}