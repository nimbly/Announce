<?php

namespace Nimbly\Announce\Tests\Mock\Subscribers;

use Nimbly\Announce\Subscribe;
use Nimbly\Announce\Tests\Mock\Events\NamedEvent;
use Nimbly\Announce\Tests\Mock\Events\StandardEvent;

class TestSubscriber
{
	#[Subscribe(NamedEvent::class)]
	public function onNamedEvent(NamedEvent $event): void
	{
		$event->status = "processed";
	}

	#[Subscribe(StandardEvent::class)]
	public function onUnnamedEvent(StandardEvent $event): void
	{
		$event->status = "processed";
	}
}