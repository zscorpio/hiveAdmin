<?php
/**
* 
* 文件操作类
* @author scorpio
* 
*/
class file{
	var $file;
	var $path;
	var $pathfile;
	var $tmp;
	const tmp_path = "/tmp/";

	function __construct($file,$path,$pathfile=""){
		$this->file = $file;
		$this->path = $_SERVER["DOCUMENT_ROOT"].$path;
		$this->pathfile = $pathfile;
		$this->tmp_path = $_SERVER["DOCUMENT_ROOT"].self::tmp_path;
		$this->tmp = $path;
		if(!$this->pathfile){
			$this->pathfile = $this->file;
		}
	}

	// 创建文件夹
	function dir($path){
		if (!file_exists($path)){ 
			$this->dir(dirname($path));
			mkdir($path, 0777);
		}
	}

	// 列出文件夹
	function list_dir($dir,$pattern=""){
		$arr=array();  
		$dir_handle=opendir($dir);  
		if($dir_handle){   
			while(($file=readdir($dir_handle))!==false){
				if($file==='.' || $file==='..'){  
					continue;  
				}  
				$tmp=realpath($dir.'/'.$file);  
				if(is_dir($tmp)){  
					$retArr=$this->list_dir($tmp,$pattern);  
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

	// 创建文件夹
	function mkdir(){
		$this->dir($this->path);
	}

	// 显示列表
	function showlist(){
		return $this->list_dir($this->path,"");
	}

	// 文件夹移动
	function move(){
		$this->dir($this->path);
		return rename($this->tmp_path.$this->file, $this->path.$this->pathfile);
	}

	// 删除文件
	function delete(){
		return unlink($this->path.$this->file);
	}
}