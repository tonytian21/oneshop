<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
use think\cache\Driver\Redis;
class Term extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','商品');
		$this->assign('breadcrumb2','期数');
	}
	
    public function index(){     	
		
		$list = Db::name('GoodsTerm')->alias('gt')->join('goods g','g.goods_id=gt.goods_id')
		->join('goods_description gd','gt.goods_id=gd.goods_id')
		->join('goods_description_en gde','gt.goods_id=gde.goods_en_id')
		->field('gt.*,gd.*,gde.*,g.minimum as gminimum,g.robot_buy_ratio as grobot_buy_ratio,g.robot_win as grobot_win,g.image')->paginate(config('page_num'));
		$this->assign('empty', '<tr><td colspan="20">~~暂无数据</td></tr>');
		$this->assign('list', $list);
	
		return $this->fetch();

	 }
	 /*
	 public	function add(){
		if(request()->isPost()){	
			return $this->single_table_insert('GoodsTerm','添加了品牌');
		}
		$this->assign('crumbs', '新增');
		$this->assign('action', url('term/add'));
		return $this->fetch('edit');
	}
	*/
	 public	function edit(){
		if(request()->isPost()){	
			return $this->single_table_update('GoodsTerm','修改了品牌');
		}
		$this->assign('crumbs', '修改');
		$this->assign('action', url('term/edit'));		
		$this->assign('d',Db::name('GoodsTerm')->find((int)input('id')));		
		return $this->fetch('edit');
	}
	public	function del(){
		if(request()->isGet()){	
			$term = Db::name('GoodsTerm')->find((int)input('id'));

			if($term){
				if($term['saledcopies']){
					$this->error('已经产生购买，不能删除');
				}
			}

			$term_num = $term['term_num'];

			$r= $this->single_table_delete('GoodsTerm','删除了品牌');

			$redis = new Redis();
			$redis->lrem('GOODS:'.$term_num,0,null);

			if($r){
				$this->redirect('term/index');
			}
		}
	}
}
?>