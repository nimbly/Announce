<?php

namespace Nimbly\Announce;

use Psr\EventDispatcher\StoppableEventInterface;

abstract class StoppableEvent implements StoppableEventInterface
{
	/**
	 * Whether the event should continue propagation.
	 *
	 * @var boolean
	 */
	protected bool $stopPropagation = false;

	/**
	 * Get the event propagation status.
	 *
	 * @return boolean
	 */
	public function isPropagationStopped(): bool
	{
		return $this->stopPropagation;
	}

	/**
	 * Stop event propagation.
	 *
	 * @return void
	 */
	public function stop(): void
	{
		$this->stopPropagation = true;
	}
}