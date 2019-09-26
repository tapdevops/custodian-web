<?php
$filename = $_GET['filename'] ;

if($filename) {
	if (unlink($filename))	$msg = "Sukses Delete File yang diupload";
	else $error = "File tidak dapat dihapus";
}else{
	$error = "File tidak ditemukan";
}


echo "{";
echo				"error: '" . $error . "',\n";
echo				"msg: '" . $msg . "'\n";
echo "}";


?>