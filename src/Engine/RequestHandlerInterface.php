<?php

/**
 * IrfanTOOR\Engine\RequestHandlerInterface
 * php version 7.4
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021-present Irfan TOOR
 */

namespace IrfanTOOR\Engine;

use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};

interface RequestHandlerInterface
{
    /**
     * Processes a request
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
