<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 14 Sep 2018																						=
= Update Terakhir	: -																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Pengeluaran Dokumen Kepemilikan Aset</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldocao.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
//LoV UTK DAFTAR PERMINTAAN DOKUMEN
function showList() {
	var docGrup="4";
	sList = window.open("popupLoan.php?gID="+docGrup+"", "Daftar_Permintaan_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
}
function remLink() {
  if (window.sList && window.sList.open && !window.sList.closed)
	window.sList.opener = null;
}

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;
	checkDocCode = 0;

	var txtTHROAOD_THLOAOD_Code = document.getElementById('txtTHROAOD_THLOAOD_Code').value;
	var txtTHROAOD_UserID = document.getElementById('txtTHROAOD_UserID').value;

		if (txtTHROAOD_THLOAOD_Code.replace(" ", "") == "") {
			alert("Kode Permintaan Dokumen Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 			$query = "SELECT *
					  FROM TH_LoanOfAssetOwnershipDocument thloaod,TD_LoanOfAssetOwnershipDocument tdloaod
					  WHERE thloaod.THLOAOD_Delete_Time IS NULL
					  AND thloaod.THLOAOD_Status='accept'
					  AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
					  AND tdloaod.TDLOAOD_Response='0'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) {
				$THLOAOD_LoanCode = $data['THLOAOD_LoanCode'];

				$a = "if (txtTHROAOD_THLOAOD_Code == '$THLOAOD_LoanCode') {";
				$a .= "checkDocCode = 1; ";
				$a .= "}";
			echo $a;
		 	}
			?>
			if (checkDocCode == 0) {
				alert("Kode Permintaan Dokumen SALAH!");
				returnValue = false;
			}
		}

	return returnValue;
}
// VALIDASI INPUT BAGIAN DETAIL UNTUK JENIS PERMINTAAN : PEMINJAMAN DOKUMEN
function validateInputDetail(elem) {
	var returnValue;
	returnValue = false;
	var notcheck = true;
	var TDLOAOD_ID = document.getElementsByName('TDLOAOD_ID[]');
	var DAO_DocCode = document.getElementsByName('DAO_DocCode[]');
	var txtTDROAOD_LeadTime = document.getElementsByName('txtTDROAOD_LeadTime[]');

	for (var i = 0; i < TDLOAOD_ID.length; i++){
		if (TDLOAOD_ID[i].checked) {
			if (txtTDROAOD_LeadTime[i].value.replace(/^\s+|\s+$/g,'') == "") {
				returnValue = false;
				notcheck = false;
				alert("Tanggal Pengembalian Untuk Dokumen "+DAO_DocCode[i].value+" Belum Ditentukan!");
			}
			else {
				returnValue = true;
				notcheck = false;
			}
		}
		else {
			notcheck = true;
		}
	}

	if (notcheck) {
		alert ("Belum Ada Dokumen Yang Dipilih!");
		returnValue = false;
	}
	return returnValue;
}
// VALIDASI INPUT BAGIAN DETAIL UNTUK JENIS PERMINTAAN : PERMINTAAN DOKUMEN
function validateInputDetailx(elem) {
	var returnValue;
	returnValue = false;
	var notcheck = true;
	var TDLOAOD_ID = document.getElementsByName('TDLOAOD_ID[]');
	var DAO_DocCode = document.getElementsByName('DAO_DocCode[]');
	var txtTDROAOD_LeadTime = document.getElementsByName('txtTDROAOD_LeadTime[]');

	for (var i = 0; i < TDLOAOD_ID.length; i++){
		if (TDLOAOD_ID[i].checked) {
			returnValue = true;
			notcheck = false;
		}
		else {
			notcheck = true;
		}
	}

	if (notcheck) {
		alert ("Belum Ada Dokumen Yang Dipilih!");
		returnValue = false;
	}
	return returnValue;
}
</script>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	//Menambah Header / Dokumen Baru
	if($act=='add') {
		$ActionContent ="
		<form name='addRelDoc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengeluaran Dokumen Kepemilikan Aset</th>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2
				  FROM M_User u
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  WHERE u.User_ID='$mv_UserID'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input id='txtTHROAOD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input id='txtTHROAOD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input id='txtTHROAOD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input id='txtTHROAOD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";

		if($field['User_SPV1']||$field['User_SPV2']){
			$ActionContent .="
			<tr>
				<td>Kode Permintaan Dokumen</td>
				<td>
					<input id='txtTHROAOD_THLOAOD_Code' name='txtTHROAOD_THLOAOD_Code' size='25' type='text' value='' readonly='readonly' onClick='javascript:showList();'/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name='txtTHROAOD_Information' id='txtTHROAOD_Information' cols='50' rows='2'></textarea></td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='addheader' type='submit' value='Simpan' class='button' onclick='return validateInputHeader(this);'/>
					<input name='cancel' type='submit' value='Batal' class='button'/>
				</th>
			</tr>";
		}else{
			if(!$_POST['cancel']){
				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.');</script>";
			}
			$ActionContent .="
			<tr>
				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
					Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.<br>
					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
				</td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='cancel' type='submit' value='OK' class='button'/>
				</th>
			</tr>";
		}
		$ActionContent .="
		</table>
		</form>";
	}

	//Menambah Detail Dokumen
	elseif($act=='adddetail')	{
		$code=$_GET["id"];

		$query = "SELECT releaseHeader.THROAOD_Information,
						 releaseHeader.THROAOD_ReleaseDate,
						 releaseHeader.THROAOD_ID,
						 releaseHeader.THROAOD_ReleaseCode,
						 u.User_FullName as FullName,
						 ddp.DDP_DeptID as DeptID,
						 ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID,
						 dp.Department_Name as DeptName,
						 d.Division_Name as DivName,
						 p.Position_Name as PosName,
						 u.User_SPV1,
						 u.User_SPV2,
						 c.Company_Name,
						 dg.DocumentGroup_Name,
						 thloaod.THLOAOD_DocumentType tipe_dokumen,
						 thloaod.THLOAOD_LoanCategoryID kategori_permintaan
				  FROM TH_ReleaseOfAssetOwnershipDocument releaseHeader
				  LEFT JOIN M_User u
					ON u.User_ID=releaseHeader.THROAOD_UserID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN TH_LoanOfAssetOwnershipDocument thloaod
					ON releaseHeader.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
					AND thloaod.THLOAOD_Delete_Time IS NULL
				  LEFT JOIN M_Company c
					ON thloaod.THLOAOD_CompanyID=c.Company_ID
				  LEFT JOIN M_DocumentGroup dg
					ON dg.DocumentGroup_ID='4'
				  WHERE releaseHeader.THROAOD_ReleaseCode='$code'
				  AND releaseHeader.THROAOD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$fregdate=date("j M Y", strtotime($field['THROAOD_ReleaseDate']));
		// $atasan=($field['User_SPV2'])?$field['User_SPV2']:$field['User_SPV1'];

		if ($field['tipe_dokumen'] == "ORIGINAL") { $jenis = '27'; }
        else if ($field['tipe_dokumen'] == "HARDCOPY") { $jenis = '28'; }
        else if ($field['tipe_dokumen'] == "SOFTCOPY") { $jenis = '29'; }
        else;
		// $jenis = "22"; //Semua Dokumen

		$queryApprover = "
			SELECT ma.Approver_UserID, rads.RADS_StepID, rads.RADS_RA_ID, ra.RA_Name
			FROM M_Role_ApproverDocStepStatus rads
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			LEFT JOIN M_Approver ma
				ON ra.RA_ID = ma.Approver_RoleID
			WHERE rads.RADS_DocID = '$jenis'
				AND rads.RADS_ProsesID = '3'
				AND ma.Approver_Delete_Time IS NULL
				AND ma.Approver_UserID != '0'
				ORDER BY rads.RADS_StepID
		";
		$sqlApprover=mysql_query($queryApprover);
		while($d = mysql_fetch_array($sqlApprover)){
			$approvers[] = $d['Approver_UserID'];  //Approval Untuk ke Custodian
		}

		$ActionContent ="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengeluaran Dokumen Kepemilikan Aset</th>
		<tr>
			<td width='30%'>Kode Pengeluaran</td>
			<td width='70%'>
				<input name='txtTDROAOD_THROAOD_ID' type='hidden' value='$field[THROAOD_ID]'/>
				<input type='hidden' name='txtTDROAOD_THROAOD_ReleaseCode' value='$field[THROAOD_ReleaseCode]'/>
				$field[THROAOD_ReleaseCode]
			</td>
		</tr>
		<tr>
			<td>Tanggal Pendaftaran</td>
			<td>$fregdate</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>$field[FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field[DivName]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field[DeptName]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field[PosName]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>$field[Company_Name]</td>
		</tr>
		<tr>
			<td>Grup</td>
			<td>$field[DocumentGroup_Name]</td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHROAOD_Information' id='txtTHROAOD_Information' cols='50' rows='2'>$field[THROAOD_Information]</textarea>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		$query="SELECT tdloaod.TDLOAOD_ID, tdloaod.TDLOAOD_Code, dao.DAO_DocCode,
		 			   -- dao.DAO_NoDoc,
					   dao.DAO_Employee_NIK,
					   -- m_e.Employee_FullName nama_pemilik,
					   m_mk.MK_Name merk_kendaraan, dao.DAO_NoPolisi,
					   dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate,
					   thloaod.THLOAOD_LoanCategoryID
				FROM TD_LoanOfAssetOwnershipDocument tdloaod, TH_LoanOfAssetOwnershipDocument thloaod, TH_ReleaseOfAssetOwnershipDocument throaod,
					 M_DocumentAssetOwnership dao,
					 -- db_master.M_Employee m_e,
					 db_master.M_MerkKendaraan m_mk
				WHERE throaod.THROAOD_ReleaseCode='$code'
				AND throaod.THROAOD_Delete_Time IS NULL
				AND throaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
				AND thloaod.THLOAOD_ID=tdloaod.TDLOAOD_THLOAOD_ID
				AND tdloaod.TDLOAOD_Response='0'
				AND tdloaod.TDLOAOD_Delete_Time IS NULL
				AND dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
				-- AND dao.DAO_Employee_NIK=m_e.Employee_NIK
				AND dao.DAO_MK_ID=m_mk.MK_ID";
		$sql = mysql_query($query);
		$i=0;

		$ActionContent .="
		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th></th>
			<th>Kode Dokumen</th>
			<th>Nama Pemilik</th>
			<th>Merk Kendaraan</th>
			<th>No. Polisi</th>
			<th>Masa STNK</th>
			<th>Keterangan</th>
			<th>Waktu Pengembalian</th>
		</tr>";

		while ($arr=mysql_fetch_array($sql)) {
			$stnk_sdate=date("j M Y", strtotime($arr['DAO_STNK_StartDate']));
			$stnk_exdate=date("j M Y", strtotime($arr['DAO_STNK_ExpiredDate']));
			// if($arr['THLOAOD_LoanCategoryID'] == "1"){
			// 	$LeadTime = date('m/d/Y',strtotime("+7 day", strtotime($field['THROAOD_ReleaseDate'])));
			// }elseif($arr['THLOAOD_LoanCategoryID'] == "2"){
			// 	if($arr['THLOAOD_DocumentType'] == "ORIGINAL" or $arr['THLOAOD_DocumentType'] == "HARCOPY"){
			// 		$LeadTime = date('m/d/Y',strtotime("+7 day", strtotime($field['THROAOD_ReleaseDate'])));
			// 	}
			// }
			if(strpos($arr['DAO_Employee_NIK'], 'CO@') !== false){
				$get_company_code = explode('CO@', $arr['DAO_Employee_NIK']);
				$company_code = $get_company_code[1];
				$query7="SELECT Company_Name AS nama_pemilik
					FROM M_Company
					WHERE Company_code='$company_code'";
			}else{
				$query7="SELECT Employee_FullName AS nama_pemilik
					FROM db_master.M_Employee
					WHERE Employee_NIK='$arr[DAO_Employee_NIK]'";
			}
			$sql7 = mysql_query($query7);
			$nama_pemilik = "-";
			if(mysql_num_rows($sql7) > 0){
				$data7 = mysql_fetch_array($sql7);
				$nama_pemilik = $data7['nama_pemilik'];
			}

			$LeadTime=($arr['THLOAOD_LoanCategoryID']=="1")?date('m/d/Y',strtotime("+7 day", strtotime($field['THROAOD_ReleaseDate']))):"";

			$ActionContent .="
			<tr>
				<td class='center'>
					<input id='TDLOAOD_ID[]' name='TDLOAOD_ID[]' type='checkbox' value='$arr[TDLOAOD_ID]'>
				</td>
				<td class='center'><input id='DAO_DocCode[]' name='DAO_DocCode[]' type='hidden' value='$arr[DAO_DocCode]'>$arr[DAO_DocCode]</td>
				<td class='center'>$nama_pemilik</td>
				<td class='center'>$arr[merk_kendaraan]</td>
				<td class='center'>$arr[DAO_NoPolisi]</td>
				<td class='center'>$stnk_sdate s/d $stnk_exdate</td>
				<td class='center'>
					<textarea id='txtTDROAOD_Information' name='txtTDROAOD_Information[]'></textarea>
				</td>
				<td class='center'>
					<input id='txtTDROAOD_LeadTime[$i]' name='txtTDROAOD_LeadTime[]' type='text' value='$LeadTime' size='10'  readonly='readonly' class='readonly'>
				</td>
			</tr>";
			$i++;
			$loanType=$arr['THLOAOD_LoanCategoryID'];
		}
		$ActionContent .="
		</table>

		<table width='100%'>
		<tr>
			<td>";
			foreach($approvers as $approver){
				$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$approver' readonly='true' class='readonly'/>";
			}
			$ActionContent .="</td>
		</tr>
		<tr>";

		if ($loanType=="1") {
			$ActionContent .="
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>";
		}else {
			$ActionContent .="
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetailx(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>";
		}

		$ActionContent .="
		</tr>
		</table>

		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}
	//Kirim Ulang Email Persetujuan
	elseif($act=='resend'){
		mail_release_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=release-of-asset-ownership-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT throaod.THROAOD_ID,
				 throaod.THROAOD_ReleaseCode,
				 throaod.THROAOD_ReleaseDate,
				 u.User_FullName,
 		         drs.DRS_Description,
				 throaod.THROAOD_Status
		  FROM TH_ReleaseOfAssetOwnershipDocument throaod
		  LEFT JOIN M_User u
			ON throaod.THROAOD_UserID=u.User_ID
		  LEFT JOIN M_DocumentRegistrationStatus drs
			ON throaod.THROAOD_Status=drs.DRS_Name
		  WHERE throaod.THROAOD_Delete_Time is NULL
		  AND u.User_ID='$mv_UserID'
		  ORDER BY throaod.THROAOD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='30%'>Kode Pengeluaran</th>
	<th width='25%'>Tanggal Pengeluaran</th>
	<th width='20%'>Dikeluarkan Oleh</th>
	<th width='20%'>Status</th>
	<th width='5%'></th>
</tr>";

if ($num==NULL) {
	$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$fregdate=date("j M Y", strtotime($field['THROAOD_ReleaseDate']));
		$resend=($field['THROAOD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[THROAOD_ReleaseCode]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-release-asset-ownership-document.php?id=$field[THROAOD_ID]' class='underline'>$field[THROAOD_ReleaseCode]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[User_FullName]</td>
			<td class='center'>$field[DRS_Description]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT throaod.THROAOD_ID, throaod.THROAOD_ReleaseCode, throaod.THROAOD_ReleaseDate, u.User_FullName,
 		          throaod.THROAOD_Status
		   FROM TH_ReleaseOfAssetOwnershipDocument throaod, M_User u
		   WHERE throaod.THROAOD_Delete_Time is NULL
		   AND throaod.THROAOD_UserID=u.User_ID
		   AND u.User_ID='$mv_UserID'
		   ORDER BY throaod.THROAOD_ID DESC";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1)
	$Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
    	if (($showPage == 1) && ($p != 2))
			$Pager.="...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			$Pager.="...";
        if ($p == $noPage)
			$Pager.="<b><u>$p</b></u> ";
        else
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";

		$showPage = $p;
	}
}

if ($noPage < $jumPage)
	$Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=release-of-asset-ownership-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_ReleaseOfAssetOwnershipDocument throaod
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       throaod.THROAOD_Delete_UserID='$mv_UserID',throaod.THROAOD_Delete_Time=sysdate(),
			       throaod.THROAOD_Update_UserID='$mv_UserID',throaod.THROAOD_Update_Time=sysdate()
			   WHERE throaod.THROAOD_ID='$_POST[txtTDROAOD_THROAOD_ID]'
			   AND throaod.THROAOD_ReleaseCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=release-of-asset-ownership-document.php'>";
	}
}

elseif(isset($_POST[addheader])) {
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
	$query = "SELECT c.Company_Code
			  FROM TH_LoanOfAssetOwnershipDocument thloaod, M_Company c
			  WHERE thloaod.THLOAOD_LoanCode='$_POST[txtTHROAOD_THLOAOD_Code]'
			  AND thloaod.THLOAOD_CompanyID=c.Company_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code="KEA";

	// Cari No Pengeluaran Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='OUT'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Pengeluaran Dokumen
	$CT_Code="$newnum/OUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode Pengeluaran dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','OUT','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$info=str_replace("<br>", "\n",$_POST['txtTHROAOD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_ReleaseOfAssetOwnershipDocument
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID','$_POST[txtTHROAOD_THLOAOD_Code]',
					    '$info','0',NULL,NULL,NULL,NULL,NULL,NULL,'$mv_UserID', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=release-of-asset-ownership-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST['adddetail'])) {
	$TDLOAOD_ID=$_POST['TDLOAOD_ID'];
	$txtTHROAOD_Information=str_replace("<br>", "\n",$_POST['txtTHROAOD_Information']);
	$txtTDROAOD_Information=str_replace("<br>", "\n",$_POST['txtTDROAOD_Information']);
	$txtTDROAOD_LeadTime=$_POST['txtTDROAOD_LeadTime'];
	$sum=count($TDLOAOD_ID);

	for ($i=0 ; $i<$sum ; $i++) {
		$TDROAOD_LeadTime=date('Y-m-d H:i:s', strtotime($txtTDROAOD_LeadTime[$i]));
		if ($TDROAOD_LeadTime=="1970-01-01 08:00:00"){
			$TDROAOD_LeadTime="";
		}
		$sql1= "INSERT INTO TD_ReleaseOfAssetOwnershipDocument
				VALUES (NULL,NULL,'$_POST[txtTDROAOD_THROAOD_ID]', '$TDLOAOD_ID[$i]','$txtTDROAOD_Information[$i]',
						'$TDROAOD_LeadTime',NULL,'$mv_UserID', sysdate(),'$mv_UserID',
						sysdate(),NULL,NULL)";
		$sql2= "UPDATE TD_LoanOfAssetOwnershipDocument
				SET TDLOAOD_Response='1', TDLOAOD_Update_UserID='$mv_UserID',TDLOAOD_Update_Time=sysdate()
				WHERE TDLOAOD_ID='$TDLOAOD_ID[$i]'";
		$mysqli->query($sql1);
		$mysqli->query($sql2);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);

	for($i=0;$i<$jumlah;$i++){
		$step=$i+1;
		$sql2= "INSERT INTO M_Approval
				VALUES (NULL,'$_POST[txtTDROAOD_THROAOD_ReleaseCode]', '$txtA_ApproverID[$i]', '$step',
				        '1',NULL,'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
		$mysqli->query($sql2);
		$sa_query="SELECT *
				   FROM M_Approval
				   WHERE A_TransactionCode='$_POST[txtTDROAOD_THROAOD_ReleaseCode]'
				   AND A_ApproverID='$txtA_ApproverID[$i]'
				   AND A_Delete_Time IS NULL";
		$sa_sql=mysql_query($sa_query);
		$sa_arr=mysql_fetch_array($sa_sql);
		$ARC_AID=$sa_arr['A_ID'];
		$str=rand(1,100);
		$RandomCode=crypt('T4pagri'.$str);
		$iSQL="INSERT INTO L_ApprovalRandomCode
			   VALUES ('$ARC_AID','$RandomCode')";
		$mysqli->query($iSQL);
	}

	$sql3= "UPDATE M_Approval
			SET A_Status='2', A_Update_UserID='$mv_UserID',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDROAOD_THROAOD_ReleaseCode]'
			AND A_Step='1'";

	$sql4= "UPDATE TH_ReleaseOfAssetOwnershipDocument
			SET THROAOD_Status='waiting', THROAOD_Information='$txtTHROAOD_Information',
			THROAOD_Update_UserID='$mv_UserID',THROAOD_Update_Time=sysdate()
			WHERE THROAOD_ReleaseCode='$_POST[txtTDROAOD_THROAOD_ReleaseCode]'
			AND THROAOD_Delete_Time IS NULL";

	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		mail_release_doc($_POST['txtTDROAOD_THROAOD_ReleaseCode']);
		echo "<meta http-equiv='refresh' content='0; url=release-of-asset-ownership-document.php'>";
	}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
// Menampilkan DatePicker
function getLeadTime(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

  	for (i=0;i<=rows;i++){
		 cal.manageFields("txtTDROAOD_LeadTime["+i+"]", "txtTDROAOD_LeadTime["+i+"]", "%m/%d/%Y");
	}
}
</script>
