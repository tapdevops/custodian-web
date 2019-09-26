<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 4 Mei 2012																						=
= Update Terakhir	: 23 Mei 2012																						=
= Revisi			:																									=
= 		23/05/2012	: Approval hanya ke 1 level di atasnya, custodian, dan custodian head (OK)							=
=					  Edit Transaksi (OK)																				=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
setcookie('Referer', $_SERVER["REQUEST_URI"], time() + (86400 * 30), "/"); // 86400 = 1 day
?>
<title>Custodian System | Detail Registrasi Dokumen</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdoc.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
// JQUERY UNTUK MENAMPILKAN TIPE DOKUMEN DARI KATEGORI DOKUMEN YANG DIPILIH
function showType(i){
	var txtDL_GroupDocID = document.getElementById('txtDL_GroupDocID').value;
		$.post("jQuery.DocumentType.php", {
			CategoryID: $('#txtDL_CategoryDocID'+i).val(),
			GroupID: txtDL_GroupDocID
		}, function(response){

			setTimeout("finishAjax('txtDL_TypeDocID"+i+"', '"+escape(response)+"')", 400);
		});
}
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
}

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
	if (typeof  document.DL_detail.optTHROLD_Status != 'undifined') {
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
	}
	var jPT = document.getElementById('count_core_companyid').value;
	for (i = 1; i <= jPT; i++){
		var jrow = document.getElementById('count_row_per_pt'+i).value;
		for(n = 1; n <= jrow; n++){
			var txtDL_CategoryDocID = document.getElementById('txtDL_CategoryDocID' + i+"_"+n).selectedIndex;
			var txtDL_TypeDocID = document.getElementById('txtDL_TypeDocID' + i+"_"+n).selectedIndex;
			var txtDL_Instance = document.getElementById('txtDL_Instance' + i+"_"+n).value;
			var txtDL_NoDoc = document.getElementById('txtDL_NoDoc' + i+"_"+n).value;
			var txtDL_RegDate = document.getElementById('txtDL_RegDate' + i+"_"+n).value;
			var txtDL_ExpDate = document.getElementById('txtDL_ExpDate' + i+"_"+n).value;
			var txtDL_Information1 = document.getElementById('txtDL_Information1' + i+"_"+n).selectedIndex;
			var txtDL_Information2 = document.getElementById('txtDL_Information2' + i+"_"+n).selectedIndex;
			var Date1 = new Date(txtDL_RegDate);
			var Date2 = new Date(txtDL_ExpDate);

			if(txtDL_CategoryDocID == 0) {
				alert("Kategori Dokumen Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false
			}
			if(txtDL_TypeDocID == 0) {
				alert("Tipe Dokumen Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false
			}
			if (txtDL_Instance.replace(" ", "") == "")  {
				alert("Nama Instansi pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			if (txtDL_NoDoc.replace(" ", "") == "")  {
				alert("Nomor Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false
			}
			// if (txtDL_RegDate.replace(" ", "") == "")  {
			// 	alert("Tanggal Publikasi pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
			// 	return false
			// }
			// if (txtDL_RegDate.replace(" ", "") != "")  {
			// 	if (checkdate(txtDL_RegDate,i) == false) {
			// 		return false
			// 	}
			// }
			// if (txtDL_ExpDate.replace(" ", "") != "")  {
			// 	if(txtDL_ExpDate!="-"){
			// 		if (checkdate(txtDL_ExpDate,i) == false) {
			// 			return false
			// 		}
			// 	}
			// 	else {
			// 		if (Date2 < Date1) {
			// 		alert("Tanggal Habis Masa Berlaku pada baris ke-" + n + " pada Perusahaan ke-"+i+" Lebih Kecil Daripada Tanggal Publikasi!");
			// 		return false
			// 		}
			// 	}
			// }
			if(txtDL_Information1 == 0) {
				alert("Informasi Dokumen 1 Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false
			}
			if(txtDL_Information2 == 0) {
				alert("Informasi Dokumen 2 Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false
			}
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
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
$decrp = new custodian_encryp;

$act=$decrp->decrypt($_GET["act"]);
$DocID=$decrp->decrypt($_GET["id"]);
if (($_GET['ati'])&&($_GET['rdm'])){
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
		  	 FROM TH_RegistrationOfLegalDocument throld, M_Approval dra
			 WHERE throld.THROLD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=throld.THROLD_RegistrationCode
			 AND throld.THROLD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));

$appQuery=(($act=='approve')&&($approver=="1"))?"AND dra.A_ApproverID='$mv_UserID'":"AND dra.A_Status='2'";

$query="SELECT DISTINCT throld.THROLD_ID,
						throld.THROLD_RegistrationCode,
						throld.THROLD_RegistrationDate,
						u.User_ID,
						u.User_FullName,
						c.Company_Name,
						throld.THROLD_Status,
						throld.THROLD_Information,
						dg.DocumentGroup_Name,
						dg.DocumentGroup_ID,
						throld.THROLD_Reason,
						c.Company_ID,
						(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dra.A_ApproverID) waitingApproval
		FROM TH_RegistrationOfLegalDocument throld
		LEFT JOIN M_User u
			ON throld.THROLD_UserID=u.User_ID
		LEFT JOIN M_Company c
			ON throld.THROLD_CompanyID=c.Company_ID
		LEFT JOIN M_Approval dra
			ON dra.A_TransactionCode=throld.THROLD_RegistrationCode
			$appQuery
		LEFT JOIN M_DocumentGroup dg
			ON throld.THROLD_DocumentGroupID=dg.DocumentGroup_ID
		WHERE throld.THROLD_Delete_Time is NULL
		AND throld.THROLD_ID='$DocID'
		ORDER BY waitingApproval DESC";
$arr = mysql_fetch_array(mysql_query($query));

$fregdate=date("j M Y", strtotime($arr['THROLD_RegistrationDate']));
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
	$MainContent .="<th colspan=3>Persetujuan Pendaftaran Dokumen</th>";
else
	$MainContent .="<th colspan=3>Pendaftaran Dokumen</th>";

if((($arr[THROLD_Status]=="accept")||($arr[THROLD_Status]=="waiting")) && (($custodian==1) || ($regUser==$mv_UserID))){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='67%'>
			<input name='txtTHROLD_ID' type='hidden' value='$arr[THROLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROLD_RegistrationCode]'/>
			$arr[THROLD_RegistrationCode]
		</td>
		<td width='3%'>
			<a href='print-registration-of-document.php?id=$arr[THROLD_RegistrationCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else{
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='70%'colspan='2'>
			<input name='txtTHROLD_ID' type='hidden' value='$arr[THROLD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROLD_RegistrationCode]'/>
			$arr[THROLD_RegistrationCode]
		</td>
	</tr>";
}

$MainContent .="
<tr>
	<td>Tanggal Pendaftaran</td>
	<td colspan='2'><input name='txtDL_RegTime' type='hidden' value='$arr[THROLD_RegistrationDate]'>$fregdate</td>
</tr>
<tr>
	<td>Nama Pendaftar</td>
	<td colspan='2'><input name='txtDL_RegUserID' type='hidden' value='$regUser'>$arr[User_FullName]</td>
</tr>
<!--<tr>
	<td>Nama Perusahaan</td>
	<td colspan='2'>--><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/><!--$arr[Company_Name]</td>
</tr>-->
<tr>
	<td>Grup Dokumen</td>
	<td colspan='2'><input name='txtDL_GroupDocID' id='txtDL_GroupDocID' type='hidden' value='$DocumentGroup_ID'>$arr[DocumentGroup_Name]</td>
</tr>
<tr>
	<td>Keterangan</td>";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1)))
	$MainContent .="<td colspan='2'><textarea name='txtTHROLD_Information' id='txtTHROLD_Information' cols='50' rows='2'>$arr[THROLD_Information]</textarea></td>";
else
	$MainContent .="<td colspan='2'><input type='hidden' name='txtTHROLD_Information' value='$arr[THROLD_Information]' />$arr[THROLD_Information]</td>";
$MainContent .="</tr>";

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
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Pendaftaran</td>";
	if($arr[THROLD_Status]=="waiting") {
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr[THROLD_Status]=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THROLD_Reason]</td>
		</tr>";
	}else if($arr[THROLD_Status]=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THROLD_Reason]</td>
		</tr>
		";
	}else {
		$MainContent .="
		<td colspan='2'>Draft</td></tr>";
	}
}

$MainContent .="</table>";

	// DETAIL DOKUMEN LEGAL
$MainContent .="
<div class='detail-title'>Daftar Dokumen</div>";
$query_get_company = "SELECT DISTINCT
			CASE WHEN td.TDROLD_Core_CompanyID IS NOT NULL
				THEN td.TDROLD_Core_CompanyID
				ELSE th.THROLD_CompanyID
			END AS company_id,
			CASE WHEN td.TDROLD_Core_CompanyID IS NOT NULL
				THEN (SELECT c.Company_Name FROM M_Company c
						WHERE c.Company_ID = td.TDROLD_Core_CompanyID
					)
				ELSE (SELECT c.Company_Name FROM M_Company c
						WHERE c.Company_ID = th.THROLD_CompanyID
					)
			END AS company_name, td.TDROLD_Core_CompanyID
		FROM TD_RegistrationOfLegalDocument td
		LEFT JOIN TH_RegistrationOfLegalDocument th
			ON td.TDROLD_THROLD_ID = th.THROLD_ID
		WHERE td.TDROLD_THROLD_ID='$DocID' AND td.TDROLD_Delete_Time IS NULL";
	$sql_gc = mysql_query($query_get_company);
	$header_ke = 0;
	$row_ke = 1;
	$array_row_ke = array();
	while($arr_gc = mysql_fetch_array($sql_gc)){
		$header_ke++;
		$MainContent .= "<table width='100%' id='mytable' class='stripeMe'>
			<tr>
				<th width='20%'>Perusahaan</th>
				<td width='80%'>".$arr_gc['company_name']."</td>
			</tr>
			<tr>
				<td><input type='hidden' id='flag_detail".$header_ke."' value='0' /></td>
				<td><a class='btn-show-detail' onclick='show_tbl_detail(\"".$header_ke."\")' id='btn-show-detail".$header_ke."'>Show</a>
			</tr>
		</table>
		";

		$query_additional = "";
		if($arr_gc['TDROLD_Core_CompanyID'] != null){
			$query_additional = "AND TDROLD_Core_CompanyID='$arr_gc[TDROLD_Core_CompanyID]'";
		}
		$query = "SELECT tdrold.TDROLD_ID, dt.DocumentType_Name, dt.DocumentType_ID, tdrold.TDROLD_DocumentNo,
						 tdrold.TDROLD_DatePublication, tdrold.TDROLD_DateExpired, di1.DocumentInformation1_ID,
						 di1.DocumentInformation1_Name, di2.DocumentInformation2_ID, di2.DocumentInformation2_Name,
				 		 tdrold.TDROLD_Instance,tdrold.TDROLD_DocumentInformation3, dc.DocumentCategory_Name,
						 dc.DocumentCategory_ID
		 		  FROM TD_RegistrationOfLegalDocument tdrold, M_DocumentType dt, M_DocumentInformation1 di1,
					   M_DocumentInformation2 di2, M_DocumentCategory dc
				  WHERE tdrold.TDROLD_THROLD_ID='$DocID'
				  AND tdrold.TDROLD_DocumentTypeID=dt.DocumentType_ID
				  AND tdrold.TDROLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND tdrold.TDROLD_DocumentInformation1ID=di1.DocumentInformation1_ID
				  AND tdrold.TDROLD_DocumentInformation2ID=di2.DocumentInformation2_ID
				  $query_additional
				  AND tdrold.TDROLD_Delete_Time IS NULL";

		$MainContent .= "<table width='100%' id='detail".$header_ke."' class='stripeMe' style='display:none;padding-bottom:10px;'>
		<tr>
		  	<th>No</th>
		   	<th>Kategori Dokumen</th>
		   	<th>Tipe Dokumen</th>
		    <th>Instansi Terkait</th>
		    <th>Nomor Dokumen</th>
		    <th colspan='2'>Tanggal Terbit<br>(MM/DD/YYY)</th>
		    <th colspan='2'>Tanggal Habis Berlaku<br>(MM/DD/YYY)</th>
		    <th>Keterangan 1</th>
		    <th>Keterangan 2</th>
		    <th>Keterangan 3</th>
		</tr>";
		$sql = mysql_query($query);
		$no=0;

		while ($arr = mysql_fetch_array($sql)) {
			$no++;
			$array_row_ke["count_row_per_pt".$header_ke] = $no;
			if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1))) {
				$DocumentCategory_ID=$arr["DocumentCategory_ID"];

				$fpubdate=(strpos($arr['TDROLD_DatePublication'], '0000-00-00') !== false || strpos($arr['TDROLD_DatePublication'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROLD_DatePublication']));
				$fexpdate=(strpos($arr['TDROLD_DateExpired'], '0000-00-00') !== false || strpos($arr['TDROLD_DateExpired'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROLD_DateExpired']));

				$MainContent .="
				<tr>
					<td align='center'>
						<input type='hidden' name='corePT".$header_ke."' id='corePT".$header_ke."' value='".$arr_gc['company_id']."' />
						<input type='hidden' name='txtTDROLD_ID".$header_ke."[]' id='txtTDROLD_ID".$header_ke."_".$no."' value='$arr[TDROLD_ID]'/>$no
					</td>
					<td class='center'>
						<select name='txtDL_CategoryDocID".$header_ke."[]' id='txtDL_CategoryDocID".$header_ke."_".$no."' onchange='showType(\"".$header_ke."_".$no."\");'>
							<option value='0'>--- Pilih Kategori Dokumen ---</option>";

					$query5="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name
							 FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
							 WHERE dgct.DGCT_DocumentGroupID='$DocumentGroup_ID'
							 AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
							 AND dgct.DGCT_Delete_Time is NULL";
					$sql5 = mysql_query($query5);

					while ($field5=mysql_fetch_array($sql5)) {
						$selected=($field5["DocumentCategory_ID"]=="$DocumentCategory_ID")?"selected='selected'":"";
						$MainContent .="
							<option value='$field5[DocumentCategory_ID]' $selected>$field5[DocumentCategory_Name]</option>";
					}
				$MainContent .="
						</select>
					</td>
					<td>
						<select name='txtDL_TypeDocID".$header_ke."[]' id='txtDL_TypeDocID".$header_ke."_".$no."'>
							<option value='0'>--- Pilih Kategori Dokumen Terlebih Dahulu ---</option>";

					$query6="SELECT DISTINCT dt.DocumentType_ID,dt.DocumentType_Name
							 FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
							 WHERE dgct.DGCT_DocumentGroupID='$DocumentGroup_ID'
							 AND dgct.DGCT_DocumentCategoryID='$DocumentCategory_ID'
							 AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
							 AND dgct.DGCT_Delete_Time is NULL";
					$sql6 = mysql_query($query6);

					while ($field6=mysql_fetch_array($sql6)) {
						$selected1=($field6["DocumentType_ID"]==$arr["DocumentType_ID"])?"selected='selected'":"";
						$MainContent .="
						<option value='$field6[DocumentType_ID]' $selected1>$field6[DocumentType_Name]</option>";
					}
				$MainContent .="
						</select>
					</td>
					<td class='center'>
						<input name='txtDL_Instance".$header_ke."[]' id='txtDL_Instance".$header_ke."_".$no."' type='text' value='$arr[TDROLD_Instance]'>
					</td>
					<td class='center'>
						<input name='txtDL_NoDoc".$header_ke."[]' id='txtDL_NoDoc".$header_ke."_".$no."' type='text' value='$arr[TDROLD_DocumentNo]'>
					</td>
					<td class='center'>
						<input name='txtDL_RegDate".$header_ke."[]' id='txtDL_RegDate".$header_ke."_".$no."' size='7' type='text' value='$fpubdate' onclick=\"javascript:NewCssCal('txtDL_RegDate".$header_ke."_".$no."', 'MMddyyyy');\">
					</td>
					<td>
						<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDL_RegDate".$header_ke."_".$no."').value=''\">
					</td>
					<td class='center'>
						<input name='txtDL_ExpDate".$header_ke."[]' id='txtDL_ExpDate".$header_ke."_".$no."' size='7' type='text' value='$fexpdate' onclick=\"javascript:NewCssCal('txtDL_ExpDate".$header_ke."_".$no."', 'MMddyyyy');\">
					</td>
					<td>
						<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDL_ExpDate".$header_ke."_".$no."').value=''\">
					</td>
					<td class='center'>
						<select name='txtDL_Information1".$header_ke."[]' id='txtDL_Information1".$header_ke."_".$no."'>
							<option value='0'>--- Pilih Keterangan Dokumen 1 ---</option>";

					$query1="SELECT *
							 FROM M_DocumentInformation1
							 WHERE DocumentInformation1_Delete_Time is NULL
							 ORDER BY DocumentInformation1_ID";
		            $hasil1 = mysql_query($query1);

		            while ($data = mysql_fetch_array($hasil1)){
						$selected2=($data[0]==$arr[DocumentInformation1_ID])?"selected='selected'":"";
						$MainContent .="
							<option value='$data[0]' $selected2>$data[1]</option>";
		            }

				$MainContent .="
						</select>
					</td>
					<td class='center'>
						<select name='txtDL_Information2".$header_ke."[]' id='txtDL_Information2".$header_ke."_".$no."'>
							<option value='0'>--- Pilih Keterangan Dokumen 2 ---</option>";

					$query1="SELECT *
							 FROM M_DocumentInformation2
							 WHERE DocumentInformation2_Delete_Time is NULL
							 ORDER BY DocumentInformation2_ID";
		            $hasil1 = mysql_query($query1);

		            while ($data = mysql_fetch_array($hasil1)){
						$selected3=($data[0]==$arr[DocumentInformation2_ID])?"selected='selected'":"";
						$MainContent .="
							<option value='$data[0]' $selected3>$data[1]</option>";
					}
				$MainContent .="
						</select>
					</td>
					<td class='center'>
						<textarea name='txtDL_Information3".$header_ke."[]' id='txtDL_Information3".$header_ke."_".$no."'>$arr[TDROLD_DocumentInformation3]</textarea>
					</td>
				</tr>";
			}else {
				$fpubdate=(strpos($arr['TDROLD_DatePublication'], '0000-00-00') !== false || strpos($arr['TDROLD_DatePublication'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROLD_DatePublication']));
				$fexpdate=(strpos($arr['TDROLD_DateExpired'], '0000-00-00') !== false || strpos($arr['TDROLD_DateExpired'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROLD_DateExpired']));

				$MainContent .="
				<tr>
					<td class='center'>
						<input type='hidden' name='txtTDROLD_ID".$header_ke."[]' value='$arr[TDROLD_ID]'/>$no
					</td>
					<td class='center'><input name='txtDL_CategoryDocID".$header_ke."[]' type='hidden' value='$arr[DocumentCategory_ID]'>$arr[DocumentCategory_Name]</td>
					<td class='center'><input name='txtDL_TypeDocID".$header_ke."[]' type='hidden' value='$arr[DocumentType_ID]'>$arr[DocumentType_Name]</td>
					<td class='center'><input name='txtDL_Instance".$header_ke."[]' type='hidden' value='$arr[TDROLD_Instance]'>$arr[TDROLD_Instance]</td>
					<td class='center'><input name='txtDL_NoDoc".$header_ke."[]' type='hidden' value='$arr[TDROLD_DocumentNo]'>$arr[TDROLD_DocumentNo]</td>
					<td class='center' colspan='2'><input name='txtDL_RegDate".$header_ke."[]' type='hidden' value='$arr[TDROLD_DatePublication]'>$fpubdate</td>
					<td class='center' colspan='2'><input name='txtDL_ExpDate".$header_ke."[]' type='hidden' value='$arr[TDROLD_DateExpired]'>$fexpdate</td>
					<td class='center'><input name='txtDL_Information1".$header_ke."[]' type='hidden' value='$arr[DocumentInformation1_ID]'>$arr[DocumentInformation1_Name]</td>
					<td class='center'><input name='txtDL_Information2".$header_ke."[]' type='hidden' value='$arr[DocumentInformation2_ID]'>$arr[DocumentInformation2_Name]</td>
					<td class='center'><input name='txtDL_Information3".$header_ke."[]' type='hidden' value='$arr[TDROLD_DocumentInformation3]'>$arr[TDROLD_DocumentInformation3]</td>
				</tr>";
			}

			// $no=$no+1;
			$MainContent .="
				<input type='hidden' name='jRow' id='jRow' value='$no'/>
				<input type='hidden' name='rowTerakhir' id='rowTerakhir' value='$row_ke'/>";
			$row_ke=$row_ke+1;
		}
		$MainContent .="<input type='hidden' name='jPT' id='jPT' value='$header_ke' />
		</table>";
	}
	$MainContent .="<input type='hidden' name='count_core_companyid' id='count_core_companyid' value='".$header_ke."'/>";
	for($o = 1; $o <= $header_ke; $o++){
		$jumlah_row_per_pt = $array_row_ke["count_row_per_pt".$o];
		$MainContent .="<input type='hidden' name='count_row_per_pt".$o."' id='count_row_per_pt".$o."' value='".$jumlah_row_per_pt."'/>";
	}
// $MainContent .="</table>";

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
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}

else if(isset($_POST[edit])) {
	// foreach ($_POST as $key => $value) {
	//     echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
	// }
	// exit();
	$info = str_replace("<br>", "\n", $_POST['txtTHROLD_Information']);
	$query="UPDATE TH_RegistrationOfLegalDocument SET THROLD_Information='$info' WHERE THROLD_RegistrationCode='$_POST[txtA_TransactionCode]'";
	$mysqli->query($query);

	// $count=$_POST['rowTerakhir'];
	$count_company = $_POST['count_core_companyid'];
	
	for ($c=1; $c<=$count_company; $c++) {
		$Core_CompanyID = $_POST['corePT'.$c];
		foreach($_POST['txtDL_NoDoc'.$c] as $key => $value){
			$txtDL_CategoryDocID=$_POST['txtDL_CategoryDocID'.$c][$key];
			$txtDL_TypeDocID=$_POST['txtDL_TypeDocID'.$c][$key];
			$txtDL_NoDoc=$_POST['txtDL_NoDoc'.$c][$key];
			$txtDL_RegDate=$_POST['txtDL_RegDate'.$c][$key];
			$txtDL_ExpDate=$_POST['txtDL_ExpDate'.$c][$key];
			$txtDL_Information1=$_POST['txtDL_Information1'.$c][$key];
			$txtDL_Information2=$_POST['txtDL_Information2'.$c][$key];
			$txtDL_Information3=str_replace("<br>", "\n",$_POST['txtDL_Information3'.$c][$key]);
			$txtDL_Instance=$_POST['txtDL_Instance'.$c][$key];
			$txtTDROLD_ID=$_POST['txtTDROLD_ID'.$c][$key];


			$txtRegDate=date('Y-m-d H:i:s', strtotime($txtDL_RegDate));
			$txtExpDate=date('Y-m-d H:i:s', strtotime($txtDL_ExpDate));
			if 	(strstr($txtExpDate, ' ', true)=="1970-01-01"){
				$txtExpDate=NULL;
			}

			$query = "UPDATE TD_RegistrationOfLegalDocument
					  SET TDROLD_DocumentCategoryID='$txtDL_CategoryDocID',
					  	  TDROLD_DocumentTypeID='$txtDL_TypeDocID',
					  	  TDROLD_DocumentNo='$txtDL_NoDoc',
					  	  TDROLD_DatePublication='$txtRegDate',
						  TDROLD_DateExpired='$txtExpDate',
						  TDROLD_DocumentInformation1ID='$txtDL_Information1',
						  TDROLD_DocumentInformation2ID='$txtDL_Information2',
						  TDROLD_DocumentInformation3='$txtDL_Information3',
						  TDROLD_Instance='$txtDL_Instance',
					  	  TDROLD_Update_Time=sysdate(),
					      TDROLD_Update_UserID='$mv_UserID'
					  WHERE TDROLD_ID='$txtTDROLD_ID'";
			$mysqli->query($query);
		}
	}

	// for($i=1;$i<$count;$i++){
	// 	$txtDL_CategoryDocID=$_POST['txtDL_CategoryDocID'.$i];
	// 	$txtDL_TypeDocID=$_POST['txtDL_TypeDocID'.$i];
	// 	$txtDL_NoDoc=$_POST['txtDL_NoDoc'.$i];
	// 	$txtDL_RegDate=$_POST['txtDL_RegDate'.$i];
	// 	$txtDL_ExpDate=$_POST['txtDL_ExpDate'.$i];
	// 	$txtDL_Information1=$_POST['txtDL_Information1'.$i];
	// 	$txtDL_Information2=$_POST['txtDL_Information2'.$i];
	// 	$txtDL_Information3=str_replace("<br>", "\n",$_POST['txtDL_Information3'.$i]);
	// 	$txtDL_Instance=$_POST['txtDL_Instance'.$i];
	// 	$txtTDROLD_ID=$_POST['txtTDROLD_ID'.$i];


	// 	$txtRegDate=date('Y-m-d H:i:s', strtotime($txtDL_RegDate));
	// 	$txtExpDate=date('Y-m-d H:i:s', strtotime($txtDL_ExpDate));
	// 	if 	(strstr($txtExpDate, ' ', true)=="1970-01-01"){
	// 		$txtExpDate=NULL;
	// 	}

	// 	$query = "UPDATE TD_RegistrationOfLegalDocument
	// 			  SET TDROLD_DocumentCategoryID='$txtDL_CategoryDocID',
	// 			  	  TDROLD_DocumentTypeID='$txtDL_TypeDocID',
	// 			  	  TDROLD_DocumentNo='$txtDL_NoDoc',
	// 			  	  TDROLD_DatePublication='$txtRegDate',
	// 				  TDROLD_DateExpired='$txtExpDate',
	// 				  TDROLD_DocumentInformation1ID='$txtDL_Information1',
	// 				  TDROLD_DocumentInformation2ID='$txtDL_Information2',
	// 				  TDROLD_DocumentInformation3='$txtDL_Information3',
	// 				  TDROLD_Instance='$txtDL_Instance',
	// 			  	  TDROLD_Update_Time=sysdate(),
	// 			      TDROLD_Update_UserID='$mv_UserID'
	// 			  WHERE TDROLD_ID='$txtTDROLD_ID'";
	// 	$mysqli->query($query);
	// }
	if ($_POST['optTHROLD_Status']){
		$A_TransactionCode=$_POST['txtA_TransactionCode'];
		$A_ApproverID=$mv_UserID;
		$A_Status=$_POST['optTHROLD_Status'];
		$THROLD_Reason=str_replace("<br>", "\n",$_POST['txtTHROLD_Reason']);

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

				// if ($_POST['txtDL_GroupDocID'] != '2') { $jenis = '1'; }
				// else if ($_POST['txtDL_GroupDocID'] == '2') { $jenis = '3'; }
				// else;
				$jenis = "7"; //Legal & Lisensi - Semua //Arief F - 24082018

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
							$query = "UPDATE TH_RegistrationOfLegalDocument
								SET THROLD_Status='accept', THROLD_Update_UserID='$A_ApproverID',
									THROLD_Update_Time=sysdate()
								WHERE THROLD_RegistrationCode='$A_TransactionCode'
								AND THROLD_Delete_Time IS NULL";
							if ($sql = mysql_query($query)) {
								mail_notif_registration_doc($A_TransactionCode, $h_arr['THROLD_UserID'], 3, 1 );
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
							$query = "UPDATE TH_RegistrationOfLegalDocument
								SET THROLD_Status='accept', THROLD_Update_UserID='$A_ApproverID',
									THROLD_Update_Time=sysdate()
								WHERE THROLD_RegistrationCode='$A_TransactionCode'
								AND THROLD_Delete_Time IS NULL";
							if ($sql = mysql_query($query)) {
								mail_notif_registration_doc($A_TransactionCode, $h_arr['THROLD_UserID'], 3, 1 );
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
						mail_notif_registration_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 3 );
						mail_notif_registration_doc($A_TransactionCode, "cust0002", 3 );
					}
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
				}*/
			}
			else {
				$jumlahRow=$_POST['rowTerakhir'];
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
							  AND DL_DocGroupID='non_grl'
							  AND DL_Delete_Time is NULL";
					$avLoc = mysql_num_rows(mysql_query($query));
					$array_company_id['banyak_ruang_tersedia'][$n] = $avLoc;

					if(!$avLoc || $avLoc<$array_company_id['banyak'][$n]){
						$array_company_id['ruang_tersedia'][$n] = "0"; //tidak
						$lokasi_dokumen_kosong++;
					}else{
						$array_company_id['ruang_tersedia'][$n] = "1"; //ya
					}
				}

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
							// mail_notif_registration_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 3, 1 );
							// mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
							// mail_notif_registration_doc($A_TransactionCode, $sql1['A_ApproverID'], 3, 1);
						//}
					}

					$query = "UPDATE TH_RegistrationOfLegalDocument
								SET THROLD_Status='accept', THROLD_Update_UserID='$A_ApproverID', THROLD_Update_Time=sysdate()
								WHERE THROLD_RegistrationCode='$A_TransactionCode'
								AND THROLD_Delete_Time IS NULL";
					$sql = mysql_query($query);
					// ACTION UNTUK GENERATE NO DOKUMEN
					$regyear=date("y");
					$regmonth=date("m");

					// for($i=1;$i<$jumlahRow;$i++){
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
									WHERE DocumentGroup_ID ='$_POST[txtDL_GroupDocID]'";
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

						foreach($_POST['txtDL_NoDoc'.$c] as $key => $value){
							$query = "SELECT *
								  FROM L_DocumentLocation
								  WHERE DL_Status='1'
								  AND DL_CompanyID='$Core_CompanyID'
								  AND DL_DocGroupID='non_grl'
								  AND DL_Delete_Time is NULL
								  AND DL_ID=(SELECT MIN(DL_ID)
											 FROM L_DocumentLocation
										     WHERE DL_Status='1'
										     AND DL_CompanyID='$Core_CompanyID'
										     AND DL_DocGroupID='non_grl'
										     AND DL_Delete_Time is NULL)";
							$arr = mysql_fetch_array(mysql_query($query));
							$DLIU_LocationCode=$arr[DL_Code];

							$txtDL_CategoryDocID=$_POST['txtDL_CategoryDocID'.$c][$key];
							$txtDL_TypeDocID=$_POST['txtDL_TypeDocID'.$c][$key];
							$txtDL_NoDoc=$_POST['txtDL_NoDoc'.$c][$key];
							$txtDL_RegDate=date('Y-m-d H:i:s', strtotime($_POST['txtDL_RegDate'.$c][$key]));
							$txtDL_ExpDate=date('Y-m-d H:i:s', strtotime($_POST['txtDL_ExpDate'.$c][$key]));
							$txtDL_Information1=$_POST['txtDL_Information1'.$c][$key];
							$txtDL_Information2=$_POST['txtDL_Information2'.$c][$key];
							$txtDL_Information3=str_replace("<br>", "\n",$_POST['txtDL_Information3'.$c][$key]);
							$txtDL_Instance=$_POST['txtDL_Instance'.$c][$key];

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
									WHERE DL_Code='$DLIU_LocationCode';";

							// Memindahkan Pendaftaran Dokumen ke M_DocumentLegal
							$sql3= "INSERT INTO M_DocumentLegal
									VALUES (NULL,
											'$CD_Code',
											'$_POST[txtDL_RegUserID]',
											'$_POST[txtDL_RegTime]',
											'$Core_CompanyID',
											'$_POST[txtDL_GroupDocID]',
											'$txtDL_CategoryDocID',
											'$txtDL_TypeDocID',
											'$txtDL_NoDoc',
											'$txtDL_RegDate',
											'$txtDL_ExpDate',
											'$txtDL_Information1',
											'$txtDL_Information2',
											'$txtDL_Information3',
											'$txtDL_Instance',
											'$DLIU_LocationCode','1', NULL, NULL,
											'$mv_UserID', sysdate(),'$mv_UserID',
											sysdate(),NULL,NULL);";
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

			$query = "UPDATE TH_RegistrationOfLegalDocument
						SET THROLD_Status='reject', THROLD_Reason='$THROLD_Reason',
							THROLD_Update_Time=sysdate(), THROLD_Update_UserID='$A_ApproverID'
						WHERE THROLD_RegistrationCode='$A_TransactionCode'";

			$query1 = "UPDATE M_Approval
						SET A_Update_Time=sysdate(), A_Update_UserID='$A_ApproverID',
							A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
							A_Status='$A_Status'
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_Step>'$step'";
			if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
				$h_query="SELECT *
							  FROM TH_RegistrationOfLegalDocument
							  WHERE THROLD_RegistrationCode='$A_TransactionCode'
							  AND THROLD_Delete_Time IS NULL";
				$h_arr=mysql_fetch_array(mysql_query($h_query));
				mail_notif_registration_doc($A_TransactionCode, $h_arr['THROLD_UserID'], 4 );

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
