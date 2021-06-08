<?php

function _Ajax($status=true,$message="Success",$data=array()){
	header("Access-Control-Allow-Origin:*");
	header("Access-Control-Allow-Headers:X-Requested-With"); 
	header('Content-type: application/json');
	exit(json_encode(array(
		's'	=>	$status,
		'm'	=>	$message,
		'd'	=>	$data
	)));
}

/**
* 打印调试信息
* @return
*/
function P($obj,$isEnd=true){
	header("Content-type: text/html; charset=utf-8");
	echo '<pre style="margin:10px; padding:10px; border:1px #ccc solid; background-color:#f9f9f9; font-size:13px;">';
	print_r($obj);
	echo '</pre>';
	if($isEnd) exit();
}

/**
* 无限级分类排序 按级别归类
* @return
*/
function category($data,$pid='0'){
    $arr = array();
    foreach($data as $v){
        if($v['pid'] == $pid){
            $v['child'] = category($data,$v['id']);
            $arr[] = $v;    
        }
    }
    return $arr;
}

/**
* 无限级分类排序 增加页面的输出
* @return
*/
function sortOut($cate,$pid=0,$level=0,$html='--'){
    $tree = array();
    foreach($cate as $v){
        if($v['pid'] == $pid){
            $v['level'] = $level + 1;
            $v['html'] = str_repeat($html, $level);
            $tree[] = $v;
            $tree = array_merge($tree, sortOut($cate,$v['id'],$level+1,$html));
        }
    }
    return $tree;
}


/**
 * 删除目录或者文件
 * @param  string  $path
 * @param  boolean $is_del_dir
 * @return fixed
 */
function del_dir_or_file($path, $is_del_dir = FALSE) {
    $handle = opendir($path);
    if ($handle) { // $path为目录路径 
        while (false !== ($item = readdir($handle))) { 
            if ($item != '.' && $item != '..') {  // 除去..目录和.目录
                if (is_dir("$path/$item")) { 
                    del_dir_or_file("$path/$item", $is_del_dir);   // 递归删除目录
                } else { 
					//echo "del tfile $path/$item<br />";
                    unlink("$path/$item");  // 删除文件
                }
            }
        }
        closedir($handle);
        if ($is_del_dir) { 					
			//echo "del dir $path <br />"; 
            return rmdir($path); // 删除目录
        }
    }else {
        if (file_exists($path)) {		
			//echo "del file $path <br />"; 
            return unlink($path);
        } else {
            return false;
        }
    }
}
