<?php

/*************************
  Dynherit
  ************************
  Copyright (c) 1999-2012 Komrod Dev Team
  v0.62 originally written by Komrod

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 3
  as published by the Free Software Foundation.

**********************************************/
class dynherit
{

	/**
	 * List of variables
	 */
	
	protected $blocks = array(); // store the blocks of script


	/**
	 * These characters are tags to limit parts of the code.
	 */
	protected $block_start = 3; // block start is tagged with chr(3)
	protected $block_end = 8; // block end is tagged with chr(8)
	protected $string_start = 2; // string start is tagged with chr(2)
	protected $string_end = 8; // string end is tagged with chr(8)

	
	protected $string = array(); // store the protected strings


	/**
	 * Regular expressions
	 */
	protected $regex_string = '(\'\')|("")|("(.|[\s\r\n\t\x02\x03\x08])*?[^\\\]")|(\'(.|[\s\r\t\n\x02\x03\x08])*?[^\\\]\')'; // for strings
	protected $regex_heredoc = '<<<\'?([^\s^\n^\r]+)\'?[\s\r\n\t]?(.|[\s\r\n\t])*?\1'; // for heredoc and nowdoc
	protected $regex_comment = '((\/\/)(.|[\s\r\x02\x03\x08])*?(\n))|(\/\*(.|[\s\r\n\x02\x03\x08])*?(\*\/))'; // for comments
	protected $regex_class = 'class([\t\s\r\n]+)([a-zA-Z0-9_]+)([\t\s\r\n]*\{[\x03]([0-9]+)[\x08])\}';
	protected $regex_class_search_begin = 'class([\t\s\r\n]+)';
	protected $regex_class_search_end = '([\t\s\r\n]*\{[\x03]([0-9]+)[\x08])\}';


	/////////////////////////////////////////////// Main ///////////////////////////////////////////////

	
	/**
	 * Load the classes with dynamic inheritance from parent to child and include it in with Eval()
	 * @param $classes Array of classes with name and file
	 * @return The instance of the new class
	 */
	static function exec($classes)
	{
		$extends='';
		foreach ($classes as $item)
		{
			dynherit::inheritance($item['file'], $item['name'], $extends);
			$extends = $item['name'];
		}
		return new $extends();
	}

	/**
	 * Load the classes with dynamic inheritance from parent to child and create the cache file. Load directly from cache file if the file exists.
	 * @param $classes Array of classes with name and file
	 * @param $cache_file File cache for those classes
	 * @return The instance of the new class
	 */
	static function exec_cache($classes, $cache_file)
	{
		$extends='';

		if (file_exists($cache_file))
		{
			require($cache_file); // just include the cache files
			$extends = $classes[count($classes)-1]['name'];
		} else
		{
			foreach ($classes as $item)
			{
				dynherit::inheritance_cache($item['file'], $item['name'], $extends, $cache_file);
				$extends = $item['name'];
			}
			require($cache_file);
		}
		return new $extends();
	}

	
	/////////////////////////////////////////////// Inheritance ///////////////////////////////////////////////

	/**
	 * Get class code from a file, set the new extends, run the new script with Eval()
	 * @param $file File of the class
	 * @param $class_name Name of the class
	 * @param $extends Extends to add to the class
	 */
	static function inheritance($file, $class_name, $extends)
	{
		$code = file_get_contents($file);
		$parser = new dynherit();
		$class = $parser->fetch_class($code, $class_name);

		$new = 'class '.$class['name'];
		if ($extends!='') $new .= ' extends '.$extends;
		$new .= ' { '.$class['source'].' } ';
		eval($new);

		return true;
	}


	/**
	 * Get class code from a file, set the new extends, append the new script in the cache file. Script is not executed!
	 * @param $file File of the class
	 * @param $class Name of the class
	 * @param $extends Extends to add to the class
	 */
	static function inheritance_cache($file, $class_name, $extends, $dest)
	{
		$code = file_get_contents($file);

		$parser = new dynherit();
		$class = $parser->fetch_class($code, $class_name);
		if (count($class)==0) return false;

		if (!file_exists($dest)) $new = '<?php '; else $new = '';
		$new .= 'class '.$class['name'];
		
		if ($extends!='') $new .= ' extends '.$extends;
		$new .= '{'.$class['source'].'}';

		$h = fopen($dest, 'a+');
		fwrite($h, $new);

		return true;
	}
	
	
	///////////////////////////////////////////////// Fetch ///////////////////////////////////////////////////


