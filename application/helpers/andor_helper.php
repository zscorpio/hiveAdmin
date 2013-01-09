<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 列出所有城市key=>value形式
 * Enter description here ...
 */
function getallcity()
{	
	$CI =& get_instance();
	$CI->config->load('const_base',TRUE);
	return $CI->config->item('city_list','const_base');
}

/**
 * 根据值遍历分析出城市列表
 * Enter description here ...
 * @param $city_value
 */
function getcitybyvalue($city_value)
{	
	$arr = array();
	$CI =& get_instance();
	$CI->config->load('const_base',TRUE);
	$citylist = $CI->config->item('city_list','const_base');
	$i=0;
	foreach ($citylist as $key=>$value){
		$tmp = $city_value|$key;
		if($city_value==$tmp){
			$arr[$i] = $value;
			$i++;
		}
	}
	return $arr;
}


/**
 * 获取所有属性列表key=>value
 * Enter description here ...
 */
function getallattr()
{
	$CI =& get_instance();
	$CI->config->load('const_base',TRUE);
	return $CI->config->item('attr_list','const_base');
}

/**
 * 根据值遍历分析出属性列表
 * Enter description here ...
 * @param $city_value
 */
function getattrbyvalue($attr_value)
{	
	$arr = array();
	$CI =& get_instance();
	$CI->config->load('const_base',TRUE);
	$list = $CI->config->item('attr_list','const_base');
	$i=0;
	foreach ($list as $key=>$value){
		$tmp = $attr_value|$key;
		if($attr_value==$tmp){
			$arr[$i] = $value;
			$i++;
		}
	}
	return $arr;
}


/**
 * 获得属性数组
 * Enter description here ...
 */
function getcityarr()
{
	$arr = array();
	$CI =& get_instance();
	$CI->config->load('const_city',TRUE);
	$citylist = $CI->config->item('city_list','const_city');
	foreach ($citylist as $key=>$value){
		$tmp = array();
		$tmp['city'] = $value;
		$tmp['value'] = $key;
		$arr[] = $tmp;
	}
	return $arr;
}

/**
 * 根据title获取ID
 * 查询成功则返回对应的城市ID,否则返回杭州的ID
 * Enter description here ...
 * @param $city_title
 * @return numbrice
 */
function getCityIdByTitle($city_title)
{	
	$arr = array();
	$CI =& get_instance();
	$CI->config->load('const_city',TRUE);
	$citylist = $CI->config->item('city_list','const_city');
	$citylist = array_flip($citylist);

	if (isset($citylist[$city_title])) return $citylist[$city_title];
	
	return 1;
}