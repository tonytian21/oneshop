<?php

namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class Moment extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','圈子');
		$this->assign('breadcrumb2','圈子管理');
	}
	
    public function index(){	
    	$filter=input('param.');	
    	$query=[];
		
		if(isset($filter['title'])){		
			$map['title']=['like',"%".$filter['title']."%"];
			$query['title']=urlencode($filter['title']);
		}else{
			$map['id']=['gt',0];
		}

		$list = Db::name('Moment')->where($map)->order('id desc')->paginate(config('page_num'),false,$query);
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		$this->assign('list', $list);
		
		return $this->fetch();  
	 }
	
	 public	function add(){
	 	
		if(request()->isPost()){
			$data=input('post.');	
			
			if(!$data['title']){
				return ['error'=>'新增失败,圈子名称不能为空。'];
			}

			if(!$data['username']){
				return ['error'=>'新增失败,请输入圈子管理员用户名。'];
			}

			$memberInfo = Db::name('member')->where("username='".$data['username']."' or nickname='".$data['username']."' or telephone='".$data['username']."' or email='".$data['username']."'")->find();

			if(!$memberInfo){
				return ['error'=>'新增失败,请检查管理员用户名是否存在。'];
			}
			unset($data['username']);
			$data['owneruid'] = $memberInfo['uid'];
			$data['createtime'] = time();
			
			$return = Db::name('moment')->insert($data,false,true);

			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了圈子');						
				return ['success'=>'新增成功','action'=>'add'];				
			}else{			
				return ['error'=>'新增失败'];
			}
			
		}
		
		$this->assign('crumbs', '新增');
		$this->assign('action', url('Moment/add'));
		return $this->fetch('edit');
		
	}
	 
	 public	function edit(){
	 	if(request()->isPost()){
	 		$data=input('post.');	
			
			if(!$data['title']){
				return ['error'=>'修改失败,圈子名称不能为空。'];
			}

			if(!$data['username']){
				return ['error'=>'修改失败,请输入圈子管理员用户名。'];
			}

			$memberInfo = Db::name('member')->where("username='".$data['username']."' or nickname='".$data['username']."' or telephone='".$data['username']."' or email='".$data['username']."'")->find();

			if(!$memberInfo){
				return ['error'=>'修改失败,请检查管理员用户名是否存在。'];
			}
			unset($data['username']);
			$data['owneruid'] = $memberInfo['uid'];
			
			$return = Db::name('moment')->update($data,false,true);	
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了圈子');						
				return ['success'=>'修改成功','action'=>'edit'];				
			}else{			
				return ['error'=>'修改失败'];
			}
	 	}
		
		$this->assign('Moment',Db::name('Moment')->alias('mo')->join('member m','m.uid=mo.owneruid')->field('mo.*,m.username')->find((int)input('id')));
		$this->assign('crumbs', '编辑');
		$this->assign('action', url('Moment/edit'));
		return $this->fetch('edit');
	}
	 
	public function del(){
		Db::name('Moment')->delete((int)input('id'));
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了圈子');		
		$this->redirect('Moment/index');	
	}
}
?>