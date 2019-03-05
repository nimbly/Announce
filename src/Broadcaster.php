<?php

namespace Announce;

interface Broadcaster
{
    /**
     * Broadcast the event.
     *
     * @return void
     */
    public function broadcast(): void;
}