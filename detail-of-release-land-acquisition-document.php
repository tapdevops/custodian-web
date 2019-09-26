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
include ("./include/mother-variable.php");
?>
<title>Custodian System | Detail Pengeluaran Dokumen Pembebasan Lahan</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldocla.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHRLOLAD_Status = document.getElementById('optTHRLOLAD_Status').selectedIndex;
	var txtTHRLOLAD_Reason = document.getElementById('txtTHRLOLAD_Reason').value;

		if(optTHRLOLAD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHRLOLAD_Status == 2) {
			if (txtTHRLOLAD_Reason.replace(" ", "") == "") {
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
			  	 FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_Approval dra
				 WHERE thrlolad.THRLOLAD_Delete_Time is NULL
				 AND dra.A_ApproverID='$mv_UserID'
				 AND dra.A_Status='2'
				 AND dra.A_TransactionCode=thrlolad.THRLOLAD_ReleaseCode
				 AND thrlolad.THRLOLAD_ID='$DocID'";
	$cApp_sql=mysql_query($cApp_query);
	$approver=mysql_num_rows($cApp_sql);

	if(($act=='approve')&&($approver=="1")) {
$query = "SELECT DISTINCT thrlolad.THRLOLAD_ID, thrlolad.THRLOLAD_ReleaseCode, thrlolad.THRLOLAD_ReleaseDate, u.User_ID,
          				  u.User_FullName, c.Company_Name, thrlolad.THRLOLAD_Status, thrlolad.THRLOLAD_Information,
		  				  thrlolad.THRLOLAD_Reason,c.Company_ID, thlolad.THLOLAD_UserID, thlolad.THLOLAD_LoanCategoryID,
						  thrlolad.THRLOLAD_DocumentReceived, thrlolad.THRLOLAD_ReasonOfDocumentCancel,
						  thlolad.THLOLAD_DocumentType tipe_dokumen, thlolad.THLOLAD_SoftcopyReceiver email_softcopy
		  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_User u, M_Company c, M_Approval dra,
			   TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad
		  WHERE thrlolad.THRLOLAD_Delete_Time is NULL
		  AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
		  AND thlolad.THLOLAD_CompanyID=c.Company_ID
		  AND thrlolad.THRLOLAD_UserID=u.User_ID
		  AND dra.A_ApproverID='$mv_UserID'
		  AND dra.A_TransactionCode=thrlolad.THRLOLAD_ReleaseCode
		  AND thrlolad.THRLOLAD_ID='$DocID'";
	}
	else {
$query = "SELECT DISTINCT thrlolad.THRLOLAD_ID, thrlolad.THRLOLAD_ReleaseCode, thrlolad.THRLOLAD_ReleaseDate, u.User_ID,
          				  u.User_FullName, c.Company_Name, thrlolad.THRLOLAD_Status, thrlolad.THRLOLAD_Information,
		  	         	  thrlolad.THRLOLAD_Reason,c.Company_ID, thlolad.THLOLAD_UserID, thlolad.THLOLAD_LoanCategoryID,
						  thrlolad.THRLOLAD_DocumentReceived, thrlolad.THRLOLAD_ReasonOfDocumentCancel,
						  thlolad.THLOLAD_DocumentType tipe_dokumen, thlolad.THLOLAD_SoftcopyReceiver email_softcopy
		  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_User u, M_Company c, M_Approval dra,
		  	   TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad
		  WHERE thrlolad.THRLOLAD_Delete_Time is NULL
		  AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
		  AND thlolad.THLOLAD_CompanyID=c.Company_ID
		  AND thrlolad.THRLOLAD_UserID=u.User_ID
		  AND dra.A_TransactionCode=thrlolad.THRLOLAD_ReleaseCode
		  AND thrlolad.THRLOLAD_ID='$DocID'
		  AND (thlolad.THLOLAD_UserID='$mv_UserID' OR u.User_ID='$mv_UserID')";
	}

$sql = mysql_query($query);
$arr = mysql_fetch_array($sql);

$showFormKonfirmasiPenerimaanDokumen = 0;
if( $arr['THRLOLAD_DocumentReceived'] == NULL && $arr['THRLOLAD_Status']=="accept" && ($arr['THLOLAD_UserID'] == $mv_UserID)){ //Arief F - 21092018
	//Jika user adalah pengaju (untuk mengonfirmasi dokumen sudah diteriim atau tidak)
	$showFormKonfirmasiPenerimaanDokumen = 1;
} //Arief F - 21092018

$regdate=strtotime($arr['THRLOLAD_ReleaseDate']);
$fregdate=date("j M Y", $regdate);

		// Cek apakah Staff Custodian atau bukan.
		// Staff Custodian memiliki wewenang untuk print pengeluaran dokumen.
		$cs_query = "SELECT *
					 FROM M_DivisionDepartmentPosition ddp, M_Department d
					 WHERE ddp.DDP_DeptID=d.Department_ID
					 AND ddp.DDP_UserID='$mv_UserID'
					 AND d.Department_Name LIKE '%Custodian%'";
		$cs_sql = mysql_query($cs_query);
		$custodian = mysql_num_rows($cs_sql);

$MainContent ="
	<form name='app-doc' method='post' action='$PHP_SELF'>
	<input name='optTHLOLAD_LoanCategoryID' type='hidden' value='$arr[THLOLAD_LoanCategoryID]'>
	<table width='100%' id='mytable' class='stripeMe'>";
	if(($act=='approve')&&($approver=="1"))
		$MainContent .="<th colspan=3>Persetujuan Pengeluaran Dokumen Pembebasan Lahan</th>";
	else
		$MainContent .="<th colspan=3>Pengeluaran Dokumen Pembebasan Lahan</th>";

$MainContent .="
	<tr>
		<td width='30%'>Kode Pengeluaran</td>";
if(($arr[THRLOLAD_Status]=="accept") && ($custodian==1) ){
$MainContent .="

		<td width='67%'>
			<input name='txtTHRLOLAD_ID' type='hidden' value='$arr[THRLOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THRLOLAD_ReleaseCode]'/>
			$arr[THRLOLAD_ReleaseCode]
		</td>
		<td width='3%'>
			<a href='print-release-of-land-acquisition-document.php?id=$arr[THRLOLAD_ReleaseCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>";
}
else {
$MainContent .="

		<td width='70%' colspan='2'>
			<input name='txtTHRLOLAD_ID' type='hidden' value='$arr[THRLOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THRLOLAD_ReleaseCode]'/>
			$arr[THRLOLAD_ReleaseCode]
		</td>";
}
$MainContent .="
	</tr>
	<tr>
		<td>Tanggal Pengeluaran</td>
		<td colspan='2'><input name='txtDLA_RegTime' type='hidden' value='$arr[THRLOLAD_ReleaseDate]'>$fregdate</td>
	</tr>
	<tr>
		<td>Dikeluarkan Oleh</td>
		<td colspan='2'><input name='txtDLA_RegUserID' type='hidden' value='$arr[User_ID]'>
		<input name='txtTHLOLAD_UserID' type='hidden' value='$arr[THLOLAD_UserID]'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td>Perusahaan</td>
		<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td>Keterangan</td>
		<td colspan='2'><pre>$arr[THRLOLAD_Information]</pre></td>
	</tr>";
	if($arr['tipe_dokumen'] == "SOFTCOPY"){
$MainContent .="
	<tr>
		<td>Email Penerima Dokumen</td>
		<td colspan='2'><pre>$arr[email_softcopy]</pre></td>
	</tr>
";
	}

	// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
	if(($act=='approve')&&($approver=="1")) {
$MainContent .="
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHRLOLAD_Status' id='optTHRLOLAD_Status'>
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
			<textarea name='txtTHRLOLAD_Reason' id='txtTHRLOLAD_Reason' cols='50' rows='2'>$arr[THRLOLAD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>
";
	}else {
$MainContent .="
	<tr>
		<td>Status Dokumen</td>
";
	if($arr[THRLOLAD_Status]=="waiting") {
		$query1="SELECT u.User_FullName
				 FROM M_Approval dra, M_User u
				 WHERE dra.A_TransactionCode='$arr[THRLOLAD_ReleaseCode]'
				 AND dra.A_Status='2'
				 AND dra.A_ApproverID=u.User_ID";
		$sql1 = mysql_query($query1);
		$arr1=mysql_fetch_array($sql1);
$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr1[User_FullName]</td></tr>";
	}
	else if($arr[THRLOLAD_Status]=="accept") {
$MainContent .="
		<td colspan='2'>Disetujui</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THRLOLAD_Reason]</pre></td>
	</tr>";
	//Tampil Form Penerimaan Dokumen
		if( $showFormKonfirmasiPenerimaanDokumen == 1 ){ //Arief F - 21092018
	$MainContent .="
		<tr>
			<td>Dokumen sudah diterima</td>
			<td colspan='2'>
				<select name='optTHRLOLAD_DocumentReceived' id='optTHRLOLAD_DocumentReceived'>
					<option value='0'>--- Menungu Konfirmasi ---</option>
					<option value='1'>Sudah</option>
					<option value='2'>Batal</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Ket. Batal Terima Dokumen</td>
			<td colspan='2'>
				<textarea name='txtTHRLOLAD_ReasonOfDocumentCancel' id='txtTHRLOLAD_ReasonOfDocumentCancel' cols='50' rows='2'>$arr[THRLOLAD_ReasonOfDocumentCancel]</textarea>
				<br>*Wajib Diisi Apabila Dokumen Batal Diterima.
			</td>
		</tr>";
		} //Arief F - 21092018

		if($arr['THRLOLAD_DocumentReceived'] == 1){ //Arief F - 21092018
			$MainContent .="
			<tr>
				<td>Dokumen sudah diterima</td>
				<td colspan='2'>
					Sudah
				</td>
			</tr>";
		}elseif($arr['THRLOLAD_DocumentReceived'] == 2){ //Arief F - 21092018
			$MainContent .="
			<tr>
				<td>Dokumen sudah diterima</td>
				<td colspan='2'>
					Batal
				</td>
			</tr>
			<tr>
				<td>Ket. Batal Terima Dokumen</td>
				<td colspan='2'>
					$arr[THRLOLAD_ReasonOfDocumentCancel]
				</td>
			</tr>";
		} //Arief F - 21092018
	}
	else if($arr[THRLOLAD_Status]=="reject") {
$MainContent .="
		<td colspan='2'>Ditolak</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THRLOLAD_Reason]</pre></td>
	</tr>
	";
	}
	else {
$MainContent .="
		<td colspan='2'>Draft</td></tr>";
	}
}

$MainContent .="
	</table>";

	// DETAIL DOKUMEN GRL
$MainContent .="
	<div class='detail-title'>Daftar Dokumen</div>
	<table width='100%' id='mytable' class='stripeMe'>
	<tr>
    	<th>No</th>
        <th>Kode Permintaan</th>
        <th>Kode Dokumen</th>
        <th>Tahap GRL</th>
    	<th>Periode GRL</th>
        <th>Tanggal Dokumen</th>
       	<th>Blok</th>
        <th>Desa</th>
        <th>Pemilik</th>
        <th>Keterangan Pengeluaran Dokumen</th>
        <th>Waktu Pengembalian</th>
    </tr>";

	$query = "SELECT tdlolad.TDLOLAD_ID, tdlolad.TDLOLAD_Code, dla.DLA_ID, dla.DLA_Code, tdlolad.TDLOLAD_Information,
				   	 dla.DLA_Phase, dla.DLA_Period, dla.DLA_DocDate, dla.DLA_Block, dla.DLA_Village,
				   	 dla.DLA_Owner, dla.DLA_Information, tdrlolad.TDRLOLAD_LeadTime, tdrlolad.TDRLOLAD_Information
			  FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
				   M_DocumentLandAcquisition dla
			  WHERE tdrlolad.TDRLOLAD_THRLOLAD_ID='$DocID'
			  AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
			  AND tdlolad.TDLOLAD_DocCode=dla.DLA_Code
			  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID";
	$sql = mysql_query($query);
	$no=1;
	while ($arr = mysql_fetch_array($sql)) {
		if ( strpos($arr['TDRLOLAD_LeadTime'], '0000-00-00') !== false ){
			$fLeadTime="-";
		}
		else {
			$LeadTime=strtotime($arr['TDRLOLAD_LeadTime']);
			$fLeadTime=date("j M Y", $LeadTime);
		}
		$perdate=strtotime($arr['DLA_Period']);
		$fperdate=date("j M Y", $perdate);
		$docdate=strtotime($arr['DLA_DocDate']);
		$fdocdate=date("j M Y", $docdate);

$MainContent .="
		<tr>
			<td class='center'>
				<input type='hidden' name='txtTDRLOLAD_ID[]' value='$arr[TDRLOLAD_ID]'/>
				<input type='hidden' name='txtDLA_ID[]' value='$arr[DLA_ID]'/>$no
			</td>
			<td class='center'>
				<input name='txtTDRLOLAD_TDLOLAD_ID[]' type='hidden' value='$arr[TDLOLAD_ID]'>
				<input name='txtTDLOLAD_Code[]' type='hidden' value='$arr[TDLOLAD_Code]'>$arr[TDLOLAD_Code]</td>
			<td class='center'>$arr[DLA_Code]</td>
			<td class='center'>$arr[DLA_Phase]</td>
			<td class='center'>$fperdate</td>
			<td class='center'>$fdocdate</td>
			<td class='center'>$arr[DLA_Block]</td>
			<td class='center'>$arr[DLA_Village]</td>
			<td class='center'>$arr[DLA_Owner]</td>
			<td class='center'><pre>$arr[TDRLOLAD_Information]</pre></td>
			<td class='center'>$fLeadTime</td>
		</tr>
		";
		$no=$no+1;
	}
	if(($act=='approve')&&($approver=="1")) {

$MainContent .="
	<th colspan=50>
		<input name='approval' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>";
	}
	//Tampil Form Penerimaan Dokumen
	if( $showFormKonfirmasiPenerimaanDokumen == 1 ){ //Arief F - 21092018
$MainContent .="
	<th colspan=11>
		<input name='konfirmasi_penerimaandokumen' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>"; //Arief F - 21092018
	} //Arief F - 21092018
$MainContent .="
		</table></form>
";


/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}

