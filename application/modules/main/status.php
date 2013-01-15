<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 集群查询
 * @author Scorpio
 *
*************************************************************/
class Status extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library(array('session','layout'));
		$this->load->helper(array('url'));
		$this->load->business('database_biz');
	}

	public function index(){
		$this->status();
	}

	public function status(){
		$this->_data["status"] = (array)$this->database_biz->getClusterStatus();
		$this->layout->template('/status', $this->_data);
	}

}