<?php

function getPlace($pid){
	if($pid=='a'){
        $bx = array(
            'id' => 'b',
            'title' => '市（不限）',
            'pid' => '',
            'type' => '',
            'status' => '',
        ); $res[] = $s; 
        return array($bx);
    }else if($pid=='b'){
        $bx = array(
            'id' => 'c',
            'title' => '区（不限）',
            'pid' => '',
            'type' => '',
            'status' => '',
        ); $res[] = $s; 
        return array($bx);
    }
    
    $ss = M('ssq')->where(array('pid'=>$pid))->select();
    $tag = array(
        0	=>	'省',
        1	=>	'市',
        2	=>	'区',
    );
    $ids = array(
        0	=>	'a',
        1	=>	'b',
        2	=>	'c',
    );
    $add = M('ssq')->where(array('id'=>$pid))->find();
    if($add['type']>=1){
		$tp = $tag[$add['type']].'（不限）';
	}else{
		$tp = '省（不限）';
	}
    $bx = array(
        'id' => $ids[$pid],
        'title' => $tp,
        'pid' => '',
        'type' => '',
        'status' => '',
    );
    $res = array();
    $res[] = $bx;
    foreach($ss as $s){
        $res[] = $s;
    }
    return $res;
}

function getListBy($conf=array(),$isexport = false){
	$pageSize = empty($conf['pageSize'])?5:$conf['pageSize'];
	$Arrs = array(
			1=>'批发',
			2=>'零售',
			3=>'批发零售',
			4=>'物流',
	);
	$page = $conf['page'];
	$limit = ($page-1)*$pageSize.','.$pageSize ;
	
	if(count($conf)<1){
		$rs = M('map')->limit($limit)->select();
		$count = M('map')->count();
	}else{
		$where = ' 1>0 ';
		if(!empty($conf['type']) && '不限'!=$conf['type']){
			$where .= ' and type = "'.$conf['type'].'"';
		}
		if(!empty($conf['title'])){
			$where .= ' and title like "%'.$conf['title'].'%" ';
		}
		if(!empty($conf['sheng'])){
			if($conf['sheng']!='a'){
				$where .= ' and sheng like "'.$conf['sheng'].'%" ';
			}
		}
		$sList = array('上海市','北京市','天津市','重庆市');
		if(!empty($conf['shi'])){
			if($conf['shi']!='b' && !in_array($conf['shi'],$shiList)){
				$where .= ' and shi = "'.$conf['shi'].'" ';
			}
		} 
		if(!empty($conf['qu'])){
			if($conf['qu']!='c'){
				$where .= ' and qu like "'.$conf['qu'].'%" ';
			}
		}
		$rs = M('map')->where($where)->limit($limit)->order('created desc')->select();
		$count = M('map')->where($where)->count();
	}
	if(!empty($rs)){
		$tar = array();
		foreach($rs as $s){
			$s['type']	=	$Arrs[$s['type']]; 
			$tar[] = $s;
		}
		$rs = $tar ;
	}
	$page = new \Think\Pager($count,$pageSize);
	return array('dataList'=>$rs,'page'=>$page);
}


