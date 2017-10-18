<?php

namespace osc\admin\controller;
use osc\common\controller\AdminBase;
use think\Db;
class Article extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','内容');
		$this->assign('breadcrumb2','文章管理');
	}
	
    public function index(){	
    	$filter=input('param.');	
    	$query=[];
		
		if(isset($filter['title'])){		
			$map['a.title']=['like',"%".$filter['title']."%"];
			$query['a.title']=urlencode($filter['title']);
		}else{
			$map['a.id']=['gt',0];
		}

		if(isset($filter['cateid'])){		
			$map['a.cateid']=intval($filter['cateid']);
			$query['a.cateid']=urlencode($filter['cateid']);
		}

		$list = Db::name('Article')->alias('a')->join('ArticleCategory Ac','a.cateid=Ac.cateid')->field('a.*,Ac.name')
		->where($map)->order('a.id desc')->paginate(config('page_num'),false,$query);
		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
		$this->assign('list', $list);		
		
		$this->assign('category',$this->get_category_tree()); 
		
		return $this->fetch();  
	 }
	
	 public	function add(){
	 	
		if(request()->isPost()){
			
			$data=input('post.');	
			$model=osc_model('admin','Article'); 
			
			$error=$model->validate($data);	
			if($error){
				return $error;
			}
					
			$return=$model->add_Article($data);		
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了文章');						
				return ['success'=>'新增成功','action'=>'add'];				
			}else{			
				return ['error'=>'新增失败'];
			}
			
		}
		
		$this->assign('crumbs', '新增');
		$this->assign('category',$this->get_category_tree());
		$this->assign('action', url('Article/add'));
		return $this->fetch('edit');
		
	}
	 
	 public	function edit(){
	 	if(request()->isPost()){
	 		
	 		$data=input('post.');	
			
			$model=osc_model('admin','Article');  		
			
			$error=$model->validate($data);	
			if($error){
				return $error;
			}
					
			$return=$model->edit_Article($data);		
			
			if($return){								
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'修改了文章');						
				return ['success'=>'修改成功','action'=>'edit'];				
			}else{			
				return ['error'=>'修改失败'];
			}
	 	}
		
		$this->assign('category',$this->get_category_tree());
		$this->assign('Article',Db::name('Article')->find((int)input('id')));
		$this->assign('crumbs', '编辑');
		$this->assign('action', url('Article/edit'));
		return $this->fetch('edit');
	}
	 
	public function del(){
		Db::name('Article')->delete((int)input('id'));
		storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'删除了文章');		
		$this->redirect('Article/index');	
	}
	
	function get_category_tree(){
		$tree=new \oscshop\Tree();	
		return $tree->toFormatTree(Db::name('article_category')->field('cateid,parentid,name')->select(),'name','cateid','parentid');
	}
}
?>