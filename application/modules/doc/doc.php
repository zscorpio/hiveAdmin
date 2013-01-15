<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 文档资料
 * @author Scorpio
 *
*************************************************************/
class Doc extends Doc_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('layout','default/layout_doc');
	}

	public function index(){
		$this->all();
	}

	public function all(){
		$this->layout->template('/doc/all', $this->_data);
	}

	public function fetch(){
		$filename = $this->input->get('path');  
		$content = implode(file($filename), ""); 
		echo $content;
	}

	public function install(){
		$filename = 'README.md';  
		$this->_data['content'] = implode(file($filename), ""); 
		$this->layout->template('/doc/install', $this->_data);
	}

	public function manage(){
		$filename = APPPATH."data/doc/MANAGE.md";  
		$this->_data['content'] = implode(file($filename), ""); 
		$this->layout->template('/doc/manage', $this->_data);
	}

	public function error(){
		$filename = APPPATH."data/doc/ERROR.md";  
		$this->_data['content'] = implode(file($filename), ""); 
		$this->layout->template('/doc/error', $this->_data);
	}

}