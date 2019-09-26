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
<title>Custodian System | Detail Permintaan Dokumen Kepemilikan Aset</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.lodocao.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHLOAOD_Status = document.getElementById('optTHLOAOD_Status').selectedIndex;
	var txtTHLOAOD_Reason = document.getElementById('txtTHLOAOD_Reason').value;

		if(optTHLOAOD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHLOAOD_Status == 2) {
			if (txtTHLOAOD_Reason.replace(" ", "") == "") {
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
		  	 FROM TH_LoanOfAssetOwnershipDocument thloaod, M_Approval dra
			 WHERE thloaod.THLOAOD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=thloaod.THLOAOD_LoanCode
			 AND thloaod.THLOAOD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));
$appQuery=(($act=='approve')&&($approver=="1"))?"AND dla.A_ApproverID='$mv_UserID'":"AND dla.A_Status='2'";

$query =  "	SELECT DISTINCT thloaod.THLOAOD_ID,
						  thloaod.THLOAOD_LoanCode,
						  thloaod.THLOAOD_LoanDate,
						  u.User_ID,
						  u.User_FullName,
						  -- c.Company_Name,
						  thloaod.THLOAOD_Status,
						  thloaod.THLOAOD_Information,
						  thloaod.THLOAOD_DocumentType,
						  thloaod.THLOAOD_DocumentWithWatermarkOrNot,
						  lc.LoanCategory_Name,
						  thloaod.THLOAOD_Reason,
						  c.Company_ID,
						  lc.LoanCategory_ID,
						  thloaod.THLOAOD_SoftcopyReceiver,
						  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dla.A_ApproverID) waitingApproval
		  	FROM TH_LoanOfAssetOwnershipDocument thloaod
			LEFT JOIN M_User u
				ON thloaod.THLOAOD_UserID=u.User_ID
			LEFT JOIN M_Company c
				ON thloaod.THLOAOD_CompanyID=c.Company_ID
			LEFT JOIN M_Approval dla
				ON dla.A_TransactionCode=thloaod.THLOAOD_LoanCode
				$appQuery
			LEFT JOIN M_LoanCategory lc
				ON thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
			WHERE thloaod.THLOAOD_Delete_Time is NULL
			AND thloaod.THLOAOD_ID='$DocID'
			ORDER BY waitingApproval DESC";

$arr = mysql_fetch_array(mysql_query($query));
$loandate=strtotime($arr['THLOAOD_LoanDate']);
$floandate=date("j M Y", $loandate);

$MainContent ="
<form name='app-doc' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";

if(($act=='approve')&&($approver=="1"))
	$MainContent .="<th colspan=3>Persetujuan Permohonan Permintaan Dokumen Kepemilikan Aset</th>";
else
	$MainContent .="<th colspan=3>Permohonan Permintaan Dokumen Kepemilikan Aset</th>";

if ($arr['User_ID']==$mv_UserID){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td width='67%'>
			<input name='txtTHLOAOD_ID' type='hidden' value='$arr[THLOAOD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOAOD_LoanCode]'/>
			$arr[THLOAOD_LoanCode]
		</td>
		<td width='3%'>
			<a href='print-loan-of-asset-ownership-document.php?id=$arr[THLOAOD_LoanCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td colspan=2>
			<input name='txtTHLOAOD_ID' type='hidden' value='$arr[THLOAOD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOAOD_LoanCode]'/>
			$arr[THLOAOD_LoanCode]
		</td>
	</tr>";
}
$MainContent .="
<tr>
	<td>Tanggal Permintaan</td>
	<td colspan='2'><input name='txtDAO_RegTime' type='hidden' value='$arr[THLOAOD_LoanDate]'>$floandate</td>
</tr>
<tr>
	<td>Nama Peminta</td>
	<td colspan='2'>
		<input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>
		<input name='txtDAO_RegUserID' type='hidden' value='$arr[User_ID]'>$arr[User_FullName]
	</td>
</tr>
<tr>
	<td>Tipe Dokumen</td>
	<td colspan='2'><input type='hidden' name='optTHLOAOD_DocumentType' value='$arr[THLOAOD_DocumentType]'>";
	if( $arr['THLOAOD_DocumentType'] == "ORIGINAL" ){
		$MainContent .="Asli";
	}elseif( $arr['THLOAOD_DocumentType'] == "HARDCOPY" or $arr['THLOAOD_DocumentType'] == "SOFTCOPY" ){
		$MainContent .= ucfirst(strtolower($arr['THLOAOD_DocumentType']));
	}else{
		if( $arr['THLOAOD_LoanCategoryID'] != '3') $ActionContent .= "Asli";
		else $MainContent .= "";
	}
	$MainContent .="</td>
