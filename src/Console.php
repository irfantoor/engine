<?php 

namespace IrfanTOOR\Engine;
 
class Console
{
	const
		CMD_NEXT=1,
		CMD_STOP=2;

	protected static 
		$colors,	# Array of colors		
		$bgColors,  # Array of bgColors colors
		$styles,	# styles
		$theme;		# a basic theme

	protected 
		$name,		  # Name of the Console App
		$version,	  # Version of the Console App
		$description, # description of this application

		# App and its args
		$commands, 	  # Application
		$args, 	# Arguments -- passed, parsed options and parsed values
		
		# Basic Commands
		$help 		= false,	# if -h or --help is present
		$verbose	= false;	# if -v is present in arguments

	# Constructs the Console
	public function __construct($name='Console', $version='', $description='') {
		# Initialise the name and the version of this console app.
		$this->name = $name;
		$this->version 	= $version;
		$this->description = $description;

		# Initialise the colors, bgColors and styles -- transpose the arrays
		self::$colors 	= array_flip([
			'30'=>'black','red','green','yellow','blue','magenta','cyan','white'
		]);

		self::$bgColors = array_flip([
			'40'=>'bgBlack','bgRed','bgGreen','bgYellow','bgBlue','bgMagenta','bgCyan','bgWhite', 'bgTransparent'
		]);

		self::$styles 	= array_flip([
			'normal','bold','light','italic','underline','blink','dim','reverse'
		]);

		self::$theme = [
			'info' => 'blue',
			'success' => ['green', 'reverse'],
			'warning' => 'red',
			'error' => ['red','bold']
		];

		# Initialise the basic commands
		$this->command('help',		'h|help', 		'shows help');
		$this->command('version',	'V|version',	'shows version');
		$this->command('verbose',	'v',			'be verbose');		
	}

	# for calling as $this->red('hello') or $this->bold('Its a test')
	public function __call($func, $para) {
		if (isset(self::$theme[$func]))
			$args = self::$theme[$func];
		else
			$args = [$func];
		
		if (!is_array($args))
				$args = [$args];

		$txt = array_shift($para);
		$this->outln($txt, array_merge($args, $para));
	}

	# Defining a new command
	public function command($name, $args, $short_help, $optional=[]) {
		$this->commands[$name] = array_merge([
			'name' => $name,
			'args' => $args,
			'short_help' => $short_help,
		], $optional);
	}

	public function help() {
		$helps = [];
		$s_options = [];
		$options = [];

		foreach($this->commands as $name => $command) {
			$cmd_options = [];
			$cmd_args = explode('|', $command['args']);
			foreach($cmd_args as $option) {
				$option = trim($option);
				if (strlen($option) == 1) {
					$s_options[] = $option;
					$cmd_options[] = '-' . $option;
				} else {
					$cmd_options[] = '--' . $option;
				}
			}
			$options = implode(', ', $cmd_options);
			$helps[$options] = $command['short_help'];
		}

		$this->info('usage: '.$this->name . ' [ -'. implode('', $s_options) .' ] ...');

		foreach ($helps as $key => $value) {
			$l = 19 - strlen($key);
			$space = str_repeat(' ', $l);
			$this->out('    ' . $key . $space, 'green');
			$this->outln($value);
		}
		$this->outln('');

		# stop processing commands after this command
		return self::CMD_STOP;
	}

	public function version() {
		$this->info($this->name . ' v' . $this->version);

		# stop processing further commands after this command
		return self::CMD_STOP;
	}	

	public function verbose() {
		$this->verbose = true;

		# continue processing further commands
		return self::CMD_NEXT;
	}	

	public function run() {
		# Stop processing if its not launched from console	
		if ('cli' !== php_sapi_name())
			die("Can only be run from command line");

		# Initialize the passed arguments and parse these arguments
		$this->args['env'] = $_SERVER;

		# parse the arguments
		$this->parse();

		# Check commands
		$default=[];
		$found = [];
		$parsed = $this->args['parsed'];

		# V=>version, version=>version
		$args2cmd = [];
		foreach($this->commands as $name=>$command) {
			$cmd_args = explode('|', $command['args']);
			foreach($cmd_args as $arg) {
				$args2cmd[$arg] = $name;
			}
		}

		# commands found in the parsed arguments and their count
		foreach($this->commands as $name=>$command) {
			# is the command defined as default command
			isset($command['default']) && $command['default'] && ($default[$name] = true);

			$cmd_args = explode('|', $command['args']);
			foreach($cmd_args as $arg) {
				$arg = trim($arg);
				if (in_array($arg, array_keys($parsed))) {
					$k = $args2cmd[$arg];
					$v = $parsed[$arg];
					$v = is_array($v) ? count($v) : $v; 
					$found[$k] = isset($found[$k]) ? $found[$k] + $v : $v;
				}
			}
		}

		$found = array_merge($found, $default, ['help'=>1]);
		$this->args['found'] = $found;

		# Check any other requested commands
		foreach($found as $cmd => $count) {
			if (isset($this->commands[$cmd]['closure']))
				$cmd_next = $this->commands[$cmd]['closure']($this);
			else
				$cmd_next = $this->$cmd();

			if ($cmd_next == self::CMD_NEXT)
				continue;
			# elseif ($cmd_next == self::CMD_STOP)
			#	break;
			else
				break;
		}
	}

