<?php

namespace IrfanTOOR\Engine;

/*
	Irfan's Engine
	==============
	
	A bare-minimum PHP framework, with the spirit with which the HTTP was invented.
	focussing on the requests and the responses. A swiss-knife for world-wide-web.
	No compliance or gaurantees of any sort. Its a sky-dive in a swim suit!
	
	The objective of this library is to be a Bare-minimum, Embeddable and Educative
*/

use IrfanTOOR\Container;
use IrfanTOOR\Container\Adapter\ArrayAdapter;
use IrfanTOOR\Container\Adapter\FileAdapter;
use IrfanTOOR\Container\Decorator\ReadOnlyDecorator;
use IrfanTOOR\Container\Decorator\NoCaseDecorator;

# to keep track of time
if (!defined('START'))
	define ('START',  	microtime(true));

if (!defined('ROOT'))
	define('ROOT', $_SERVER['DOCUMENT_ROOT'] . "/");

if (!defined('APP'))
	define ('APP',    	ROOT . 'app/');
	
define("IE_PATH", dirname(__FILE__) . "/");
	
class IE {

	public
		$name       = "Irfan's Engine",
		$version    = "0.6",

		$config,
		
		$env,
		$request,
		$response,
				
		$routes    = [],
		$data      = [];

    protected static 
    	$sent      = false,
    	$instance  = null;
		
