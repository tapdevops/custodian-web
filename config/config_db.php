<?php
	date_default_timezone_set('Asia/Jakarta');
	
	$database="custodian";
	$username = "root";
	$password = "tap123";
	$host="10.20.1.180";
	$mysqli = new mysqli($host,$username,$password,$database);
	
	$link=mysql_connect($host,$username,$password) or die ("could not connect: ".mysql_error());
	mysql_select_db($database, $link)  or exit('Error Selecting database: '.mysql_error()); 
?>