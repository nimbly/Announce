<?php

namespace Nimbly\Announce\Tests\Mock;

use Nimbly\Announce\StoppableEvent;
use Nimbly\Announce\Subscribe;

class TestSubscriber
{
	#[Subscribe(TestEvent::class)]
	public function onNamedEvent(TestEvent $event): void
	{
		$event->status = "processed";
	}

	#[Subscribe(StoppableEvent::class)]
	public function onStoppableEvent(StoppableEvent $event): void
	{
		$event->stop();
	}
}