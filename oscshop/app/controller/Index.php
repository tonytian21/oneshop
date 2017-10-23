<?php
namespace osc\app\controller;
use think\Db;
class Index extends AppBase
{
 	function index(){
		cookie('jump_url',request()->url(true));
		
		$this->assign('SEO',['title'=>config('SITE_TITLE'),'keywords'=>config('SITE_KEYWORDS'),'description'=>config('SITE_DESCRIPTION')]);
		
		$this->assign('flag','index');
		
		if(in_wechat())
			$this->assign('signPackage',wechat()->getJsSign(request()->url(true)));	
		
		//获取广告

		//获取购物车商品数量
		
        return $this->fetch('index');
    }

    /**
     * 获取广告
     */
    function GetAdvert(){

    }

    /**
     * 获取公告
     */
    function GetNotice(){

    }

    /**
     * 根据查询条件获取不同的展示商品,支持分页获取
     */
    function GetProduct(){

    }
}
