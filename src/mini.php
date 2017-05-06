<?php

namespace IrfanTOOR\Engine;

define("START", microtime(1));
define("PATH",  dirname(__FILE__) . "/");

class ie {
	public
		$name       = "Irfan's Engine",
		$version    = "0.5",

		$config,
		
		$env,
		$request,
		$response,
				
		$routes    = [],
		$data      = [];
		
    public static 
    	$sent      = false,
    	$instance  = null;

	function __construct($config=[])
	{
		# register the shutdown function
		register_shutdown_function([$this, "send"]);
		
		# regsiter the autoloader
		# spl_autoload_register([$this, "load"]);
		
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
		$this->config = $config;
		
		# default timezone
		date_default_timezone_set(isset($this->config["timezone"]) ? $this->config["timezone"] : "Europe/Paris");
		
		# Session
		if (!isset($_SESSION))
			session_start();
		
		# Environment
		$env = array_merge(
			$_SERVER, 
			['session' => $_SESSION],
			(isset($this->config["env"]) ? $this->config['env'] : [])	
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
    			(($e = $env['HTTP_HOST']) ? $e : $env['SERVER_NAME']). 
    			$env['REQUEST_URI']
    		)
    	);
		
		# Request received
		$this->request = [
			'method'	=> $env['REQUEST_METHOD'],
			'uri'       => $uri, #new Container($uri),
			'headers'   => $headers, #$env['headers'], # new ContainerCI($env['headers']),
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
			'headers'   => [
				"Content-Type" => "text/html",
				"Engine" => $this->name . " v" . $this->version,
			], #new ContainerCI([]),
			'body'      => null,
			'version'   => substr($env['SERVER_PROTOCOL'], 5),
			'cookie'    => $_COOKIE,
		];
		
		$this->env = $env;
		
		# Routes		
		$this->routes   = [];
		
		self::$instance = $this;
		ob_start();
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
	static function trace() {
		$trace = '';
		$color = '#d00';
		
		foreach(debug_backtrace() as $er) {
			$file = isset($er['file'])? $er['file']: '';
			$line = isset($er['line'])? $er['line']: '';
			$class = isset($er['class'])? $er['class']: '';
			$func = isset($er['function'])? $er['function']: '';

			#if ($func == 'trace' || $func=='error' || $func=='{closure}')
			#	continue;

			$func_tag = ($class!='')?$class.'=>'.$func.'()': $func.'()';

			# last two sections of the path
			if ($file!='' && FALSE !== strrpos($file, '/')) {
				$x = explode('/',$file);
				$l = count($x);
				$file = $x[$l-2].'/'.$x[$l-1];
				
				$t = ' -- <span style="color:#999">[<span style="color:'.$color.'">'.$file.'</span>] line:<span style="color:'.$color.'">'.$line.'</span> '.$func_tag.'<br>';

				if (in_array($func, ['d','dd','dump'])) {
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
	
	/**
	 * Runs the engine - process route and execute the matching route
	 */
	function run() 
	{
		$path = ($p=ltrim(rtrim($this->request["uri"]["path"], "/"), "/")) ? $p : "/";
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
	function send($res=null) {
		ob_get_clean();
		if (self::$sent)
			return;
		
		$ie = ie::$instance;
		
		if ($res)
			$ie->response = $res;
		else
			$res = $ie->response;
			
		$body = $res["body"];

		if (!$body) {
			$res["status"] = 500;
			$body = ["500" => "Server Error"];
		}	
				
		### Debug Info according to debug level configured
		if ($dl = isset($ie->config['debug'])? $ie->config['debug'] : 0)
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
			
			echo '<div style="border-left:4px double #36c; padding:6px;">';
			# echo '<div style="color:#d00; padding: 10px; ">' . $this->name . ' v'. $this->version . ' -- debug level: ' . $dl . '</div>';
			
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
				$ie->request["body"] =  "...";
				$ie->response["body"] =  "...";
				$da["IE"] = $ie;
			}
			
			ie::dump($da);
			
			echo '</div>';
			
			$debug = ob_get_clean();
		}
		
    	if (!is_string($body)) {
    		$body = json_encode($body);
    		if (!$debug)
    			$res["headers"]["Content-Type"] = "text/json";
    	}		
    	    	
		if ($debug)
			$body .= $debug;
		
		
		#### Process and send Headers
    	$res["headers"]["Content-Length"] = strlen($body);
    	
    	header('HTTP/' . $res["version"] . ' ' . $res["status"] . ' ' . "--");
    	
		foreach($res["headers"] as $header=>$value) {
			$value = is_array($value) ? $value : [$value];
			$header_string = $header . ": " . implode("' ", $value);
			header($header_string);
		}
		
		### Send the response body
		echo $body;
	}
}
