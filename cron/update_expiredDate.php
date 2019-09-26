<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 27 Sep 2012																						=
= Update Terakhir	: 27 Sep 2012																						=
= Revisi			:	
=========================================================================================================================
*/
?>
<HTML>
<HEAD>
	<title>Custodian System | Update Expired Date</title>
</HEAD>
<BODY>
<?PHP 
include_once ("./config/config_db.php");
//Update Expired Date
$query="UPDATE M_DocumentLegal
		SET DL_ExpDate='0000-00-00'
		WHERE date_format(DL_ExpDate, '%Y-%m')='1970-01'";
$sql = mysql_query($query);
?>
</BODY>
</HTML>