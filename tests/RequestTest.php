<?php

use IrfanTOOR\Test;
use IrfanTOOR\Engine\Http\{
    Message,
    Request,
    Uri
};
// use Fig\Http\Message\RequestMethodInterface;
// use Psr\Http\Message\{
//     MessageInterface,
//     RequestInterface,
//     UriInterface,
//     StreamInterface
// };

/**
 * Representation of an outgoing, client-side request.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - HTTP method
 * - URI
 * - Headers
 * - Message body
 *
 * During construction, implementations MUST attempt to set the Host header from
 * a provided URI if no Host header is provided.
 *
 * Requests are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */

// class MockRequest extends Request # implements RequestInterface, RequestMethodInterface
// {
//     function withUri(UriInterface $uri, $preserveHost = false)
//     {
//         return parent::withUri($uri, $preserveHost);
//     }
// }

class RequestTest extends Test
{
    function getRequest($env=[])
    {
        return new Request();
    }

    function testRequestInstance()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\Request::class,
            $request
        );

        $this->assertInstanceOf(Request::class, $request);
        $this->assertInstanceOf(Message::class, $request);
    }

    function testRequestHeaders()
    {
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $this->assertTrue(is_array($headers));
        foreach($headers as $k => $v) {
            $this->assertTrue(is_array($v));
        }
    }

    function testDefaultRequestMethod()
    {
        $request = $this->getRequest();
        $this->assertEquals('GET', $request->getMethod());
    }

    function testRequestWithMethod() {
        $request = $this->getRequest();
        $r2 = $request->withMethod('POST');
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('POST', $r2->getMethod());
    }

    function testRequestGetUri()
    {
        $request = $this->getRequest();
        $this->assertInstanceOf(
            IrfanTOOR\Engine\Http\Uri::class,
            $request->getUri()
        );
    }

    function testRequestDefaultUri()
    {
        $request = $this->getRequest();
        $uri = $request->getUri();

        $this->assertEquals('http://localhost/', (string) $uri);
    }

    function testRequestWithUri()
    {
        $request = $this->getRequest();
        $r2 = $request->withUri(new Uri('https://example.com:80/'));

        $this->assertEquals('https://example.com:80/', (string) $r2->getUri());
    }

    function testRequestCloning()
    {
        $request1 = $this->getRequest();
        $request2 = clone $request1;
        $this->assertEquals((string)$request1->getBody(), (string)$request2->getBody());
        $this->assertNotEquals($request1, $request2);
        $this->assertNotSame($request1, $request2);

        $uri1 = $request1->getUri();
        $uri2 = $request2->getUri();
        $uri3 = $request1->getUri();

        $this->assertEquals($uri1, $uri2);
        $this->assertNotSame($uri1, $uri2);
        $this->assertNotSame($uri1, $uri3);
    }
}
