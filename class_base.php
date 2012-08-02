<?php

	class class_base
	{
		
		public $public_string = 'This string is public!';
		private $private_string = 'This string is private!';
		protected $protected_string = 'This string is protected!';
		
		public $public_int = 1;
		private $private_int = 2;
		protected $protected_int = 3;
		
		public $public_bool = false;
		private $private_bool = false;
		protected $protected_bool = false;
		
		public $public_mix;
		private $private_mix;
		protected $protected_mix;
		
		
		public function heredoc_test()
		{
			echo <<<HEREDOC
Heredoc is not messing with the block start : { {
HEREDOC;
		}
		
		// Only with PHP > 5.3
		public function nowdoc_test()
		{
/*			echo <<<'NOWDOC'
Nowdoc is not messing with the block start : { {
NOWDOC;
*/			
		}
		
		public function public_function($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
		
		private function private_function($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
			
		protected function protected_function($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
		
		public function a($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
		
		public function b($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
			
		public function ab($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
		
		public function echo_all()
		{
			echo(__CLASS__." :: echo_all <br/>\n");
			echo(__CLASS__." :: public_string = ".$this->public_string." <br/>\n");
			echo(__CLASS__." :: private_string = ".$this->private_string." <br/>\n");
			echo(__CLASS__." :: protected_string = ".$this->protected_string." <br/>\n");
			echo(__CLASS__." :: public_int = ".$this->public_int." <br/>\n");
			echo(__CLASS__." :: private_int = ".$this->private_int." <br/>\n");
			echo(__CLASS__." :: protected_int = ".$this->protected_int." <br/>\n");
			echo(__CLASS__." :: public_bool = ".var_export($this->public_bool, true)." <br/>\n");
			echo(__CLASS__." :: private_bool = ".var_export($this->private_bool, true)." <br/>\n");
			echo(__CLASS__." :: protected_bool = ".var_export($this->protected_bool, true)." <br/>\n");
			echo(__CLASS__." :: public_mix = ".var_export($this->public_mix, true)." <br/>\n");
			echo(__CLASS__." :: private_mix = ".var_export($this->private_mix, true)." <br/>\n");
			echo(__CLASS__." :: protected_mix = ".var_export($this->protected_mix, true)." <br/>\n");
		}
		
		public function call_all($var)
		{
			$this->public_function($var);
			$this->private_function($var);
			$this->protected_function($var);
			$this->a($var);
			$this->b($var);
			$this->ab($var);
		}
	}
	
	