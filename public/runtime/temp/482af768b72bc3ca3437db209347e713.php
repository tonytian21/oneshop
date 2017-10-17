<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:52:"../oscshop/mobile/view/agent_backend/agent_list.html";i:1507895017;s:38:"../oscshop/admin/view/public/base.html";i:1508162033;}*/ ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo \think\Config::get('SITE_NAME'); ?>-<?php echo lang('后台管理中心'); ?></title>

		<meta name="description" content="<?php echo \think\Config::get('SITE_NAME'); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="__PUBLIC__/js/bt/bootstrap.min.css" />
		<link rel="stylesheet" href="__ADMIN__/ace/font-awesome/4.2.0/css/font-awesome.min.css" />


		<!-- ace styles -->
		<link rel="stylesheet" href="__ADMIN__/ace/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="__ADMIN__/ace/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="__ADMIN__/ace/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!-- ace settings handler -->
		<script src="__ADMIN__/ace/js/ace-extra.min.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="__ADMIN__/ace/js/html5shiv.min.js"></script>
		<script src="__ADMIN__/ace/js/respond.min.js"></script>
		<![endif]-->
		<style>
			.table thead > tr > td, .table tbody > tr > td {
			    vertical-align: middle;
			}	
			.table thead td span[data-toggle="tooltip"]:after, label.control-label span:after {
				font-family: FontAwesome;
				color: #1E91CF;
				content: "\f059";
				margin-left: 4px;
			}
		</style>
		
		
			
		
		
		
		<script src="__PUBLIC__/js/jquery/jquery-2.0.3.min.js"></script>
		<script src="__PUBLIC__/js/jquery/jquery-migrate-1.2.1.min.js"></script>
			
		
		<script src="__PUBLIC__/artDialog/artDialog.js"></script>
		<link href="__PUBLIC__/artDialog/skins/default.css" rel="stylesheet" type="text/css" />			
		
		<script>
			var filemanager_url="<?php echo url('admin/FileManager/index'); ?>";
		</script>
		<script src="__PUBLIC__/js/oscshop_common.js"></script>
	</head>

	<body class="no-skin">
		<div id="navbar" class="navbar navbar-default    navbar-collapse       h-navbar">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="<?php echo url('admin/Index/index'); ?>" class="navbar-brand">
						<small>							
							<?php echo \think\Config::get('SITE_NAME'); ?> <?php echo lang('后台管理'); ?>
						</small>
					</a>
					<button class="pull-right navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#sidebar">
						<span class="sr-only">Toggle sidebar</span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>
					</button>
				</div>

				<div class="navbar-buttons navbar-header pull-right  collapse navbar-collapse" role="navigation">
					<ul class="nav ace-nav">

						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								
								<span class="user-info">
									<?php echo session('user_auth.username'); ?>
								</span>

								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								
								<li>
									<a target="_blank" href="<?php echo \think\Request::instance()->root(true); ?>"><?php echo lang('网站前台'); ?></a>
								</li>
								
								<li>
									<a href="<?php echo url('admin/User/edit',array('id'=>session('user_auth.uid'))); ?>"><?php echo lang('修改密码'); ?></a>
								</li>
								
								<li><a href="<?php echo url('admin/Index/clear'); ?>"><?php echo lang('清空缓存'); ?></a></li>

								<li>
									<a href="<?php echo url('admin/Index/logout'); ?>"><?php echo lang('退出系统'); ?></a>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			
			</div><!-- /.navbar-container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
			
			<div id="sidebar" class="sidebar h-sidebar navbar-collapse collapse">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>
				
				<ul class="nav nav-list">
					<li class="hover">
						<a target="_blank" href="<?php echo \think\Request::instance()->root(true); ?>">
							<i class="menu-icon fa fa fa-home fa-lg"></i>
							<span class="menu-text"><?php echo lang('前台'); ?> </span>
							<b class="arrow fa fa-angle-down"></b>
						</a>
						<b class="arrow"></b>
					</li>
					
					<?php if(is_array($admin_menu) || $admin_menu instanceof \think\Collection || $admin_menu instanceof \think\Paginator): $i = 0; $__LIST__ = $admin_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?>					
					<li class="hover <?php if(isset($breadcrumb1) AND ($breadcrumb1 == $m["title"])): ?> active open <?php endif; ?>">
						<a href="#" class="dropdown-toggle">
							<i class="menu-icon fa <?php echo $m['icon']; ?>"></i>
							<span class="menu-text"> <?php echo $m['title']; ?> </span>
							<b class="arrow fa fa-angle-down"></b>
						</a>

						<b class="arrow"></b>
						<?php if(isset($m['children'])): ?>
						<ul class="submenu">
							
							<?php if(is_array($m['children']) || $m['children'] instanceof \think\Collection || $m['children'] instanceof \think\Paginator): if( count($m['children'])==0 ) : echo "" ;else: foreach($m['children'] as $key=>$vo): ?>   
							<li class="hover">
								<a href="<?php echo \think\Request::instance()->root(true); ?>/<?php echo $vo['url']; ?>">
									<i class="menu-icon fa fa-caret-right"></i>
									<?php echo $vo['title']; if(isset($vo['children'])): ?>
										<b class="arrow fa fa-angle-down"></b>
									<?php endif; ?>
								</a>

								<b class="arrow"></b>
								
									<?php if(isset($vo['children'])): ?>
										<ul class="submenu">
											<?php if(is_array($vo['children']) || $vo['children'] instanceof \think\Collection || $vo['children'] instanceof \think\Paginator): if( count($vo['children'])==0 ) : echo "" ;else: foreach($vo['children'] as $key=>$v2): ?> 
												<li class="hover">
													<a href="<?php echo \think\Request::instance()->root(true); ?>/<?php echo $v2['url']; ?>">
														<i class="menu-icon fa fa-caret-right"></i>
														<?php echo $v2['title']; ?>
													</a>
			
													<b class="arrow"></b>
												</li> 
											<?php endforeach; endif; else: echo "" ;endif; ?> 
										</ul>	
									<?php endif; ?>
								
							</li>
							<?php endforeach; endif; else: echo "" ;endif; ?>
							
						</ul>
						<?php endif; ?>
					</li>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					
				</ul><!-- /.nav-list -->

				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
				</script>
			</div>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="page-content">
						
