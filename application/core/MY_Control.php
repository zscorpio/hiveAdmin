<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."libraries/Views.php";
class MY_Control extends Views{ 

	protected $_file;
	function __construct()
	{
		parent::__construct();
	}
	
	function set_file($file){
		$this->_file = $file;
	}
	
	function render($vars=array()){
		return $this->load->template($this->_file,$vars,true);
	}
	
}
?>
