<?php


namespace XnzDev\AntAutoMake\Controllers;


use App\Http\Controllers\Controller;

class AutoMakeController extends Controller
{
    public function ant(){
        $viewPath = "xnz_ant::ant";
        return response()->view($viewPath);
    }
}
