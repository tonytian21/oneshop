<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:42:"../oscshop/install/view/install/step3.html";i:1505885316;s:40:"../oscshop/install/view/public/base.html";i:1505885316;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>安装</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- zui -->
    <link href="__RES__/install/zui.css" rel="stylesheet">
    <script src="__PUBLIC__/js/jquery/jquery-1.10.2.min.js"></script>
</head>

<body style="background:  rgb(230, 234, 234)">
<div class="container" style="background: white;margin-top: 50px;margin-bottom: 50px;width:800px">
    <div class="with-padding row">
        <ul class="nav nav-secondary">
            
    <li class="active"><a href="javascript:;">安装协议</a></li>
    <li class="active"><a href="javascript:;">环境检测</a></li>
    <li class="active"><a href="javascript:;">创建数据库</a></li>
    <li class="active"><a href="javascript:;">安装</a></li>
    <li><a href="javascript:;">完成</a></li>
    <script src="__PUBLIC__/artDialog/artDialog.js"></script>
	<link href="__PUBLIC__/artDialog/skins/default.css" rel="stylesheet" type="text/css" />	

        </ul>
        <div>

        </div>
        <div class="article">
            
    <h1>安装</h1>
    <div id="show-list" class="install-database">
    </div>
    <script type="text/javascript">
       $(function(){
       	
       	art.dialog({				
			  	id:'install',
				title: '安装中',
			    lock: true,	
			   
			});
       	
       	$.post(
       		'<?php echo url("Install/insert_db"); ?>',
       		function(d){
       			if(d.success){
       				location=d.url;
       			}
       		}
       	);
       /*	*/
       });
    </script>


            <div>
                
    <button class="btn btn-warning btn-large btn-block disabled">正在安装，请稍候...</button>

            </div>
        </div>
    </div>
    <style>
        body{
            font-family: Arial, "Microsoft Yahei",'新宋体';
        }
    </style>

</div>

</body>
</html>
