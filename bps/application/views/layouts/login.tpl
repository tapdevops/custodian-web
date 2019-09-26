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
<link rel="stylesheet" href="css/login.css" type="text/css" media="screen" />

<!-- scripts -->

</head>
<body>
<div class="page">
    <?php echo $this->layout()->content; ?>
</div>
<div class="footer">
	<div class="footer-container">
		<div>
			<b>Budgeting and Planning System</b><BR>
			Version 4.0.0<br />
			Copyright &copy; <?php echo date('Y'); ?> PT Triputra Agro Persada<br />
			All Rights Reserved.
		</div>
		<div style="clear:both;"></div>
	</div>
</div>
</body>
</html>
