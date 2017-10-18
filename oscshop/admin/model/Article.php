<?php

namespace osc\admin\model;
use think\Db;
class Article{
	
	public function validate($data) {

		$error=array();
		if (!isset($data['title']) || (mb_strlen($data['title']) <1) || (mb_strlen($data['title']) > 200)) {
			$error['error'] = '文章名称必须大于1小于200个字！！';
		}
		else{
			if (!isset($data['cateid']) || intval($data['cateid']) <= 0) {
				$error['error'] = '文章栏目必填！！';
			}else{
				if (!isset($data['description']) || !$data['description']) {
					$error['error'] = '文章详情必填！！';
				}
			}
		}
		

		if($error){
			return $error;
			die;
		}
	
	}
	
	public function add_Article($data){			
		
			$Article['cateid']=$data['cateid'];		
			$Article['title']=$data['title'];
			$Article['author']=$data['author'];
			$Article['keyword']=$data['keyword'];
			$Article['subtitle']=$data['subtitle'];
			$Article['image']=$data['image'];
			$Article['description']=$data['description'];
			$Article['createtime']=time();	
			
			$Article_id=Db::name('Article')->insert($Article,false,true);
			
			if($Article_id){
				return true;
			}else{
				return false;
			}		
	}
	public function edit_Article($data){		
			$Article['id']=$data['id'];
			$Article['cateid']=$data['cateid'];		
			$Article['title']=$data['title'];
			$Article['author']=$data['author'];
			$Article['keyword']=$data['keyword'];
			$Article['subtitle']=$data['subtitle'];
			$Article['image']=$data['image'];
			$Article['description']=$data['description'];
			
			
			$r=Db::name('Article')->update($Article,false,true);	
			
			if($r){
				return true;
			}else{
				return false;
			}		
	}

	
	
}
?>