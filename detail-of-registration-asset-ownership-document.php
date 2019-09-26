<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 20 Agustus 2018																					=
= Update Terakhir	: -           																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
setcookie('Referer', $_SERVER["REQUEST_URI"], time() + (86400 * 30), "/"); // 86400 = 1 day
?>
<title>Custodian System | Detail Registrasi Dokumen Kepemilikan Aset</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdocao.php");
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
	if (typeof  document.DAO_detail.optTHROAOD_Status != 'undifined') {
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
	}
	if (typeof  document.DAO_detail.optDAO_Employee_NIK != 'undifined') {
		for (i = 1; i < jRow; i++){
			var optDAO_Employee_NIK = document.getElementById('optDAO_Employee_NIK' + i).selectedIndex;
			var optDAO_MK_ID = document.getElementById('optDAO_MK_ID' + i).selectedIndex;
			var txtDAO_Jenis = document.getElementById('txtDAO_Jenis' + i).value;
			var txtDAO_Type = document.getElementById('txtDAO_Type' + i).value;
			var txtDAO_NoPolisi = document.getElementById('txtDAO_NoPolisi' + i).value;
			var txtDAO_NoRangka = document.getElementById('txtDAO_NoRangka' + i).value;
			var txtDAO_NoMesin = document.getElementById('txtDAO_NoMesin' + i).value;
			var txtDAO_NoBPKB = document.getElementById('txtDAO_NoBPKB' + i).value;
			var txtDAO_STNK_StartDate = document.getElementById('txtDAO_STNK_StartDate' + i).value;
			var txtDAO_STNK_ExpiredDate = document.getElementById('txtDAO_STNK_ExpiredDate' + i).value;
			var txtDAO_Pajak_StartDate = document.getElementById('txtDAO_Pajak_StartDate' + i).value;
			var txtDAO_Pajak_ExpiredDate = document.getElementById('txtDAO_Pajak_ExpiredDate' + i).value;
			var txtDAO_Lokasi_PT = document.getElementById('txtDAO_Lokasi_PT' + i).value;
			var optDAO_Region = document.getElementById('optDAO_Region' + i).selectedIndex;
			var txtDAO_Keterangan = document.getElementById('txtDAO_Keterangan' + i).value;
			var Date1 = new Date(txtDAO_STNK_StartDate);
			var Date2 = new Date(txtDAO_STNK_ExpiredDate);
			var Date3 = new Date(txtDAO_Pajak_StartDate);
			var Date4 = new Date(txtDAO_Pajak_ExpiredDate);

			if(optDAO_Employee_NIK == 0) {
				alert("Nama Pemilik Pada Baris ke-" + i + " Belum Dipilih!");
				return false
			}
			if(optDAO_MK_ID == 0) {
				alert("Merk Kendaraan Pada Baris ke-" + i + " Belum Dipilih!");
				return false
			}
			if (txtDAO_Jenis.replace(" ", "") == "")  {
				alert("Jenis Kendaraan pada baris ke-" + i + " Belum Terisi!");
				return false
			}
			if (txtDAO_Type.replace(" ", "") == "")  {
				alert("Tipe Kendaraan pada baris ke-" + i + " Belum Terisi!");
				return false
			}
			if (txtDAO_NoPolisi.replace(" ", "") == "")  {
				alert("Nomor Polisi pada baris ke-" + i + " Belum Terisi!");
				return false
			}
			if (txtDAO_NoRangka.replace(" ", "") == "")  {
				alert("Nomor Rangka pada baris ke-" + i + " Belum Terisi!");
				return false
			}
			if (txtDAO_NoMesin.replace(" ", "") == "")  {
				alert("Nomor Mesin pada baris ke-" + i + " Belum Terisi!");
				return false
			}
			if (txtDAO_NoBPKB.replace(" ", "") == "")  {
				alert("Nomor BPKB pada baris ke-" + i + " Belum Terisi!");
				return false
			}

			if (txtDAO_Lokasi_PT.replace(" ", "") == "")  {
				alert("Lokasi pada baris ke-" + i + " Belum Terisi!");
				return false;
			}
			if(optDAO_Region == 0) {
				alert("Region pada baris ke-" + i + " Belum Dipilih!");
				return false;
			}
			if (txtDAO_Keterangan.replace(" ", "") == "")  {
				alert("Keterangan pada baris ke-" + i + " Belum Terisi!");
				return false;
			}
		}
	}
	return true
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
		  	 FROM TH_RegistrationOfAssetOwnershipDocument throld, M_Approval dra
			 WHERE throld.THROAOD_Delete_Time is NULL
			 AND dra.A_ApproverID='$mv_UserID'
			 AND dra.A_Status='2'
			 AND dra.A_TransactionCode=throld.THROAOD_RegistrationCode
			 AND throld.THROAOD_ID='$DocID'";
