<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 *	tab解析
 */
class Tab_biz
{

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
	}

	function tabExplode($array)
	{
		foreach ($array as $key => $value) {
			$array[$key] = explode("\t", $value);
		}
		return $array;
	}

}