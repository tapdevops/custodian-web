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
include_once ("./include/function.mail.regdocla.php");
include_once ("./include/function.mail.lodocla.php");
include_once ("./include/function.mail.reldocla.php");
include_once ("./include/function.mail.retdocla.php");

echo "REMINDER APPROVAL UNTUK TRANSAKSI BERIKUT :<br>";
//reminder registrasi GRL
$query="SELECT DISTINCT THRGOLAD_RegistrationCode
		FROM TH_RegistrationOfLandAcquisitionDocument
		WHERE THRGOLAD_RegStatus='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "REGISTRATION:<br>";
	echo "$arr[THRGOLAD_RegistrationCode]<br>";
	mail_registration_doc($arr['THRGOLAD_RegistrationCode'],'1');
}

//reminder permintaan GRL
$query="SELECT DISTINCT THLOLAD_LoanCode
		FROM TH_LoanOfLandAcquisitionDocument
		WHERE THLOLAD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>LOAN:<br>";
	echo "$arr[THLOLD_LoanCode]<br>";
	mail_loan_doc($arr['THLOLD_LoanCode'],'1');
}

//reminder pengeluaran GRL
$query="SELECT DISTINCT THRLOLAD_ReleaseCode
		FROM TH_ReleaseOfLandAcquisitionDocument
		WHERE THRLOLAD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RELEASE:<br>";
	echo "$arr[THRLOLAD_ReleaseCode]<br>";
	mail_release_doc($arr['THRLOLAD_ReleaseCode'],'1');
}

//reminder pengembalian dokumen
$query="SELECT DISTINCT TDRTOLAD_ReturnCode
		FROM TD_ReturnOfLandAcquisitionDocument
		WHERE TDRTOLAD_Status='waiting'";
$sql = mysql_query($query);
while ($arr = mysql_fetch_array($sql)){
	echo "<br><br>RETURN:<br>";
	echo "$arr[TDRTOLAD_ReturnCode]<br>";
	mail_return_doc($arr['TDRTOLAD_ReturnCode'],'1');
}
?>
</BODY>
</HTML>
