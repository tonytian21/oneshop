<?php

namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class Reply extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','帖子回复');
		$this->assign('breadcrumb2','帖子回复管理');
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

		if(isset($filter['content'])){		
			$map['mpr.content']=['like',"%".$filter['content']."%"];
			$query['mpr.content']=urlencode($filter['content']);
		}

		$list = Db::name('MomentPostsReply')->alias('mpr')
		->join('MomentPosts mp','mp.id=mpr.pid')
		->join('moment m','m.id=mp.mid')
		->join('member me','mp.uid=me.uid')->field('mpr.*,mp.title as poststitle,m.title as momentname,me.username,me.nickname,me.reg_type')->where($map)->order('mpr.id desc')->paginate(config('page_num'),false,$query);
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		$this->assign('list', $list);
		
		return $this->fetch();  
	 }
	 
	public function del(){
		Db::name('MomentPostsReply')->delete((int)input('id'));
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了帖子回复');		
		$this->redirect('Reply/index');	
	}
}
?>