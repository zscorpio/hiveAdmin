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