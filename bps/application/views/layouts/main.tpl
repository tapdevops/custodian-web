<?php echo $this->doctype() . "\n"; ?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<base href="<?php echo $this->baseUrl; ?>" />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
<title>Budgeting and Planning System</title>
<!-- jquery -->
<?php if ($this->jQuery()->isEnabled()) { echo $this->jQuery(); } ?>
<!-- styles -->
<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
<?php echo $this->headLink() . "\n"; ?>
<!-- scripts -->
<?php echo $this->headScript() . "\n"; ?>
<!-- sooperfish-menu -->
<link rel="stylesheet" href="css/sooperfish.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/sooperfish-theme-custom.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery.easing-sooper.js"></script>
<script type="text/javascript" src="js/jquery.sooperfish.min.js"></script>
<script type="text/javascript" src="js/global.js"></script>
</head>
<body>
<div class="page">
	<div style='height:8px; background:#000'>&nbsp;</div>
	<div class="header">
		<div class="header-container">
			<div style="float:right;width:20%;text-align:right;">
				<span class="subtitle" title="Nama User"><?php echo $this->userInfo['username']; ?></span>
				<BR />
				<span class="subtitle" title="Nama Grup">as <?php echo $this->userInfo['grupname']; ?></span>
				<br /><br />
				<span class="subtitle" title="Tanggal"><?php echo date('l, j F Y'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
	<div class="menu">
		<div class="menu-container">
			<?php echo $this->render('sooperfish-menu.tpl'); ?>
		</div>
    </div>
	
	<div class="content">
		<div class="content-container">
			<?php if (!empty($this->title)) { ?>
			<span class="title"><?php echo $this->title; ?></span><br /><br />
			<?php } ?>
			<?php echo $this->layout()->content; ?>
		</div>
    </div>
	
	<div class="footer">
		<div class="footer-container">
			<div style="float:left;width:50%;">
				<b>Budgeting and Planning System</b><BR>
				Version 4.0.0<br />
				Copyright &copy; <?php echo date('Y'); ?> PT Triputra Agro Persada<br />
				All Rights Reserved.
			</div>
			<div style="float:right;width:50%;text-align:right;">
				&nbsp;
			</div>
			<div style="clear:both;"></div>
		</div>
    </div>
</div>
<script type="text/javascript">
var bShowProgress = true;
$(document).ready(function() {
    $("body").ajaxStart(function() {
        if (bShowProgress) {
            $("<div />").addClass("overlay").appendTo("body").show();
            $("<div />").addClass("modal").appendTo("body");
        }
    });
    $("body").ajaxStop(function() {
        if (bShowProgress) {
            $(".overlay").remove();
            $(".modal").remove();
        }
    });
});
</script>
</body>
</html>
