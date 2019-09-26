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
<title>Custodian System | Pengeluaran Dokumen Lainnya (Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldocol.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
//LoV UTK DAFTAR PERMINTAAN DOKUMEN
function showList() {
	var docGrup="5";
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

	var txtTHROOLD_THLOOLD_Code = document.getElementById('txtTHROOLD_THLOOLD_Code').value;
	var txtTHROOLD_UserID = document.getElementById('txtTHROOLD_UserID').value;

		if (txtTHROOLD_THLOOLD_Code.replace(" ", "") == "") {
			alert("Kode Permintaan Dokumen Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 			$query = "SELECT *
					  FROM TH_LoanOfOtherLegalDocuments thloold,TD_LoanOfOtherLegalDocuments tdloold
					  WHERE thloold.THLOOLD_Delete_Time IS NULL
					  AND thloold.THLOOLD_Status='accept'
					  AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
					  AND tdloold.TDLOOLD_Response='0'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) {
				$THLOOLD_LoanCode = $data['THLOOLD_LoanCode'];

				$a = "if (txtTHROOLD_THLOOLD_Code == '$THLOOLD_LoanCode') {";
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
	var TDLOOLD_ID = document.getElementsByName('TDLOOLD_ID[]');
	var DOL_DocCode = document.getElementsByName('DOL_DocCode[]');
	var txtTDROOLD_LeadTime = document.getElementsByName('txtTDROOLD_LeadTime[]');

	for (var i = 0; i < TDLOOLD_ID.length; i++){
		if (TDLOOLD_ID[i].checked) {
			if (txtTDROOLD_LeadTime[i].value.replace(/^\s+|\s+$/g,'') == "") {
				returnValue = false;
				notcheck = false;
				alert("Tanggal Pengembalian Untuk Dokumen "+DOL_DocCode[i].value+" Belum Ditentukan!");
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
	var TDLOOLD_ID = document.getElementsByName('TDLOOLD_ID[]');
	var DOL_DocCode = document.getElementsByName('DOL_DocCode[]');
	var txtTDROOLD_LeadTime = document.getElementsByName('txtTDROOLD_LeadTime[]');

	for (var i = 0; i < TDLOOLD_ID.length; i++){
		if (TDLOOLD_ID[i].checked) {
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
		<th colspan=3>Pengeluaran Dokumen Lainnya (Legal)</th>";

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
				<input id='txtTHROOLD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input id='txtTHROOLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input id='txtTHROOLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input id='txtTHROOLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";

		if(!empty($field['User_SPV1']) || !empty($field['User_SPV2'])){
			$ActionContent .="
			<tr>
				<td>Kode Permintaan Dokumen</td>
				<td>
					<input id='txtTHROOLD_THLOOLD_Code' name='txtTHROOLD_THLOOLD_Code' size='25' type='text' value='' readonly='readonly' onClick='javascript:showList();'/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name='txtTHROOLD_Information' id='txtTHROOLD_Information' cols='50' rows='2'></textarea></td>
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

		$query = "SELECT releaseHeader.THROOLD_Information,
						 releaseHeader.THROOLD_ReleaseDate,
						 releaseHeader.THROOLD_ID,
						 releaseHeader.THROOLD_ReleaseCode,
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
						 thloold.THLOOLD_DocumentType tipe_dokumen,
						 thloold.THLOOLD_LoanCategoryID kategori_permintaan
				  FROM TH_ReleaseOfOtherLegalDocuments releaseHeader
				  LEFT JOIN M_User u
					ON u.User_ID=releaseHeader.THROOLD_UserID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN TH_LoanOfOtherLegalDocuments thloold
					ON releaseHeader.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
					AND thloold.THLOOLD_Delete_Time IS NULL
				  LEFT JOIN M_Company c
					ON thloold.THLOOLD_CompanyID=c.Company_ID
				  LEFT JOIN M_DocumentGroup dg
					ON dg.DocumentGroup_ID='5'
				  WHERE releaseHeader.THROOLD_ReleaseCode='$code'
				  AND releaseHeader.THROOLD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$fregdate=date("j M Y", strtotime($field['THROOLD_ReleaseDate']));
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
		<th colspan=3>Pengeluaran Dokumen Lainnya (Legal)</th>
		<tr>
			<td width='30%'>Kode Pengeluaran</td>
			<td width='70%'>
				<input name='txtTDROOLD_THROOLD_ID' type='hidden' value='$field[THROOLD_ID]'/>
				<input type='hidden' name='txtTDROOLD_THROOLD_ReleaseCode' value='$field[THROOLD_ReleaseCode]'/>
				$field[THROOLD_ReleaseCode]
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
				<textarea name='txtTHROOLD_Information' id='txtTHROOLD_Information' cols='50' rows='2'>$field[THROOLD_Information]</textarea>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		$query="SELECT tdloold.TDLOOLD_ID, tdloold.TDLOOLD_Code, dol.DOL_NoDokumen, dol.DOL_DocCode,
					 thloold.THLOOLD_LoanCategoryID, dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait,
					 dol.DOL_TglTerbit, dol.DOL_TglBerakhir, dc.DocumentCategory_ID, dc.DocumentCategory_Name
				FROM TD_LoanOfOtherLegalDocuments tdloold, TH_LoanOfOtherLegalDocuments thloold, TH_ReleaseOfOtherLegalDocuments throold,
					 M_DocumentsOtherLegal dol, db_master.M_DocumentCategory dc
				WHERE throold.THROOLD_ReleaseCode='$code'
				AND throold.THROOLD_Delete_Time IS NULL
				AND throold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
				AND thloold.THLOOLD_ID=tdloold.TDLOOLD_THLOOLD_ID
				AND tdloold.TDLOOLD_Response='0'
				AND tdloold.TDLOOLD_Delete_Time IS NULL
				AND dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
				AND dol.DOL_CategoryDocID=dc.DocumentCategory_ID
				";
		$sql = mysql_query($query);
		$i=0;

		$ActionContent .="
		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th></th>
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

		while ($arr=mysql_fetch_array($sql)) {
			$LeadTime=($arr['THLOOLD_LoanCategoryID']=="1")?date('m/d/Y',strtotime("+7 day", strtotime($field['THROOLD_ReleaseDate']))):"";
			$tgl_terbit=date("j M Y", strtotime($arr['DOL_TglTerbit']));
			$tgl_berakhir=date("j M Y", strtotime($arr['DOL_TglBerakhir']));

			$ActionContent .="
			<tr>
				<td class='center'>
					<input id='TDLOOLD_ID[]' name='TDLOOLD_ID[]' type='checkbox' value='$arr[TDLOOLD_ID]'>
				</td>
				<td class='center'><input id='DOL_DocCode[]' name='DOL_DocCode[]' type='hidden' value='$arr[DOL_DocCode]'>$arr[DOL_DocCode]</td>
				<td class='center'>$arr[DocumentCategory_Name]</td>
				<td class='center'>$arr[DOL_NamaDokumen]</td>
				<td class='center'>$arr[DOL_InstansiTerkait]</td>
				<td class='center'>$arr[DOL_NoDokumen]</td>
				<td class='center'>$tgl_terbit</td>
				<td class='center'>$tgl_berakhir</td>
				<td class='center'>
					<textarea id='txtTDROOLD_Information' name='txtTDROOLD_Information[]'></textarea>
				</td>
				<td class='center'>
					<input id='txtTDROOLD_LeadTime[$i]' name='txtTDROOLD_LeadTime[]' type='text' value='$LeadTime' size='10'  readonly='readonly' class='readonly'>
				</td>
			</tr>";
			$i++;
			$loanType=$arr['THLOOLD_LoanCategoryID'];
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
		echo "<meta http-equiv='refresh' content='0; url=release-of-other-legal-documents.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT throold.THROOLD_ID,
				 throold.THROOLD_ReleaseCode,
				 throold.THROOLD_ReleaseDate,
				 u.User_FullName,
 		         drs.DRS_Description,
				 throold.THROOLD_Status
		  FROM TH_ReleaseOfOtherLegalDocuments throold
		  LEFT JOIN M_User u
			ON throold.THROOLD_UserID=u.User_ID
		  LEFT JOIN M_DocumentRegistrationStatus drs
			ON throold.THROOLD_Status=drs.DRS_Name
		  WHERE throold.THROOLD_Delete_Time is NULL
		  AND u.User_ID='$mv_UserID'
		  ORDER BY throold.THROOLD_ID DESC
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
		$fregdate=date("j M Y", strtotime($field['THROOLD_ReleaseDate']));
		$resend=($field['THROOLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[THROOLD_ReleaseCode]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-release-other-legal-documents.php?id=$field[THROOLD_ID]' class='underline'>$field[THROOLD_ReleaseCode]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[User_FullName]</td>
			<td class='center'>$field[DRS_Description]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT throold.THROOLD_ID, throold.THROOLD_ReleaseCode, throold.THROOLD_ReleaseDate, u.User_FullName,
 		          throold.THROOLD_Status
		   FROM TH_ReleaseOfOtherLegalDocuments throold, M_User u
		   WHERE throold.THROOLD_Delete_Time is NULL
		   AND throold.THROOLD_UserID=u.User_ID
		   AND u.User_ID='$mv_UserID'
		   ORDER BY throold.THROOLD_ID DESC";
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
	echo "<meta http-equiv='refresh' content='0; url=release-of-other-legal-documents.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_ReleaseOfOtherLegalDocuments throold
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       throold.THROOLD_Delete_UserID='$mv_UserID',throold.THROOLD_Delete_Time=sysdate(),
			       throold.THROOLD_Update_UserID='$mv_UserID',throold.THROOLD_Update_Time=sysdate()
			   WHERE throold.THROOLD_ID='$_POST[txtTDROOLD_THROOLD_ID]'
			   AND throold.THROOLD_ReleaseCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=release-of-other-legal-documents.php'>";
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
			  FROM TH_LoanOfOtherLegalDocuments thloold, M_Company c
			  WHERE thloold.THLOOLD_LoanCode='$_POST[txtTHROOLD_THLOOLD_Code]'
			  AND thloold.THLOOLD_CompanyID=c.Company_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code="DLL";

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
	// $ActionContent .=$sql;

	if($mysqli->query($sql)) {
		$info=str_replace("<br>", "\n",$_POST['txtTHROOLD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_ReleaseOfOtherLegalDocuments
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID','$_POST[txtTHROOLD_THLOOLD_Code]',
					    '$info','0',NULL,NULL,NULL,NULL,NULL,NULL,'$mv_UserID', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=release-of-other-legal-documents.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$TDLOOLD_ID=$_POST[TDLOOLD_ID];
	$txtTHROOLD_Information=str_replace("<br>", "\n",$_POST[txtTHROOLD_Information]);
	$txtTDROOLD_Information=str_replace("<br>", "\n",$_POST[txtTDROOLD_Information]);
	$txtTDROOLD_LeadTime=$_POST[txtTDROOLD_LeadTime];
	$sum=count($TDLOOLD_ID);

	for ($i=0 ; $i<$sum ; $i++) {
		$TDROOLD_LeadTime=date('Y-m-d H:i:s', strtotime($txtTDROOLD_LeadTime[$i]));
		if ($TDROOLD_LeadTime=="1970-01-01 08:00:00"){
			$TDROOLD_LeadTime="";
		}
		$sql1= "INSERT INTO TD_ReleaseOfOtherLegalDocuments
				VALUES (NULL,NULL,'$_POST[txtTDROOLD_THROOLD_ID]', '$TDLOOLD_ID[$i]','$txtTDROOLD_Information[$i]',
						'$TDROOLD_LeadTime',NULL,'$mv_UserID', sysdate(),'$mv_UserID',
						sysdate(),NULL,NULL)";
		$sql2= "UPDATE TD_LoanOfOtherLegalDocuments
				SET TDLOOLD_Response='1', TDLOOLD_Update_UserID='$mv_UserID',TDLOOLD_Update_Time=sysdate()
				WHERE TDLOOLD_ID='$TDLOOLD_ID[$i]'";
		$mysqli->query($sql1);
		$mysqli->query($sql2);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);

	for($i=0;$i<$jumlah;$i++){
		$step=$i+1;
		$sql2= "INSERT INTO M_Approval
				VALUES (NULL,'$_POST[txtTDROOLD_THROOLD_ReleaseCode]', '$txtA_ApproverID[$i]', '$step',
				        '1',NULL,'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
		$mysqli->query($sql2);
		$sa_query="SELECT *
				   FROM M_Approval
				   WHERE A_TransactionCode='$_POST[txtTDROOLD_THROOLD_ReleaseCode]'
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
			WHERE A_TransactionCode ='$_POST[txtTDROOLD_THROOLD_ReleaseCode]'
			AND A_Step='1'";

	$sql4= "UPDATE TH_ReleaseOfOtherLegalDocuments
			SET THROOLD_Status='waiting', THROOLD_Information='$txtTHROOLD_Information',
			THROOLD_Update_UserID='$mv_UserID',THROOLD_Update_Time=sysdate()
			WHERE THROOLD_ReleaseCode='$_POST[txtTDROOLD_THROOLD_ReleaseCode]'
			AND THROOLD_Delete_Time IS NULL";

	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		mail_release_doc($_POST['txtTDROOLD_THROOLD_ReleaseCode']);
		echo "<meta http-equiv='refresh' content='0; url=release-of-other-legal-documents.php'>";
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
		 cal.manageFields("txtTDROOLD_LeadTime["+i+"]", "txtTDROOLD_LeadTime["+i+"]", "%m/%d/%Y");
	}
}
</script>
