<?php

namespace Announce\Tests\Mock\Events;

use Announce\Event;

class NamedEvent extends Event
{
    protected $name = "named.event";
}