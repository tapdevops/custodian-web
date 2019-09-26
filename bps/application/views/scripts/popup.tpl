<script type="text/javascript">
var oWindow = null;
var arr = location.href.toString().split("/");
var strBase = arr[0] + "//" + arr[2] + "/" + arr[3];
$(document).ready(function() {
    $(window).bind("beforeunload", function(e) {
        if (oWindow && !oWindow.closed) {
            oWindow.close();
        }
    });
});
function popup(url, name, xwidth, xheight) {
    var width   = (typeof xwidth == "undefined") ? <?php echo $this->width; ?> : xwidth;
    var height  = (typeof xheight == "undefined") ? <?php echo $this->height; ?> : xheight;
    var left    = (screen.width-width)/2;
    var top     = (screen.height-height)/2;
    var options = "menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=no," +
                  "left="+left+",top="+top+",width="+width+",height="+height;
    var page = strBase + "/" + url;
    oWindow = window.open(page, name, options);
    oWindow.focus();
}
function popup_full(url, name) {
	var params  = 'width='+screen.width;
	    params += ', height='+screen.height;
	    params += ', top=0, left=0'
		params += ', scrollbars=yes'
	    params += ', fullscreen=yes';
	var page = strBase + "/" + url;	

	oWindow=window.open(page, name, params);
    oWindow.focus();
}
</script>
