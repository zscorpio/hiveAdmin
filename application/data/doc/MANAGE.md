## 基础控制命令:
	格式化一个新的分布式文件系统:
		/usr/hadoop/hadoop-1.1.1/bin/hadoop namenode -format
	启动Hadoop守护进程：
		 /usr/hadoop/hadoop-1.1.1/bin/start-all.sh	
	启动hbase
		 /usr/hadoop/hbase-0.94.2/bin/start-hbase.sh
	启动thrift
		/usr/hadoop/hive-0.9.0/bin/hive --service hiveserver
		/usr/hadoop/hive-0.9.0/bin/hive --service hiveserver >/dev/null 2>/dev/null &(这样不会输出结果，后台运行)
	全部web服务重启
		/root/lnmp restart
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
	查看端口被哪个进程占用
		 lsof -i:10000(yum install lsof)
		 kill -9 PID
	时间校对
		rdate -t 60 -s stdtime.gov.hk	 
	格式化
		/usr/hadoop/hadoop-1.1.1/bin/stop-all.sh
		find / -name derby.logderby.log	 rm -rf derby.log
		find / -name metastore_db rm -rf metastore_db
		find / -name metastore_db -exec rm -rf {} \;
		find / -name derby.log -exec rm -rf {} \;
		rm -rf /tmp/*
		/usr/hadoop/hadoop-1.1.1/bin/hadoop namenode -format
