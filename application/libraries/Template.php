<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//require APPPATH."libraries/Views.php";
/*
	500mi GROUP(C) 2010-2011 Author:JETLEE
*/
class Template {

	private $_STEMPLATE;
	private $_tpl_path;
	private $_file;
	private $_tplrefresh;
	
	/*
	 * 模板的路径，文件名
	 */
    function __construct($path='',$file='')
    {
    	//parent::__construct();
    	$this->_STEMPLATE = array();
    	$this->_STEMPLATE['i'] = 0;
    	$this->_STEMPLATE['block_search'] = array();
    	$this->_STEMPLATE['block_replace'] = array();
    	$this->_tpl_path = $path;
    	$this->_file = $file;
    	$this->_tplrefresh = 0;
    }
	
	function parse_template($tpl='') {
	
		//包含模板
		$this->_STEMPLATE['sub_tpls'] = array($tpl);
	
		$tplfile = $tpl.'.htm'; 
		$objfile = APPPATH.'./views/tpl_cache/'.str_replace('/','_',$tpl).'.php';
		
		$template = $this->sreadfile($tplfile);
		if(empty($template)) {
			exit("Template file : $tplfile Not found or have no access!");
		}
	
		//模板
		$template = preg_replace("/\<\!\-\-\{template\s+([a-z0-9_\/]+)\}\-\-\>/ie", "\$this->readtemplate('\\1')", $template);
		//处理子页面中的代码
		$template = preg_replace("/\<\!\-\-\{template\s+([a-z0-9_\/]+)\}\-\-\>/ie", "\$this->readtemplate('\\1')", $template);		
		//处理页面中的控件
		//$template = preg_replace("/\<\!\-\-\{control\s+([a-z0-9_\/]+)\}\-\-\>/ie", "\$this->readcontrol('\\1')", $template);
		$template =  preg_replace("/\<\!\-\-\{control\((.+?)\)\}\-\-\>/ie", "\$this->controltags('\\1')", $template);
		//处理页面中的URL
		$template =  preg_replace("/\<\!\-\-\{resource\((.+?)\)\}\-\-\>/ie", "\$this->resourcetags('\\1')", $template);
		//PHP代码
		$template = preg_replace("/\<\!\-\-\{eval\s+(.+?)\s*\}\-\-\>/ies", "\$this->evaltags('\\1')", $template);
	
		//开始处理
		//变量
		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
		$template = preg_replace("/(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/s", "\\1['\\2']", $template);
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
		$template = preg_replace("/$var_regexp/es", "\$this->addquote('<?=\\1?>')", $template);
		$template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "\$this->addquote('<?=\\1?>')", $template);
		//逻辑
		$template = preg_replace("/\{elseif\s+(.+?)\}/ies", "\$this->stripvtags('<?php } elseif(\\1) { ?>','')", $template);
		$template = preg_replace("/\{else\}/is", "<?php } else { ?>", $template);
		//循环
		for($i = 0; $i < 6; $i++) {
			$template = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}(.+?)\{\/loop\}/ies", "\$this->stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<?php } } ?>')", $template);
			$template = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}(.+?)\{\/loop\}/ies", "\$this->stripvtags('<?php if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<?php } } ?>')", $template);
			$template = preg_replace("/\{if\s+(.+?)\}(.+?)\{\/if\}/ies", "\$this->stripvtags('<?php if(\\1) { ?>','\\2<?php } ?>')", $template);
		}
		//常量
		$template = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/s", "<?=\\1?>", $template);
		
		//替换
		if(!empty($this->_STEMPLATE['block_search'])) {
			$template = str_replace($this->_STEMPLATE['block_search'], $this->_STEMPLATE['block_replace'], $template);
		}
		
		//换行
		$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
		
		//附加处理
		$mtime = explode(' ', microtime());
		$timestamp = $mtime[1];
		$template = "<?php if(!defined('BASEPATH')) exit('Access Denied');?><?php subtplcheck('".implode('|', $this->_STEMPLATE['sub_tpls'])."', '$timestamp', '$tpl','$this->_tplrefresh');?>$template";
		//write
		if(!$this->swritefile($objfile, $template)) {
			exit("File: $objfile can not be write!");
		}
	}
	
	function addquote($var) {
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}
	
	function striptagquotes($expr) {
		$expr = preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr);
		$expr = str_replace("\\\"", "\"", preg_replace("/\[\'([a-zA-Z0-9_\-\.\x7f-\xff]+)\'\]/s", "[\\1]", $expr));
		return $expr;
	}
	
	function evaltags($php) {
	
		$this->_STEMPLATE['i']++;
		$search = "<!--EVAL_TAG_{$this->_STEMPLATE['i']}-->";
		$this->_STEMPLATE['block_search'][$this->_STEMPLATE['i']] = $search;
		$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php ".$this->stripvtags($php)." ?>";
		
		return $search;
	}
	
	function blocktags($parameter) {
	
		$this->_STEMPLATE['i']++;
		$search = "<!--BLOCK_TAG_{$this->_STEMPLATE['i']}-->";
		$this->_STEMPLATE['block_search'][$this->_STEMPLATE['i']] = $search;
		$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php block(\"$parameter\"); ?>";
		return $search;
	}
	
	function resourcetags($resource) {
		$this->_STEMPLATE['i']++;
		$search = "<!--MURL_TAG_{$this->_STEMPLATE['i']}-->";
		$this->_STEMPLATE['block_search'][$this->_STEMPLATE['i']] = $search;
		$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php echo resource($resource); ?>";
		return $search;
	}
	
	function adtags($pagetype) {
	
		$this->_STEMPLATE['i']++;
		$search = "<!--AD_TAG_{$this->_STEMPLATE['i']}-->";
		$this->_STEMPLATE['block_search'][$this->_STEMPLATE['i']] = $search;
		$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php adshow('$pagetype'); ?>";
		return $search;
	}
	
	function datetags($parameter) {
	
		$this->_STEMPLATE['i']++;
		$search = "<!--DATE_TAG_{$this->_STEMPLATE['i']}-->";
		$this->_STEMPLATE['block_search'][$this->_STEMPLATE['i']] = $search;
		$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php echo sgmdate($parameter); ?>";
		return $search;
	}
	
	function avatartags($parameter) {

		$this->_STEMPLATE['i']++;
		$search = "<!--AVATAR_TAG_{$this->_STEMPLATE['i']}-->";
		$this->_STEMPLATE['block_search'][$this->_STEMPLATE['i']] = $search;
		$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php echo avatar($parameter); ?>";
		return $search;
	}
	
	function stripvtags($expr, $statement='') {
		$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}
	
	function controltags($parameter) {
		$paras = explode(',', $parameter);		
		$_name = trim($paras[0],'\'');
		$vars = ltrim($parameter,$paras[0].',');
		
		$this->_STEMPLATE['i']++;
		$search = "<!--CONTROL_TAG_{$this->_STEMPLATE['i']}-->";
		$this->_STEMPLATE['block_search'][$this->_STEMPLATE['i']] = $search;
		if(empty($vars)){
			$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php echo control('$_name'); ?>";
		}else{
			$this->_STEMPLATE['block_replace'][$this->_STEMPLATE['i']] = "<?php echo control('$_name',$vars); ?>";
		}
		return $search;
	}
	
	/* 后三条与主模板的查询规则相同
	 * 1 先去模块TPL目录下找，如果直接给的子模板名，认为是在主模板同目录下找；如果是用户自定义的子模板（有/分割），则取用户自定义的路径
	 * 2 再去模块VIEWS目录下找(这个不实现，模板暂不许放在VIEWS下)
	 * 3 再去应用根目录TPL下找；
	 * 4 再去应用根目录VIEWS目录下找；
	 */
	function readtemplate($name) {

		$tpl = $this->strexists($name,'/')?$name:"$this->_tpl_path/$name";
		$tplfile = $tpl.'.htm'; 
		
		$this->_STEMPLATE['sub_tpls'][] = $tpl;
		
		if(!file_exists($tplfile)) {
			$tplfile = APPPATH.'./tpl/'.$tpl.'.htm';
		}
		if(!file_exists($tplfile)) {
			$tplfile = APPPATH.'./views/'.$tpl.'.htm';
		}
			
		$content = $this->sreadfile($tplfile);
		return $content;
	}	

	//获取文件内容
	function sreadfile($filename) {
		$content = '';
		if(function_exists('file_get_contents')) {
			@$content = file_get_contents($filename);
		} else {
			if(@$fp = fopen($filename, 'r')) {
				@$content = fread($fp, filesize($filename));
				@fclose($fp);
			}
		}
		return $content;
	}
	
	//写入文件
	function swritefile($filename, $writetext, $openmod='w') {
		if(@$fp = fopen($filename, $openmod)) {
			flock($fp, 2);
			fwrite($fp, $writetext);
			fclose($fp);
			return true;
		} else {
			log_message('error', "File: $filename write error.");
			return false;
		}
	}
	
	//获取文件名后缀
	function fileext($filename) {
		return strtolower(trim(substr(strrchr($filename, '.'), 1)));
	}
	
	//获取目录
	function sreaddir($dir, $extarr=array()) {
		$dirs = array();
		if($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(!empty($extarr) && is_array($extarr)) {
					if(in_array(strtolower(fileext($file)), $extarr)) {
						$dirs[] = $file;
					}
				} else if($file != '.' && $file != '..') {
					$dirs[] = $file;
				}
			}
			closedir($dh);
		}
		return $dirs;
	}
	
	//判断字符串是否存在
	function strexists($haystack, $needle) {
		return !(strpos($haystack, $needle) === FALSE);
	}
	

}
	
