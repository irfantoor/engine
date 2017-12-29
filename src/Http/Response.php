<?php

namespace IrfanTOOR\Engine\Http;

use IrfanTOOR\Collection;

use IrfanTOOR\Engine\Debug;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\ResponseStatus;
use IrfanTOOR\Engine\Http\Stream;

/**
 * Request
 */
class Response  extends Collection
{
    function __construct($status=ResponseStatus::STATUS_OK, $headers=[], $body=null)
    {
        $stream = new Stream(fopen('php://temp', 'w+'));
        $stream->write($body);

        $headers = array_merge($headers, ['Engine' => "Irfan's Engine v1.0"]);
        $defaults = [
            'status'  => new ResponseStatus($status),
            'headers' => new Headers($headers),
            'body' => $stream,
            'version' => '1.1',
            'cookie'  => null,
        ];

        parent::__construct($defaults);
    }

    function with($key, $value) {
        $clone = clone $this;

        switch($key) {
            case 'headers':
                $clone->set('headers.' . $value[0], $value[1]);
                break;

            default:
                $this->set($key, $value);
        }

        return $clone;
    }

    function send()
    {
        extract($this->toArray());

        if (!is_string($body)) {
            $body = json_encode($body);
            if (Debug::level())
                 $body = '<pre>' . $body . '</pre>';
            else
                $headers->set("Content-Type", "text/json");

        }

        #### Process and send Headers
        if (!headers_sent()) {
            if ($cookie)
                $cookie->send();
           $headers->send();
            header('HTTP/' . $version . ' ' . $status->getStatusCode() . ' ' . $status->getStatusPhrase());
        }

        # send the body
        echo $body;
        die();
    }
}
