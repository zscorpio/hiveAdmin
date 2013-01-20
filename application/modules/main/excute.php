<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 数据库查询
 * @author Scorpio
 *
*************************************************************/
class Excute extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('url'));
		$this->load->business(array('database_biz','utils_biz'));
		$this->load->library('layout','default/layout_excute');
	}

	public function index(){
		$this->query();
	}

	public function query(){
		$database = $this->input->get('database');
		$tables = $this->database_biz->listTable($database);
		$table_detail = array();
		foreach ($tables as $key => $value) {
			$tmp = $this->utils_biz->object_to_array($this->database_biz->getTableInfo($database,$value));
			if(isset($tmp['parameters']['comment'])){
				$table_detail[$key]['cn_key'] = $tmp['parameters']['comment'];
			}else{
				$table_detail[$key]['cn_key'] = '木有注释';
			}
			$table_detail[$key]['detail'] = json_encode($this->utils_biz->json_cols($tmp['sd']['cols']));
		}

		$this->_data['database'] 		= $database;
		$this->_data['tables'] 			= $tables;
		$this->_data['table_detail'] 	= $table_detail;
		$this->layout->template('excute', $this->_data);
	}

}