<template>
	<div class="flex-center position-ref full-height">
        <div class="content">
            <div class="title m-b-md center">
                配置参数
            </div>
            <div class="links">
                <div class="row p">
                    <router-link to="/">生成面板</router-link>
                    <!-- <a href="docs.html">文档说明</a> -->
                </div>
                <form action="">
                    <div class="row">
                        <label class="col-2">MySQL数据库连接：</label>
                        <div class="col-7">
                            <input type="text" v-model="conn" placeholder="{h:127.0.0.1,p:3306,u:root,pw:123456,db:xxx}">
                        </div>
                        <div class="col-1 right">
                            <button type="button" v-on:click="conn='{h:127.0.0.1,p:3306,u:root,pw:123456,db:xxx}'">快速填写</button>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-2">默认生成目录：</label>
                        <div class="col-7">
                            <input type="text" v-model="base_make_path" placeholder="d:/www/web/demo/component/">
                        </div>
                    </div>
                   
                    <div class="row">
                        <label class="col-2">模板文件目录：</label>
                        <div class="col-7">
                            <input type="text" v-model="dir_path" placeholder="">
                        </div>
                        <div class="col-1 ">
                            <!-- <input type="file"> -->
                            <!-- <button type="button">查询文件</button> -->
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-2">选择模板文件：</label>
                        <div class="col-7 note" style="font-size: 12px;">
                            模板文件名需以"_"为间隔，以生成文件名+后缀为名 <br>
                            如 table_js.blade.php 模板会生成 table.js 文件
                        </div>
                        <div class="col-1 ">
                            <!-- <button type="button">查询文件</button> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-9">
                        <!-- 
                            <div class="clear">
                                <label>
                                    <input type="checkbox">
                                    table.blade.php
                                </label>
                                <a>查看模板</a>
                            </div> 
                        -->
                            <div class="clear label-cursor" v-for="f in files">
                                <label class="col-5">
                                    <template v-if="checkTemplateFileName(f)">
                                        <input type="checkbox" v-model="choose_files" :value="f">
                                    </template>
                                    <span v-else class="note" style="padding:0 9px 0 6px">x</span>
                                    <span>{{f}}</span>
                                </label>
                                <div class="col-5">
                                    <a @click="query_file_contents(f)">查看模板</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-1">
                            <button type="button" @click="save()" class="pull-right">保存配置</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2">
                            自定义模板变量：<br>
                            <p style="font-family:'雅黑';font-size:12px;" class="note">
                                如需已经配置的变量可以用“[@:xxx]”格式从v（<a @click="showv()">配置结果</a>）中获取，
                                此处变量将赋值在v.other对象处，当生成文件时进行转义
                            </p>
                        </div>
                        <div class="col-7 clear">
                            <textarea rows="5" v-model="vother" style="width:100%"></textarea>
                        </div>
                        <div class="col-1 clear">
                            <button type="button" @click="showv()" class="pull-right">查看配置</button>
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
                conn:"",
                base_make_path:"",
                dir_path:"",
                files:[],   // 模板目录下所有文件
                choose_files:[], // 选择使用的模板文件

                tmp_content:'', //临时显示模板文件内容
                tmp_file:"", 
                tmp_content_isshow: false,

                vother: '{ "example":1, "test":"[@:author]" }'
            }
        },
        methods:{
            /**
             * [template_dir description]
             * 1 api 查询模板目录，目前只读取一个目录由后台指定
             * 2 api 查询某模板目录下的模板文件，目前合并为一个接口
             * @return {[type]} [description]
             */
            template_dir:function(){
                var _this = this;
                axios.get(this.$api.query_template_dir)
                .then(function(resp){
                    _this.dir_path = resp.data.dir_path;
                    _this.files = resp.data.files;
                    _this.$mconfig.template_dir = resp.data.dir_path;
                });
            },

            /**
             * 格式化数据库连接数据，并赋值给mconfig
             * @return {[type]} [description]
             */
            format_mysql_connection:function(){
                var str = this.conn.replace(/[\s|\{|\}]*/g, "");
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


            save:function(){
                // 构建要编译的模板文件，用以生成新文件
                var make_rules = [];
                for (var i = this.choose_files.length - 1; i >= 0; i--) {
                    var temp_file_name = this.choose_files[i];
                    make_rules.push({
                        use_template: this.dir_path + temp_file_name,
                        temp_file_name: temp_file_name,
                        make_path:"", // 这2个路径在预览时创建
                        make_file:"" 
                    });
                }
                this.$mconfig.base_make_path = this.base_make_path;
                this.$mconfig.make_rules = make_rules;
                this.$mconfig.db_conn = this.conn;
                
                this.format_mysql_connection(); // 构建db_变量
                var _this = this;
                axios.get(this.$api.query_tables, {
                    params: this.$mconfig
                })
                .then(function(resp){
                    alert("数据库连接正常，配置成功")
                })
                .catch(function(e){
                    console.log(e)
                    alert("数据库连接错误")
                });
            },


            /**
             * 查看模板文件内容
             * @param  {[type]} file [description]
             * @return {[type]}      [description]
             */
            query_file_contents:function(file){
                this.tmp_file = file;
                var _this = this;
                axios.post(this.$api.query_file_contents, {
                    view_file: file
                })
                .then(function(resp){
                    _this.tmp_content = resp.data;
                    _this.tmp_content_isshow = true;
                })
                .catch(function(e){
                    console.log(e);
                    alert("文件内容查看错误")
                })
            },

            /**
             * 编译自定义变量,并将结果赋值给mconfig
             * @return {[type]} [description]
             */
            compile_custom_vars: function(){
                try{
                    var vo = this.vother;
                    var arr = vo.match(/(\[@:.*?\])/g);
                    // console.log(arr); return ;
                    for (var i = arr.length - 1; i >= 0; i--) {
                        var value = this.$mconfig;
                        var allkeys = arr[i].substring(3, arr[i].length-1) .split(".");  //得到 xxx.xx.x
                        for (var k = 0; k < allkeys.length; k++) {
                            value = value[allkeys[k]];
                        }
                        vo = vo.replace(arr[i], value);
                    }
                    // console.log(vo);
                    // this.$mconfig.other = JSON.parse(vo); // 将结果赋值到mconfig
                    JSON.parse(vo); //仅进行转义测试，并不正式使用
                    this.$mconfig.other_original = this.vother; //若转义无问题则缓存配置，生成文件时再进行转义正式other变量，或后台开发相关功能
                }catch(e){
                    console.log(e);
                    alert("自定义变量JSON格式不严格错误")
                }
            }, 

            /**
             * 展示当前保存的所有的变量
             * @return {[type]} [description]
             */
            showv: function(){
                this.compile_custom_vars(); // 转义变量测试
                
                this.tmp_content = this.$mconfig;
                this.tmp_content_isshow = true;
            },

            /**
             * 检查模板文件名格式
             */
            checkTemplateFileName: function(f){
                return (f.substr(-10) == ".blade.php");
            }

        },
        mounted(){
            // console.log(this.$mconfig);
            // this.$mconfig.template_dir = "d:xxxx"
            this.conn = this.$mconfig.db_conn;
            this.base_make_path = this.$mconfig.base_make_path;
            this.template_dir()
        }
    }
</script>