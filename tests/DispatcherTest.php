<?php

namespace Announce\Tests;

use Carton\Container;
use DateTime;
use Nimbly\Announce\Dispatcher;
use Nimbly\Announce\Subscribe;
use Nimbly\Announce\Tests\Mock\Events\NamedEvent;
use Nimbly\Announce\Tests\Mock\Events\ObjectEvent;
use Nimbly\Announce\Tests\Mock\Events\StandardEvent;
use Nimbly\Announce\Tests\Mock\Subscribers\TestSubscriber;
use Nimbly\Resolve\Resolve;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use UnexpectedValueException;

/**
 * @covers Nimbly\Announce\Dispatcher
 * @covers Nimbly\Announce\Subscribe
 * @covers Nimbly\Announce\Event
 */
class DispatcherTest extends TestCase
{
	public function test_subscribers_in_constructor(): void
	{
		$dispatcher = new Dispatcher(
			subscribers: [
				TestSubscriber::class
			]
		);

		$reflectionClass = new ReflectionClass($dispatcher);
		$reflectionProperty = $reflectionClass->getProperty("subscriptions");
		$reflectionProperty->setAccessible(true);

		$subscriptions = $reflectionProperty->getValue($dispatcher);

		$this->assertArrayHasKey(NamedEvent::class, $subscriptions);
		$this->assertArrayHasKey(StandardEvent::class, $subscriptions);

		$this->assertCount(1, $subscriptions[NamedEvent::class]);
		$this->assertCount(1, $subscriptions[StandardEvent::class]);

		$this->assertIsCallable($subscriptions[NamedEvent::class][0]);
		$this->assertIsCallable($subscriptions[StandardEvent::class][0]);
	}

	public function test_container_in_constructor(): void
	{
		$container = new Container;

		$dispatcher = new Dispatcher(
			container: $container
		);

		$reflectionClass = new ReflectionClass($dispatcher);
		$reflectionProperty = $reflectionClass->getProperty("resolve");
		$reflectionProperty->setAccessible(true);

		$resolve = $reflectionProperty->getValue($dispatcher);

		$this->assertInstanceOf(Resolve::class, $resolve);

		$reflectionClass = new ReflectionClass($resolve);
		$reflectionProperty = $reflectionClass->getProperty("container");
		$reflectionProperty->setAccessible(true);

		$resolveContainer = $reflectionProperty->getValue($resolve);

		$this->assertSame($container, $resolveContainer);
	}

	public function test_non_class_subscriber_in_constructor_throws_exception(): void
	{
		$this->expectException(UnexpectedValueException::class);

		$dispatcher = new Dispatcher(
			subscribers: [
				"foo"
			]
		);
	}

	public function test_subscriber_with_no_methods_throws_exception(): void
	{
		$this->expectException(UnexpectedValueException::class);

		$dispatcher = new Dispatcher(
			subscribers: [
				new class {}
			]
		);
	}

	public function test_subscriber_in_constructor_with_no_subscibe_attributes_throws_exception(): void
	{
		$this->expectException(UnexpectedValueException::class);

		$dispatcher = new Dispatcher(
			subscribers: [
				new class {
					public function onFoo($event): void {
						echo $event;
					}
				}
			]
		);
	}

	public function test_subscriber_with_non_public_method_throws_exception(): void
	{
		$this->expectException(UnexpectedValueException::class);

		$dispatcher = new Dispatcher(
			subscribers: [
				new class {
					#[Subscribe("BarEvent")]
					protected function onFoo($bar): void {
					}
				}
			]
		);
	}

	public function test_listen_single_event(): void
	{
		$dispatcher = new Dispatcher;
		$dispatcher->listen(
			"FooEvent",
			fn($event) => $event->name
		);

		$reflectionClass = new ReflectionClass($dispatcher);
		$reflectionProperty = $reflectionClass->getProperty("subscriptions");
		$reflectionProperty->setAccessible(true);

		$subscriptions = $reflectionProperty->getValue($dispatcher);

		$this->assertArrayHasKey("FooEvent", $subscriptions);
		$this->assertCount(1, $subscriptions["FooEvent"]);
		$this->assertIsCallable($subscriptions["FooEvent"][0]);
	}

