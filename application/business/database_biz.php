<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *	数据库基本操作
 */
class Database_biz
{

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
		$this->ci->load->model('database_model');
	}
	
	function getClusterStatus()
	{
		return $this->ci->database_model->getClusterStatus();
	}

	function createDb($array)
	{
		return $this->ci->database_model->createDb($array);
	}

	function delDb($array)
	{
		return $this->ci->database_model->delDb($array);
	}

	function listDb()
	{
		return $this->ci->database_model->listDb();
	}

	function getDbInfo($db)
	{
		return $this->ci->database_model->getDbInfo($db);
	}	

	function listTable($db)
	{
		return $this->ci->database_model->listTable($db);
	}

	function createTable($params)
	{
		return $this->ci->database_model->createTable($params);
	}	

	function delTable($database,$table)
	{
		return $this->ci->database_model->delTable($database,$table);
	}

	function getTbaleInfo($database,$table)
	{
		require_once APPPATH."third_party/thrift/classes/class.etc.php";
		$etc = new Etc;
		$result = $this->ci->database_model->getTbaleInfo($database,$table);
		$table_desc_tmp = $etc->GetTableDetail($result, "1");
		$table_desc = array();
		foreach ($table_desc_tmp as $key => $value) {
			$array = array();
			$tmp = explode('	',$value);
			$array['name']  	= trim($tmp[0]);
			$array['type']  	= trim($tmp[1]);
			if(isset($tmp[2])){
				$array['comment']  	= trim($tmp[2]);
			}else{
				$array['comment']  	= '';
			}
			array_push($table_desc, $array);
		}
		// ==============================================
		$detail_tmp = $etc->GetTableDetail($result, "2");
		$detail = array();
		foreach ($detail_tmp as $key => $value) {
			$array = array();
			$tmp = explode('	',$value);
			$array['key']  	= trim($tmp[0]);
			if(isset($tmp[1])){
				$array['value'] = trim($tmp[1]);
			}else{
				$array['value'] = '';
			}
			array_push($detail, $array);
		}
		// ==============================================
		$storage_tmp = $etc->GetTableDetail($result, "3");
		$storage = array();
		foreach ($storage_tmp as $key => $value) {
			$array = array();
			$tmp = explode('	',$value);
			$array['key']  	= trim($tmp[0]);
			if(isset($tmp[1])){
				$array['value'] = trim($tmp[1]);
			}else{
				$array['value'] = '';
			}
			array_push($storage, $array);
		}
		// ==============================================
		$tmp = array();
		$tmp["table_desc"] 	= $table_desc;
		$tmp["detail"] 		= $detail;
		$tmp["storage"] 	= $storage;
		return $tmp;
	}	

}