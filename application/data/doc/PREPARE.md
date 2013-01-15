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

2.安装vim  
	`yum install vim`
	`yum  install openssh-server openssh-clients`

