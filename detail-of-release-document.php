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
<title>Custodian System | Detail Pengeluaran Dokumen</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldoc.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHROLD_Status = document.getElementById('optTHROLD_Status').selectedIndex;
	var txtTHROLD_Reason = document.getElementById('txtTHROLD_Reason').value;

		if(optTHROLD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHROLD_Status == 2) {
			if (txtTHROLD_Reason.replace(" ", "") == "") {
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
			  	 FROM TH_ReleaseOfLegalDocument throld, M_Approval dra
				 WHERE throld.THROLD_Delete_Time is NULL
				 AND dra.A_ApproverID='$mv_UserID'
				 AND dra.A_Status='2'
				 AND dra.A_TransactionCode=throld.THROLD_ReleaseCode
				 AND throld.THROLD_ID='$DocID'";
	$cApp_sql=mysql_query($cApp_query);
	$approver=mysql_num_rows($cApp_sql);

	if(($act=='approve')&&($approver=="1")) {
$query = "SELECT DISTINCT throld.THROLD_ID, throld.THROLD_ReleaseCode, throld.THROLD_ReleaseDate, u.User_ID,
		          u.User_FullName, c.Company_Name, throld.THROLD_Status, throld.THROLD_Information, thlold.THLOLD_UserID,
				  dg.DocumentGroup_Name, dg.DocumentGroup_ID, throld.THROLD_Reason,c.Company_ID,thlold.THLOLD_LoanCategoryID,
				  throld.THROLD_DocumentReceived, throld.THROLD_ReasonOfDocumentCancel,
				  thlold.THLOLD_DocumentType tipe_dokumen, thlold.THLOLD_SoftcopyReceiver email_softcopy
		  	FROM TH_ReleaseOfLegalDocument throld, M_User u, M_Company c, M_Approval dra,
				 M_DocumentGroup dg, TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold
			WHERE throld.THROLD_Delete_Time is NULL
			AND throld.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
			AND thlold.THLOLD_CompanyID=c.Company_ID
			AND throld.THROLD_UserID=u.User_ID
			AND dra.A_ApproverID='$mv_UserID'
			AND dra.A_TransactionCode=throld.THROLD_ReleaseCode
			AND throld.THROLD_ID='$DocID'
			AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID";
	}
	else {
$query = "SELECT DISTINCT throld.THROLD_ID, throld.THROLD_ReleaseCode, throld.THROLD_ReleaseDate, u.User_ID,
		          u.User_FullName, c.Company_Name, throld.THROLD_Status, throld.THROLD_Information, thlold.THLOLD_UserID,
				  dg.DocumentGroup_Name, dg.DocumentGroup_ID, throld.THROLD_Reason,c.Company_ID,thlold.THLOLD_LoanCategoryID,
				  throld.THROLD_DocumentReceived, throld.THROLD_ReasonOfDocumentCancel,
				  thlold.THLOLD_DocumentType tipe_dokumen, thlold.THLOLD_SoftcopyReceiver email_softcopy
		  	FROM TH_ReleaseOfLegalDocument throld, M_User u, M_Company c, M_Approval dra,
				 M_DocumentGroup dg, TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold
			WHERE throld.THROLD_Delete_Time is NULL
			AND throld.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
			AND thlold.THLOLD_CompanyID=c.Company_ID
			AND throld.THROLD_UserID=u.User_ID
			AND dra.A_TransactionCode=throld.THROLD_ReleaseCode
			AND throld.THROLD_ID='$DocID'
			AND (thlold.THLOLD_UserID='$mv_UserID' OR u.User_ID='$mv_UserID')
			AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID";
	}

$sql = mysql_query($query);
$arr = mysql_fetch_array($sql);

$showFormKonfirmasiPenerimaanDokumen = 0;
if( $arr['THROLD_DocumentReceived'] == NULL && $arr['THROLD_Status']=="accept" && ($arr['THLOLD_UserID'] == $mv_UserID)){ //Arief F - 21092018
	//Jika user adalah pengaju (untuk mengonfirmasi dokumen sudah diteriim atau tidak)
	$showFormKonfirmasiPenerimaanDokumen = 1;
} //Arief F - 21092018

$regdate=strtotime($arr['THROLD_ReleaseDate']);
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
	<input name='optTHLOLD_LoanCategoryID' type='hidden' value='$arr[THLOLD_LoanCategoryID]'>
	<table width='100%' id='mytable' class='stripeMe'>";
	if(($act=='approve')&&($approver=="1"))
		$MainContent .="<th colspan=3>Persetujuan Pengeluaran Dokumen</th>";
	else
		$MainContent .="<th colspan=3>Pengeluaran Dokumen</th>";

$MainContent .="
	<tr>
		<td width='30%'>Kode Pengeluaran</td>";
if(($arr[THROLD_Status]=="accept") && ($custodian==1) ){
$MainContent .="

		<td width='67%'>
			<input name='txtTHROLD_ID' type='hidden' value='$arr[THROLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROLD_ReleaseCode]'/>
			$arr[THROLD_ReleaseCode]
		</td>
		<td width='3%'>
			<a href='print-release-of-document.php?id=$arr[THROLD_ReleaseCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>";
}
else {
$MainContent .="

		<td width='70%' colspan='2'>
			<input name='txtTHROLD_ID' type='hidden' value='$arr[THROLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROLD_ReleaseCode]'/>
			$arr[THROLD_ReleaseCode]
		</td>";
}
$MainContent .="
	</tr>
	<tr>
		<td>Tanggal Pengeluaran</td>
		<td colspan='2'><input name='txtDL_RegTime' type='hidden' value='$arr[THROLD_ReleaseDate]'>$fregdate</td>
	</tr>
	<tr>
		<td>Dikeluarkan Oleh</td>
		<td colspan='2'><input name='txtDL_RegUserID' type='hidden' value='$arr[User_ID]'>
		<input name='txtTHLOLD_UserID' type='hidden' value='$arr[THLOLD_UserID]'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td>Perusahaan</td>
		<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td>Grup Dokumen</td>
		<td colspan='2'><input name='txtDL_GroupDocID' type='hidden' value='$arr[DocumentGroup_ID]'>$arr[DocumentGroup_Name]</td>
	</tr>
	<tr>
		<td>Keterangan</td>
		<td colspan='2'><pre>$arr[THROLD_Information]</pre></td>
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
			<select name='optTHROLD_Status' id='optTHROLD_Status'>
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
			<textarea name='txtTHROLD_Reason' id='txtTHROLD_Reason' cols='50' rows='2'>$arr[THROLD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>
";
	}else {
$MainContent .="
	<tr>
		<td>Status Dokumen</td>
";
	if($arr[THROLD_Status]=="waiting") {
		$query1="SELECT u.User_FullName
					FROM M_Approval dra, M_User u
					WHERE dra.A_TransactionCode='$arr[THROLD_ReleaseCode]'
					AND dra.A_Status='2'
					AND dra.A_ApproverID=u.User_ID";
		$sql1 = mysql_query($query1);
		$arr1=mysql_fetch_array($sql1);
$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr1[User_FullName]</td></tr>";
	}
	else if($arr[THROLD_Status]=="accept") {
$MainContent .="
		<td colspan='2'>Disetujui</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THROLD_Reason]</pre></td>
	</tr>";
	 //Tampil Form Penerimaan Dokumen
		if( $showFormKonfirmasiPenerimaanDokumen == 1 ){ //Arief F - 21092018
	$MainContent .="
	<tr>
		<td>Dokumen sudah diterima</td>
		<td colspan='2'>
			<select name='optTHROLD_DocumentReceived' id='optTHROLD_DocumentReceived'>
				<option value='0'>--- Menungu Konfirmasi ---</option>
				<option value='1'>Sudah</option>
				<option value='2'>Batal</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Ket. Batal Terima Dokumen</td>
		<td colspan='2'>
			<textarea name='txtTHROLD_ReasonOfDocumentCancel' id='txtTHROLD_ReasonOfDocumentCancel' cols='50' rows='2'>$arr[THROLD_ReasonOfDocumentCancel]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Batal Diterima.
		</td>
	</tr>";
		} //Arief F - 21092018

		if($arr['THROLD_DocumentReceived'] == 1){ //Arief F - 21092018
			$MainContent .="
			<tr>
				<td>Dokumen sudah diterima</td>
				<td colspan='2'>
					Sudah
				</td>
			</tr>";
		}elseif($arr['THROLD_DocumentReceived'] == 2){ //Arief F - 21092018
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
					$arr[THROLD_ReasonOfDocumentCancel]
				</td>
			</tr>";
		} //Arief F - 21092018
	}
	else if($arr[THROLD_Status]=="reject") {
$MainContent .="
		<td colspan='2'>Ditolak</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THROLD_Reason]</pre></td>
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

	// DETAIL DOKUMEN LEGAL
$MainContent .="
	<div class='detail-title'>Daftar Dokumen</div>
	<table width='100%' id='mytable' class='stripeMe'>
	<tr>
    	<th>No</th>
    	<th>Kode Permintaan Dokumen</th>
    	<th>Kode Dokumen</th>
    	<th>Tipe Dokumen</th>
        <th>Nomor Dokumen</th>
        <th>Keterangan</th>
        <th>Waktu Pengembalian</th>
    </tr>";

	$query = "SELECT tdrold.TDROLD_ID, tdlold.TDLOLD_ID, tdlold.TDLOLD_Code, dt.DocumentType_Name, dt.DocumentType_ID,
				     dl.DL_NoDoc, dl.DL_ID,tdrold.TDROLD_Information, dl.DL_DocCode, tdrold.TDROLD_LeadTime
				FROM TD_ReleaseOfLegalDocument tdrold, M_DocumentType dt, TD_LoanOfLegalDocument tdlold,
					 M_DocumentLegal dl
				WHERE tdrold.TDROLD_THROLD_ID='$DocID'
				AND tdrold.TDROLD_Delete_Time IS NULL
				AND tdlold.TDLOLD_DocCode=dl.DL_DocCode
				AND tdrold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				AND dl.DL_TypeDocID=dt.DocumentType_ID";
	$sql = mysql_query($query);
	$no=1;
	while ($arr = mysql_fetch_array($sql)) {
		if ( (strpos($arr['TDROLD_LeadTime'], '0000-00-00') !== false ) || ( strpos($arr['TDROLD_LeadTime'], '1970-01-01') !== false ) ){
			$fLeadTime="-";
		}
		else {
			$LeadTime=strtotime($arr['TDROLD_LeadTime']);
			$fLeadTime=date("j M Y", $LeadTime);
		}

$MainContent .="
		<tr>
			<td class='center'>
				<input type='hidden' name='txtTDROLD_ID[]' value='$arr[TDROLD_ID]'/>$no
			</td>
			<td class='center'>
				<input name='txtTDROLD_TDLOLD_ID[]' type='hidden' value='$arr[TDLOLD_ID]'>
				<input name='txtTDLOLD_Code[]' type='hidden' value='$arr[TDLOLD_Code]'>$arr[TDLOLD_Code]</td>
			<td class='center'>$arr[DL_DocCode]</td>
			<td class='center'><input name='txtDocumentType_ID[]' type='hidden' value='$arr[DocumentType_ID]'>$arr[DocumentType_Name]</td>
			<td class='center'><input name='txtDL_ID[]' type='hidden' value='$arr[DL_ID]'>$arr[DL_NoDoc]</td>
			<td class='center'><pre>$arr[TDROLD_Information]</pre></td>
			<td class='center'>$fLeadTime</td>
		</tr>
		";
		$no=$no+1;
	}
	if(($act=='approve')&&($approver=="1")) {

$MainContent .="
	<th colspan=9>
		<input name='approval' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>";
	}
	//Tampil Form Penerimaan Dokumen
	if( $showFormKonfirmasiPenerimaanDokumen == 1 ){ //Arief F - 21092018
$MainContent .="
	<th colspan=9>
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
	$txtTHROLD_ID=$_POST['txtTHROLD_ID'];
	$optTHROLD_DocumentReceived=$_POST['optTHROLD_DocumentReceived'];

	$query = "UPDATE TH_ReleaseOfLegalDocument
				SET THROLD_DocumentReceived='$optTHROLD_DocumentReceived',
					THROLD_ReasonOfDocumentCancel='$_POST[txtTHROLD_ReasonOfDocumentCancel]',
					THROLD_Update_UserID='$mv_UserID', THROLD_Update_Time=sysdate()
				WHERE THROLD_ID='$txtTHROLD_ID'
				AND THROLD_Delete_Time IS NULL";
	$sql = mysql_query($query);
	if($sql){
		if($optTHROLD_DocumentReceived == "1" ) $status = 3;//Sudah Diterima
		elseif($optTHROLD_DocumentReceived == "2" ) $status = 4;
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], $mv_UserID, $status,1);
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], "cust0002", $status );
		echo "<meta http-equiv='refresh' content='0; url=detail-of-release-document.php?id=$txtTHROLD_ID'>";
	}else{
		$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
	}
}

