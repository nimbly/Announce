<?php

namespace Nimbly\Announce\Tests\Mock;

use DateTime;
use Nimbly\Announce\StoppableEvent;

class TestEvent extends StoppableEvent
{
	public function __construct(
		public string $status = "pending",
		public ?DateTime $occured_at = null)
	{
	}
}