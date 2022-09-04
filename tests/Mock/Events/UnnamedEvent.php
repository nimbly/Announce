<?php

namespace Nimbly\Announce\Tests\Mock\Events;

use Nimbly\Announce\Event;
use Nimbly\Announce\Tests\Mock\Subject;

class UnnamedEvent extends Event
{
    /**
     * @param Subject $subject
     */
    public function __construct(public Subject $subject)
    {
    }
}