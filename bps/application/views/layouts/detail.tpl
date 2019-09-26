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
<link rel="stylesheet" href="css/detail.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/global.js"></script>
<?php echo $this->headLink() . "\n"; ?>
<!-- scripts -->
<script type="text/javascript">
$(document).ready(function() {
    if (window.opener === null) {
        self.location = "index/main";
    }
    $("body").ajaxStart(function() {
        $("<div />").addClass("overlay").appendTo("body").show();
        $("<div />").addClass("modal").appendTo("body");
    });
    $("body").ajaxStop(function() {
        $(".overlay").remove();
        $(".modal").remove();
    });
});
</script>
<?php echo $this->headScript() . "\n"; ?>
</head>
<body>
<div class="page">
    <div class="content">
        <?php if (!empty($this->title)) { ?>
        <span class="title"><?php echo $this->title; ?></span><br /><br />
        <?php } ?>
        <?php echo $this->layout()->content; ?>
    </div>
</div>
</body>
</html>
