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
####Q:WARNING: org.apache.hadoop.metrics.jvm.EventCounter is deprecated. Please use org.apache.hadoop.log.metrics.EventCounter in all the log4j.properties files.
	这个报错是在/usr/hadoop/hive-0.9.0/bin/hive --service hiveserver的时候命令行下出现的错误
	解决的办法是vim /usr/hadoop/hive-0.9.0/conf/hive-log4j.properties
	将log4j.appender.EventCounter的值修改为org.apache.hadoop.log.metrics.EventCounter
####Q:Warning: $HADOOP_HOME is deprecated
	编辑.bash_profile文件
	export HADOOP_HOME_WARN_SUPPRESS=1
####Q:Failed with exception java.io.IOException: File /tmp/hive-root/hive_2013-01-16_22-36-46_262_8325390394866081524/-ext-10000/test.txt could only be replicated to 0 nodes, instead of 1
	可以看下jps命令,看下datanode和namenode是不是起来了,没有的话就
	/usr/hadoop/hadoop-1.1.1/bin/hadoop-daemon.sh start namenode	
	/usr/hadoop/hadoop-1.1.1/bin/hadoop-daemon.sh start datanode
	单独重新起一遍
	关闭slave的iptables
####Q:FAILED: Hive Internal Error: java.lang.RuntimeException(org.apache.hadoop.ipc.RemoteException: org.apache.hadoop.hdfs.server.namenode.SafeModeException: Cannot create directory /tmp/hive-root/hive_2013-01-17_08-29-50_514_3377431792490577734. Name node is in safe mode.The reported blocks is only 0 but the threshold is 0.9990 and the total blocks 5. Safe mode will be turned off automatically.
	/usr/hadoop/hadoop-1.1.1/bin/hadoop dfsadmin -safemode leave 离开安全模式
####Q:Failed with exception java.io.IOException:java.io.IOException: Could not obtain block: blk_6921337026575992023_1409 file=/user/hive/warehouse/login/test.txt
		datanode没起来 /usr/hadoop/hadoop-1.1.1/bin/hadoop-daemon.sh start datanode
		datanode起不来不知道为什么,我把tmp删掉貌似就好了...求解决方法
####Q:FAILED: Execution Error, return code 1 from org.apache.hadoop.hive.ql.exec.DDLTask<br>org.apache.commons.dbcp.SQLNestedException: Cannot get a connection, pool error Could not create a validated object, cause: A read-only user or a user in a read-only database is not permitted to disable read-only mode on a connection.<br>NestedThrowables:<br>FAILED: Error in metadata: javax.jdo.JDOFatalDataStoreException: Cannot get a connection, pool error Could not create a validated object, cause: A read-only user or a user in a read-only database is not permitted to disable read-only mode on a connection.<br>Database Class Loader started - derby.database.classpath=''<br><br>on database directory /root/metastore_db in READ ONLY mode<br>Booting Derby version The Apache Software Foundation - Apache Derby - 10.4.2.0 - (689064): instance a816c00e-013c-469b-c608-00000036f028<br>2013-01-17 03:44:40.112 GMT:<br>----------------------------------------------------------------<br>2013-01-17 03:44:39.430 GMT Thread[main,5,main] java.io.FileNotFoundException: derby.log (Permission denied
	chown -R root:root metastore_db
	如果不知道是哪个....那就全部吧.....
####Q:FAILED: Execution Error, return code 1 from org.apache.hadoop.hive.ql.exec.DDLTask<br>java.lang.reflect.InvocationTargetException NestedThrowables:FAILED: Error in metadata: javax.jdo.JDOFatalInternalException: Error creating transactional connection factory
	需要把mysql-connector-java-5.1.15-bin.jar拷贝到hive的lib目录下才行
####Q:at org.apache.hadoop.hdfs.server.namenode.FSPermissionChecker.check(FSPermissionChecker.java:199)<br>Caused by: org.apache.hadoop.ipc.RemoteException: org.apache.hadoop.security.AccessControlException: Permission denied: user=www, access=WRITE, inode="tmp":root:supergroup:rwxr-xr-x
	/usr/hadoop/hadoop-1.1.1/bin/hadoop fs -chmod 777  /tmp
	/usr/hadoop/hadoop-1.1.1/bin/hadoop dfs -chown -R  root:root /tmp
	/usr/hadoop/hadoop-1.1.1/bin/hadoop dfs -chmod a+w /tmp


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