<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;

class PackageController extends CommonController{
	
	private $pageSize = 20;
	
	function _initialize(){
		parent::_initialize();
	}
	
	function index(){ 
		$query = array(
			'status'	=>	1,
		);
		$p =I('p');
		$p = empty($p)?1:$p; 
		$st = I('s_title');
		if(!empty($st)){
			$query['title'] = array('like','%'.$st.'%'); 
			$this->assign('st',$st);
		}

		$this->assign('parm',$parm); 
		
		$Pack = M('Package'); // 实例化User对象// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$list = $Pack->where($query)->order('created')->page($p.','.$this->pageSize)->select();
		
		$listData = array();
		foreach($list as $li){
			$li['typeObj'] = M('Type')->find($li['type']);
			$li['PeiziObj'] = M('Peizhi')->find($li['peizhi']);
			$li['keshiData'] = M('Keshi')->find($li['keshi']);
			$plist = M('Packagedetial')->where(array('package_id'=>$li['id']))->select();
			 
			$li['Prolist'] = $plist;
			$listData[] = $li;
		} 
		//P($listData);
		$this->assign('list',$listData);
		$count      = $Pack->where($query)->count();
		$Page       = new Pager($count,$this->pageSize);
		
		$show = $Page->showhtml(); 
		$this->assign('page',$show);
		
		$catList = M('Category')->where(array('pid'=>0))->select();
		$this->assign('catList',$catList);
		
		$ksList = M('Keshi')->where(array('pid'=>0))->select();
		$this->assign('ksList',$ksList);
		
		
		$this->display();
	}
	
	function edit(){ 
		$tpList = M('Type')->select();
		$this->assign('tpList',$tpList);
		$pzList = M('Peizhi')->select();
		$this->assign('pzList',$pzList);
		$ksList = M('Keshi')->where(array('pid'=>0))->select();
		$this->assign('ksList',$ksList); 
		$id = I('get.id');
		if(!empty($id)){
			$pack = M('Package')->where(array('id'=>$id))->find();
			$this->assign('info',$pack);
		}
		$this->display();
	}
	
	function save(){
		$dat = I('post.');
		$Pack = M('Package');
		if(empty($dat['id'])){
			$tres = M('Package')->where(array('title'=>$dat['title'],'id'=>array('neq',$dat['id'])))->find();
			if(count($tres)){
				///_A(false,'包名已经存在');
			}
			$rs = $Pack->data($dat)->add();
			if($rs){
				_A(true,'添加成功');
			}else{
				_A(false,'添加失败，请稍后重试');
			}
		}else{
			$tres = M('Package')->where(array('title'=>$dat['title']))->find();
			if(count($tres)){
				//_A(false,'包名已经存在');
			}
			$rs = $Pack->where(array('id'=>$dat['id']))->save($dat);
			if($rs){
				_A(true,'保存成功');
			}else{
				_A(false,'保存失败，请稍后重试');
			}
		} 
	}
	
	function setting(){
		$id = I('get.id');
		$pack = M('Package')->where(array('id'=>$id))->find();
		$packDetial = M('Packagedetial')->where(array('package_id'=>$id))->select();
		$packList = array();
		$mods = array();
		foreach($packDetial as $pd){
			$pd['Pro'] = M('Product')->field('id,title')->where(array('id'=>$pd['pro_id']))->find();
			$mods[] = $pd['pro_model'];
			$packList[] = $pd;
		} 
		$this->assign('pack',$pack);
		$this->assign('packDetial',$packList);
		
		$modellist = I('post.modellist');
		if(!empty($modellist)){
			$this->assign('modellist',$modellist);
			$modellist = str_replace("\r",'',$modellist);
			$smodels = explode("\n",$modellist); 
			$proList = M('Product')->where(array('model'=>array('in',$smodels)))->select();
			$proData = array();
			foreach($proList as $pro){
				if(!in_array($pro['model'],$mods)){
					$proData[] = $pro;
				}
			}
			$this->assign('proList',$proData);
		}
		$this->display();
	}
	
	function addtopack(){
		$pid = I('get.id');
		$ids = I('post.ids');
		$tm = date('Y-m-d H:i:s');
		foreach($ids as $id){
			$pro = M('Product')->where(array('id'=>$id))->find();
			$data = array(
				'package_id'	=>	$pid,
				'pro_id'		=>	$pro['id'],
				'pro_model'		=>	$pro['model'],
				'pro_count'		=>	1,
				'canchange'		=>	1,
				'candelete'		=>	1,
				'canchangecount'		=>	1,
				'status'		=>	1,
				'created'		=>	$tm,
				'modified'		=>	$tm,
			);
			$rs = M('Packagedetial')->data($data)->add();
		}
		_A(true,'保存完成');
	}
	
	function dochange(){
		$id = I('get.id'); 
		$field = I('get.oper'); 
		$val = I('get.val'); 
		$rs = M('Packagedetial')->where(array('id'=>$id))->save(array($field=>$val));
		if($rs){
			_A(true,'保存完成');
		}else{
			_A(false,'保存失败，请稍后重试');
		}
	}
	
	function savecount(){
		$id = I('get.id'); 
		$count = I('get.count');  
		$rs = M('Packagedetial')->where(array('id'=>$id))->save(array('pro_count'=>$count));
		if($rs){
			_A(true,'保存完成');
		}else{
			_A(false,'保存失败，请稍后重试');
		}
	}
	
	function getChild(){
		$tp = I('get.tp');
		$pid = I('get.pid'); 
		if($tp=='category'){
			$list = M('Category')->where(array('pid'=>$pid))->select();
		}elseif($tp=='keshi'){
			$list = M('Keshi')->where(array('pid'=>$pid))->select();
		}
		_A(true,'ds',$list);
	}
	
	function saveitem(){
		$id = I('post.id');
		$ids = I('post.ids');
		$ids = json_encode($ids); 
		if(!empty($id)&&!empty($ids)){
			$Pack = M('Package');  
			$Pack->where(array('id'=>$id))->save(array('pro_list'=>$ids));
		}
		_A(true,'OK');
	}
	
	
	function delpro(){
		$proid = I('get.proid');
		$packid = I('get.packid');
		$where = array(
			'package_id'	=>	$packid,
			'pro_id'		=>	$proid
		); 
		$rs = M('Packagedetial')->where($where)->delete(); 
		if($rs){
			_A(true,'删除完成');
		}else{
			_A(false,'操作失败，请重试');
		} 
	}
	 
}
