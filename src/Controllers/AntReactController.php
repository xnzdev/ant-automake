<?php

namespace XnzDev\AntAutoMake\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;

class AntReactController extends Controller
{
    //
    public function testIndex(){
        return response()->json(
            ['test_response'=>"Welcome AntReactController"]
        )->withHeaders([
            "Access-Control-Allow-Origin" =>"*"
        ]);
    }


    /**
     * 查询模板文件，只展示目录下的文件，不展示子目录文件，
     * 如果有子目录，需将模板放在模板目录下，
     * 然后到生成文件方法设置生成规则（找AutoMakeController 中配置生成路径方法）
     * @return [type] [description]
     */
    function query_template_dir(){
        $temp_dir = request('template_dir', '');
        if(!empty($temp_dir)){
            $path = resource_path("views/$temp_dir") . "/";
            $path = str_replace("\\", "/", $path);
        }else{
            $path = __DIR__."/../stub/ant_v4";
        }
        // 配置模板目录
        $files = scandir($path);
        $fr = [];
        foreach ($files as $v) {
            if(!is_dir($path . $v) && ! ($v == '.' || $v == "..")){
                array_push($fr, $v);
            }
        }
        return response()->json(["dir_path" => $path, "files"=>$fr]);
    }

    private function dbconn(){
        $servername = request('db_host'); //localhost;
        $dbname = request('db_database');
        $username = request('db_user');
        $password = request('db_password');
        $port = request('db_port');
        // echo "mysql:host=$servername;port=$port;dbname=$dbname";

        $conn = new \PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
        $con = new Connection($conn);
        return $con;
    }

    function tables(){
        $con = $this->dbconn();
        $r = $con->select("show table status");
        return response()->json($r);
    }

    function fields(){
        $con = $this->dbconn();
        $r = $con->select("show full fields from ". request("table_name"));
        return response()->json($r);
    }


    function query_file_contents(){
        $compiled = request("compiled"); //是否返回编译后数据
        $make_item = request("make_item");
        $vars = request('vars');
        $make = request('make');

        $view_file = request('view_file'); // 如果有值，则是Config.vue中查看模板内容
        $viewPath = '';
        if($view_file){
            $viewPath = 'ant::/'.str_replace('.blade.php', '', $view_file);
        }else if($make_item){
            $viewPath = 'ant::/'.str_replace('.blade.php', '', $make_item['temp_file_name']);
        }

        if(empty($viewPath)){
            return "错误，模板文件不存在";
        }
        $v = view($viewPath);
        if($compiled){ // 如果不编译则是查看预览内容，编译时生成文件
            $v->with('vars', $vars);
            $d = $v->getEngine()->get($v->getPath(), $v->gatherData());
            $p = $make_item['make_path'];
            if($make && $make !== 'false'){
                if (!is_dir(dirname($p))) {
                    mkdir(iconv("UTF-8", "GBK", dirname($p)), 0777,true);
                }
                $mkr = file_put_contents($p, $d);
                $make_item['mkr'] = $mkr;
                return response()->json($make_item);
            }
            return response($d)->withHeaders([
                'Access-Control-Allow-Origin'=>'*']);
        }else{
            $path = $v->getPath();
            return response(file_get_contents($path))->withHeaders([
                'Access-Control-Allow-Origin'=>'*']);
        }

    }



    /**
     * 1.查询表字段
     * 2.根据模板生成页面，复制页面相关文件
     * 3.生成模拟数据文件
     *
     */

    /**
     * 说明：
     * 模板文件名说明（不同文件定义不同）
     *     Antd模板文件未大写驼峰命名，“_”表示文件夹分割，而最后一个表示后缀分割
     *     例如： 文件“components_CreateForm_jsx.blade.php”表示生成 “components/CreateForm.jsx”文件
     * "make_rules":[
    {
    "use_template":"", // 模板的绝对路径、不同后台接口是否采用绝对路径自行决定
    "temp_file_name":"",
    "make_path":"", // 页面生成路径，它根据model_name 和模板文件名生成make_file,{make_path/model_name/mode_name+temp_file_name}
    "make_file":"", // 解析模板中的变量，生成该文件
    }
    ...
    ],
     */
    function useTemplateFileSetMakeFileRules(){
        $vars = request('vars');
        $new_rules = [];
        foreach ($vars['make_rules'] as $item) {
            $tfn_arr = explode('_', str_replace('.blade.php', '', $item['temp_file_name']));
            $hz = $tfn_arr[count($tfn_arr)-1];
            array_pop($tfn_arr);
            $newfn = implode("/", $tfn_arr) . '.'. $hz;
            $item['make_file'] = $newfn;
            $item['make_path'] = $vars['base_make_path'].$vars['model_name'].'/'.$newfn;
            array_push($new_rules, $item);
        }

        return response()->json($new_rules);
    }
}
