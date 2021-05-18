<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;


 class KeshiController extends CommonController{
 	 
	
	function index(){    
		$Dis = M('Keshi');
		$list = $Dis->order('id asc')->select(); 
		$data = category($list,0); 
		$this->assign('list',$data);// 赋值数据集   
		$this->display();
	}
	
	
	function getinfo(){
		$id = I('get.id');
		$Dis = M('Keshi');
		$info = $Dis->find($id); 
		_A(true,'Success',$info);
		
	}
	
	
	function save(){
		$id = I('post.id');
		$pid = I('post.pid');
		$Dis = M('Keshi');
		if(empty($id)){
			$data = array(
				'info'=>I('post.info'),
				'pid'=>I('post.pid'),
				'title'=>I('post.title'),
			);
			$Dis->data($data)->add();
		}else{
			$data = array(
				'info'=>I('post.info'), 
				'title'=>I('post.title'),
			);
			$Dis->where(array('id'=>$id))->save($data);
		}
		_A(true,'保存成功','');
	}
	
	
	function delete(){
		$id = I('get.id'); 
		$Dis = M('Keshi');
		$Dis->delete($id);
		_A(true,'删除完成','');
	}
 }





