<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 数据库管理
 * @author Scorpio
 *
*************************************************************/
class Table extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library(array('session','layout'));
		$this->load->helper('url');
		$this->load->business('database_biz');
		$this->load->business('tab_biz');
	}

	public function index(){
		$this->manage();
	}

	// 单独数据库管理
	public function manage(){
		$database = $this->input->get('database');	
		$result = $this->database_biz->listTable($database);
		$this->_data["info"] = $this->tab_biz->tabExplode($this->database_biz->getDbInfo($database));
		$this->_data['table_list'] = $result;
		$this->_data['database'] = $database;
		$this->layout->template('/table/manage', $this->_data);
	}

	// 添加表
	public function add(){
		$params							= array();
		$params["database"] 			= $this->input->get('database');
		$params["external"]  			= $this->input->post('external');
		$params["table"]  				= $this->input->post('table_name');
		$params["field_name"]  			= $this->input->post('field_name');
		$params["field_type"]  			= $this->input->post('field_type');
		$params["comment"]  			= $this->input->post('comment');
		$params["format"]  				= $this->input->post('format');
		$params["ext"]  				= "";
		$params["table_comment"]  		= $this->input->post('table_comment');
		$params["data_terminator"]  	= $this->input->post('data_terminator');
		$params["column_terminator"]  	= $this->input->post('column_terminator');	
		$result = $this->database_biz->createTable($params);
		if($result){
			echo "添加失败";
		}else{
			echo "添加成功";
			redirect('/main/table?database='.$params["database"], 'refresh');
		}
	}

	// 删除表
	public function drop(){
		$database 	= $this->input->get('database');
		$table 		= $this->input->get('table');
		$result = $this->database_biz->delTable($database,$table);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}

	// 复制表
	public function copy(){
		$database 	= $this->input->get('database');
		$table 		= $this->input->get('table');
		$this->client->execute('use '.$database);
		$sql = "drop table ".$table;
		$this->client->execute($sql);
		$this->transport->close();
		echo "success";
	}

	// 详细表
	public function detail(){
		$database 	= $this->input->get('database');
		$table 		= $this->input->get('table');
		$result = $this->database_biz->getTbaleInfo($database,$table);
		$this->_data["table_desc"] 	= $result["table_desc"];
		$this->_data["detail"] 		= $result["detail"];
		$this->_data["storage"] 	= $result["storage"];
		$this->layout->template('/table/detail', $this->_data);
	}
}