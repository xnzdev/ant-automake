<template>
	<div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md center">
                Laravel-Antd页面生成器
            </div>
            <div class="links">
                <div class="row p">
                    <router-link to="/config">配置面板</router-link> 
                    <router-link to="/format">表转换器</router-link> 
                    <!-- <a href="docs.html">文档说明</a> -->
                </div>
                <form action="">
                    <div class="row">
                        <label for="" class="col-2">当前DB连接：</label>
                        <div class="col-7">
                        	<input type="text" v-model="dbconn" placeholder="no connection" disabled readonly>
                        </div>
                        
                    </div>
                    <div class="row">
                        <label for="" class="col-2">选择表（Model）:</label>
                        <div class="col-7">
                            <select v-model="choose_table_and_comment" @change="choose_table_change()">
                                <option value="" style="color:#ccc">no choose</option>
                                <option v-for="t in tables" :value="t.Name+'#'+t.Comment">
                                    <span>{{t.Name}}</span>
                                    <span>{{t.Comment}}</span>
                                </option>
                            </select>
                        </div>
                        <div class="col-1 right">
                            <button type="button" @click="query_tables()">刷新表</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 label-cursor">
                            <div>选择字段：</div>
                            <div class="clear">
                                <label class="col-4 np">
                                	<input type="checkbox" :checked="checked" @click='checkedAll'>全选
                                </label>
                                <div class="col-3">备注</div>
                                <div class="col-3">模板控件<input type="checkbox" v-model="use_form_comp" @click="use_form_component"></div>
                            </div>
                            <div id="fields">
                            	<div v-for="item in fields">
                            		<label class="col-4 np">
                            			<input type="checkbox" v-model="fields_choosed" :value="item"  />{{item.Field}}
                            		</label>
                                    <span class="font-small col-3" :title="item.Comment">&nbsp;{{item.Comment}}</span>
                            		<span class="font-small col-3" :title="item.Comment" v-show="use_form_comp">
                                        <select v-model="item.Component" class="np">
                                            <option value="Input">Input</option>
                                            <option value="Upload">Upload</option>
                                        </select>
                                    </span>
                    			</div>
                            </div>
                        </div>
                        <div class="col-5">
                            <!-- <div>生成项：</div> -->
                            <div>             
                                <div class="row">
                                    <label>组件名称：</label>
                                    <div>
                                    	<input type="text" v-model="component" @change="update_component_name()" placeholder="component">
                                    </div>
                                </div>
                            </div>
                            <div class="row">生成预览：</div>
                            <div class="clear" v-for="item in mk_rules">
                            	<span>{{item.make_file}}</span>
                            	<a @click="query_file_contents(item)" class="pull-right">预览内容</a>
                                <span class="pull-right mkr-css" :id="item.make_file"></span>
                        	</div>
                            <div class="row"></div>
                            <div id="make-result"></div>
                        </div>
                        <div class="col-1 right">
                            <div class="row" style="margin-bottom:45px;">
                            	<button type="button" @click="preview()">生成预览</button>
                            </div>
                            <div class="row">
                            	<button type="button" @click="make()">生成文件</button>
                            </div>
                            <div class="row">
                                <button type="button" @click="showv()" class="pull-right">查看配置</button>
                            </div>
                            <!-- <div class="row"><button type="button">浏览访问</button></div> -->
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div v-show="tmp_content_isshow" class="file-contents">
            <div class="file-head clear">
                <span>fileName: {{tmp_file}}</span>
                <button type="button" @click="tmp_content_isshow=false" class="pull-right">关闭</button>
            </div>
            <div class="file-body">
                <textarea class="file-textarea">{{tmp_content}}</textarea>
            </div>
        </div>

    </div>
</template>

