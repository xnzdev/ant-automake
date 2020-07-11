<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Connection;

class ModelVueController extends Controller
{
    function query_template_dir(){
    	$path = resource_path("views/tempv1") . "/";
    	$path = str_replace("\\", "/", $path);
    	$files = scandir($path);
    	$fr = [];
    	foreach ($files as $v) {
    		if(!is_dir($v)){
    			array_push($fr, $v);
    		}
    	}
    	return response()->json(["dir_path" => $path, "files"=>$fr]);
    }

    private function dbconn(){
    	$servername = "localhost";//request('db_host');
    	$dbname = request('db_database');
    	$username = request('db_user');
    	$password = request('db_password');
		$conn = new \PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
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
        $viewPath = request("view_path");
        $vars = request('vars');
        $make = request('make');

        if(empty($viewPath)){
            return "错误，模板文件不存在";
        }
        $v = view($viewPath);
        if($compiled){
            $vars = json_decode($vars, true);
            $v->with('vars', $vars);
            $d = $v->getEngine()->get($v->getPath(), $v->gatherData());
            if($make && $make != 'false'){
                if (!is_dir($p)) {
                    mkdir(iconv("UTF-8", "GBK", $p), 0777,true);
                }
                return file_put_contents($make, $d);
            }
            return $d;
        }else{
            $path = $v->getPath();
            return response(file_get_contents($path))->withHeaders([
                'Access-Control-Allow-Origin'=>'*']);
        }
    	
    }
}
