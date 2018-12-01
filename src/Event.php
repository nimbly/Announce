<?php

namespace Announce;


abstract class Event
{
    /**
     * Event name - defaults to class name.
     *
     * @var string
     */
    protected $name;

    /**
     * Whether the event should continue propagation.
     *
     * @var boolean
     */
    protected $shouldPropagate = true;

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

    /**
     * Get the event propagation status.
     *
     * @return boolean
     */
    public function shouldPropagate()
    {
        return $this->shouldPropagate;
    }

    /**
     * Stop event propagation.
     *
     * @return void
     */
    public function stopPropagation()
    {
        $this->shouldPropagate = false;
    }
}