<?php
	date_default_timezone_set('Asia/Jakarta');

	$host = "10.20.1.180";
	// $username = "custodian";
	$username = 'root';
	$password = "tap123";
	$databasename = "custodian";

	$link=mysql_connect($host,$username,$password) or die ("could not connect: ".mysql_error());
	$link = mysql_connect($host,$username,$password) or die ("Cannot Connect To Database");
	mysql_select_db($databasename,$link);

?>
