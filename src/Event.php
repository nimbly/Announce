<?php declare(strict_types=1);

namespace Announce;

use Psr\EventDispatcher\StoppableEventInterface;


abstract class Event implements StoppableEventInterface
{
    /**
     * Override default event name.
     *
     * @var string|null
     */
    protected $name;

    /**
     * Whether the event should continue propagation.
     *
     * @var boolean
     */
    protected $stopPropagation = false;

    /**
     * Get event name
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