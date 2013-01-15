<?php
class Csv
{	
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
	}
	public function read($fileName)
	{
		setlocale(LC_ALL, 'zh_CN'); 
		$dataarr = array();
		$title = array();
		$handle = fopen($fileName,"r");
		$m = 1;
		while($datas=fgetcsv($handle,10000,","))
		{
			if($m==1)
			{
				for($i=0;$i<count($datas);$i++)
				{
					$title[$i] = trim(iconv("GBK", "utf-8//IGNORE", $datas[$i]));
				}			
			}else{
				for($i=0;$i<count($datas);$i++)
				{
					$dataarr['content'][$m-2][$i] = trim(@iconv("GBK", "utf-8//IGNORE", $datas[$i]));
				}
			}
			$m += 1;
		}
		$dataarr['title'] = $title;		
		fclose($handle);
		return $dataarr;
	}
   
	public function readrow($fileName)
	{
		$dataarr = array();
		$handle = fopen($fileName,"r");
		while($datas=fgetcsv($handle,1,","))
		{
			var_dump($datas);
			for($i=0;$i<count($datas);$i++)
			{
				$dataarr[$i]['title'] = $datas[$i];
			}
		}
		fclose($handle);
		return $dataarr;
	}
}




?>