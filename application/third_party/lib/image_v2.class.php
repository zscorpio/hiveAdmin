<?php
/**
* 
* 客户端类库--v2
* @author scorpio
* 
*/
class image{
	var $file;
	var $path;
	const img_url	= "http://res.500mi.com/";
	const secret 	= "500mi";

	function __construct($file,$path,$uploadfile="",$type,$config=array("save_key"=>"date","upload_type"=>"image")){
		$this->file = $file;
		$this->path = $path;
		$this->uploadfile = $uploadfile;
		$this->type = $type;
		$this->save_key = $config["save_key"];
		$this->upload_type = $config["upload_type"];
		include("snoopy.php");
	}

	public function upload(){
		// $options['save_path'] = "comment/feed"; // 存储文件夹名
		// $options['time'] = time(); // 时间戳
		// $options['save_key'] = 'date'; // 文件存储方式(random & date),分别是当前文件夹随机文件名 & 当前文件夹/年/月/日/随机文件名
		// $options['upload_type'] = 'image';
		// $data["type"] = $this->type;
		// $data["filename"] = $this->file;
		// $data["path"] = $this->path;
		$snoopy = new Snoopy; 
		$submit = self::img_url."v2/upload.php"; 
		$file = $this->uploadfile;
		$data["save_path"] = $this->path;
		$data["time"] = time();
		$data["save_key"] = $this->save_key;
		$data["upload_type"] = $this->upload_type;
		$form["policy"] = base64_encode(json_encode($data));
		$form["signature"] = md5($form["policy"].'&'.self::secret);
		$form["type"] = $this->type;
		$snoopy->_submit_type = "multipart/form-data";  
		var_dump($file);
		$snoopy->submit($submit,$form,$file); 
		return $snoopy->results; 
	}
}