if(isset($_POST[approval])) {
	$A_TransactionCode=$_POST['txtA_TransactionCode'];
	$A_ApproverID=$mv_UserID;
	$A_Status=$_POST['optTHROLD_Status'];
	$THROLD_Reason=str_replace("<br>", "\n",$_POST['txtTHROLD_Reason']);

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
			if ($sql = mysql_query($query)){
				mail_release_doc($A_TransactionCode);
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
		}
		else {
			$query = "UPDATE TH_ReleaseOfLegalDocument
						SET THROLD_Status='accept', THROLD_Update_UserID='$A_ApproverID', THROLD_Update_Time=sysdate()
						WHERE THROLD_ReleaseCode='$A_TransactionCode'
						AND THROLD_Delete_Time IS NULL";
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
						WHERE DocumentGroup_ID ='$_POST[txtDL_GroupDocID]'";
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
				$txtDL_ID=$_POST['txtDL_ID'];
				$txtTDROLD_TDLOLD_ID=$_POST['txtTDROLD_TDLOLD_ID'];
				$jumlah=count($txtDL_ID);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Pengeluaran Dokumen
					$CT_Code="$newnum/DOUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST[optTHLOLD_LoanCategoryID]) {
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

					$query1 = "UPDATE M_DocumentLegal
								SET DL_Status='$docStatus', DL_Update_UserID='$A_ApproverID', DL_Update_Time=sysdate()
								WHERE DL_ID='$txtDL_ID[$i]'";

					$sql= "INSERT INTO M_CodeTransaction
								VALUES (NULL,'$CT_Code','$nnum','DOUT','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$mv_UserID',sysdate(),
										'$mv_UserID',sysdate(),NULL,NULL)";

					$sql1 = "UPDATE TD_ReleaseOfLegalDocument
								SET TDROLD_Code='$CT_Code',TDROLD_ReturnCode='$code',
									TDROLD_Update_Time=sysdate(),TDROLD_Update_UserID='$mv_UserID'
								WHERE TDROLD_THROLD_ID='$_POST[txtTHROLD_ID]'
								AND TDROLD_TDLOLD_ID='$txtTDROLD_TDLOLD_ID[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOLD_UserID'], 3);
				mail_notif_release_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
		}
	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_ReleaseOfLegalDocument
					SET THROLD_Status='reject', THROLD_Reason='$THROLD_Reason',
						THROLD_Update_Time=sysdate(), THROLD_Update_UserID='$A_ApproverID'
					WHERE THROLD_ReleaseCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";
		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
			$txtDL_ID=$_POST['txtDL_ID'];
			$jumlah=count($txtDL_ID);

			for ($i=0;$i<$jumlah;$i++) {
				$query = "UPDATE M_DocumentLegal
						  SET DL_Status='1', DL_Update_UserID='$A_ApproverID', DL_Update_Time=sysdate()
						  WHERE DL_ID='$txtDL_ID[$i]'";
				$mysqli->query($query);
			}
			mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOLD_UserID'], 4 );
			mail_notif_release_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 4 );
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
