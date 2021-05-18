<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;


 class CategoryController extends CommonController{
 	
 	private $pageSize = 20;
	
	function index(){    
		$Dis = M('Category');
		$list = $Dis->order('id asc')->select(); 
		$data = category($list,0); 
		$this->assign('list',$data);// 赋值数据集   
		$this->display();
	}
	
	
	function getinfo(){
		$id = I('get.id');
		$Dis = M('Category');
		$info = $Dis->find($id); 
		_A(true,'Success',$info);
		
	}
	
	
	function save(){
		$id = I('post.id');
		$pid = I('post.pid');
		$Dis = M('Category');
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
		$Dis = M('Category');
		$Dis->delete($id);
		_A(true,'删除完成','');
	}
 }



