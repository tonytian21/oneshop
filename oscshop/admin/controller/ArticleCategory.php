<?php

namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class ArticleCategory extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','内容管理');
		$this->assign('breadcrumb2','栏目管理');
	}
	
    public function index(){
    	
				
		$cate =Db::query('SELECT cateid as id,parentid as pid,name FROM '.config('database.prefix').'article_category ORDER BY `order` ASC');
		
		$list =list_to_tree($cate);
		
		$this->assign('list',json_encode($list));
		
		return $this->fetch();   
    }
	
	public function add(){
		
		if(request()->isPost()){
			
			$data=input('post.');		
			$data['parentid']=$data['cateid'];
			unset($data['cateid']);
			
			if(!$data['name']){
				return ['error'=>'栏目名称必填'];
			}
			$id=Db::name('ArticleCategory')->insert($data,false,true);
			if($id){
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'添加了文章栏目，'.$data['name']);	
											
				return ['status'=>'success','cateid'=>$id,'name'=>$data['name']];
			}else{
				return ['error'=>'新增失败'];
			}
			
		}
	}
	
	function edit(){
		
		if(request()->isPost()){
			
			$data=input('post.');	
			
			if(!$data['name']){
				return ['error'=>'栏目名称必填'];
			}
			
			$r=Db::name('ArticleCategory')->update($data);
			
			if($r){
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了文章栏目，'.$data['name']);
				
				return ['success'=>'修改成功','name'=>$data['name']];				
			
			}else{
								
				return ['error'=>'修改失败'];
			}
		}
	}
	function del(){
		
		if(request()->isPost()){
			$id=(int)input('post.id');
			
			if(Db::name('ArticleCategory')->where('parentid',$id)->find()){				
				return ['error'=>'请先删除子节点！！'];
			}

			if(Db::name('ArticleCategory')->where('cateid',$id)->delete()){
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了文章栏目，cateid='.$id);
								
				return ['success'=>'删除成功'];
			}
		}		
	}
	function get_info(){
		
		if(request()->isPost()){
			$id=(int)input('cateid');
			$d=Db::name('ArticleCategory')->find($id);
			return ['name'=>$d['name'],'pic_url'=>resize($d['pic_url'],100,100),'order'=>$d['order']] ;
		}
	}
	
	
}
