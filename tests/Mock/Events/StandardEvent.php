<?php

namespace Nimbly\Announce\Tests\Mock\Events;

use DateTime;
use Nimbly\Announce\Event;

class StandardEvent extends Event
{
	public function __construct(
		public string $status = "pending",
		public ?DateTime $occured_at = null)
	{
	}
}