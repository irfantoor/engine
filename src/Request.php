<?php

namespace IrfanTOOR;

use IrfanTOOR\Collection;
use IrfanTOOR\Environment;
use IrfanTOOR\Headers;
use IrfanTOOR\Uri;

class Request extends Collection
{
    function __construct($env = [])
    {
        if (!($env instanceof Environment)) {
            $env = new Environment($env);
        }

        $host = $env['HTTP_HOST'] ?: ($env['SERVER_NAME'] ?: 'localhost');
        $protocol = $env['SERVER_PROTOCOL'] ?: 'HTTP/1.1';
        $pos = strpos($protocol, '/');
        $scheme = strtolower(substr($protocol, 0, $pos));
        $ver = substr($protocol, $pos+1);
        $uri = $scheme . '://' . $host . ($env['REQUEST_URI'] ?: '/');

        # Headers
        $headers = [];
        foreach($env->toArray() as $k=>$v) {
            if (strpos($k, 'HTTP_') === 0) {
                $k = substr($k, 5);
                # normalize Token
                $k = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", $k))));
                $headers[$k] = $v;
            }
        }

		$req = [
            # 'env'       => $env,
			'method'	=> $env['REQUEST_METHOD'],
			'uri'       => new Uri($uri),
			'headers'   => new Headers($headers),
			'body'      => null,
			'version'   => $ver,
			'get'       => $_GET,
			'post'      => $_POST,
            'cookie'    => $_COOKIE,
            'files'     => $_FILES,
            'input'     => array_merge($_GET, $_POST),
            'ip'        => $env['REMOTE_ADDR'],
            'time'      => $env['REQUEST_TIME_FLOAT'],
		];

        parent::__construct($req);
    }
}
