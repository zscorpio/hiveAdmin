<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 导出数据文件
 *
*************************************************************/
class Export
{
	/*导出数据文件*/
	function export_csv($data, $status) {
		$status = $status ? '-'.$status : '';
		$filename = date('Y-m-d').$status.".csv";
		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=".$filename);
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');
		echo $this->array_to_string($data);
	}

	/**
	 * 传递变量导出文件
	 */
	function export_csv_by_params($title,$data,$file){
		$file = $file ? '-'.$file : '';
		$filename = date('Y-m-d').$file.".csv";
		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=".$filename);
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');
		echo $this->format($title,$data);
	}

	/**
	 * 格式化双重数据
	 */
	function format($title,$params){
		if ( ! $title) {
			$title = array_keys($params[0]);
		}
		if(empty($params)){
			return i("没有符合您要求的数据！");
		}
		if(count($title)!=count($params[0])){
			return i("类目和数据对不上");
		}
		$title = implode(",",$title);
		$data = i($title)."\n";
		$size = count($params);
		for($i = 0 ; $i < $size ;  $i++) {
			// var_dump($i);
			$data .= i(implode(",", $params[$i]))."\n";
		}
		// exit();
		return $data;      
	}

	/*格式化表格*/
	function array_to_string($result) {
		if(empty($result)) {
			return i("没有符合您要求的数据！");
		}
		$data = i('快递单号, 快递公司, 收件人, 收件人手机, 收件地址, 登记时间')."\n";
		$size_result = sizeof($result);
		for($i = 0 ; $i < $size_result ;  $i++) {
			$data .= '="'.i($result[$i]['out_id']).'",'
					.i($result[$i]['express']).','
					.i($result[$i]['owner_name']).','
					.'="'.i($result[$i]['owner_mobile']).'",'
					.i($result[$i]['address']).','
					.'="'.i($result[$i]['sign_time'])."\"\n";
		}
		return $data;
	}

	function i($strInput) {
		//转换一下编码 
		return iconv('utf-8','gb2312//IGNORE',$strInput);//页面编码为utf-8时使用，否则导出的中文为乱码
		//在输出的字符编码字符串后面加上"//IGNORE"，如iconv('UTF-8', 'GB2312//IGNORE', '囧')，这样做其实是忽略了不能转换的字符，避免了出错但却不能够正确地输出(即空白不、输出)。
	}
}
