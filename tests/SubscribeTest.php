<?php

namespace Nimbly\Announce\Tests;

use Attribute;
use Nimbly\Announce\Subscribe;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @covers Nimbly\Announce\Subscribe
 */
class SubscribeTest extends TestCase
{
	public function test_subscribe_is_attribute(): void
	{
		$reflectionClass = new ReflectionClass(Subscribe::class);
		$attributes = $reflectionClass->getAttributes(Attribute::class);
		$this->assertCount(1, $attributes);
	}

	public function test_get_events(): void
	{
		$subscribe = new Subscribe("event1", "event2");

		$this->assertCount(2, $subscribe->getEvents());

		$this->assertEquals(
			"event1",
			$subscribe->getEvents()[0]
		);

		$this->assertEquals(
			"event2",
			$subscribe->getEvents()[1]
		);
	}
}