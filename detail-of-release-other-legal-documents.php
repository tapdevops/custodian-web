<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource           																				=
= Dibuat Tanggal	: 14 Sep 2018																						=
= Update Terakhir	: -					              																	=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Detail Pengeluaran Dokumen Lainnya (Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldocol.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHROOLD_Status = document.getElementById('optTHROOLD_Status').selectedIndex;
	var txtTHROOLD_Reason = document.getElementById('txtTHROOLD_Reason').value;

		if(optTHROOLD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHROOLD_Status == 2) {
			if (txtTHROOLD_Reason.replace(" ", "") == "") {
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
			  	 FROM TH_ReleaseOfOtherLegalDocuments throold, M_Approval dra
				 WHERE throold.THROOLD_Delete_Time is NULL
				 AND dra.A_ApproverID='$mv_UserID'
				 AND dra.A_Status='2'
				 AND dra.A_TransactionCode=throold.THROOLD_ReleaseCode
				 AND throold.THROOLD_ID='$DocID'";
	$cApp_sql=mysql_query($cApp_query);
	$approver=mysql_num_rows($cApp_sql);

	if(($act=='approve')&&($approver=="1")) {
$query = "SELECT DISTINCT throold.THROOLD_ID, throold.THROOLD_ReleaseCode, throold.THROOLD_ReleaseDate, u.User_ID,
		          u.User_FullName, c.Company_Name, throold.THROOLD_Status, throold.THROOLD_Information, thloold.THLOOLD_UserID,
				  dg.DocumentGroup_Name, dg.DocumentGroup_ID, throold.THROOLD_Reason,c.Company_ID,thloold.THLOOLD_LoanCategoryID,
				  throold.THROOLD_DocumentReceived, throold.THROOLD_ReasonOfDocumentCancel,
				  thloold.THLOOLD_DocumentType tipe_dokumen, thloold.THLOOLD_SoftcopyReceiver email_softcopy
		  	FROM TH_ReleaseOfOtherLegalDocuments throold, M_User u, M_Company c, M_Approval dra,
				 M_DocumentGroup dg, TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold
			WHERE throold.THROOLD_Delete_Time is NULL
			AND throold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
			AND thloold.THLOOLD_CompanyID=c.Company_ID
			AND throold.THROOLD_UserID=u.User_ID
			AND dra.A_ApproverID='$mv_UserID'
			AND dra.A_TransactionCode=throold.THROOLD_ReleaseCode
			AND throold.THROOLD_ID='$DocID'
			AND dg.DocumentGroup_ID='5'";
	}
	else {
$query = "SELECT DISTINCT throold.THROOLD_ID, throold.THROOLD_ReleaseCode, throold.THROOLD_ReleaseDate, u.User_ID,
		          u.User_FullName, c.Company_Name, throold.THROOLD_Status, throold.THROOLD_Information, thloold.THLOOLD_UserID,
				  dg.DocumentGroup_Name, dg.DocumentGroup_ID, throold.THROOLD_Reason,c.Company_ID,thloold.THLOOLD_LoanCategoryID,
				  throold.THROOLD_DocumentReceived, throold.THROOLD_ReasonOfDocumentCancel,
				  thloold.THLOOLD_DocumentType tipe_dokumen, thloold.THLOOLD_SoftcopyReceiver email_softcopy
		  	FROM TH_ReleaseOfOtherLegalDocuments throold, M_User u, M_Company c, M_Approval dra,
				 M_DocumentGroup dg, TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold
			WHERE throold.THROOLD_Delete_Time is NULL
			AND throold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
			AND thloold.THLOOLD_CompanyID=c.Company_ID
			AND throold.THROOLD_UserID=u.User_ID
			AND dra.A_TransactionCode=throold.THROOLD_ReleaseCode
			AND throold.THROOLD_ID='$DocID'
			-- AND (thloold.THLOOLD_UserID='$mv_UserID' OR u.User_ID='$mv_UserID')
			AND dg.DocumentGroup_ID='5'";
	}

$sql = mysql_query($query);
$arr = mysql_fetch_array($sql);

$showFormKonfirmasiPenerimaanDokumen = 0;
if( $arr['THROOLD_DocumentReceived'] == NULL && $arr['THROOLD_Status']=="accept" && ($arr['THLOOLD_UserID'] == $mv_UserID)){ //Arief F - 21092018
	//Jika user adalah pengaju (untuk mengonfirmasi dokumen sudah diterima atau tidak)
	$showFormKonfirmasiPenerimaanDokumen = 1;
} //Arief F - 21092018

$regdate=strtotime($arr['THROOLD_ReleaseDate']);
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
	<input name='optTHLOOLD_LoanCategoryID' type='hidden' value='$arr[THLOOLD_LoanCategoryID]'>
	<table width='100%' id='mytable' class='stripeMe'>";
	if(($act=='approve')&&($approver=="1"))
		$MainContent .="<th colspan=3>Persetujuan Pengeluaran Dokumen Lainnya (Legal)</th>";
	else
		$MainContent .="<th colspan=3>Pengeluaran Dokumen Lainnya (Legal)</th>";

$MainContent .="
	<tr>
		<td width='30%'>Kode Pengeluaran</td>";
if(($arr[THROOLD_Status]=="accept") && ($custodian==1) ){
$MainContent .="

		<td width='67%'>
			<input name='txtTHROOLD_ID' type='hidden' value='$arr[THROOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROOLD_ReleaseCode]'/>
			$arr[THROOLD_ReleaseCode]
		</td>
		<td width='3%'>
			<a href='print-release-of-other-legal-documents.php?id=$arr[THROOLD_ReleaseCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>";
}
else {
$MainContent .="

		<td width='70%' colspan='2'>
			<input name='txtTHROOLD_ID' type='hidden' value='$arr[THROOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROOLD_ReleaseCode]'/>
			$arr[THROOLD_ReleaseCode]
		</td>";
}
$MainContent .="
	</tr>
	<tr>
		<td>Tanggal Pengeluaran</td>
		<td colspan='2'><input name='txtDOL_RegTime' type='hidden' value='$arr[THROOLD_ReleaseDate]'>$fregdate</td>
	</tr>
	<tr>
		<td>Dikeluarkan Oleh</td>
		<td colspan='2'><input name='txtDOL_RegUserID' type='hidden' value='$arr[User_ID]'>
		<input name='txtTHLOOLD_UserID' type='hidden' value='$arr[THLOOLD_UserID]'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td>Perusahaan</td>
		<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td>Grup Dokumen</td>
		<td colspan='2'><input name='txtDOL_GroupDocID' type='hidden' value='$arr[DocumentGroup_ID]'>$arr[DocumentGroup_Name]</td>
	</tr>
	<tr>
		<td>Keterangan</td>
		<td colspan='2'><pre>$arr[THROOLD_Information]</pre></td>
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
			<select name='optTHROOLD_Status' id='optTHROOLD_Status'>
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
			<textarea name='txtTHROOLD_Reason' id='txtTHROOLD_Reason' cols='50' rows='2'>$arr[THROOLD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>
";
	}else {
$MainContent .="
	<tr>
		<td>Status Dokumen</td>
";
	if($arr[THROOLD_Status]=="waiting") {
		$query1="SELECT u.User_FullName
					FROM M_Approval dra, M_User u
					WHERE dra.A_TransactionCode='$arr[THROOLD_ReleaseCode]'
					AND dra.A_Status='2'
					AND dra.A_ApproverID=u.User_ID";
		$sql1 = mysql_query($query1);
		$arr1=mysql_fetch_array($sql1);
$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr1[User_FullName]</td></tr>";
	}
	else if($arr[THROOLD_Status]=="accept") {
$MainContent .="
		<td colspan='2'>Disetujui</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THROOLD_Reason]</pre></td>
	</tr>";
	//Tampil Form Penerimaan Dokumen
		if( $showFormKonfirmasiPenerimaanDokumen == 1 ){ //Arief F - 21092018
	$MainContent .="
		<tr>
			<td>Dokumen sudah diterima</td>
			<td colspan='2'>
				<select name='optTHROOLD_DocumentReceived' id='optTHROOLD_DocumentReceived'>
					<option value='0'>--- Menungu Konfirmasi ---</option>
					<option value='1'>Sudah</option>
					<option value='2'>Batal</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Ket. Batal Terima Dokumen</td>
			<td colspan='2'>
				<textarea name='txtTHROOLD_ReasonOfDocumentCancel' id='txtTHROOLD_ReasonOfDocumentCancel' cols='50' rows='2'>$arr[THROOLD_ReasonOfDocumentCancel]</textarea>
				<br>*Wajib Diisi Apabila Dokumen Batal Diterima.
			</td>
		</tr>";
		} //Arief F - 21092018

		if($arr['THROOLD_DocumentReceived'] == 1){ //Arief F - 21092018
			$MainContent .="
			<tr>
				<td>Dokumen sudah diterima</td>
				<td colspan='2'>
					Sudah
				</td>
			</tr>";
		}elseif($arr['THROOLD_DocumentReceived'] == 2){ //Arief F - 21092018
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
					$arr[THROOLD_ReasonOfDocumentCancel]
				</td>
			</tr>";
		} //Arief F - 21092018
	}
	else if($arr[THROOLD_Status]=="reject") {
$MainContent .="
		<td colspan='2'>Ditolak</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THROOLD_Reason]</pre></td>
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
		<th>Kategori Dokumen</th>
		<th>Nama Dokumen</th>
		<th>Instansi Terkait</th>
		<th>No. Dokumen</th>
		<th>Tgl. Terbit</th>
		<th>Tgl. Berakhir</th>
        <th>Keterangan</th>
        <th>Waktu Pengembalian</th>
    </tr>";

	$query = "SELECT tdroold.TDROOLD_ID, tdloold.TDLOOLD_ID, tdloold.TDLOOLD_Code,
				     dol.DOL_ID,tdroold.TDROOLD_Information, dol.DOL_DocCode, tdroold.TDROOLD_LeadTime,
					 dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait, dol.DOL_NoDokumen, tdroold.TDROOLD_Information,
					 dol.DOL_TglTerbit, dol.DOL_TglBerakhir, dc.DocumentCategory_ID, dc.DocumentCategory_Name
				FROM TD_ReleaseOfOtherLegalDocuments tdroold, TD_LoanOfOtherLegalDocuments tdloold,
					 M_DocumentsOtherLegal dol, db_master.M_DocumentCategory dc
				WHERE tdroold.TDROOLD_THROOLD_ID='$DocID'
				AND tdroold.TDROOLD_Delete_Time IS NULL
				AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
				AND tdroold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
                AND dol.DOL_CategoryDocID=dc.DocumentCategory_ID";
	$sql = mysql_query($query);
	$no=1;
	while ($arr = mysql_fetch_array($sql)) {
		if ( (strpos($arr['TDROOLD_LeadTime'], '0000-00-00') !== false ) || ( strpos($arr['TDROOLD_LeadTime'], '1970-01-01') !== false ) ){
			$fLeadTime="-";
		}
		else {
			$LeadTime=strtotime($arr['TDROOLD_LeadTime']);
			$fLeadTime=date("j M Y", $LeadTime);
		}

        $tgl_terbit=date("j M Y", strtotime($arr['DOL_TglTerbit']));
        $tgl_berakhir=date("j M Y", strtotime($arr['DOL_TglBerakhir']));

$MainContent .="
		<tr>
			<td class='center'>
				<input type='hidden' name='txtTDROOLD_ID[]' value='$arr[TDROOLD_ID]'/>
				<input name='txtDOL_ID[]' type='hidden' value='$arr[DOL_ID]'>$no
			</td>
			<td class='center'>
				<input name='txtTDROOLD_TDLOOLD_ID[]' type='hidden' value='$arr[TDLOOLD_ID]'>
				<input name='txtTDLOOLD_Code[]' type='hidden' value='$arr[TDLOOLD_Code]'>$arr[TDLOOLD_Code]</td>
			<td class='center'>$arr[DOL_DocCode]</td>
			<td class='center'>$arr[DocumentCategory_Name]</td>
			<td class='center'>$arr[DOL_NamaDokumen]</td>
			<td class='center'>$arr[DOL_InstansiTerkait]</td>
			<td class='center'>$arr[DOL_NoDokumen]</td>
			<td class='center'>$tgl_terbit</td>
			<td class='center'>$tgl_berakhir</td>
			<td class='center'><pre>$arr[TDROOLD_Information]</pre></td>
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
	$txtTHROOLD_ID=$_POST['txtTHROOLD_ID'];
	$optTHROOLD_DocumentReceived=$_POST['optTHROOLD_DocumentReceived'];

	$query = "UPDATE TH_ReleaseOfOtherLegalDocuments
				SET THROOLD_DocumentReceived='$optTHROOLD_DocumentReceived',
				THROOLD_ReasonOfDocumentCancel='$_POST[txtTHROOLD_ReasonOfDocumentCancel]',
				THROOLD_Update_UserID='$mv_UserID', THROOLD_Update_Time=sysdate()
				WHERE THROOLD_ID='$txtTHROOLD_ID'
				AND THROOLD_Delete_Time IS NULL";
	$sql = mysql_query($query);
	if($sql){
		if($optTHROOLD_DocumentReceived == "1" ) $status = 3;//Sudah Diterima
		elseif($optTHROOLD_DocumentReceived == "2" ) $status = 4;
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], $mv_UserID, $status,1);
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], "cust0002", $status );
		echo "<meta http-equiv='refresh' content='0; url=detail-of-release-other-legal-documents.php?id=$txtTHROOLD_ID'>";
	}else{
		$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
	}
}

