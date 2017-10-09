<?php
return [
	/***********框架系统配置*************/
	//'app_trace' =>  true,
	'app_debug' => true,
	'default_module'=> 'index',	
    'url_route_on' => true,
	'url_route_must'=>  false,	
	// 是否自动转换URL中的控制器和操作名
    'url_convert'            => false,
	// 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'htmlspecialchars',
	
    'view_replace_str'=>[
	    '__PUBLIC__'=>'/static',
	    '__RES__'=>'/static/view_res',
	    '__ADMIN__' =>'/static/view_res/admin',
	    'IMG_ROOT'=>'/'
	],		
	'session'                => [
		'type'           => '',
		'auto_start'     => true,
	],	
	'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => 'osc_',        
        // cookie 保存路径
        'path'      => '/'       
    ],
		
	'URL_HTML_SUFFIX'=>'',
	//默认错误跳转对应的模板文件
	'dispatch_error_tmpl' => APP_PATH.'common/view/public/error.tpl',
	//默认成功跳转对应的模板文件
	'dispatch_success_tmpl' => APP_PATH.'common/view/public/success.tpl',	
	//'配置项'=>'配置值'
    'LANG_SWITCH_ON'     =>     true,    //开启语言包功能        
    'LANG_AUTO_DETECT'     =>     true, // 自动侦测语言
    'DEFAULT_LANG'         =>     'zh-cn', // 默认语言        
    'LANG_LIST'            =>    'en-us,zh-cn', //必须写可允许的语言列表
    'VAR_LANGUAGE'     => 'l', // 默认语言切换变量
];
