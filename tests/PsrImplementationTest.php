<?php

use IrfanTOOR\Test;

use IrfanTOOR\Engine\Http\{
    Message,
    Request,
    Response,
    ServerRequest,
    Stream,
    UploadedFile,
    Uri
};

use Psr\Http\Message\{
    MessageInterface,
    RequestInterface,
    ResponseInterface,
    ServerRequestInterface,
    StreamInterface,
    UploadedFileInterface,
    UriInterface
};

class IMessage extends Message implements MessageInterface
{
    function withBody(StreamInterface $body)
    {
        return parent::withBody($body);
    }
}

class IRequest extends Request implements RequestInterface
{
    function withBody(StreamInterface $body)
    {
        return parent::withBody($body);
    }

    function withUri(UriInterface $uri, $preserveHost = false)
    {
        return parent::withUri($uri, $preserveHost);
    }
}

class IResponse extends Response implements ResponseInterface
{
    function withBody(StreamInterface $body)
    {
        return parent::withBody($body);
    }
}

class IServerRequest extends ServerRequest implements ServerRequestInterface
{
    function withBody(StreamInterface $body)
    {
        return parent::withBody($body);
    }

    function withUri(UriInterface $uri, $preserveHost = false)
    {
        return parent::withUri($uri, $preserveHost);
    }
}

class IStream extends Stream implements StreamInterface
{
}

class IUploadedFile extends UploadedFile implements UploadedFileInterface
{
}

class IUri extends Uri implements UriInterface
{
}

class PsrImplementationTest extends Test
{
    function testMessageImplementsPsrMessageInterface()
    {
        $m = new IMessage();
        $this->assertInstanceOf(Message::class, $m);
        $this->assertImplements(MessageInterface::class, $m);
    }

    function testRequestImplementsPsrRequestInterface()
    {
        $r = new IRequest();
        $this->assertInstanceOf(Request::class, $r);
        $this->assertImplements(RequestInterface::class, $r);
    }

    function testResponseImplementsPsrResponseInterface()
    {
        $r = new IResponse();
        $this->assertInstanceOf(Response::class, $r);
        $this->assertImplements(ResponseInterface::class, $r);
    }

    function testServerRequestImplementsPsrServerRequestInterface()
    {
        $sr = new IServerRequest();
        $this->assertInstanceOf(ServerRequest::class, $sr);
        $this->assertImplements(ServerRequestInterface::class, $sr);
    }

    function testStreamImplementsPsrStreamInterface()
    {
        $s = new IStream();
        $this->assertInstanceOf(Stream::class, $s);
        $this->assertImplements(StreamInterface::class, $s);
    }

    function testUploadedFileImplementsPsrUploadedFileInterface()
    {
        $f = new IUploadedFile(__FILE__);

        $this->assertInstanceOf(UploadedFile::class, $f);
        $this->assertImplements(UploadedFileInterface::class, $f);
    }

    function testUriImplementsPsrUriInterface()
    {
        $m = new IUri();
        $this->assertInstanceOf(Uri::class, $m);
        $this->assertImplements(UriInterface::class, $m);
    }
}