<?php

namespace Announce\Tests\src\Events;

use Announce\Event;

class NamedEvent extends Event
{
    protected $name = "named.event";
}