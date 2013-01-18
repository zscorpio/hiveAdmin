<?php
class Database_model extends HIVE_Model{
	function __construct(){
		parent::__construct();	
	}

	// 获取集群状态
	public function getClusterStatus(){
		try{
			return $this->client->getClusterStatus();
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

//	数据库相关==========================================================	

	// 新建数据库
	public function createDb($array){
		try{
			$sql = "CREATE ".$array['type']." IF NOT EXISTS ".$array['db_name']." COMMENT '".$array['db_comment']."'";
			$result = $this->client->execute($sql);
			return $result;
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 删除数据库
	public function delDb($db){
		try{
			$sql = "DROP DATABASE IF EXISTS ".$db;
			$result = $this->client->execute($sql);
			return $result;
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 列出数据库
	public function listDb(){
		try{
			$sql = "SHOW DATABASES";
			$this->client->execute($sql);
			$result = $this->client->fetchAll();
			return $result;
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 获取数据库信息
	public function getDbInfo($db){
		try{
			$this->client->execute('use '.$db);
			$this->client->execute('describe database '.$db);
			$result = $this->client->fetchAll();
			return $result;
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

//	表相关==========================================================

	// 获取表
	public function listTable($db){
		try{
			$this->client->execute('use '.$db);
			$this->client->execute('show tables');
			return $this->client->fetchAll();
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 新建表
	public function createTable($params){
		try{
			if($params["external"] == 2){
				$ext = " EXTERNAL ";
			}
			$sql = "CREATE ".$params["ext"]." TABLE IF NOT EXISTS `".$params["database"]."`.`".$params["table"]."` (";
			$arr = array();
			foreach ($params["field_name"] as $key => $value) {
				$tmp = "`".$params["field_name"][$key]."` ".$params["field_type"][$key]." COMMENT '".$params["comment"][$key]."' ";
				array_push($arr, $tmp);
			}
			$str = implode(",", $arr);
			$str = substr($str,0,-1);
			$sql 			= $sql.$str.")";
			$tablecomment 	= " COMMENT '".$params["table_comment"]."' ";
			$partition 		= '';
			$columnTerminator = stripcslashes($params["data_terminator"]);
			$columnTerminator = " ROW FORMAT DELIMITED FIELDS TERMINATED BY \"".$columnTerminator."\" ";
			$lineTerminator = stripcslashes($params["column_terminator"]);
			$lineTerminator = " LINES TERMINATED BY \"".$lineTerminator."\" ";
			$stored = '';
			if($params["format"]){
				switch ($params["format"]){
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
			$this->client->execute('use '.$params["database"]);
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 删除表
	public function delTable($database,$table){
		try{
			$this->client->execute('use '.$database);
			$sql = "drop table ".$table;
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 获取表信息
	public function getTbaleInfo($database,$table){
		try{
			$this->client->execute('use '.$database);
			$sql = "desc formatted ".$table;
			$this->client->execute($sql);
			$result =  $this->client->fetchAll();
			require_once APPPATH."third_party/thrift/classes/class.etc.php";
			$etc = new Etc;
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
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 复制表
	public function cloneTable($data){
		// if($location == 'hdfs:///user/hive/warehouse/'){
		// 	$location = ' LOCATION "hdfs:///user/hive/warehouse/'.$data['database'].'.db/'.$data['table'].'" ';
		// }else{
		// 	$location = ' LOCATION "'. $location.'"';
		// }
		try{
			$location = ' LOCATION "hdfs:///user/hive/warehouse/'.$data['database'].'.db/'.$data['table'].'" ';
			if($data["external"] == TRUE){
				$sql = "CREATE EXTERNAL TABLE IF NOT EXISTS `".$data['database']."`.`".$data["new_table"]."` LIKE `". $data['database'] ."`.`" . $data["table"] . "` " . $location;
			}else{
				$sql = "CREATE TABLE IF NOT EXISTS `".$data['database']."`.`".$data["new_table"]."` LIKE `". $data['database'] ."`.`" . $data["table"] . "` ";
			}
			// echo $sql;
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}	
	}

	// 加载数据
	public function load($data){
		$local = $data['local'];
		$path = $data['path'];
		$partition = $data['partition'];
		$overwrite = $data['overwrite'];
		$database = $data['database'];
		$table = $data['table'];
		try{
			if($local == "HDFS"){
				$local = " ";
			}else{
				$local = $local;
			}
			if($partition == ""){
				$partition = " ";
			}else{
				$partition = " PARTITION " . $partition;
			}
			if($overwrite == 'nocover'){
				$overwrite = " ";
			}else{
				$overwrite = " OVERWRITE ";
			}
			$sql = "LOAD DATA " . $local . " INPATH '" . $path . "' " .$overwrite . " INTO TABLE " . $table . $partition;
			$this->client->execute('USE '.$database);
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 修改表名字
	public function renameTbale($database,$table,$new_table){
		try{
			$sql = "ALTER TABLE ".$table." RENAME TO ".trim($new_table);
			$this->client->execute("USE ".$database);
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 修改表类型
	public function changeExternal($database,$table,$external){
		try{
			if($external == "EXTERNAL_TABLE"){
				$external = "TRUE";
			}else{
				$external = "FALSE";
			}
			$sql = 'ALTER TABLE '.$table.' SET TBLPROPERTIES ("EXTERNAL" = "'.$external.'")';
			$this->client->execute("USE ".$database);
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 删除字段
	public function removeColumn($database,$table,$column){
		try{
			$info = $this->getTbaleInfo($database, $table);
			$table_desc = $info['table_desc'];
			$tmp = array();
			foreach ($table_desc as $key => $value) {
				if($value['name'] == $column){
					$value['name'] = '';
					$value['type'] = '';
					$value['comment'] = '';
				}else{
					$str = " `" . $value['name'] . "` " . $value['type'] . " COMMENT '" . $value['comment'] . "'";
					array_push($tmp, $str);
				}
			}
			$res = " ( ". implode(",", $tmp) . " ) ";
			$sql = "ALTER TABLE ".$table." REPLACE COLUMNS " . $res;
			$this->client->execute("USE ".$database);
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 修改字段
	public function changeColumn($database,$table,$data){
		try{
			$sql = "ALTER TABLE ". $table ." CHANGE `".$data['bf_column']."` `".$data['column']."` ".$data['type']." COMMENT '".$data['comment']."'" ;
			$this->client->execute("USE ".$database);
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 增加字段
	public function addColumn($database,$table,$data){
		try{
			$cols = "`".$data['column']."` ".$data['type']." COMMENT '".$data['comment']."'";
			$sql = "ALTER TABLE ".$table." ADD COLUMNS (" . $cols.")";
			$this->client->execute("USE ".$database);
			return $this->client->execute($sql);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}
	
	// cli查询
	public function cliQuery($sql, $finger_print){
		try{
			$this->load->business('utils_biz');
			$this->config->load('base_config',TRUE);
			$config = $this->config->item('base_config');
			$LANG = " export LANG=" . $config['lang_set'] . "; ";
			$JAVA_HOME = " export JAVA_HOME=" . $config['java_home'] . "; ";
			$HADOOP_HOME = " export HADOOP_HOME=" . $config['hadoop_home'] . "; ";
			$HIVE_HOME = " export HIVE_HOME=" . $config['hive_home']. "; ";

			$filename = $this->utils_biz->makeFilename($finger_print);
			$log_file = $filename['log_with_path'];
			$out_file = $filename['out_with_path'];
			$run_file = $filename['run_with_path'];

			$this->load->helper('file');
			write_file($log_file, $sql);
			// $this->load->model('history_model', 'history');
			// $this->history->create_history($this->session->userdata('username'), $finger_print);
			echo $run_file;
			$cmd = $LANG . $JAVA_HOME . $HADOOP_HOME . $HIVE_HOME . $config['hive_home'] . "/bin/hive -f " . $log_file . " > " . $out_file;
			// var_dump($cmd);
			$this->asyncExecuteHql($cmd, $run_file, 2, $code);
			$this->utils_biz->exportCsv($finger_print);
			sleep(1);
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}

	// 同步执行hql
	public function asyncExecuteHql($command, $file_name, $type, &$code){
		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w") // stderr is a file to write to
		);
		$pipes= array();
		$process = proc_open($command, $descriptorspec, $pipes);
		$output= "";
		if (!is_resource($process)){
			return false;
		}
		#close child's input immediately
		fclose($pipes[0]);
		stream_set_blocking($pipes[1],0);
		stream_set_blocking($pipes[2],0);
		$todo= array($pipes[1],$pipes[2]);
		try{
			$fp = fopen($file_name, "w");
			while( true ){
				$read= array(); 
				if( !feof($pipes[$type]) )
					$read[]= $pipes[$type];// get system stderr on real time
				if (!$read){
					break;
				}
				$ready = stream_select($read, $write=NULL, $ex= NULL, 2);
				if ($ready === false){
					break; #should never happen - something died
				}
				foreach ($read as $r){
					$s= fread($r,128);
					$output .= $s;
					fwrite($fp,$s);
				}
			}
			fclose($fp);
		}catch (Exception $e){
			echo 'Caught exception: '.  $e->getMessage(). "\n";
		}
		fclose($pipes[1]);
		fclose($pipes[2]);
		$code= proc_close($process);
		return $output;
	}

	// 查询状态
	public function queryStatus($finger_print){
		$this->load->business('utils_biz');
		$filename = $this->utils_biz->makeFilename($finger_print);
		$run_file = $filename['run_with_path'];
		try{
			$array = file($run_file);
			if(is_array($array)){
				$array = array_reverse($array);
				$text = "";
				foreach($array as $k => $v)
				{
					$text .= trim($v)."<br>";
				}
				$str = $array[0];
				$start_map = strpos($str, "map = ")+6;
				$end_map = strpos($str, "%");
				$len_map = $end_map - $start_map;
				$start_reduce = strpos($str, "reduce = ")+9;
				$end_reduce = strrpos($str, "%");
				$len_reduce = $end_reduce - $start_reduce;
				$map_per = substr($str, $start_map, $len_map);
				$reduce_per = substr($str, $start_reduce, $len_reduce);
				if(!is_numeric($map_per) || !is_numeric($reduce_per)){
					$map_per = 0;
					$reduce_per = 0;
				}
				$json = '{"map_percent":"'.$map_per.'","reduce_percent":"'.$reduce_per.'","text":"'.$text.'"}';
				return $json;
			}else{
				die('Do not re-submit!!!');
			}
		}catch (Exception $e){
			echo 'Caught exception: '.  $e->getMessage(). "\n";
		}
	}

	// 查询
	public function hiveQuery($sql){
		try{
			$this->client->execute($sql);
			return $this->client->fetchAll();
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}
}	
?>