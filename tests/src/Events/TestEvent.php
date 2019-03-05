<?php

namespace Announce\Tests\src\Events;

use Announce\Broadcaster;
use Announce\Event;
use Announce\Tests\src\Subject;

class TestEvent extends Event implements Broadcaster
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

    public function broadcast(): void
    {
        //
    }
}