<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * HDFS文件管理
 * @author Scorpio
 *
*************************************************************/
class Hdfs extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library(array('session','layout'));
		$this->load->helper(array('url'));
		$this->load->business('hdfs_biz');
	}

	public function index(){
		$this->file();
	}

	public function file(){
		$path = $this->input->get('path');
		if(!$path){
			$path = "/";
		}
		$this->_data["result"] = $this->hdfs_biz->read($path);
		$this->layout->template('/hdfs/file', $this->_data);
	}

}