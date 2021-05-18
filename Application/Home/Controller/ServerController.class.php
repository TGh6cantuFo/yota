<?php

namespace Home\Controller;
use Think\Controller\JsonRpcController;

class ServerController extends JsonRpcController{

	public function index(){
		return 'Hello, JsonRPC!';
	}

	// 支持参数传入
	public function test($id=0){
		$where = array();
		if($id>0){
			$where['id'] = $id;
		}
		$list = M('Node')->where($where)->select();
		return my_json_encode($list);
	}

}