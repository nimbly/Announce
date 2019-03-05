<?php declare(strict_types=1);

namespace Announce;

interface BroadcastableEvent
{
    /**
     * Broadcast the event.
     *
     * @return void
     */
    public function broadcast(): void;
}