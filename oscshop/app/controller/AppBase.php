<?php

namespace osc\app\controller;
use think\Controller;
class AppBase extends Controller{

    protected function _initialize() {
		parent::_initialize();		
		if(!request()->isMobile()&&(false==config('app_debug'))){
			$this->error('请使用移动端打开');
		}
		//微信中获取用户信息自动注册	
		if(in_wechat()){
			wechat()->wechatAutoReg(wechat()->getOpenId());				
			
			if(!session('mobile_total')){
				session('mobile_total',osc_cart()->count_cart_total(user('uid')));
			}
				
		}
	}
}
?>