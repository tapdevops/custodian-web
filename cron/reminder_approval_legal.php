<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 18 Sep 2012																						=
= Update Terakhir	: 19 Sep 2012																						=
= Revisi			:																									=
=		19/09/2012	: Perubahan Reminder Email																			=
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
include_once ("./include/function.mail.regdoc.php");
include_once ("./include/function.mail.lodoc.php");
include_once ("./include/function.mail.reldoc.php");
include_once ("./include/function.mail.retdoc.php");

echo "REMINDER APPROVAL UNTUK TRANSAKSI BERIKUT :<br>";
//reminder registrasi dokumen
$query="SELECT DISTINCT THROLD_RegistrationCode
		FROM TH_RegistrationOfLegalDocument
		WHERE THROLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "REGISTRATION:<br>";
	echo "$arr[THROLD_RegistrationCode]<br>";
	mail_registration_doc($arr['THROLD_RegistrationCode'],'1');
}

//reminder permintaan dokumen
$query="SELECT DISTINCT THLOLD_LoanCode
		FROM TH_LoanOfLegalDocument
		WHERE THLOLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>LOAN:<br>";
	echo "$arr[THLOLD_LoanCode]<br>";
	mail_loan_doc($arr['THLOLD_LoanCode'],'1');
}

//reminder pengeluaran dokumen
$query="SELECT DISTINCT THROLD_ReleaseCode
		FROM TH_ReleaseOfLegalDocument
		WHERE THROLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RELEASE:<br>";
	echo "$arr[THROLD_ReleaseCode]<br>";
	mail_release_doc($arr['THROLD_ReleaseCode'],'1');
}

//reminder pengembalian dokumen
$query="SELECT DISTINCT TDRTOLD_ReturnCode
		FROM TD_ReturnOfLegalDocument
		WHERE TDRTOLD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RETURN:<br>";
	echo "$arr[TDRTOLD_ReturnCode]<br>";
	mail_return_doc($arr['TDRTOLD_ReturnCode'],'1');
}
?>
</BODY>
</HTML>