if(isset($_POST['konfirmasi_penerimaandokumen'])){
	$txtTHRLOLAD_ID=$_POST['txtTHRLOLAD_ID'];
	$optTHRLOLAD_DocumentReceived=$_POST['optTHRLOLAD_DocumentReceived'];

	$query = "UPDATE TH_ReleaseOfLandAcquisitionDocument
				SET THRLOLAD_DocumentReceived='$optTHRLOLAD_DocumentReceived',
				THRLOLAD_ReasonOfDocumentCancel='$_POST[txtTHRLOLAD_ReasonOfDocumentCancel]',
				THRLOLAD_Update_UserID='$mv_UserID', THRLOLAD_Update_Time=sysdate()
				WHERE THRLOLAD_ID='$txtTHRLOLAD_ID'
				AND THRLOLAD_Delete_Time IS NULL";
	$sql = mysql_query($query);
	if($sql){
		if($optTHRLOLAD_DocumentReceived == "1" ) $status = 3;//Sudah Diterima
		elseif($optTHRLOLAD_DocumentReceived == "2" ) $status = 4;
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], $mv_UserID, $status,1);
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], "cust0002", $status );
		echo "<meta http-equiv='refresh' content='0; url=detail-of-release-land-acquisition-document.php?id=$txtTHRLOLAD_ID'>";
	}else{
		$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
	}
}

