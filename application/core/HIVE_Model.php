<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
class HIVE_Model extends CI_Model
{
	public function __construct(){
		parent::__construct();
		$this->config->load('base_config',TRUE);
		$config = $this->config->item('base_config');
		$thirft = $config['thirft'];
		$GLOBALS['THRIFT_ROOT'] = $thirft['root'];
		require_once $thirft['root'] . 'packages/hive_service/ThriftHive.php';
		require_once $thirft['root'] . 'transport/TSocket.php';
		require_once $thirft['root'] . 'protocol/TBinaryProtocol.php';
		$this->transport = new TSocket($thirft['host'], $thirft['port']);
		$this->protocol = new TBinaryProtocol($this->transport);
		$this->client = new ThriftHiveClient($this->protocol);
		$this->transport->open();
	}

	public function __destruct(){
		$this->transport->close();
	}
}