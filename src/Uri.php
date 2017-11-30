<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;

class Uri extends Collection
{
    function __construct($url = null)
    {
        $parsed = $url ? parse_url($url) : [];

        $url = array_merge(
            [
                'scheme'    => '',
                'user'      => '',
                'pass'      => '',
                'host'      => '',
                'port'      => '',
                'base_path' => '',
                'path'      => '',
                'query'     => '',
                'fragment'  => '',
            ],
            $parsed
        );

        $url['base_path'] = ltrim(rtrim($url['path'], '/'), '/') ?: '/';

        parent::__construct($url);
    }
}
