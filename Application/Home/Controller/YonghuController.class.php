<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class YonghuController extends CommonController{
	
	/**
	* 查看师生
	* @return
	*/
    public function datalist(){
         P('查看师生');
    }
	
	/**
	* 添加师生信息
	* @return
	*/
    public function add(){
         P('添加师生信息');
    }
	
	/**
	* 删除师生信息
	* @return
	*/
    public function del(){
         P('删除师生信息');
    }
	
}