<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Agustus 2018																					=
= Update Terakhir	: -           																						=
= Revisi			:																									=
=========================================================================================================================
*/
// session_start();
include ("./include/mother-variable.php");
setcookie('Referer', $_SERVER["REQUEST_URI"], time() + (86400 * 30), "/"); // 86400 = 1 day
?>
<title>Custodian System | Detail Registrasi Dokumen Lainnya (Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdocol.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">

// VALIDASI TANGGAL
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   }
   return this
}

function checkdate(dtStr,row){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("Format Tanggal Pada Baris ke-" + row + " Salah. Format Tanggal : MM/DD/YYYY")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Bulan Pada Baris ke-" + row + " Tidak Valid")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Hari Pada Baris ke-" + row + " Tidak Valid")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Masukkan 4 Digit Tahun Dari "+minYear+" Dan "+maxYear+" Pada Baris ke-" + row)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Tanggal Pada Baris ke-" + row + " Tidak Valid")
		return false
	}
return true
}

// VALIDASI BAGIAN DETAIL SAAT EDIT / APPROVE TRANSAKSI
function validateInput(elem) {
	var returnValue;
	returnValue = true;
	var jRow = document.getElementById('jRow').value;
	if (typeof  document.DL_detail.optTHROOLD_Status != 'undifined') {
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
	}
	var jPT = document.getElementById('count_core_companyid').value;
	for (i = 1; i <= jPT; i++){
		var jrow = document.getElementById('count_row_per_pt'+i).value;
		for(n = 1; n <= jrow; n++){
			var optDOL_KategoriDokumenID = document.getElementById('optDOL_KategoriDokumenID' + i+"_"+n).selectedIndex;
			var txtDOL_NamaDokumen = document.getElementById('txtDOL_NamaDokumen' + i+"_"+n).value;
			var txtDOL_InstansiTerkait = document.getElementById('txtDOL_InstansiTerkait' + i+"_"+n).value;
			var txtDOL_NoDokumen = document.getElementById('txtDOL_NoDokumen' + i+"_"+n).value;
			var txtDOL_TglTerbit = document.getElementById('txtDOL_TglTerbit' + i+"_"+n).value;
			var txtDOL_TglBerakhir = document.getElementById('txtDOL_TglBerakhir' + i+"_"+n).value;
			var txtDOL_Keterangan = document.getElementById('txtDOL_NoDokumen' + i+"_"+n).value;
			// var Date1 = new Date(txtDOL_TglTerbit);
			// var Date2 = new Date(txtDOL_TglBerakhir);

			if(optDOL_KategoriDokumenID == 0) {
				alert("Kategori Dokumen Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false
			}
            if (txtDOL_NamaDokumen.replace(" ", "") == "")  {
				alert("Nama Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtDOL_InstansiTerkait.replace(" ", "") == "")  {
				alert("Instansi Terkait pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtDOL_NoDokumen.replace(" ", "") == "")  {
				alert("Nomor Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtDOL_Keterangan.replace(" ", "") == "")  {
				alert("Keterangan pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			// if (txtDOL_TglTerbit.replace(" ", "") == "")  {
			// 	alert("Tanggal Publikasi pada baris ke-" + i + " Belum Terisi!");
			// 	return false
			// }
			// if (txtDOL_TglTerbit.replace(" ", "") != "")  {
			// 	if (checkdate(txtDOL_TglTerbit,i) == false) {
			// 		return false
			// 	}
			// }
			// if (txtDOL_TglBerakhir.replace(" ", "") != "")  {
			// 	if(txtDOL_TglBerakhir!="-"){
			// 		if (checkdate(txtDOL_TglBerakhir,i) == false) {
			// 			return false
			// 		}
			// 	}
			// 	else {
			// 		if (Date2 < Date1) {
			// 		alert("Tanggal Berakhir Dokumen pada baris ke-" + i + " Lebih Kecil Daripada Tanggal Terbit Dokumen!");
			// 		return false
			// 		}
			// 	}
			// }
		}
	}
	return true
}

//Phase 2
function show_tbl_detail(IDtarget){
	if(document.getElementById('flag_detail'+IDtarget).value == '0'){
		document.getElementById('flag_detail'+IDtarget).value = '1';
		document.getElementById('detail'+IDtarget).style.display = 'block';
		document.getElementById('btn-show-detail'+IDtarget).innerHTML = "Hide";
	}else{
		document.getElementById('flag_detail'+IDtarget).value = '0';
		document.getElementById('detail'+IDtarget).style.display = 'none';
		document.getElementById('btn-show-detail'+IDtarget).innerHTML = "Show";
	}
}
</script>
<style>
.btn-show-detail{
	border:1px solid cornflowerblue;
	background-color:cornflowerblue;
	font-weight:bold;
	border-radius:3px;
	color:#fff;
	padding:5px 10px;
	float:left;
	margin-bottom:10px;
	cursor:pointer;
}
.btn-show-detail:hover{
	background-color:skyblue;
}
</style>
</head>
<?php
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
$decrp = new custodian_encryp;

$act=$decrp->decrypt($_GET["act"]);
$DocID=$decrp->decrypt($_GET["id"]);
if (!empty($_GET['ati']) && !empty($_GET['rdm'])){
	$A_ID=$decrp->decrypt($_GET['ati']);
	$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);

	$query = "SELECT *
			  FROM L_ApprovalRandomCode
			  WHERE ARC_AID='$A_ID'
			  AND ARC_RandomCode='$ARC_RandomCode'";
	$num = mysql_num_rows(mysql_query($query));
	if ($num==0)
		echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}

// Cek apakah user berikut memiliki hak untuk approval
$cApp_query="SELECT DISTINCT dra.A_ApproverID
		  	 FROM TH_RegistrationOfOtherLegalDocuments throold, M_Approval dra
			 WHERE throold.THROOLD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=throold.THROOLD_RegistrationCode
			 AND throold.THROOLD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));

$appQuery=(($act=='approve')&&($approver=="1"))?"AND dra.A_ApproverID='$mv_UserID'":"AND dra.A_Status='2'";

$query="SELECT DISTINCT throold.THROOLD_ID,
						throold.THROOLD_RegistrationCode,
						throold.THROOLD_RegistrationDate,
						u.User_ID,
						u.User_FullName,
						c.Company_Name,
						throold.THROOLD_Status,
						throold.THROOLD_Information,
						dg.DocumentGroup_Name,
						dg.DocumentGroup_ID,
						throold.THROOLD_Reason,
						c.Company_ID,
						(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dra.A_ApproverID) waitingApproval
		FROM TH_RegistrationOfOtherLegalDocuments throold
		LEFT JOIN M_User u
			ON throold.THROOLD_UserID=u.User_ID
		LEFT JOIN M_Company c
			ON throold.THROOLD_CompanyID=c.Company_ID
		LEFT JOIN M_Approval dra
			ON dra.A_TransactionCode=throold.THROOLD_RegistrationCode
			$appQuery
		LEFT JOIN M_DocumentGroup dg
			ON throold.THROOLD_DocumentGroupID=dg.DocumentGroup_ID
		WHERE throold.THROOLD_Delete_Time is NULL
		AND throold.THROOLD_ID='$DocID'
		ORDER BY waitingApproval DESC";
$arr = mysql_fetch_array(mysql_query($query));

$fregdate=date("j M Y", strtotime($arr['THROOLD_RegistrationDate']));
$regUser=$arr['User_ID'];
$DocumentGroup_ID=$arr["DocumentGroup_ID"];

// Cek apakah Staff Custodian atau bukan.
// Staff Custodian memiliki wewenang untuk print registrasi dokumen.
$cs_query = "SELECT *
			 FROM M_DivisionDepartmentPosition ddp, M_Department d
			 WHERE ddp.DDP_DeptID=d.Department_ID
			 AND ddp.DDP_UserID='$mv_UserID'
			 AND d.Department_Name LIKE '%Custodian%'";
$custodian = mysql_num_rows(mysql_query($cs_query));

// Cek apakah Administrator atau bukan.
// Administrator memiliki hak untuk upload softcopy & edit dokumen.
$query = "SELECT *
		  FROM M_UserRole
		  WHERE MUR_RoleID='1'
		  AND MUR_UserID='$mv_UserID'
		  AND MUR_Delete_Time IS NULL";
$admin = mysql_num_rows(mysql_query($query));

$MainContent ="
<form name='DL_detail' id='DL_detail' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";
if(($act=='approve')&&($approver=="1"))
	$MainContent .="<th colspan=3>Persetujuan Pendaftaran Dokumen Lainnya (Legal)</th>";
else
	$MainContent .="<th colspan=3>Pendaftaran Dokumen Lainnya (Legal)</th>";

if((($arr['THROOLD_Status']=="accept")||($arr['THROOLD_Status']=="waiting")) && (($custodian==1) || ($regUser==$mv_UserID))){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='67%'>
			<input name='txtTHROOLD_ID' type='hidden' value='$arr[THROOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROOLD_RegistrationCode]'/>
			$arr[THROOLD_RegistrationCode]
		</td>
		<td width='3%'>
			<a href='print-registration-of-other-legal-documents.php?id=$arr[THROOLD_RegistrationCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else{
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='70%'colspan='2'>
			<input name='txtTHROOLD_ID' type='hidden' value='$arr[THROOLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROOLD_RegistrationCode]'/>
			$arr[THROOLD_RegistrationCode]
		</td>
	</tr>";
}

$MainContent .="
<tr>
	<td>Tanggal Pendaftaran</td>
	<td colspan='2'><input name='txtDOL_RegTime' type='hidden' value='$arr[THROOLD_RegistrationDate]'>$fregdate</td>
</tr>
<tr>
	<td>Nama Pendaftar</td>
	<td colspan='2'>
		<input name='txtDOL_RegUserID' type='hidden' value='$regUser'>$arr[User_FullName]
		<input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>
		<input name='txtDOL_GroupDocID' id='txtDOL_GroupDocID' type='hidden' value='$DocumentGroup_ID'>
	</td>
</tr>
<tr>
	<td>Keterangan</td>";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1)))
	$MainContent .="<td colspan='2'><textarea name='txtTHROOLD_Information' id='txtTHROOLD_Information' cols='50' rows='2'>$arr[THROOLD_Information]</textarea></td>";
else
	$MainContent .="<td colspan='2'><input type='hidden' name='txtTHROOLD_Information' value='$arr[THROOLD_Information]' />$arr[THROOLD_Information]</td>";
$MainContent .="</tr>";

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
						if ($field1['DRS_ID']==3)
							$MainContent .="<option value='$field1[DRS_ID]'>Setuju</option>";
						else if ($field1['DRS_ID']==4)
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
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Pendaftaran</td>";
	if($arr['THROOLD_Status']=="waiting") {
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr['THROOLD_Status']=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THROOLD_Reason]</td>
		</tr>";
	}else if($arr['THROOLD_Status']=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THROOLD_Reason]</td>
		</tr>
		";
	}else {
		$MainContent .="
		<td colspan='2'>Draft</td></tr>";
	}
}

