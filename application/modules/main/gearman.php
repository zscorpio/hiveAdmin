<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/************************************************************
 *
 * 任务调度系统
 * @author Scorpio
 *
*************************************************************/
class Gearman extends Doc_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('url'));
		$this->config->load('base_config',TRUE);
	}

	public function index(){
		$this->work();
	}

	public function work(){
		$config = $this->config->item('base_config');
		$host = $config['gearman']['host'];
		$port = $config['gearman']['port'];
		$worker= new GearmanWorker();
		$worker->addServer($host, $port);	
		function send_request($job){
			var_dump("scorpio");
			include_once(APPPATH.'third_party/snoopy.php');
			$snoopy = new Snoopy;
			$data = json_decode($job->workload());
			$method = $data->method;
			$url = $data->url;
			$params = $data->params;
			$$method = array();
			foreach ($params as $key => $value) {
				$$method[$key] = $value;
			}
			$snoopy->submit($url,$$method);
			$result = $snoopy->results;
			return $result;
		}
		$worker->addFunction("send_request", "send_request");
		while ($worker->work());
	}

	public function client(){
		$config = $this->config->item('base_config');
		$host = $config['gearman']['host'];
		$port = $config['gearman']['port'];
		$client= new GearmanClient();
		$client->addServer($host, $port);
		$data = array(
			'method'	=> 'get',
			'url'		=> 'http://master.500mi.com/main/gearman/test',
			'params'	=> array(
				'wd'	=> '哈哈'
			)
		);
		$job_handle = $client->doBackground("send_request", json_encode($data));
		if ($client->returnCode() != GEARMAN_SUCCESS){
		  echo "bad return code\n";
		  exit;
		}
		$done = false;
		do{
		   sleep(1);
		   $stat = $client->jobStatus($job_handle);
		   var_dump($stat);
		   if (!$stat[0])
		      $done = true;
		   echo "Running: " . ($stat[1] ? "true" : "false") . ", numerator: " . $stat[2] . ", denomintor: " . $stat[3] . "\n";
		}
		while(!$done);
		echo "done!\n";
	}

	public function task(){
		$config = $this->config->item('base_config');
		$host = $config['gearman']['host'];
		$port = $config['gearman']['port'];
		$task= new GearmanTask();
		var_dump($task->taskDenominator());
		var_dump($task->taskNumerator());
	}

	public function test(){
		echo "11";
		sleep(5);
		echo "22";
	}

}