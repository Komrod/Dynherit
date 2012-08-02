<?php

	class class_a
	{
		// class_a overrides strings
		public $public_string = 'This string is public from CLASS_A!';
		private $private_string = 'This string is private from CLASS_A!';
		protected $protected_string = 'This string is protected from CLASS_A!';
		
		// class_a adds new properties
		public $public_a = "New public property from CLASS_A";
		private $private_a = "New private property from CLASS_A";
		protected $protected_a = "New protected property from CLASS_A";
		
		
		// class_a overrides completely this public function
		public function public_function($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
		
		// class_a tries to override the private function, but WON'T
		// child class can't override private functions
		private function private_function($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
			
		// class_a overrides completely this protected function
		protected function protected_function($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = ".$var.") <br/>\n");
		}
		
		// class_a overrides a function
		public function a($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = $var) <br/>\n");
		}
		
		// class_a overrides ab function
		public function ab($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = $var) <br/>\n");
		}
		
		// create new function
		public function new_a($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = $var) <br/>\n");
		}
		
		// class_a overrides echo_all, but keep the inherited part
		public function echo_all()
		{
			// calling the parent part here
			parent::echo_all();
			
			// continue with new echo
			echo(__CLASS__." :: public_a = ".$this->public_a." <br/>\n");
			echo(__CLASS__." :: private_a = ".$this->private_a." <br/>\n");
			echo(__CLASS__." :: protected_a = ".$this->protected_a." <br/>\n");
		}
		
		public function call_all($var)
		{
			// calling the parent part here
			parent::call_all($var);
			
			// continue with new call
			$this->new_a($var);
		}
	}
	
	