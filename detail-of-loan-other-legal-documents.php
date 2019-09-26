<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.3.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 31 Agustus 2018																					=
= Update Terakhir	: -           																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Detail Permintaan Dokumen Lainnya (Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.lodocol.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHLOOLD_Status = document.getElementById('optTHLOOLD_Status').selectedIndex;
	var txtTHLOOLD_Reason = document.getElementById('txtTHLOOLD_Reason').value;

		if(optTHLOOLD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHLOOLD_Status == 2) {
			if (txtTHLOOLD_Reason.replace(" ", "") == "") {
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
		  	 FROM TH_LoanOfOtherLegalDocuments thloold, M_Approval dra
			 WHERE thloold.THLOOLD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=thloold.THLOOLD_LoanCode
			 AND thloold.THLOOLD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));
$appQuery=(($act=='approve')&&($approver=="1"))?"AND dla.A_ApproverID='$mv_UserID'":"AND dla.A_Status='2'";

$query =  "	SELECT DISTINCT thloold.THLOOLD_ID,
						  thloold.THLOOLD_LoanCode,
						  thloold.THLOOLD_LoanDate,
						  u.User_ID,
						  u.User_FullName,
						  c.Company_Name,
						  thloold.THLOOLD_Status,
						  thloold.THLOOLD_Information,
						  thloold.THLOOLD_DocumentType,
						  thloold.THLOOLD_DocumentWithWatermarkOrNot,
						  lc.LoanCategory_Name,
						  thloold.THLOOLD_Reason,
						  c.Company_ID,
						  lc.LoanCategory_ID,
						  thloold.THLOOLD_SoftcopyReceiver,
						  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dla.A_ApproverID) waitingApproval
		  	FROM TH_LoanOfOtherLegalDocuments thloold
			LEFT JOIN M_User u
				ON thloold.THLOOLD_UserID=u.User_ID
			LEFT JOIN M_Company c
				ON thloold.THLOOLD_CompanyID=c.Company_ID
			LEFT JOIN M_Approval dla
				ON dla.A_TransactionCode=thloold.THLOOLD_LoanCode
				$appQuery
			LEFT JOIN M_LoanCategory lc
				ON thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
			WHERE thloold.THLOOLD_Delete_Time is NULL
			AND thloold.THLOOLD_ID='$DocID'
			ORDER BY waitingApproval DESC";

$arr = mysql_fetch_array(mysql_query($query));
$loandate=strtotime($arr['THLOOLD_LoanDate']);
$floandate=date("j M Y", $loandate);

$MainContent ="
<form name='app-doc' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";

if(($act=='approve')&&($approver=="1"))
	$MainContent .="<th colspan=3>Persetujuan Permohonan Permintaan Dokumen Lainnya (Legal)</th>";
else
	$MainContent .="<th colspan=3>Permohonan Permintaan Dokumen Lainnya (Legal)</th>";

if ($arr['User_ID']==$mv_UserID){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td width='67%'>
			<input name='txtTHLOOLD_ID' type='hidden' value='$arr[THLOOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOOLD_LoanCode]'/>
			$arr[THLOOLD_LoanCode]
		</td>
		<td width='3%'>
			<a href='print-loan-of-other-legal-documents.php?id=$arr[THLOOLD_LoanCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td colspan=2>
			<input name='txtTHLOOLD_ID' type='hidden' value='$arr[THLOOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOOLD_LoanCode]'/>
			$arr[THLOOLD_LoanCode]
		</td>
	</tr>";
}
$MainContent .="
<tr>
	<td>Tanggal Permintaan</td>
	<td colspan='2'><input name='txtDOL_RegTime' type='hidden' value='$arr[THLOOLD_LoanDate]'>$floandate</td>
</tr>
<tr>
	<td>Nama Peminta</td>
	<td colspan='2'><input name='txtDOL_RegUserID' type='hidden' value='$arr[User_ID]'>$arr[User_FullName]</td>
</tr>
<tr>
	<td>Nama Perusahaan</td>
	<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
