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
		$version    = "1.0",

		$config,
		
		$env,
		$request,
		$response,
				
		$routes    = [],
		$data      = [];
		
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
    
    protected static $instance;

	function __construct($config=[])
	{
		# register the shutdown function
		register_shutdown_function([$this, "send"]);
		
		# regsiter the autoloader
		spl_autoload_register([$this, "load"]);
	
		
		set_exception_handler(
			function($obj) {
				ob_start();
				$this->dump($obj);
				$this->response["body"] = ob_get_clean();
				
				$this->response["status"] = 500;
			}
		);
		
		# Config Container
		$this->config = new Container($config);
		
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

		$this->env = new Container($env);		
		
		$uri = array_merge(
			[
				'scheme'    => '',
				'user'      => '',
				'password'  => '',
				'host'      => isset($env['HTTP_HOST']) ? $env['HTTP_HOST'] : $env['SERVER_NAME'],
				'port'      => $env['SERVER_PORT'],
				# 'basePath'  => '---',
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
		
		# Request received
		$this->request = [
			'method'	=> $env['REQUEST_METHOD'],
			'uri'       => $uri, #new Container($uri),
			'headers'   => new ContainerCI($env['headers']),
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
			'headers'   => new ContainerCI([]),
			'body'      => null,
			'version'   => substr($env['SERVER_PROTOCOL'], 5),
			'cookie'    => $_COOKIE,
		];
		
		# Routes		
		$this->routes   = [];
		
		self::$instance = $this;
		ob_start();
	}
	
	static function getInstance()
	{
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
		#echo $path . str_replace("_", "/", $c) . ".php";
		@require  $path . str_replace("_", "/", $c) . ".php";
	}
			
	/**
	 * Returns the calling trace
	 *
	 * @param optional $dbt - debug back trace
	 * @param optional $full - true if full call trace is requested or just the recent
	 */
	static function trace($dbt=null, $full=true) {
		$dbt = !$dbt ? debug_backtrace() : $dbt;
		$trace = '';
		$color = '#d00';
		
		foreach($dbt as $er) {
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
		$trace = self::trace(null, false);
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
			$handles_route = 	strpos($route["methods"], $method) !== false || 
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
		ob_get_clean();
		
		if ($response)
			$this->response = $response;
					
		$body = $this->response["body"];

		if (!$body) {
			$this->response["status"] = 500;
			$body = ["500" => "Server Error"];
		}	
				
		### Debug Info according to debug level configured
		if ($dl = $this->config->get('debug'))
		{
			ob_start(); 
			
			### If there has been an error
			if ($err=error_get_last()) {
				$response["body"] = '<div style="border-left:4px solid #d00; padding:6px;">' .
					'<div style="color:#d00">Error: ' . $err['type'] . ' - ' . $err['message'] . '</div><code>' .
					$err['file'] . ' - ' . $err['line'] .
					'</code></div>';
			}
			
			echo '<hr><div style="border-left:4px solid #ddd; padding:6px;">';
			echo '<div style="color:#d00; padding: 10px; ">' . $this->name . ' v'. $this->version . ' -- debug level: ' . $dl . '</div>';
			
			$t = microtime(true) - START;
			$da["Time elapsed"] = sprintf(' %.2f mili sec.', $t * 1000);

			if ($dl > 1) {
				$files = get_included_files();
				foreach ($files as $k=>$file) {
					$files[$k] = str_replace(ROOT, "", $file);
				}
				$da["Included files"] = $files;
			}
			
			if ($dl > 2) {
				$this->request["body"] =  "...";
				$this->response["body"] =  "...";
				$da["IE"] = $this;
			}
			
			$this->dump($da);
			
			echo '</div>';
			
			$debug = ob_get_clean();
		}
		
    	if (!is_string($body)) {
    		$body = json_encode($body);
    		if (!$debug)
    			$this->response["headers"]->set("Content-Type", "text/json");
    	}		
    	    	
		if ($debug)
			$body .= $debug;
		
		
		#### Process and send Headers
    	$this->response["headers"]->set("Content-Length", strlen($body));
    	
    	header('HTTP/' . $this->response["version"] . ' ' . $this->response["status"] . ' ' . self:: $phrases[$this->response["status"]]);
    	
		foreach($this->response["headers"]->raw() as $header=>$value) {
			$value = is_array($value) ? $value : [$value];
			$header_string = $header . ": " . implode("' ", $value);
			header($header_string);
		}
		
		### Send the response body
		echo $body;
	}	
}
