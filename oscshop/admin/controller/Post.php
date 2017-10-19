<?php

namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class Post extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','帖子');
		$this->assign('breadcrumb2','帖子管理');
	}
	
    public function index(){	
    	$filter=input('param.');	
    	$query=[];
		
		if(isset($filter['title'])){		
			$map['mp.title']=['like',"%".$filter['title']."%"];
			$query['mp.title']=urlencode($filter['title']);
		}else{
			$map['mp.id']=['gt',0];
		}

		$list = Db::name('MomentPosts')->alias('mp')->join('moment m','m.id=mp.mid')
		->join('member me','mp.uid=me.uid')->field('mp.*,m.title as momentname,me.username,me.nickname,me.reg_type')->where($map)->order('mp.id desc')->paginate(config('page_num'),false,$query);
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		$this->assign('list', $list);
		
		return $this->fetch();  
	 }
	
	 public	function edit(){
	 	if(request()->isPost()){
	 		$data=input('post.');	
			
			if(!$data['title']){
				return ['error'=>'修改失败,帖子名称不能为空。'];
			}

			if(!$data['content']){
				return ['error'=>'修改失败,请输入帖子内容。'];
			}
			
			$return = Db::name('MomentPosts')->update($data,false,true);	
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了帖子');						
				return ['success'=>'修改成功','action'=>'edit'];				
			}else{			
				return ['error'=>'修改失败'];
			}
	 	}
		
		$this->assign('Post',Db::name('MomentPosts')->find((int)input('id')));
		$this->assign('crumbs', '编辑');
		$this->assign('action', url('Post/edit'));
		return $this->fetch('edit');
	}
	 
	public function del(){
		Db::name('MomentPosts')->delete((int)input('id'));
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了帖子');		
		$this->redirect('Post/index');	
	}
}
?>