<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 任务队列
 * @author Scorpio
 *
*************************************************************/
class Taskqueue extends Doc_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('url'));
		$this->load->library('layout','default/layout_doc');
	}

	public function index(){
		$this->queue();
	}

	public function queue(){
		$this->layout->template('queue/index', $this->_data);
	}

}