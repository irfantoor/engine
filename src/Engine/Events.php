<?php

namespace IrfanTOOR\Engine;

use Closure;
use IrfanTOOR\Collection;

class Events extends Collection
{
    function __construct()
    {
    }
    
    public function register($event_id, Closure $event, $level = 10)
    {
        if ($level < 0)
            $level = 0;

        if ($level > 10)
            $level = 10;

        $old = $this->get($event_id . '.' . $level, []);
        $new = array_merge($old, [
            $event
        ]);
        $this->set($event_id . '.' . $level, $new);
    }

    public function trigger($event_id)
    {
        for ($level = 0; $level <= 10; $level++) {
            $list = $this->get($event_id . '.' . $level, []);
            foreach ($list as $event) {
                $event($this->engine);
            }
        }
    }
}