	public function test_listen_multiple_events(): void
	{
		$dispatcher = new Dispatcher;
		$dispatcher->listen(
			["FooEvent", "BarEvent"],
			fn($event) => $event->name
		);

		$reflectionClass = new ReflectionClass($dispatcher);
		$reflectionProperty = $reflectionClass->getProperty("subscriptions");
		$reflectionProperty->setAccessible(true);

		$subscriptions = $reflectionProperty->getValue($dispatcher);

		$this->assertArrayHasKey("FooEvent", $subscriptions);
		$this->assertArrayHasKey("BarEvent", $subscriptions);

		$this->assertCount(1, $subscriptions["FooEvent"]);
		$this->assertCount(1, $subscriptions["BarEvent"]);

		$this->assertIsCallable($subscriptions["FooEvent"][0]);
		$this->assertIsCallable($subscriptions["BarEvent"][0]);
	}

	public function test_get_listeners_for_event_with_event_abstract(): void
	{
		$callback = function(StandardEvent $event): void {
			$event->status = "processed";
		};

		$dispatcher = new Dispatcher;
		$dispatcher->listen(
			StandardEvent::class,
			$callback
		);

		$listeners = $dispatcher->getListenersForEvent(new StandardEvent);

		$this->assertCount(1, $listeners);
		$this->assertSame($callback, $listeners[0]);
	}

	public function test_get_listeners_with_object_event(): void
	{
		$callback = function(ObjectEvent $event): void {
			$event->status = "processed";
		};

		$dispatcher = new Dispatcher;
		$dispatcher->listen(
			ObjectEvent::class,
			$callback
		);

		$listeners = $dispatcher->getListenersForEvent(new ObjectEvent);

		$this->assertCount(1, $listeners);
		$this->assertSame($callback, $listeners[0]);
	}

	public function test_should_propagation_stop_for_event_abstract(): void
	{
		$event = new StandardEvent;

		$dispatcher = new Dispatcher;
		$reflectionClass = new ReflectionClass($dispatcher);
		$reflectionMethod = $reflectionClass->getMethod("shouldPropagationStop");
		$reflectionMethod->setAccessible(true);

		$this->assertFalse(
			$reflectionMethod->invokeArgs($dispatcher, [$event])
		);

		$event->stop();

		$this->assertTrue(
			$reflectionMethod->invokeArgs($dispatcher, [$event])
		);
	}

	public function test_should_propagation_stop_for_generic_event_returns_false(): void
	{
		$event = new ObjectEvent;

		$dispatcher = new Dispatcher;
		$reflectionClass = new ReflectionClass($dispatcher);
		$reflectionMethod = $reflectionClass->getMethod("shouldPropagationStop");
		$reflectionMethod->setAccessible(true);

		$this->assertFalse(
			$reflectionMethod->invokeArgs($dispatcher, [$event])
		);
	}

	public function test_dispatch(): void
	{
		$dispatcher = new Dispatcher;
		$dispatcher->listen(
			StandardEvent::class,
			function(StandardEvent $event): void {
				$event->status = "processed";
			}
		);

		$event = new StandardEvent;
		$dispatcher->dispatch($event);

		$this->assertEquals(
			"processed",
			$event->status
		);
	}

	public function test_dispatch_event_propagation_check(): void
	{
		$dispatcher = new Dispatcher;

		$dispatcher->listen(
			StandardEvent::class,
			function(StandardEvent $event): void {
				$event->status = "processed";
			}
		);

		$dispatcher->listen(
			StandardEvent::class,
			function(StandardEvent $event): void {
				$event->status = "foo";
			}
		);

		$event = new StandardEvent;
		$event->stop();

		$dispatcher->dispatch($event);

		$this->assertEquals(
			"pending",
			$event->status
		);
	}

	public function test_dependency_injection_handler_calls(): void
	{
		$container = new Container;
		$container->set(
			DateTime::class,
			new DateTime("Mar 14, 2020")
		);

		$dispatcher = new Dispatcher(
			subscribers: [
				new class {
					#[Subscribe(StandardEvent::class)]
					public function onDateUpdated(StandardEvent $event, DateTime $date): void {
						$event->occured_at = $date;
					}
				}
			],
			container: $container
		);

		$event = new StandardEvent;

		$dispatcher->dispatch($event);

		$this->assertEquals(
			"2020-03-14",
			$event->occured_at?->format("Y-m-d")
		);
	}
}