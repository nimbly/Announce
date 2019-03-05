<?php

namespace Announce\Tests;

use Announce\Tests\src\Events\NamedEvent;
use Announce\Tests\src\Events\TestEvent;
use Announce\Tests\src\Subject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Announce\Event
 */
class EventTest extends TestCase
{
    public function test_default_event_name()
    {
        $event = new TestEvent(new Subject);
        $this->assertEquals(TestEvent::class, $event->getName());
    }

    public function test_custom_event_name()
    {
        $event = new NamedEvent;
        $this->assertEquals('named.event', $event->getName());
    }
}