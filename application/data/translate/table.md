from https://cwiki.apache.org/confluence/display/Hive/LanguageManual
## 新建表
	CREATE [EXTERNAL] TABLE [IF NOT EXISTS] [db_name.]table_name
		[(col_name data_type [COMMENT col_comment], ...)]
		[COMMENT table_comment]
		[PARTITIONED BY (col_name data_type [COMMENT col_comment], ...)]
		[CLUSTERED BY (col_name, col_name, ...) [SORTED BY (col_name [ASC|DESC], ...)] INTO num_buckets BUCKETS]
		[SKEWED BY (col_name, col_name, ...) ON ([(col_value, col_value, ...), ...|col_value, col_value, ...]) (Note: only available starting with 0.10.0)]
		[
		[ROW FORMAT row_format] [STORED AS file_format]
		| STORED BY 'storage.handler.class.name' [WITH SERDEPROPERTIES (...)]  (Note: only available starting with 0.6.0)
		]
		[LOCATION hdfs_path]
		[TBLPROPERTIES (property_name=property_value, ...)]  (Note: only available starting with 0.6.0)
		[AS select_statement]  (Note: this feature is only available starting with 0.5.0, and is not supported when creating external tables.)

	CREATE [EXTERNAL] TABLE [IF NOT EXISTS] [db_name.]table_name
		LIKE existing_table_or_view_name
		[LOCATION hdfs_path]

	data_type
		: primitive_type
		| array_type
		| map_type
		| struct_type
		| union_type (Note: Only available starting with Hive 0.7.0) - needs documentation

	primitive_type
		: TINYINT
		| SMALLINT
		| INT
		| BIGINT
		| BOOLEAN
		| FLOAT
		| DOUBLE
		| STRING
		| BINARY (Note: Only available starting with Hive 0.8.0)
		| TIMESTAMP (Note: Only available starting with Hive 0.8.0)

	array_type
		: ARRAY < data_type >

	map_type
		: MAP < primitive_type, data_type >

	struct_type
		: STRUCT < col_name : data_type [COMMENT col_comment], ...>

	union_type
		: UNIONTYPE < data_type, data_type, ... >

	row_format
		: DELIMITED [FIELDS TERMINATED BY char] [COLLECTION ITEMS TERMINATED BY char]
		[MAP KEYS TERMINATED BY char] [LINES TERMINATED BY char]
		| SERDE serde_name [WITH SERDEPROPERTIES (property_name=property_value, property_name=property_value, ...)]

	file_format:
		: SEQUENCEFILE
		| TEXTFILE
		| RCFILE     (Note: only available starting with 0.6.0)
		| INPUTFORMAT input_format_classname OUTPUTFORMAT output_format_classname

(下面内容基本上来自网络...)

用已知表名创建一个表的时候,如果同名表已经存在则会报一个表已存在的异常,不过你可以使用IF NOT EXISTS来防止这个错误.

EXTERNAL 关键字可以让用户创建一个外部表,在建表的同时指定一个指向实际数据的路径（LOCATION）,Hive 创建内部表时,会将数据移动到数据仓库指向的路径；若创建外部表,仅记录数据所在的路径,不对数据的位置做任何改变.在删除表的时候,内部表的元数据和数据会被一起删除,而外部表只删除元数据,不删除数据.

有分区的表可以在创建的时候使用 PARTITIONED BY 语句。一个表可以拥有一个或者多个分区，每一个分区单独存在一个目录下。而且，表和分区都可以对某个列进行 CLUSTERED BY 操作，将若干个列放入一个桶（bucket）中。也可以利用SORT BY 对数据进行排序。这样可以为特定应用提高性能。

like仅复制表结构，但是不复制数据 

Hive 只支持等值连接（equality joins）、外连接（outer joins）和（left semi joins）。Hive 不支持所有非等值的连接，因为非等值连接非常难转化到 map/reduce 任务。另外，Hive 支持多于 2 个表的连接。 

允许的等值连接 
SELECT a.* FROM a JOIN b ON (a.id = b.id)   
SELECT a.* FROM a JOIN b ON (a.id = b.id AND a.department = b.department)  

join 时，每次 map/reduce 任务的逻辑是这样的：reducer 会缓存 join 序列中除了最后一个表的所有表的记录，再通过最后一个表将结果序列化到文件系统。这一实现有助于在 reduce 端减少内存的使用量。实践中，应该把最大的那个表写在最后（否则会因为缓存浪费大量内存）。例如： 

keytool -genkey -alias [alias_name] -keyalg RSA -keysize 2048 -keystore [keystore_name]