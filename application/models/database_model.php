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
			return $this->client->fetchAll();
		}catch (Exception $e){
			return 'Caught exception: '. $e->getMessage()."\n";
		}
	}
}	
?>