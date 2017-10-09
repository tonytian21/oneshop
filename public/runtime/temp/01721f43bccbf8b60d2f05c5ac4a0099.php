<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:40:"../oscshop/install/view/index/index.html";i:1505885316;s:40:"../oscshop/install/view/public/base.html";i:1505885316;}*/ ?>
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
            
    <li class="active"><a href="javascript:;">1.安装协议</a></li>
    <li><a href="javascript:;">2.环境检测</a></li>
    <li><a href="javascript:;">3.创建数据库</a></li>
    <li><a href="javascript:;">4.安装</a></li>
    <li><a href="javascript:;">5.完成</a></li>

        </ul>
        <div>

        </div>
        <div class="article">
            

    <div class="container">


    </div>

    <header>
        <h1 class="text-center">__NAME__ 安装协议</h1>

        <section class="abstract">版权所有 (c) 2015-<?php echo date('Y',time()); ?>，__COMPANY__保留所有权利。</section>
    </header>
    <section class="article-content" style="text-indent: 2em">
        <p>__NAME__系__COMPANY__基于<a target="_blank" href="http://www.thinkphp.cn">thinkphp5</a>开发的B2C商城。感谢顶想公司为__NAME__提供内核支持。</p>

        <p>感谢您选择__NAME__，本系统遵循Apache2开源协议发布。Apache Licence是著名的非盈利开源组织Apache采用的协议。该协议和BSD类似，鼓励代码共享和尊重原作者的著作权，同样允许代码修改，再作为开源或商业软件发布。希望我们的努力可以为您创造价值。官方网站网址为   <a href="http://__WEBSITE__" target="_blank">http://__WEBSITE__</a>。</p>
        
    </section>




            <div>
                
    <a class="btn btn-primary btn-block btn-large" href="<?php echo url('Install/step1'); ?>">同意安装协议</a>
    <a class="btn btn-default  btn-large btn-block" style="background: white;" href="http://__WEBSITE__">不同意</a>

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
