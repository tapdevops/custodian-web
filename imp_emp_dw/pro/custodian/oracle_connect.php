<?php
 
/**
 * A class file to connect to database
 */

function __construct() {
    // connecting to database
    $this->connect();
}

function __destruct() {
    // closing db connection
    $this->close();
}

function connect() {
	//$vLocation = "C:/xampp/htdocs/imp_emp_dw/pro/custodian"; // khusus offline pakai ini
	$vLocation = "";
    include($vLocation."config.oracle.php");

    // Connecting to mysql database
    $con = oci_connect(ORACLE_DB_USER, ORACLE_DB_PASSWORD, ORACLE_DB_SERVER.'/'.ORACLE_DB_DATABASE) or die ('Connection Failed');

    // returing connection cursor
    return $con;
}

function close() {
    // closing db connection
    oci_close($con);
}

?>