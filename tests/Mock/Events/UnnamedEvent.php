<?php

namespace Announce\Tests\Mock\Events;

use Announce\BroadcastableEvent;
use Announce\Event;
use Announce\Tests\Mock\Subject;

class UnnamedEvent extends Event
{
    /**
     * @var Subject
     */
    public $subject;

    /**
     * Test constructor
     *
     * @param Subject $test
     */
    public function __construct(Subject $subject)
    {
        $this->subject = $subject;
    }
}