function getAllListBy($conf=array(),$isexport = false){
	$pageSize = $conf['pageSize'];
	$Arrs = array(
			1=>'批发',
			2=>'零售',
			3=>'批发零售',
			4=>'物流',
	);
	$page = $conf['page']; 
	$limit = ($page-1)*$pageSize.','.$pageSize ;
	$order = $conf['order'];
	$field = $conf['field'];
	
	if(count($conf)<1){
		if($field){
			$rs = M('map')->field($field)->limit($limit)->select();
		}else{
			$rs = M('map')->limit($limit)->select();
		}
		$count = M('map')->count();
	}else{
		$where = ' 1>0 ';
		if(!empty($conf['title'])){
			//$where .= ' and title like "%'.$conf['title'].'%" ';
			$where .= ' and title = "'.$conf['title'].'" ';
		}
		if(!empty($conf['area'])){
			$where .= ' and area like "%'.$conf['area'].'%" ';
		}
		if(!empty($conf['type']) && '不限'!=$conf['type']){
			$where .= ' and type = "'.$conf['type'].'"';
		}
		if(!empty($conf['sheng'])){
			if($conf['sheng']!='a'){
				$where .= ' and sheng = "'.$conf['sheng'].'" ';
			}
		}
		$sList = array('上海市','北京市','天津市','重庆市');
		if(!empty($conf['shi'])){
			if($conf['shi']!='b' && !in_array($conf['shi'],$sList)){
				$where .= ' and shi = "'.$conf['shi'].'" ';
			}
		}
		if(!empty($conf['qu'])){
			if($conf['qu']!='c'){
				$where .= ' and qu = "'.$conf['qu'].'" ';
			}
		}
		if(!empty($conf['addr'])){
			if($conf['addr']!='c'){
				$where .= ' and addr like "%'.$conf['addr'].'%" ';
			}
		}
		if($field){
			$rs = M('map')->field($field)->where($where)->order($order)->limit($limit)->select();
		}else{
			$rs = M('map')->where($where)->order($order)->limit($limit)->select();
		}
		
		$count = M('map')->where($where)->count();
	}
	
	$user = M('User')->where(array('id'=>array('gt',0)))->find();
	$userTpl = array();
	foreach($user as $k => $v){
		$userTpl[$k] = '';
	}
	if(!empty($rs)){
		$tar = array();
		foreach($rs as $tk => $s){
			$s['typeStr']	=	$Arrs[$s['type']];
			$user = M('User')->where(array('gongsi_id'=>$s['id']))->order('type asc')->find();
			if(count($user)>0){
				$s['User']	= $user;
			}else{
				$s['User']	= $userTpl;
			}
			$s['idStr'] = ($page-1)*$pageSize+$tk;
			$tar[] = $s; 
		}
		$rs = $tar ;
	}
	$page = new \Think\Pager($count,$pageSize,$where); 
	return array('dataList'=>$rs,'page'=>$page);
}

function getDetialById($id){
	if(empty($id)){
		//_Ajax(false,'参数有误');
	}
	$Arrs = array(
			1=>'批发',
			2=>'零售',
			3=>'批发零售',
			4=>'物流',
	);
	$M = M('map'); 
	$result = $M->find($id);
	if(!empty($result)){ 
		$result['typeStr']	=	$Arrs[$result['type']]; 
	}
	_Ajax(true,'Success',$result);
}

function getParmBySheng($parm){ 
	if(empty($sheng)){
		//_Ajax(false,'参数有误');
	}
	$M = M('map');
	$sql = 'select';
	$sql .= ' count(mj) as wl_count , sum(mj) as wl_size , sum(cwk) as cwk ,sum(ylk) as ylk , sum(lk_mj) as lk_mj , sum(lk_tj) as lk_tj, sum(ccws) as ccws, sum(pscl) as pscl , sum(zy_cl) as zy_cl , sum(lc_cl) as lc_cl , sum(ts_cl) as ts_cl ';
	$sql .=' from yy_map';
	$where = ' where 1>0 ';
	if(!empty($parm['sheng'])){
		$where .=' and sheng = "'.$parm['sheng'].'"';
	}
	$sList = array('上海市','北京市','天津市','重庆市');
	if(!empty($parm['shi']) && !in_array($parm['shi'],$sList) && $parm['shi']!='b'){
		$where .=' and shi = "'.$parm['shi'].'"';
	}
	if(!empty($parm['qu'])){
		$where .=' and qu = "'.$parm['qu'].'"';
	}
	if(!empty($parm['type'])){
		$where .=' and type = '.$parm['type'] ;
	}
	if(!empty($parm['area'])){
		$where .=' and area = "'.$parm['area'].'"' ;
	}
	if(!empty($parm['title'])){
		$where .=' and title like "%'.$parm['title'].'%"' ;
	}
	$sql .= $where;
	$result = $M->query($sql);
	if(count($result)<1){
		_Ajax(false,'没有查找到你要的数据');
	}else{
		$rss = $result[0];
		$rss['wl_size'] = number_format($rss['wl_size'],1);
		$rss['cwk'] = number_format($rss['cwk'],1);
		$rss['ylk'] = number_format($rss['ylk'],1);
		$rss['lk_mj'] = number_format($rss['lk_mj'],1);
		$rss['lk_tj'] = number_format($rss['lk_tj'],1);
		_Ajax(true,'Success',$rss);
	}
}


