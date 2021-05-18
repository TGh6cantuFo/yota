<?php
namespace Common\Controller;
use Think\Controller;
	
class BaseController extends Controller{
	function _initialize(){
		header("Content-type: text/html; charset=utf-8"); 		
	}
}

?>