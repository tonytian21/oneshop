<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:42:"../oscshop/mobile/view/category/index.html";i:1507895017;s:39:"../oscshop/mobile/view/public/base.html";i:1506516777;s:41:"../oscshop/mobile/view/public/search.html";i:1507895017;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv=Content-Type content="text/html;charset=utf-8" />
    <title><?php echo (isset($SEO['title']) && ($SEO['title'] !== '')?$SEO['title']:''); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">    
    
    <script src="__PUBLIC__/js/jquery/jquery-1.10.2.min.js"></script>   
      
    
<link href="__PUBLIC__/view_res/mobile/css/category.css" type="text/css" rel="Stylesheet" />
<link href="__PUBLIC__/jquery-weui/dist/lib/weui.min.css" type="text/css" rel="Stylesheet" />
<link href="__PUBLIC__/jquery-weui/dist/css/jquery-weui.min.css" type="text/css" rel="Stylesheet" />
<script src="__PUBLIC__/jquery-weui/dist/js/jquery-weui.min.js"></script>

    
</head>
<body>

<form class="search-w-box" onsubmit='searchdo(this);return false;'>
	<input type="search" name="search" id="searchBox" targ="<?php echo url('search/index'); ?>" value="<?php echo input('param.search'); ?>"  class="search-w-input" placeholder="<?php echo lang('搜一搜，找到你想要的'); ?>" /> 
</form>
<script>
function searchdo(form) {
    var search = $('input[type=search]', form);
    if (search.val() === '')
    return;            
 	var url = search.attr('targ');
 	
    var query = $('.search-w-box').find('input,select').serialize();
    query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
    query = query.replace(/^&/g, '');
    if (url.indexOf('?') > 0) {
        url += '&' + query;
    } else {
        url += '?' + query;
    }
    window.location.href = url;            
}	
</script>


<input type="hidden" value="0" id="cat" />
<div class="clearfix" id="viewCat">
    <div id="viewCatLeft">
    	<?php if(is_array($category) || $category instanceof \think\Collection || $category instanceof \think\Paginator): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($i % 2 );++$i;?>
        <div class="viewCatTopItem Elipsis" data-catid="<?php echo $d['id']; ?>"><?php echo $d['name']; ?></div>                
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div id="viewCatRight"></div>
</div>





</body>

<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?32e3cb4cf7e5c6772c4dc1f5d496b6af";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>



<script>
        // <?php echo lang('对应分类'); ?>id
        var cat = $('#cat').val() !== '' && parseInt($('#cat').val()) !== 0 ? parseInt($("#cat").val()) : parseInt($('.viewCatTopItem').eq(0).attr('data-catid'));

        // <?php echo lang('左侧按钮点击'); ?>
        $('.viewCatTopItem').bind({'touchend touchcancel mouseup': function (event) {
                var node = $(this);
                event.preventDefault();
                if (cat !== node.attr('data-catid')) {
                    cat = node.attr('data-catid');
                    
                    $.showLoading();
                    
                    fnLoadCatlist(cat);
                    
                    setTimeout(function (){
				        $.hideLoading();		     
				    }, 500);
				    
                    $('.viewCatTopItem.hov').removeClass('hov');
                    node.addClass('hov');
                }
            }});

        // window resizer
        $(window).bind('resize', function () {
            // <?php echo lang('调整高度'); ?>
            $('#viewCatRight').height($(window).height() - $('.search-w-box').height() - 35);
            $('#viewCatLeft').height($(window).height() - $('.search-w-box').height() - 20);
            $('#whiteWrap').height($('#viewCatRight').height() + 35);
            // <?php echo lang('调整圆图标宽高'); ?>
            $('.subcat_item').each(function () {
                $(this).css({
                    'height': $(this).width() + 25 + 'px'
                });
            });
        }).resize();

        // <?php echo lang('默认'); ?>load<?php echo lang('第一个分类的列表'); ?>
        $('.viewCatTopItem[data-catid="' + cat + '"]').eq(0).addClass('hov');
        fnLoadCatlist(cat);

        /**
         *<?php echo lang('列表加载函数'); ?>
         * @param {type} cat
         * @returns {undefined}
         */
      
        function fnLoadCatlist(cat) {
            if ($('#whiteWrap').length === 0) {
                $('#viewCatRight').append('<div id="whiteWrap"></div>');
            }
            $('#whiteWrap').height($('#viewCatRight').height() + 35);            
            
            $('#viewCatRight').load("<?php echo url('category/get_goods'); ?>" +'/id/'+ cat,
                    function () {
                        // <?php echo lang('调整圆图标宽高'); ?>
                        $('.subcat_item img').each(function () {
                            $(this).css({
                                'height': $(this).width() + 'px'
                            });
                        });
                        $('.subcatBrand').width(($('#viewCatRight').width() / 2) - 15);
                    });
                  
        }
</script>

</html>