if(isset($_POST['approval'])) {
	$A_TransactionCode=$_POST['txtA_TransactionCode'];
	$A_ApproverID=$mv_UserID;
	$A_Status=$_POST['optTHROOLD_Status'];
	$THROOLD_Reason=str_replace("<br>", "\n",$_POST['txtTHROOLD_Reason']);

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
			$query = "UPDATE TH_ReleaseOfOtherLegalDocuments
						SET THROOLD_Status='accept', THROOLD_Update_UserID='$A_ApproverID', THROOLD_Update_Time=sysdate()
						WHERE THROOLD_ReleaseCode='$A_TransactionCode'
						AND THROOLD_Delete_Time IS NULL";
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
						WHERE DocumentGroup_ID ='$_POST[txtDOL_GroupDocID]'";
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
				$txtDOL_ID=$_POST['txtDOL_ID'];
				$txtTDROOLD_TDLOOLD_ID=$_POST['txtTDROOLD_TDLOOLD_ID'];
				$jumlah=count($txtDOL_ID);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Pengeluaran Dokumen
					$CT_Code="$newnum/DOUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST['optTHLOOLD_LoanCategoryID']) {
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

					$query1 = "UPDATE M_DocumentsOtherLegal
								SET DOL_Status='$docStatus', DOL_Update_UserID='$A_ApproverID', DOL_Update_Time=sysdate()
								WHERE DOL_ID='$txtDOL_ID[$i]'";

					$sql= "INSERT INTO M_CodeTransaction
								VALUES (NULL,'$CT_Code','$nnum','DOUT','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$mv_UserID',sysdate(),
										'$mv_UserID',sysdate(),NULL,NULL)";

					$sql1 = "UPDATE TD_ReleaseOfOtherLegalDocuments
								SET TDROOLD_Code='$CT_Code',TDROOLD_ReturnCode='$code',
									TDROOLD_Update_Time=sysdate(),TDROOLD_Update_UserID='$mv_UserID'
								WHERE TDROOLD_THROOLD_ID='$_POST[txtTHROOLD_ID]'
								AND TDROOLD_TDLOOLD_ID='$txtTDROOLD_TDLOOLD_ID[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOOLD_UserID'], 3);
				mail_notif_release_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
		}
	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_ReleaseOfOtherLegalDocuments
					SET THROOLD_Status='reject', THROOLD_Reason='$THROOLD_Reason',
						THROOLD_Update_Time=sysdate(), THROOLD_Update_UserID='$A_ApproverID'
					WHERE THROOLD_ReleaseCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";
		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
			$txtDOL_ID=$_POST['txtDOL_ID'];
			$jumlah=count($txtDOL_ID);

			for ($i=0;$i<$jumlah;$i++) {
				$query = "UPDATE M_DocumentsOtherLegal
						  SET DOL_Status='1', DOL_Update_UserID='$A_ApproverID', DOL_Update_Time=sysdate()
						  WHERE DOL_ID='$txtDOL_ID[$i]'";
				$mysqli->query($query);
			}
			mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOOLD_UserID'], 4 );
			mail_notif_release_doc($A_TransactionCode, $_POST['txtDOL_RegUserID'], 4 );
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