function getParmByType($yt){ 
	if(empty($yt)){
		//_Ajax(false,'参数有误');
	}
	$M = M('map');
	$sql = 'select';
	$sql .= ' count(mj) as wl_count , sum(mj) as wl_size , sum(cwk) as cwk ,sum(ylk) as ylk , sum(lk_mj) as lk_mj , sum(lk_tj) as lk_tj, sum(ccws) as ccws, sum(pscl) as pscl , sum(zy_cl) as zy_cl , sum(lc_cl) as lc_cl , sum(ts_cl) as ts_cl ';
	$sql .=' from yy_map'; 
	if(!empty($yt)){
		$sql .=' where type = '.$yt;
	}
	$result = $M->query($sql);
	if(count($result)<1){
		_Ajax(false,'没有查找到你要的数据');
	}else{
		$rss = $result[0];
		$rss['wl_size'] = number_format($rss['wl_size'],1);
		$rss['cwk'] = number_format($rss['cwk'],1);
		$rss['ylk'] = number_format($rss['ylk'],1);
		$rss['lk_mj'] = number_format($rss['lk_mj'],1);
		$rss['lk_tj'] = number_format($rss['lk_tj'],1); 
		_Ajax(true,'Success',$rss);
	}
}

function getTitleList($title){
	$where = ' title like "%'.$title.'%" '; 
	$rs = M('map')->where($where)->limit(10)->select();
	return $rs;
}

function initData(){ 
	return false;
	$titleArr = array(
		'title',
		'type',
		'area',
		'sheng',
		'shi',
		'qu',
		'mj',
		'cc',
		'y_cc',
		'y_ttdd',
		'y_ttzh',
		'y_tt',
		'nph_1',
		'nph_2',
		'kczz',
		'ddnl',
		'psbl',
		'fgfw',
		'pjbj',
		'zdbj',
		'cwk',
		'ylk',
		'lk_mj',
		'lk_tj',
		'ccws',
		'pscl',
		'zy_cl',
		'lc_cl',
		'ts_cl',
		'wms',
		'wsjc',
		'ddxt',
		'dps',
		'rfid',
		'wcs',
		'tms',
		'wskz',
		'crm',
		'tpl'
	);

	$f = fopen('./Public/data/data.csv','r');
	$line = fgetcsv($f); 
	
	$m = M('Map');
	
	while($line = fgetcsv($f)){ 
		$li = array();
		foreach($titleArr as $k => $l){
			$li[$l] = trim($line[$k]);
		}
		$li['qu'] = preg_replace('/'.$li['shi'].'/','',$li['qu']);
		$li['qu'] = preg_replace('/'.$li['sheng'].'/','',$li['qu']);
		$li['shi'] = preg_replace('/'.$li['sheng'].'/','',$li['shi']);
		
		$li['wms'] = ($li['wms']=="是")?1:0;
		$li['wsjc'] = ($li['wsjc']=="是")?1:0;
		$li['ddxt'] = ($li['ddxt']=="是")?1:0;
		$li['dps'] = ($li['dps']=="是")?1:0; 
		$li['rfid'] = ($li['rfid']=="是")?1:0;
		$li['wcs'] = ($li['wcs']=="是")?1:0;
		$li['tms'] = ($li['tms']=="是")?1:0; 
		$li['wskz'] = ($li['wskz']=="是")?1:0;
		$li['crm'] = ($li['crm']=="是")?1:0;
		$li['tpl'] = ($li['tpl']=="是")?1:0;
		
		$rs = $m->data($li)->add();
		if(!$rs){
			P($li,false);
		}
	}
	//  同步一下 缓存表里的数据 
	//	insert into yy_title select id, title from yy_map;
}

function getSheng(){ 
	$m = M('Map');
	$sheng = $m->field('sheng')->group('sheng')->select();
	$data = array();
	foreach($sheng as $s){
		$dat = array(
			'title'	=>	$s['sheng'],
			'type'	=>	1,
			'pid'	=>	0 
		);
		M('ssq')->data($dat)->add();
	}
	$ss = M('ssq')->where(array('type'=>1))->select(); 
	foreach($ss as $s){
		$shi = $m->field('shi')->group('shi')->where(array('sheng'=>$s['title']))->select(); 
		foreach($shi as $sh){
			$dat = array(
				'title'	=>	$sh['shi'],
				'type'	=>	2,
				'pid'	=>	$s['id'] 
			); 
			M('ssq')->data($dat)->add();
		}
	}
	$shilist = M('ssq')->where(array('type'=>2))->select();
	foreach($shilist as $shii){
		$qs = $m->field('qu')->group('qu')->where(array('shi'=>$shii['title']))->select(); 
		$qs['t'] = $shii['title']; 
		foreach($qs as $q){
			$dat = array(
				'title'	=>	$q['qu'],
				'type'	=>	3,
				'pid'	=>	$shii['id'] 
			);
			if(!empty($q['qu'])){
				M('ssq')->data($dat)->add();
			}
		}
	}
	exit();
}
 