    public static $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];
    
	function __construct($config=[])
	{
		# register the shutdown function
		register_shutdown_function([$this, "send"]);
		
		# register the autoload function
		spl_autoload_register([$this, "load"]);
		
		set_exception_handler(
			function($obj) {
				$this->response["status"] = 500;
				$this->response["body"] = '<div style="border-left:4px solid #d00; padding:6px;">' .
				 	'<div style="color:#d00">Exception: ' . $obj->getMessage() . '</div><code>' .
				 	$obj->getFile() . ' - ' . $obj->getLine() .
					'</code></div>';
			}
		);
		
		# Config Container
		$this->config = new Container(new ReadonlyDecorator(new ArrayAdapter($config)));
		
		# default timezone
		date_default_timezone_set($this->config->get("timezone", "Europe/Paris"));
		
		# Session
		if (!isset($_SESSION))
			session_start();
		
		# Environment
		$env = array_merge(
			$_SERVER, 
			['session' => $_SESSION],
			$this->config->get('env', [])
		);
				
		# Headers
		$headers = [];
		foreach($env as $k=>$v) {
			if (strpos($k, 'HTTP_') === 0) {
				$k = substr($k, 5);
				# normalize Token
				$k = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", $k))));
				$headers[$k] = $v;
			}
		}
		
		$env['headers'] = $headers;
		$this->env = new Container(new ArrayAdapter($env));
		
		$uri = array_merge(
			[
				'scheme'    => '',
				'user'      => '',
				'password'  => '',
				'host'      => isset($env['HTTP_HOST']) ? $env['HTTP_HOST'] : $env['SERVER_NAME'],
				'port'      => $env['SERVER_PORT'],
				'base_path'  => '',
				'path'      => '',
				'query'     => '',
				# 'fragment'  => '',
    		], 
    	
    		parse_url(
    			"http://" . 
    			(isset($env['HTTP_HOST']) ? $env['HTTP_HOST'] : $env['SERVER_NAME']) . 
    			$env['REQUEST_URI']
    		)
    	);
    	
    	$uri["base_path"] = $path = ltrim(rtrim($uri["path"], "/"), "/") ?: "/";
		
		# Request received
		$this->request = [
			'method'	=> $env['REQUEST_METHOD'],
			'uri'       => $uri, #new Container($uri),
			'headers'   => new Container(new NoCaseDecorator(new ArrayAdapter($env['headers']))),
			'body'      => null,
			'version'   => substr($env['SERVER_PROTOCOL'], 5),
			'get'       => $_GET,
			'post'      => $_POST,
			'cookie'    => $_COOKIE,
			'files'     => $_FILES,
		];
		
		# Response
		$this->response = [
			'status'    => 200,
			'headers'   => new Container(new NoCaseDecorator(new ArrayAdapter([
				'Engine' => $this->name . " " . $this->version,
			]))),
			'body'      => null,
			'version'   => substr($env['SERVER_PROTOCOL'], 5),
			'cookie'    => $_COOKIE,
		];
		
		# Routes		
		$this->routes   = [];
		
		self::$instance = $this;
		if ($this->config->get("debug",0) < 3)
			ob_start();
	}
	
	static function getInstance()
	{
		if (! self::$instance)
			return new IE();
		return self::$instance;
	}
		
	/**
	 * Loads IrfanTOOR\Engine classes or App\ ... classes etc
	 * Note this method is not called directly but is called when ever a required class has not been loaded
	 *
	 * @param String	$class	class to be loaded
	 */
	function load($class) 
	{
		$aclass = explode("\\",$class);
		$c = array_pop($aclass);
		$ns = implode("\\", $aclass);
		$path = ($ns == "IrfanTOOR\\Engine") ? IE_PATH : ROOT . strtolower(str_replace("\\", "/", $ns)) . "/";
		require  $path . str_replace("_", "/", $c) . ".php";
	}
			
	/**
	 * Returns the calling trace
	 *
	 * @param optional $dbt - debug back trace
	 * @param optional $full - true if full call trace is requested or just the recent
	 */
	static function trace($full=false) {
		$trace = '';
		$color = '#d00';
		
		foreach(debug_backtrace() as $er) {
			$file = isset($er['file'])? $er['file']: '';
			$line = isset($er['line'])? $er['line']: '';
			$class = isset($er['class'])? $er['class']: '';
			$func = isset($er['function'])? $er['function']: '';

			if ($func == 'trace' || $func=='error' || $func=='{closure}')
				continue;

			$func_tag = ($class!='')?$class.'=>'.$func.'()': $func.'()';

			# last two sections of the path
			if ($file!='' && FALSE !== strrpos($file, '/')) {
				$x = explode('/',$file);
				$l = count($x);
				$file = $x[$l-2].'/'.$x[$l-1];
				
				$t = ' -- <span style="color:#999">[<span style="color:'.$color.'">'.$file.'</span>] line:<span style="color:'.$color.'">'.$line.'</span> '.$func_tag.'<br>';

				if ($full) {
					$trace .= $t;
					$color = '#36c';
				}
				else {
					if (in_array($func, ['d','dd','dump']))
						$trace = $t;
					break;
				}
			}
		}
		return $trace;
	}
	
	/**
	 * Dumps the variable
	 *
	 * @param mixed $v
	 */
	static function dump($v) {
		$s = print_r($v, 1);
		$s = preg_replace('/\[(.*)\]/U', '[<span style="color:#d00">$1</span>]', $s);
		$trace = self::trace();
		$tag = (is_array($v) || is_object($v)) ? 'pre' : 'code';
		echo '<' . $tag . ' style="padding:0 10px; color:#36c; font-size:13px">' . $s . $trace . '</' . $tag . '>';
	}
		
	function addRoute($methods, $regex, $callback) 
	{
		$this->routes[] = [
			"methods" 	=> $methods, 
			"regex"		=> $regex, 
			"callback"	=> $callback
		];
	}
	
	function run() 
	{
		$path = ltrim(rtrim($this->request["uri"]["path"], "/"), "/") ?: "/";
		$method = $this->request["method"];
		
		$found = false;		
		foreach($this->routes as $route) {
			$handles_route = strpos($route["methods"], $method) !== false || 
						strpos($route["methods"], "ANY") !== false;
			$regex = $route["regex"];
			preg_match('|(' . $regex . ')|', $path, $m);
			$matches_regex = (isset($m[1]) && $m[1] == $path) ? true : false;
			
			if ($handles_route && $matches_regex)
			{		
				$found = true;

				$return = $contents = null;
				$callback = $route["callback"];
				ob_start();
				
				### If its a callback function
				if (is_callable($callback)) {
					$return = $callback();
				}
				
				### If its a method@Controller
				elseif (is_string($callback)) {
					if (($pos = strpos($callback, '@')) !== FALSE) {
						$method = substr($callback, 0, $pos);
						$controller = substr($callback, $pos+1);
					} else {
						$method = "default_method";
						$controller = $callback;
					}
					$c = new $controller();
			
					if (!method_exists($c, $method))
						$method  = 'default_method';
				
					$return = $c->$method();
					
				}
				
				# if something was returned or dumped, process it
				$contents = ob_get_clean();
				if (!$this->response["body"])
					$this->response["body"] = $contents ?: $return;
					
				return;
			}
		}
		
		if (!$found) {
			$this->response["status"] = 404;
			$this->response["body"] = ["404" => "Not found"];
		}
	}
	
	/**
	 * Sends the response ot the client
	 *
	 * @param $response null|[]
	 */
	function send($response=null) {
		$ob = "";
		if ($this->config->get("debug",0) < 3)
			$ob = ob_get_clean();
		
		if (self::$sent)
			return;
		
		$ie = self::getInstance();
		
		if ($response)
			$ie->response = $response;
		else
			$response = $ie->response;
		
		$body = $response["body"];
		
		if (!$body) {
			$response["status"] = 500;
			$body = ["500" => "Server Error"];
		}	
				
		### Debug Info according to debug level configured
		if ($dl = $ie->config->get('debug', 0))
		{
			ob_start(); 
			
			### If there has been an error
			if ($err = error_get_last()) {
				$res["status"] = 500;
				$body = $ob . '<div style="border-left:4px solid #d00; padding:6px;">' .
					'<div style="color:#d00">Error: ' . $err['type'] . ' - ' . $err['message'] . '</div><code>' .
					$err['file'] . ' - ' . $err['line'] .
					'</code></div>';
			}
			
			$t  = microtime(true) - START;
			$te = sprintf(' %.2f mili sec.', $t * 1000);
			
			if ($dl > 1)
				$da["Time elapsed"] = $te;
			else
				$da = $te;

			if ($dl > 1) {
				$files = get_included_files();
				foreach ($files as $k=>$file) {
					$files[$k] = str_replace(ROOT, "", $file);
				}
				$da["Included files"] = $files;
			}
			
			if ($dl > 2) {
				$ie->request["body"] =  "...";
				$ie->response["body"] =  "...";
				$da["IE"] = $ie;
			}
			
			self::dump($da);
			
			echo '</div>';
			
			$debug = ob_get_clean();
		}
		
    	if (!is_string($body)) {
    		$body = json_encode($body);
    		if (!$dl)
    			$response["headers"]->set("Content-Type", "text/json");
    	}
    	    	
		if ($dl)
			$body .= $debug;
		
		
		#### Process and send Headers
    	header('HTTP/' . $response["version"] . ' ' . $response["status"] . ' ' . self:: $phrases[$response["status"]]);
    	
		foreach($response["headers"]->toArray() as $header=>$value) {
			$value = is_array($value) ? $value : [$value];
			$header_string = $header . ": " . implode("' ", $value);
			header($header_string);
		}
		
		### Send the response body
		echo $body;
	}	
}
