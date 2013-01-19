<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *	HDFS管理
 */
class Hdfs_biz
{

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
	}

	function read($path)
	{
		$this->ci->load->model('database_model');
		$this->config->load('base_config',TRUE);
		$config = $this->config->item('base_config');
		$sql = 'dfs -ls '.$path;
		$LANG = " export LANG=" . $config['lang_set'] . "; ";
		$JAVA_HOME = " export JAVA_HOME=" . $config['java_home'] . "; ";
		$HADOOP_HOME = " export HADOOP_HOME=" . $config['hadoop_home'] . "; ";
		$HIVE_HOME = " export HIVE_HOME=" . $config['hive_home']. "; ";
		$time = time();
		$filename = $config['results'] . 'dfs.' . $time . '.out';
		$cmd = $LANG . $JAVA_HOME . $HADOOP_HOME . $HIVE_HOME . $config['hive_home'] . '/bin/hive -e "' . $sql .'"';
		$this->ci->database_model->asyncExecuteHql($cmd, $filename, 1, $code);
		try
		{
			$list_arr = file($filename);
			$result = array();
			foreach($list_arr as $k => $line)
			{
				// var_dump($line);
				if(!preg_match("/Found/i", $line))
				{
					$cols = array_merge(array(),array_filter(explode(" ", $line)));
					// var_dump($cols);
					$arr = array();
					$arr['file_property'] = trim($cols[0]);
					$arr['file_user'] = trim($cols[1]);
					$arr['file_group'] = trim($cols[2]);
					$arr['file_size'] = trim($cols[3]);
					$arr['file_time'] = trim($cols[4]) . " " . trim($cols[5]);
					$arr['file_name'] = trim($cols[6]);
					array_push($result,$arr);
				}
			}
			unlink($filename);
			return $result;
		}
		catch (Exception $e)
		{
			echo 'Caught exception: '.  $e->getMessage(). "\n";
		}
	}	

}