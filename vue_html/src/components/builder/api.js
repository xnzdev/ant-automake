/**
 * server api 服务端接口文件
 * 与 api.md 接口文档说明对应
 */

export default {
	/**
	 * 1. /query/template_dir 
	 * response:
	 * {
	 * 	dir_path:"", // 模板目录绝对路径
	 * 	files:[]	 // 文件数组
	 * }
	 */
	query_template_dir: "/xnzdev/antd/query/template_dir",

	/**
	 * 2. /query/tables?config // 参数读取config中数据库db_xxx相关参数
	 * response:
	 * [
	 *     {
	 *     		Name:table_name,
	 *     		Comment: xxx
	 *     }			
	 *	// 数据库表数据列表 show tables / show table status 
	 * ]
	 */
	query_tables: "/xnzdev/antd/query/tables",



	/**
	 * 3. /query/fields?config // 参数读取config中table_name参数
	 * response:
	 * [{
	 * 	"Field":"id",
	 * 	"Type":"bigint(20) unsigned",
	 * 	"Collation":null,
	 * 	"Null":"NO",
	 * 	"Key":"PRI",
	 * 	"Default":null,
	 * 	"Extra":"auto_increment",
	 * 	"Privileges":"select,insert,update,references",
	 * 	"Comment":""
	 * }, ...]
	 * // 表字段数据 show full fields from table_name 
	 */
	query_fields: "/xnzdev/antd/query/fields",


	/**
	 * 查询模板文件内容/ 预览接口/ 生产接口
	 * 4. /query/file_contents?
	 * @param {string,必填}  view_path 参数为一个视图文件的路径（目前为laravel视图路径：view('temp.table')）
	 * @param {boolean} compiled  是否编译过得模板 true or false
	 * @param {object}  vars 	  编译模板的全部返回变量 vars对象/ 或 使用make_rules的子项，获取生成文件路径
	 * @param {boolean} make 	  是否直接生成文件，默认为false，或要生成的文件绝对路径
	 * 响应文件内容
	 * @type {[type]}
	 */
	query_file_contents: "/xnzdev/antd/query/file_contents",
	make_rules: "/xnzdev/antd/make_rules",
} 
