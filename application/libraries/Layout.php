<?php  
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."libraries/Views.php";

class Layout extends Views{
	//private $obj;
	private $layout;
	
	function __construct($layout = "default/layout_main")
	{
		parent::__construct();
		//$this->obj =& get_instance();
		$this->layout = $layout;
		$this->load->config('site_settings', TRUE);
		$this->siteconfig = $this->config->item('site_settings');
		$this->siteconfig['charset'] = $this->config->item('charset');
	}

	function setLayout($layout)
	{
	  $this->layout = $layout;
	}

	function view($view, $data=null, $return=false)
	{
		$loadedData = array();
		$loadedData['content_for_layout'] = $this->load->view($view,$data,true);

		if($return)
		{
			$output = $this->load->view($this->layout, $loadedData, true);
			return $output;
		}
		else
		{
			$this->load->view($this->layout, $loadedData, false);
		}
	}
	
	function template($template, $data=null, $return=false)
	{
		$loadedData = array();
		$loadedData['content_for_layout'] = $this->load->template($template,$data,true);
		$loadedData['_SC'] =  $this->siteconfig;
		//var_dump($loadedData);die();
		if($return)
		{
			$output = $this->load->template($this->layout, $loadedData, true);
			return $output;
		}
		else
		{
			$this->load->template($this->layout, $loadedData, false);
		}
	}
}

/* End of file: Layout.php */