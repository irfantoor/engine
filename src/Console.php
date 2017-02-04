<?php 

namespace IrfanTOOR\Engine;

/**
 * A simple console
 */
class Console
{
	protected static 
		# Colors
		$colors = [
			'black'		=> '30',
			'red'		=> '31',
			'green'		=> '32',
			'yellow'	=> '33',
			'blue'		=> '34',
			'magenta'	=> '35',
			'cyan'		=> '36',
			'white'		=> '37',
		],

		# Background colors
		$bgColors = [
			'bgBlack'	=> '40',
			'bgRed'		=> '41',
			'bgGreen'	=> '42',
			'bgYellow'	=> '43',
			'bgBlue'	=> '44',
			'bgMagenta' => '45',
			'bgCyan'	=> '46',
			'bgWhite'	=> '47',
			'bgNormal'	=> '48',
		],

		# Font textures
		$textures = [
			'normal'	=> '0',
			'bold'		=> '1',
			'light'		=> '2',
			'italic'	=> '3',
			'underline'	=> '4',
			'blink'		=> '5',
			'dim'		=> '6',
			'reverse'	=> '7',
		],

		# Default theme
		$theme = [
			'info' 		=> 'blue',
			'success' 	=> 'green',
			'warning' 	=> 'magenta',
			'error' 	=> 'red',
		];

	/**
	 * Constructs a console
	 */
	public function __construct() {
	}

	/**
	 * Style using ansii escape sequence
	 * 
	 * @param mixed $style can be string like 'red' or 'bgRed' etc., an array of multiple styles or null for reseting style 
	 */
	public function style($styles=null) {
		$color   = 'black';
		$bgColor = 'bgNormal';
		$texture = 'normal';
		$escape  = "[";

		if ($styles && (is_string($styles) || is_array($styles))) {
			if (is_string($styles)) {
				$styles = [$styles];
			}

			foreach($styles as $style) {
				if (isset(self::$theme[$style]))
					$style = self::$theme[$style];

				$color = isset(self::$colors[$style]) ? $style : $color;
				$bgColor = isset(self::$bgColors[$style]) ? $style : $bgColor;
				$texture = isset(self::$textures[$style]) ? $style : $texture;
			}

			$escape .= 
				self::$textures[$texture] . ';' . 
				self::$bgColors[$bgColor] . ';' . 
				self::$colors[$color] . 'm';
		} 
		else {
			$escape .= "0m";
		}

		echo $escape;
	}

	/**
	 * Read a line from input with an optional prompt and optional style
	 * 
	 * @param string $prompt can be string to be prompted before reading from console
	 * @param mixed $style can be null, a style code as string or an array of strings.
	 *
	 * @return the line read from console
	 */
	function read(string $prompt=null, $style=null) {
		if (!$style)
			return readline($prompt);

		$style ? $this->style($style) : 0;
		$prompt ? $this->write($prompt) : 0;
		$line = readline();
		$style ? $this->style() : 0;

		return $line;
	}

	/**
	 * Write a line or a group of lines to output
	 * 
	 * @param mixed $text can be string or an array of strings
	 * @param mixed $style can be null, a style code as string or an array of strings.
	 */	
	function write($text=null, $style=null) {
		if (!$style) {
			if (is_array($text))
				foreach($text as $txt)
					$this->writeln($txt);
			else
				echo $text;
		}
		elseif (is_array($text)) {
			# $max = 0;
			foreach($text as $txt) {
				$max = max(isset($max) ? $max : 0, strlen($txt));
			}
			$outline = str_repeat(' ', $max+4);
			$this->writeln($outline, $style);
			foreach($text as $txt) {
				$len = strlen($txt);
				$pre_space = str_repeat(' ', 2);
				$post_space = str_repeat(' ', $max+2 - $len);
				$this->writeln($pre_space . $txt . $post_space, $style);
			}
			$this->writeln($outline, $style);
		} 
		else {
			$style ? $this->style($style) : 0;
			echo $text;
			$style ? $this->style() : 0;
		}
	}

	/**
	 * Write a line or a group of lines to output and an End of Line finally.
	 * 
	 * @param mixed $text can be string or an array of strings
	 * @param mixed $style can be null, a style code as string or an array of strings.
	 */	
	function writeln($text=null, $style=null) {
		$this->write($text, $style);
		echo PHP_EOL;
	}	
}