	/**
	 * Parse the code and search for the class name
	 * @param string $code PHP source code
	 * @return array Return the class array. If class is not found, returns an empty array
	 */
	public function fetch_class($code, $class_name)
	{
		// remove comments
		$code = $this->comments_remove($code);

		// protect strings
		$code = $this->strings_protect($code);

		// get class
		$class = $this->class_get($code, $class_name);
		return $class;
	}


	/**
	 * Parse the code and search for all the classes
	 * @param string $code PHP source code
	 * @return array Return the classes array. If no classes found, returns an empty array
	 */
	public function fetch_classes($code)
	{
		// remove comments
		$code = $this->comments_remove($code);

		// protect strings
		$code = $this->strings_protect($code);

		// get class
		$classes = $this->classes_get($code);
		return $classes;
	}


	///////////////////////////////////////////////// Classes ////////////////////////////////////////////////


	/**
	 * Return all the classes found in the source code
	 * @param string $code
	 * @param string $class_name Name of the class to find. If the string is empty, return
	 * @param bool $blocks_protected True if the code is already block protected (blocks replaced by character tags and numbers in the source)
	 * @param bool $blocks_registered True if the blocks are already registered (blocks are stored in this->blocks
	 * @return array Return the classes name and source in an array
	 */
	public function classes_get($code, $blocks_protected = false, $blocks_registered = false)
	{
		$classes = array();

		// protect blocks
		if (!$blocks_protected) $code = $this->blocks_protect($code, !$blocks_registered);

		$pat = '/'.$this->regex_class.'/';
		preg_match_all($pat, $code, $matches);
		$count=count($matches);
		if ($count===0) return array();

		for($t=0; $t<$count; ++$t)
		{
			$class = array();
			$class['name'] = $matches[2][$t];
			$class['source'] = $this->strings_retrieve($this->blocks[intval($matches[4][$t])]['source']);
			$classes[] = $class;
		}
		return $classes;
	}


	/**
	 * Register the class named $class_name found in the source code
	 * @param string $code
	 * @param string $class_name Name of the class to find
	 * @param bool $blocks_protected True if the code is already block protected (blocks replaced by character tags and numbers in the source)
	 * @param bool $blocks_registered True if the blocks are already registered (blocks are stored in this->blocks
	 * @return array Return the name and source of the class in an array
	 */
	public function class_get($code, $class_name, $blocks_protected = false, $blocks_registered = false)
	{
		// protect blocks
		if (!$blocks_protected) $code = $this->blocks_protect($code, !$blocks_registered);

		$pat = '/'.$this->regex_class_search_begin.$class_name.$this->regex_class_search_end.'/';
		preg_match_all($pat, $code, $matches);
		if (count($matches)===0) return array();
		$class = array();
		$class['name'] = $class_name;
		$class['source'] = $this->strings_retrieve($this->blocks[intval($matches[3][0])]['source']);

		return $class;
	}


	///////////////////////////////////////////////// Blocks ////////////////////////////////////////////////

	/**
	 * Protect the script in blocks within "{" and "}" and replace it with an index
	 * @param bool $register If True, add the strings found from code in this->string. Default True.
	 */
	private function blocks_protect($code, $register=true)
	{
		// register blocks in this->blocks
		if ($register) $this->blocks_register($code);
		$blocks = $this->blocks;

		// replace every block begining by the last one
		for ($t=count($blocks)-1; $t>=0; $t--)
		{
			if ($blocks[$t]!==true) $code = str_replace($blocks[$t]['source'], chr($this->block_start).$t.chr($this->block_end), $code);
		}
		return $code;
	}


	/**
	 * Registers all blocks within "{" and "}" in this->blocks
	 * @param $code Source code
	 * @return string Modified code
	 */
	private function blocks_register($code)
	{
		$block_start = array();
		$offset=0;

		$l=strpos($code, '{', $offset);
		$r=strpos($code, '}', $offset);
		while ($l!==false || $r!==false)
		{
			if ($r===false || $l!==false && $l<$r)
			{
				$block_start[] = $l;
				$offset = $l+1;
			} else
			{
				$start = array_pop($block_start)+1;
				if (count($block_start)===0) $this->blocks[] = array( 'source' => substr($code, $start, $r-$start) );
				$offset = $r+1;
			}
			$l=strpos($code, '{', $offset);
			$r=strpos($code, '}', $offset);
		}
	}

