1.准备两台虚拟机,一台主机(Masters-Hadoop:192.168.1.59),一台从机(Slaves-Hadoop:192.168.1.58).  	
	桥接网络,设置ip:  
	
- `/etc/sysconfig/network-scripts/ifcfg-eth0`

	DEVICE=eth0  
	BOOTPROTO=static  
	TYPE=Ethernet  
	BROADCAST=192.168.1.255  
	IPADDR=192.168.1.59(58) #静态IP地址  
	NETMASK=255.255.255.0 #子网掩码  
	GATEWAY=192.168.1.1 #网关  
	ONBOOT=yes  

- `/etc/resolv.conf`

	nameserver 8.8.8.8  

然后重启网络,service network restart,如果无效,那就reboot...

2.更换源  
`cd /etc/yum.repos.d  
wget http://mirrors.163.com/.help/CentOS6-Base-163.repo   
mv CentOS6-Base-163.repo CentOS-Base.repo  
yum update`  
3.安装vim  
	`yum install vim`
	`yum  install openssh-server openssh-clients`  
	基本上都是需要的
	`yum install vim  openssh-server openssh-clients automake make libtool flex bison pkgconfig gcc-c++ boost-devel libevent-devel zlib-devel python-devel ruby-devel openssl openssl-devel mysql-devel libmcrypt libmcrypt-devel curl-devel`
4.配置虚拟主机 ci的rewrite重新设置
`if (-f $request_filename/index.php) {
	rewrite (.*) $1/index.php;
}
if (!-f $request_filename) {
	rewrite (.*) /index.php;
}`
5.如果报错不显示,显示500错误,需要打开报错
	php.ini 两处
`	error_reporting = E_ALL & ~E_NOTICE  只是报出error，notiec过滤掉
	display_errors = On    on是开启报错，off关闭	`

