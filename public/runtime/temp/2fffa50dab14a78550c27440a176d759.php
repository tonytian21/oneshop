<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:42:"../oscshop/mobile/view/category/goods.html";i:1505885316;}*/ ?>
<div style="margin: 0 10px;"> </div>

<?php if(is_array($goods) || $goods instanceof \think\Collection || $goods instanceof \think\Paginator): $i = 0; $__LIST__ = $goods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($i % 2 );++$i;?>
	<section class="productListWrap hoz" onclick="location='<?php echo url('goods/detail',array('id'=>$d['goods_id'])); ?>';">
		<a class="productList clearfix">
			<img src="IMG_ROOT<?php echo resize($d['image'],200,200); ?>" />
			<section>
				<title class="title"><?php echo $d['name']; ?></title>
				<span class="prices">¥<?php echo $d['price']; ?></span>
			</section>			
		</a>		
	</section>
<?php endforeach; endif; else: echo "" ;endif; ?>