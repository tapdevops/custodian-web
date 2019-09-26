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
include_once ("./include/function.mail.regdocol.php");
include_once ("./include/function.mail.lodocol.php");
include_once ("./include/function.mail.reldocol.php");
include_once ("./include/function.mail.retdocol.php");

echo "REMINDER APPROVAL UNTUK TRANSAKSI BERIKUT :<br>";
//reminder registrasi dokumen
$query="SELECT DISTINCT THROOLD_RegistrationCode
		FROM TH_RegistrationOfOtherLegalDocuments
		WHERE THROOLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "REGISTRATION:<br>";
	echo "$arr[THROOLD_RegistrationCode]<br>";
	mail_registration_doc($arr['THROOLD_RegistrationCode'],'1');
}

//reminder permintaan dokumen
$query="SELECT DISTINCT THLOOLD_LoanCode
		FROM TH_LoanOfOtherLegalDocuments
		WHERE THLOOLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>LOAN:<br>";
	echo "$arr[THLOOLD_LoanCode]<br>";
	mail_loan_doc($arr['THLOOLD_LoanCode'],'1');
}

//reminder pengeluaran dokumen
$query="SELECT DISTINCT THROOLD_ReleaseCode
		FROM TH_ReleaseOfOtherLegalDocuments
		WHERE THROOLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RELEASE:<br>";
	echo "$arr[THROOLD_ReleaseCode]<br>";
	mail_release_doc($arr['THROOLD_ReleaseCode'],'1');
}

//reminder pengembalian dokumen
$query="SELECT DISTINCT TDRTOOLD_ReturnCode
		FROM TD_ReturnOfOtherLegalDocuments
		WHERE TDRTOOLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RETURN:<br>";
	echo "$arr[TDRTOOLD_ReturnCode]<br>";
	mail_return_doc($arr['TDRTOOLD_ReturnCode'],'1');
}
?>
</BODY>
</HTML>
