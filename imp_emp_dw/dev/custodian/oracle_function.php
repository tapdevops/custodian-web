<?php

	function insert_data($conn,$query) {
		$stmt = oci_parse($conn,$query);
		oci_execute($stmt, OCI_DEFAULT);
		oci_free_statement($stmt);
		//echo $conn . " inserted \n\n";
	}

	function update_data($conn,$query) {
		$stmt = oci_parse($conn,$query);
		oci_execute($stmt, OCI_DEFAULT);
		oci_free_statement($stmt);
		//echo $conn . " updated \n\n";
	}

	function delete_data($conn,$query) {
		$stmt = oci_parse($conn,$query);
		oci_execute($stmt, OCI_DEFAULT);
		oci_free_statement($stmt);
		//echo $conn . " deleted \n\n";
	}

	function num_rows($conn,$query) {
		$stmt = oci_parse($conn,$query);
		oci_execute($stmt, OCI_DEFAULT);
		$row = oci_num_rows($stmt);
		oci_free_statement($stmt);
		//echo $conn . " deleted \n\n";
		return $row;
	}

	function commit($conn) {
		oci_commit($conn);
		//echo $conn . " committed\n\n";
	}

	function rollback($conn) {
		oci_rollback($conn);
		//echo $conn . " rollback\n\n";
	}

	function select_data($conn,$query) {
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$row = oci_fetch_array($stid, OCI_ASSOC);
		oci_free_statement($stid);
		return $row;
	}

	function select_all_data($conn, $query) {
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		$output = oci_fetch_all($stid, $res, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
		return $res;
	}

	function oracle_query($conn,$query) {
		$stid = oci_parse($conn, $query);
		oci_execute($stid);
		return $stid;
	}

	function replace_dot($bcc) {
		$result = str_replace(".","",$bcc,$i);
		return $result;
	}

?>