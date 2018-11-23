<?php

namespace Announce;


abstract class Event
{
    /**
     * Event name - defaults to class name
     *
     * @var string
     */
    protected $name;

    /**
     * Get event name
     *
     * @return string
     */
    public function getName()
    {
        if( empty($this->name) ){
            return static::class;
        }

        return $this->name;
    }
}