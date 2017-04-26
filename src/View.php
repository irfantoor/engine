<?php

namespace IrfanTOOR\Engine;

class View
{
	protected 
		$name,
		$contents,
		$data;
	
	public function __construct($name) {
		$this->name = $name;		
		$this->contents = "";
		$this->data = [];
	}
	
	public function show($data=[])
	{	
		$view = APP . "views/" . $this->name . ".php";
		
		if (!is_file($view))
			throw new Exception("ViewNotFoundException: View $this->name($view) not found");
		
		ob_start();
		require $view;
		$view = ob_get_clean();
		
		foreach($data as $k=>$v) {
			$view = preg_replace('|\{\{\s*\$' . $k . '\s*\}\}|s', $v, $view);
		}
		
		echo $view;
	}
}
