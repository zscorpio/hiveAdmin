本代码基于phphiveadmin编写,原repo在https://github.com/xianglei/phpHiveAdmin, 本软件遵从GPLv3协议
web环境配好之后http://ip/doc里面还有一些其他的文档
## 0.环境
	CentOS release 6.3 (Final)
	jdk-6u37
	hadoop 1.1.1
	hive 0.9.0
	thrift-0.9.0
## 1. 前期准备(纯个人)  
	1. 增加PS1配色  
		PS1='\n\[\e[01;37m\][`a=$?;if [ $a -ne 0 ]; then echo -n -e "\[\e[01;32;41m\]{$a}"; fi`\[\033[01;32m\]\u\[\033[01;33m\]@\[\033[01;35m\]\h\[\033[00m\] \[\033[01;34m\]`pwd``B=$(git branch 2>/dev/null | sed -e "/^ /d" -e "s/* \(.*\)/\1/"); if [ "$B" != "" ]; then S="git"; elif [ -e .bzr ]; then S=bzr; elif [ -e .hg ]; then S="hg"; elif [ -e .svn ]; then S="svn"; else S=""; fi; if [ "$S" != "" ]; then if [ "$B" != "" ]; then M=$S:$B; else M=$S; fi; fi; [[ "$M" != "" ]] && echo -n -e "\[\e[33;40m\]($M)\[\033[01;32m\]\[\e[00m\]"`\[\033[01;34m\]\[\e[01;37m\]]\n\[\e[01;34m\]$ \[\e[00m\]'
	2. 文件夹布局  
		/home/root/data--数据存储  
		/home/root/download--下载程序
## 2. 安装jdk  
	1.检查是否安装  
		java -version 
		如果存在 	rpm -qa|grep jdk 和 rpm -qa|grep java
					rpm -e --nodeps jdk-1.6.0_37-fcs.586类似方法删除全部
		可以删除
		不存在则安装
	2.下载jdk  
		http://www.oracle.com/technetwork/java/javase/downloads/jdk6u37-downloads-1859587.html
		好像jdk7安装不上,所以下载jdk6
		wget http://download.oracle.com/otn-pub/java/jdk/6u37-b06/jdk-6u37-linux-i586-rpm.bin?AuthParam=1353690819_8060052c7555149f21a9085f7d80d606
		后面的AuthParam必须要
		下载的版本是6u37
	3.安装  
		mkdir /usr/java/
		mv jdk-6u37-linux-i586-rpm.bin /usr/java/
		cd /usr/java/
		chmod 777 jdk-6u37-linux-i586-rpm.bin
		./jdk-6u37-linux-i586-rpm.bin
	4.配置  
		vim /etc/profile
		最后增加
		export JAVA_HOME=/usr/java/jdk1.6.0_37
		export CLASSPATH=.:$JAVA_HOME/jre/lib/rt.jar:$JAVA_HOME/lib/dt.jar:$JAVA_HOME/lib/tools.jar
		export PATH=$PATH:$JAVA_HOME/bin
		source /etc/profile 应用配置
	5.测试  
		java -version
		小程序
		public class Test{  
		   public static void main(String args[]){  
		      System.out.println("Hello,World!");  
		   }  
		}  
		编译：javac Test.java
		运行：java Test
## 3. 安装hadoop
	0. require jdk&ssh
		官方中文文档:http://hadoop.apache.org/docs/r0.20.0/cn/index.html
	1.下载hadoop
		http://www.apache.org/dyn/closer.cgi/hadoop/common/
		wget http://mirror.bjtu.edu.cn/apache/hadoop/common/hadoop-1.1.1/hadoop-1.1.1.tar.gz(北京大学的源,速度较快)
		hadoop 1.1.1版本
	2.ssh配置
		如果没有密钥
		ssh-keygen -t rsa 
		如果不能直接登录(ssh localhost)
		cat ~/.ssh/id_rsa.pub >> ~/.ssh/authorized_keys
		ssh公钥复制到服务器
		ssh-copy-id -i .ssh/id_rsa.pub root@master.500mi.com
		ssh-copy-id -i .ssh/id_rsa.pub root@slave.500mi.com
	3.安装hadoop
		tar -zxvf hadoop-1.1.1.tar.gz
		mkdir /usr/hadoop
		mkdir /usr/hadoop/hadoop-1.1.1
		然后移动到/usr/hadoop/hadoop-1.1.1
		mv /root/download/hadoop-1.1.1 /usr/hadoop/
	4.配置hadoop
		(伪分布式模式)
		vim conf/hadoop-env.sh
		第八九行,去注释
		#The java implementation to use.  Required.
		export JAVA_HOME=/usr/java/jdk1.6.0_37
		=>vim core-site.xml(这里localhost可能需要配置host,或者换成ip)
	
		<configuration>
			<property>
				<name>fs.default.name</name>
				<value>hdfs://localhost:9000</value>
			</property>
		</configuration>

		=>vim hdfs-site.xml
		<configuration>
			<property>
				<name>dfs.replication</name>
				<value>1</value>
			</property>
		</configuration>
	
		=>vim mapred-site.xml
		<configuration>
			<property>
				<name>mapred.job.tracker</name>
				<value>localhost:9001</value>
			</property>
		</configuration>

	5.启动hadoop
		格式化一个新的分布式文件系统:
			/usr/hadoop/hadoop-1.1.1/bin/hadoop namenode -format
		启动Hadoop守护进程：
			/usr/hadoop/hadoop-1.1.1/bin/start-all.sh
		Hadoop守护进程的日志写入到 ${HADOOP_LOG_DIR} 目录 (默认是 ${HADOOP_HOME}/logs).
		浏览NameNode和JobTracker的网络接口，它们的地址默认为:
			NameNode - http://localhost:50070/
			JobTracker - http://localhost:50030/	
		jps
			9650 DataNode
			9754 SecondaryNameNode
			9546 NameNode
			10121 Jps
			9950 TaskTracker
			9831 JobTracker
	6.测试hadoop
		cd ~
		mkdir hadoopData
		cd hadoopData
		echo "a b c a b c aa bb c" >test.txt		
