<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 29 Mei 2012																						=
= Update Terakhir	: 29 Mei 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start(); 
?>
<title>Custodian System | Detail Permintaan Dokumen Pembebasan Lahan</title>
<head>
<?PHP 
include ("./config/config_db.php"); 
include ("./include/function.mail.lodocla.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;							

	var optTHLOLAD_Status = document.getElementById('optTHLOLAD_Status').selectedIndex;
	var txtTHLOLAD_Reason = document.getElementById('txtTHLOLAD_Reason').value;
		
		if(optTHLOLAD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}
		
		if(optTHLOLAD_Status == 2) {
			if (txtTHLOLAD_Reason.replace(" ", "") == "") {	
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
if(!isset($_SESSION['User_ID'])) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
	
require_once "./include/template.inc";
$page=new Template();
$act=$_GET["act"];
$DocID=$_GET["id"];

// Cek apakah user berikut memiliki hak untuk approval
$cApp_query="SELECT DISTINCT a.A_ApproverID
		  	 FROM TH_LoanOfLandAcquisitionDocument thlolad, M_Approval a
			 WHERE thlolad.THLOLAD_Delete_Time is NULL 
			 AND a.A_ApproverID='$_SESSION[User_ID]' 
			 AND a.A_Status='2' 
			 AND a.A_TransactionCode=thlolad.THLOLAD_LoanCode 
			 AND thlolad.THLOLAD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));

$appQuery=(($act=='approve')&&($approver=="1"))?"AND a.A_ApproverID='$_SESSION[User_ID]'":"AND a.A_Status='2'";
	
$query = "SELECT DISTINCT thlolad.THLOLAD_ID, 
						  thlolad.THLOLAD_LoanCode, 
						  thlolad.THLOLAD_LoanDate, 
						  u.User_ID,
						  u.User_FullName, 
						  c.Company_Name, 
						  thlolad.THLOLAD_Status,
						  lc.LoanCategory_Name,
						  lc.LoanCategory_ID,
						  thlolad.THLOLAD_Information,
						  thlolad.THLOLAD_Reason,
						  c.Company_ID,
						  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=a.A_ApproverID) waitingApproval
		  FROM TH_LoanOfLandAcquisitionDocument thlolad
		  LEFT JOIN M_User u
			ON thlolad.THLOLAD_UserID=u.User_ID
		  LEFT JOIN M_Company c
			ON thlolad.THLOLAD_CompanyID=c.Company_ID 
		  LEFT JOIN M_Approval a
			ON a.A_TransactionCode=thlolad.THLOLAD_LoanCode 
			$appQuery 
		  LEFT JOIN M_LoanCategory lc
			ON thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
		  WHERE thlolad.THLOLAD_Delete_Time is NULL 	  
		  AND thlolad.THLOLAD_ID='$DocID'
		  ORDER BY waitingApproval DESC";
$arr = mysql_fetch_array(mysql_query($query));
$floandate=date("j M Y", strtotime($arr['THLOLAD_LoanDate']));

$MainContent ="
<form name='app-doc' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";

if(($act=='approve')&&($approver=="1"))
	$MainContent .="<th colspan=3>Persetujuan Permohonan Permintaan Dokumen Pembebasan Lahan</th>";
else
	$MainContent .="<th colspan=3>Permohonan Permintaan Dokumen Pembebasan Lahan</th>";

if ($arr['User_ID']==$_SESSION['User_ID']){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td width='67%'>
			<input name='txtTHLOLAD_ID' type='hidden' value='$arr[THLOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOLAD_LoanCode]'/>
			$arr[THLOLAD_LoanCode]
		</td>
		<td width='3%'>
			<a href='print-loan-of-land-acquisition-document.php?id=$arr[THLOLAD_LoanCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else{
	$MainContent .="
	<tr>
		<td width='30%'>Kode Permintaan</td>
		<td colspan='2'>
			<input name='txtTHLOLAD_ID' type='hidden' value='$arr[THLOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THLOLAD_LoanCode]'/>
			$arr[THLOLAD_LoanCode]
		</td>
	</tr>";
}

$MainContent .="	
<tr>
	<td>Tanggal Permintaan</td>
	<td colspan='2'><input name='txtDLA_RegTime' type='hidden' value='$arr[THLOLAD_LoanDate]'>$floandate</td>
</tr>
<tr>
	<td>Nama Pendaftar</td>
	<td colspan='2'><input name='txtDLA_RegUserID' type='hidden' value='$arr[User_ID]'>$arr[User_FullName]</td>
</tr>
<tr>
	<td>Nama Perusahaan</td>
	<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
