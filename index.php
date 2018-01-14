<?php

require 'vendor/autoload.php';

use IrfanTOOR\Engine;
use IrfanTOOR\Collection;
use IrfanTOOR\Engine\Debug;
use IrfanTOOR\Engine\Exception;
use IrfanTOOR\Engine\Middleware;
use IrfanTOOR\Engine\Router;
use IrfanTOOR\Engine\Http\Cookie;
use IrfanTOOR\Engine\Http\Environment;
use IrfanTOOR\Engine\Http\Headers;
use IrfanTOOR\Engine\Http\Message;
use IrfanTOOR\Engine\Http\Request;
use IrfanTOOR\Engine\Http\Response;
use IrfanTOOR\Engine\Http\ServerRequest;
use IrfanTOOR\Engine\Http\Stream;
use IrfanTOOR\Engine\Http\UploadedFile;
use IrfanTOOR\Engine\Http\Uri;


define('HACKER_MODE', true);

// $uri  = Factory::createUri(
//     'http://irfan:test@www.example.com:8000/hello/world?hello=world#frag'
// );
//
// $sr = Factory::createServerRequest();
// # $f = Factory::createUploadedFile('/tmp/hello.txt');
//
// print_r($sr);
//

// $sr = (new ServerRequest())->withHeader('User-Agent', 'Hello World v1.0');

// $c = new Cookie(['hello' => 'world!']);
// $c = $c->withValue(['hello' => 'AnotherWorld!'])->withOptions([
//     'domain'   => 'example.com',
//     'httponly' => 1
// ]);
#$_SESSION['hello'] = 'world!';

// $cookies = $sr->getCookieParams();
// foreach($cookies as $cookie)
// {
//     $value = $cookie->getValue();
//     foreach($value as $k=>$v) {
//         if ($k === 'PHPSESSID') {
//             #$c = $cookie->withValue(['PHPSESSID' => '3c23a877cdd7db97d959ce9bd6ad2737']);
//             $c = $cookie->withOptions(['expires' => 1]);
//             $c->send();
//         }
//     }
// }
//
// Debug::dump($sr);

// $s = Stream::createFromString('');
//
// $s->write('Hello');
// $s->write(' ');
// $s->write('World!');
// $s->seek(0);
// print_r($s->write('Shello'));
// print_r((string) $s);

$ie = new Engine([
    'debug' => [
        'level' => 1,
    ]
]);




$ie->run();
exit;



// $uri = new Uri('http://example.com');
// print_r($uri);
// print_r((string) $uri->withScheme('https')->withPort(8080));
// exit;


// $request = RequestFactory::createRequest()
//             ->withMethod('SMART');

// $r = RequestFactory::create([
//     #'method' => '',
//     'uri'    => 'http://hello:world@irfantoor.com:8080/test/page?hello=world#first',
// ]);
//
// Debug::dump($r);

// $router = new Router();
// Debug::dump($router);
// exit;

// $r = RequestFactory::createFromEnvironment();
// # $r = new Response();
// $uri = $r->getUri();
//
// //*/
// $uri = $uri->withUserInfo('neo', '31337-#4k3r')
//         ->withHost('MATRIX.COM')
//         ->withScheme('reality')
//         ->withPath('take/the/red/pill')
//         ->withPort(01101001-01110100);
//
// /*/
// $uri = $uri->withUserInfo('neo', '31337-#4k3r')
//         ->withHost('MATRIX.COM')
//         ->withScheme('https')
//         ->withPath('take/the/red/pill')
//         ->withPort(8080);
// //*/
//
// $r = $r->withUri($uri);
//
// Debug::dump($r);
//
// exit;

class Hello extends Middleware
{
    protected $method;
    public function __construct($method)
    {
        $this->method = $method;
    }

    public function __invoke(Request $request, Response $response, $next = null)
    {
        $request = $request->withMethod($this->method);

        if ($next)
            list($request, $response) = $next($request, $response);

        return [$request, $response];
    }
}

