<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function getallunit()
{	
	$CI =& get_instance();
	$CI->config->load('const_unit',TRUE);
	return $CI->config->item('unit_list','const_unit');
}

function getunit($unitid)
{	
	$CI =& get_instance();
	$CI->config->load('const_unit',TRUE);
	$unitlist = $CI->config->item('unit_list','const_unit');

	return isset($unitlist[$unitid]) ? $unitlist[$unitid] : '';
}