<div class="page-header">
	
<h1>	
	<?php echo lang('移动端'); ?>
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		<?php echo $breadcrumb1; ?>
	</small>
	<small>
		<i class="ace-icon fa fa-angle-double-right"></i>
		<?php echo lang('代理管理'); ?>
	</small>
</h1>	

</div>	
	
<div class="row">
	<div class="col-xs-12">	
		<div class="table-responsive">
			<table id="table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						
						<th><?php echo lang('姓名'); ?></th> 
						<th><?php echo lang('级别'); ?></th> 
						<th><?php echo lang('电话'); ?></th> 
						<th><?php echo lang('加入时间'); ?></th>
						<th><?php echo lang('返点'); ?></th>
						<th><?php echo lang('名下会员'); ?></th>
						<th><?php echo lang('成交'); ?></th>						
						<th><?php echo lang('本月收益'); ?></th>								
						<th><?php echo lang('总收益'); ?></th>	
						<th><?php echo lang('未结算'); ?></th>	
						<th><?php echo lang('状态'); ?></th>	
						<th><?php echo lang('操作'); ?></th>
					</tr>
				</thead>
				<tbody>
						<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
						<tr>		
							<td><?php echo $v['name']; ?></td>
							<td>
								<?php echo get_agent_level_info($v['agent_level'],'name'); ?>
							</td>
							<td><?php echo $v['tel']; ?></td>
							<td><?php echo date('Y-m-d',$v['create_time']); ?></td>
							<td><?php echo num_to_percent($v['return_percent']); ?></td>
							<td><a href="<?php echo url('AgentBackend/sub_member',array('uid'=>$v['uid'])); ?>"> <?php echo (isset($v['member']) && ($v['member'] !== '')?$v['member']:'0'); ?><?php echo lang('人'); ?> </a></td>
							<td><a href="<?php echo url('AgentBackend/sub_order',array('uid'=>$v['uid'])); ?>"> <?php echo (isset($v['order']) && ($v['order'] !== '')?$v['order']:'0'); ?><?php echo lang('笔'); ?> </a></td>
							<td><span class="red bold"><?php echo lang('￥'); ?><?php echo (isset($v['month']) && ($v['month'] !== '')?$v['month']:'0.00'); ?></span></td>
							<td><span class="red bold"><?php echo lang('￥'); ?><?php echo (isset($v['total_bonus']) && ($v['total_bonus'] !== '')?$v['total_bonus']:'0.00'); ?></span></td>
							<td><span class="red bold"><?php echo lang('￥'); ?><?php echo (isset($v['no_cash']) && ($v['no_cash'] !== '')?$v['no_cash']:'0.00'); ?></span></td>
							<td>
								<?php switch($v['status']): case "1": ?><span class="green bold"><?php echo lang('开启'); ?></span><?php break; case "0": ?><span class="red bold"><?php echo lang('关闭'); ?></span><?php break; endswitch; ?>
							</td>
							<td>
								<a href="<?php echo url('AgentBackend/edit_agent',array('id'=>$v['agent_id'])); ?>"><?php echo lang('编辑'); ?></a>
							</td>
						</tr>
						<?php endforeach; endif; else: echo "$empty" ;endif; ?>	
						
						<tr>
							<td colspan="20" class="page"><?php echo $page->render(); ?></td>
						</tr>
						<tr>
							<td colspan="20" class="page"><?php echo lang('总计'); ?> <?php echo ($page->total() ?: "0"); ?> <?php echo lang('个代理'); ?></td>
						</tr>
				</tbody>
				
			</table>
		</div>
	</div>
</div>

					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->



			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if IE]>
		<script type="text/javascript">
		 window.jQuery || document.write("<script src='__ADMIN__/ace/js/jquery1x.min.js'>"+"<"+"/script>");
		</script>
		<![endif]-->

		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='__ADMIN__/ace/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script type="text/javascript">
		 window.jQuery || document.write("<script src='__ADMIN__/ace/js/jquery1x.min.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='__ADMIN__/ace/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="__PUBLIC__/js/bt/bootstrap.min.js"></script>

		<!-- ace scripts -->
		<script src="__ADMIN__/ace/js/ace-elements.min.js"></script>
		<script src="__ADMIN__/ace/js/ace.min.js"></script>

		
								
		
	</body>
</html>
