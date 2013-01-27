#### addOptions  
增加选项  
`public bool GearmanClient::addOptions ( int $options )`  
#### addServer  
添加处理任务的服务器  
`public bool GearmanClient::addServer ([ string $host = 127.0.0.1 [, int $port = 4730 ]] )`   
#### addServers  
添加服务器列表,逗号分割
`public bool GearmanClient::addServers ([ string $servers = 127.0.0.1:4730 ] )
	addServers("10.0.0.1,10.0.0.2:7003");`
#### addTask
添加并行任务
`public GearmanTask GearmanClient::addTask ( string $function_name , string $workload [, mixed &$context [, string $unique ]] )`  
由程序自动执行的已注册函数+被处理的序列化数据+与任务关联的应用程序上下文+用于标识特定任务的唯一性ID
#### addTaskBackground
添加后台运行并行任务
#### addTaskHigh
添加优先并行任务
#### addTaskHighBackground
添加后台优先并行任务	
#### addTaskLow
添加低优先并行任务
#### addTaskLowBackground
添加后台低优先并行任务	
#### addTaskStatus
添加任务并获取执行信息  
`public GearmanTask GearmanClient::addTaskStatus ( string $job_handle [, string &$context ] )`  
#### clearCallbacks
清楚全部回调函数
#### clone
复制GearmanClient
#### context 
获取上下文
#### data
获取参数
#### do
执行单线程任务并立即返回结果  
`public string GearmanClient::do ( string $function_name , string $workload [, string $unique ] )`  
#### doBackground
后台执行单线程任务并立即返回结果
#### doHigh
优先执行单线程任务并立即返回结果
#### doHighBackground 
后台优先执行单线程任务并立即返回结果
#### doNormal
执行单线程任务并立即返回结果
#### doStatus
获取正在运行的任务信息
#### echo
发送信息到job server看是否回调
#### error
返回最后一次的错误信息
#### getErrno 
获取错误信息
#### jobStatus
获取后台运行脚本的状态
#### ping
类似echo
#### removeOptions
移除配置信息
#### returnCode
获取最后一次返回的code
#### runTasks
并行执行任务列表
#### setClientCallback
设置回调
#### setCompleteCallback
设置完成回调
#### setContext
设置上下文
#### setCreatedCallback
设置创建回调
#### setData
设置数据
#### setDataCallback
设置数据回调
#### setTimeout
设置socket I/O 活动超时
#### timeout
检测是否超时