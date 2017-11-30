<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;

class Headers extends Collection
{
    function __construct($init = [])
	{
        parent::__construct($init);
	}

    function setItem($id, $value=null)
    {
        parent::setItem(strtolower($id), ['id' => $id, 'value'=>$value]);
    }

    function has($id)
    {
        return parent::has(strtolower($id));
    }

    function get($id, $default=null)
    {
        return $this->has($id) ? parent::get(strtolower($id))['value'] : $default;
    }

    function remove($id)
    {
        parent::remove(strtolower($id));
    }

    function toArray()
    {
        $headers = [];
        foreach(parent::toArray() as $v) {
            $headers[$v['id']] = $v['value'];
        }
        return $headers;
    }

    function send()
    {
        if (headers_sent())
            return;

        foreach($this->toArray() as $k=>$v) {
            $value = is_array($v) ? implode('; ', $v) : $v;
            header( "$k:$value" );
        }
    }
}
