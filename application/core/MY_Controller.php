<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* The MX_Controller class is autoloaded as required */

class MY_Controller extends CI_Controller {
	
	protected $_data = array();

	function __construct()
	{
		parent::__construct();
		$this->config->load('base_config',TRUE);
		$config = $config = $this->config->item('base_config');
		$thirft = $config['thirft'];
		$GLOBALS['THRIFT_ROOT'] = $thirft['root'];
		require_once $thirft['root'] . 'packages/hive_service/ThriftHive.php';
		require_once $thirft['root'] . 'transport/TSocket.php';
		require_once $thirft['root'] . 'protocol/TBinaryProtocol.php';
		$this->transport = new TSocket($thirft['host'], $thirft['port']);
		$this->protocol = new TBinaryProtocol($this->transport);
		$this->client = new ThriftHiveClient($this->protocol);
		$this->transport->open();
		$this->client->execute('show databases');
		$db_list = $this->client->fetchAll();
		$this->_data["db_list"] = $db_list;
	}
}	


