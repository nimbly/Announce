<?php

namespace Announce\Tests\Mock\Events;

use Announce\BroadcastableEvent;
use Announce\Event;
use Announce\Tests\Mock\Subject;

class BroadcastEvent extends Event implements BroadcastableEvent
{
    public function broadcast(): void
    {
        throw new \Exception("Broadcasting event.");
    }
}