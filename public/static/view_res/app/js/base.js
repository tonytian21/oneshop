if(!mui.plugin) {
	mui.plugin = {};
}

$.each($(".mui-progressbar"), function() {
	mui(this).progressbar().setProgress($(this).attr("data-progress"));
});
if(window.Swiper) {
	new Swiper('.swiper-container', {
		autoplay: 5000,
		// 可选选项，自动滑动
		pagination: '.swiper-pagination',
		paginationClickable: true,
	});
}

$("nav.mui-bar-tab .mui-tab-item").each(
	function() {
//		alert(location.pathname.indexOf($(this).attr("href").split(".html")[0]));

		var hr=$(this).attr("href").split("/");
		hr=hr[hr.length-1].split(".")[0];
		if(location.pathname.indexOf(hr) > -1) {
			$(this).addClass("mui-active");
			return;
		}
	}
).click(function() {
	location.href = $(this).attr("href");
});
$(".mui-action-to-search").click(function () {
	
	$(".search-page").removeClass("hide");
});
$(".mui-action-back-hoem").click(function () {
	
	$(".search-page").addClass("hide");
});


$(".mui-action-user").click(function () {
	location="user/home.html"
});
$(".mui-action-home").click(function () {
	location="../index.html"
})

if($("#notice-wrapper").length > 0) {

	(function() {
		// 公告
		var speed = 60
		var notice = document.getElementById("notice-wrapper");
		var noticeitem = document.getElementById("notice-item-a");
		document.getElementById("notice-item-b").innerHTML = noticeitem.innerHTML;

		function marquee() {

			if(notice.scrollTop >= noticeitem.offsetHeight) {
				notice.scrollTop = 0;

			} else {
				notice.scrollTop++;

			}
		}
		var myMar = setInterval(marquee, speed)
		notice.onmouseover = function() {
			clearInterval(myMar)
		}
		notice.onmouseout = function() {
			myMar = setInterval(marquee, speed)
		}

	})();
}