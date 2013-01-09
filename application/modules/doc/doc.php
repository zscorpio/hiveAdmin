<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 文档资料
 * @author Scorpio
 *
*************************************************************/
class Doc extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library(array('session','layout'));
	}

	public function index(){
		$this->install();
	}

	public function install(){
		$filename = "README.md";  
		$this->_data['content'] = implode(file($filename), ""); 
		$this->layout->template('/doc/install', $this->_data);
	}

	public function manage(){
		$filename = "data/MANAGE.md";  
		$this->_data['content'] = implode(file($filename), ""); 
		$this->layout->template('/doc/manage', $this->_data);
	}

	public function error(){
		$filename = "data/ERROR.md";  
		$this->_data['content'] = implode(file($filename), ""); 
		$this->layout->template('/doc/error', $this->_data);
	}
}