if(isset($_POST[approval])) {
	$A_TransactionCode=$_POST['txtA_TransactionCode'];
	$A_ApproverID=$mv_UserID;
	$A_Status=$_POST['optTHRLOLAD_Status'];
	$THRLOLAD_Reason=str_replace("<br>", "\n",$_POST['txtTHRLOLAD_Reason']);

	// MENCARI TAHAP APPROVAL USER TERSEBUT
	$query = "SELECT *
			  FROM M_Approval
			  WHERE A_TransactionCode='$A_TransactionCode'
			  AND A_ApproverID='$A_ApproverID'";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);
	$step=$arr['A_Step'];
	$AppDate=$arr['A_ApprovalDate'];

	if ($AppDate==NULL) {

	// MENCARI JUMLAH APPROVAL
	$query = "SELECT MAX(A_Step) AS jStep
			  FROM M_Approval
			  WHERE A_TransactionCode='$A_TransactionCode'";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);
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
			$query = "UPDATE M_Approval
					  SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
					  WHERE A_TransactionCode='$A_TransactionCode'
					  AND A_Step='$nStep'";
			if ($sql = mysql_query($query)) {
				mail_release_doc($A_TransactionCode);
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
		}
		else {
			$query = "UPDATE TH_ReleaseOfLandAcquisitionDocument
				      SET THRLOLAD_Status='accept', THRLOLAD_Update_UserID='$A_ApproverID',
						  THRLOLAD_Update_Time=sysdate()
					  WHERE THRLOLAD_ReleaseCode='$A_TransactionCode'
					  AND THRLOLAD_Delete_Time IS NULL";
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
			$sql = mysql_query($query);
			$field = mysql_fetch_array($sql);
			$Company_Code=$field['Company_Code'];

			// Cari Kode Dokumen Grup
			$query = "SELECT *
						FROM M_DocumentGroup
						WHERE DocumentGroup_ID ='3'";
			$sql = mysql_query($query);
			$field = mysql_fetch_array($sql);
			$DocumentGroup_Code=$field['DocumentGroup_Code'];

			// Cari No Pengeluaran Dokumen Terakhir
			$query = "SELECT MAX(CT_SeqNo)
						FROM M_CodeTransaction
						WHERE CT_Year='$regyear'
						AND CT_Action='DOUT'
						AND CT_GroupDocCode='$DocumentGroup_Code'
						AND CT_Delete_Time is NULL";
			$sql = mysql_query($query);
			$field = mysql_fetch_array($sql);

			if($field[0]==NULL)
				$maxnum=0;
			else
				$maxnum=$field[0];
			$nnum=$maxnum+1;

				// Mengubah Status Dokumen menjadi "DIPINJAM"
				$txtDLA_ID=$_POST['txtDLA_ID'];
				$txtTDRLOLAD_TDLOLAD_ID=$_POST['txtTDRLOLAD_TDLOLAD_ID'];
				$jumlah=count($txtDLA_ID);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Pengeluaran Dokumen
					$CT_Code="$newnum/DOUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST[optTHLOLAD_LoanCategoryID]) {
						case "1":
							$docStatus="4";
							$code="0";
							break;
						case "2":
							$docStatus="5";
							$code=NULL;
							break;
						case "3":
							$docStatus="1";
							$code=NULL;
							break;
					}
					$query1 = "UPDATE M_DocumentLandAcquisition dla, M_DocumentLandAcquisitionAttribute dlaa
							   SET dla.DLA_Status ='$docStatus',dla.DLA_Update_Time=sysdate(),
								   dla.DLA_Update_UserID='$mv_UserID',
								   dlaa.DLAA_Status ='$docStatus',dlaa.DLAA_Update_Time=sysdate(),
								   dlaa.DLAA_Update_UserID='$mv_UserID'
							   WHERE dla.DLA_ID='$txtDLA_ID[$i]'
							   AND dlaa.DLAA_DLA_ID=dla.DLA_ID";

					$sql= "INSERT INTO M_CodeTransaction
						   VALUES (NULL,'$CT_Code','$nnum','DOUT','$Company_Code','$DocumentGroup_Code',
								   '$rmonth','$regyear','$mv_UserID',sysdate(),
								   '$mv_UserID',sysdate(),NULL,NULL)";

					$sql1 = "UPDATE TD_ReleaseOfLandAcquisitionDocument
							 SET TDRLOLAD_Code ='$CT_Code', TDRLOLAD_ReturnCode='$code',
							 	 TDRLOLAD_Update_Time=sysdate(),TDRLOLAD_Update_UserID='$mv_UserID'
							 WHERE TDRLOLAD_THRLOLAD_ID='$_POST[txtTHRLOLAD_ID]'
							 AND TDRLOLAD_TDLOLAD_ID='$txtTDRLOLAD_TDLOLAD_ID[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOLAD_UserID'], 4 );
				mail_notif_release_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
		}
	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_ReleaseOfLandAcquisitionDocument
					SET THRLOLAD_Status='reject', THRLOLAD_Reason='$THRLOLAD_Reason',
						THRLOLAD_Update_Time=sysdate(), THRLOLAD_Update_UserID='$A_ApproverID'
					WHERE THRLOLAD_ReleaseCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";
		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
			$txtDLA_ID=$_POST['txtDLA_ID'];
			$jumlah=count($txtDLA_ID);

			for ($i=0;$i<$jumlah;$i++) {
				$query = "UPDATE M_DocumentLandAcquisition dla, M_DocumentLandAcquisitionAttribute dlaa
						  SET dla.DLA_Status ='1',dla.DLA_Update_Time=sysdate(),
							  dla.DLA_Update_UserID='$mv_UserID',
							  dlaa.DLAA_Status ='1',dlaa.DLAA_Update_Time=sysdate(),
							  dlaa.DLAA_Update_UserID='$mv_UserID'
						  WHERE dla.DLA_ID='$txtDLA_ID[$i]'
						  AND dlaa.DLAA_DLA_ID=dla.DLA_ID";
				$mysqli->query($query);
			}
			mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOLAD_UserID'], 4 );
			mail_notif_release_doc($A_TransactionCode, $_POST['txtDLA_RegUserID'], 4 );
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