</tr>
<tr>
	<td>Tipe Dokumen</td>
	<td colspan='2'><input type='hidden' name='optTHLOOLD_DocumentType' value='$arr[THLOOLD_DocumentType]'>";
	if( $arr['THLOOLD_DocumentType'] == "ORIGINAL" ){
		$MainContent .="Asli";
	}elseif( $arr['THLOOLD_DocumentType'] == "HARDCOPY" or $arr['THLOOLD_DocumentType'] == "SOFTCOPY" ){
		$MainContent .= ucfirst(strtolower($arr['THLOOLD_DocumentType']));
	}else{
		if( $arr['THLOOLD_LoanCategoryID'] != '3') $ActionContent .= "Asli";
		else $MainContent .= "";
	}
	$MainContent .="</td>
</tr>
";
if( $arr['THLOOLD_DocumentType'] != "ORIGINAL" ){
	if( $arr['THLOOLD_DocumentType'] == "HARDCOPY" ){
		$cap_or_watermark = "Watermark";
	}elseif( $arr['THLOOLD_DocumentType'] == "SOFTCOPY" ){
		$cap_or_watermark = "Cap";
	}
$MainContent .="<tr>
	<td>Dokumen dengan ".$cap_or_watermark."</td>
	<td colspan='2'><input type='hidden' name='optTHLOOLD_DocumentWithWatermarkOrNot' value='$arr[THLOOLD_DocumentWithWatermarkOrNot]'>";
		if( $arr['THLOOLD_DocumentWithWatermarkOrNot'] == "1" ){
			$MainContent .="Iya";
		}elseif( $arr['THLOOLD_DocumentWithWatermarkOrNot'] == "2" ){
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
	if($arr['THLOOLD_DocumentType'] != "SOFTCOPY"){ $MainContent .="Kategori Permintaan";}
	else{ $MainContent .="Email Penerima Dokumen"; }
	$MainContent .="</td>
	<td colspan='2'>
		<input type='hidden' name='optTHLOOLD_LoanCategoryID' value='$arr[LoanCategory_ID]'>";
		if($arr['THLOOLD_DocumentType'] != "SOFTCOPY"){
			$MainContent .="$arr[LoanCategory_Name]";
		}else{
			$MainContent .="<input id='txtTHLOOLD_SoftcopyReceiver' name='txtTHLOOLD_SoftcopyReceiver' type='hidden' value='THLOOLD_SoftcopyReceiver'/>
			$arr[THLOAOD_SoftcopyReceiver]";
		}
	$MainContent .="</td>
</tr>
<tr>
	<td>Keterangan</td>
	<td colspan='2'>$arr[THLOOLD_Information]</td>
</tr>";

// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
if(($act=='approve') && ($approver=="1")) {
	$MainContent .="
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHLOOLD_Status' id='optTHLOOLD_Status'>
				<option value='0'>--- Menunggu Persetujuan ---</option>";
					$query1="SELECT *
								FROM M_DocumentRegistrationStatus
								WHERE (DRS_Name <> '' AND DRS_Name <> 'waiting')
								AND DRS_Delete_Time is NULL";
					$sql1 = mysql_query($query1);
					while ($field1=mysql_fetch_array($sql1)) {
						if ($field1['DRS_ID']==3)
							$MainContent .="<option value='$field1[DRS_ID]'>Setuju</option>";
						else if ($field1['DRS_ID']==4)
							$MainContent .="<option value='$field1[DRS_ID]'>Tolak</option>";
					}
	$MainContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Keterangan Persetujuan</td>
		<td colspan='2'>
			<textarea name='txtTHLOOLD_Reason' id='txtTHLOOLD_Reason' cols='50' rows='2'>$arr[THLOOLD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Dokumen</td>";

	if($arr['THLOOLD_Status']=="waiting") {
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr['THLOOLD_Status']=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOOLD_Reason]</td>
		</tr>";
	}else if($arr['THLOOLD_Status']=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOOLD_Reason]</td>
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
  	<th>Nama Dokumen</th>
    <th>Instansi Terkait</th>
	<th>No. Dokumen</th>
   	<th>Tanggal Terbit</th>
    <th>Tanggal Berakhir</th>
    <th>Ket Permintaan</th>
</tr>";

$query = "SELECT tdloold.TDLOOLD_ID, tdloold.TDLOOLD_DocCode,
				 dol.DOL_CompanyID, dol.DOL_GroupDocID, dol.DOL_CategoryDocID,
				 dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait,
				 dol.DOL_NoDokumen, dol.DOL_TglTerbit, dol.DOL_TglBerakhir,
				 tdloold.TDLOOLD_Information,
			 	 dc.DocumentCategory_ID, dc.DocumentCategory_Name
		  FROM TD_LoanOfOtherLegalDocuments tdloold
		  LEFT JOIN M_DocumentsOtherLegal dol
			ON dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
		  LEFT JOIN db_master.M_DocumentCategory dc
			ON dol.DOL_CategoryDocID=dc.DocumentCategory_ID
		  WHERE tdloold.TDLOOLD_THLOOLD_ID='$DocID'
		  	AND tdloold.TDLOOLD_Delete_Time IS NULL";
$sql = mysql_query($query);
$no=1;
while ($arr = mysql_fetch_array($sql)) {
	$tgl_terbit=date("j M Y", strtotime($arr['DOL_TglTerbit']));
	$tgl_berakhir=(($arr['DOL_TglBerakhir']=="0000-00-00 00:00:00")||($arr['DOL_TglBerakhir']=="1970-01-01 01:00:00"))?"-":date("j M Y", strtotime($arr['DOL_TglBerakhir']));

	$MainContent .="
	<tr>
		<td class='center'>
			<input type='hidden' name='txtTDLOOLD_ID[]' value='$arr[TDLOOLD_ID]'/>$no
		</td>
		<td class='center'><input name='txtDOL_DocCode[]' type='hidden' value='$arr[TDLOOLD_DocCode]'>$arr[TDLOOLD_DocCode]</td>
		<td class='center'><input name='txtDOL_CategoryDocID[]' type='hidden' value='$arr[DocumentCategory_ID]'>$arr[DocumentCategory_Name]</td>
		<td class='center'><input name='txtDOL_NamaDokumen[]' type='hidden' value='$arr[DocumentType_ID]'>$arr[DOL_NamaDokumen]</td>
		<td class='center'><input name='txtDOL_InstansiTerkait[]' type='hidden' value='$arr[DOL_InstansiTerkait]'>$arr[DOL_InstansiTerkait]</td>
		<td class='center'><input name='txtDOL_NoDokumen[]' type='hidden' value='$arr[DOL_NoDokumen]'>$arr[DOL_NoDokumen]</td>
		<td class='center'><input name='txtDOL_TglTerbit[]' type='hidden' value='$arr[DOL_TglTerbit]'>$tgl_terbit</td>
		<td class='center'><input name='txtDOL_TglBerakhir[]' type='hidden' value='$arr[DOL_TglBerakhir]'>$tgl_berakhir</td>
		<td class='center'>$arr[TDLOOLD_Information]</td>
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
	$A_Status=$_POST['optTHLOOLD_Status'];
	$A_GroupDocID = '5';
	$THLOOLD_Reason=str_replace("<br>", "\n", $_POST['txtTHLOOLD_Reason']);

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

			if ($_POST['optTHLOOLD_DocumentType'] == "ORIGINAL") { $jenis = '17'; }
			else if ($_POST['optTHLOOLD_DocumentType'] == "HARDCOPY") { $jenis = '18'; }
			else if ($_POST['optTHLOOLD_DocumentType'] == "SOFTCOPY") { $jenis = '26'; }
			else;

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
					$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));
					if ($yquery['abc'] != '0') {
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
					} else {
						$query = "UPDATE M_Approval
									SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
						if ($sql = mysql_query($query)) {
							mail_loan_doc($A_TransactionCode);
						}
					}

					/************************************
					* Nicholas - 24 Sept 2018			*
					* Fix Bug skip approval				*
					************************************/

					/*if ($i == $jStep) {
						$query = "UPDATE TH_LoanOfOtherLegalDocuments
							SET THLOOLD_Status='accept', THLOOLD_Update_UserID='$A_ApproverID',
						    	THLOOLD_Update_Time=sysdate()
							WHERE THLOOLD_LoanCode='$A_TransactionCode'
							AND THLOOLD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOOLD_UserID'], 3, 1 );
							mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
						}
					}*/
					break;
				} else if ($result['RADS_StatusID'] == '2') {
					//echo 'Step : ' . $i . ' => Kirim Email Notifikasi<br />';
					$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
					$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));

					if ($yquery['abc'] != '0') {
						$query = "UPDATE M_Approval
									SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
						if ($sql = mysql_query($query)) {
							$xquery = "UPDATE M_Approval
										SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
										WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
							$xsql = mysql_query($xquery);
						}
					} else {
						$query = "UPDATE M_Approval
									SET A_Status='3', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
									WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $result['A_ApproverID'], 3);
						}
					}

					/************************************
					* Nicholas - 24 Sept 2018			*
					* Fix Bug skip approval				*
					************************************/

					/*if ($i == $jStep) {
						$query = "UPDATE TH_LoanOfOtherLegalDocuments
							SET THLOOLD_Status='accept', THLOOLD_Update_UserID='$A_ApproverID',
						    	THLOOLD_Update_Time=sysdate()
							WHERE THLOOLD_LoanCode='$A_TransactionCode'
							AND THLOOLD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOOLD_UserID'], 3, 1 );
							mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
						}
					}*/
				}
			}
			echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
		else {
			$query = "UPDATE TH_LoanOfOtherLegalDocuments
						SET THLOOLD_Status='accept', THLOOLD_Update_UserID='$A_ApproverID', THLOOLD_Update_Time=sysdate()
						WHERE THLOOLD_LoanCode='$A_TransactionCode'
						AND THLOOLD_Delete_Time IS NULL";
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
						WHERE DocumentGroup_ID ='5'";
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
				$txtDOL_DocCode=$_POST['txtDOL_DocCode'];
				$jumlah=count($txtDOL_DocCode);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Permintaan Dokumen
					$CT_Code="$newnum/DREQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST['optTHLOLAD_LoanCategoryID']) {
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

					$query1 = "UPDATE M_DocumentsOtherLegal
								SET DOL_Status ='$docStatus',DOL_Update_Time=sysdate(),DOL_Update_UserID='$mv_UserID'
								WHERE DOL_DocCode='$txtDOL_DocCode[$i]'";
					$sql= "INSERT INTO M_CodeTransaction
								VALUES (NULL,'$CT_Code','$nnum','DREQ','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$mv_UserID',sysdate(),
										'$mv_UserID',sysdate(),NULL,NULL)";
					$sql1 = "UPDATE TD_LoanOfOtherLegalDocuments
								SET TDLOOLD_Code ='$CT_Code',TDLOOLD_Update_Time=sysdate(),
									TDLOOLD_Update_UserID='$mv_UserID'
								WHERE TDLOOLD_THLOOLD_ID='$_POST[txtTHLOOLD_ID]'
								AND TDLOOLD_DocCode='$txtDOL_DocCode[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDOL_RegUserID'], 3, 1 );
				mail_notif_loan_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
	}

	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_LoanOfOtherLegalDocuments
					SET THLOOLD_Status='reject', THLOOLD_Reason='$THLOOLD_Reason', THLOOLD_Update_UserID='$A_ApproverID',
					    THLOOLD_Update_Time=sysdate()
					WHERE THLOOLD_LoanCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";

		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {

			// Mengubah Status Dokumen menjadi "TERSEDIA"
				$txtDOL_DocCode=$_POST['txtDOL_DocCode'];
				$jumlah=count($txtDOL_DocCode);

				for($i=0;$i<$jumlah;$i++){

					$query1 = "UPDATE M_DocumentsOtherLegal
								SET DOL_Status ='1', DOL_Update_Time=sysdate(),DOL_Update_UserID='$mv_UserID'
								WHERE DOL_DocCode='$txtDOL_DocCode[$i]'";
					$mysqli->query($query1);
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDOL_RegUserID'], 4 );
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
