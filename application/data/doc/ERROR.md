####Q: proc_open() has been disabled for security reason  
	修改php.ini中的disable_functions去掉proc_open
####Q: Warning: stream_set_timeout(): supplied argument is not a valid stream resource in /home/wwwroot/phpHiveAdmin/libs/transport/TSocket.php on line 194
	修改php.ini中的disable_functions
	disable_functions = passthru,exec,system,chroot,scandir,chgrp,chown,shell_exec,proc_get_status,ini_alter,ini_alter,ini_restore,dl,openlog,syslog,readlink,symlink,popepassthru
####Q: Fatal error: Uncaught exception 'TException' with message 'TSocket: Could not connect to 192.168.1.208:10000 (Connection refused [111])' in /home/wwwroot/phpHiveAdmin/libs/transport/TSocket.php:191 Stack trace: #0 /home/wwwroot/phpHiveAdmin/dbList.php(9): TSocket->open() #1 {main} thrown in /home/wwwroot/phpHiveAdmin/libs/transport/TSocket.php on line 191
	没有启动thrift
	/usr/hadoop/hive-0.9.0/bin/hive --service hiveserver
	netstat -nl | grep 10000**
	如果出现
	tcp        0      0 :::10000                    :::*                        LISTEN
	说明启动成功
####Q: FAILED: Error in metadata: MetaException(message:Got exception: java.net.ConnectException Call to localhost/127.0.0.1:9000 failed on connection exception: java.net.ConnectException: Connection refused)FAILED: Execution Error, return code 1 from org.apache.hadoop.hive.ql.exec.DDLTask
	是hadoop的配置出错了,
	conf/core-site.xml
	=>vim core-site.xml(这里localhost可能需要配置host,或者换成ip)
	<configuration>
		<property>
			<name>fs.default.name</name>
			<value>hdfs://localhost:9000</value>
		</property>
	</configuration>
	http://stackoverflow.com/questions/10735843/create-table-exception-in-hive
####Q: FAILED: Error in metadata: javax.jdo.JDOFatalDataStoreException: Failed to start database '/home/wwwroot/phpHiveAdmin/metastore_db', see the next exception for details.NestedThrowables:java.sql.SQLException: Failed to start database '/home/wwwroot/phpHiveAdmin/metastore_db', see the next exception for details.FAILED: Execution Error, return code 1 from org.apache.hadoop.hive.ql.exec.DDLTask
	这个应该是不能同时开着thrift和cli的原因,所以只要关闭thrift就好了

### 附录:
		1.如果出现下面错误
			$ bin/hadoop namenode –format
			12/11/24 02:30:56 INFO namenode.NameNode: STARTUP_MSG:
			/************************************************************
			STARTUP_MSG: Starting NameNode
			STARTUP_MSG:   host = java.net.UnknownHostException: Test.localhost: Test.localh                                                      ost
			STARTUP_MSG:   args = [–format]
			STARTUP_MSG:   version = 1.1.1
			STARTUP_MSG:   build = https://svn.apache.org/repos/asf/hadoop/common/branches/branch-1.1 -r 1394289; compiled by 'hortonfo' on Thu Oct  4 22:06:49 UTC 2012
			************************************************************/
			Usage: java NameNode [-format [-force ] [-nonInteractive]] | [-upgrade] | [-rollback] | [-finalize] | [-importCheckpoint] | [-recover [ -force ] ]
			12/11/24 02:30:56 INFO namenode.NameNode: SHUTDOWN_MSG:
			/************************************************************
			SHUTDOWN_MSG: Shutting down NameNode at java.net.UnknownHostException: Test.localhost: Test.localhost
			************************************************************/
			hostname提示Test.localhost
		vim /etc/hosts
		添加127.0.0.1 Test.localhost