function doExpoertGSData($rs,$filename){
	$header_str = "id,企业名称,业务类型,行政区域,省（自治区、直辖市）,市（州、盟）,县（区、旗）,物流中心仓库总面积/平方米,物流中心设计存储能力（存储折合件数）/件,物流中心仓库月均库存折合件数/件,月均出入库吞吐总订单行数/行,月均出入库吞吐折合件数/件,月均出入库吞吐量/吨,年度配送货值1（药品经营企业年度无税销售额）/千元,年度配送货值2（第三方医药物流业务配送货值）/千元,库存周转天数/天,每小时行订单处理能力/行,自运配送比例%,配送覆盖范围（□ 全国 □ 跨省 □ 省内 □ 市内）,自运配送平均半径（平均距离）/公里,自运配送最大半径（最远距离）/公里,常温库面积/平方米,阴凉库面积/平方米,冷库面积/平方米,冷库容积/立方米,仓库存储货位数/个,配送车辆（含自有+租赁）/台,自有配送车辆/台,自有冷藏车/台,自有特殊药品专用车/台,仓储管理系统（WMS）,温湿度自动监测系统,订单管理系统,数码拣选系统（DPS）,射频识别系统（RFID）,仓库控制系统（WCS）（设备控制系统）,运输管理系统(TMS),可追溯温湿度监控系统,客户关系管理系统（CRM）,货主管理系统（TPL）\n";
	$line = explode(',',$header_str); 
	doExport($rs,$filename,$line);
}

function doExport($rs,$filename,$title){
	$path = $filename;  
	$datalist = array();
	$datalist[] = $title;
	foreach($rs as $l){
		$datalist[] = $l;
	}
	create_xls($datalist,$filename); 
}

function create_xls($data,$filename='simple.xls'){
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename).'.xls';
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("Nicky")
        ->setLastModifiedBy("545588891@qq.com")
        ->setTitle("Data Export")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("Data Export")
        ->setCategory("Test result file");
    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle(date('Y_m_d'));
    $phpexcel->setActiveSheetIndex(0); 
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save($filename);
}

function doExpoertUserData($conf,$filename){
	$typeStr = array(
		'4'	=> '普通用户',  '3'	=> 'VIP用户',  '2'	=> '系统管理员',  '1'	=> '超级管理员'
	);
	$statuStr = array( '1'	=> '正常',  '2'	=> '禁用',  '3'	=> '待审核' );
	if(count($conf)<1){
		$rs = M('User')->select();
	}else{
		if(!empty($conf['company'])){
			$com = M('Map')->where(array('title'=>$conf['company']))->find();
			if(count($com)>0){
				$conf['gongsi_id'] = $com['id'];
			}
			unset($conf['company']);
		}
		if(!empty($conf['key'])){
			$conf['username'] = array('like','%'.$conf['key'].'%');
		}
		$rs = M('User')->where($conf)->select();
	} 
	if(!empty($rs)){
		$tar = array();
		foreach($rs as $s){
			$s['type']	=	$typeStr[$s['type']];
			$s['status']	=	$statuStr[$s['status']];
			unset($s['password']);
			unset($s['modified']);
			unset($s['gongsiname']);
			unset($s['lgcount']);
			unset($s['isonline']);
			$tar[] = $s;
		}
		$rs = $tar ;
	}
	$title = '序号,用户名,姓名,部门/职务,手机,Email,用户类型,公司ID,所属集团,状态,注册日期'; 
	$line = explode(',',$title); 
	doExport($rs,$filename,$line); 
}