	# Parse the passed arguments or the arguments passed to the application
	public function parse($args = null) {
		$passed = $args;

		if (!$args) {
			$passed = $this->args['env']['argv'];
			array_shift($passed);
		}

		$parsed = [];
		$values = [];

		$last = null;
		$waiting = null;
		foreach($passed as $arg) {
			# arg starts with a --
			if (0 == strncmp($arg, '--', 2)) {
				if (strlen($arg)>3) {
					$last = $raw = substr($arg, 2);
					if (false !== ($pos=strpos($raw, '='))) {
						list($k, $v) = explode('=', $raw); # overloading two conditions
						isset($parsed[$k])? $parsed[$k][] = $v : $parsed[$k] = [$v];
						$last = null;
					} else {
						isset($parsed[$raw])? $parsed[$raw][] = '' : $parsed[$raw] = [''];
					}
				}
				$waiting = null;
			}

			# arg starts with a single -
			elseif (0 == strncmp($arg, '-', 1)) {
				$raw = substr($arg, 1);
				$l = strlen($raw);
				# If multiple single letter args are present after '-'
				for ($i=0; $i<$l; $i++) {
					$r = substr($raw, $i, 1);
					isset($parsed[$r]) ? $parsed[$r] += 1 : $parsed[$r] = 1;
				}
				$last = null;
				$waiting = null;
			}

			# its a value
			else {
				if ($last) {
					($arg == '=') ? $waiting = $last : $parsed['values'][] = $arg;
				}
				elseif ($waiting) {
					isset($parsed[$waiting]) ? array_pop($parsed[$waiting]) : 0;
					isset($parsed[$waiting]) ? $parsed[$waiting][] = $arg : $parsed[$waiting] = [$arg];
					$waiting = null;
				}
				else {
					$values[] = $arg;
					$waiting = null;					
				}
				$last = null;
			}
		}

		if (!$args) {
			$this->args['parsed'] = $parsed;
			$this->args['values'] = $values;
		}

		return [
			'parsed' => $parsed,
			'values' => $values
		];
	}	

	public function escape($args=null) {
		$color = 'black';
		$bgColor = 'bgTransparent';
		$style = 'normal';

		if ($args) {
			if (is_string($args)) {
				$args = [$args];
			}

			foreach($args as $arg) {
				$color = isset(self::$colors[$arg]) ? $arg : $color;
				$bgColor = isset(self::$bgColors[$arg]) ? $arg : $bgColor;
				$style = isset(self::$styles[$arg]) ? $arg : $style;
			}
		}

		$escape = "[" . self::$styles[$style] . ';' . self::$bgColors[$bgColor] . ';' . self::$colors[$color] . 'm';
		echo $escape;		
	}

	public function in($prompt='') {
		echo $prompt .' ';
		return readline();
	}

	public function out($txt, $args=null) {
		if ($args)
			$this->escape($args);
		
		print_r( $txt );

		if ($args)
			$this->escape('');
	}

	public function outln($txt, $args=null) {
		$this->out($txt, $args);
		echo PHP_EOL;
	}

	public function banner($txt, $args=['blue', 'reverse']) {
		if (is_string($txt))
			$txt = [$txt];
		
		$max = 0;
		foreach($txt as $line) {
			$max = max($max, strlen($line));
		}

		foreach($txt as $line) {
			$l = strlen($line);
			$outline = str_repeat(' ', $max+14);
			$pre_space = str_repeat(' ', 7);
			$post_space = str_repeat(' ', $max - $l);
			$this->outln($outline, $args);
			$this->outln($pre . $txt . $space, $args);
			$this->outln($outline, $args);
		}
	}
}
