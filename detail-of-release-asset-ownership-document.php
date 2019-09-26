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
<title>Custodian System | Detail Pengeluaran Dokumen Kepemilikan Aset</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldocao.php");
?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optTHROAOD_Status = document.getElementById('optTHROAOD_Status').selectedIndex;
	var txtTHROAOD_Reason = document.getElementById('txtTHROAOD_Reason').value;

		if(optTHROAOD_Status == 0) {
			alert("Persetujuan Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optTHROAOD_Status == 2) {
			if (txtTHROAOD_Reason.replace(" ", "") == "") {
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
			  	 FROM TH_ReleaseOfAssetOwnershipDocument throaod, M_Approval dra
				 WHERE throaod.THROAOD_Delete_Time is NULL
				 AND dra.A_ApproverID='$mv_UserID'
				 AND dra.A_Status='2'
				 AND dra.A_TransactionCode=throaod.THROAOD_ReleaseCode
				 AND throaod.THROAOD_ID='$DocID'";
	$cApp_sql=mysql_query($cApp_query);
	$approver=mysql_num_rows($cApp_sql);

	if(($act=='approve')&&($approver=="1")) {
$query = "SELECT DISTINCT throaod.THROAOD_ID, throaod.THROAOD_ReleaseCode, throaod.THROAOD_ReleaseDate, u.User_ID,
		          u.User_FullName, c.Company_Name, throaod.THROAOD_Status, throaod.THROAOD_Information, thloaod.THLOAOD_UserID,
				  dg.DocumentGroup_Name, dg.DocumentGroup_ID, throaod.THROAOD_Reason,c.Company_ID,thloaod.THLOAOD_LoanCategoryID,
				  throaod.THROAOD_DocumentReceived, throaod.THROAOD_ReasonOfDocumentCancel,
				  thloaod.THLOAOD_DocumentType tipe_dokumen, thloaod.THLOAOD_SoftcopyReciever email_softcopy
		  	FROM TH_ReleaseOfAssetOwnershipDocument throaod, M_User u, M_Company c, M_Approval dra,
				 M_DocumentGroup dg, TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod
			WHERE throaod.THROAOD_Delete_Time is NULL
			AND throaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
			AND thloaod.THLOAOD_CompanyID=c.Company_ID
			AND throaod.THROAOD_UserID=u.User_ID
			AND dra.A_ApproverID='$mv_UserID'
			AND dra.A_TransactionCode=throaod.THROAOD_ReleaseCode
			AND throaod.THROAOD_ID='$DocID'
			AND dg.DocumentGroup_ID='4'";
	}
	else {
$query = "SELECT DISTINCT throaod.THROAOD_ID, throaod.THROAOD_ReleaseCode, throaod.THROAOD_ReleaseDate, u.User_ID,
		          u.User_FullName, c.Company_Name, throaod.THROAOD_Status, throaod.THROAOD_Information, thloaod.THLOAOD_UserID,
				  dg.DocumentGroup_Name, dg.DocumentGroup_ID, throaod.THROAOD_Reason,c.Company_ID,thloaod.THLOAOD_LoanCategoryID,
				  throaod.THROAOD_DocumentReceived, throaod.THROAOD_ReasonOfDocumentCancel,
				  thloaod.THLOAOD_DocumentType tipe_dokumen, thloaod.THLOAOD_SoftcopyReciever email_softcopy
		  	FROM TH_ReleaseOfAssetOwnershipDocument throaod, M_User u, M_Company c, M_Approval dra,
				 M_DocumentGroup dg, TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod
			WHERE throaod.THROAOD_Delete_Time is NULL
			AND throaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
			AND thloaod.THLOAOD_CompanyID=c.Company_ID
			AND throaod.THROAOD_UserID=u.User_ID
			AND dra.A_TransactionCode=throaod.THROAOD_ReleaseCode
			AND throaod.THROAOD_ID='$DocID'
			AND (thloaod.THLOAOD_UserID='$mv_UserID' OR u.User_ID='$mv_UserID')
			AND dg.DocumentGroup_ID='4'";
	}

$sql = mysql_query($query);
$arr = mysql_fetch_array($sql);

$showFormKonfirmasiPenerimaanDokumen = 0;
if( $arr['THROAOD_DocumentReceived'] == NULL && $arr['THROAOD_Status']=="accept" && ($arr['THLOAOD_UserID'] == $mv_UserID)){ //Arief F - 21092018
	//Jika user adalah pengaju (untuk mengonfirmasi dokumen sudah diteriim atau tidak)
	$showFormKonfirmasiPenerimaanDokumen = 1;
} //Arief F - 21092018

$regdate=strtotime($arr['THROAOD_ReleaseDate']);
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
	<input name='optTHLOAOD_LoanCategoryID' type='hidden' value='$arr[THLOAOD_LoanCategoryID]'>
	<table width='100%' id='mytable' class='stripeMe'>";
	if(($act=='approve')&&($approver=="1"))
		$MainContent .="<th colspan=3>Persetujuan Pengeluaran Dokumen Kepemilikan Aset</th>";
	else
		$MainContent .="<th colspan=3>Pengeluaran Dokumen Kepemilikan Aset</th>";

$MainContent .="
	<tr>
		<td width='30%'>Kode Pengeluaran</td>";
if(($arr[THROAOD_Status]=="accept") && ($custodian==1) ){
$MainContent .="

		<td width='67%'>
			<input name='txtTHROAOD_ID' type='hidden' value='$arr[THROAOD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROAOD_ReleaseCode]'/>
			$arr[THROAOD_ReleaseCode]
		</td>
		<td width='3%'>
			<a href='print-release-of-asset-ownership-document.php?id=$arr[THROAOD_ReleaseCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>";
}
else {
$MainContent .="

		<td width='70%' colspan='2'>
			<input name='txtTHROAOD_ID' type='hidden' value='$arr[THROAOD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROAOD_ReleaseCode]'/>
			$arr[THROAOD_ReleaseCode]
		</td>";
}
$MainContent .="
	</tr>
	<tr>
		<td>Tanggal Pengeluaran</td>
		<td colspan='2'><input name='txtDAO_RegTime' type='hidden' value='$arr[THROAOD_ReleaseDate]'>$fregdate</td>
	</tr>
	<tr>
		<td>Dikeluarkan Oleh</td>
		<td colspan='2'><input name='txtDAO_RegUserID' type='hidden' value='$arr[User_ID]'>
		<input name='txtTHLOAOD_UserID' type='hidden' value='$arr[THLOAOD_UserID]'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td>Perusahaan</td>
		<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td>Grup Dokumen</td>
		<td colspan='2'><input name='txtDAO_GroupDocID' type='hidden' value='$arr[DocumentGroup_ID]'>$arr[DocumentGroup_Name]</td>
	</tr>
	<tr>
		<td>Keterangan</td>
		<td colspan='2'><pre>$arr[THROAOD_Information]</pre></td>
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
			<select name='optTHROAOD_Status' id='optTHROAOD_Status'>
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
			<textarea name='txtTHROAOD_Reason' id='txtTHROAOD_Reason' cols='50' rows='2'>$arr[THROAOD_Reason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>
";
	}else {
$MainContent .="
	<tr>
		<td>Status Dokumen</td>
";
	if($arr[THROAOD_Status]=="waiting") {
		$query1="SELECT u.User_FullName
					FROM M_Approval dra, M_User u
					WHERE dra.A_TransactionCode='$arr[THROAOD_ReleaseCode]'
					AND dra.A_Status='2'
					AND dra.A_ApproverID=u.User_ID";
		$sql1 = mysql_query($query1);
		$arr1=mysql_fetch_array($sql1);
$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr1[User_FullName]</td></tr>";
	}
	else if($arr[THROAOD_Status]=="accept") {
$MainContent .="
		<td colspan='2'>Disetujui</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THROAOD_Reason]</pre></td>
	</tr>";
	//Tampil Form Penerimaan Dokumen
		if( $showFormKonfirmasiPenerimaanDokumen == 1 ){ //Arief F - 21092018
	$MainContent .="
		<tr>
			<td>Dokumen sudah diterima</td>
			<td colspan='2'>
				<select name='optTHROAOD_DocumentReceived' id='optTHROAOD_DocumentReceived'>
					<option value='0'>--- Menungu Konfirmasi ---</option>
					<option value='1'>Sudah</option>
					<option value='2'>Batal</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Ket. Batal Terima Dokumen</td>
			<td colspan='2'>
				<textarea name='txtTHROAOD_ReasonOfDocumentCancel' id='txtTHROAOD_ReasonOfDocumentCancel' cols='50' rows='2'>$arr[THROAOD_ReasonOfDocumentCancel]</textarea>
				<br>*Wajib Diisi Apabila Dokumen Batal Diterima.
			</td>
		</tr>";
		} //Arief F - 21092018

		if($arr['THROAOD_DocumentReceived'] == 1){ //Arief F - 21092018
			$MainContent .="
			<tr>
				<td>Dokumen sudah diterima</td>
				<td colspan='2'>
					Sudah
				</td>
			</tr>";
		}elseif($arr['THROAOD_DocumentReceived'] == 2){ //Arief F - 21092018
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
					$arr[THROAOD_ReasonOfDocumentCancel]
				</td>
			</tr>";
		} //Arief F - 21092018
	}
	else if($arr[THROAOD_Status]=="reject") {
$MainContent .="
		<td colspan='2'>Ditolak</td>
	</tr>
	<tr>
		<td>Alasan</td>
		<td colspan='2'><pre>$arr[THROAOD_Reason]</pre></td>
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
        <th>Nama Pemilik</th>
        <th>Merk Kendaraan</th>
        <th>No. Polisi</th>
        <th>Masa STNK</th>
        <th>Keterangan</th>
        <th>Waktu Pengembalian</th>
    </tr>";

	$query = "SELECT tdroaod.TDROAOD_ID, tdloaod.TDLOAOD_ID, tdloaod.TDLOAOD_Code,
				     dao.DAO_ID,tdroaod.TDROAOD_Information, dao.DAO_DocCode, tdroaod.TDROAOD_LeadTime,
                     dao.DAO_ID,
					 dao.DAO_Employee_NIK,
					 CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
					   THEN
						 (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
					   ELSE
						 (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
					 END nama_pemilik,
                     m_mk.MK_Name merk_kendaraan, dao.DAO_NoPolisi,
                     dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate
				FROM TD_ReleaseOfAssetOwnershipDocument tdroaod
				INNER JOIN TD_LoanOfAssetOwnershipDocument tdloaod
					ON tdroaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
				INNER JOIN M_DocumentAssetOwnership dao
				 	ON tdloaod.TDLOAOD_DocCode=dao.DAO_DocCode
				LEFT JOIN db_master.M_MerkKendaraan m_mk
					ON dao.DAO_MK_ID=m_mk.MK_ID
				-- , db_master.M_Employee m_e
				WHERE tdroaod.TDROAOD_THROAOD_ID='$DocID'
				AND tdroaod.TDROAOD_Delete_Time IS NULL";
	$sql = mysql_query($query);
	$no=1;
	while ($arr = mysql_fetch_array($sql)) {
		if ( (strpos($arr['TDROAOD_LeadTime'], '0000-00-00') !== false ) || ( strpos($arr['TDROAOD_LeadTime'], '1970-01-01') !== false ) ){
			$fLeadTime="-";
		}
		else {
			$LeadTime=strtotime($arr['TDROAOD_LeadTime']);
			$fLeadTime=date("j M Y", $LeadTime);
		}

		$stnk_sdate=(strpos($arr['DAO_STNK_StartDate'], '0000-00-00') !== false || strpos($arr['DAO_STNK_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_STNK_StartDate']));
		$stnk_exdate=(strpos($arr['DAO_STNK_ExpiredDate'], '0000-00-00') !== false || strpos($arr['DAO_STNK_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_STNK_ExpiredDate']));

$MainContent .="
		<tr>
			<td class='center'>
				<input type='hidden' name='txtTDROAOD_ID[]' value='$arr[TDROAOD_ID]'/>
				<input name='txtDAO_ID[]' type='hidden' value='$arr[DAO_ID]'>$no
			</td>
			<td class='center'>
				<input name='txtTDROAOD_TDLOAOD_ID[]' type='hidden' value='$arr[TDLOAOD_ID]'>
				<input name='txtTDLOAOD_Code[]' type='hidden' value='$arr[TDLOAOD_Code]'>$arr[TDLOAOD_Code]</td>
			<td class='center'>$arr[DAO_DocCode]</td>
            <td class='center'>$arr[nama_pemilik]</td>
            <td class='center'>$arr[merk_kendaraan]</td>
            <td class='center'>$arr[DAO_NoPolisi]</td>
            <td class='center'>$stnk_sdate s/d $stnk_exdate</td>
			<td class='center'><pre>$arr[TDROAOD_Information]</pre></td>
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
	$txtTHROAOD_ID=$_POST['txtTHROAOD_ID'];
	$optTHROAOD_DocumentReceived=$_POST['optTHROAOD_DocumentReceived'];

	$query = "UPDATE TH_ReleaseOfAssetOwnershipDocument
				SET THROAOD_DocumentReceived='$optTHROAOD_DocumentReceived',
				THROAOD_ReasonOfDocumentCancel='$_POST[txtTHROAOD_ReasonOfDocumentCancel]',
				THROAOD_Update_UserID='$mv_UserID', THROAOD_Update_Time=sysdate()
				WHERE THROAOD_ID='$txtTHROAOD_ID'
				AND THROAOD_Delete_Time IS NULL";
	$sql = mysql_query($query);
	if($sql){
		if($optTHROAOD_DocumentReceived == "1" ) $status = 3;//Sudah Diterima
		elseif($optTHROAOD_DocumentReceived == "2" ) $status = 4;
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], $mv_UserID, $status,1);
		mail_notif_reception_release_doc($_POST['txtA_TransactionCode'], "cust0002", $status );
		echo "<meta http-equiv='refresh' content='0; url=detail-of-release-asset-ownership-document.php?id=$txtTHROAOD_ID'>";
	}else{
		$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
	}
}

if(isset($_POST[approval])) {
	$A_TransactionCode=$_POST['txtA_TransactionCode'];
	$A_ApproverID=$mv_UserID;
	$A_Status=$_POST['optTHROAOD_Status'];
	$THROAOD_Reason=str_replace("<br>", "\n",$_POST['txtTHROAOD_Reason']);

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
			$query = "UPDATE TH_ReleaseOfAssetOwnershipDocument
						SET THROAOD_Status='accept', THROAOD_Update_UserID='$A_ApproverID', THROAOD_Update_Time=sysdate()
						WHERE THROAOD_ReleaseCode='$A_TransactionCode'
						AND THROAOD_Delete_Time IS NULL";
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
						WHERE DocumentGroup_ID ='$_POST[txtDAO_GroupDocID]'";
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
				$txtDAO_ID=$_POST['txtDAO_ID'];
				$txtTDROAOD_TDLOAOD_ID=$_POST['txtTDROAOD_TDLOAOD_ID'];
				$jumlah=count($txtDAO_ID);

				for($i=0;$i<$jumlah;$i++){
					$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
					// Kode Pengeluaran Dokumen
					$CT_Code="$newnum/DOUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

					switch ($_POST['optTHLOAOD_LoanCategoryID']) {
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

					$query1 = "UPDATE M_DocumentAssetOwnership
								SET DAO_Status='$docStatus', DAO_Update_UserID='$A_ApproverID', DAO_Update_Time=sysdate()
								WHERE DAO_ID='$txtDAO_ID[$i]'";

					$sql= "INSERT INTO M_CodeTransaction
								VALUES (NULL,'$CT_Code','$nnum','DOUT','$Company_Code','$DocumentGroup_Code',
										'$rmonth','$regyear','$mv_UserID',sysdate(),
										'$mv_UserID',sysdate(),NULL,NULL)";

					$sql1 = "UPDATE TD_ReleaseOfAssetOwnershipDocument
								SET TDROAOD_Code='$CT_Code',TDROAOD_ReturnCode='$code',
									TDROAOD_Update_Time=sysdate(),TDROAOD_Update_UserID='$mv_UserID'
								WHERE TDROAOD_THROAOD_ID='$_POST[txtTHROAOD_ID]'
								AND TDROAOD_TDLOAOD_ID='$txtTDROAOD_TDLOAOD_ID[$i]'";

					$mysqli->query($query1);
					$mysqli->query($sql);
					$mysqli->query($sql1);
					$nnum=$nnum+1;
				}
				mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOAOD_UserID'], 3);
				mail_notif_release_doc($A_TransactionCode, "cust0002", 3 );

				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
		}
	// PROSES BILA "TOLAK"
	if ($A_Status=='4') {
		$query = "UPDATE TH_ReleaseOfAssetOwnershipDocument
					SET THROAOD_Status='reject', THROAOD_Reason='$THROAOD_Reason',
						THROAOD_Update_Time=sysdate(), THROAOD_Update_UserID='$A_ApproverID'
					WHERE THROAOD_ReleaseCode='$A_TransactionCode'";

		$query1 = "UPDATE M_Approval
					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
						A_Status='$A_Status'
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step>'$step'";
		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
			$txtDAO_ID=$_POST['txtDAO_ID'];
			$jumlah=count($txtDAO_ID);

			for ($i=0;$i<$jumlah;$i++) {
				$query = "UPDATE M_DocumentAssetOwnership
						  SET DAO_Status='1', DAO_Update_UserID='$A_ApproverID', DAO_Update_Time=sysdate()
						  WHERE DAO_ID='$txtDAO_ID[$i]'";
				$mysqli->query($query);
			}
			mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOAOD_UserID'], 4 );
			mail_notif_release_doc($A_TransactionCode, $_POST['txtDAO_RegUserID'], 4 );
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
