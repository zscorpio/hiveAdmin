<?php
/**
 *--------------------------------------------------------------------------
 * 日志记录类
 *--------------------------------------------------------------------------
 * 修改：实现由Zend_Log转移到CI_Log, 保持调用方法不变
 * @by danchex
 * Sorry, 计划取消 ：CI_Log 依赖 Config, 一旦设置了 Config, 可能影响所有Log
 *
 */

class Logger
{
	/**
     * 类型定义，暂时不动Ta，Log迁移完成后，考虑是否去掉没有的类型
     */
	const EMERG   = 0;  // Emergency: system is unusable
    const ALERT   = 1;  // Alert: action must be taken immediately
    const CRIT    = 2;  // Critical: critical conditions
    const ERR     = 3;  // Error: error conditions
    const WARN    = 4;  // Warning: warning conditions
    const NOTICE  = 5;  // Notice: normal but significant condition
    const INFO    = 6;  // Informational: informational messages
    const DEBUG   = 7;  // Debug: debug messages

	/**
     * 构造函数，载入CI->Log库
     */
	public function __construct()
	{
		// $this->ci =& get_instance();
		// $this->ci->load->library('log');
		$this->ci =& get_instance();
		$this->config = $this->ci->config;
	}

	/**
	 * 记录日志(等级，消息，文件前缀-分组)
	 * Enter description here ...
	 * @param $level
	 * @param $message
	 * @param $filename
	 */
	function log($level, $message, $filename='application')
	{
		require_once 'Zend/Log.php';
		@mkdir(APPPATH.'./logs/'.substr($filename,0,strrpos($filename,'/')),0777,1);
		$logger = Zend_Log::factory(array(
		    'timestampFormat' => 'Y-m-d H:i:s',
		    array(
		        'writerName'   => 'Stream',
		        'writerParams' => array(
		            'stream'   => APPPATH.'/logs/'.$filename.'-'.date('Y-m-d').'.log',
		        ),
		        'formatterName' => 'Simple',
		        'formatterParams' => array(
		            'format'   => '%timestamp%: %message%'.PHP_EOL,
		        ),
		        'filterName'   => 'Priority',
		        'filterParams' => array(
		            'priority' => Zend_Log::DEBUG,
		        ),
		    ),
		));
		$logger->log($message,$level);
	}

	/**
	 * 记录日志
	 * Enter description here ...
	 * @param $level
	 * @param $message
	 * @param $filename
	 */
	public function log_order($level, $message, $filename='order_process')
	{
		$this->log($level, $message, $filename);
	}

	/**
	 * 记录日志
	 * Enter description here ...
	 * @param $level
	 * @param $message
	 * @param $filename
	 */
	function log_biz($level, $message, $filename='biz')
	{
		$this->log($level, $message, $filename);
	}

	/**
	 * 记录登录状态
	 * @param $message
	 * @author scorpio
	 */
	function login_log($message)
	{
		$this->ci->load->library('session');
		$uid = $this->ci->session->userdata("uid");
		$role_ids = $this->ci->session->userdata("role_ids");
		$ip = $this->ci->session->userdata("ip_address");
		$browser = $this->ci->session->userdata("user_agent");
		$locate = $this->ci->session->userdata("locate");
		$tmp = substr($uid, -2, 2); 
		$child_dir_name = sprintf("%02d",$tmp);
		$root_dir = APPPATH."/logs/login/";
		$parent_dir = APPPATH."/logs/login/".$role_ids;
		$child_dir = APPPATH."/logs/login/".$role_ids."/".$child_dir_name;
		$file = APPPATH."/logs/login/".$role_ids."/".$child_dir_name."/".$uid.".txt";
		if (!is_dir($root_dir)){
			mkdir($root_dir, 0777);
		}
		if (!is_dir($parent_dir)){
			mkdir($parent_dir, 0777);
		}
		if (!is_dir($child_dir)){
			mkdir($child_dir, 0777);
		}
		if(!file_exists($file))
		{
			$fp= fopen($file,"w");
			fclose($fp);
		}
		$data = array();
		$data["date"] = date("Y-m-d G:i:s");
		$data["ip"] = $ip;
		$data["browser"] = $browser;
		$data["locate"] = $locate;
		$data["message"] = $message;
		$res = json_encode($data)."\r\n";
		file_put_contents($file, $res, FILE_APPEND);
	}

	/**
	 * 打log
	 */
	public function write($level,$message,$path){
		$real_path = APPPATH.'logs/'.$level."/".substr($path,0,strrpos($path,'/'));
		if(!file_exists($real_path)){
			mkdir($real_path,0777,1);
		}
		$stream =  APPPATH.'logs/'.$level."/".$path.'-'.date('Y-m-d').'.log';
		if(!file_exists($stream))
		{
			$fp= fopen($stream,"w");
			fclose($fp);
		}
		$res = $message."\r\n";
		file_put_contents($stream, $res, FILE_APPEND);
	}
}
//Class::EOF