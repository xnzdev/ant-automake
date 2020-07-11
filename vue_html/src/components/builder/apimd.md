<!-- api.md -->
# 接口说明


1. 读取模板目录，并模板文件列表

```
	/query/template_dir 

	response:
	{
		dir_path:"", // 模板目录绝对路径
		files:[]	 // 文件数组
	}
```

2. 数据库连接测试/数据表查询

```
	/query/tables?config // 参数读取config中数据库db_xxx相关参数
	
	response:
	{
		// 数据库表数据列表 show tables
	}

```

3. 查询表中字段信息

```
	/query/fields?config // 参数读取config中table_name参数
	
	response:
	{
		// 表字段数据 show full fields from table_name 
	}

```

