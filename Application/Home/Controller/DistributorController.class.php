<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;

class DistributorController extends CommonController{
 	
 	private $pageSize = 20;
 	
 	private $TyepStr = array(
 		1=>'经销商',
 		2=>'销售',
 		4=>'内部销售',
 		3=>'注册用户',
 	);
 	private $StatusStr = array(
 		1=>'正常',
 		2=>'删除',
 		3=>'待审批',
 	);
	
	function index(){
		$query = array(
			'status'	=>	1,
		); 
		$parm = I('get.');
		$p = empty($parm['p'])?1:$parm['p']; 
		
		if(!empty($parm['s_username'])){
			$query['username'] = array('like','%'.$parm['s_username'].'%'); 
		}
		
		$this->assign('parm',$parm); 
		
		$Dis = M('User'); // 实例化User对象// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$list = $Dis->where($query)->order('created desc')->page($p.','.$this->pageSize)->select();
		
		$dataList = array();
		foreach($list as $li){
			$li['typeStr'] = $this->TyepStr[$li['type']];
			$li['statusStr'] = $this->StatusStr[$li['status']];
			$dataList[] = $li;
		}
		$this->assign('list',$dataList);
		$count      = $Dis->where($query)->count();
		$Page       = new Pager($count,$this->pageSize); 
		$show = $Page->showhtml();
		$this->assign('page',$show); 
		$this->display();
	}
	
	function saveData(){
		$Dis = M('User'); 
		$id = I('post.id');
		$dat = I('post.'); 
		if(empty($id)){
			$Dis->data($dat)->add();
		}else{
			$Dis->where(array('id'=>$id))->save($dat);
		}
		_A(true,'保存完成');
	}
	
	function getData(){
		$id = I('get.id');
		$Dis = M('User'); 
		$info = $Dis->where(array('id'=>$id))->find();
		_A(true,'',$info);
	}
	
	
	function exportUser(){
		$Dis = M('User');
		$list = $Dis->where($query)->order('created desc')->select();
		excelUser($list);
	}
	
	
	function del(){
		$id = I('get.id');
		if(empty($id)){
			_A(false,'参数有误');
		}
		$Dis = M('User');
		$where = array('id'=>$id);
		$rs = $list = $Dis->where($where)->delete();
		if($rs){
			_A(true,'删除成功');
		}else{
			_A(false,'删除失败，请稍后重试');
		}
		
	}
 }
