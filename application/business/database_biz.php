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

	function getTableInfo($database,$table)
	{
		return $this->ci->database_model->getTableInfo($database,$table);
	}	
	
	function parsedInfo($database,$table)
	{
		return $this->ci->database_model->parsedInfo($database,$table);
	}	

	function cloneTable($data)
	{
		return $this->ci->database_model->cloneTable($data);
	}

	function load($data)
	{
		return $this->ci->database_model->load($data);
	}

	function renameTbale($database,$table,$new_table)
	{
		return $this->ci->database_model->renameTbale($database,$table,$new_table);
	}

	function changeExternal($database,$table,$external)
	{
		return $this->ci->database_model->changeExternal($database,$table,$external);
	}

	function removeColumn($database,$table,$column)
	{
		return $this->ci->database_model->removeColumn($database,$table,$column);
	}

	function changeColumn($database,$table,$data)
	{
		return $this->ci->database_model->changeColumn($database,$table,$data);
	}

	function addColumn($database,$table,$data)
	{
		return $this->ci->database_model->addColumn($database,$table,$data);
	}

	function hiveQuery($sql)
	{
		return $this->ci->database_model->hiveQuery($sql);
	}

	function cliQuery($sql, $finger_print)
	{
		return $this->ci->database_model->cliQuery($sql, $finger_print);
	}
	
	function queryStatus($finger_print)
	{
		return $this->ci->database_model->queryStatus($finger_print);
	}

}