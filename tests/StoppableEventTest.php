<?php

namespace Announce\Tests;

use Nimbly\Announce\Tests\Mock\TestEvent;
use PHPUnit\Framework\TestCase;

/**
 * @covers Nimbly\Announce\StoppableEvent
 */
class StoppableEventTest extends TestCase
{
	public function test_stop(): void
	{
		$event = new TestEvent;
		$event->stop();

		$this->assertTrue($event->isPropagationStopped());
	}

	public function test_is_propagation_stopped_false_by_default(): void
	{
		$event = new TestEvent;
		$this->assertFalse($event->isPropagationStopped());
	}
}