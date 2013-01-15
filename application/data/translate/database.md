## 新建数据库  
	CREATE (DATABASE|SCHEMA) [IF NOT EXISTS] database_name
	[COMMENT database_comment]
	[LOCATION hdfs_path]
	[WITH DBPROPERTIES (property_name=property_value, ...)];
## 删除数据库
	DROP (DATABASE|SCHEMA) [IF EXISTS] database_name [RESTRICT|CASCADE];
## 查看全部数据库
	SHOW (DATABASES|SCHEMAS) [LIKE identifier_with_wildcards];(后面可以正则来查询) ==>我能说结果是一样的吗....
## 描述数据库
	DESCRIBE DATABASE(0.7release才实现)