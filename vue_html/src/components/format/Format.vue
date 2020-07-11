<template>
	<div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md center">
                数据格式化转换
            </div>
            <div class="links">
                <div class="row p">
                    <router-link to="/">生成面板</router-link> 
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
                        <div class="col-3 label-cursor">
                            <div>选择字段：</div>
                            <div class="clear">
                                <label class="col-5 np">
                                	<input type="checkbox" :checked="checked" @click='checkedAll'>全选
                                </label>
                                <div class="col-5">备注</div>
                            </div>
                            <div id="fields">
                            	<div v-for="item in fields">
                            		<label>
                            			<input type="checkbox" v-model="fields_choosed" :value="item"  />{{item.Field}}
                            		</label>
                            		<span :title="item.Comment">{{item.Comment}}</span>
                    			</div>
                            </div>
                        </div>
                        <div class="col-5">
                            <!-- <div>生成项：</div> -->
                            <textarea class="file-textarea" v-model="tmp_content"></textarea>
                        </div>
                        <div class="col-2 right">
                            <div class="row"><button type="button" @click="format_table_data('php_array')">表2PHP数组</button></div>
                            <div class="row"><button type="button" @click="format_table_data('json')">表2JSON格式</button></div>
                            <div class="row"><button type="button" @click="format_clear()">清空</button></div>
                            <div class="row"><button type="button" @click="format_line()">一行格式</button></div>
                            <div class="row"><button type="button" @click="format_json()">JSON格式化</button></div>
                            <!-- <div class="row"><button type="button">浏览访问</button></div> -->
                        </div>
                    </div>
                </form>
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

    			tmp_content:'', //临时显示生成文件内容//
               
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

    		
            field_length_max:function(){
                var length = 0;
                var zds = this.fields_choosed.length>0?this.fields_choosed:this.fields;
                for(var i in zds){
                    var item = zds[i];
                    if(item.Field.length > length){
                        length = item.Field.length;
                    }
                }
                return length;
            },

            // Format 格式方法
            /**
             * [format_table_data 将表字段生成需要的格式]
             * @param  {[type]} fstr 枚举 json、php_array 默认json格式
             * @return {[type]}      [展示在tmp_content]
             */
            format_table_data:function(fstr){ 
                var fengefu = ":";
                var kuohao = '{}'; // 通过kuohao.charAt(0) 使用
                if(fstr == "php_array"){
                    fengefu = "=>";
                    kuohao = "[]";
                }

                var content_arr = [];
                var max = this.field_length_max();
                content_arr.push(kuohao.charAt(0));
                var zds = this.fields_choosed.length>0?this.fields_choosed:this.fields;
                for(var i in zds){
                    var item = zds[i];
                    // content_arr.push('    "'+item.Field+'"'+ " ".repeat(max - item.Field.length) +' => "",');
                    content_arr.push('    "'+item.Field+'"'+ " ".repeat(max - item.Field.length) +' '+fengefu+' "",');
                }
                content_arr.push(kuohao.charAt(1));
                this.tmp_content = content_arr.join("\n");

            },
            format_line:function(){
                console.log(this.tmp_content)
                this.tmp_content = this.tmp_content.replace(/\s/g, "");
            },
            format_clear:function(){
                this.tmp_content = '';
            },
            format_json:function(){
                var json_c = this.tmp_content;
                try{
                    var obj = JSON.parse(json_c);
                    console.log(obj);
                    var fengefu = ":";
                    var kuohao = '{}'; // 通过kuohao.charAt(0) 使用
                    var content_arr = [];
                    content_arr.push(kuohao.charAt(0));
                    var i = 0;
                    var ks = Object.keys(obj).length;
                    for(var k in obj){
                        i++;
                        if(i < ks){
                            content_arr.push('    "'+k+'"'+ ' '+fengefu+' "'+obj[k]+'",');    
                        }else{
                            content_arr.push('    "'+k+'"'+ ' '+fengefu+' "'+obj[k]+'"'); //最后一行不能有，号
                        }
                        
                    }
                    content_arr.push(kuohao.charAt(1));
                    this.tmp_content = content_arr.join("\n");
                }catch(e){
                    console.error(e);
                    alert("数据有误，转JSON格式出错\n" + e);
                }
            }
    		

    	},
        mounted(){
            // console.log("builder", this.$mconfig);
            this.dbconn = this.$mconfig.db_conn;
            this.query_tables();
        }
    }
</script>
