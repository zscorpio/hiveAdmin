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
		$this->load->business('utils_biz');
	}

	public function index(){
		$this->manage();
	}

	// 单独数据库管理
	public function manage(){
		$database = $this->input->get('database');	
		$result = $this->database_biz->listTable($database);
		$table_detail = array();
		foreach ($result as $key => $value) {
			$detail = $this->database_biz->getTableInfo($database,$value);
			$table_detail[$key]['type'] = $detail->tableType;
		}
		$this->_data["info"] = $this->utils_biz->object_to_array($this->database_biz->getDbInfo($database));
		$this->_data['table_list'] = $result;
		$this->_data['table_detail'] = $table_detail;
		$this->_data['database'] = $database;
		$this->layout->template('/table/manage', $this->_data);
	}

	// 添加表
	public function add(){
		$params							= array();
		$params["database"] 			= $this->input->get('database');
		$params["external"]  			= $this->input->post('external');
		$params["table"]  				= $this->input->post('new_table');
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
		$data["database"] = $this->input->post('database');
		$data["table"] = $this->input->post('table');
		$data["new_table"] = $this->input->post('new_table');
		$external = $this->input->post('external');
		if($external == "EXTERNAL_TABLE"){
			$data["external"] = TRUE;
		}else{
			$data["external"] = FALSE;
		}
		$result = $this->database_biz->cloneTable($data);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}

	// 详细表
	public function detail(){
		$database 	= $this->input->get('database');
		$table 		= $this->input->get('table');
		$result 	= $this->utils_biz->object_to_array($this->database_biz->getTableInfo($database,$table));
		$sd 		= $result['sd'];
		unset($result['sd']);
		$this->_data["result"] 	= $result;
		$this->_data["sd"] 		= $sd;
		$this->layout->template('/table/detail', $this->_data);
	}

	// 加载数据
	public function load(){
		$data = array();
		$data["database"] 	= $this->input->post('database');
		$data["table"] 		= $this->input->post('table');
		$data["local"] 		= $this->input->post('local');
		$data["path"] 		= $this->input->post('path');
		$data["partition"] 	= $this->input->post('partition');
		$data["overwrite"] 	= $this->input->post('overwrite');
		// var_dump($data);
		$result = $this->database_biz->load($data);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}

	// 数据查询
	public function query(){
		$this->_data["database"] 	= $this->input->get('database');
		$this->_data["table"] 		= $this->input->get('table');
		$this->layout->template('/table/query', $this->_data);
	}

	// 修改表
	public function alert(){
		if($this->input->post('submit')){
			$database 	= $this->input->post('database');
			$table		= $this->input->post('table');
			$new_table 	= $this->input->post('new_table');
			$external 	= $this->input->post('external');
			$field 		= json_decode($this->input->post('field'));
			$response = array();
			if($this->_renameTable($database,$table,$new_table)){
				$rename = "fail";
			}else{
				$rename = "success";
			}
			if($this->_changeExternal($database,$table,$external)){
				$change = "fail";
			}else{
				$change = "success";
			}
			$response['rename'] = $rename;
			$response['change'] = $change;
			echo json_encode($response);
		}else{		
			$database 	= $this->input->get('database');
			$table		= $this->input->get('table');
			$result		= $this->utils_biz->object_to_array($this->database_biz->getTableInfo($database,$table));
			$sd 		= $result['sd'];
			unset($result['sd']);
			$external = $result["tableType"];
			$this->_data["sd"] 		= $sd;
			$this->_data["database"] 	= $database;
			$this->_data["table"] 		= $table;
			$this->_data["result"] 		= $result;
			$this->_data["external"] 	= $external;
			$this->layout->template('/table/alert', $this->_data);
		}
	}

	// 移除字段
	public function removeColumn(){
		$database 	= $this->input->post('database');
		$table		= $this->input->post('table');
		$column		= $this->input->post('field_name');
		$result =  $this->database_biz->removeColumn($database,$table,$column);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}

	// 修改字段
	public function changeColumn(){
		$database 	= $this->input->post('database');
		$table		= $this->input->post('table');
		$bf_column	= $this->input->post('bf_field_name');
		$column		= $this->input->post('field_name');
		$type		= $this->input->post('cols_type');
		$comment	= $this->input->post('comment');
		$data 		= array(
			'bf_column'	=>	$bf_column,
			'column'	=>	$column,
			'type'		=>	$type,
			'comment'	=>	$comment
		);
		$result =  $this->database_biz->changeColumn($database,$table,$data);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}

	// 增加字段
	public function addColumn(){
		$database 	= $this->input->post('database');
		$table		= $this->input->post('table');
		$column		= $this->input->post('field_name');
		$type		= $this->input->post('cols_type');
		$comment	= $this->input->post('comment');
		$data 		= array(
			'column'	=>	$column,
			'type'		=>	$type,
			'comment'	=>	$comment
		);
		$result =  $this->database_biz->addColumn($database,$table,$data);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}

	// 修改表名
	private function _renameTable($database,$table,$new_table){
		return $this->database_biz->renameTbale($database,$table,$new_table);
	}

	// 修改表类型
	private function _changeExternal($database,$table,$external){
		return $this->database_biz->changeExternal($database,$table,$external);
	}

	// hive query
	public function hiveQuery(){
		$result =  $this->database_biz->hiveQuery($this->input->post('sql'));
		var_dump($result);
	}

	// 查询相关
	public function excuteQuery(){
		$database 	= $this->input->post('database');
		$table		= $this->input->post('table');
		$column		= $this->input->post('field_name');
		$type		= $this->input->post('cols_type');
		$comment	= $this->input->post('comment');
		$data 		= array(
			'column'	=>	$column,
			'type'		=>	$type,
			'comment'	=>	$comment
		);
		$result =  $this->database_biz->addColumn($database,$table,$data);
		if($result){
			echo "fail";
		}else{
			echo "success";
		}
	}

	// getFingerPrint
	public function getFingerPrint(){
		$this->load->business('utils_biz');
		echo $this->utils_biz->makeFingerPrint();
	}

	// 开始查询
	public function sqlQuery(){
		$sql = $this->input->post('sql');
		$database = $this->input->post('database');
		$sql = "USE ".$database. ";".$sql;
		$finger_print = $this->input->post('finger_print');
		$this->database_biz->cliQuery($sql, $finger_print);
	}

	// 查询状态
	public function queryStatus($finger_print){
		$str = $this->database_biz->queryStatus($finger_print);
		echo $str;
	}

}