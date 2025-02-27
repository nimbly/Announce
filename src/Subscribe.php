<?php

namespace Nimbly\Announce;

use Attribute;

#[Attribute]
class Subscribe
{
	/**
	 * Event names to listen for.
	 *
	 * @var array<string>
	 */
	protected array $events;

	/**
	 * @param string ...$events Event names to subscribe to.
	 */
	public function __construct(string ...$events)
	{
		$this->events = $events;
	}

	/**
	 * Get list of event names for the subscription.
	 *
	 * @return array<string>
	 */
	public function getEvents(): array
	{
		return $this->events;
	}
}