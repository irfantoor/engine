<?php
 
use IrfanTOOR\Engine\IE;
 
class IETest extends PHPUnit_Framework_TestCase 
{
	protected
		$env,
		$prophecy,
		$ie;
	
	public function setup() {
		$this->env = [
			"DOCUMENT_ROOT" 	=> __DIR__,
			"REMOTE_ADDR"		=> "::1",
			"REMOTE_PORT" 		=> 12345,
			"SERVER_SOFTWARE" 	=> "PHP 5.6.30 Development Server",
			"SERVER_PROTOCOL" 	=> "HTTP/1.1",
			"SERVER_NAME"		=> "localhost",
			"SERVER_PORT"		=> "8000",
			"REQUEST_URI"		=> "/",
			"REQUEST_METHOD"	=> "GET",
			"SCRIPT_NAME"		=> "/index.php",
			"SCRIPT_FILENAME"	=> __DIR__ . "/IETest.php",
			"PHP_SELF"			=> "/IETest.php",
			"HTTP_HOST"			=> "localhost:8000",
			"HTTP_ACCEPT"		=> "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"HTTP_UPGRADE_INSECURE_REQUESTS" => "1",
			"HTTP_COOKIE"		=> "",
			#"HTTP_USER_AGENT"	=> "Terminal",
			#"HTTP_ACCEPT_LANGUAGE" => "fr-fr",
			#"HTTP_ACCEPT_ENCODING"=> "gzip, deflate",
			#"HTTP_CONNECTION"=> "keep-alive",
			"REQUEST_TIME_FLOAT"=> microtime(1),
			"REQUEST_TIME"=> (int) microtime(1),
		];
		
		$this->prophecy = $this->prophesize("IrfanTOOR\\Engine\\IE");
		$this->ie = $this->prophecy->reveal($this->env);
	}
		
	public function testIEClassExists(){
		print_r($this->ie);
		#$this->assertInstanceOf('IrfanTOOR\Engine\IE', $this->ie);
	}
	
	public function testEnv(){
		#$this->prophecy->getEnv()->willReturn([]);
		#$this->assertEquals($this->env, $this->ie->getEnv());
		#$this->assertEquals(__DIR__, $this->ie->env()["DOCUMENT_ROOT"]);
	}
	
}
