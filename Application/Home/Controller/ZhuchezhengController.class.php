<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;

class ZhuchezhengController extends CommonController{
	
	private $pageSize = 20;
	
	function index(){
		$query = array(
			'status'	=>	1,
		);
		$parm = I('get.');
		$p = empty($parm['p'])?1:$parm['p']; 
		
		if(!empty($parm['s_title'])){
			$parm['s_title'] = trim($parm['s_title'],' ');
			$query['title'] = array('like','%'.$parm['s_title'].'%'); 
		} 
		$this->assign('parm',$parm);  
		$Zcz = M('Zhuchezheng');
		$list = $Zcz->where($query)->order('created')->page($p.','.$this->pageSize)->select();
		$this->assign('list',$list);
		$count      = $Zcz->where($query)->count();
		$Page       = new Pager($count,$this->pageSize);
		$show = $Page->showhtml(); 
		$this->assign('page',$show); 
		$this->display();
	}
	
	function edit(){
		$id = I('get.id',0);
		if($id>0){
			$zcz = M('Zhuchezheng')->where(array('id'=>$id))->find();
			$this->assign('info',$zcz);
		}
		$this->display();
	}
	
	function save(){
		$dat = I('post.');
		$tm = date('Y-m-d H:i:s');
		$dat['modified'] = $tm;
		$dat['status'] = 1;
		$M = M('Zhuchezheng');
		if($dat['id']){
			$rs = $M->where(array('title'=>$dat['title'],'id'=>array('neq',$dat['id'])))->find();
			if(count($rs)>0){
				_A(false,'注册证已经存在！');
			}
			$res = $M->where(array('id'=>$dat['id']))->save($dat);
			if($res){
				_A(true,'保存成功');
			}else{
				_A(true,'保存失败，请稍后重试');
			}
		}else{
			$dat['created'] = $tm;
			$rs = $M->where(array('title'=>$dat['title']))->find();
			if(count($rs)>0){
				_A(false,'注册证已经存在！');
			}
			$res = $M->data($dat)->add();
			if($res){
				_A(true,'添加成功');
			}else{
				_A(true,'添加失败，请稍后重试');
			}
		}
		
	}
	
	function del(){
		$M = M('Zhuchezheng');
		$id = I('get.id');
		if($id){
			$rs = $M->where(array('id'=>$id))->delete();
			if($rs>0){
				_A(true,'删除成功');
			}
		}
		_A(false,'删除失败请稍后重试');
	}
	
}

?>