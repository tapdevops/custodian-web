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
<title>Custodian System | Pengeluaran Dokumen Lainnya (Di Luar Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldoconl.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
//LoV UTK DAFTAR PERMINTAAN DOKUMEN
function showList() {
	var docGrup="6";
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

	var txtTHROONLD_THLOONLD_Code = document.getElementById('txtTHROONLD_THLOONLD_Code').value;
	var txtTHROONLD_UserID = document.getElementById('txtTHROONLD_UserID').value;

		if (txtTHROONLD_THLOONLD_Code.replace(" ", "") == "") {
			alert("Kode Permintaan Dokumen Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 			$query = "SELECT *
					  FROM TH_LoanOfOtherNonLegalDocuments thloonld,TD_LoanOfOtherNonLegalDocuments tdloonld
					  WHERE thloonld.THLOONLD_Delete_Time IS NULL
					  AND thloonld.THLOONLD_Status='accept'
					  AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
					  AND tdloonld.TDLOONLD_Response='0'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) {
				$THLOONLD_LoanCode = $data['THLOONLD_LoanCode'];

				$a = "if (txtTHROONLD_THLOONLD_Code == '$THLOONLD_LoanCode') {";
				$a .= "checkDocCode = 1; ";
				$a .= "}
";
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
	var TDLOONLD_ID = document.getElementsByName('TDLOONLD_ID[]');
	var DONL_DocCode = document.getElementsByName('DONL_DocCode[]');
	var txtTDROONLD_LeadTime = document.getElementsByName('txtTDROONLD_LeadTime[]');

	for (var i = 0; i < TDLOONLD_ID.length; i++){
		if (TDLOONLD_ID[i].checked) {
			if (txtTDROONLD_LeadTime[i].value.replace(/^\s+|\s+$/g,'') == "") {
				returnValue = false;
				notcheck = false;
				alert("Tanggal Pengembalian Untuk Dokumen "+DONL_DocCode[i].value+" Belum Ditentukan!");
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
	var TDLOONLD_ID = document.getElementsByName('TDLOONLD_ID[]');
	var DONL_DocCode = document.getElementsByName('DONL_DocCode[]');
	var txtTDROONLD_LeadTime = document.getElementsByName('txtTDROONLD_LeadTime[]');

	for (var i = 0; i < TDLOONLD_ID.length; i++){
		if (TDLOONLD_ID[i].checked) {
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
		<th colspan=3>Pengeluaran Dokumen Lainnya (Di Luar Legal)</th>";

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
				<input id='txtTHROONLD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input id='txtTHROONLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input id='txtTHROONLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input id='txtTHROONLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";

		if($field['User_SPV1']||$field['User_SPV2']){
			$ActionContent .="
			<tr>
				<td>Kode Permintaan Dokumen</td>
				<td>
					<input id='txtTHROONLD_THLOONLD_Code' name='txtTHROONLD_THLOONLD_Code' size='25' type='text' value='' readonly='readonly' onClick='javascript:showList();'/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name='txtTHROONLD_Information' id='txtTHROONLD_Information' cols='50' rows='2'></textarea></td>
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

		$query = "SELECT releaseHeader.THROONLD_Information,
						 releaseHeader.THROONLD_ReleaseDate,
						 releaseHeader.THROONLD_ID,
						 releaseHeader.THROONLD_ReleaseCode,
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
						 dg.DocumentGroup_Name
				  FROM TH_ReleaseOfOtherNonLegalDocuments releaseHeader
				  LEFT JOIN M_User u
					ON u.User_ID=releaseHeader.THROONLD_UserID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN TH_LoanOfOtherNonLegalDocuments thloonld
					ON releaseHeader.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
					AND thloonld.THLOONLD_Delete_Time IS NULL
				  LEFT JOIN M_Company c
					ON thloonld.THLOONLD_CompanyID=c.Company_ID
				  LEFT JOIN M_DocumentGroup dg
					ON dg.DocumentGroup_ID='6'
				  WHERE releaseHeader.THROONLD_ReleaseCode='$code'
				  AND releaseHeader.THROONLD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$fregdate=date("j M Y", strtotime($field['THROONLD_ReleaseDate']));
		// $atasan=($field['User_SPV2'])?$field['User_SPV2']:$field['User_SPV1'];

		// if ($field['tipe_dokumen'] == "ORIGINAL") { $jenis = '27'; }
        // else if ($field['tipe_dokumen'] == "HARDCOPY") { $jenis = '28'; }
        // else if ($field['tipe_dokumen'] == "SOFTCOPY") { $jenis = '29'; }
        // else;
		$jenis = '27'; //Dokumen Asli
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
		<th colspan=3>Pengeluaran Dokumen Lainnya (Di Luar Legal)</th>
		<tr>
			<td width='30%'>Kode Pengeluaran</td>
			<td width='70%'>
				<input name='txtTDROONLD_THROONLD_ID' type='hidden' value='$field[THROONLD_ID]'/>
				<input type='hidden' name='txtTDROONLD_THROONLD_ReleaseCode' value='$field[THROONLD_ReleaseCode]'/>
				$field[THROONLD_ReleaseCode]
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
				<textarea name='txtTHROONLD_Information' id='txtTHROONLD_Information' cols='50' rows='2'>$field[THROONLD_Information]</textarea>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		$query="SELECT tdloonld.TDLOONLD_ID, tdloonld.TDLOONLD_Code, donl.DONL_DocCode,
					   donl.DONL_NoDokumen,
					   donl.DONL_NamaDokumen, donl.DONL_TahunDokumen,
					   donl.DONL_Dept_Code, m_d.Department_Name,
					   thloonld.THLOONLD_LoanCategoryID
				FROM TD_LoanOfOtherNonLegalDocuments tdloonld, TH_LoanOfOtherNonLegalDocuments thloonld, TH_ReleaseOfOtherNonLegalDocuments throld,
					 M_DocumentsOtherNonLegal donl
 	  		  	LEFT JOIN db_master.M_Department m_d
 	  		  		ON donl.DONL_Dept_Code=m_d.Department_Code
				WHERE throld.THROONLD_ReleaseCode='$code'
				AND throld.THROONLD_Delete_Time IS NULL
				AND throld.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
				AND thloonld.THLOONLD_ID=tdloonld.TDLOONLD_THLOONLD_ID
				AND tdloonld.TDLOONLD_Response='0'
				AND tdloonld.TDLOONLD_Delete_Time IS NULL
				AND donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode";
		$sql = mysql_query($query);
		$i=0;

		$ActionContent .="
		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th></th>
			<th>Kode Dokumen</th>
			<th>No. Dokumen</th>
			<th>Nama Dokumen</th>
			<th>Tahun Dokumen</th>
			<th>Departemen</th>
			<th>Keterangan</th>
			<th>Waktu Pengembalian</th>
		</tr>";

		while ($arr=mysql_fetch_array($sql)) {
			$LeadTime=($arr['THLOONLD_LoanCategoryID']=="1")?date('m/d/Y',strtotime("+7 day", strtotime($field['THROONLD_ReleaseDate']))):"";

			$ActionContent .="
			<tr>
				<td class='center'>
					<input id='TDLOONLD_ID[]' name='TDLOONLD_ID[]' type='checkbox' value='$arr[TDLOONLD_ID]'>
				</td>
				<td class='center'>
					<input id='DONL_DocCode[]' name='DONL_DocCode[]' type='hidden' value='$arr[DONL_DocCode]'>$arr[DONL_DocCode]
				</td>
				<td class='center'>$arr[DONL_NoDokumen]</td>
				<td class='center'>$arr[DONL_NamaDokumen]</td>
				<td class='center'>$arr[DONL_TahunDokumen]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>
					<textarea id='txtTDROONLD_Information' name='txtTDROONLD_Information[]'></textarea>
				</td>
				<td class='center'>
					<input id='txtTDROONLD_LeadTime[$i]' name='txtTDROONLD_LeadTime[]' type='text' value='$LeadTime' size='10'  readonly='readonly' class='readonly'>
				</td>
			</tr>";
			$i++;
			$loanType=$arr['THLOONLD_LoanCategoryID'];
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
		echo "<meta http-equiv='refresh' content='0; url=release-of-other-non-legal-documents.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT throld.THROONLD_ID,
				 throld.THROONLD_ReleaseCode,
				 throld.THROONLD_ReleaseDate,
				 u.User_FullName,
 		         drs.DRS_Description,
				 throld.THROONLD_Status
		  FROM TH_ReleaseOfOtherNonLegalDocuments throld
		  LEFT JOIN M_User u
			ON throld.THROONLD_UserID=u.User_ID
		  LEFT JOIN M_DocumentRegistrationStatus drs
			ON throld.THROONLD_Status=drs.DRS_Name
		  WHERE throld.THROONLD_Delete_Time is NULL
		  AND u.User_ID='$mv_UserID'
		  ORDER BY throld.THROONLD_ID DESC
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
		$fregdate=date("j M Y", strtotime($field['THROONLD_ReleaseDate']));
		$resend=($field['THROONLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[THROONLD_ReleaseCode]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-release-other-non-legal-documents.php?id=$field[THROONLD_ID]' class='underline'>$field[THROONLD_ReleaseCode]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[User_FullName]</td>
			<td class='center'>$field[DRS_Description]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT throld.THROONLD_ID, throld.THROONLD_ReleaseCode, throld.THROONLD_ReleaseDate, u.User_FullName,
 		          throld.THROONLD_Status
		   FROM TH_ReleaseOfOtherNonLegalDocuments throld, M_User u
		   WHERE throld.THROONLD_Delete_Time is NULL
		   AND throld.THROONLD_UserID=u.User_ID
		   AND u.User_ID='$mv_UserID'
		   ORDER BY throld.THROONLD_ID DESC";
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
	echo "<meta http-equiv='refresh' content='0; url=release-of-other-non-legal-documents.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_ReleaseOfOtherNonLegalDocuments throld
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       throld.THROONLD_Delete_UserID='$mv_UserID',throld.THROONLD_Delete_Time=sysdate(),
			       throld.THROONLD_Update_UserID='$mv_UserID',throld.THROONLD_Update_Time=sysdate()
			   WHERE throld.THROONLD_ID='$_POST[txtTDROONLD_THROONLD_ID]'
			   AND throld.THROONLD_ReleaseCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=release-of-other-non-legal-documents.php'>";
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
			  FROM TH_LoanOfOtherNonLegalDocuments thloonld, M_Company c
			  WHERE thloonld.THLOONLD_LoanCode='$_POST[txtTHROONLD_THLOONLD_Code]'
			  AND thloonld.THLOONLD_CompanyID=c.Company_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code="DLNL";

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
	echo $CT_Code;

	// Insert kode Pengeluaran dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','OUT','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		$info=str_replace("<br>", "\n",$_POST['txtTHROONLD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_ReleaseOfOtherNonLegalDocuments
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID','$_POST[txtTHROONLD_THLOONLD_Code]',
					    '$info','0',NULL,NULL,NULL,NULL,NULL,NULL,'$mv_UserID', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=release-of-other-non-legal-documents.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$TDLOONLD_ID=$_POST[TDLOONLD_ID];
	$txtTHROONLD_Information=str_replace("<br>", "\n",$_POST[txtTHROONLD_Information]);
	$txtTDROONLD_Information=str_replace("<br>", "\n",$_POST[txtTDROONLD_Information]);
	$txtTDROONLD_LeadTime=$_POST[txtTDROONLD_LeadTime];
	$sum=count($TDLOONLD_ID);

	for ($i=0 ; $i<$sum ; $i++) {
		$TDROONLD_LeadTime=date('Y-m-d H:i:s', strtotime($txtTDROONLD_LeadTime[$i]));
		if ($TDROONLD_LeadTime=="1970-01-01 08:00:00"){
			$TDROONLD_LeadTime="";
		}
		$sql1= "INSERT INTO TD_ReleaseOfOtherNonLegalDocuments
				VALUES (NULL,NULL,'$_POST[txtTDROONLD_THROONLD_ID]', '$TDLOONLD_ID[$i]','$txtTDROONLD_Information[$i]',
						'$TDROONLD_LeadTime',NULL,'$mv_UserID', sysdate(),'$mv_UserID',
						sysdate(),NULL,NULL)";
		$sql2= "UPDATE TD_LoanOfOtherNonLegalDocuments
				SET TDLOONLD_Response='1', TDLOONLD_Update_UserID='$mv_UserID',TDLOONLD_Update_Time=sysdate()
				WHERE TDLOONLD_ID='$TDLOONLD_ID[$i]'";
		$mysqli->query($sql1);
		$mysqli->query($sql2);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);

	for($i=0;$i<$jumlah;$i++){
		$step=$i+1;
		$sql2= "INSERT INTO M_Approval
				VALUES (NULL,'$_POST[txtTDROONLD_THROONLD_ReleaseCode]', '$txtA_ApproverID[$i]', '$step',
				        '1',NULL,'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
		$mysqli->query($sql2);
		$sa_query="SELECT *
				   FROM M_Approval
				   WHERE A_TransactionCode='$_POST[txtTDROONLD_THROONLD_ReleaseCode]'
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
			WHERE A_TransactionCode ='$_POST[txtTDROONLD_THROONLD_ReleaseCode]'
			AND A_Step='1'";

	$sql4= "UPDATE TH_ReleaseOfOtherNonLegalDocuments
			SET THROONLD_Status='waiting', THROONLD_Information='$txtTHROONLD_Information',
			THROONLD_Update_UserID='$mv_UserID',THROONLD_Update_Time=sysdate()
			WHERE THROONLD_ReleaseCode='$_POST[txtTDROONLD_THROONLD_ReleaseCode]'
			AND THROONLD_Delete_Time IS NULL";

	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		mail_release_doc($_POST['txtTDROONLD_THROONLD_ReleaseCode']);
		echo "<meta http-equiv='refresh' content='0; url=release-of-other-non-legal-documents.php'>";
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
		 cal.manageFields("txtTDROONLD_LeadTime["+i+"]", "txtTDROONLD_LeadTime["+i+"]", "%m/%d/%Y");
	}
}
</script>