$approver=mysql_num_rows(mysql_query($cApp_query));

$appQuery=(($act=='approve')&&($approver=="1"))?"AND dra.A_ApproverID='$mv_UserID'":"AND dra.A_Status='2'";

$query="SELECT DISTINCT throld.THROAOD_ID,
						throld.THROAOD_RegistrationCode,
						throld.THROAOD_RegistrationDate,
						u.User_ID,
						u.User_FullName,
						c.Company_Code,
						c.Company_Name,
						throld.THROAOD_Status,
						throld.THROAOD_Information,
						dg.DocumentGroup_Name,
						dg.DocumentGroup_ID,
						throld.THROAOD_Reason,
						c.Company_ID,
						(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dra.A_ApproverID) waitingApproval
		FROM TH_RegistrationOfAssetOwnershipDocument throld
		LEFT JOIN M_User u
			ON throld.THROAOD_UserID=u.User_ID
		LEFT JOIN M_Company c
			ON throld.THROAOD_CompanyID=c.Company_ID
		LEFT JOIN M_Approval dra
			ON dra.A_TransactionCode=throld.THROAOD_RegistrationCode
			$appQuery
		LEFT JOIN M_DocumentGroup dg
			ON throld.THROAOD_DocumentGroupID=dg.DocumentGroup_ID
		WHERE throld.THROAOD_Delete_Time is NULL
		AND throld.THROAOD_ID='$DocID'
		ORDER BY waitingApproval DESC";
$arr = mysql_fetch_array(mysql_query($query));

$fregdate=date("j M Y", strtotime($arr['THROAOD_RegistrationDate']));
$regUser=$arr['User_ID'];
$DocumentGroup_ID=$arr["DocumentGroup_ID"];
$Company_ID = $arr['Company_ID'];
$Company_Code = $arr['Company_Code'];
$Company_Name = $arr['Company_Name'];

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
<form name='DAO_detail' id='DAO_detail' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";
if(($act=='approve')&&($approver=="1"))
	$MainContent .="<th colspan=3>Persetujuan Pendaftaran Dokumen</th>";
else
	$MainContent .="<th colspan=3>Pendaftaran Dokumen</th>";

if((($arr[THROAOD_Status]=="accept")||($arr[THROAOD_Status]=="waiting")) && (($custodian==1) || ($regUser==$mv_UserID))){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='67%'>
			<input name='txtTHROAOD_ID' type='hidden' value='$arr[THROAOD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROAOD_RegistrationCode]'/>
			$arr[THROAOD_RegistrationCode]
		</td>
		<td width='3%'>
			<a href='print-registration-of-asset-ownership-document.php?id=$arr[THROAOD_RegistrationCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else{
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='70%'colspan='2'>
			<input name='txtTHROAOD_ID' type='hidden' value='$arr[THROAOD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THROAOD_RegistrationCode]'/>
			$arr[THROAOD_RegistrationCode]
		</td>
	</tr>";
}

$MainContent .="
<tr>
	<td>Tanggal Pendaftaran</td>
	<td colspan='2'><input name='txtDAO_RegTime' type='hidden' value='$arr[THROAOD_RegistrationDate]'>$fregdate</td>
</tr>
<tr>
	<td>Nama Pendaftar</td>
	<td colspan='2'>
		<input name='txtDAO_RegUserID' type='hidden' value='$regUser'>$arr[User_FullName]
		<input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>
	</td>
</tr>";
// <tr>
// 	<td>Nama Perusahaan</td>
// 	<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
// </tr>
// <tr>
// 	<td>Grup Dokumen</td>
// 	<td colspan='2'><input name='txtDAO_GroupDocID' id='txtDAO_GroupDocID' type='hidden' value='$DocumentGroup_ID'>$arr[DocumentGroup_Name]</td>
// </tr>
$MainContent .="
<tr>
	<td>Keterangan</td>";
// echo $act;
if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1)))
	$MainContent .="<td colspan='2'><textarea name='txtTHROAOD_Information' id='txtTHROAOD_Information' cols='50' rows='2'>$arr[THROAOD_Information]</textarea></td>";
else
	$MainContent .="<td colspan='2'><input type='hidden' name='txtTHROAOD_Information' value='$arr[THROAOD_Information]'>$arr[THROAOD_Information]</td>";
$MainContent .="</tr>";

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
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Pendaftaran</td>";
	if($arr[THROAOD_Status]=="waiting") {
		$MainContent .="
		<td colspan='2'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr[THROAOD_Status]=="accept") {
		$MainContent .="
			<td colspan='2'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THROAOD_Reason]</td>
		</tr>";
	}else if($arr[THROAOD_Status]=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THROAOD_Reason]</td>
		</tr>
		";
	}else {
		$MainContent .="
		<td colspan='2'>Draft</td></tr>";
	}
}

