<?php
namespace app\admin\controller;
use app\admin\Login;
//use app\index\model\Type;
//use app\common\model\Money as MoneyModel;
//model不放在module目录下,可以用,但不能直接使用model帮助函数;
use app\common\model\Money as MoneyModel;
use app\common\model\Order as OrderModel;

class Money extends Login{
  
    public function index(){
		$this->view->page_header = '资金列表';
		    $request = request();
       	$list = MoneyModel::getList($request);
        $this->assign('list',$list);
       	// 统计数据
        $stat_fields = [
            'ifnull(sum(amount), 0.00) as flowing_water',
            'ifnull(sum(case when direct = 0 then amount else 0 end), 0.00) as income',
            'ifnull(sum(case when direct = 1 then amount else 0 end), 0.00) as expenditure',
        ];
        $statData = MoneyModel::getStatData($stat_fields);
      	$this->assign('statData', $statData);
      	$names = MoneyModel::NAME_ARRAY;
      	$this->assign('names',$names);
      	$urls = MoneyModel::URL_ARRAY;
      	$this->assign('urls',$urls);
		    return $this->fetch();
    }
    
}