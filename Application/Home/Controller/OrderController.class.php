<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;


 class OrderController extends CommonController{ 
	 
	 private $CurrentStatusStr = array(
	 	1=>'已提交',2=>'仅保存',3=>'已删除'
	 ); 
	 
	 private $typeStr = array(
	 	1=>'正常订单',2=>'可调配订单',3=>'期货订单'
	 );
	 
	 private $pageSize = 20;
	 
	 function index(){ 
	 	
		$query = array(
			'id'	=>	array('gt',0),
		);
		
		$parm = I('get.');
		$p = empty($parm['p'])?1:$parm['p']; 
		
		if(!empty($parm['s_code'])){
			$query['code'] = $parm['s_code']; 
		}
		
		if(!empty($parm['s_uname'])){
			$query['username'] = $parm['s_uname']; 
		}
		
		if(!empty($parm['s_phone'])){
			$query['phone'] = $parm['s_phone']; 
		}
		
		$this->assign('parm',$parm); 
		
		$Ord = M('Order'); 
		$list = $Ord->where($query)->order('created')->page($p.','.$this->pageSize)->select();
		 
		
		$dalist = array();
		foreach($list as $tl){ 
			$dalist[] = $tl;
		}
		
		$this->assign('OrderList',$dalist);// 赋值数据集
		
		$count      = $Ord->where($query)->count();// 查询满足要求的总记录数
		$Page       = new Pager($count,$this->pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		
		$show = $Page->showhtml();
		 
		$this->assign('page',$show); 
		$this->assign('CurrentStatusStr',$this->CurrentStatusStr);  
		$this->display();
		 
	 }
	 
	 
	 function detial(){
	 	$id = I('get.id');
	 	if(empty($id)){
			$this->redirect('index');
		}
		$order = M('Order')->where(array('id'=>$id))->find();
		if(empty($order)){
			$this->redirect('index');
		}
		$order['currentStatusStr'] = $this->CurrentStatusStr[$order['currentstatus']];
		$subOrder = M('Suborder')->where(array('order_id'=>$id))->select(); 
		$sOrder = array();
		foreach($subOrder as $so){
			$odList = M('Orderdetial')->where(array('suborder_id'=>$so['id']))->select();
			if(count($odList)<1) continue;
			$proList = array();
			foreach($odList as $od){ 
				$od['Pro'] = M('Product')->where(array('id'=>$od['product_id']))->find();
				$proList[] = $od;
			}
			$so['typeStr'] = $this->typeStr[$so['type']];
			$so['OrderDetial'] = $proList ;
			$sOrder[] = $so;
		}
		$order['SubOrder'] = $sOrder;
		//P($order);
		
		$this->assign('order',$order);
		$this->display();
	 }
	 
	 
	 function droporder(){
	 	$id = I('get.id');
	 	$rs = M('Order')->where(array('id'=>$id))->save(array('status'=>3));
	 	if($rs){
			_A(true,'删除完成');
		}else{
			_A(true,'删除完成');
		}
	 }
	 
	 function export(){
	 	$query = array(
			'id'	=>	array('gt',0),
		);
		
		$parm = I('get.');
		$p = empty($parm['p'])?1:$parm['p']; 
		
		if(!empty($parm['s_code'])){
			$query['code'] = $parm['s_code']; 
		}
		
		if(!empty($parm['s_uname'])){
			$query['username'] = $parm['s_uname']; 
		}
		
		if(!empty($parm['s_phone'])){
			$query['phone'] = $parm['s_phone']; 
		}
		$Ord = M('Order'); 
		$list = $Ord->where($query)->order('created')->select();
		/*
		$res = array();
		foreach($list as $li){
			$t = $li;
			$prolist = json_decode($li['pro_list'],true);
			$tpro = array();
			foreach($prolist['proList'] as $pro){
				$tp = M('Product')->field('title')->where(array('id'=>$pro['pro_id']))->find(); 
				$tpro[] = array_merge($pro,$tp);
			}
			$t['pro_list'] = $tpro;
			$res[] = $t;
		}
		*/
		exportOrder($list);
		
	 }
 
 }