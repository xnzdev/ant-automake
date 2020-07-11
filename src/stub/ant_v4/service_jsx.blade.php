<?php
  $apiName = 'webcms/'.str_replace("_", "", $vars['model_name']);
  // $apiName = $vars['model_name'];
?>

import request from '@/utils/request';

@if(false)
/**
 * 请求接口数据示例
 * @return {object}
 * {
    'status'  : 'ok',     //通过‘ok’ 字符串判断接口避免ajax出现数据类型错误。 枚举ok, error
    'code' : 200, 
    'message'     : 'success',
    'result'    : {
      "list" => $list,
      "pagination" => [
          "total" => $total,            // 总的数据条数
          "current" => $current_page,   // 当前页
          "pageSize" => $page_size,     // 每页的条数
      ]
    }
  } 
 */

/**
 * GII api 接口
 */
// Route::any('/response_api/{{$apiName}}/list',          'Web\XXX@getList');
// Route::any('/response_api/{{$apiName}}/detail',        'Web\XXX@getDetail');
// Route::any('/response_api/{{$apiName}}/save',          'Web\XXX@save');
// Route::any('/response_api/{{$apiName}}/delete',        'Web\XXX@delete');
// Route::any('/response_api/{{$apiName}}/batch_delete',  'Web\XXX@batchDelete');
@endif
export async function queryGII(params) {
  let resp = await request('/api/{{$apiName}}/list', {
    method: 'POST',
    data: {
      ...params,
      page: params.current,
    },
  });
  return {
    data: resp.result.data,
    success: true,
    page: resp.result.per_page,
    total: resp.result.total,
  };
}
export async function removeGII(params) {
  return request('/api/{{$apiName}}/delete', {
    method: 'POST',
    data: { ...params, method: 'delete' },
  });
}
export async function addGII(params) {
  return request('/api/{{$apiName}}/save', {
    method: 'POST',
    data: { ...params, method: 'post' },
  });
}
export async function updateGII(params) {
  return request('/api/{{$apiName}}/save', {
    method: 'POST',
    data: { ...params, method: 'update' },
  });
}