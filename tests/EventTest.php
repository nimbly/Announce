<?php

namespace Announce\Tests;

use Announce\Dispatcher;
use Announce\Event;
use Announce\Subscriber;
use PHPUnit\Framework\TestCase;

class Test
{
    public $foo = false;
}

class TestEvent extends Event
{
    /**
     * @var Test
     */
    public $test;

    /**
     * Test constructor
     *
     * @param Test $test
     */
    public function __construct(Test $test)
    {
        $this->test = $test;
    }
}

class TestSubscriber extends Subscriber
{
    public function testHandler(TestEvent $testEvent)
    {
        $testEvent->test->foo = true;
    }

    public function register(Dispatcher $dispatcher): void
    {
        $dispatcher->listen(
            TestEvent::class,
            [$this, 'testHandler']
        );
    }
}

/**
 * @covers Announce\Dispatcher
 * @covers Announce\Event
 * @covers Announce\Subscriber
 */
class EventTest extends TestCase
{
    public function test_event_subscription()
    {
        $dispatcher = new Dispatcher;
        $dispatcher->register([
            TestSubscriber::class,
        ]);

        $test = new Test;
        $this->assertFalse($test->foo);

        $dispatcher->trigger(new TestEvent($test));

        $this->assertTrue($test->foo);
    }
}