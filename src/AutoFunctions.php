<?php

/**
 * [fieldsFormLabel 根据表字段注释获取表单label内容（或表格头部）]
 * @param  {[type]} $comment [图文内容：文章的图文详情]
 * @return {[type]}          [返回“图文内容”，若未找到中文：冒号返回原字符串]
 */
function fieldsFormLabel($comment, $fenGeFu = "："){
	$arr = explode($fenGeFu, $comment); //此处是默认中文冒号
	if(count($arr)>=1){
		return $arr[0];
	}
	if(empty($comment)){
		return "Null注释";
	}
	return $comment;
}



/**
 * 获取字段中的主键key，如果没有主键返回第一个字段
 */
function primaryKeyOfFields($fields){
	foreach($fields as $item){
		if($item['Key'] == 'PRI'){
			return $item['Field'];
		}
	}
	if(count($fields) > 0){
		return $fields[0]['Field'];
	}
	return 'id'; 
}


/**
 *	mysql数据类型转typescript数据类型
 */
function dbType2Ts($db_type){
	$arr = explode('(', $db_type);
	if(in_array($arr[0], ['int', 'tinyint'])){
		return 'number';
	}

	if(in_array($arr[0], ['varchar', 'char', 'text', 'longtext'])){
		return 'string';
	}

	if(in_array($arr[0], ['time', 'date', 'timestamp'])){
		return 'Date';
	}
}