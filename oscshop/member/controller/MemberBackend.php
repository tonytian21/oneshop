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
 
namespace osc\member\controller;
use osc\common\controller\AdminBase;
use think\Db;
use oscshop\ExcelHelper;

class MemberBackend extends AdminBase{
	
	protected function _initialize(){
		parent::_initialize();
		$this->assign('breadcrumb1','会员');
		$this->assign('breadcrumb2','会员管理');
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
		
		$list=Db::name('member')->alias('m')->field('m.*,mag.title')	
		->join('member_auth_group mag','m.groupid = mag.id')
		->where($map)->order('m.uid desc')->paginate(config('page_num'),false,$query);		
		
		$this->assign('list',$list);
				
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
    	return $this->fetch();
	 }
	 public function add(){
		if(request()->isPost()){
			$date=input('post.');
			$result = $this->validate($date,'Member');			
			if($result!==true){
			
				return ['error'=>$result];
			}
			$member['username']=$date['username'];
			$member['password']=think_ucenter_encrypt($date['password'],config('PWD_KEY'));
			$member['regdate']=time();
			$member['reg_type']='pc';
			$member['email']=$date['email'];
			$member['telephone']=$date['telephone'];
			$member['groupid']=$date['groupid'];
			$member['country'] = $date['country'];
			$member['province'] = $date['province'];
			$member['city'] = $date['city'];
			$member['from'] = '后台添加';
			$member['checked'] = '1';
			$member['userpic'] = $date['userpic'];
			$member['isrobot'] = $date['isrobot'];
			$uid=Db::name('member')->insert($member,false,true);
			
			if($uid){
				
				Db::name('member_auth_group_access')->insert(['uid'=>$uid,'group_id'=>$date['groupid']]);
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'新增了会员');
			
				return ['success'=>'新增成功','action'=>'add'];
			}else{
				return ['error'=>'新增失败'];
				
			}
			
		}
		$this->assign('group',Db::name('member_auth_group')->field('id,title')->select());
		$this->assign('crumbs','新增');
	 	return $this->fetch();
	 }
 	 public function edit(){
	 	
		if(request()->isPost()){
		
			$date=input('post.');			
			$member['password']=think_ucenter_encrypt($date['password'],config('PWD_KEY'));			
			$member['email']=$date['email'];
			$member['checked']=$date['checked'];
			$member['telephone']=$date['telephone'];
			$member['groupid']=$date['groupid'];
			$member['userpic'] = $date['userpic'];
			
			if(Db::name('member')->where('uid',$date['uid'])->update($member)){
				
				Db::name('member_auth_group_access')->where('uid',$date['uid'])->update(['group_id'=>$date['groupid']]);
				
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'编辑了会员');
				$this->success('编辑成功',url('MemberBackend/index'));
			}else{
				$this->error('编辑失败');
			}
		}
		
		$list=[
			'info'=>Db::name('member')->find(input('param.id')),
			'address'=>Db::name('address')->where('uid',input('param.id'))->select()
		];
		$this->assign('data',$list);
		$this->assign('group',Db::name('member_auth_group')->field('id,title')->select());
		$this->assign('crumbs','会员资料');
	 	return $this->fetch('info');
	 }
 	
 	public function template(){
 		$file_name = 'importuser.xls';
		$file_sub_path=$_SERVER['DOCUMENT_ROOT']."/static/resource/";
		$file_path=$file_sub_path.$file_name; 
		$fp=fopen($file_path,"r"); 
		$file_size=filesize($file_path);
		//下载文件需要用到的头 
		Header("Content-type: application/octet-stream"); 
		Header("Accept-Ranges: bytes"); 
		Header("Accept-Length:".$file_size); 
		Header("Content-Disposition: attachment; filename=".$file_name); 
		$buffer=1024; 
		$file_count=0; 
		while(!feof($fp) && $file_count<$file_size){ 
			$file_con=fread($fp,$buffer); 
			$file_count+=$buffer; 
			echo $file_con; 
		} 
		fclose($fp);    
		exit();
 	}

 	public function import(){
	 	
		if(request()->isPost()){
			$config = [
		        'headrow' => '0',
		        'columns' => [
		            0 => [
		                'name' => 'username'
		            ],
		            1 => [
		                'name' => 'password'
		            ],
		            2 => [
		                'name' => 'email'
		            ],
		            3 => [
		                'name' => 'telephone'
		            ],
		        ]
		    ];

			$file = request()->file('userfile');
			    
			if($file){
				$DictName = date('Ymd').'/'.rand(100000000,999999999);
                    
	            $path = $_SERVER['DOCUMENT_ROOT'].'/uploads/'. $DictName.'/';
	            if(!is_dir($path))
	                mkdir($path, 0777, true);
	                
			    $info = $file->move($path);
			    if($info){
			        $excel = new ExcelHelper();
            
		            $tmpDataArr = $excel->ReadExcel($path. $info->getSaveName(), $config);
		            
		            if(empty($tmpDataArr))
		            {
		            	$this->error('导入表格为空。');
		            	return;
		            }          
		            
		            if(isset($tmpDataArr['success']) && count($tmpDataArr['success']) > 0) {
		            	$successCount = 0;
		            	foreach ($tmpDataArr['success'] as $data) {
		            		$username = $data['username'] ? $data['username'] : ($data['email'] ? $data['email'] : $data['telephone']);

		            		if(!$username)
		            			break;

		            		$member['username']= $username;

							$member['password']= think_ucenter_encrypt(($data['password'] ? $data['password'] : '888888'),config('PWD_KEY'));
							$member['regdate']=time();
							$member['reg_type']='pc';
							$member['email']= $data['email'];
							$member['telephone']= $data['telephone'];
							$member['groupid']= 2;
							$member['from'] = '后台导入';
							$member['checked'] = '1';
							//$member['userpic'] = 'images/osc1/11/3';
							$member['isrobot'] = 1;
							$uid=Db::name('member')->insert($member,false,true);
							
							if($uid){
								$successCount++;
							}
		            	}

		            	$this->success('导入成功,成功导入'.$successCount.'条，失败：'.(count($tmpDataArr['success']) - $successCount).'条。',url('MemberBackend/index'));
		            }
		            else{
		            	$this->error('导入表格失败，请检查导入内容。');
		            	return;
		            }
			    }else{
			        $this->error($file->getError());
			    }
			}else{
		    	$this->error('请选择导入文件');
			}
		}
		$this->assign('crumbs','批量导入虚拟会员');
	 	return $this->fetch('import');
	 }

	 public function setwinner(){
		if(request()->isPost()){
			$data=input('post.');

			if(!$data['term_id'] || !$data['uid']){
				return ['error'=>'参数错误'];
			}

			$member['winneruid'] = $data['uid'];
			$map['winneruid'] = 0;
			$map['term_id'] = $data['term_id']; 
			if(Db::name('goods_term')->where($map)->update($member)){
				storage_user_action(UID,session('user_auth.username'),config('BACKEND_USER'),'指定了中奖人');
				return ['success'=>'指定中奖人成功','action'=>'add'];
			}else{
				return ['error'=>'指定中奖人失败'];
			}
		}
		$this->assign('crumbs','指定中奖人');

		$param=input('param.');
		
		$query=[];
		
		if(isset($param['goods_id'])){		
			$map['og.goods_id']= $param['goods_id'];
			$query['og.goods_id']=urlencode($param['goods_id']);
		}

		if(isset($param['user_name'])){		
			$map['m.username|m.nickname']=['like',"%".$param['user_name']."%"];
			$query['m.username']=urlencode($param['user_name']);
		}

		if(isset($param['province']) && $param['province'] != '省份、州'){		
			$map['m.province']=['like',"%".$param['province']."%"];
			$query['m.province']=urlencode($param['province']);
		}

		if(isset($param['city']) && $param['city'] != '地级市、县'){		
			$map['m.username']=['like',"%".$param['city']."%"];
			$query['m.city']=urlencode($param['city']);
		}

		$map['m.isrobot'] = 0;
		$map['gt.winneruid'] = 0;
		$map['gt.complate'] = ['in','1,2'];
		$list=[];
		
		$list=Db::name('member')->alias('m')->field('m.*,mag.title,og.goods_id,gd.name as goodsname,gt.term_id,gt.term_num,o.total as paytotal')	
		->join('member_auth_group mag','m.groupid = mag.id')
		->join('order o','o.uid=m.uid')
		->join('order_goods og','og.order_id=o.order_id')
		->join('goods_term gt','gt.term_id=og.term_id')
		->join('goods_description gd','og.goods_id=gd.goods_id')
		->where($map)->paginate(config('page_num'),false,$query);		
		
		$this->assign('list',$list);

		//获取所有销售中以及等待开奖的商品信息
		$goodsMap['gt.complate'] = ['in','1,2'];
		$goodsInfo = Db::name('goods_term')->alias('gt')->field('gd.name,gd.goods_id')	
		->join('goods_description gd','gt.goods_id=gd.goods_id')->where($goodsMap)->distinct(true)->select();
		$this->assign('goodsInfo',$goodsInfo);		
		$this->assign('empty','<tr><td colspan="20">没有数据~</td></tr>');
		
	 	return $this->fetch('setwinner');
	 }
}
?>