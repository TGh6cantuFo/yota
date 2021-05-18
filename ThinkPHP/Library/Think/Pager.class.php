<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Think;

class Pager{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 5;// 分页栏每页显示的页数 

    private $p       = 'p'; //分页参数名
    private $url     = ''; //当前链接URL
    private $nowPage = 1;

	// 分页显示定制
    private $config  = array( 
        'prev'   => '<span class="glyphicon glyphicon-triangle-left"></span>',
        'next'   => '<span class="glyphicon glyphicon-triangle-right"></span>',
        'first'  => '<span class="glyphicon glyphicon-step-backward"></span>',
        'last'   => '<span class="glyphicon glyphicon-step-forward"></span>', 
    );

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20, $parameter = array()) {
        C('VAR_PAGE') && $this->p = C('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = $listRows;  //设置每页显示行数
        $this->parameter  = empty($parameter) ? $_GET : $parameter;
        $this->nowPage    = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    private function url($page){
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function showhtml() {
        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $this->url = U(ACTION_NAME, $this->parameter);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows); //总页数
        if(!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
		$now_cool_page_ceil = floor($now_cool_page);
		//$this->lastSuffix && $this->config['last'] = $this->totalPages;

        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ? 
					'<li><a href="' . $this->url($up_row) . '">' . $this->config['prev'] . '</a></li>' : 
					'<li class="disabled"><a>' . $this->config['prev'] . '</a></li>';

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? 
					'<li><a href="' . $this->url($down_row) . '">' . $this->config['next'] . '</a></li>' : 
					'<li class="disabled"><a>' . $this->config['next'] . '</a></li>';

        //第一页
        $the_first = '';
        if($this->nowPage != 1){
            $the_first = '<li><a href="' . $this->url(1) . '">' . $this->config['first'] . '</a></li>';
        }else{
            $the_first = '<li class="disabled"><a>' . $this->config['first'] . '</a></li>';
		}

        //最后一页
        $the_end = '';
        if($this->nowPage != $this->totalPages){
            $the_end = '<li><a href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a></li>';
        }else{
			$the_end = '<li class="disabled"><a>' . $this->config['last'] . '</a></li>';
		}

        //数字连接
        $link_page = "";
		$pages = array();
        for($i = $this->nowPage-$now_cool_page_ceil; $i <= $this->nowPage+$now_cool_page_ceil; $i++){
			if( $i>0 && $i<=$this->totalPages ){
				if($this->nowPage==$i){
					$pages[] = '<li class="active"><a>' . $i . '</a></li>';
				}else{
					$pages[] = '<li><a href="' . $this->url($i) . '">' . $i . '</a></li>';
				}
			} 
        }
		
		$link_page = '<ul class="pagination pagination-sm pull-right">'.$the_first.$up_page.implode($pages,'').$down_page.$the_end.'</ul>'
					.'<label class="pagerlabel pull-left">共<b>'.$this->totalPages.'</b>页 共<b>'.$this->totalRows.'</b>条数据 每页<b>'.$this->listRows.'</b>条数据</label>';
		
		
		return $link_page; 
    }
}
