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
<title>Custodian System | Detail Permintaan Dokumen Lainnya (Di Luar Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.lodoconl.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHLOONLD_Status = document.getElementById('optTHLOONLD_Status').selectedIndex;
	var txtTHLOONLD_Reason = document.getElementById('txtTHLOONLD_Reason').value;

		if(optTHLOONLD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHLOONLD_Status == 2) {
			if (txtTHLOONLD_Reason.replace(" ", "") == "") {
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
		  	 FROM TH_LoanOfOtherNonLegalDocuments thloold, M_Approval dra
			 WHERE thloold.THLOONLD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=thloold.THLOONLD_LoanCode
			 AND thloold.THLOONLD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));
$appQuery=(($act=='approve')&&($approver=="1"))?"AND dla.A_ApproverID='$mv_UserID'":"AND dla.A_Status='2'";

$query =  "	SELECT DISTINCT thloold.THLOONLD_ID,
						  thloold.THLOONLD_LoanCode,
						  thloold.THLOONLD_LoanDate,
						  u.User_ID,
						  u.User_FullName,
						  c.Company_Name,
						  thloold.THLOONLD_Status,
						  thloold.THLOONLD_Information,
						  thloold.THLOONLD_DocumentType,
						  thloold.THLOONLD_DocumentWithWatermarkOrNot,
						  lc.LoanCategory_Name,
						  thloold.THLOONLD_Reason,
						  c.Company_ID,
						  lc.LoanCategory_ID,
						  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dla.A_ApproverID) waitingApproval
		  	FROM TH_LoanOfOtherNonLegalDocuments thloold
			LEFT JOIN M_User u
				ON thloold.THLOONLD_UserID=u.User_ID
			LEFT JOIN M_Company c
				ON thloold.THLOONLD_CompanyID=c.Company_ID
			LEFT JOIN M_Approval dla
				ON dla.A_TransactionCode=thloold.THLOONLD_LoanCode
				$appQuery
			LEFT JOIN M_LoanCategory lc
				ON thloold.THLOONLD_LoanCategoryID=lc.LoanCategory_ID
			WHERE thloold.THLOONLD_Delete_Time is NULL
			AND thloold.THLOONLD_ID='$DocID'
			ORDER BY waitingApproval DESC";

$arr = mysql_fetch_array(mysql_query($query));
$loandate=strtotime($arr['THLOONLD_LoanDate']);
$floandate=date("j M Y", $loandate);

$MainContent ="
<form name='app-doc' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";

if(($act=='approve')&&($approver=="1"))
	$MainContent .="<th colspan=3>Persetujuan Permohonan Permintaan Dokumen Lainnya (Di Luar Legal)</th>";
else
	$MainContent .="<th colspan=3>Permohonan Permintaan Dokumen Lainnya (Di Luar Legal)</th>";

if ($arr['User_ID']==$mv_UserID){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td width='67%'>
			<input name='txtTHLOONLD_ID' type='hidden' value='$arr[THLOONLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOONLD_LoanCode]'/>
			$arr[THLOONLD_LoanCode]
		</td>
		<td width='3%'>
			<a href='print-loan-of-other-non-legal-documents.php?id=$arr[THLOONLD_LoanCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td colspan=2>
			<input name='txtTHLOONLD_ID' type='hidden' value='$arr[THLOONLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOONLD_LoanCode]'/>
			$arr[THLOONLD_LoanCode]
		</td>
	</tr>";
}
$MainContent .="
<tr>
	<td>Tanggal Permintaan</td>
	<td colspan='2'><input name='txtDONL_RegTime' type='hidden' value='$arr[THLOONLD_LoanDate]'>$floandate</td>
</tr>
<tr>
	<td>Nama Peminta</td>
	<td colspan='2'><input name='txtDONL_RegUserID' type='hidden' value='$arr[User_ID]'>$arr[User_FullName]</td>