</tr>
<tr>
	<td>Kategori Permintaan</td>
	<td colspan='2'><input type='hidden' name='optTHLOLAD_LoanCategoryID' value='$arr[LoanCategory_ID]'>$arr[LoanCategory_Name]</td>
</tr>
<tr>
	<td>Keterangan</td>
	<td colspan='2'>$arr[THLOLAD_Information]</td>
</tr>";
	
// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
if(($act=='approve')&&($approver=="1")) {
	$MainContent .="	
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHLOLAD_Status' id='optTHLOLAD_Status'>
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
			<textarea name='txtTHLOLAD_Reason' id='txtTHLOLAD_Reason' cols='50' rows='2'>$arr[THLOLAD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>";
}else {
	$MainContent .="
		<tr>
			<td>Status Dokumen</td>";
	if($arr[THLOLAD_Status]=="waiting"){
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr[THLOLAD_Status]=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOLAD_Reason]</td>
		</tr>";
	}else if($arr[THLOLAD_Status]=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THLOLAD_Reason]</td>
		</tr>";
	}else {
		$MainContent .="<td colspan='2'>Draft</td></tr>";
	}
}

$MainContent .="</table>";

// DETAIL Permintaan DOKUMEN Land Acquisition	
$MainContent .="
<div class='detail-title'>Daftar Dokumen</div>
<table width='100%' id='mytable' class='stripeMe'>
<tr>
   	<th>No</th>
    <th>Kode Dokumen</th>
    <th>Tahap GRL</th>
  	<th>Periode GRL</th>
    <th>Tanggal Dokumen</th>
  	<th>Blok</th>
    <th>Desa</th>
    <th>Pemilik</th>
    <th>Ket Dokumen</th>
    <th>Ket Permintaan</th>
</tr>";
	
$query ="SELECT tdlolad.TDLOLAD_ID,tdlolad.TDLOLAD_DocCode, tdlolad.TDLOLAD_Information,
				dla.DLA_Phase, dla.DLA_Period, dla.DLA_DocDate, dla.DLA_Block, dla.DLA_Village,
				dla.DLA_Owner, dla.DLA_Information
		 FROM TD_LoanOfLandAcquisitionDocument tdlolad, M_DocumentLandAcquisition dla
		 WHERE tdlolad.TDLOLAD_THLOLAD_ID='$DocID' 
		 AND tdlolad.TDLOLAD_Delete_Time IS NULL
		 AND dla.DLA_Code=tdlolad.TDLOLAD_DocCode";
$sql = mysql_query($query);
$no=1;

while ($arr = mysql_fetch_array($sql)) {
	$fperdate=date("j M Y", strtotime($arr['DLA_Period']));
	$fdocdate=date("j M Y", strtotime($arr['DLA_DocDate']));

	$MainContent .="
	<tr>
		<td class='center'>
			<input type='hidden' name='txtTDLOLAD_ID[]' value='$arr[TDLOLAD_ID]'/>$no
		</td>
		<td class='center'><input name='txtDLA_Code[]' type='hidden' value='$arr[TDLOLAD_DocCode]'>$arr[TDLOLAD_DocCode]</td>
		<td class='center'><input name='txtDLA_Phase[]' type='hidden' value='$arr[DLA_Phase]'>$arr[DLA_Phase]</td>
		<td class='center'><input name='txtDLA_Period[]' type='hidden' value='$arr[DLA_Period]'>$fperdate</td>
		<td class='center'><input name='txtDLA_DocDate[]' type='hidden' value='$arr[DLA_DocDate]'>$fdocdate</td>
		<td class='center'><input name='txtDLA_Block[]' type='hidden' value='$arr[DLA_Block]'>$arr[DLA_Block]</td>
		<td class='center'><input name='txtDLA_Village[]' type='hidden' value='$arr[DLA_Village]'>$arr[DLA_Village]</td>
		<td class='center'><input name='txtDLA_Owner[]' type='hidden' value='$arr[DLA_Owner]'>$arr[DLA_Owner]</td>
		<td class='center'><input name='txtDLA_Information[]' type='hidden' value='$arr[DLA_Information]'>$arr[DLA_Information]</td>
		<td class='center'>$arr[TDLOLAD_Information]</td>
	</tr>";
	$no=$no+1;
}

if(($act=='approve')&&($approver=="1")) {
	$MainContent .="
	<tr>
		<th colspan=11>
			<input name='approval' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
			<input name='cancel' type='submit' value='Batal' class='button'/>
		</th>
	</tr>";
}
$MainContent .="</table></form>";


/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}

