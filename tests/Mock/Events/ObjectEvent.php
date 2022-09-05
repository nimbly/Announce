<?php

namespace Nimbly\Announce\Tests\Mock\Events;

class ObjectEvent
{
	public function __construct(
		public string $status = "pending")
	{
	}
}