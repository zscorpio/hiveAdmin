<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| 邮件类配置
|--------------------------------------------------------------------------
|
| 邮件类一些基础配置
| 包括帐号信息等
|
*/

//配置参数
$config['protocol'] = 'smtp'; 
$config['smtp_host'] = 'smtp.exmail.qq.com'; 
$config['smtp_user'] = 'noreply@500mi.com'; 
$config['smtp_pass'] = '500mi2012'; 
$config['smtp_port'] = 25; 
$config['smtp_timeout'] = 5;
$config['wordwrap'] = TRUE; 
$config['mailtype'] = 'html'; 
$config['charset'] = 'utf-8'; 
$config['priority'] = 3; 
$config['validate'] = TRUE; 
?>