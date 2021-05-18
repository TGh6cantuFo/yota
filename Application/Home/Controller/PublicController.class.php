<?php
namespace Home\Controller;
use Common\Controller\BaseController;

class PublicController extends BaseController{
	
	
	function index(){
		$this->display();
	}
	
	
	function dologin(){
		$data = I('post.');
		$user = M('Admin')->where(array('username'=>$data['un']))->find();
		if(empty($user)){
			$this->error('账号不存在',U('/Home/Public/index'));
		} 
		//if(md5($data['pd'])!=$user['password']){
		if($data['pd']!=$user['password']){
			$this->error('密码错误',U('/Home/Public/index'));
		}
		if(1 != $user['status']){
			$this->error('账号被禁用',U('/Home/Public/index'));
		} 
		$_SESSION['id'] = $user['id'] ; 
		$this->redirect('/Home/Index/index');
	}
	
	function lgout(){
        session_unset();
		session_destroy();
		$this->redirect('/Home/Public/index'); 
	}
	
}