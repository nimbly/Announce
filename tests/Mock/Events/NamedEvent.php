<?php

namespace Nimbly\Announce\Tests\Mock\Events;

use Nimbly\Announce\Event;

class NamedEvent extends Event
{
    protected ?string $name = "named.event";

	public function __construct(
		public string $status = "pending")
	{
	}
}