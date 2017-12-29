<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\RequestMethod;
use IrfanTOOR\Engine\Http\Uri;

class Request extends Collection
{
    function __construct($env = [])
    {
        if (!$env instanceof Environment) {
            $env = is_array($env) ? $env : [];
            $env = new Environment($env);
        }

        $protocol = $env['SERVER_PROTOCOL'];
        $pos = strpos('/', $protocol);
        $ver = ($pos !== false) ? substr($protocol, $pos+1) : '1.1';

		$req = [
            # 'env'       => $env,
			'method'	=> new RequestMethod($env['REQUEST_METHOD']),
			'uri'       => Uri::createFromEnvironment($env),
			'headers'   => Headers::createFromEnvironment($env),
			'body'      => null,
			'version'   => $ver,
			'get'       => $_GET,
			'post'      => $_POST,
            'cookie'    => new Cookie($_COOKIE),
            'files'     => $_FILES, # todo -- UploadedFilesInterface
            'input'     => array_merge($_GET, $_POST),
            'ip'        => $env['REMOTE_ADDR'],
            'time'      => $env['REQUEST_TIME_FLOAT'],
		];

        parent::__construct($req);
    }

    public static function createFromEnvironment($env = [])
    {
        $mocked = new Environment($env);
        return new static($mocked);
    }
}