class Auth extends Middleware
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function __invoke(Request $request, Response $response, $next = null)
    {
        if (md5($this->id) !== '24b90bc48a67ac676228385a7c71a119') {
            $body = $response->getBody();
            $body->write('[' . $this->id . ': Can not be authenticated]');

            $response->send();
            exit;
        }

        if ($next)
            list($request, $response) = $next($request, $response);

        return [$request, $response];
    }
}

class Controller
{
    protected $ie;

    function __construct($ie)
    {
        $this->ie = $ie;

        $ie->addMiddleware(new Auth('irfan'));
        $ie->addMiddleware(new Hello('POST'));
    }

    function color($txt, $color)
    {

        return '<span style="color:' . $color . '">' . $txt . '</span>';
    }

    function pre($txt)
    {
        return '<pre>' . $txt . '</pre>';
    }

    function defaultMethod($request, $response)
    {
        $c = new IrfanTOOR\Console();

        ob_start();
        $c->write($request->getMethod(), 'blue');
        $c->write(' : ', 'yellow');
        $c->writeln('Hello World!', 'red');
        $contents = ob_get_clean();

        $stream = $response->getBody();
        $stream->write($contents);

        return $response->withStatus(666, 'Tah Dah!');
    }
}

$ie = new Engine([
    'debug' => [
        'level' => 1,
    ]
]);

# $ie->add($ie);
# $ie->add(new hello());

$ie->addRoute('GET', '.*', function($request, $response){
    $stream = $response->getBody();
    $stream->write($request->getMethod() . ': Hello World!');

    # $response = $response->withStatus(66);
    return $response;
});

$ie->addRoute('SMART', '.*', 'controller');

$ie->addRoute('ANY', '.*', function($request, $response){
    $stream = $response->getBody();
    $stream->write('default : ' . $request->getMethod());
    return $response;
});

$request = Request::createFromEnvironment();
$request = $request->withMethod('SMART');


$response = Response::create();
$stream = $response->getBody();
# Debug::dump($request);

# $ie->add(new hello());

$ie->run($request, $response);
#print_r($e->container()['environment']);


# Debug::dump($e);


// $a = new Message([
//     'version' => '1.1',
//     'headers' => [
//         'hello' => 'world!',
//     ]
// ]);
//
// $b = $a->with('headers.HELLO', 'again')->with('headers.Content-Type', 'json/text');
//
// $b = $b->without('headers.content-length');
//
// $c = $b->with('version', '2.0');
// $c->set('body', 'wake up neo!');
//
// Debug::dump(assert($a != $b) ? 'T' : 'F');
// Debug::dump(assert($a !== $b) ? 'T' : 'F');
// Debug::dump($a->get('headers'));
// Debug::dump($a);
// Debug::dump($b);
// Debug::dump($c);
//
// $h = $b->get('headers');
// Debug::dump($b);

// $method = new RequestMethod('F*CK');
// Debug::dump($method);


// $headers = new Headers([
//     'hello' => 'world!',
//     'Content-Type' => 'html/text',
// ]);
// $headers->set('content-type', 'json/text');
// Debug::dump($headers->getLine('content-type'));


// $h = Headers::createFromEnvironment();
// Debug::dump($h);


// $request = Request::createFromEnvironment();
// $r = $request
//     ->without('headers.dnt')
//     ->with('headers.user-agent','Agent Smith 1.0')
//     ->with('method', 'HELLO')
//     ->with('version', '2.0')
//     ;
// Debug::dump($r);
// Debug::dump($r->isValid('version') ? 'valid' : 'not valid');
//
// $uri = $r['uri'];
// $uri = $uri->with('userinfo', 'irfan:test');
//
// Debug::dump([$uri->get('port'), $uri->isValid('user') ? 'valid' : 'not valid']);

// $response = new Response();
// $response = $response
//     ->with('body', 'hello world')
//     ;
//
// $response->send();

# Debug::dump($response);
