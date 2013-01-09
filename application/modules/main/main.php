<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 首页
 * @author Scorpio
 *
*************************************************************/
class Main extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library(array('session','layout'));
		$this->load->helper(array('url'));
	}

	public function index(){
		$this->manage();
	}

	// 数据库管理
	public function manage(){
		$this->layout->template('manage', $this->_data);
	}

	// 添加数据库
	public function add(){
		$sql = "CREATE DATABASE IF NOT EXISTS ".$this->input->post('database')." COMMENT '".$this->input->post('comment')."'";
		$result = $this->client->execute($sql);
		$this->transport->close();
		if($result){
			echo "添加失败";
		}else{
			echo "添加成功";
			redirect('/main', 'refresh');
		}
	}
}