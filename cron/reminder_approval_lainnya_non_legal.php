<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																			=
= Dibuat Tanggal	: 11 Okt 2018																						=
= Update Terakhir	: -           																						=
= Revisi			:																									=
=========================================================================================================================
*/
?>
<HTML>
<HEAD>
	<title>Custodian System | Reminder Approval</title>
</HEAD>
<BODY>
<?PHP
include_once ("./config/config_db.php");
include_once ("./include/function.mail.regdoconl.php");
include_once ("./include/function.mail.lodoconl.php");
include_once ("./include/function.mail.reldoconl.php");
include_once ("./include/function.mail.retdoconl.php");

echo "REMINDER APPROVAL UNTUK TRANSAKSI BERIKUT :<br>";
//reminder registrasi dokumen
$query="SELECT DISTINCT THROONLD_RegistrationCode
		FROM TH_RegistrationOfOtherNonLegalDocuments
		WHERE THROONLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "REGISTRATION:<br>";
	echo "$arr[THROONLD_RegistrationCode]<br>";
	mail_registration_doc($arr['THROONLD_RegistrationCode'],'1');
}

//reminder permintaan dokumen
$query="SELECT DISTINCT THLOONLD_LoanCode
		FROM TH_LoanOfOtherNonLegalDocuments
		WHERE THLOONLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>LOAN:<br>";
	echo "$arr[THLOONLD_LoanCode]<br>";
	mail_loan_doc($arr['THLOONLD_LoanCode'],'1');
}

//reminder pengeluaran dokumen
$query="SELECT DISTINCT THROONLD_ReleaseCode
		FROM TH_ReleaseOfOtherNonLegalDocuments
		WHERE THROONLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RELEASE:<br>";
	echo "$arr[THROONLD_ReleaseCode]<br>";
	mail_release_doc($arr['THROONLD_ReleaseCode'],'1');
}

//reminder pengembalian dokumen
$query="SELECT DISTINCT TDRTOONLD_ReturnCode
		FROM TD_ReturnOfOtherNonLegalDocuments
		WHERE TDRTOONLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RETURN:<br>";
	echo "$arr[TDRTOONLD_ReturnCode]<br>";
	mail_return_doc($arr['TDRTOONLD_ReturnCode'],'1');
}
?>
</BODY>
</HTML>
