<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* The MX_Controller class is autoloaded as required */

class MY_Controller extends CI_Controller {
	
	protected $_data = array();

	function __construct()
	{
		parent::__construct();
		$this->load->business('database_biz');
		$this->_data['db_list'] = $this->database_biz->listDb();
	}
}	


