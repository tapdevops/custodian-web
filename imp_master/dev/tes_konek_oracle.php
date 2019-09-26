<?php
$username = 'TAP_DW';
$password = 'tapdw123#';
$dbname = '10.20.1.103/tapdw';
//$password = 'DBLINKADM123';
//$dbname = '10.0.99.150/HRDEV';
$c = oci_connect($username, $password, $dbname);
if (!$c) {
	//echo 'Koneksi ke server database gagal dilakukan';
	//exit();
	    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);

} else {
	echo 'Koneksi ke server database sukses';
}
echo '<br />';
?>