$MainContent .="</table>";

	// DETAIL DOKUMEN KEPEMILIKAN ASET
$MainContent .="
<div class='detail-title'>Daftar Dokumen</div>
<table width='100%' id='mytable' class='stripeMe'>
<tr>
	<th rowspan='2'>No.</th>
	<th rowspan='2'>Nama Pemilik</th>
	<th rowspan='2'>Merk Kendaraan</th>
	<th rowspan='2'>Type</th>
	<th rowspan='2'>Jenis</th>
	<th rowspan='2'>No. Polisi / No. Seri Unit</th>
	<th rowspan='2'>No. Rangka</th>
	<th rowspan='2'>No. Mesin</th>
	<th rowspan='2'>No. BPKB / No. Invoice</th>
	<th colspan='2'>STNK</th>
	<th colspan='2'>Pajak Kendaraan</th>
	<th rowspan='2'>Lokasi (PT)</th>
	<th rowspan='2'>Region</th>
	<th rowspan='2'>Keterangan</th>
</tr>
<tr>
	<th>Start Date</th>
	<th>Expired Date</th>
	<th>Start Date</th>
	<th>Expired Date</th>
</tr>";

$query = "SELECT TDROAOD_ID, TDROAOD_Employee_NIK,
			CASE WHEN TDROAOD_Employee_NIK LIKE 'CO@%'
			  THEN
				(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(TDROAOD_Employee_NIK, 'CO@', ''))
			  ELSE
				(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=TDROAOD_Employee_NIK)
			END nama_pemilik,
			TDROAOD_MK_ID, TDROAOD_Type, TDROAOD_Jenis,
			TDROAOD_NoPolisi, TDROAOD_NoRangka, TDROAOD_NoMesin, TDROAOD_NoBPKB,
			TDROAOD_STNK_StartDate, TDROAOD_STNK_ExpiredDate, TDROAOD_Pajak_StartDate, TDROAOD_Pajak_ExpiredDate,
			TDROAOD_Lokasi_PT, TDROAOD_Region, TDROAOD_Keterangan
 		  FROM TD_RegistrationOfAssetOwnershipDocument
		  WHERE TDROAOD_THROAOD_ID='$DocID'
		  AND TDROAOD_Delete_Time IS NULL";
$sql = mysql_query($query);
$no=1;