<script>
	import axios from 'axios'
    export default {
    	data(){
    		return {
    			dbconn:"",
    			tables:[], // 数据库表列表
    			choose_table_and_comment:"",
    			fields:[], // 展示表的字段列表
    			fields_choosed:[], // 选中的字段
    			component:"",
    			mk_rules:[],
    			checked:false, //全选框

    			tmp_content:'', //临时显示生成文件内容
                tmp_file:"", 
                tmp_content_isshow: false,

                use_form_comp:false //是否启用模板控件生成页面
    		}
    	},
    	methods:{

            /**
             * 格式化数据库连接数据，并赋值给mconfig
             * @return {[type]} [description]
             */
            format_mysql_connection:function(){
                var str = this.dbconn.replace(/[\s|\{|\}]*/g, "");
                var arr = str.split(",");
                // {h:127.0.0.1,p:3306,u:root,pw:123456,db:xxx}
                for (var i = arr.length - 1; i >= 0; i--) {
                    var s = arr[i].substr(0,2);
                    if(s == "h:") {
                        this.$mconfig.db_host = arr[i].substr(2);
                    } else if( s == "p:"){
                        this.$mconfig.db_port = arr[i].substr(2);
                    } else if( s == "u:"){
                        this.$mconfig.db_user = arr[i].substr(2);
                    } else if( s == "pw"){
                        this.$mconfig.db_password = arr[i].substr(3);
                    } else if( s == "db"){
                        this.$mconfig.db_database = arr[i].substr(3);
                    } 
                }
            },
    		/**
    		 * 查询数据表
    		 * @return {[type]} [description]
    		 */
    		query_tables:function(){
    			if(this.$mconfig.db_conn != ""){
                    this.format_mysql_connection(); // 构建db_变量
    				var _this = this;
	                axios.get(this.$api.query_tables, {
	                	params: this.$mconfig
	                })
	                .then(function(resp){
	                   _this.tables = resp.data;

	                });
    			}
    		},

    		/**
    		 * 查询表字段
    		 * @return {[type]} [description]
    		 */
    		choose_table_change:function(){
                var tableAndCommentArr = this.choose_table_and_comment.split("#"); 
    			this.$mconfig.table_name = tableAndCommentArr.length==2?tableAndCommentArr[0]:'';
                this.$mconfig.table_comment = tableAndCommentArr.length==2?tableAndCommentArr[1]:'';
    			this.component = this.$mconfig.table_name;
    			this.$mconfig.model_name = this.$mconfig.table_name;

    			var _this = this;
    			axios.get(this.$api.query_fields, {
    				params: this.$mconfig
    			})
    			.then(function(resp){
    				_this.fields = resp.data;
    			})
    			.catch(function(e){
    				console.log(e);
    				alert(e.response.data.message);
    			});
    		},

    		

    		checkedAll:function(){ 
    			if(this.checked){
    				this.checked = false;
    				this.fields_choosed = [];
    			}else{
    				this.checked = true;
    				this.fields_choosed = this.fields;
    			}
    		},

    		/**
    		 * 当要求组件名称与表名不相同是使用此处定义的名称作为最终组件名
    		 * @return {[type]} [description]
    		 */
    		update_component_name:function(){
    			this.$mconfig.model_name = this.component;
    		},

            /**
             * 是否使用模板的控件，默认都是input组件
             * 判断 use_form_comp 变量
             * @return {[type]} [description]
             */
            use_form_component:function(){
                if(this.use_form_comp && this.fields_choosed.length > 0){
                    // 当不启用模板控件是，初始化已经选择的字段数据的控件类型为input
                    for (var i = 0; i < this.fields_choosed.length; i++) {
                        this.fields_choosed[i].Component = "Input";
                    }
                }
            },


    		

    		/**
    		 * 查看模板文件内容
    		 * @param  {object} file [description]
    		 * @param  {string} make false | d:/www/demo/components/mm/mm_list.js
    		 * @return {[type]}      [description]
    		 */
    		query_file_contents:function(file, make){
    			this.tmp_file = "临时内容： " + file.make_file ;
                var _this = this;
                axios.post(this.$api.query_file_contents, {
                    make_item: file,
                    compiled: true,
                    vars: this.$mconfig,
                    make: make?make:false
                })
                .then(function(resp){
                    if(!make){ //当生成文件时不需展示
                    	_this.tmp_content = resp.data;
                    	_this.tmp_content_isshow = true;
                    }else{
                        var ele = document.getElementById(file.make_file);
                        // console.log(resp);
                        if(resp.data.mkr == false){
                            ele.innerHTML = "未成功"
                            ele.style.color = "#ff0000"
                        }else{
                            ele.innerHTML = "已生成"
                            ele.style.color = "rgb(43, 185, 3)"
                        }
                    }
                })
                .catch(function(e){
                    console.log(e);
                    alert(e.response.data.message);
                })
    		},

    		/**
    		 * 预览
    		 */
    		preview:function(){
    			// console.log(this.fields_choosed);
    			this.$mconfig.fields_arr = this.fields_choosed;
                var _this = this;      
                // 预览时根据表model，字段，模板文件名，验证生成规则进行生成文件确认
                axios.post(this.$api.make_rules, {
                    vars: this.$mconfig,
                })
                .then(function(resp){
                    _this.mk_rules = resp.data;
                    if(_this.mk_rules.length == 0){
                        alert("你可能未选择模板文件，无任何生成")
                    }else{
                        _this.$mconfig.make_rules = _this.mk_rules;
                    }
                })
                .catch(function(e){
                    console.log(e);
                    alert(e.response.data.message);
                })
    		},

    		/**
    		 * 生成文件
    		 */
    		make: function(){
    			if(this.mk_rules.length == 0){
    				alert("请执行“生成预览”后再生成文件")
    			}
    			for (var i = this.mk_rules.length - 1; i >= 0; i--) {
    				var mf = this.mk_rules[i];
    				this.query_file_contents(mf, true);
    			}
    		},

            /**
             * 展示当前保存的所有的变量
             * @return {[type]} [description]
             */
            showv: function(){
                // this.compile_custom_vars(); // 转义变量测试
                this.tmp_content = this.$mconfig;
                this.tmp_content_isshow = true;
            },
    	},
        mounted(){
            // console.log("builder", this.$mconfig);
            this.dbconn = this.$mconfig.db_conn;
            this.query_tables();
        }
    }
</script>
