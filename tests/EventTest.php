<?php

namespace Announce\Tests;

use Announce\Tests\Mock\Events\NamedEvent;
use Announce\Tests\Mock\Events\UnnamedEvent;
use Announce\Tests\Mock\Subject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Announce\Event
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
        $this->assertEquals('named.event', $event->getName());
    }
}