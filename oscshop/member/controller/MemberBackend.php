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

		    if(!$_FILE['userfile'])
		    	$this->error('请选择导入文件');
                    
            $DictName = date('Ymd').'/'.rand(100000000,999999999);
                    
            $path = $_SERVER['DOCUMENT_ROOT'].'/uploads/'. $DictName.'/';
            if(!is_dir($path))
                mkdir($path, 0777, true);
                
            $filename = date('YmdHis').rand(100,999).'.' . $fileModel->file->extension;
            
            /*
                
            $excel = new ExcelHelper();
            
            $tmpDataArr = $excel->ReadExcel($path. $filename, 'OrderConfig');
            
            if(empty($tmpDataArr))
            {
                $message = '导入表格为空。';
                break;
            }          
            //print_r(count($tmpDataArr['success']));
            //insert success records
            if(isset($tmpDataArr['success']) && count($tmpDataArr['success']) > 0) {
            }
            */
		}
		$this->assign('crumbs','批量导入虚拟会员');
	 	return $this->fetch('import');
	 }
}
?>