<?php

	class class_b
	{
		// class_b overrides mix
		public $public_mix = true;
		private $private_mix = 128; // try to change private BUT WONT WORK!
		protected $protected_mix = 'Cool!';
		
		// class_b adds new properties
		public $public_b = "New public property from CLASS_B";
		private $private_b = "New private property from CLASS_B";
		protected $protected_b = "New protected property from CLASS_B";
		
		
		// class_b overrides b function
		public function b($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = $var) <br/>\n");
		}
		
		// class_b overrides ab function
		public function ab($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = $var) <br/>\n");
		}
		
		// adding a completely new class
		public function new_b($var)
		{
			echo(__CLASS__." :: ".__FUNCTION__." (var = $var) <br/>\n");
		}
		
		// class_b overrides echo_all, but keep the inherited part from class_a and class_base
		public function echo_all()
		{
			// calling the parent part here
			parent::echo_all();
			
			// continue with new echo
			echo(__CLASS__." :: public_b = ".$this->public_b." <br/>\n");
			echo(__CLASS__." :: private_b = ".$this->private_b." <br/>\n");
			echo(__CLASS__." :: protected_b = ".$this->protected_b." <br/>\n");
		}

		public function call_all($var)
		{
			// calling the parent part here
			parent::call_all($var);
			
			// continue with new call
			$this->new_a($var);
			
			// continue with new call
			$this->new_b($var);
		}
		
	}
	
	