<?php

namespace IrfanTOOR\Engine;

use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Response;

/**
 * Middleware
 *
 * This is an internal class that enables concentric middleware layers. This
 * class is an implementation detail and is used only inside of the Slim
 * application; it is not visible to—and should not be used by—end users.
 */
trait MiddlewareTrait
{
    protected $root;
    protected $lock = false;

    /**
     * Add middleware
     *
     */
    public function addMiddleware(callable $callable)
    {
        if ($this->lock) {
            throw new Exception('Middleware can’t be added once the stack is dequeuing');
        }

        if (is_null($this->root))
            $this->root = $this;

        $next = $this->root;
        $this->root = function (
            Request $request,
            Response $response
        ) use (
            $callable,
            $next
        ) {
            $result = call_user_func($callable, $request, $response, $next);

            if (is_array($result)) {
                if (count($result) !== 2)
                    throw new Exception(
                        'callable can return an array of request and response'
                    );
            } else {
                if (!($result instanceof Response)) {
                    throw new Exception(
                        'Middleware must return instance of Response'
                    );
                }

                $result  = [$request, $result];
            }

            return $result;
        };

        return $this;
    }


    /**
     * Call middleware stack
     *
     */
    public function callMiddlewares(Request $request, Response $response)
    {
        # safety measure
        if (is_null($this->root)) {
            $this->root = $this;
        }

        // call the root middleware which will call the next and so on ...
        $root = $this->root;

        $this->lock = true;
        $result = $root($request, $response);
        $this->lock = false;

        return $result;
    }
}
