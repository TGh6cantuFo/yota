<?php
namespace Home\Controller;
use Common\Controller\BaseController;

class IndexController extends BaseController{
	
	/**
	* 欢迎页面
	* @return
	*/
    public function index(){ 
		/*
		if (!isset($_SESSION['id'])){
           $this->error('请重新登 录', U('Home/Public/index')); 
        }
        
		$user = M('User')->where('id='.$_SESSION['id'])->find();
		$this->assign('USER',$user);
		if($user['username']==session(C('ADMIN_AUTH_KEY'))){  
			$list = M('Menu')->select(); 
			$data = category($list);	
			$this->assign('isAdmin',true);	
		}else{
			$role_id = M('RoleUser')->where('user_id = '.$_SESSION['id'])->getField('role_id'); 
			$list = M('Menu')->select();
			$data = array();
			foreach($list as $li){
				$mid = M('RoleMenu')->where(array('role_id'=>$role_id,'menu_id'=>$li['id']))->getField('menu_id');
				if($mid) $data[] = $li;
			}
			$this->assign('isAdmin',false);	
			$data = category($data);  
		}*/
		$this->assign('list',$data);
		
        $this->display();
    }
	
}
