<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."libraries/Views.php";
/**
 * JETLEE
 * 整个页面分为Layout,Control,Template；全部继承自View（视图）
 * Layout 布局类，用来控制页面整体布局，一个页面只能对应一个布局，可以由Template和Control组成，由模板引擎完成页面渲染
 * Template 模板类，有两层意义：
 * 		1是页面的组成单元，每个模板都对应页面的某个区域 ，一般指页面的核心区域。
 * 		2模板渲染引擎，无论LAYOUT，还是CONTROL可以使用TEMPLATE来渲染页面
 * Control 控件类 是指可在模板中需要重用的独立的区域，如某个城市下拉列表，类目选择;
 * 
 * 所有页面控 件均继承自此类，放在VIEWS/CONTROLS下
 * 页面控件可以对应.PHP代码并由模板引擎调用.HTM模板完成渲染；可以只有CONTROL代码；可以只有.HML模板
 * @author Administrator
 *
 */
class MY_Control extends Views{ 

	protected $_file;
	function __construct()
	{
		parent::__construct();
	}
	
	function set_file($file){
		$this->_file = $file;
	}
	
	function render($vars=array()){
		return $this->load->template($this->_file,$vars,true);
	}
	
}
?>
