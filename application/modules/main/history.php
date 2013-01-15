<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 历史查询
 * @author Scorpio
 *
*************************************************************/
class History extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library(array('session','layout'));
		$this->load->helper(array('url'));
		$config = $this->config->item('base_config');
		$this->log_path = $config['log_path'];
		$this->results = $config['results'];
	}

	public function index(){
		$this->history();
	}

	public function history(){
		$files = $this->getFile($this->log_path);
		$file_list = array();
		foreach ($files as $key => $value) {
			$simple_value = str_replace("application/logs/","",$value);
			$tmp = array();
			$tmp['filename'] = $simple_value;
			$tmp['real_path'] = $value;
			$tmp['content'] = implode(file($value), "");
			$tmp['type'] = filetype($value);
			$tmp['size'] = filesize($value);
			array_push($file_list, $tmp);
		}
		$this->_data['file_list'] = $file_list;
		$this->layout->template('/history/list', $this->_data);
	}

	public function result(){
		$filename = $this->input->get('filename');
		if(!is_numeric(substr($filename,0,1))){
			$str = explode("_",$filename);
			$str = substr($str[1]."_".$str[2],0,-4);
		}else{
			$str = $filename;
		}
		$res_file = $this->results."/hive_res.".$str.".csv";
		$this->load->library('common/csv');
		$csvdata  = $this->csv->read($res_file);
		if (!isset($csvdata['content'])) {
			$csvdata = array('content'=>array());
		}
		$this->_data['csvdata'] = $csvdata;
		$this->_data['filename'] = $res_file;
		$this->layout->template('/history/result', $this->_data);
	}

	public function down(){
		$filename = "/".$this->input->get('filename');
		Header("Location:$filename");
	}

	/**
	 * 获取当前目录及子目录下的所有文件
	 */
	private function getFile($dir) {
		$files = array();

		if(!is_dir($dir)) {
			return $files;
		}

		$handle = opendir($dir);
		if($handle) {
			while(false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..') {
					$filename = $dir . "/"  . $file;
					if(is_file($filename)) {
						$files[] = $filename;
					}else {
						$files = array_merge($files, $this->getFile($filename));
					}
				}
			}
			closedir($handle);
		}
		return $files;
	}

}