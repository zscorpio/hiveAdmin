<?php	if (!defined('BASEPATH')) exit('No direct script access allowed');

class Views{ 
	function __construct()
	{
		log_message('debug', "Views Class Initialized");
	}
	
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
	
	function render($name,$vars = array(), $return = FALSE)
	{

	}
	
}


?>
