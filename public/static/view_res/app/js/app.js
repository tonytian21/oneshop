if(!mui.plugin) {
	mui.plugin = {};
}
mui.init({
	swipeBack: false,
	statusBarBackground: '#ff6600',
	preloadLimit: 5,
	subpages: [{
		url: 'home.html',
		id: 'home',
		styles: {
			top: '45px', //mui标题栏默认高度为45px；
			bottom: '48px' //默认为0px，可不定义；
		}
	}, {
		url: 'glist.html',
		id: 'glist.html',
		styles: {
			top: '45px', //mui标题栏默认高度为45px；
			bottom: '48px' //默认为0px，可不定义；
		}
	}]
});





$(".mui-bar-tab .mui-tab-item").click(function() {
	$("#home").attr("src", $(this).attr("href"));
});