## 4.安装hive
	0.require hadoop
	1.下载hive
		http://mirror.bit.edu.cn/apache/hive/
		wget http://mirror.bit.edu.cn/apache/hive/hive-0.9.0/hive-0.9.0.tar.gz
		hive 0.9.0版本
	2.安装hive
		tar -zxvf hive-0.9.0.tar.gz
		路径:/usr/hadoop/hive-0.9.0
		编辑/etc/profile
			export HADOOP_HOME=/usr/hadoop/hadoop-1.1.1
			export HIVE_HOME=/usr/hadoop/hive-0.9.0
		source /etc/profile
	3.测试hive
		/usr/hadoop/hive-0.9.0/bin/hive
		如果出现hive>就是成功
		hive> create table auction(nid INT, price INT)
		    > row format delimited
		    > fields terminated by ','
		    > stored as textfile;
		drop table auction;
		Time taken: 0.177 seconds
## 5.安装hbase(好像没用)
	0.官方文档汉化版本
		http://www.yankay.com/wp-content/hbase/book.html
	1.下载habse
		http://mirror.bjtu.edu.cn/apache/hbase/
		wget http://mirror.bjtu.edu.cn/apache/hbase/hbase-0.94.2/hbase-0.94.2.tar.gz
	2.安装habse
		tar -zxvf hbase-0.94.2.tar.gz
		=>vim  conf/hbase-site.xml
	
		<?xml version="1.0"?>
		<?xml-stylesheet type="text/xsl" href="configuration.xsl"?>
			<configuration>
			<property>
				<name>hbase.rootdir</name>
				<value>file:///DIRECTORY/hbase</value>
			</property>
		</configuration> 
		DIRECTORY我改成data/hbase
		启动hbase
		bin/start-hbase.sh
		脚本
		./bin/hbase shell
## 6.安装thrift
	1.首先下载thrift
		wget --no-check-certificate https://dist.apache.org/repos/dist/release/thrift/0.9.0/thrift-0.9.0.tar.gz
		http://thrift.apache.org(版本是 thrift-0.9.0)
	2.解压缩
		tar -zxvf thrift-0.9.0.tar.gz
	3.安装
		依赖:yum install automake make libtool flex bison pkgconfig gcc-c++ boost-devel libevent-devel zlib-devel python-devel ruby-devel
			(不行? configure: error: "Error: libcrypto required.")	
			yum install openssl openssl-devel mysql-devel libmcrypt libmcrypt-devel curl-devel	
		./configure --without-ruby(可能需要安装python,ruby,php) 	
		make 	
		make install
	4.检查
		thrift -version
	5.启动
		/usr/hadoop/hive-0.9.0/bin/hive --service hiveserver
## 7.安装lnmp或者apache
	参考文档:http://lnmp.org/
	一件安装包,就不详细说了
	or
	1.yum安装apache+php+mysql
		yum -y install httpd php mysql mysql-server php-mysql 
	2. 配置开机启动服务 
		/sbin/chkconfig httpd on             [设置apache服务器httpd服务开机启动] 
		/sbin/chkconfig --add mysqld         [在服务清单中添加mysql服务] 
		/sbin/chkconfig mysqld on            [设置mysql服务开机启动] 
		/sbin/service httpd start            [启动httpd服务,与开机启动无关] 
		/sbin/service mysqld start           [启动mysql服务,与开机无关] 
	3.可以顺便安装下x-debug...这样调试更清楚
		wget http://xdebug.org/files/xdebug-2.2.1.tgz
		cp /root/download/xdebug-2.2.1.tgz /usr/local/xdebug
		tar -xzf xdebug-2.2.1.tgz
		rm -rf xdebug-2.2.1.tgz
		cd xdebug-2.2.1/
		/usr/local/php/bin/phpize
		./configure --with-php-config=/usr/local/php/bin/php-config
		make && make install
		安装成功提示 Installing shared extensions:     /usr/local/php/lib/php/extensions/no-debug-non-zts-20090626/
		vim /usr/local/php/etc/php.ini
		最后加上
			[xDebug]
			zend_extension=/usr/local/php/lib/php/extensions/no-debug-non-zts-20090626/xdebug.so
		最后/root/lnmp restart	


### 附录
	https://cwiki.apache.org/confluence/display/Hive/AdminManual+MetastoreAdmin(官方metastore介绍)
	http://www.fuzhijie.me/?p=377(Hive metastore三种存储方式)

		