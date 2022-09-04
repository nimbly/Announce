<?php

namespace Nimbly\Announce\Tests\Mock\Subscribers;

use Nimbly\Announce\Subscribe;
use Nimbly\Announce\Tests\Mock\Events\UnnamedEvent;

class TestSubscriber
{
	#[Subscribe(UnnamedEvent::class)]
	public function shouldStopPropagation(UnnamedEvent $testEvent)
	{
		$testEvent->subject->flag = true;
		$testEvent->stop();
	}

	#[Subscribe(UnnamedEvent::class)]
	public function convertNameToUppercase(UnnamedEvent $testEvent)
	{
		$testEvent->subject->name = strtoupper($testEvent->subject->name);
	}
}