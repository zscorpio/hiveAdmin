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
		$this->load->business('database_biz');
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
		$array = array(
			'type'			=>	$this->input->post('type'),
			'db_name'		=>	$this->input->post('database'),
			'db_comment'	=>	$this->input->post('comment'),
		);
		$result = $this->database_biz->createDb($array);
		if($result){
			echo "添加失败";
		}else{
			echo "添加成功";
			redirect('/main', 'refresh');
		}
	}

	// 删除数据库
	public function del(){
		$database = $this->input->get('database');
		$result = $this->database_biz->delDb($database);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}
}