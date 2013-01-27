<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* The MX_Controller class is autoloaded as required */

class Doc_Controller extends CI_Controller {
	
	protected $_data = array();

	function __construct()
	{
		parent::__construct();
		$this->load->conster('const_file');
		$file_trans = $this->conster->item('file');
		$dir_list = $this->getDir(APPPATH.'data');
		$tmp = array();
		foreach ($dir_list as $key => $value) {
			$tmp[$key]['dir']['en'] = $value;
			if(isset($file_trans[$value])){
				$tmp[$key]['dir']['ch'] = $file_trans[$value];
			}else{
				$tmp[$key]['dir']['ch'] = $value;
			}
			$file_list = $this->getFile(APPPATH.'data/'.$value);
			foreach ($file_list as $skey => $svalue) {
				$atmp = explode(APPPATH.'data/'.$value."/", $svalue);
				$atmp = explode(".", $atmp[1]);
				$file_list[$skey] = array();
				$file_list[$skey]['en'] = $svalue;
				if(isset($file_trans[$atmp[0]])){
					$file_list[$skey]['ch'] = $file_trans[$atmp[0]];
				}else{
					$file_list[$skey]['ch']	= $atmp[0];				
				}
			}
			$tmp[$key]['child'] = $file_list;
		}
		$this->_data['file_list'] = $tmp;
	}

	
	/**
	 * 循环文件夹
	 */
	private function file_list($dir,$pattern=""){
		$arr=array();
		$dir_handle=opendir($dir);
		if($dir_handle){
			while(($file=readdir($dir_handle))!==false){
				if($file==='.' || $file==='..'){
					continue;
				}
				$tmp=realpath($dir.'/'.$file);
				if(is_dir($tmp)){
					$retArr=$this->file_list($tmp,$pattern);
					if(!empty($retArr)){
						$arr[]=$retArr;
					}
				}else{
					if($pattern==="" || preg_match($pattern,$tmp)){
						$arr[]=$tmp;
					}
				}
			}
			closedir($dir_handle);
		}
		return $arr;
	}

	//获取文件目录列表
	private function getDir($dir) {
		$dirArray[]=NULL;
		if (false != ($handle = opendir ( $dir ))) {
			$i=0;
			while ( false !== ($file = readdir ( $handle )) ) {
				if ($file != "." && $file != ".."&&!strpos($file,".")) {
					$dirArray[$i]=$file;
					$i++;
				}
			}
			closedir ( $handle );
		}
		return $dirArray;
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


