## 基础控制命令:
	启动Hadoop守护进程：
		 /usr/hadoop/hadoop-1.1.1/bin/start-all.sh	
	启动hbase
		 /usr/hadoop/hbase-0.94.2/bin/start-hbase.sh
	启动thrift
		/usr/hadoop/hive-0.9.0/bin/hive --service hiveserver
	重启nginx
		service nginx restart
	重启php-fpm
		service php-fpm restart
	开启samba服务
		service smb start 
	关闭iptables
		service iptables stop 
	开启hwi
		/usr/hadoop/hive-0.9.0/bin/hive --service hwi
		访问:http://hadoop.500mi.com:9999/hwi/
	单独启动namenode或者datanode
	/usr/hadoop/hadoop-1.1.1/bin/hadoop-daemon.sh start namenode	
	/usr/hadoop/hadoop-1.1.1/bin/hadoop-daemon.sh start datanode	
	重启需要关闭iptables,开启smb,开启hadoop,开启thrift...(个人需要)	
