<?php
	// 基本配置
	$config = array(
		'thirft'		=>	array(
			'host'		=>	'192.168.1.59',
			'port'		=>	'10000',
			'root'		=>	APPPATH.'third_party/thrift/'
		),
		'log_path'		=> APPPATH.'logs/',
		'results'		=> APPPATH.'results/',
		'lang_set'		=> 'zh_CN.UTF-8',
		'hadoop_home' 	=> '/usr/hadoop/hadoop-1.1.1',
		'java_home'		=> '/usr/java/jdk1.6.0_37',
		'hive_home'		=> '/usr/hadoop/hive-0.9.0',
		'out_seperator'	=> '\t'
	);