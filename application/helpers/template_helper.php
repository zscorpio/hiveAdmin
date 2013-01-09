<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * JETLEE
 * 与模板处理相关的辅助类
 * 
 *
 */	

function subtplcheck($subfiles, $mktime, $tpl,$tplrefresh=0) {
	
	if($tplrefresh&& ($tplrefresh == 1 || mt_rand(1, $tplrefresh) == 1)) {
		$subfiles = explode('|', $subfiles);
		foreach ($subfiles as $subfile) {
			$tplfile = $subfile.'.htm';
			@$submktime = filemtime($tplfile);
			if($submktime > $mktime) {
				include_once(APPPATH.'libraries/Template.php');
				parse_template($tpl);
				break;
			}
		}
	}
}

// 表单加密验证，防止重复提交表单与csrf
function formhash() {
	$CI =& get_instance();
	$mtime = explode(' ', microtime());
	$timestamp = $mtime[1];
	$uid = $Ci->session->userdata('uid');
	$salt = '[、Z$e2*/';

	$formhash = $CI->session->userdata('formhash');

	if(!empty($formhash)) {
		$formhash = substr(md5(substr($timestamp, 0, -7).'|'.$uid.'|'.md5($salt).'|'), 8, 8);
		$CI->session->set_userdata('formhash', $formhash);
	}
	return $formhash;
}

function debuginfo($starttime=0,$isshow=0) {
	if(!$isshow) {
		$info = '';
	} else {
		$mtime = explode(' ', microtime());
		$totaltime = number_format(($mtime[1] + $mtime[0] - $starttime), 4);
		$info = 'Processed in '.$totaltime.' second(s)';
	}

	return $info;
}

if ( ! function_exists('resource'))
{
	function resource($file,$level='',$display='url',$params='') {
		include_once(APPPATH.'libraries/Assets.php');
		$assets = new Assets();
		if($display==='url'){
			return $assets->url($file,$level);
		}elseif($display==='file'){
			return $assets->load($file,$level,$params);
		}else{
			return $assets->url($file,$level);
		}		
	}
}

function control($name,$vars=array(),$flag='') {
	if ( ! class_exists('MY_Control')){
		$control = load_class('Control', 'core');
	}
	
	$module = CI::$APP->router->fetch_module();
	// list($path, $_control) = Modules::find($name.'_control', $module, 'controls/');
	$path = APPPATH.'modules/'.$module.'/controls/'.$name.'_control';

	if(file_exists($path.'.php')) {
		$_control = $name.'_control';
		//$_control = ucfirst($name.'_control');
		Modules::load_file($_control, APPPATH.'modules/'.$module.'/controls/');
		$control = new $_control($vars);
		$control->set_file($_control);
		return $control->render($vars);
	}else{
		if (empty($vars)) {
			include_once(APPPATH.'libraries/Template.php');
			$template = new Template();
			$content = $template->sreadfile($path.'.htm');
		} else {
			$control->set_file($path);
			$content = $control->render($vars);
		}

		return $content;
	}
}

