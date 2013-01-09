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
	}

	public function index(){
		$this->manage();
	}

	// 单独数据库管理
	public function manage(){
		$database = $this->input->get('database');	
		$this->client->execute('use '.$database);
		$this->client->execute('show tables');
		$this->_data['table_list'] = $this->client->fetchAll();
		$this->_data['database'] = $database;
		$this->transport->close();
		$this->layout->template('/table/manage', $this->_data);
	}

	// 添加表
	public function add(){
		$database 	= $this->input->get('database');
		$external 	= $this->input->post('external');
		$table 		= $this->input->post('table_name');
		$field_name = $this->input->post('field_name');
		$field_type = $this->input->post('field_type');
		$comment 	= $this->input->post('comment');
		$format 	= $this->input->post('format');
		$ext 		= "";
		$table_comment 		= $this->input->post('table_comment');
		$data_terminator 	= $this->input->post('data_terminator');
		$column_terminator 	= $this->input->post('column_terminator');
		if($external == 2){
			$ext = " EXTERNAL ";
		}
		$sql = "CREATE ".$ext." TABLE IF NOT EXISTS `".$database."`.`".$table."` (";
		$arr = array();
		foreach ($field_name as $key => $value) {
			$tmp = "`".$field_name[$key]."` ".$field_type[$key]." COMMENT '".$comment[$key]."' ";
			array_push($arr, $tmp);
		}
		$str = implode(",", $arr);
		$str = substr($str,0,-1);
		$sql 			= $sql.$str.")";
		$tablecomment 	= " COMMENT '".$table_comment."' ";
		$partition 		= '';
		$columnTerminator = stripcslashes($data_terminator);
		$columnTerminator = " ROW FORMAT DELIMITED FIELDS TERMINATED BY \"".$columnTerminator."\" ";
		$lineTerminator = stripcslashes($column_terminator);
		$lineTerminator = " LINES TERMINATED BY \"".$lineTerminator."\" ";
		$stored = '';
		if($format){
			switch ($format){
				case 'text':
					$stored = " STORED AS TEXTFILE ";
					break;
				case 'lzop':
					$stored = " STORED AS INPUTFORMAT \"com.hadoop.mapred.DeprecatedLzoTextInputFormat\" OUTPUTFORMAT \"org.apache.hadoop.hive.ql.io.HiveIgnoreKeyTextOutputFormat\" ";
					break;
				case 'sequence':
					$stored = " STORED AS SEQUENCEFILE ";
					break;
				case 'rcfile':
					$stored = " STORED AS RCFILE ";
					break;
				case 'gzip':
					$stored = " STORED AS TEXTFILE ";
					break;
				case 'bzip2':
					$stored = " STORED AS TEXTFILE ";
					break;
				default:
					$stored = " STORED AS TEXTFILE ";
					break;
			}
		}
		$path = '';
		$as = '';
		$sql = $sql . $tablecomment . $partition .$columnTerminator . $lineTerminator . $stored . $path . $as;
		$this->client->execute('use '.$database);
		$result = $this->client->execute($sql);
		if($result){
			echo "添加失败";
		}else{
			echo "添加成功";
			redirect('/main/table?database='.$database, 'refresh');
		}
		$this->transport->close();
	}

	// 删除表
	public function drop(){
		$database 	= $this->input->get('database');
		$table 		= $this->input->get('table');
		$this->client->execute('use '.$database);
		$sql = "drop table ".$table;
		$this->client->execute($sql);
		$this->transport->close();
		echo "success";
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
		require_once APPPATH."third_party/thrift/classes/class.etc.php";
		$database 	= $this->input->get('database');
		$table 		= $this->input->get('table');
		$this->client->execute('use '.$database);
		$sql = "desc formatted ".$table;
		$etc = new Etc;
		$this->client->execute($sql);
		$result = $this->client->fetchAll();
		// var_dump($result);
		// ==============================================
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
		$this->_data["table_desc"] 	= $table_desc;
		$this->_data["detail"] 		= $detail;
		$this->_data["storage"] 	= $storage;
		$this->layout->template('/table/detail', $this->_data);
	}
}