$MainContent .="</table>";

//Phase 2
$MainContent .="
<div class='detail-title'>Daftar Dokumen</div>";

$query_get_company = "SELECT DISTINCT
			CASE WHEN td.TDROOLD_Core_CompanyID IS NOT NULL
				THEN td.TDROOLD_Core_CompanyID
				ELSE th.THROOLD_CompanyID
			END AS company_id,
			CASE WHEN td.TDROOLD_Core_CompanyID IS NOT NULL
				THEN (SELECT c.Company_Name FROM M_Company c
						WHERE c.Company_ID = td.TDROOLD_Core_CompanyID
					)
				ELSE (SELECT c.Company_Name FROM M_Company c
						WHERE c.Company_ID = th.THROOLD_CompanyID
					)
			END AS company_name, td.TDROOLD_Core_CompanyID
		FROM TD_RegistrationOfOtherLegalDocuments td
		LEFT JOIN TH_RegistrationOfOtherLegalDocuments th
			ON td.TDROOLD_THROOLD_ID = th.THROOLD_ID
		WHERE td.TDROOLD_THROOLD_ID='$DocID' AND td.TDROOLD_Delete_Time IS NULL";
	$sql_gc = mysql_query($query_get_company);
	$header_ke = 0;
	$row_ke = 1;
	$array_row_ke = array();
	while($arr_gc = mysql_fetch_array($sql_gc)){
		$header_ke++;
		$MainContent .= "<table width='100%' id='mytable' class='stripeMe'>
			<tr>
				<th width='20%'>Perusahaan</th>
				<td width='80%'>
					<input type='hidden' name='txtCore_CompanyID' value='".$arr_gc['company_id']."'>
					".$arr_gc['company_name']."
				</td>
			</tr>
			<tr>
				<td><input type='hidden' id='flag_detail".$header_ke."' value='0' /></td>
				<td><a class='btn-show-detail' onclick='show_tbl_detail(\"".$header_ke."\")' id='btn-show-detail".$header_ke."'>Show</a>
			</tr>
		</table>
		";

		$query_additional = "";
		if($arr_gc['TDROOLD_Core_CompanyID'] != null){
			$query_additional = "AND TDROOLD_Core_CompanyID='$arr_gc[TDROOLD_Core_CompanyID]'";
		}

		$query_detail = "SELECT TDROOLD_ID, TDROOLD_KategoriDokumenID, TDROOLD_NamaDokumen,
					TDROOLD_InstansiTerkait, TDROOLD_NoDokumen, TDROOLD_TglTerbit, TDROOLD_TglBerakhir,
					TDROOLD_Keterangan
				  FROM TD_RegistrationOfOtherLegalDocuments
				  WHERE TDROOLD_THROOLD_ID='$DocID'
				  $query_additional
				  AND TDROOLD_Delete_Time IS NULL";

		// DETAIL DOKUMEN
		$MainContent .="
		<table width='100%' id='detail".$header_ke."' class='stripeMe' style='display:none;padding-bottom:10px;'>
		<tr>
		    <th>No.</th>
		    <th>Kategori Dokumen</th>
		    <th>Nama Dokumen</th>
		    <th>Instansi Terkait</th>
		    <th>No. Dokumen</th>
		    <th>Tanggal Terbit</th>
		    <th>Tanggal Berakhir</th>
			<th>Keterangan</th>
		</tr>";
		$sql_detail = mysql_query($query_detail);
		$no=0;

		while ( $arr_d = mysql_fetch_array($sql_detail) ){
			$no++;
			$array_row_ke["count_row_per_pt".$header_ke] = $no;
			if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1))) {
				$DocumentCategory_ID=$arr_d["TDROOLD_KategoriDokumenID"];

				$tglterbit=(strpos($arr['TDROOLD_TglTerbit'], '0000-00-00') !== false || strpos($arr_d['TDROOLD_TglTerbit'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr_d['TDROOLD_TglTerbit']));
				$tglberakhir=(strpos($arr_d['TDROOLD_TglBerakhir'], '0000-00-00') !== false || strpos($arr_d['TDROOLD_TglBerakhir'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr_d['TDROOLD_TglBerakhir']));

				$MainContent .="
				<tr>
					<td align='center'>
						<input type='hidden' name='corePT".$header_ke."' id='corePT".$header_ke."' value='".$arr_gc['company_id']."' />
						<input type='hidden' name='txtDOL_ID".$header_ke."[]' id='txtDOL_ID".$header_ke."_".$no."' value='$arr_d[TDROOLD_ID]'/>$no
					</td>
					<td class='center'>
						<select name='optDOL_KategoriDokumenID".$header_ke."[]' id='optDOL_KategoriDokumenID".$header_ke."_".$no."' onchange='showType($no);'>
							<option value='0'>--- Pilih Kategori Dokumen ---</option>";

					$query5="SELECT DocumentCategory_ID, DocumentCategory_Name
						FROM db_master.M_DocumentCategory
						WHERE DocumentCategory_Delete_Time IS NULL";
					$sql5 = mysql_query($query5);

					while ($field5=mysql_fetch_array($sql5)) {
						$selected=($field5["DocumentCategory_ID"]=="$DocumentCategory_ID")?"selected='selected'":"";
						$MainContent .="
							<option value='$field5[DocumentCategory_ID]' $selected>$field5[DocumentCategory_Name]</option>";
					}
				$MainContent .="
						</select>
					</td>
					<td class='center'>
						<input name='txtDOL_NamaDokumen".$header_ke."[]' id='txtDOL_NamaDokumen".$header_ke."_".$no."' type='text' value='$arr_d[TDROOLD_NamaDokumen]'>
					</td>
					<td class='center'>
						<input name='txtDOL_InstansiTerkait".$header_ke."[]' id='txtDOL_InstansiTerkait".$header_ke."_".$no."' type='text' value='$arr_d[TDROOLD_InstansiTerkait]'>
					</td>
		            <td class='center'>
						<input name='txtDOL_NoDokumen".$header_ke."[]' id='txtDOL_NoDokumen".$header_ke."_".$no."' type='text' value='$arr_d[TDROOLD_NoDokumen]'>
					</td>
					<td class='center'>
						<input name='txtDOL_TglTerbit".$header_ke."[]' id='txtDOL_TglTerbit".$header_ke."_".$no."' size='7' type='text' value='$tglterbit' onclick=\"javascript:NewCssCal('txtDOL_TglTerbit".$header_ke."_".$no."', 'MMddyyyy');\">
					<!--</td>
					<td>-->
						<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDOL_TglTerbit".$header_ke."_".$no."').value=''\">
					</td>
					<td class='center'>
						<input name='txtDOL_TglBerakhir".$header_ke."[]' id='txtDOL_TglBerakhir".$header_ke."_".$no."' size='7' type='text' value='$tglberakhir' onclick=\"javascript:NewCssCal('txtDOL_TglBerakhir".$header_ke."_".$no."', 'MMddyyyy');\">
					<!--</td>
					<td>-->
						<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDOL_TglBerakhir".$header_ke."_".$no."').value=''\">
					</td>
					<td class='center'>
						<textarea name='txtDOL_Keterangan".$header_ke."[]' id='txtDOL_Keterangan".$header_ke."_".$no."'>$arr_d[TDROOLD_Keterangan]</textarea>
					</td>
				</tr>";
			}else {
				$tglterbit=(strpos($arr_d['TDROOLD_TglTerbit'], '0000-00-00') !== false || strpos($arr_d['TDROOLD_TglTerbit'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr_d['TDROOLD_TglTerbit']));
				$tglberakhir=(strpos($arr_d['TDROOLD_TglBerakhir'], '0000-00-00') !== false || strpos($arr_d['TDROOLD_TglBerakhir'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr_d['TDROOLD_TglBerakhir']));

				// include ("./config/config_db_master.php");
				$query7="SELECT DocumentCategory_Name
					FROM db_master.M_DocumentCategory
					WHERE DocumentCategory_ID='$arr_d[TDROOLD_KategoriDokumenID]'
						AND DocumentCategory_Delete_Time IS NULL";
				$sql7 = mysql_query($query7);
				$nama_kategoridokumen = "-";
				if(mysql_num_rows($sql7) > 0){
					$data7 = mysql_fetch_array($sql7);
					$nama_kategoridokumen = $data7['DocumentCategory_Name'];
				}
				// include ("./config/config_db.php");

				$MainContent .="
				<tr>
					<td class='center'>
						<input type='hidden' name='corePT".$header_ke."' id='corePT".$header_ke."' value='".$arr_gc['company_id']."' />
						<input type='hidden' name='txtDOL_ID".$header_ke."[]' value='$arr_d[TDROOLD_ID]'/>$no
					</td>
					<td class='center'><input name='optDOL_KategoriDokumenID".$header_ke."[]' type='hidden' value='$arr_d[TDROOLD_KategoriDokumenID]'>$nama_kategoridokumen</td>
					<td class='center'><input name='txtDOL_NamaDokumen".$header_ke."[]' type='hidden' value='$arr_d[TDROOLD_NamaDokumen]'>$arr_d[TDROOLD_NamaDokumen]</td>
					<td class='center'><input name='txtDOL_InstansiTerkait".$header_ke."[]' type='hidden' value='$arr_d[TDROOLD_InstansiTerkait]'>$arr_d[TDROOLD_InstansiTerkait]</td>
					<td class='center'><input name='txtDOL_NoDokumen".$header_ke."[]' type='hidden' value='$arr_d[TDROOLD_NoDokumen]'>$arr_d[TDROOLD_NoDokumen]</td>
					<td class='center'><input name='txtDOL_TglTerbit".$header_ke."[]' type='hidden' value='$arr_d[TDROOLD_TglTerbit]'>$tglterbit</td>
					<td class='center'><input name='txtDOL_TglBerakhir".$header_ke."[]' type='hidden' value='$arr_d[TDROOLD_TglBerakhir]'>$tglberakhir</td>
					<td class='center'><input name='txtDOL_Keterangan".$header_ke."[]' type='hidden' value='$arr_d[TDROOLD_Keterangan]'>$arr_d[TDROOLD_Keterangan]</td>
				</tr>";
			}

			// $no=$no+1;
			$MainContent .="
				<input type='hidden' name='jRow' id='jRow' value='".$no."'/>
				<input type='hidden' name='rowTerakhir' id='rowTerakhir' value='".$row_ke."'/>";
			$row_ke++;
		}
			
		
		$MainContent .="<input type='hidden' name='jPT' id='jPT' value='".$header_ke."' />
		</table>";
		// $header_ke++;
	}
	$MainContent .="<input type='hidden' name='count_core_companyid' id='count_core_companyid' value='".$header_ke."'/>";
	for($o = 1; $o <= $header_ke; $o++){
		$jumlah_row_per_pt = $array_row_ke["count_row_per_pt".$o];
		$MainContent .="<input type='hidden' name='count_row_per_pt".$o."' id='count_row_per_pt".$o."' value='".$jumlah_row_per_pt."'/>";
	}

// echo $act;
if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1"))) {
	$MainContent .="
	<table width='100%' id='button'>
	<tr>
		<th colspan=20>
			<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
			<input name='cancel' type='submit' value='Batal' class='button'/>
		</th>
	</tr>
	</table>";
}
$MainContent .="</form>";


/* ACTIONS */
if(isset($_POST['cancel'])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}

else if(isset($_POST['edit'])) {
	// echo "asd";
	// foreach ($_POST as $key => $value) {
	//     echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
	// }
	// exit();
	$info = str_replace("<br>", "\n", $_POST['txtTHROOLD_Information']);
	$query="UPDATE TH_RegistrationOfOtherLegalDocuments SET THROOLD_Information='$info' WHERE THROOLD_RegistrationCode='$_POST[txtA_TransactionCode]'";
	$mysqli->query($query);

	// $count=$_POST['rowTerakhir'];
	$count_company = $_POST['count_core_companyid'];
	
	for ($c=1; $c<=$count_company; $c++) {
		$Core_CompanyID = $_POST['corePT'.$c];
		foreach($_POST['txtDOL_NoDokumen'.$c] as $key => $value){
			$optDOL_KategoriDokumenID=$_POST['optDOL_KategoriDokumenID'.$c][$key];
			$txtDOL_NamaDokumen=$_POST['txtDOL_NamaDokumen'.$c][$key];
			$txtDOL_InstansiTerkait=$_POST['txtDOL_InstansiTerkait'.$c][$key];
			$txtDOL_NoDokumen=$_POST['txtDOL_NoDokumen'.$c][$key];
			$txtDOL_TglTerbit=$_POST['txtDOL_TglTerbit'.$c][$key];
			$txtDOL_TglBerakhir=$_POST['txtDOL_TglBerakhir'.$c][$key];

			$txtDOL_ID=$_POST['txtDOL_ID'.$c][$key];

			$txtDOL_TglTerbit=date('Y-m-d H:i:s', strtotime($txtDOL_TglTerbit));
			$txtDOL_TglBerakhir=date('Y-m-d H:i:s', strtotime($txtDOL_TglBerakhir));

			$query = "UPDATE TD_RegistrationOfOtherLegalDocuments
					  SET TDROOLD_KategoriDokumenID='$optDOL_KategoriDokumenID',
					  	  TDROOLD_NoDokumen='$txtDOL_NoDokumen',
					  	  TDROOLD_NamaDokumen='$txtDOL_NamaDokumen',
					  	  TDROOLD_InstansiTerkait='$txtDOL_InstansiTerkait',
						  TDROOLD_TglTerbit='$txtDOL_TglTerbit',
						  TDROOLD_TglBerakhir='$txtDOL_TglBerakhir',
					  	  TDROOLD_Update_Time=sysdate(),
					      TDROOLD_Update_UserID='$mv_UserID'
					  WHERE TDROOLD_ID='$txtDOL_ID'";
			$mysqli->query($query);
		}
	}
	if ($_POST['optTHROOLD_Status']){
		$A_TransactionCode=$_POST['txtA_TransactionCode'];
		$A_ApproverID=$mv_UserID;
		$A_Status=$_POST['optTHROOLD_Status'];
		$THROOLD_Reason=str_replace("<br>", "\n",$_POST['txtTHROOLD_Reason']);

		// MENCARI TAHAP APPROVAL USER TERSEBUT
		$query = "SELECT *
			FROM M_Approval
			WHERE A_TransactionCode='$A_TransactionCode'
			AND A_ApproverID='$A_ApproverID' AND A_ApprovalDate IS NULL";
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
		if ($A_Status == '3') {
			if ($step <> $jStep) {
				$query = "UPDATE M_Approval
					SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
					WHERE A_TransactionCode='$A_TransactionCode' AND A_ApproverID='$A_ApproverID'";
				$sql = mysql_query($query);
			}
		}

		// PROSES BILA "SETUJU"
		if ($A_Status=='3') {
			// CEK APAKAH MERUPAKAN APPROVAL FINAL
			if ($step <> $jStep) {
				$nStep=$step+1;

				$jenis = "16"; //Dokumen Lainnya (Legal) - Semua Tipe Dokumen

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
						AND rads.RADS_ProsesID = '1'
					";
					$result = mysql_fetch_array(mysql_query($query));

					if ($result['RADS_StatusID'] == '1') {
						//echo 'Step : ' . $i . ' => Kirim Email Approval<br />';
						$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
						$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
						if ($yquery['abc'] != '0') {
							$query = "UPDATE M_Approval
										SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
										WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
							if ($sql = mysql_query($query)) {
								$xquery = "UPDATE M_Approval
											SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
								if ($xsql = mysql_query($xquery)) {
									mail_registration_doc($A_TransactionCode);
								}
							}
						} else {
							$query = "UPDATE M_Approval
										SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
										WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
							if ($sql = mysql_query($query)) {
								mail_registration_doc($A_TransactionCode);
							}
						}

						/************************************
						* Nicholas - 26 Sept 2018			*
						* Fix Bug skip approval				*
						************************************/

						/*if ($i == $jStep) {
							$query = "UPDATE TH_RegistrationOfOtherLegalDocuments
								SET THROOLD_Status='accept', THROOLD_Update_UserID='$A_ApproverID',
									THROOLD_Update_Time=sysdate()
								WHERE THROOLD_RegistrationCode='$A_TransactionCode'
								AND THROOLD_Delete_Time IS NULL";
							if ($sql = mysql_query($query)) {
								mail_notif_registration_doc($A_TransactionCode, $h_arr['THROOLD_UserID'], 3, 1 );
								mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
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
								mail_notif_registration_doc($A_TransactionCode, $result['A_ApproverID'], 3);
							}
						}

						/************************************
						* Nicholas - 26 Sept 2018			*
						* Fix Bug skip approval				*
						************************************/

						/*if ($i == $jStep) {
							$query = "UPDATE TH_RegistrationOfOtherLegalDocuments
								SET THROOLD_Status='accept', THROOLD_Update_UserID='$A_ApproverID',
									THROOLD_Update_Time=sysdate()
								WHERE THROOLD_RegistrationCode='$A_TransactionCode'
								AND THROOLD_Delete_Time IS NULL";
							if ($sql = mysql_query($query)) {
								mail_notif_registration_doc($A_TransactionCode, $h_arr['THROOLD_UserID'], 3, 1 );
								mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
							}
						}*/
					} else;
				}
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";

				// UPDATE APPROVAL
				/*$query = "UPDATE M_Approval
							SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
								A_Update_Time=sysdate()
							WHERE A_TransactionCode='$A_TransactionCode'
							AND A_ApproverID='$A_ApproverID'";
				$sql = mysql_query($query);

				$nStep=$step+1;
				$query = "UPDATE M_Approval
							SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
							WHERE A_TransactionCode='$A_TransactionCode'
							AND A_Step='$nStep'";
				if ($sql = mysql_query($query)){
					mail_registration_doc($A_TransactionCode);
					if($step=='1'){
						mail_notif_registration_doc($A_TransactionCode, $_POST['txtDOL_RegUserID'], 3 );
						mail_notif_registration_doc($A_TransactionCode, "cust0002", 3 );
					}
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
				}*/
			}
			else {
				$jumlahRow=$_POST['rowTerakhir'];
				// echo $jumlahRow."<br>";
				$jumlahPT=$_POST['jPT'];

				$array_company_id = array();
				for($i = 1; $i <= $jumlahPT; $i++){
					$Core_CompanyID = $_POST['corePT'.$i];
					// echo $Core_CompanyID."<br>";
					$query_get_company="SELECT Company_ID, Company_Name, Company_Code
						FROM M_Company
						WHERE Company_ID='$Core_CompanyID'";

					$sql_gc = mysql_query($query_get_company);
					$dgc = mysql_fetch_array($sql_gc);
					if( !in_array($dgc['Company_ID'], $array_company_id) ){
						$array_company_id['company_id'][] = $dgc['Company_ID'];
						$array_company_id['company_name'][] = $dgc['Company_Name']." - ".$dgc['Company_Code'];
						$array_company_id['banyak'][] = 1;
					}else{
						$index = array_search($dgc['Company_ID'], $array_company_id['company_id']);
						$array_company_id['banyak'][$index] = $array_company_id['banyak'][$index]+1;
					}
				}
				// exit();
				$lokasi_dokumen_kosong = 0;

				for($n = 0; $n < count($array_company_id['company_id']); $n++){
					$company_id = $array_company_id['company_id'][$n];
					// ACTION UNTUK MENENTUKAN JUMLAH LOKASI DOKUMEN YANG TERSEDIA
					$query = "SELECT *
							  FROM L_DocumentLocation
							  WHERE DL_Status='1'
							  AND DL_CompanyID='$company_id'
							  AND DL_DocGroupID='dll'
							  AND DL_Delete_Time IS NULL";
					$avLoc = mysql_num_rows(mysql_query($query));

					$array_company_id['banyak_ruang_tersedia'][$n] = $avLoc;

					if(!$avLoc || $avLoc<$array_company_id['banyak'][$n]){
						$array_company_id['ruang_tersedia'][$n] = "0"; //tidak
						$lokasi_dokumen_kosong++;
					}else{
						$array_company_id['ruang_tersedia'][$n] = "1"; //ya
					}
				}

				// ACTION UNTUK MENENTUKAN JUMLAH LOKASI DOKUMEN YANG TERSEDIA
				// $query = "SELECT *
				// 		  FROM L_DocumentLocation
				// 		  WHERE DL_Status='1'
				// 		  AND DL_CompanyID='$_POST[txtCompany_ID]'
				// 		  AND DL_DocGroupID='dll'
				// 		  AND DL_Delete_Time is NULL";
				// $avLoc = mysql_num_rows(mysql_query($query));

					// print_r($array_company_id);
					// exit();

				// if((!$avLoc)||($avLoc<$jumlahRow)){
				if($lokasi_dokumen_kosong > 0){
					$pesan = "";
					for($z = 0; $z < count($array_company_id['company_id']); $z++){
						if($array_company_id['ruang_tersedia'][$z] == 0){
							$pesan .= "Lokasi untuk Dokumen ".$array_company_id['company_name'][$z]." Tidak Tersedia. Lokasi yang Tersedia : ".$array_company_id['banyak_ruang_tersedia'][$z].". ";
						}
					}
					?>
                    <script language="JavaScript" type="text/JavaScript">

					alert("<?=$pesan?>\nHubungi Custodian System Administrator untuk Mengatur Lokasi dan Lakukan Persetujuan Ulang.");
					</script>
                    <?PHP
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
				}
				else{
					// UPDATE APPROVAL
					$query = "UPDATE M_Approval
								SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
									A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_ApproverID='$A_ApproverID'";
					if ($sql = mysql_query($query)){
						//mail_registration_doc($A_TransactionCode);
						$query1 = "SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '1'";
						$sql1 = mysql_fetch_array(mysql_query($query1));
						//if($step=='1'){
							mail_notif_registration_doc($A_TransactionCode, $_POST['txtDOL_RegUserID'], 3, 1 );
							mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
							mail_notif_registration_doc($A_TransactionCode, $sql1['A_ApproverID'], 3, 1);
						//}
					}

					$query = "UPDATE TH_RegistrationOfOtherLegalDocuments
								SET THROOLD_Status='accept', THROOLD_Update_UserID='$A_ApproverID', THROOLD_Update_Time=sysdate()
								WHERE THROOLD_RegistrationCode='$A_TransactionCode'
								AND THROOLD_Delete_Time IS NULL";
					$sql = mysql_query($query);
					// ACTION UNTUK GENERATE NO DOKUMEN
					$regyear=date("y");
					$regmonth=date("m");

					$count_company = $_POST['count_core_companyid'];
	
					for ($c=1; $c<=$count_company; $c++) {
						$Core_CompanyID = $_POST['corePT'.$c];

						// Cari Kode Perusahaan
						$query = "SELECT *
									FROM M_Company
									WHERE Company_ID='$Core_CompanyID'";
						$field = mysql_fetch_array(mysql_query($query));
						$Company_Code=$field['Company_Code'];

						// Cari Kode Dokumen Grup
						$query = "SELECT *
									FROM M_DocumentGroup
									WHERE DocumentGroup_ID ='$_POST[txtDOL_GroupDocID]'";
						$field = mysql_fetch_array(mysql_query($query));
						$DocumentGroup_Code=$field['DocumentGroup_Code'];

						// Cari No Dokumen Terakhir
						$query = "SELECT MAX(CD_SeqNo)
									FROM M_CodeDocument
									WHERE CD_Year='$regyear'
									AND CD_GroupDocCode='$DocumentGroup_Code'
									AND CD_CompanyCode='$Company_Code'
									AND CD_Delete_Time is NULL";
						$field = mysql_fetch_array(mysql_query($query));

						if($field[0]==NULL)
							$maxnum=0;
						else
							$maxnum=$field[0];
						$nnum=$maxnum+1;

						// for($i=1;$i<$jumlahRow;$i++){
						
						foreach($_POST['txtDOL_NoDokumen'.$c] as $key => $value){
							$k = $i-1;
							$company_id = $array_company_id['company_id'][$k];

							$query = "SELECT *
								  FROM L_DocumentLocation
								  WHERE DL_Status='1'
								  AND DL_CompanyID='$Core_CompanyID'
								  AND DL_DocGroupID='dll'
								  AND DL_Delete_Time is NULL
								  AND DL_ID=(SELECT MIN(DL_ID)
											 FROM L_DocumentLocation
										     WHERE DL_Status='1'
										     AND DL_CompanyID='$Core_CompanyID'
										     AND DL_DocGroupID='dll'
										     AND DL_Delete_Time is NULL)";
							$arr = mysql_fetch_array(mysql_query($query));
							$DLIU_LocationCode=$arr['DL_Code'];

							$optDOL_KategoriDokumenID=$_POST['optDOL_KategoriDokumenID'.$c][$key];
							$txtDOL_NamaDokumen=$_POST['txtDOL_NamaDokumen'.$c][$key];
	                        $txtDOL_InstansiTerkait=$_POST['txtDOL_InstansiTerkait'.$c][$key];
							$txtDOL_NoDokumen=$_POST['txtDOL_NoDokumen'.$c][$key];
							$txtDOL_TglTerbit=date('Y-m-d H:i:s', strtotime($_POST['txtDOL_TglTerbit'.$c][$key]));
							$txtDOL_TglBerakhir=date('Y-m-d H:i:s', strtotime($_POST['txtDOL_TglBerakhir'.$c][$key]));
							$txtDOL_Keterangan=$_POST['txtDOL_Keterangan'.$c][$key];

							$step=$i+1;
							$newnum=str_pad($nnum,4,"0",STR_PAD_LEFT);
							$CD_Code="$newnum$Company_Code$DocumentGroup_Code$regmonth$regyear";

							$sql2= "INSERT INTO M_CodeDocument
										VALUES ('$CD_Code','$nnum','$Company_Code','$DocumentGroup_Code','$regmonth','$regyear',
												'$mv_UserID', sysdate(),'$mv_UserID',
												sysdate(),NULL,NULL)";
							$mysqli->query($sql2);

							$query="UPDATE L_DocumentLocation
									SET DL_Status='0', 	DL_Update_UserID='$mv_UserID', DL_Update_Time=sysdate()
									WHERE DL_Code='$DLIU_LocationCode'";

							// Memindahkan Pendaftaran Dokumen ke M_DocumentsOtherLegal
							$sql3= "INSERT INTO M_DocumentsOtherLegal
									VALUES (NULL,
											'$CD_Code',
											'$_POST[txtDOL_RegUserID]',
											'$_POST[txtDOL_RegTime]',
											'$Core_CompanyID',
											'$_POST[txtDOL_GroupDocID]',
											'$optDOL_KategoriDokumenID',
											'$txtDOL_NamaDokumen',
											'$txtDOL_InstansiTerkait',
											'$txtDOL_NoDokumen',
											'$txtDOL_TglTerbit',
											'$txtDOL_TglBerakhir',
											'$txtDOL_Keterangan',
											'$DLIU_LocationCode','1', NULL,
											'$mv_UserID', sysdate(),'$mv_UserID',
											sysdate(),NULL,NULL)";
							$mysqli->query($sql3);
							$mysqli->query($query);
							$nnum=$nnum+1;
						}
					}
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
				}
			}
		}

		// PROSES BILA "TOLAK"
		if ($A_Status=='4') {
			// UPDATE APPROVAL
			$query = "UPDATE M_Approval
						SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
							A_Update_Time=sysdate()
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_ApproverID='$A_ApproverID'";
			$sql = mysql_query($query);

			$query = "UPDATE TH_RegistrationOfOtherLegalDocuments
						SET THROOLD_Status='reject', THROOLD_Reason='$THROOLD_Reason',
							THROOLD_Update_Time=sysdate(), THROOLD_Update_UserID='$A_ApproverID'
						WHERE THROOLD_RegistrationCode='$A_TransactionCode'";

			$query1 = "UPDATE M_Approval
						SET A_Update_Time=sysdate(), A_Update_UserID='$A_ApproverID',
							A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
							A_Status='$A_Status'
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_Step>'$step'";
			if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
				$h_query="SELECT *
							  FROM TH_RegistrationOfOtherLegalDocuments
							  WHERE THROOLD_RegistrationCode='$A_TransactionCode'
							  AND THROOLD_Delete_Time IS NULL";
				$h_arr=mysql_fetch_array(mysql_query($h_query));
				mail_notif_registration_doc($A_TransactionCode, $h_arr['THROOLD_UserID'], 4 );

				$e_query="SELECT *
						  FROM M_Approval
						  WHERE A_TransactionCode='$A_TransactionCode'
						  AND A_Step<'$step' ";
				$e_sql=mysql_query($e_query);
				while ($e_arr=mysql_fetch_array($e_sql)){
					mail_notif_registration_doc($A_TransactionCode, $e_arr['A_ApproverID'], 4 );
				}
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
		}
		}
		else {
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
		}
	}
	else{
	echo "<meta http-equiv='refresh' content='0; url=$PHP_SELF'>";
	}
}

$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>
