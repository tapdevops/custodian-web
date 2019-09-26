<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
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
include_once ("./include/function.mail.regdocao.php");
include_once ("./include/function.mail.lodocao.php");
include_once ("./include/function.mail.reldocao.php");
include_once ("./include/function.mail.retdocao.php");

echo "REMINDER APPROVAL UNTUK TRANSAKSI BERIKUT :<br>";
//reminder registrasi dokumen
$query="SELECT DISTINCT THROAOD_RegistrationCode
		FROM TH_RegistrationOfAssetOwnershipDocument
		WHERE THROAOD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "REGISTRATION:<br>";
	echo "$arr[THROAOD_RegistrationCode]<br>";
	mail_registration_doc($arr['THROAOD_RegistrationCode'],'1');
}

//reminder permintaan dokumen
$query="SELECT DISTINCT THLOAOD_LoanCode
		FROM TH_LoanOfAssetOwnershipDocument
		WHERE THLOAOD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>LOAN:<br>";
	echo "$arr[THLOAOD_LoanCode]<br>";
	mail_loan_doc($arr['THLOAOD_LoanCode'],'1');
}

//reminder pengeluaran dokumen
$query="SELECT DISTINCT THROAOD_ReleaseCode
		FROM TH_ReleaseOfAssetOwnershipDocument
		WHERE THROAOD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RELEASE:<br>";
	echo "$arr[THROAOD_ReleaseCode]<br>";
	mail_release_doc($arr['THROAOD_ReleaseCode'],'1');
}

//reminder pengembalian dokumen
$query="SELECT DISTINCT TDRTOAOD_ReturnCode
		FROM TD_ReturnOfAssetOwnershipDocument
		WHERE TDRTOAOD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RETURN:<br>";
	echo "$arr[TDRTOAOD_ReturnCode]<br>";
	mail_return_doc($arr['TDRTOAOD_ReturnCode'],'1');
}
?>
</BODY>
</HTML>