</tr>
<tr>
	<td>Nama Perusahaan</td>
	<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
</tr>
<tr>
	<td>Tipe Dokumen</td>
	<td colspan='2'><input type='hidden' name='optTHLOONLD_DocumentType' value='$arr[THLOONLD_DocumentType]'>";
	if( $arr['THLOONLD_DocumentType'] == "ORIGINAL" ){
		$MainContent .="Asli";
	}else{
		$MainContent .= ucfirst(strtolower($arr['THLOONLD_DocumentType']));
	}
	$MainContent .="</td>
</tr>
<tr>
	<td>Kategori Permintaan</td>
	<td colspan='2'><input type='hidden' name='optTHLOONLD_LoanCategoryID' value='$arr[LoanCategory_ID]'>$arr[LoanCategory_Name]</td>
</tr>
<tr>
	<td>Keterangan</td>
	<td colspan='2'>$arr[THLOONLD_Information]</td>
</tr>";

	// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
if(($act=='approve')&&($approver=="1")) {
	$MainContent .="
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHLOONLD_Status' id='optTHLOONLD_Status'>
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
			<textarea name='txtTHLOONLD_Reason' id='txtTHLOONLD_Reason' cols='50' rows='2'>$arr[THLOONLD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Dokumen</td>";

	if($arr[THLOONLD_Status]=="waiting") {
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr[THLOONLD_Status]=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOONLD_Reason]</td>
		</tr>";
	}else if($arr[THLOONLD_Status]=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOONLD_Reason]</td>
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
	<th>No. Dokumen</th>
	<th>Nama Dokumen</th>
	<th>Tahun Dokumen</th>
	<th>Departemen</th>
    <th>Ket Permintaan</th>
</tr>";

$query = "SELECT tdloonld.TDLOONLD_ID, tdloonld.TDLOONLD_DocCode,
				 donl.DONL_NoDokumen,
				 donl.DONL_NamaDokumen, donl.DONL_TahunDokumen,
				 donl.DONL_Dept_Code, m_d.Department_Name,
				 donl.DONL_Location, tdloonld.TDLOONLD_Information
		  FROM TD_LoanOfOtherNonLegalDocuments tdloonld
		  LEFT JOIN M_DocumentsOtherNonLegal donl
			ON donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
		  LEFT JOIN db_master.M_Department m_d
		  	ON donl.DONL_Dept_Code=m_d.Department_Code
		  WHERE tdloonld.TDLOONLD_THLOONLD_ID='$DocID'
		  	AND tdloonld.TDLOONLD_Delete_Time IS NULL";
$sql = mysql_query($query);
$no=1;
while ($arr = mysql_fetch_array($sql)) {

	$MainContent .="
	<tr>
		<td class='center'>
			<input type='hidden' name='txtTDLOONLD_ID[]' value='$arr[TDLOONLD_ID]'/>$no
		</td>
		<td class='center'><input name='txtDONL_DocCode[]' type='hidden' value='$arr[TDLOONLD_DocCode]'>$arr[TDLOONLD_DocCode]</td>
		<td class='center'><input name='txtDONL_NoDokumen[]' type='hidden' value='$arr[DONL_NoDokumen]'>$arr[DONL_NoDokumen]</td>
		<td class='center'><input name='txtDONL_NamaDokumen[]' type='hidden' value='$arr[DONL_NamaDokumen]'>$arr[DONL_NamaDokumen]</td>
		<td class='center'><input name='txtDONL_TahunDokumen[]' type='hidden' value='$arr[DONL_TahunDokumen]'>$arr[DONL_TahunDokumen]</td>
		<td class='center'><input name='txtDONL_Dept_Code[]' type='hidden' value='$arr[DONL_Dept_Code]'>$arr[Department_Name]</td>
		<td class='center'>$arr[TDLOONLD_Information]</td>
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
	$A_Status=$_POST['optTHLOONLD_Status'];
	$A_GroupDocID = '6';
	$THLOONLD_Reason=str_replace("<br>", "\n", $_POST['txtTHLOONLD_Reason']);

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

			if ($_POST['optTHLOONLD_DocumentType'] == "ORIGINAL") { $jenis = '20'; }
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
						$query = "UPDATE TH_LoanOfOtherNonLegalDocuments
							SET THLOONLD_Status='accept', THLOONLD_Update_UserID='$A_ApproverID',
						    	THLOONLD_Update_Time=sysdate()
							WHERE THLOONLD_LoanCode='$A_TransactionCode'
							AND THLOONLD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOONLD_UserID'], 3, 1 );
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
						$query = "UPDATE TH_LoanOfOtherNonLegalDocuments
							SET THLOONLD_Status='accept', THLOONLD_Update_UserID='$A_ApproverID',
						    	THLOONLD_Update_Time=sysdate()
							WHERE THLOONLD_LoanCode='$A_TransactionCode'
							AND THLOONLD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							mail_notif_loan_doc($A_TransactionCode, $h_arr['THLOONLD_UserID'], 3, 1 );
							mail_notif_loan_doc($A_TransactionCode, "cust0002", 3, 1 );
						}
					}*/
				}
			}
			echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
		else {
			$query = "UPDATE TH_LoanOfOtherNonLegalDocuments
						SET THLOONLD_Status='accept', THLOONLD_Update_UserID='$A_ApproverID', THLOONLD_Update_Time=sysdate()
						WHERE THLOONLD_LoanCode='$A_TransactionCode'
						AND THLOONLD_Delete_Time IS NULL";
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
						WHERE DocumentGroup_ID ='6'";
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
				$txtDONL_DocCode=$_POST['txtDONL_DocCode'];
				$jumlah=count($txtDONL_DocCode);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Permintaan Dokumen
					$CT_Code="$newnum/DREQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST['optTHLOONLD_LoanCategoryID']) {
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

					$query1 = "UPDATE M_DocumentsOtherNonLegal
								SET DONL_Status ='$docStatus', DONL_Update_Time=sysdate(), DONL_Update_UserID='$mv_UserID'
								WHERE DONL_DocCode='$txtDONL_DocCode[$i]'";
					$sql= "INSERT INTO M_CodeTransaction
								VALUES (NULL,'$CT_Code','$nnum','DREQ','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$mv_UserID',sysdate(),
										'$mv_UserID',sysdate(),NULL,NULL)";
					$sql1 = "UPDATE TD_LoanOfOtherNonLegalDocuments
								SET TDLOONLD_Code ='$CT_Code',TDLOONLD_Update_Time=sysdate(),
									TDLOONLD_Update_UserID='$mv_UserID'
								WHERE TDLOONLD_THLOONLD_ID='$_POST[txtTHLOONLD_ID]'
								AND TDLOONLD_DocCode='$txtDONL_DocCode[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDONL_RegUserID'], 3, 1 );
				mail_notif_loan_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
	}

	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_LoanOfOtherNonLegalDocuments
					SET THLOONLD_Status='reject', THLOONLD_Reason='$THLOONLD_Reason', THLOONLD_Update_UserID='$A_ApproverID',
					    THLOONLD_Update_Time=sysdate()
					WHERE THLOONLD_LoanCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";

		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {

			// Mengubah Status Dokumen menjadi "TERSEDIA"
				$txtDONL_DocCode=$_POST['txtDONL_DocCode'];
				$jumlah=count($txtDONL_DocCode);

				for($i=0;$i<$jumlah;$i++){

					$query1 = "UPDATE M_DocumentsOtherNonLegal
								SET DONL_Status ='1', DONL_Update_Time=sysdate(),DONL_Update_UserID='$mv_UserID'
								WHERE DONL_DocCode='$txtDONL_DocCode[$i]'";
					$mysqli->query($query1);
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDONL_RegUserID'], 4 );
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
