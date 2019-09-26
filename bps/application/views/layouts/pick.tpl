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
<link rel="stylesheet" href="css/pick.css" type="text/css" media="screen" />
<?php echo $this->headLink() . "\n"; ?>
<script type="text/javascript" src="js/global.js"></script>
<!-- scripts -->
<script type="text/javascript">
    $(document).ready(function() {
        if (window.opener === null) {
            self.location = "index/main";
        }
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