if(isset($_POST[approval])) {
	$A_TransactionCode=$_POST['txtA_TransactionCode'];
	$A_ApproverID=$_SESSION['User_ID'];
	$A_Status=$_POST['optTHLOLAD_Status'];
	$THLOLAD_Reason=str_replace("<br>", "\n",$_POST['txtTHLOLAD_Reason']);
				
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
			/*$nStep=$step+1;
			$query = "UPDATE M_Approval
						SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_Step='$nStep'";
			if ($sql = mysql_query($query)) {
				// Kirim Email ke Approver selanjutnya
				mail_loan_doc($A_TransactionCode);
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";			
			}*/
			$nStep=$step+1;

			if ($_POST['optTHLOLAD_LoanCategoryID'] != '3') { $jenis = '5'; }
			else if ($_POST['optTHLOLAD_LoanCategoryID'] == '3') { $jenis = '6'; }
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
				//echo $query; echo '<br /><br />';
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
				} else;
			}
			echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
		else {
			$query = "UPDATE TH_LoanOfLandAcquisitionDocument
						SET THLOLAD_Status='accept', THLOLAD_Update_UserID='$A_ApproverID', THLOLAD_Update_Time=sysdate()
						WHERE THLOLAD_LoanCode='$A_TransactionCode'
						AND THLOLAD_Delete_Time IS NULL";
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
						WHERE DocumentGroup_ID ='3'";
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
				$txtDLA_Code=$_POST['txtDLA_Code'];
				$jumlah=count($txtDLA_Code);
			
				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Permintaan Dokumen	
					$CT_Code="$newnum/DREQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";
					
					switch ($_POST[optTHLOLAD_LoanCategoryID]) {
						case "1":
							$docStatus="3";
							break;
						case "2":
							$docStatus="3";
							break;
						case "3":
							$docStatus="1";
							break;
					}

					$query1 = "UPDATE M_DocumentLandAcquisition dla, M_DocumentLandAcquisitionAttribute dlaa
							   SET dla.DLA_Status ='$docStatus',dla.DLA_Update_Time=sysdate(),
								   dla.DLA_Update_UserID='$_SESSION[User_ID]',
								   dlaa.DLAA_Status ='$docStatus',dlaa.DLAA_Update_Time=sysdate(),
								   dlaa.DLAA_Update_UserID='$_SESSION[User_ID]'
							   WHERE dla.DLA_Code='$txtDLA_Code[$i]'
							   AND dlaa.DLAA_DLA_ID=dla.DLA_ID ";
					$sql= "INSERT INTO M_CodeTransaction 
								VALUES (NULL,'$CT_Code','$nnum','DREQ','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$_SESSION[User_ID]',sysdate(),
										'$_SESSION[User_ID]',sysdate(),NULL,NULL)";
					$sql1 = "UPDATE TD_LoanOfLandAcquisitionDocument
								SET TDLOLAD_Code ='$CT_Code',TDLOLAD_Update_Time=sysdate(),
									TDLOLAD_Update_UserID='$_SESSION[User_ID]'
								WHERE TDLOLAD_THLOLAD_ID='$_POST[txtTHLOLAD_ID]'
								AND TDLOLAD_DocCode='$txtDLA_Code[$i]'";
										
					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDLA_RegUserID'], 3, 1 );
				mail_notif_loan_doc($A_TransactionCode, "cust0002", 3 );
				
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";			
		}		
	}
	
	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_LoanOfLandAcquisitionDocument
					SET THLOLAD_Status='reject', THLOLAD_Reason='$THLOLAD_Reason', THLOLAD_Update_UserID='$A_ApproverID',
					    THLOLAD_Update_Time=sysdate()
					WHERE THLOLAD_LoanCode='$A_TransactionCode'";
		
		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID', 
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";

		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
			
			// Mengubah Status Dokumen menjadi "TERSEDIA"
				$txtDLA_Code=$_POST['txtDLA_Code'];
				$jumlah=count($txtDLA_Code);
			
				for($i=0;$i<$jumlah;$i++){
			
					$query1 = "UPDATE M_DocumentLandAcquisition dla, M_DocumentLandAcquisitionAttribute dlaa
							   SET dla.DLA_Status ='1',dla.DLA_Update_Time=sysdate(),
								   dla.DLA_Update_UserID='$_SESSION[User_ID]',
								   dlaa.DLAA_Status ='1',dlaa.DLAA_Update_Time=sysdate(),
								   dlaa.DLAA_Update_UserID='$_SESSION[User_ID]'
							   WHERE dla.DLA_Code='$txtDLA_Code[$i]'
							   AND dlaa.DLAA_DLA_ID=dla.DLA_ID";
					$mysqli->query($query1);
				}
				mail_notif_loan_doc($A_TransactionCode, $_POST['txtDLA_RegUserID'], 4 );
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