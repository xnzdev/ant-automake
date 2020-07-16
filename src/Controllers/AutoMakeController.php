<?php


namespace XnzDev\AntAutoMake\Controllers;


use App\Http\Controllers\Controller;

/**
 * @group Class AutoMakeController
 * @package XnzDev\AntAutoMake\Controllers
 */
class AutoMakeController extends Controller
{
    public function ant(){
        $viewPath = "xnz_ant::ant";
        return response()->view($viewPath);
    }
}
