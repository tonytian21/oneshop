<?php
namespace osc\member\controller;
use osc\common\controller\AdminBase;
use think\Db;

class RechargeBackend extends AdminBase{
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','会员');
		$this->assign('breadcrumb2','充值记录');
	}
	
     public function index(){     	
		$param=input('param.');

		$query=[];
		
		if(isset($param['user_name'])){		
			$map['m.username|m.nickname']=['like',"%".$param['user_name']."%"];
			$query['m.username']=urlencode($param['user_name']);
		}else{
			$map['m.uid']=['gt',0];
		}
		
		$list=[];
		
		$list=Db::name('recharge_list')->alias('rl')->field('rl.*,m.username,m.nickname,m.userpic,m.province,m.city,m.reg_type')	
		->join('member m','m.uid = rl.uid')
		->where($map)->order('rl.createtime desc')->paginate(config('page_num'),false,$query);		
		
		$this->assign('list',$list);
				
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
    	return $this->fetch();
	}

	public function add(){
		if(request()->isPost()){
			$data=input('post.');
			
			if(!$data['username']){
				return ['error'=>'充值失败，用户名必填'];
			}

			//根据username获取uid
			$userInfo = Db::name('member')->where("username='".$data['username']."' or nickname = '".$data['username']."'")->find();

			if(!$userInfo){
				return ['error'=>'充值失败，用户不存在，请检查用户名'];
			}

			$recharge['uid'] = $userInfo['uid'];

			if(!$data['money'] || doubleval($data['money']) < 0)
			{
				return ['error'=>'充值失败，请输入正确的充值金额'];
			}

			$recharge['money'] = doubleval($data['money']);
			$recharge['chargetype'] = 1;
			$recharge['createtime'] = time();
			$recharge['paytype'] = '后台充值';
			$recharge['rechargenum'] = date('YmdHis',time()).mt_rand(100000,999999);
			$recharge['createuser'] = session('user_auth.username');
			$recharge['remark'] = $data['remark'];
			
			$id=Db::name('recharge_list')->insert($recharge,false,true);
			
			if($id){
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了充值记录');
			
				return ['success'=>'充值成功','action'=>'add'];
			}else{
				return ['error'=>'充值失败'];
				
			}
			
		}
		
		$this->assign('crumbs','会员充值');
	 	return $this->fetch();
	 }
}