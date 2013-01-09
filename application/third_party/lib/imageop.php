<?php
/**
* 
* 客户端类库
* @author scorpio
* 
*/
class image{
	var $file;
	var $path;
	const img_url = "http://res.500mi.com/";

	function __construct($file,$path,$uploadfile="",$type=""){
		$this->file = $file;
		$this->path = $path;
		$this->uploadfile = $uploadfile;
		$this->type = $type;
		include("snoopy.php");
	}

	function move(){
		$snoopy = new Snoopy; 
		$submit = self::img_url."move.php"; 
		$data["filename"] = $this->file; 
		$data["path"] = $this->path; 
		$snoopy->submit($submit,$data); 
		return $snoopy->results; 
	}

	function upload(){
		$snoopy = new Snoopy; 
		$submit = self::img_url."post.php"; 
		$file = $this->uploadfile;
		$data["type"] = $this->type;
		$data["filename"] = $this->file;
		$data["path"] = $this->path;
		$snoopy->_submit_type = "multipart/form-data";  
		$snoopy->submit($submit,$data,$file); 
		return $snoopy->results; 
	}
}