function doExpoertUserByIdsData($ids,$filename){
	$typeStr = array(
		'4'	=> '普通用户',  '3'	=> 'VIP用户',  '2'	=> '管理员',  '1'	=> '超级管理员'
	);
	$statuStr = array( '1'	=> '正常',  '2'	=> '禁用',  '3'	=> '待审核' );
	$rs = M('User')->where(array('id'=>array('in',$ids)))->select();
	if(!empty($rs)){
		$tar = array();
		foreach($rs as $s){
			$s['type']	=	$typeStr[$s['type']];
			$s['status']	=	$statuStr[$s['status']];
			unset($s['password']);
			unset($s['modified']);
			unset($s['gongsiname']);
			unset($s['lgcount']);
			unset($s['isonline']);
			$tar[] = $s;
		}
		$rs = $tar ;
	}
	$title = '序号,用户名,姓名,部门/职务,手机,Email,用户类型,公司ID,所属集团,状态,注册日期'; 
	$line = explode(',',$title); 
	doExport($rs,$filename,$line);
	
}


function doExpoertCompanyData($conf,$filename){ 
	$Arrs = array(
			1=>'批发',
			2=>'零售',
			3=>'批发零售',
			4=>'物流',
	);
	if(count($conf)<1){
		$rs = M('map')->select();
	}else{
		$where = ' 1>0 ';
		if(!empty($conf['title'])){
			$where .= ' and title like "%'.$conf['title'].'%" ';
		}
		if(!empty($conf['area'])){
			$where .= ' and area like "%'.$conf['area'].'%" ';
		} 
		if(!empty($conf['type']) && '不限'!=$conf['type']){
			if($conf['type']=='批发'){
				$where .= ' and type = 1 ';
			}elseif($conf['type']=='零售'){
				$where .= ' and type = 2 ';
			}elseif($conf['type']=='批发零售'){
				$where .= ' and type = 3 ';
			}elseif($conf['type']=='物流'){
				$where .= ' and type = 4 ';
			}
			//$where .= ' and type = "'.$conf['type'].'"';
		} 
		
		if(!empty($conf['sheng'])){
			if($conf['sheng']!='a'){
				$where .= ' and sheng = "'.$conf['sheng'].'" ';
			}
		}
		
		$sList = array('上海市','北京市','天津市','重庆市');
		if(!empty($conf['shi'])){
			if($conf['shi']!='b' && !in_array($conf['shi'],$sList)){
				$where .= ' and shi = "'.$conf['shi'].'" ';
			}
		}
		if(!empty($conf['qu'])){
			if($conf['qu']!='c'){
				$where .= ' and qu = "'.$conf['qu'].'" ';
			}
		}
		if(!empty($conf['addr'])){
			if($conf['addr']!='c'){
				$where .= ' and addr like "%'.$conf['addr'].'%" ';
			}
		}
		$rs = M('map')->where($where)->select();
	}
	 
	if(!empty($rs)){
		$tar = array();
		foreach($rs as $s){
			$s['type']	=	$Arrs[$s['type']];
			$tar[] = $s;
		}
		$rs = $tar ;
	}
	$title = '序号,企业名称,所属集团,业务类型,省（自治区、直辖市）,市（州、盟）,县（区、旗）,地址,是否为具备独立法人资质的物流企业,物流中心数量（个）,物流中心仓库总面积（㎡）,常温库面积（㎡）,阴凉库面积（㎡）,冷库面积（㎡）,冷库体积（m3）,仓库存储标准托盘货位数（个）,物流中心设计存储能力（存储折合件数）（件）,配送车辆（含自有+租赁）（台）,自有配送车辆（台）,自有冷藏车（台）,自有特殊药品专用车（台）,物流中心仓库月均库存折合件数（件）,月均出入库吞吐总订单行数（行）,月均出入库吞吐折合件数（件）,月均出入库吞吐量（吨）,冷链产品月均出入库吞吐量（件）,配送客户数（个）,自营配送比例（%）,委托配送比例（%）,固定资产总额（千元）,年度配送货值1（药品经营企业年度无税销售额）（千元）,年度配送货值2（第三方医药物流业务配送货值）（千元）,冷链产品年配送货值（千元）,物流服务总收入（千元）,第三方物流服务收入（千元）,物流费用总额（千元）,物流业务利润总额（千元）,库存周转天数（天）,账货相符率（%）,出库差错率（%）,货物准时送达率（%）,仓储管理系统（WMS）,温湿度自动监测系统,订单管理系统,数码拣选系统（DPS）,射频识别系统（RFID）,仓库控制系统（WCS）（设备控制系统）,运输管理系统(TMS),可追溯温湿度监控系统,客户关系管理系统（CRM）,货主管理系统（TPL）';
	
	$line = explode(',',$title); 
	doExport($rs,$filename,$line); 
}