	/**
	 * Clean the blocks array
	 */
	private function blocks_clean()
	{
		$this->blocks = array();
	}

	/**
	 * Retrieve the blocks of a previously protected code,
	 * chr(3) + index + chr(8) : index is the block index in this->blocks
	 * @param string $code
	 * @return string Modified code
	 */
	private function blocks_retrieve($code)
	{
		$pat = '/'.chr($this->block_start).'(.)*?'.chr($this->block_end).'/';
		preg_match_all($pat, $code, $matches);
		foreach($matches[0] as $key => $value)
		{
			$int = intval(str_replace(array(chr($this->block_start), chr($this->block_end)), '', $value));
			$str = $this->blocks[$int]['source'];
			$code = str_replace($value, $str, $code);
		}

		return $code;
	}

	//////////////////////////////////////////////// Comments ///////////////////////////////////////////////

	/**
	 * Removes all the comments, do not remove comments inside quoted strings
	 * @param string $code Source code
	 * @return string Modifed code
	 */
	private function comments_remove($code)
	{
		$pat = '/'.$this->regex_string.'|'.$this->regex_comment.'/';
		preg_match_all($pat, $code, $matches);
		$strings = string_sort_by_len($matches[0]);
		foreach ($strings as $str)
		{
			//			if (substr($str, 0, 1)==="/")
			if (strncmp($str, '/', 1)===0)
			{
				$code = str_replace($str, "\n", $code);
			}
		}
		return $code;
	}


	/////////////////////////////////////////////// Strings ///////////////////////////////////////////////

	/**
	 * Protects the quoted strings, puts it in an array and replaces it.
	 * chr(2) + index + chr(8) : index is the string index in this->strings
	 * @param string $code
	 * @param bool $register If True, add the strings found into this->string. Default True.
	 * @return string The new code
	 */
	private function strings_protect($code, $register=true)
	{
		// registers the quoted strings from the code in this->strings
		if ($register)
		{
			$this->strings_heredoc_register($code);
			$this->strings_register($code);
			$this->strings = string_sort_by_len($this->strings);
		}

		// Replace the strings
		foreach($this->strings as $key => $value)
		{
			$code = str_replace($value, chr($this->string_start).$this->strings_index($value).chr($this->string_end), $code);
		}

		return $code;
	}

	/**
	 * Return the index of the string into this->strings, or False if not found
	 * @param string $string
	 * @return int
	 */
	private function strings_index($string)
	{
		return array_search($string, $this->strings);
	}

	/**
	 * Registers the Heredoc string into this->strings
	 * @param string $code Source code
	 */
	private function strings_heredoc_register($code)
	{
		$pat = '/'.$this->regex_heredoc.'/';
		preg_match_all($pat, $code, $matches);
		foreach ($matches[0] as $string)
		{
			$this->strings[] = $string;
		}
	}

	/**
	 * Registers the quoted strings into this->strings
	 * @param string $code Source code
	 */
	private function strings_register($code)
	{
		$pat = '/'.$this->regex_string.'/';
		preg_match_all($pat, $code, $matches);
		foreach ($matches[0] as $string)
		{
			$this->strings[] = $string;
		}
	}

	/**
	 * Clean the strings array
	 */
	private function strings_clean()
	{
		$this->strings = array();
	}

	/**
	 * Retrieve the quoted strings in a previously protected code,
	 * chr(this->string_start) + index + chr(this->string_end) : index is the array index in this->strings
	 * @param string $code
	 * @return string Modified code
	 */
	private function strings_retrieve($code)
	{
		$pat = '/'.chr($this->string_start).'(.)*?'.chr($this->string_end).'/';
		preg_match_all($pat, $code, $matches);
		foreach($matches[0] as $key => $value)
		{
			$int = intval(str_replace(array(chr($this->string_start), chr($this->string_end)), '', $value));
			$str = $this->strings[$int];
			$code = str_replace($value, $str, $code);
		}

		return $code;
	}



}


/**
 * Sort an array of strings by length from long to short
 * @param array $strings
 */
function string_sort_by_len($strings)
{
	$sort = array_combine($strings, array_map('strlen', $strings));
	arsort($sort);
	return array_keys($sort);
}

