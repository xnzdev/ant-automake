/**
 * config 将是模板解析的全部变量，
 * 项目的所有变量将由前台提供，所以本项目为开发者工具，不可应用在正式项目中，安全问题本项目开发者概不负责
 * @type {Object}
 */
let config = {
	"author":"JIM",
	"template_dir":"", //模板文件所在目录,基于laravel的views目录 
	// 数据库配置 {h:127.0.0.1,p:3306,u:root,pw:123456,db:xxx}
	"db_conn":"", // 未格式化数据{h:127.0.0.1,p:3306,u:root,pw:123456,db:xxx}
	"db_host":"",
	"db_user":"",
	"db_port":"",
	"db_password":"",
	"db_database":"",

	"table_name":"", // 选择的表名
	"table_comment":"",// 选择的表备注
	"fields_arr":[
		// {
		// 	"Field": "id",
		// 	"Type": "bigint(20) unsigned",
		// 	"Collation": null,
		// 	"Null": "NO",
		// 	"Key": "PRI",
		// 	"Default": null,
		// 	"Extra": "auto_increment",
		// 	"Privileges": "select,insert,update,references",
		// 	"Comment": "主键ID",
		// 	"Component": "Input" // 模板控件类型 
		// },
	], // 最终确定的字段，
	"model_name" : "", // 要生成的组件名称，也是组件文件名的构成要素

	"base_make_path" : "", // 默认生成路径，当没有给模板指定各自的页面生成目录时使用
	// 生成规则配置: 数组内的每一项都是要生成的文件
	"make_rules":[
		// {
		// 	"use_template":"", // 模板的绝对路径、不同后台接口是否采用绝对路径自行决定
		// 	"temp_file_name":"",
		// 	"make_path":"", // 页面生成路径，它根据model_name 和模板文件名生成make_file,{make_path/model_name/mode_name+temp_file_name}
		// 	"make_file":"", // 解析模板中的变量，生成该文件
		// }
		// ...
	], 
	"preview_url":"", // 预览地址
	"other_original":"", // 其它变量的原始变量[@:author]
	"other":{}, //其它变量（由other_original原始变量转义而来，编译了其中的变量），预订使用
};

if(typeof VVV_InitConfig == 'object'){
	Object.assign(config, VVV_InitConfig);
}
export default config;