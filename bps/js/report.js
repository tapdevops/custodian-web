$(document).ready(function(){
	resizeIframe();
	$("#load").show();
	$("#ifrm").bind("load",function() {
		$("#load").hide();
	});
});

$(window).resize(function() {
	resizeIframe();
});

function resizeIframe(){
	$("#ifrm").width($(window).width());
	$("#ifrm").height($(window).height());
}