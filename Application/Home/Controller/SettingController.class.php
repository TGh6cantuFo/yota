<?php
namespace Home\Controller;
use Common\Controller\CommonController ;
use Think\Pager;

class SettingController extends CommonController{ 
	
	function index(){
		$conf = M('Config')->where(array('key'=>'minPer'))->find();
		$this->assign('conf',$conf);
		$this->display();
	}
	
	function save(){
		$dat = I('get.');
		if(!empty($dat['key']) && !empty($dat['val'])){
			$rs = M('Config')->where(array('key'=>$dat['key']))->find();
			if(!empty($rs)){
				$res = M('Config')->where(array('key'=>$dat['key']))->save(array('key'=>$dat['key'],'val' => $dat['val']));
			}else{
				$res = M('Config')->data(array('key'=>$dat['key'],'val' => $dat['val']))->add();
			}
		}
		if($res){
			_A(true,'保存成功');
		}
		else{
			_A(false,'保存失败，请稍后重试');
		}
	}
	
}
?>

