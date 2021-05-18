<?php


namespace Think\Template\Taglib;
use Think\Template\TagLib;

Class Test extends TagLib{
	
	protected $tags   =  array(  // 定义标签 
		'input'    =>    array('attr'=>'type,name,id,value','close'=>0), // input标签 
		'textarea' =>    array('attr'=>'name,id'),
		'pager'		=>	array('attr'=>'count,curpage','close'=>0), // input标签
	);
	
	// input标签解析
	public function _input($tag,$content)   {    
		$name   =   $tag['name'];    
		$id    =    $tag['id'];    
		$type   =   $tag['type'];    
		$value   =   $this->autoBuildVar($tag['value']);    
		$str = "<input type='".$type."' id='".$id."' name='".$name."' value='".$value."' />";    
		return $str;
	}
	
	// textarea标签解析
	public function _textarea($tag,$content)   {    
		$name  =   $tag['name'];    
		$id    =   $tag['id'];    
		$str   =   '<textarea id="'.$id.'" name="'.$name.'">'.$content.'</textarea>';    
		return $str;
	}
	
	
	// textarea标签解析
	public function _pager($tag,$content)   {  
		$count  =   $tag['count']; 
		$cur  =   $tag['curpage']; 
		$html = '';
		for($i = 1; $i <= $count; $i++){ 
			if($i==$cur){
				$html .= '<li class="disabled"><a>' . $i . '</a></li>';
			}else{
				$html .= '<li><a>' . $i . '</a></li>';
			}
		}   
		$html = '<ul class="pagination pagination-sm">'.$html.'</ul>';   
		return $html;
	}
	
	
}