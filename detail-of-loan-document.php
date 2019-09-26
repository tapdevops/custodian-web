<?PHP
/*
======================================================================================================== Nama Project			: Custodian
=
= Versi 				: 1.2
=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada
=
= Developer			: Sabrina Ingrid Davita					=
= Dibuat Tanggal	: 04 Mei 2012																						=
= Update Terakhir	: 31 Mei 2012																						=
= Revisi			:																									=
= 		23/05/2012	: Validasi keterangan dihilangkan. (OK)																=
=					  Kategori dokumen dipindahkan ke bagian detail peminjaman -> Perubahan Struktur DB (OK)			=
=					  Button "Cancel" untuk detail transaksi (OK)														=
= 		31/05/2012	: Persetujuan transaksi via email & email notifikasi. (OK)											=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Detail Permintaan Dokumen</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.lodoc.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHLOLD_Status = document.getElementById('optTHLOLD_Status').selectedIndex;
	var txtTHLOLD_Reason = document.getElementById('txtTHLOLD_Reason').value;

		if(optTHLOLD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHLOLD_Status == 2) {
			if (txtTHLOLD_Reason.replace(" ", "") == "") {
				alert("Keterangan Persetujuan Harus Diisi Apabila Anda Menolak Dokumen Ini!");
				returnValue = false;
			}
		}

	return returnValue;
}
</script>
</head>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
$act=$_GET["act"];
$DocID=$_GET["id"];

// Cek apakah user berikut memiliki hak untuk approval
$cApp_query="SELECT DISTINCT dra.A_ApproverID
		  	 FROM TH_LoanOfLegalDocument thlold, M_Approval dra
			 WHERE thlold.THLOLD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=thlold.THLOLD_LoanCode
			 AND thlold.THLOLD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));
$appQuery=(($act=='approve')&&($approver=="1"))?"AND dla.A_ApproverID='$mv_UserID'":"AND dla.A_Status='2'";

$query =  "	SELECT DISTINCT thlold.THLOLD_ID,
						  thlold.THLOLD_LoanCode,
						  thlold.THLOLD_LoanDate,
						  u.User_ID,
						  u.User_FullName,
						  c.Company_Name,
						  thlold.THLOLD_Status,
						  thlold.THLOLD_Information,
						  thlold.THLOLD_DocumentType,
						  thlold.THLOLD_DocumentWithWatermarkOrNot,
						  lc.LoanCategory_Name,
						  dg.DocumentGroup_Name,
						  dg.DocumentGroup_ID,
						  thlold.THLOLD_Reason,
						  c.Company_ID,
						  lc.LoanCategory_ID,
						  thlold.THLOLD_SoftcopyReceiver,
						  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dla.A_ApproverID) waitingApproval
		  	FROM TH_LoanOfLegalDocument thlold
			LEFT JOIN M_User u
				ON thlold.THLOLD_UserID=u.User_ID
			LEFT JOIN M_Company c
				ON thlold.THLOLD_CompanyID=c.Company_ID
			LEFT JOIN M_Approval dla
				ON dla.A_TransactionCode=thlold.THLOLD_LoanCode
				$appQuery
			LEFT JOIN M_DocumentGroup dg
				ON thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
			LEFT JOIN M_LoanCategory lc
				ON thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID
			WHERE thlold.THLOLD_Delete_Time is NULL
			AND thlold.THLOLD_ID='$DocID'
			ORDER BY waitingApproval DESC";

$arr = mysql_fetch_array(mysql_query($query));
$loandate=strtotime($arr['THLOLD_LoanDate']);
$floandate=date("j M Y", $loandate);

$MainContent ="
<form name='app-doc' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";

if(($act=='approve')&&($approver=="1"))
	$MainContent .="<th colspan=3>Persetujuan Permohonan Permintaan Dokumen</th>";
else
	$MainContent .="<th colspan=3>Permohonan Permintaan Dokumen</th>";

if ($arr['User_ID']==$mv_UserID){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td width='67%'>
			<input name='txtTHLOLD_ID' type='hidden' value='$arr[THLOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOLD_LoanCode]'/>
			$arr[THLOLD_LoanCode]
		</td>
		<td width='3%'>
			<a href='print-loan-of-document.php?id=$arr[THLOLD_LoanCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td colspan=2>
			<input name='txtTHLOLD_ID' type='hidden' value='$arr[THLOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOLD_LoanCode]'/>
			$arr[THLOLD_LoanCode]
		</td>
	</tr>";
}
$MainContent .="
<tr>
	<td>Tanggal Permintaan</td>
	<td colspan='2'><input name='txtDL_RegTime' type='hidden' value='$arr[THLOLD_LoanDate]'>$floandate</td>
</tr>
<tr>
	<td>Nama Peminta</td>
	<td colspan='2'><input name='txtDL_RegUserID' type='hidden' value='$arr[User_ID]'>$arr[User_FullName]</td>
</tr>
<tr>
	<td>Nama Perusahaan</td>
	<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
</tr>
<tr>
	<td>Grup Dokumen</td>
	<td colspan='2'><input name='txtDL_GroupDocID' type='hidden' value='$arr[DocumentGroup_ID]'>$arr[DocumentGroup_Name]</td>
</tr>
<tr>
	<td>Tipe Dokumen</td>
	<td colspan='2'><input type='hidden' name='optTHLOLD_DocumentType' value='$arr[THLOLD_DocumentType]'>";
	if( $arr['THLOLD_DocumentType'] == "ORIGINAL" ){
		$MainContent .="Asli";
	}elseif( $arr['THLOLD_DocumentType'] == "HARDCOPY" or $arr['THLOLD_DocumentType'] == "SOFTCOPY" ){
		$MainContent .= ucfirst(strtolower($arr['THLOLD_DocumentType']));
	}else{
		if( $arr['THLOLD_LoanCategoryID'] != '3') $ActionContent .= "Asli";
		else $MainContent .= "";
	}
	$MainContent .="</td>
</tr>
";
if( $arr['THLOLD_DocumentType'] != "ORIGINAL" ){
	if( $arr['THLOLD_DocumentType'] == "HARDCOPY" ){
		$cap_or_watermark = "Watermark";
	}elseif( $arr['THLOLD_DocumentType'] == "SOFTCOPY" ){
		$cap_or_watermark = "Cap";
	}
$MainContent .="<tr>
	<td>Dokumen dengan ".$cap_or_watermark."</td>
	<td colspan='2'><input type='hidden' name='optTHLOLD_DocumentWithWatermarkOrNot' value='$arr[THLOLD_DocumentWithWatermarkOrNot]'>";
		if( $arr['THLOLD_DocumentWithWatermarkOrNot'] == "1" ){
			$MainContent .="Iya";
		}elseif( $arr['THLOLD_DocumentWithWatermarkOrNot'] == "2" ){
			$MainContent .="Tidak";
		}else{
			$MainContent .= "-";
		}
	$MainContent .="</td>
</tr>
";
}
$MainContent .="<tr>
	<td>";
	if($arr['THLOLD_DocumentType'] != "SOFTCOPY"){ $MainContent .="Kategori Permintaan";}
	else{ $MainContent .="Email Penerima Dokumen"; }
	$MainContent .="</td>
	<td colspan='2'>
		<input type='hidden' name='optTHLOLD_LoanCategoryID' value='$arr[LoanCategory_ID]'>";
		if($arr['THLOLD_DocumentType'] != "SOFTCOPY"){
			$MainContent .="$arr[LoanCategory_Name]";
		}else{
			$MainContent .="<input id='txtTHLOLD_SoftcopyReceiver' name='txtTHLOLD_SoftcopyReceiver' type='hidden' value='THLOLD_SoftcopyReceiver'/>
			$arr[THLOLD_SoftcopyReceiver]";
		}
	$MainContent .="</td>
</tr>
<tr>
	<td>Keterangan</td>
	<td colspan='2'>$arr[THLOLD_Information]</td>
</tr>";

	// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
if(($act=='approve')&&($approver=="1")) {
	$MainContent .="
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHLOLD_Status' id='optTHLOLD_Status'>
				<option value='0'>--- Menunggu Persetujuan ---</option>";
					$query1="SELECT *
								FROM M_DocumentRegistrationStatus
								WHERE (DRS_Name <> '' AND DRS_Name <> 'waiting')
								AND DRS_Delete_Time is NULL";
					$sql1 = mysql_query($query1);
					while ($field1=mysql_fetch_array($sql1)) {
						if ($field1[DRS_ID]==3)
							$MainContent .="<option value='$field1[DRS_ID]'>Setuju</option>";
						else if ($field1[DRS_ID]==4)
							$MainContent .="<option value='$field1[DRS_ID]'>Tolak</option>";
					}
	$MainContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Keterangan Persetujuan</td>
		<td colspan='2'>
			<textarea name='txtTHLOLD_Reason' id='txtTHLOLD_Reason' cols='50' rows='2'>$arr[THLOLD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Dokumen</td>";

	if($arr[THLOLD_Status]=="waiting") {
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr[THLOLD_Status]=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOLD_Reason]</td>
		</tr>";
	}else if($arr[THLOLD_Status]=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOLD_Reason]</td>
		</tr>";
	}else {
		$MainContent .="
		<td colspan='2'>Draft</td></tr>";
	}
}

$MainContent .="
</table>";

// DETAIL Permintaan DOKUMEN LEGAL
$MainContent .="
<div class='detail-title'>Daftar Dokumen</div>
<table width='100%' id='mytable' class='stripeMe'>
<tr>
   	<th>No</th>
    <th>Kode Dokumen</th>
    <th>Kategori Dokumen</th>
  	<th>Tipe Dokumen</th>
    <th>Instansi Terkait</th>
   	<th>Tanggal Terbit</th>
    <th>Tanggal Habis Berlaku</th>
    <th>Keterangan 1</th>
    <th>Keterangan 2</th>
    <th>Keterangan 3</th>
    <th>Ket Permintaan</th>
</tr>";

$query = "SELECT tdlold.TDLOLD_ID, dt.DocumentType_Name, dt.DocumentType_ID, tdlold.TDLOLD_DocCode,
				 dl.DL_PubDate, dl.DL_ExpDate, di1.DocumentInformation1_ID, tdlold.TDLOLD_Information,
				 di1.DocumentInformation1_Name, di2.DocumentInformation2_ID, di2.DocumentInformation2_Name,
			 	 dl.DL_Instance,dl.DL_Information3, dc.DocumentCategory_ID, dc.DocumentCategory_Name
		  FROM TD_LoanOfLegalDocument tdlold
		  LEFT JOIN M_DocumentLegal dl
			ON dl.DL_DocCode=tdlold.TDLOLD_DocCode
		  LEFT JOIN M_DocumentType dt
			ON dl.DL_TypeDocID=dt.DocumentType_ID
		  LEFT JOIN M_DocumentInformation1 di1
			ON dl.DL_Information1=di1.DocumentInformation1_ID
		  LEFT JOIN M_DocumentInformation2 di2
			ON dl.DL_Information2=di2.DocumentInformation2_ID
		  LEFT JOIN M_DocumentCategory dc
			ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
		  WHERE tdlold.TDLOLD_THLOLD_ID='$DocID'
		  AND tdlold.TDLOLD_Delete_Time IS NULL";
$sql = mysql_query($query);
$no=1;
while ($arr = mysql_fetch_array($sql)) {
	$fpubdate=(strpos($arr['DL_PubDate'], '0000-00-00') !== false || strpos($arr['DL_PubDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DL_PubDate']));
	$fexpdate=(strpos($arr['DL_ExpDate'], '0000-00-00') !== false || strpos($arr['DL_ExpDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DL_ExpDate']));

	$MainContent .="
	<tr>
		<td class='center'>
			<input type='hidden' name='txtTDLOLD_ID[]' value='$arr[TDLOLD_ID]'/>$no
		</td>
		<td class='center'><input name='txtDL_DocCode[]' type='hidden' value='$arr[TDLOLD_DocCode]'>$arr[TDLOLD_DocCode]</td>
		<td class='center'><input name='txtDL_CategoryDocID[]' type='hidden' value='$arr[DocumentCategory_ID]'>$arr[DocumentCategory_Name]</td>
		<td class='center'><input name='txtDL_TypeDocID[]' type='hidden' value='$arr[DocumentType_ID]'>$arr[DocumentType_Name]</td>
		<td class='center'><input name='txtDL_Instance[]' type='hidden' value='$arr[DL_Instance]'>$arr[DL_Instance]</td>
		<td class='center'><input name='txtDL_RegDate[]' type='hidden' value='$arr[DL_PubDate]'>$fpubdate</td>
		<td class='center'><input name='txtDL_ExpDate[]' type='hidden' value='$arr[DL_ExpDate]'>$fexpdate</td>
		<td class='center'><input name='txtDL_Information1[]' type='hidden' value='$arr[DocumentInformation1_ID]'>$arr[DocumentInformation1_Name]</td>
		<td class='center'><input name='txtDL_Information2[]' type='hidden' value='$arr[DocumentInformation2_ID]'>$arr[DocumentInformation2_Name]</td>
		<td class='center'><input name='txtDL_Information3[]' type='hidden' value='$arr[DL_Information3]'>$arr[DL_Information3]</td>
		<td class='center'>$arr[TDLOLD_Information]</td>
	</tr>";
		$no=$no+1;
}

if(($act=='approve')&&($approver=="1")) {
	$MainContent .="
	<th colspan=11>
		<input name='approval' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>";
}
$MainContent .="
</table></form>";


/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}

if(isset($_POST[approval])) {
	//echo '<pre>'; print_r ($_POST); echo '</pre>';
	//die;
	$A_TransactionCode=$_POST['txtA_TransactionCode'];
	$A_ApproverID=$mv_UserID;
	$A_Status=$_POST['optTHLOLD_Status'];
	$A_GroupDocID = $_POST['txtDL_GroupDocID'];
	$THLOLD_Reason=str_replace("<br>", "\n", $_POST['txtTHLOLD_Reason']);

	// MENCARI TAHAP APPROVAL USER TERSEBUT
	$query = "SELECT *
				FROM M_Approval
				WHERE A_TransactionCode='$A_TransactionCode'
				AND A_ApproverID='$A_ApproverID'";
	$arr = mysql_fetch_array(mysql_query($query));
	$step=$arr['A_Step'];
	$AppDate=$arr['A_ApprovalDate'];

	if (!empty($arr) && $AppDate==NULL) {

	// MENCARI JUMLAH APPROVAL
	$query = "SELECT MAX(A_Step) AS jStep
				FROM M_Approval
				WHERE A_TransactionCode='$A_TransactionCode'";
	$arr = mysql_fetch_array(mysql_query($query));
	$jStep=$arr['jStep'];

	// UPDATE APPROVAL
	$query = "UPDATE M_Approval
				SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
					A_Update_Time=sysdate()
				WHERE A_TransactionCode='$A_TransactionCode'
				AND A_ApproverID='$A_ApproverID'";
	$sql = mysql_query($query);

	// PROSES BILA "SETUJU"
	if ($A_Status=='3') {
		// CEK APAKAH MERUPAKAN APPROVAL FINAL
		if ($step <> $jStep) {
			$nStep=$step+1;

			if($_POST['optTHLOLD_DocumentType'] == "ORIGINAL"){
				if($_POST['optTHLOLD_LoanCategoryID'] == '1'){
					$jenis = "1";
				}elseif($_POST['optTHLOLD_LoanCategoryID'] == '2'){
					$jenis = "3";
				}
			}elseif($_POST['optTHLOLD_DocumentType'] == "HARDCOPY"){
				$jenis = "9";
			}elseif($_POST['optTHLOLD_DocumentType'] == "SOFTCOPY"){
				$jenis = "23";
			}else{
				// if($_POST['optTHLOLD_LoanCategoryID'] == '1' or $_POST['optTHLOLD_LoanCategoryID'] == '2'){
				// 	if($_POST['THLOLD_DocumentGroupID'] == '1'){
				// 		$jenis = "1";
				// 	}elseif($_POST['THLOLD_DocumentGroupID'] == '2'){
				// 		$jenis = "3";
				// 	}
				// }elseif($_POST['optTHLOLD_LoanCategoryID'] == '3'){
				// 	$jenis = "9";
				// }else{
				// 	$jenis = "23";
				// }
			}

			$qComp = "SELECT Company_Area FROM M_Company WHERE Company_ID = '{$_POST['txtCompany_ID']}'";
			$aComp = mysql_fetch_array(mysql_query($qComp));

			for ($i=$nStep; $i<=$jStep; $i++) {
				$j = $i + 1;
				$query = "
				SELECT rads.RADS_StatusID, ma.A_ApproverID
				FROM M_Approval ma
				JOIN M_Role_ApproverDocStepStatus rads
					ON ma.A_Step = rads.RADS_StepID
				LEFT JOIN M_Role_Approver ra
					ON rads.RADS_RA_ID = ra.RA_ID
				WHERE ma.A_Step = '{$i}'
					AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$aComp['Company_Area']}')
					AND ma.A_TransactionCode = '{$A_TransactionCode}'
					AND rads.RADS_DocID = '{$jenis}'
					AND rads.RADS_ProsesID = '2'
				";
				$result = mysql_fetch_array(mysql_query($query));

				if ($result['RADS_StatusID'] == '1') {
					//echo 'Step : ' . $i . ' => Kirim Email Approval<br />';
					$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
					$yquery = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS abc FROM M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));
					if ($yquery['abc'] == 0) {
						$query = "UPDATE M_Approval
									SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
						if ($sql = mysql_query($query)) {
							mail_loan_doc($A_TransactionCode);
						}
					} else {
						$query = "UPDATE M_Approval
									SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
						if ($sql = mysql_query($query)) {
							$xquery = "UPDATE M_Approval
										SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
										WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
							if ($xsql = mysql_query($xquery)) {
								mail_loan_doc($A_TransactionCode);
							}
						}
					}

					/************************************
					* Nicholas - 24 Sept 2018			*
					* Fix Bug skip approval				*
					************************************/

					/*if ($i == $jStep) {
						$query = "UPDATE TH_LoanOfLegalDocument
							SET THLOLD_Status='accept', THLOLD_Update_UserID='$A_ApproverID',
						    	THLOLD_Update_Time=sysdate()
							WHERE THLOLD_LoanCode='$A_TransactionCode'
							AND THLOLD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOLD_UserID'], 3, 1 );
							mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
						}
					}*/
					break;
				} else if ($result['RADS_StatusID'] == '2') {
					//echo 'Step : ' . $i . ' => Kirim Email Notifikasi<br />';
					$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
					$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));

					if ($yquery['abc'] == 0) {
						$query = "UPDATE M_Approval
									SET A_Status='3', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $result['A_ApproverID'], 3);
						}
					} else {
						$query = "UPDATE M_Approval
									SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
						if ($sql = mysql_query($query)) {
							$xquery = "UPDATE M_Approval
										SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
										WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
							$xsql = mysql_query($xquery);
						}
					}

					/************************************
					* Nicholas - 24 Sept 2018			*
					* Fix Bug skip approval				*
					************************************/

					/*if ($i == $jStep) {
						$query = "UPDATE TH_LoanOfLegalDocument
							SET THLOLD_Status='accept', THLOLD_Update_UserID='$A_ApproverID',
						    	THLOLD_Update_Time=sysdate()
							WHERE THLOLD_LoanCode='$A_TransactionCode'
							AND THLOLD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOLD_UserID'], 3, 1 );
							mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
						}
					}*/
				}
			}
			echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
		else {
			$query = "UPDATE TH_LoanOfLegalDocument
						SET THLOLD_Status='accept', THLOLD_Update_UserID='$A_ApproverID', THLOLD_Update_Time=sysdate()
						WHERE THLOLD_LoanCode='$A_TransactionCode'
						AND THLOLD_Delete_Time IS NULL";
			$sql = mysql_query($query);

			$regyear=date("Y");
			$rmonth=date("n");

			// Mengubah Bulan ke Romawi
			switch ($rmonth)	{
				case 1: $regmonth="I"; break;
				case 2: $regmonth="II"; break;
				case 3: $regmonth="III"; break;
				case 4: $regmonth="IV"; break;
				case 5: $regmonth="V"; break;
				case 6: $regmonth="VI"; break;
				case 7: $regmonth="VII"; break;
				case 8: $regmonth="VIII"; break;
				case 9: $regmonth="IX"; break;
				case 10: $regmonth="X"; break;
				case 11: $regmonth="XI"; break;
				case 12: $regmonth="XII"; break;
			}

			// Cari Kode Perusahaan
			$query = "SELECT *
						FROM M_Company
						WHERE Company_ID='$_POST[txtCompany_ID]'";
			$field = mysql_fetch_array(mysql_query($query));
			$Company_Code=$field['Company_Code'];

			// Cari Kode Dokumen Grup
			$query = "SELECT *
						FROM M_DocumentGroup
						WHERE DocumentGroup_ID ='$_POST[txtDL_GroupDocID]'";
			$field = mysql_fetch_array(mysql_query($query));
			$DocumentGroup_Code=$field['DocumentGroup_Code'];

			// Cari No Permintaan Dokumen Terakhir
			$query = "SELECT MAX(CT_SeqNo)
						FROM M_CodeTransaction
						WHERE CT_Year='$regyear'
						AND CT_Action='DREQ'
						AND CT_GroupDocCode='$DocumentGroup_Code'
						AND CT_Delete_Time is NULL";
			$field = mysql_fetch_array(mysql_query($query));

			if($field[0]==NULL)
				$maxnum=0;
			else
				$maxnum=$field[0];
			$nnum=$maxnum+1;

				// Mengubah Status Dokumen menjadi "Permintaan DISETUJUI"
				$txtDL_DocCode=$_POST['txtDL_DocCode'];
				$jumlah=count($txtDL_DocCode);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Permintaan Dokumen
					$CT_Code="$newnum/DREQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST['optTHLOLD_LoanCategoryID']) {
						case "1":
							$docStatus="3";
							break;
						case "2":
							$docStatus="3";
							break;
						case "3":
							$docStatus="1";
							break;
						default: $docStatus="1";
					}

					$query1 = "UPDATE M_DocumentLegal
								SET DL_Status ='$docStatus',DL_Update_Time=sysdate(),DL_Update_UserID='$mv_UserID'
								WHERE DL_DocCode='$txtDL_DocCode[$i]'";
					$sql= "INSERT INTO M_CodeTransaction
								VALUES (NULL,'$CT_Code','$nnum','DREQ','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$mv_UserID',sysdate(),
										'$mv_UserID',sysdate(),NULL,NULL)";
					$sql1 = "UPDATE TD_LoanOfLegalDocument
								SET TDLOLD_Code ='$CT_Code',TDLOLD_Update_Time=sysdate(),
									TDLOLD_Update_UserID='$mv_UserID'
								WHERE TDLOLD_THLOLD_ID='$_POST[txtTHLOLD_ID]'
								AND TDLOLD_DocCode='$txtDL_DocCode[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 3, 1 );
				mail_notif_loan_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
	}

	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_LoanOfLegalDocument
					SET THLOLD_Status='reject', THLOLD_Reason='$THLOLD_Reason', THLOLD_Update_UserID='$A_ApproverID',
					    THLOLD_Update_Time=sysdate()
					WHERE THLOLD_LoanCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";

		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {

			// Mengubah Status Dokumen menjadi "TERSEDIA"
				$txtDL_DocCode=$_POST['txtDL_DocCode'];
				$jumlah=count($txtDL_DocCode);

				for($i=0;$i<$jumlah;$i++){

					$query1 = "UPDATE M_DocumentLegal
								SET DL_Status ='1', DL_Update_Time=sysdate(),DL_Update_UserID='$mv_UserID'
								WHERE DL_DocCode='$txtDL_DocCode[$i]'";
					$mysqli->query($query1);
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 4 );
				$e_query="SELECT *
						  FROM M_Approval
						  WHERE A_TransactionCode='$A_TransactionCode'
						  AND A_Step<'$step' ";
				$e_sql=mysql_query($e_query);
				while ($e_arr=mysql_fetch_array($e_sql)){
					mail_notif_loan_doc($A_TransactionCode, $e_arr['A_ApproverID'], 4 );
				}
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
	}
	}
	else {
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
	}

}

$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>
</script>
