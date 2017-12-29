<?php

namespace IrfanTOOR\Engine;


# todo -- convert to either Sqlit3 or use PDO in Container\Storage
use IrfanTOOR\Engine\Debug;

use PDO;
use IrfanTOOR\Exception;

class Model
{
	protected
		$db        = null,
		$schema    = "",
		$page      = 1,
		$per_page  = 15,
		$int_pages = 7,

		$select    = "*",
		$table     = "",
		$where     = "1=1",
		$bind      = [],
		$order     = "id DESC",

		$from      = "0",
		$to        = "14",
		$limit     = "0,15",

		$count     = 0,
		$total     = 0;

	function __construct($args=[])
	{
		foreach (['per_page', 'int_pages'] as $arg) {
			if (isset($args[$arg]))
				$this->{$arg} = $args[$arg];
		}

		$class = explode('\\', strtolower(get_called_class()));
		$this->table = array_pop($class);

		$db_file = ROOT . "database/" . $this->table . ".sqlite";
		if (!file_exists($db_file)) {
			$message = "$db_file does not exist<br>" . PHP_EOL;
			$message .= "<pre>SCHEMA:" . PHP_EOL;
			$message .= $this->schema();
			$message .= "</pre>";

			throw new Exception($message);
		}

		$this->db = new PDO( "sqlite:" .  $db_file);

		/*
		$this->schema = [
			"id"         => "integer not null primary key autoincrement",

			"created_at" => "datetime null",
			"updated_at" => "datetime null",
		];

		$this->indecies = [
			# ["index"  => "field"],
			# ["unique" => "unique_field"],
		];
		*/
	}

	function schema() {
		# Create Table
		$schema = 'CREATE TABLE "' . $this->table . '" (';
		$sep = "";
		foreach($this->schema as $k=>$v) {
			$schema .= $sep . '"' . $k . '" ' . $v;
			$sep = ", ";
		}
		$schema .= ');' . PHP_EOL;

		# Indecies
		foreach($this->indecies as $index) {
			foreach($index as $type=>$field) {
				$type = strtolower($type);
				switch ($type) {
					case 'u':
					case 'unique':
						$schema .=  'CREATE UNIQUE INDEX ';

						break;
					case 'i':
					case 'index':
					default:
						$schema .=  'CREATE INDEX ';
				}
				$schema .=  '"' . "{$this->table}_{$field}_{$type}" . '" on "'.$this->table.'" ("'.$field.'");' . PHP_EOL;
			}
		}

		return $schema;
	}

	function select ($select="*") {
		$this->select = $select;

		return $this;
	}

	/*
	function table($table) {
		$this->table = $table;

		return $this;
	}
	*/

	function where($where)
	{
		$this->where = $where;
		return $this;
	}

	function bind($args)
	{
		if (!is_array($args))
			throw new Exception("An array should be passed as bind arguments", 1);

		$this->bind = $args;
		return $this;
	}

	function order($order) {
		$this->order = $order;

		return $this;
	}

	/**
	 * Sets the limit of the succedng operations
	 *
	 * @para integer $mixed is either the count or if the second option is provided is the page no.
	 * @para integer $per_page - per page results optional
	 *
	 * returns null or array of results
	 */
	function limit($page, $per_page=null)
	{
		$page = $page ?: 1;
		$this->page = $page;

		if ($per_page) {
			$this->per_page = $per_page;

			$this->from = ($this->page - 1) * $this->per_page;
		# $this->to = $this->from + $this->per_page - 1;
			$this->limit = "{$this->from}, {$this->per_page}";
		}
		else {
			$this->limit = "{$this->page}";
		}
	}

	private function fetch($sql) {
		if (strpos($this->where, ':')) {
			$q = $this->db->prepare($sql);
			foreach($this->bind as $k=>$v) {
				$q->bindValue($k, $v);
			}
			$q->execute();
		}
		else {
			$q = $this->db->query($sql);
		}
		$rows = [];
		if ($q) {
			while($row = $q->fetch()) {
				$rows[] = $row;
			}
		}
		return $rows;
	}

	function get($page = null, $per_page = null)
	{
		$rows = null;
		$this->limit($page, $per_page);
		$sql = "SELECT count(*) FROM {$this->table} WHERE ({$this->where})";
		$this->total = $this->fetch($sql)[0][0];
		if ($this->total) {
			$rows = [];
			if ($this->page > $this->total)
				$this->page = $this->total;

			# $this->from = ($this->page - 1) * $this->per_page;
			# $this->to = $this->from + $this->per_page - 1;

			$sql = "SELECT {$this->select} FROM {$this->table} WHERE ({$this->where}) ORDER BY {$this->order} LIMIT {$this->limit}";
			$rows = $this->fetch($sql);
		}

		$this->count = count($rows);
		return $rows;
	}

	function pagination() {
		ob_start();

		$d0 = $this->int_pages;
		$d1 = ($d0-1)/2;
		$d2 = $d1 + 1;

		$first = 1;
		$current = $this->page;
		if (($current - $d2) < 1)
			$first=0;

		$last  = ceil($this->total / $this->per_page);

		if ($last<=1)
			return;

		$prev = $current - 1;

		$next = $current + 1;
		if ($next > $last)
			$next=0;

		$from = $current - $d1;
		if ($from < 1)
			$from = 1;

		$to = $from + $d0 -1;

		if ($to > $last)
			$to = $last;

		if ($to < $from)
			$to = $from;

		if (($current + $d2) > $last)
			$last=0;

		echo PHP_EOL . '<ul class="pagination">' . PHP_EOL;

		if ($prev)
			echo '<li><a href="?page=' . $prev . '" rel="prev">&laquo</a></li>' . PHP_EOL;
		else
			echo '<li class="disabled"><span>&laquo</span></li>' . PHP_EOL;

		if ($first) {
			echo '<li><a href="?page=' . $first . '">' . $first . '</a></li>' . PHP_EOL;
			if (($from - $first) > 1)
				echo '<li class="disabled"><span>...</span></li>' . PHP_EOL;
		}

		for($i = $from; $i <= $to; $i++) {
			if ($i == $current)
				echo '<li class="active"><span>' . $current . '</span></li>'. PHP_EOL;
			else
				echo '<li><a href="?page=' . $i . '">' . $i . '</a></li>' . PHP_EOL;
		}

		if ($last) {
			if (($last - $to) > 1)
				echo '<li class="disabled"><span>...</span></li>' . PHP_EOL;

			echo '<li><a href="?page=' . $last . '">' . $last . '</a></li>' . PHP_EOL;
		}

		if ($next)
			echo '<li><a href="?page=' . $next . '" rel="next">&raquo</a></li>' . PHP_EOL;
		else
			echo '<li class="disabled"><span>&raquo</span></li>' . PHP_EOL;

		echo '</ul>' . PHP_EOL;

		return ob_get_clean();
	}
}
