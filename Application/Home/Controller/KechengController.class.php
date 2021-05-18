<?php
namespace Home\Controller;
use Common\Controller\CommonController;

class KechengController extends CommonController{
	
	/**
	* 课程查看
	* @return
	*/
    public function datalist(){
         P('课程查看');
    }
	
	/**
	* 添加课程
	* @return
	*/
    public function add(){
         P('添加课程');
    }
	
	/**
	* 删除课程
	* @return
	*/
    public function del(){
         P('删除课程');
    }

}
