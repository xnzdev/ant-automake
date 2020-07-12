<?php

Route::get('/xnzdev/index.html', '\XnzDev\AntAutoMake\Controllers\AutoMakeController@ant'); // 首页

// view-source:127.0.0.1:8085
//
// 1.设定模板
// 2.查询表即设定model
// 3.选择字段
// 4.解析模板生成页面文件
// 说明：
// 		模板文件名说明（不同文件定义不同）
// 		Antd模板文件未大写驼峰命名，“_”表示文件夹分割，而最后一个表示后缀分割
// 		例如： 文件“components_CreateForm_jsx”表示生成 “components/CreateForm.jsx”文件
// \XnzDev\AntAutoMake\Controllers\AntReactController antd pro 的CURD模板
Route::get('/xnzdev/antd/test/index', '\XnzDev\AntAutoMake\Controllers\AntReactController@testIndex');
Route::get('/xnzdev/antd/query/template_dir', '\XnzDev\AntAutoMake\Controllers\AntReactController@query_template_dir');
Route::get('/xnzdev/antd/query/tables', '\XnzDev\AntAutoMake\Controllers\AntReactController@tables');
Route::get('/xnzdev/antd/query/fields', '\XnzDev\AntAutoMake\Controllers\AntReactController@fields');
Route::post('/xnzdev/antd/make_rules', '\XnzDev\AntAutoMake\Controllers\AntReactController@useTemplateFileSetMakeFileRules');
Route::post('/xnzdev/antd/query/file_contents', '\XnzDev\AntAutoMake\Controllers\AntReactController@query_file_contents');
