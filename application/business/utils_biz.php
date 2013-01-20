<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *	utils
 */
class Utils_biz
{

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
	}

	function makeFingerPrint()
	{
		$mtime = explode(" ",microtime());
		$date = date("Y-m-d-H-i-s",$mtime[1]);
		$mtime = (float)$mtime[1] + (float)$mtime[0];
		$sha1 = $date."_".sha1($mtime);
		return $sha1;
	}

	function makeFilename($finger_print = '')
	{
		$config = $this->config->item('base_config');
		if(!$finger_print){
			$finger_print = $this->makeFingerPrint();
		}
		$filename = array();
		$filename['log'] =  "scorpio_" . $finger_print . ".log";
		$filename['out'] = 'hive_res.' . $finger_print . '.out';
		$filename['csv'] = 'hive_res.' . $finger_print . '.csv';
		$filename['run'] = 'hive_res.' . $finger_print . '.run';
		$filename['log_with_path'] = $config['log_path'] . $filename['log'];
		$filename['out_with_path'] = $config['results'] . $filename['out'];
		$filename['csv_with_path'] = $config['results'] . $filename['csv'];
		$filename['run_with_path'] = $config['results'] . $filename['run'];
		return $filename;
	}

	function exportCsv($finger_print)
	{
		$config = $this->config->item('base_config');
		$filename = $this->makeFilename($finger_print);
		$filename1 = $filename['out_with_path'];
		$filename2 = $filename['csv_with_path'];
		try{
			$fp1 = @fopen($filename1,"r");
			$fp2 = @fopen($filename2,"w");
			while(!feof($fp1))
			{
				$str = str_replace($config['out_seperator'], ",", fgets($fp1));
				fputs($fp2,$str);
			}
			fclose($fp2);
			fclose($fp1);
			unlink($filename1);
		}catch (Exception $e){
			echo 'Caught exception: '.  $e->getMessage(). "\n";
		}
	}

	// 对象转数组
	function object_to_array($obj){
		$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
		$arr = array();
		foreach ($_arr as $key => $val){
			$val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
			$arr[$key] = $val;
		}
		return $arr;
	}

	// 表属性json化
	function json_cols($array){
		$tmp = array();
		foreach ($array as $key => $value) {
			$tmp[$value['name']] = $value['comment'];
		}
		return $tmp;
	}
}