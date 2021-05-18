<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;

class ProductController extends CommonController{
	
	private $pageSize = 20;
	
	function index(){
		 
		$query = array(
			'status'	=>	1,
		);
		
		$parm = I('get.');
		$p = empty($parm['p'])?1:$parm['p']; 
		
		if(!empty($parm['s_title'])){
			$query['key'] = array('like','%'.$parm['s_title'].'%'); 
		} 
		
		$this->assign('parm',$parm); 
		
		$Pro = M('Product'); // 实例化User对象// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
		$list = $Pro->where($query)->order('created')->page($p.','.$this->pageSize)->select();
		
		$dalist = array();
		foreach($list as $tl){
			$tl['Category1'] = M('Category')->where(array('id'=>$tl['category']))->find();
			$tl['Category2'] = M('Category')->where(array('id'=>$tl['category2']))->find();
			$dalist[] = $tl;
		}
		
		$this->assign('list',$dalist);// 赋值数据集
		
		$count      = $Pro->where($query)->count();// 查询满足要求的总记录数
		$Page       = new Pager($count,$this->pageSize);// 实例化分页类 传入总记录数和每页显示的记录数
		
		$show = $Page->showhtml();
		 
		$this->assign('page',$show);// 赋值分页输出$this->display(); // 输出模板 
		
		$this->display();
	}
	
	function save(){
		$dat = I('post.');
		$qihuo = I('post.qihuo');
		$dat['qihuo'] = empty($qihuo)?2:1;  
		if(empty($dat['id'])){
			$rs = $Pro = M('Product')->data($dat)->add();
			if($rs){
				_A(true,'添加成功');
			}else{
				_A(false,'添加失败，请稍后重试');
			}
		}else{
			$rs = $Pro = M('Product')->where(array('id'=>$dat['id']))->save($dat); 
			if($rs){
				_A(true,'保存成功');
			}else{
				_A(false,'保存失败，请稍后重试');
			}
		}
		_A(false,'OK');
	}
	function info(){
		$id = I('get.id');
		$Pro = M('Product'); 
		$p = $Pro->find($id);
		_A(true,'OK',$p);
	}
	
	
	function getCate(){
		$pid = I('get.pid',0);
		$dat = M('Category')->where(array('pid'=>$pid))->select();
		_A(true,'OK',$dat);
	}
	
	
	function edit(){
		$dat = M('Category')->where(array('pid'=>0))->select(); 
		$this->assign('catList',$dat);
		$zczList = M('Zhuchezheng')->select();
		$this->assign('zczList',$zczList);
		$id = I('get.id');
		if($id){
			$info = M('Product')->where(array('id'=>$id))->find();
			if(!empty($info['image'])){
				$imgList = explode('|',$info['image']);
				$this->assign('imgList',$imgList);
			}
			$this->assign('info',$info);
		}
		
		$this->display();
	}
	
	function saveImg(){
		$images = array();
		$dir = './imgs/';
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		            $images[] = $file;
		        }
		        closedir($dh);
		    }
		}
		$Pro = M('Product');
		$ps = $Pro->select();
		foreach($ps as $p){
			$code = $p['model'];
			if(in_array($code.'.jpg',$images)){
				$dat = array(
					'id'	=>	$p['id'],
					'face_img'	=>	$code.'.jpg',
				);
				$imgs = array();
				for($i=1;$i<5;$i++){
					if(in_array($code.'-'.$i.'.jpg',$images)){
						$imgs[] = $code.'-'.$i.'.jpg';
						echo $code.'-'.$i.'.jpg has In<br />';
					}else{
						echo $code.'-'.$i.'jpg has Not In =========================     <br />';
					}
				} 
				if(count($imgs)>0){
					$dat['image'] = implode('|',$imgs);
				}
				P($dat,false);
				$Pro->save($dat);
			}elseif(in_array($code.'-1.jpg',$images)){
				rename($dir.$code.'-1.jpg',$dir.$code.'.jpg');
				$dat = array(
					'id'	=>	$p['id'],
					'face_img'	=>	$code.'.jpg',
				);
				$imgs = array();
				for($i=2;$i<5;$i++){
					if(in_array($code.'-'.$i.'.jpg',$images)){
						$imgs[] = $code.'-'.$i.'.jpg';
					}
				} 
				if(count($imgs)>0){
					$dat['image'] = implode('|',$imgs);
				}
				P($dat,false);
				$Pro->save($dat);
			}else{
				echo '=====================================================================<br />';
			}
		}
		exit();
	}
	
}
