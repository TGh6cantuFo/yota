<?php
namespace Index\Controller;
use Think\Controller;
use Org\Util\Wechat;

class ApiController extends Controller{
	private $WX = NULL;
	private $callback = '';
	public function _initialize(){
		header("Access-Control-Allow-Origin:*");
		header("Access-Control-Allow-Headers:X-Requested-With"); 
		$conf = C('wechat'); 
		$this->WX = new Wechat($conf);
	}
	
	/**
	* 授权登陆
	* @return
	*/
	public function oauth(){
		$rurl = $_GET['url'];
		$state = '';
		if(!empty($rurl)){
			$state = $rurl;
		}
		$url = $this->WX->getOauthRedirect($this->callback,$state,'snsapi_userinfo');
		//P($url);
		header('location:'.$url);
	}
	
	/**
	* 授权回调地址
	* @return
	*/
	public function oauthback(){
		$state = $_GET['state'];
		$dat = $this->WX->getOauthAccessToken();
		if(!empty($dat['data'])){
			$userinfo = $this->WX->getOauthUserinfo($dat['access_token'],$dat['openid']);
			if(!empty($userinfo)){
				session('UserInfo',$userinfo);
			}
		}
		if(!empty($state)){
			header('location:'.$state);
		}else{
			header('location:/');
		}
	}
	
	/**
	* 获取用户信息
	* @return
	*/
	public function getUserInfo(){
		$userInfo = session('UserInfo');
		if(!empty($userInfo)){
			_Ajax(true,'userinfo success',$userInfo);
		}else{
			_Ajax(false,'userinfo empty');
		}
	}
	
	/**
	* 获取JS签名【POST】
	* url 当前页面的地址[全路径]
	* @return
	*/
	public function getJsSign(){
		$url = $_POST['url'];
		$url = 'http://www.baidu.com/';
		if(empty($url)){
			_Ajax(false,' Url is mast [POST] ');
		}
		$res = $this->WX->getJsSign($url);
		_Ajax(true,'success',$res);
	}  
	
	/**
	* 保存成绩数据【POST】
	* score 成绩
	* @return
	*/
	public function saveScore(){
		$score = I('post.score');
		//$openid = I('post.openid');
		$userInfo = session('UserInfo');
		if(empty($userInfo)){
			_Ajax(false,' has not login ');
		}
		$dat = array( 'score'=>$score );
		$score = M('Score')->where(array('openid'=>$userInfo['openid']))->find();
		if(empty($score)){
			$rs = M('Score')->data(array( 'score'=>$score,'openid'=>$userInfo['openid'] ))->add();
			if($rs){
				_Ajax(true,' data add Success ');
			}else{
				_Ajax(false,' data add Error ');
			}
		}else{
			$rs = M('Score')->where(array('openid'=>$userInfo['openid']))->save($dat);
			if($rs){
				_Ajax(true,' data save Success ');
			}else{
				_Ajax(false,' data save Error ');
			}
		}
	}
	
	/**
	* 获取排行榜数据【get】
	* count 获取的数量
	* @return
	*/
	public function orderList(){
		$limt = I('get.count',10);
		$list = M('Score')->order('score desc')->limit($limt)->select();
		_Ajax(true,' data list Success ',$list);
	}

} 