function doExpoertCompanyByIdsData($ids,$filename){ 
	$Arrs = array(
			1=>'批发',
			2=>'零售',
			3=>'批发零售',
			4=>'第三方物流',
	);
	$rs = M('map')->where(array('id'=>array('in',$ids)))->select();
	if(!empty($rs)){
		$tar = array();
		foreach($rs as $s){
			$s['type']	=	$Arrs[$s['type']];
			$tar[] = $s;
		}
		$rs = $tar ;
	}
	$title = '序号,企业名称,所属集团,业务类型,省（自治区、直辖市）,市（州、盟）,县（区、旗）,地址,是否为具备独立法人资质的物流企业,物流中心数量（个）,物流中心仓库总面积（㎡）,常温库面积（㎡）,阴凉库面积（㎡）,冷库面积（㎡）,冷库体积（m3）,仓库存储标准托盘货位数（个）,物流中心设计存储能力（存储折合件数）（件）,配送车辆（含自有+租赁）（台）,自有配送车辆（台）,自有冷藏车（台）,自有特殊药品专用车（台）,物流中心仓库月均库存折合件数（件）,月均出入库吞吐总订单行数（行）,月均出入库吞吐折合件数（件）,月均出入库吞吐量（吨）,冷链产品月均出入库吞吐量（件）,配送客户数（个）,自营配送比例（%）,委托配送比例（%）,固定资产总额（千元）,年度配送货值1（药品经营企业年度无税销售额）（千元）,年度配送货值2（第三方医药物流业务配送货值）（千元）,冷链产品年配送货值（千元）,物流服务总收入（千元）,第三方物流服务收入（千元）,物流费用总额（千元）,物流业务利润总额（千元）,库存周转天数（天）,账货相符率（%）,出库差错率（%）,货物准时送达率（%）,仓储管理系统（WMS）,温湿度自动监测系统,订单管理系统,数码拣选系统（DPS）,射频识别系统（RFID）,仓库控制系统（WCS）（设备控制系统）,运输管理系统(TMS),可追溯温湿度监控系统,客户关系管理系统（CRM）,货主管理系统（TPL）';
	
	$line = explode(',',$title); 
	doExport($rs,$filename,$line); 
}
 
/**
* 发送邮件
* @param undefined $address
* @param undefined $subject
* @param undefined $content
* 
* @return
*/
function doSendEmail($address,$subject,$content){
		$email_smtp=C('EMAIL_SMTP');
	    $email_username=C('EMAIL_USERNAME');
	    $email_password=C('EMAIL_PASSWORD');
	    $email_from_name=C('EMAIL_FROM_NAME');
	    if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
	        return array("error"=>1,"message"=>'邮箱配置不完整');
	    }
 
	    require_once './ThinkPHP/Library/Org/Util/class.phpmailer.php';
    	require_once './ThinkPHP/Library/Org/Util/class.smtp.php';

	    $phpmailer=new PHPMailer();
	    // 设置PHPMailer使用SMTP服务器发送Email
	    $phpmailer->IsSMTP();
	    // 设置为html格式
	    $phpmailer->IsHTML(true);
	    // 设置邮件的字符编码'
	    $phpmailer->CharSet='UTF-8';
	    // 设置SMTP服务器。
	    $phpmailer->Host=$email_smtp;
	    // 设置为"需要验证"
	    $phpmailer->SMTPAuth=true;
	    // 设置用户名
	    $phpmailer->Username=$email_username;
	    // 设置密码
	    $phpmailer->Password=$email_password;
	    // 设置邮件头的From字段。
	    $phpmailer->From=$email_username;
	    // 设置发件人名字
	    $phpmailer->FromName=$email_from_name;
	    // 添加收件人地址，可以多次使用来添加多个收件人
	    if(is_array($address)){
	        foreach($address as $addressv){
	            $phpmailer->AddAddress($addressv);
	        }
	    }else{
	        $phpmailer->AddAddress($address);
	    }
	    // 设置邮件标题
	    $phpmailer->Subject=$subject;
	    // 设置邮件正文
	    $phpmailer->IsHTML(true);
	    $phpmailer->Body=$content;
	    // 发送邮件。
	    if(!$phpmailer->Send()) {
	        $phpmailererror=$phpmailer->ErrorInfo;
	        return array("error"=>1,"message"=>$phpmailererror);
	    }else{
	        return array("error"=>0);
	    }
}


?>