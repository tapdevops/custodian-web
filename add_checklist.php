<!-- REFRESH PAGE TIAP 5 menit -->
<meta http-equiv="refresh" content="300">
<TITLE>Tambah Checklist</TITLE>
<?PHP 
include ("./config/config_db.php"); 

// TAMBAH CHECKLIST DI TRANSAKSI
$query="SELECT TDRGOLAD_ID, TDRGOLAD_Insert_Time, User_FullName, THRGOLAD_RegStatus
		FROM TD_RegistrationOfLandAcquisitionDocument
		LEFT JOIN M_User
		ON User_ID=TDRGOLAD_Insert_UserID
		LEFT JOIN TH_RegistrationOfLandAcquisitionDocument
		ON THRGOLAD_ID = TDRGOLAD_THRGOLAD_ID
		WHERE TDRGOLAD_ID NOT IN (SELECT TDRGOLADD_TDRGOLAD_ID FROM TD_RegistrationOfLandAcquisitionDocumentDetail)";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

echo"
	<table width='80%' border='1' cellpadding='0' cellspacing='0' style='font-family:verdana; font-size:12px;'>
	<tr bgcolor='#CCC'>
		<td width='30%' align='center'>ID DETAIL</td>
		<td width='30%' align='center'>PENDAFTAR</td>
		<td width='20%' align='center'>TANGGAL DAFTAR</td>			
		<td width='20%' align='center'>KETERANGAN</td>
	</tr>		
";
	
if($num){
	while($obj=mysql_fetch_object($sql)){
		for ($i=1 ; $i<=14 ; $i++) {
			$insQuery="INSERT INTO TD_RegistrationOfLandAcquisitionDocumentDetail
					   (TDRGOLADD_TDRGOLAD_ID,TDRGOLADD_AttibuteID,TDRGOLADD_AttributeStatusID,
						TDRGOLADD_Insert_UserID,TDRGOLADD_Insert_Time,TDRGOLADD_Update_UserID,TDRGOLADD_Update_Time)
					   VALUES ('".$obj->TDRGOLAD_ID."','$i','1','".$obj->TDRGOLAD_Insert_UserID."','".$obj->TDRGOLAD_Insert_Time."',
							   '".$obj->TDRGOLAD_Insert_UserID."','".$obj->TDRGOLAD_Insert_Time."')";					   
			$insSql = mysql_query($insQuery);
		}
		echo"
			<tr>
				<td align='center'>".$obj->TDRGOLAD_ID."</td>
				<td align='center'>".$obj->User_FullName."</td>
				<td align='center'>".$obj->TDRGOLAD_Insert_Time."</td>
				<td align='center'>TELAH DITAMBAHKAN</td>
			</tr>		
		";	
	}
}else{
	echo"
		<tr>
			<td colspan='4' align='center'>TIDAK ADA DOKUMEN YANG PERLU DITAMBAHKAN</td>
		</tr>		
	";	
}	
echo"</table>";	


//TAMBAH CHECK LIST DI MASTER DOKUMEN
$query="SELECT DLA_ID, DLA_Code, SUBSTRING(DLA_Code,1,4) FRONTCODE,  SUBSTRING(DLA_Code,5) ENDCODE, DLA_Insert_Time, DLA_Insert_UserID, User_FullName, DLA_Status
		FROM M_DocumentLandAcquisition
		LEFT JOIN M_User
		ON User_ID=DLA_Insert_UserID
		WHERE DLA_ID NOT IN (SELECT DLAA_DLA_ID FROM M_DocumentLandAcquisitionAttribute)";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

echo"<br><br>
	<table width='80%' border='1' cellpadding='0' cellspacing='0' style='font-family:verdana; font-size:12px;'>
	<tr bgcolor='#CCC'>
		<td width='30%' align='center'>ID DOKUMEN</td>
		<td width='30%' align='center'>PENDAFTAR</td>
		<td width='20%' align='center'>TANGGAL DAFTAR</td>			
		<td width='20%' align='center'>KETERANGAN</td>
	</tr>		
";

if($num){
	while($obj=mysql_fetch_object($sql)){
		for ($i=1 ; $i<=14 ; $i++) {
			$newnumQuery="SELECT CONCAT('".$obj->FRONTCODE."', LPAD('".$i."',2,'00'), '".$obj->ENDCODE."') NEW_CODE";
			$newnumSQL=mysql_query($newnumQuery);
			$newnumObj=mysql_fetch_object($newnumSQL);
			
			$insQuery="INSERT INTO M_DocumentLandAcquisitionAttribute
					   (DLAA_DocCode,DLAA_DLA_ID,DLAA_LAA_ID,DLAA_LAAS_ID,DLAA_Status,
						DLAA_Insert_UserID,DLAA_Insert_Time,DLAA_Update_UserID,DLAA_Update_Time)
					   VALUES ('".$newnumObj->NEW_CODE."','".$obj->DLA_ID."','$i','1','".$obj->DLA_Status."',
							   '".$obj->DLA_Insert_UserID."','".$obj->DLA_Insert_Time."',
							   '".$obj->DLA_Insert_UserID."','".$obj->DLA_Insert_Time."')";					   
			$insSql = mysql_query($insQuery);
		}
		
		echo"
			<tr>
				<td align='center'>".$obj->DLA_Code."</td>
				<td align='center'>".$obj->User_FullName."</td>
				<td align='center'>".$obj->DLA_Insert_Time."</td>
				<td align='center'>TELAH DITAMBAHKAN</td>
			</tr>		
		";	
	}
}else{
	echo"
		<tr>
			<td colspan='4' align='center'>TIDAK ADA DOKUMEN YANG PERLU DITAMBAHKAN</td>
		</tr>		
	";
}	
echo"</table>";	
?>