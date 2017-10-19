<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:38:"../oscshop/admin/view/index/index.html";i:1507895017;s:38:"../oscshop/admin/view/public/base.html";i:1508162033;}*/ ?>
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
						
<link rel="stylesheet" href="__ADMIN__/css/index.css" />	
<div class="row">
	<div class="col-xs-12">	
		    <div class="row">
		      <div class="col-lg-3 col-md-3 col-sm-6">
					<div class="tile">
						<div class="tile-heading">
							<?php echo lang('今日订单'); ?>
							<span class="pull-right"> <?php echo lang('总'); ?><?php echo $total_order; ?><?php echo lang('单'); ?></span>
						</div>
						<div class="tile-body">
							<i class="fa fa-shopping-cart"></i>
							<h2 class="pull-right"><?php echo $today_order; ?></h2>
						</div>
						<div class="tile-footer">
							<a href="<?php echo url('member/OrderBackend/index'); ?>"><?php echo lang('显示详细'); ?>...</a>
						</div>
					</div>
			  </div>
		      <div class="col-lg-3 col-md-3 col-sm-6">
					<div class="tile">
					<div class="tile-heading">
					<?php echo lang('今日销售额'); ?> 
					<span class="pull-right"> <?php echo lang('总'); ?><?php echo $total_money; ?> </span>
					</div>
					<div class="tile-body">
					<i class="fa fa-credit-card"></i>
					<h2 class="pull-right"><?php echo $today_money; ?></h2>
					</div>
					<div class="tile-footer">
					<a href="<?php echo url('member/OrderBackend/index'); ?>"><?php echo lang('显示详细'); ?>...</a>
					</div>
					</div> 	
		      </div>
		      <div class="col-lg-3 col-md-3 col-sm-6">
					<div class="tile">
					<div class="tile-heading">
					<?php echo lang('新增客户'); ?> 
					<span class="pull-right"> <?php echo lang('总'); ?><?php echo $member_count; ?><?php echo lang('个'); ?></span>
					</div>
					<div class="tile-body">
					<i class="fa fa-user"></i>
					<h2 class="pull-right"><?php echo $today_member; ?></h2>
					</div>
					<div class="tile-footer">
					<a href="<?php echo url('member/MemberBackend/index'); ?>"><?php echo lang('显示详细'); ?>...</a>
					</div>
					</div>
			  </div>
		      <div class="col-lg-3 col-md-3 col-sm-6">
					<div class="tile">
					<div class="tile-heading"><?php echo lang('新增用户行为'); ?>
					<span class="pull-right"> <?php echo lang('总'); ?><?php echo $user_action_total; ?><?php echo lang('个'); ?></span>	
					</div>
					<div class="tile-body">
					<i class="fa fa-eye"></i>
					<h2 class="pull-right"><?php echo $today_user_action; ?><?php echo lang('个'); ?></h2>
					</div>
					<div class="tile-footer">
					<a href="<?php echo url('admin/UserAction/index'); ?>"><?php echo lang('显示详细'); ?>...</a>
					</div>
					</div>	  
			  </div>
		    </div>
		    <div class="row">
		      <div class="col-lg-4 col-md-12 col-sm-12 col-sx-12">
				  	<div class="panel panel-default">
					<div class="panel-heading">
					<h3 class="panel-title">
					<i class="fa fa-calendar"></i>
					<?php echo lang('用户行为'); ?>
					<a style="font-size: 14px" href="<?php echo url('UserAction/index'); ?>" class="pull-right"><?php echo lang('更多'); ?>..</a>
					</h3>
					</div>
					<ul class="list-group">
						
						<?php if(is_array($user_action) || $user_action instanceof \think\Collection || $user_action instanceof \think\Paginator): $i = 0; $__LIST__ = $user_action;if( count($__LIST__)==0 ) : echo "$uc_empty" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
							<li class="list-group-item">
							<?php if(empty($v['uname'])): ?>	
								<a href="<?php echo url('member/MemberBackend/edit',array('id'=>$v['user_id'])); ?>"><?php echo $v['user_id']; ?></a>
							<?php else: ?>
								 <?php echo $v['uname']; endif; ?>
							
							<?php echo $v['info']; ?>
							<br>
							<small class="text-muted">						
								<?php echo date("Y-m-d H:i:s",$v['add_time']); ?>
							</small>
							</li>	
						<?php endforeach; endif; else: echo "$uc_empty" ;endif; ?>
					</ul>
					</div>
			  </div>
		      <div class="col-lg-8 col-md-12 col-sm-12 col-sx-12">
		      	<div class="panel panel-default">
		      		<div class="panel-heading">
						<h3 class="panel-title">
						<i class="fa fa-shopping-cart"></i>
						<?php echo lang('最新订单'); ?>
						<a style="font-size: 14px" href="<?php echo url('member/OrderBackend/index'); ?>" class="pull-right"><?php echo lang('更多'); ?>..</a>
						</h3>
					</div>
					<div class="table-responsive">
						<table class="table">
							<thead>
							<tr>
							<td><?php echo lang('订单号'); ?></td>
							<td><?php echo lang('支付方式'); ?></td>
							<th><?php echo lang('客户端'); ?></th> 
							<td><?php echo lang('客户名称'); ?></td>
							<td><?php echo lang('状态'); ?></td>
							<td><?php echo lang('生成日期'); ?></td>
							<td><?php echo lang('金额'); ?></td>
							<td><?php echo lang('操作'); ?></td>
							</tr>
							</thead>
							<tbody>								
								<?php if(is_array($order_list) || $order_list instanceof \think\Collection || $order_list instanceof \think\Paginator): $i = 0; $__LIST__ = $order_list;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
								<tr>
									<td>
										<?php echo $v['order_num_alias']; ?>
									</td>
									<td>
										<?php echo $v['payment_code']; ?>
									</td>
									<td>
										<?php echo $v['reg_type']; ?>
									</td>
									<td>
										<?php if($v['reg_type'] != 'weixin'): ?>
											<?php echo $v['username']; else: ?>
											<?php echo $v['nickname']; endif; ?>
									</td>
									<td>
										<?php echo $v['name']; ?>
									</td>
									<td>
										<?php echo date('Y-m-d H:i:s',$v['date_added']); ?>
									</td>
									<td>
										<?php if($v['points_order'] == 1): ?>
											<?php echo lang('积分'); ?> <?php echo $v['pay_points']; else: ?>
											&yen; <?php echo $v['total']; endif; ?>										
									</td>
									<td>
										<a  class="btn btn-info" href='<?php echo url("member/OrderBackend/show_order",array("id"=>$v["order_id"])); ?>'>
											<i class="fa-eye fa"></i>
										</a> 
									</td>
								</tr>
								<?php endforeach; endif; else: echo "$empty" ;endif; ?>	
							</tbody>
						</table>
						
					</div>
		      	</div>
		      	
		      	
			  </div>
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