while ($arr = mysql_fetch_array($sql)) {
	if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1))) {
		// echo 1;
		$Employee_NIK=$arr["TDROAOD_Employee_NIK"];
		$MK_ID=$arr["TDROAOD_MK_ID"];
		$stnk_sdate=(strpos($arr['TDROAOD_STNK_StartDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_STNK_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_STNK_StartDate']));
		$stnk_exdate=(strpos($arr['TDROAOD_STNK_ExpiredDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_STNK_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_STNK_ExpiredDate']));

		$pajak_sdate=(strpos($arr['TDROAOD_Pajak_StartDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_Pajak_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_Pajak_StartDate']));
		$pajak_exdate=(strpos($arr['TDROAOD_Pajak_ExpiredDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_Pajak_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_Pajak_ExpiredDate']));

		$MainContent .="
		<tr>
			<td align='center'>
				<input type='hidden' name='txtDAO_ID$no' id='txtDAO_ID$no' value='$arr[TDROAOD_ID]'/>$no
			</td>
			<td class='center'>
				<select name='optDAO_Employee_NIK$no' id='optDAO_Employee_NIK$no'>
					<option value='0'>--- Pilih Nama Pemilik ---</option>";

			$query5="SELECT Employee_NIK, Employee_FullName, Employee_CompanyCode AS Company_Code
				FROM db_master.M_Employee
				WHERE Employee_ResignDate IS NULL
				AND Employee_GradeCode IN ('0000000005', '06', '0000000003', '05', '04', '0000000004')";
			$sql5 = mysql_query($query5);

			while ($field5=mysql_fetch_array($sql5)) {
				$selected=($field5["Employee_NIK"]=="$Employee_NIK")?"selected='selected'":"";
				$MainContent .="
					<option value='$field5[Employee_NIK]' $selected>$field5[Employee_FullName] - $field5[Company_Code]</option>";
			}
			$query_comp = "SELECT CONCAT('CO@',Company_Code) AS id, Company_Name AS name, Company_Code
					  FROM M_Company
					  WHERE Company_Delete_Time is NULL
					  ORDER BY Company_Name ASC";
	  	  	$sql_comp = mysql_query($query_comp);
			while($field_comp=mysql_fetch_array($sql_comp)){
				if(strpos($arr['TDROAOD_Employee_NIK'], 'CO@') !== false){
					$selected=($field_comp["id"] == $arr['TDROAOD_Employee_NIK']) ? "selected='selected'":"";
				}
				$MainContent .="
					<option value='$field_comp[id]' $selected>$field_comp[name] - $field_comp[Company_Code]</option>";
			}
		$MainContent .="
				</select>
			</td>
			<td>
				<select name='optDAO_MK_ID$no' id='optDAO_MK_ID$no'>
					<option value='0'>--- Pilih Merk Kendaraan ---</option>";
			$query6="SELECT *
					 FROM db_master.M_MerkKendaraan
					 WHERE MK_DeleteTime is NULL";
			$sql6 = mysql_query($query6);

			while ($field6=mysql_fetch_array($sql6)) {
				$selected1=($field6["MK_ID"]==$MK_ID)?"selected='selected'":"";
				$MainContent .="
				<option value='$field6[MK_ID]' $selected1>$field6[MK_Name]</option>";
			}

		$MainContent .="
				</select>
			</td>
			<td class='center'>
				<input name='txtDAO_Type$no' id='txtDAO_Type$no' type='text' value='$arr[TDROAOD_Type]'>
			</td>
			<td class='center'>
				<input name='txtDAO_Jenis$no' id='txtDAO_Jenis$no' type='text' value='$arr[TDROAOD_Jenis]'>
			</td>
			<td class='center'>
				<input name='txtDAO_NoPolisi$no' id='txtDAO_NoPolisi$no' type='text' value='$arr[TDROAOD_NoPolisi]'>
			</td>
			<td class='center'>
				<input name='txtDAO_NoRangka$no' id='txtDAO_NoRangka$no' type='text' value='$arr[TDROAOD_NoRangka]'>
			</td>
			<td class='center'>
				<input name='txtDAO_NoMesin$no' id='txtDAO_NoMesin$no' type='text' value='$arr[TDROAOD_NoMesin]'>
			</td>
			<td class='center'>
				<input name='txtDAO_NoBPKB$no' id='txtDAO_NoBPKB$no' type='text' value='$arr[TDROAOD_NoBPKB]'>
			</td>
			<td class='center'>
				<input name='txtDAO_STNK_StartDate$no' id='txtDAO_STNK_StartDate$no' size='7' type='text' value='$stnk_sdate' onclick=\"javascript:NewCssCal('txtDAO_STNK_StartDate$no', 'MMddyyyy');\">
			<!-- </td>
			<td> -->
				<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDAO_STNK_StartDate$no').value=''\">
			</td>
			<td class='center'>
				<input name='txtDAO_STNK_ExpiredDate$no' id='txtDAO_STNK_ExpiredDate$no' size='7' type='text' value='$stnk_exdate' onclick=\"javascript:NewCssCal('txtDAO_STNK_ExpiredDate$no', 'MMddyyyy');\">
			<!-- </td>
			<td> -->
				<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDAO_STNK_ExpiredDate$no').value=''\">
			</td>
			<td class='center'>
				<input name='txtDAO_Pajak_StartDate$no' id='txtDAO_Pajak_StartDate$no' size='7' type='text' value='$pajak_sdate' onclick=\"javascript:NewCssCal('txtDAO_Pajak_StartDate$no', 'MMddyyyy');\">
			<!-- </td>
			<td> -->
				<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDAO_Pajak_StartDate$no').value=''\">
			</td>
			<td class='center'>
				<input name='txtDAO_Pajak_ExpiredDate$no' id='txtDAO_Pajak_ExpiredDate$no' size='7' type='text' value='$pajak_exdate' onclick=\"javascript:NewCssCal('txtDAO_Pajak_ExpiredDate$no', 'MMddyyyy');\">
			<!-- </td>
			<td> -->
				<img src='images/icon_close.gif' onclick=\"document.getElementById('txtDAO_Pajak_ExpiredDate$no').value=''\">
			</td>
			<td class='center'>
				<input name='txtDAO_Lokasi_PT$no' id='txtDAO_Lokasi_PT$no' type='text' value='$arr[TDROAOD_Lokasi_PT]'>
			</td>
			<td class='center'>
				<select name='optDAO_Region$no' id='optDAO_Region$no'>
					<option value=''>--- Pilih Region ---</option>
					<option value='KALTIM' ".($arr['TDROAOD_Region'] == "KALTIM" ? "selected" : "").">Kaltim</option>
					<option value='KALTENG' ".($arr['TDROAOD_Region'] == "KALTENG" ? "selected" : "").">Kalteng</option>
					<option value='KALBAR' ".($arr['TDROAOD_Region'] == "KALBAR" ? "selected" : "").">Kalbar</option>
					<option value='JAMBI' ".($arr['TDROAOD_Region'] == "JAMBI" ? "selected" : "").">Jambi</option>
					<option value='HO' ".($arr['TDROAOD_Region'] == "HO" ? "selected" : "").">Head Office</option>
				</select>
			</td>
			<td class='center'>
				<textarea name='txtDAO_Keterangan$no' id='txtDAO_Keterangan$no'>$arr[TDROAOD_Keterangan]</textarea>
			</td>
		</tr>";
	}else {
		// $stnk_sdate=date("j M Y", strtotime($arr['TDROAOD_STNK_StartDate']));
		$stnk_sdate=(strpos($arr['TDROAOD_STNK_StartDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_STNK_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_STNK_StartDate']));
		$stnk_exdate=(strpos($arr['TDROAOD_STNK_ExpiredDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_STNK_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_STNK_ExpiredDate']));

		// $pajak_sdate=date("j M Y", strtotime($arr['TDROAOD_Pajak_StartDate']));
		$pajak_sdate=(strpos($arr['TDROAOD_Pajak_StartDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_Pajak_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_Pajak_StartDate']));
		$pajak_exdate=(strpos($arr['TDROAOD_Pajak_ExpiredDate'], '0000-00-00') !== false || strpos($arr['TDROAOD_Pajak_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['TDROAOD_Pajak_ExpiredDate']));

		$query8="SELECT MK_Name
			FROM db_master.M_MerkKendaraan
			WHERE MK_ID='$arr[TDROAOD_MK_ID]'
				#Employee_ResignDate IS NULL";
		$sql8 = mysql_query($query8);
		$merk_kendaraan = "-";
		if(mysql_num_rows($sql8) > 0){
			$data8 = mysql_fetch_array($sql8);
			$merk_kendaraan = $data8['MK_Name'];
		}

		$MainContent .="
		<tr>
			<td class='center'>
				<input type='hidden' name='txtDAO_ID$no' value='$arr[TDROAOD_ID]'/>$no
			</td>
			<td class='center'><input name='optDAO_Employee_NIK$no' type='hidden' value='$arr[TDROAOD_Employee_NIK]'>$arr[nama_pemilik]</td>
			<td class='center'><input name='optDAO_MK_ID$no' type='hidden' value='$arr[TDROAOD_MK_ID]'>$merk_kendaraan</td>
			<td class='center'><input name='txtDAO_Type$no' type='hidden' value='$arr[TDROAOD_Type]'>$arr[TDROAOD_Type]</td>
			<td class='center'><input name='txtDAO_Jenis$no' type='hidden' value='$arr[TDROAOD_Jenis]'>$arr[TDROAOD_Jenis]</td>
			<td class='center'><input name='txtDAO_NoPolisi$no' type='hidden' value='$arr[TDROAOD_NoPolisi]'>$arr[TDROAOD_NoPolisi]</td>
			<td class='center'><input name='txtDAO_NoRangka$no' type='hidden' value='$arr[TDROAOD_NoRangka]'>$arr[TDROAOD_NoRangka]</td>
			<td class='center'><input name='txtDAO_NoMesin$no' type='hidden' value='$arr[TDROAOD_NoMesin]'>$arr[TDROAOD_NoMesin]</td>
			<td class='center'><input name='txtDAO_NoBPKB$no' type='hidden' value='$arr[TDROAOD_NoBPKB]'>$arr[TDROAOD_NoBPKB]</td>
			<td class='center'><input name='txtDAO_STNK_StartDate$no' type='hidden' value='$arr[TDROAOD_STNK_StartDate]'>$stnk_sdate</td>
			<td class='center'><input name='txtDAO_STNK_ExpiredDate$no' type='hidden' value='$arr[TDROAOD_STNK_ExpiredDate]'>$stnk_exdate</td>
			<td class='center'><input name='txtDAO_Pajak_StartDate$no' type='hidden' value='$arr[TDROAOD_Pajak_StartDate]'>$pajak_sdate</td>
			<td class='center'><input name='txtDAO_Pajak_ExpiredDate$no' type='hidden' value='$arr[TDROAOD_Pajak_ExpiredDate]'>$pajak_exdate</td>
			<td class='center'><input name='txtDAO_Lokasi_PT$no' type='hidden' value='$arr[TDROAOD_Lokasi_PT]'>$arr[TDROAOD_Lokasi_PT]</td>
			<td class='center'><input name='optDAO_Region$no' type='hidden' value='$arr[TDROAOD_Region]'>$arr[TDROAOD_Region]</td>
			<td class='center'><input name='txtDAO_Keterangan$no' type='hidden' value='$arr[TDROAOD_Keterangan]'>$arr[TDROAOD_Keterangan]</td>
		</tr>";
	}

	$no=$no+1;
	$MainContent .="
		<input type='hidden' name='jRow' id='jRow' value='$no'/>";
}
$MainContent .="</table>";

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
	$info = str_replace("<br>", "\n", $_POST['txtTHROAOD_Information']);
	$query="UPDATE TH_RegistrationOfAssetOwnershipDocument SET THROAOD_Information='$info' WHERE THROAOD_RegistrationCode='$_POST[txtA_TransactionCode]'";
	$mysqli->query($query);

	$count=$_POST[jRow];

	for($i=1;$i<$count;$i++){
		$optDAO_Employee_NIK=$_POST['optDAO_Employee_NIK'.$i];
		$optDAO_MK_ID=$_POST['optDAO_MK_ID'.$i];
		$txtDAO_Type=$_POST['txtDAO_Type'.$i];
		$txtDAO_Jenis=$_POST['txtDAO_Jenis'.$i];
		$txtDAO_NoPolisi=$_POST['txtDAO_NoPolisi'.$i];
		$txtDAO_NoRangka=$_POST['txtDAO_NoRangka'.$i];
		$txtDAO_NoMesin=$_POST['txtDAO_NoMesin'.$i];
		$txtDAO_NoBPKB=$_POST['txtDAO_NoBPKB'.$i];
		$txtDAO_STNK_StartDate=$_POST['txtDAO_STNK_StartDate'.$i];
		$txtDAO_STNK_ExpiredDate=$_POST['txtDAO_STNK_ExpiredDate'.$i];
		$txtDAO_Pajak_StartDate=$_POST['txtDAO_Pajak_StartDate'.$i];
		$txtDAO_Pajak_ExpiredDate=$_POST['txtDAO_Pajak_ExpiredDate'.$i];
		$txtDAO_Lokasi_PT=$_POST['txtDAO_Lokasi_PT'.$i];
		$optDAO_Region=$_POST['optDAO_Region'.$i];
		$txtDAO_Keterangan=str_replace("<br>", "\n",$_POST['txtDAO_Keterangan'.$i]);

		$txtDAO_ID=$_POST['txtDAO_ID'.$i];

		$txtDAO_STNK_StartDate=date('Y-m-d H:i:s', strtotime($txtDAO_STNK_StartDate));
		$txtDAO_STNK_ExpiredDate=date('Y-m-d H:i:s', strtotime($txtDAO_STNK_ExpiredDate));
		$txtDAO_Pajak_StartDate=date('Y-m-d H:i:s', strtotime($txtDAO_Pajak_StartDate));
		$txtDAO_Pajak_ExpiredDate=date('Y-m-d H:i:s', strtotime($txtDAO_Pajak_ExpiredDate));

		$query = "UPDATE TD_RegistrationOfAssetOwnershipDocument
				  SET TDROAOD_Employee_NIK='$optDAO_Employee_NIK',
				  	  TDROAOD_MK_ID='$optDAO_MK_ID',
				  	  TDROAOD_Type='$txtDAO_Type',
				  	  TDROAOD_Jenis='$txtDAO_Jenis',
					  TDROAOD_NoPolisi='$txtDAO_NoPolisi',
					  TDROAOD_NoRangka='$txtDAO_NoRangka',
					  TDROAOD_NoMesin='$txtDAO_NoMesin',
					  TDROAOD_NoBPKB='$txtDAO_NoBPKB',
					  TDROAOD_STNK_StartDate='$txtDAO_STNK_StartDate',
					  TDROAOD_STNK_ExpiredDate='$txtDAO_STNK_ExpiredDate',
					  TDROAOD_Pajak_StartDate='$txtDAO_Pajak_StartDate',
					  TDROAOD_Pajak_ExpiredDate='$txtDAO_Pajak_ExpiredDate',
					  TDROAOD_Lokasi_PT='$txtDAO_Lokasi_PT',
					  TDROAOD_Region='$optDAO_Region',
				  	  TDROAOD_Update_Time=sysdate(),
				      TDROAOD_Update_UserID='$mv_UserID'
				  WHERE TDROAOD_ID='$txtDAO_ID'";
		$mysqli->query($query);
	}
	if ($_POST['optTHROAOD_Status']){
		$A_TransactionCode=$_POST['txtA_TransactionCode'];
		$A_ApproverID=$mv_UserID;
		$A_Status=$_POST['optTHROAOD_Status'];
		$THROAOD_Reason=str_replace("<br>", "\n",$_POST['txtTHROAOD_Reason']);

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

				$jenis = "13"; //Kepemilikan Aset - Semua Tipe Dokumen

				// $qComp = "SELECT Company_Area FROM M_Company WHERE Company_ID = '{$_POST['txtCompany_ID']}'";
				// $aComp = mysql_fetch_array(mysql_query($qComp));
				for ($i=$nStep; $i<=$jStep; $i++) {
					$j = $i + 1;
					$query = "SELECT rads.RADS_StatusID, ma.A_ApproverID
					FROM M_Approval ma
					JOIN M_Role_ApproverDocStepStatus rads
						ON ma.A_Step = rads.RADS_StepID
					LEFT JOIN M_Role_Approver ra
						ON rads.RADS_RA_ID = ra.RA_ID
					WHERE ma.A_Step = '{$i}'
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

					} else;
				}
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";

			}
			else {
				$jumlahRow=$_POST['jRow'];

				$array_company_id = array();
				for ($i = 1; $i < $jumlahRow; $i++){
					$pemilik_aset = $_POST['optDAO_Employee_NIK'.$i];
					if(strpos($pemilik_aset, 'CO@') !== false){
						$get_company_code = explode('CO@', $pemilik_aset);
						$company_code = $get_company_code[1];
						$query_pa="SELECT Company_ID, Company_Name, Company_Code
							FROM M_Company
							WHERE Company_Code='$company_code'";
					}else{
						$query_pa="SELECT c.Company_ID, c.Company_Name, c.Company_Code
							FROM db_master.M_Employee e
							INNER JOIN M_Company c
								ON e.Employee_CompanyCode=c.Company_Code
							WHERE e.Employee_NIK='$pemilik_aset'";
					}
					$sql_pa = mysql_query($query_pa);
					$dpa = mysql_fetch_array($sql_pa);

					if( !in_array($dpa['Company_ID'], $array_company_id) ){
						$array_company_id['company_id'][] = $dpa['Company_ID'];
						$array_company_id['company_name'][] = $dpa['Company_Name']." - ".$dpa['Company_Code'];
						$array_company_id['banyak'][] = 1;
					}else{
						$index = array_search($dpa['Company_ID'], $array_company_id['company_id']);
						$array_company_id['banyak'][$index] = $array_company_id['banyak'][$index]+1;
					}
					$array_company_id['ruang_tersedia'][] = "";
					$array_company_id['banyak_ruang_tersedia'][] = 0;
				}
				$lokasi_dokumen_kosong = 0;

				for($n = 0; $n < count($array_company_id['company_id']); $n++){
					$company_id = $array_company_id['company_id'][$n];
					// ACTION UNTUK MENENTUKAN JUMLAH LOKASI DOKUMEN YANG TERSEDIA
					$query = "SELECT *
							  FROM L_DocumentLocation
							  WHERE DL_Status='1'
							  AND DL_CompanyID='$company_id'
							  AND DL_DocGroupID='kea'
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

				// if((!$avLoc)||($avLoc<$jumlahRow)){
				if($lokasi_dokumen_kosong > 0){
					// print_r($array_company_id);
					$pesan = "";
					for($z = 0; $z < count($array_company_id['company_id']); $z++){
						if($array_company_id['ruang_tersedia'][$z] == 0){
							$pesan .= "Lokasi untuk Dokumen ".$array_company_id['company_name'][$z]." Tidak Tersedia. Lokasi yang Tersedia : ".$array_company_id['banyak_ruang_tersedia'][$z]." ";
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
							mail_notif_registration_doc($A_TransactionCode, $_POST['txtDAO_RegUserID'], 3, 1 );
							mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
							mail_notif_registration_doc($A_TransactionCode, $sql1['A_ApproverID'], 3, 1);
						//}
					}

					$query = "UPDATE TH_RegistrationOfAssetOwnershipDocument
								SET THROAOD_Status='accept', THROAOD_Update_UserID='$A_ApproverID', THROAOD_Update_Time=sysdate()
								WHERE THROAOD_RegistrationCode='$A_TransactionCode'
								AND THROAOD_Delete_Time IS NULL";
					$sql = mysql_query($query);

					for($i=1;$i<$jumlahRow;$i++){
						$k = $i-1;
						$company_id = $array_company_id['company_id'][$k];

						// ACTION UNTUK GENERATE NO DOKUMEN
						$regyear=date("y");
						$regmonth=date("m");

						// Cari Kode Perusahaan
						$query = "SELECT *
									FROM M_Company
									WHERE Company_ID='$company_id'";
						$field = mysql_fetch_array(mysql_query($query));
						$Company_Code=$field['Company_Code'];

						// Cari Kode Dokumen Grup
						$query = "SELECT *
									FROM M_DocumentGroup
									WHERE DocumentGroup_ID ='4'";
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

						$query = "SELECT *
							  FROM L_DocumentLocation
							  WHERE DL_Status='1'
							  AND DL_CompanyID='$company_id'
							  AND DL_DocGroupID='kea'
							  AND DL_Delete_Time is NULL
							  AND DL_ID=(SELECT MIN(DL_ID)
										 FROM L_DocumentLocation
									     WHERE DL_Status='1'
									     AND DL_CompanyID='$company_id'
									     AND DL_DocGroupID='kea'
									     AND DL_Delete_Time is NULL)";
						$arr = mysql_fetch_array(mysql_query($query));
						$DLIU_LocationCode=$arr['DL_Code'];

						$optDAO_Employee_NIK=$_POST['optDAO_Employee_NIK'.$i];
						$optDAO_MK_ID=$_POST['optDAO_MK_ID'.$i];
						$txtDAO_Jenis=$_POST['txtDAO_Jenis'.$i];
						$txtDAO_Type=$_POST['txtDAO_Type'.$i];
						$txtDAO_NoPolisi=$_POST['txtDAO_NoPolisi'.$i];
						$txtDAO_NoRangka=$_POST['txtDAO_NoRangka'.$i];
						$txtDAO_NoMesin=$_POST['txtDAO_NoMesin'.$i];
						$txtDAO_NoBPKB=$_POST['txtDAO_NoBPKB'.$i];
						$txtDAO_STNK_StartDate=date('Y-m-d H:i:s', strtotime($_POST['txtDAO_STNK_StartDate'.$i]));
						$txtDAO_STNK_ExpiredDate=date('Y-m-d H:i:s', strtotime($_POST['txtDAO_STNK_ExpiredDate'.$i]));
						$txtDAO_Pajak_StartDate=date('Y-m-d H:i:s', strtotime($_POST['txtDAO_Pajak_StartDate'.$i]));
						$txtDAO_Pajak_ExpiredDate=date('Y-m-d H:i:s', strtotime($_POST['txtDAO_Pajak_ExpiredDate'.$i]));
						$txtDAO_Lokasi_PT=$_POST['txtDAO_Lokasi_PT'.$i];
						$optDAO_Region=$_POST['optDAO_Region'.$i];
						$txtDAO_Keterangan=$_POST['txtDAO_Keterangan'.$i];

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

						// Memindahkan Pendaftaran Dokumen ke M_DocumentAssetOwnership
						$sql3= "INSERT INTO M_DocumentAssetOwnership
								VALUES (NULL,
										'$CD_Code',
										'$_POST[txtDAO_RegUserID]',
										'$_POST[txtDAO_RegTime]',
										'$company_id',
										'4',
										'$optDAO_Employee_NIK',
										'$optDAO_MK_ID',
										'$txtDAO_Jenis',
										'$txtDAO_Type',
										'$txtDAO_NoPolisi',
										'$txtDAO_NoRangka',
										'$txtDAO_NoMesin',
										'$txtDAO_NoBPKB',
										'$txtDAO_STNK_StartDate',
										'$txtDAO_STNK_ExpiredDate',
										'$txtDAO_Pajak_StartDate',
										'$txtDAO_Pajak_ExpiredDate',
										'$txtDAO_Lokasi_PT',
										'$optDAO_Region',
										'$txtDAO_Keterangan',
										'$DLIU_LocationCode','1', NULL, NULL,
										'$mv_UserID', sysdate(),'$mv_UserID',
										sysdate(),NULL,NULL);";
						$mysqli->query($sql3);
						$mysqli->query($query);
						// $nnum=$nnum+1;
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

			$query = "UPDATE TH_RegistrationOfAssetOwnershipDocument
						SET THROAOD_Status='reject', THROAOD_Reason='$THROAOD_Reason',
							THROAOD_Update_Time=sysdate(), THROAOD_Update_UserID='$A_ApproverID'
						WHERE THROAOD_RegistrationCode='$A_TransactionCode'";

			$query1 = "UPDATE M_Approval
						SET A_Update_Time=sysdate(), A_Update_UserID='$A_ApproverID',
							A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
							A_Status='$A_Status'
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_Step>'$step'";
			if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
				$h_query="SELECT *
							  FROM TH_RegistrationOfAssetOwnershipDocument
							  WHERE THROAOD_RegistrationCode='$A_TransactionCode'
							  AND THROAOD_Delete_Time IS NULL";
				$h_arr=mysql_fetch_array(mysql_query($h_query));
				//mail_notif_registration_doc($A_TransactionCode, $h_arr['THROAOD_UserID'], 4 );

				$e_query="SELECT *
						  FROM M_Approval
						  WHERE A_TransactionCode='$A_TransactionCode'
						  AND A_Step<'$step' ";
				$e_sql=mysql_query($e_query);
				while ($e_arr=mysql_fetch_array($e_sql)){
					//mail_notif_registration_doc($A_TransactionCode, $e_arr['A_ApproverID'], 4 );
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