</tr>
";
if( $arr['THLOAOD_DocumentType'] != "ORIGINAL" ){
	if( $arr['THLOAOD_DocumentType'] == "HARDCOPY" ){
		$cap_or_watermark = "Watermark";
	}elseif( $arr['THLOAOD_DocumentType'] == "SOFTCOPY" ){
		$cap_or_watermark = "Cap";
	}
$MainContent .="<tr>
	<td>Dokumen dengan ".$cap_or_watermark."</td>
	<td colspan='2'><input type='hidden' name='optTHLOAOD_DocumentWithWatermarkOrNot' value='$arr[THLOAOD_DocumentWithWatermarkOrNot]'>";
		if( $arr['THLOAOD_DocumentWithWatermarkOrNot'] == "1" ){
			$MainContent .="Iya";
		}elseif( $arr['THLOAOD_DocumentWithWatermarkOrNot'] == "2" ){
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
	if($arr['THLOAOD_DocumentType'] != "SOFTCOPY"){ $MainContent .="Kategori Permintaan";}
	else{ $MainContent .="Email Penerima Dokumen"; }
	$MainContent .="</td>
	<td colspan='2'>
		<input type='hidden' name='optTHLOAOD_LoanCategoryID' value='$arr[LoanCategory_ID]'>";
		if($arr['THLOAOD_DocumentType'] != "SOFTCOPY"){
			$MainContent .="$arr[LoanCategory_Name]";
		}else{
			$MainContent .="<input id='txtTHLOAOD_SoftcopyReceiver' name='txtTHLOAOD_SoftcopyReceiver' type='hidden' value='THLOAOD_SoftcopyReceiver'/>
			$arr[THLOAOD_SoftcopyReceiver]";
		}
	$MainContent .="</td>
</tr>
<tr>
	<td>Keterangan</td>
	<td colspan='2'>$arr[THLOAOD_Information]</td>
</tr>";

	// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
if(($act=='approve')&&($approver=="1")) {
	$MainContent .="
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHLOAOD_Status' id='optTHLOAOD_Status'>
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
			<textarea name='txtTHLOAOD_Reason' id='txtTHLOAOD_Reason' cols='50' rows='2'>$arr[THLOAOD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Dokumen</td>";

	if($arr[THLOAOD_Status]=="waiting") {
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr[THLOAOD_Status]=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOAOD_Reason]</td>
		</tr>";
	}else if($arr[THLOAOD_Status]=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOAOD_Reason]</td>
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
	<th rowspan='2'>No.</th>
	<th rowspan='2'>Kode Dokumen</th>
	<th rowspan='2'>Nama Pemilik</th>
	<th rowspan='2'>Merk Kendaraan</th>
	<th rowspan='2'>Type</th>
	<th rowspan='2'>Jenis</th>
	<th rowspan='2'>No. Polisi</th>
	<th rowspan='2'>No. Rangka</th>
	<th rowspan='2'>No. Mesin</th>
	<th rowspan='2'>No. BPKB</th>
	<th colspan='2'>STNK</th>
	<th colspan='2'>Pajak Kendaraan</th>
	<th rowspan='2'>Lokasi (PT)</th>
	<th rowspan='2'>Region</th>
	<th rowspan='2'>Keterangan</th>
	<th rowspan='2'>Ket Permintaan</th>
</tr>
<tr>
	<th>Start Date</th>
	<th>Expired Date</th>
	<th>Start Date</th>
	<th>Expired Date</th>
</tr>";

$query = "SELECT tdloaod.TDLOAOD_ID, tdloaod.TDLOAOD_DocCode,
				 dao.DAO_Employee_NIK,
				 CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
				   THEN
					 (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
				   ELSE
					 (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
				 END nama_pemilik,
				 m_mk.MK_Name, dao.DAO_Type, dao.DAO_Jenis,
				 dao.DAO_NoPolisi, dao.DAO_NoRangka, dao.DAO_NoMesin, dao.DAO_NoBPKB,
				 dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate,
				 dao.DAO_Pajak_StartDate, dao.DAO_Pajak_ExpiredDate,
				 dao.DAO_Lokasi_PT, dao.DAO_Region, dao.DAO_Keterangan,
				 tdloaod.TDLOAOD_Information
			 FROM TD_LoanOfAssetOwnershipDocument tdloaod
			 LEFT JOIN M_DocumentAssetOwnership dao
				ON dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
			 LEFT JOIN db_master.M_MerkKendaraan m_mk
			 	ON m_mk.MK_ID=dao.DAO_MK_ID
		  	 WHERE tdloaod.TDLOAOD_THLOAOD_ID='$DocID'
		  		AND tdloaod.TDLOAOD_Delete_Time IS NULL";
$sql = mysql_query($query);
$no=1;
while ($arr = mysql_fetch_array($sql)) {
	$stnk_sdate=(strpos($arr['DAO_STNK_StartDate'], '0000-00-00') !== false || strpos($arr['DAO_STNK_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_STNK_StartDate']));
	$stnk_exdate=(strpos($arr['DAO_STNK_ExpiredDate'], '0000-00-00') !== false || strpos($arr['DAO_STNK_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_STNK_ExpiredDate']));

	$pajak_sdate=(strpos($arr['DAO_Pajak_StartDate'], '0000-00-00') !== false || strpos($arr['DAO_Pajak_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_Pajak_StartDate']));
	$pajak_exdate=(strpos($arr['DAO_Pajak_ExpiredDate'], '0000-00-00') !== false || strpos($arr['DAO_Pajak_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_Pajak_ExpiredDate']));

	$MainContent .="
	<tr>
		<td class='center'>
			<input type='hidden' name='txtTDLOAOD_ID[]' value='$arr[TDLOAOD_ID]'/>$no
		</td>
		<td class='center'><input name='txtDAO_DocCode[]' type='hidden' value='$arr[TDLOAOD_DocCode]'>$arr[TDLOAOD_DocCode]</td>
		<td class='center'><input name='txtDAO_Employee_NIK[]' type='hidden' value='$arr[DAO_Employee_NIK]'>$arr[nama_pemilik]</td>
		<td class='center'><input name='txtDAO_MK_ID[]' type='hidden' value='$arr[MK_Name]'>$arr[MK_Name]</td>
		<td class='center'><input name='txtDAO_Type[]' type='hidden' value='$arr[DAO_Type]'>$arr[DAO_Type]</td>
		<td class='center'><input name='txtDAO_Jenis[]' type='hidden' value='$arr[DAO_Jenis]'>$arr[DAO_Jenis]</td>
		<td class='center'><input name='txtDAO_NoPolisi[]' type='hidden' value='$arr[DAO_NoPolisi]'>$arr[DAO_NoPolisi]</td>
		<td class='center'><input name='txtDAO_NoRangka[]' type='hidden' value='$arr[DAO_NoRangka]'>$arr[DAO_NoRangka]</td>
		<td class='center'><input name='txtDAO_NoMesin[]' type='hidden' value='$arr[DAO_NoMesin]'>$arr[DAO_NoMesin]</td>
		<td class='center'><input name='txtDAO_NoBPKB[]' type='hidden' value='$arr[DAO_NoBPKB]'>$arr[DAO_NoBPKB]</td>
		<td class='center'><input name='txtDAO_STNK_StartDate[]' type='hidden' value='$arr[DAO_STNK_StartDate]'>$stnk_sdate</td>
		<td class='center'><input name='txtDAO_STNK_ExpiredDate[]' type='hidden' value='$arr[DAO_STNK_ExpiredDate]'>$stnk_exdate</td>
		<td class='center'><input name='txtDAO_Pajak_StartDate[]' type='hidden' value='$arr[DAO_Pajak_StartDate]'>$pajak_sdate</td>
		<td class='center'><input name='txtDAO_Pajak_ExpiredDate[]' type='hidden' value='$arr[DAO_Pajak_ExpiredDate]'>$pajak_exdate</td>
		<td class='center'><input name='txtDAO_Lokasi_PT[]' type='hidden' value='$arr[DAO_Lokasi_PT]'>$arr[DAO_Lokasi_PT]</td>
		<td class='center'><input name='txtDAO_Region[]' type='hidden' value='$arr[DAO_Region]'>$arr[DAO_Region]</td>
		<td class='center'><input name='txtDAO_Keterangan[]' type='hidden' value='$arr[DAO_Keterangan]'>$arr[DAO_Keterangan]</td>
		<td class='center'>$arr[TDLOAOD_Information]</td>
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
	$A_Status=$_POST['optTHLOAOD_Status'];
	$A_GroupDocID = '4';
	$THLOAOD_Reason=str_replace("<br>", "\n", $_POST['txtTHLOAOD_Reason']);

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

			if ($_POST['optTHLOAOD_DocumentType'] == "ORIGINAL") { $jenis = '14'; }
			else if ($_POST['optTHLOAOD_DocumentType'] == "HARDCOPY") { $jenis = '15'; }
			else if ($_POST['optTHLOAOD_DocumentType'] == "SOFTCOPY") { $jenis = '25'; }

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
						$query = "UPDATE TH_LoanOfAssetOwnershipDocument
							SET THLOAOD_Status='accept', THLOAOD_Update_UserID='$A_ApproverID',
						    	THLOAOD_Update_Time=sysdate()
							WHERE THLOAOD_LoanCode='$A_TransactionCode'
							AND THLOAOD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOAOD_UserID'], 3, 1 );
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
						$query = "UPDATE TH_LoanOfAssetOwnershipDocument
							SET THLOAOD_Status='accept', THLOAOD_Update_UserID='$A_ApproverID',
						    	THLOAOD_Update_Time=sysdate()
							WHERE THLOAOD_LoanCode='$A_TransactionCode'
							AND THLOAOD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOAOD_UserID'], 3, 1 );
							mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
						}
					}*/
				}
			}
			echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
		else {
			$query = "UPDATE TH_LoanOfAssetOwnershipDocument
						SET THLOAOD_Status='accept', THLOAOD_Update_UserID='$A_ApproverID', THLOAOD_Update_Time=sysdate()
						WHERE THLOAOD_LoanCode='$A_TransactionCode'
						AND THLOAOD_Delete_Time IS NULL";
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
						WHERE DocumentGroup_ID ='4'";
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
				$txtDAO_DocCode=$_POST['txtDAO_DocCode'];
				$jumlah=count($txtDAO_DocCode);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Permintaan Dokumen
					$CT_Code="$newnum/DREQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST['optTHLOAOD_LoanCategoryID']) {
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

					$query1 = "UPDATE M_DocumentAssetOwnership
								SET DAO_Status ='$docStatus',DAO_Update_Time=sysdate(),DAO_Update_UserID='$mv_UserID'
								WHERE DAO_DocCode='$txtDAO_DocCode[$i]'";
					$sql= "INSERT INTO M_CodeTransaction
								VALUES (NULL,'$CT_Code','$nnum','DREQ','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$mv_UserID',sysdate(),
										'$mv_UserID',sysdate(),NULL,NULL)";
					$sql1 = "UPDATE TD_LoanOfAssetOwnershipDocument
								SET TDLOAOD_Code ='$CT_Code',TDLOAOD_Update_Time=sysdate(),
									TDLOAOD_Update_UserID='$mv_UserID'
								WHERE TDLOAOD_THLOAOD_ID='$_POST[txtTHLOAOD_ID]'
								AND TDLOAOD_DocCode='$txtDAO_DocCode[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDAO_RegUserID'], 3, 1 );
				mail_notif_loan_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
	}

	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_LoanOfAssetOwnershipDocument
					SET THLOAOD_Status='reject', THLOAOD_Reason='$THLOAOD_Reason', THLOAOD_Update_UserID='$A_ApproverID',
					    THLOAOD_Update_Time=sysdate()
					WHERE THLOAOD_LoanCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";

		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {

			// Mengubah Status Dokumen menjadi "TERSEDIA"
				$txtDAO_DocCode=$_POST['txtDAO_DocCode'];
				$jumlah=count($txtDAO_DocCode);

				for($i=0;$i<$jumlah;$i++){

					$query1 = "UPDATE M_DocumentAssetOwnership
								SET DAO_Status ='1', DAO_Update_Time=sysdate(),DAO_Update_UserID='$mv_UserID'
								WHERE DAO_DocCode='$txtDAO_DocCode[$i]'";
					$mysqli->query($query1);
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDAO_RegUserID'], 4 );
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
