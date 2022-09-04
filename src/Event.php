<?php declare(strict_types=1);

namespace Nimbly\Announce;

use Psr\EventDispatcher\StoppableEventInterface;

abstract class Event implements StoppableEventInterface
{
	/**
	 * Override default event name (default is the class name).
	 *
	 * @var string|null
	 */
	protected ?string $name;

	/**
	 * Whether the event should continue propagation.
	 *
	 * @var boolean
	 */
	protected bool $stopPropagation = false;

	/**
	 * Get event name.
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name ?? static::class;
	}

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