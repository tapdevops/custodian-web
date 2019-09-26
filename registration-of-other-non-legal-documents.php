<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2.3																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 20 Agustus 2018																					=
= Update Terakhir	: -																						            =
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Registrasi Dokumen Lainnya (Di Luar Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdoconl.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var optTHROONLD_CompanyID = document.getElementById('optTHROONLD_CompanyID').selectedIndex;

		if(optTHROONLD_CompanyID == 0) {
			alert("Perusahaan Belum Dipilih!");
			return false;
		}
	return true;
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

function checkdate(dtStr){
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
		alert("Format Tanggal : MM/DD/YYYY")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Bulan Tidak Valid")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Hari Tidak Valid")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Masukkan 4 Digit Tahun Dari "+minYear+" Dan "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Tanggal Tidak Valid")
		return false
	}
return true
}

// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var jPT = document.getElementById('count_core_companyid').value;
	for (i = 1; i <= jPT; i++){
		var optTHROONLD_Core_CompanyID = document.getElementById('optTHROONLD_Core_CompanyID' + i).selectedIndex;
		if(optTHROONLD_Core_CompanyID == 0) {
			alert("Nama Perusahaan Pada Baris pada Perusahaan ke-"+i+" Belum Dipilih!");
			return false;
		}
		var jrow = document.getElementById('count_row_per_pt'+i).value;
		for(n = 1; n <= jrow; n++){
			var txtTDROONLD_NoDokumen = document.getElementById('txtTDROONLD_NoDokumen' + i+"_"+n).value;
			var txtTDROONLD_NamaDokumen = document.getElementById('txtTDROONLD_NamaDokumen' + i+"_"+n).value;
			var txtTDROONLD_TahunDokumen = document.getElementById('txtTDROONLD_TahunDokumen' + i+"_"+n).selectedIndex;
			var optTDROONLD_Departemen = document.getElementById('optTDROONLD_Departemen' + i+"_"+n).selectedIndex;
			var txtTDROONLD_Keterangan = document.getElementById('txtTDROONLD_Keterangan' + i+"_"+n).value;

			if (txtTDROONLD_NoDokumen.replace(" ", "") == "")  {
				alert("Nomor Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROONLD_NamaDokumen.replace(" ", "") == "")  {
				alert("Nama Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROONLD_TahunDokumen == 0)  {
				alert("Tahun Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false;
			}
			if(optTDROONLD_Departemen == 0) {
				alert("Departemen pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false;
			}
			if (txtTDROONLD_Keterangan.replace(" ", "") == "")  {
				alert("Keterangan pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi! Isi dengan list lampiran Dokumen terkait");
				return false;
			}
		}
	}
	return true;
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
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
$decrp = new custodian_encryp;

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	//Menambah Header / Dokumen Baru
	if($act=='add') {
		$ActionContent ="
		<form name='add-doc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Lainnya (Di Luar Legal)</th>
		</tr>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2,grup.DocumentGroup_Name,grup.DocumentGroup_ID,
						 e.Employee_GradeCode, e.Employee_Grade
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
				  LEFT JOIN M_DocumentGroup grup
				  	ON grup.DocumentGroup_ID='6'
				  LEFT JOIN db_master.M_Employee AS e
                	ON u.User_ID = e.Employee_NIK
                	AND e.Employee_GradeCode IN ('0000000005','06','0000000003','05','04','0000000004')
				  WHERE u.User_ID='$mv_UserID'";
		$field = mysql_fetch_array(mysql_query($query));

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHROONLD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtTHROONLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtTHROONLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input name='txtTHROONLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>
		<!--<tr>
			<td>Grup Dokumen</td>
			<td>-->
				<input name='txtTHROONLD_DocumentGroupID' type='hidden' value='$field[DocumentGroup_ID]'/>
				<!--Dokumen $field[DocumentGroup_Name]
			</td>
		</tr>-->";

		if(!empty($field['User_SPV1']) || !empty($field['User_SPV2'])){
			if( !empty($field['Employee_GradeCode']) && !empty($field['Employee_Grade']) ){
				// $ActionContent .="
				// <tr>
				// 	<td>Perusahaan</td>
				// 	<td>
				// 		<select name='optTHROONLD_CompanyID' id='optTHROONLD_CompanyID' style='width:350px'>
				// 			<option value='0'>--- Pilih Perusahan ---</option>";
				//
				// 		$query = "SELECT *
				// 				  FROM M_Company
				// 				  WHERE Company_Delete_Time is NULL
				// 				  ORDER BY Company_Name ASC";
				// 		$sql = mysql_query($query);
				//
				// 		while ($field = mysql_fetch_array($sql) ){
				// 			$ActionContent .="
				// 			<option value='$field[Company_ID]'>$field[Company_Name]</option>";
				// 		}
				// $ActionContent .="
				// 		</select>
				// 	</td>
				// </tr>
				$ActionContent .="
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
			}else{ //Else cek jabatan minimal Dept. Head
    			if(!$_POST['cancel']){
    				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini. Minimal jabatan Department Head.);</script>";
    			}

    			$ActionContent .="
    			<tr>
    				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
    					Anda Tidak Dapat Melakukan Transaksi Ini. Minimal jabatan Department Head.<br>
    					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
    				</td>
    			</tr>
    			<tr>
    				<th colspan=3>
    					<input name='cancel' type='submit' value='OK' class='button'/>
    				</th>
    			</tr>";
    		}
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
		// $ActionContent .="<select id='Daftar_Departemen' style='display:none;'>
		// 	<option value='0'>--- Pilih Departemen ---</option>";
		// $query5="SELECT Department_Code, Department_Name
		// 	FROM db_master.M_Department
		// 	WHERE Department_InactiveTime IS NULL";
		// $sql5 = mysql_query($query5);
		//
		// while ($field5=mysql_fetch_array($sql5)) {
		// 	$ActionContent .="
		// 	<option value='$field5[Department_Code]'>$field5[Department_Name]</option>";
		// }
		// $ActionContent .="</select>";

		// $ActionContent .="<select id='Daftar_PT' style='display:none;'>
		// 	<option value='0'>--- Pilih PT ---</option>";
		// $query6="SELECT Company_ID, Company_Name, Company_Code
		// 	FROM M_Company
		// 	WHERE Company_Delete_Time IS NULL";
		// $sql6 = mysql_query($query6);
		//
		// while ($field6=mysql_fetch_array($sql6)) {
		// 	$ActionContent .="
		// 	<option value='$field6[Company_ID]'>$field6[Company_Code] - $field6[Company_Name]</option>";
		// }
		// $ActionContent .="</select>";

		$code=$_GET["id"];
		$query = "SELECT header.THROONLD_ID,
						 header.THROONLD_RegistrationCode,
						 header.THROONLD_RegistrationDate,
						 header.THROONLD_Information,
						 u.User_FullName as FullName,
						 ddp.DDP_DeptID as DeptID,
						 ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID,
						 dp.Department_Name as DeptName,
						 d.Division_Name as DivName,
						 p.Position_Name as PosName,
						 grup.DocumentGroup_Name,
						 grup.DocumentGroup_ID,
						 comp.Company_Name, comp.Company_ID, comp.Company_Area
				  FROM TH_RegistrationOfOtherNonLegalDocuments header
				  LEFT JOIN M_User u
					ON u.User_ID=header.THROONLD_UserID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_Company comp
					ON comp.Company_ID=header.THROONLD_CompanyID
				  LEFT JOIN M_DocumentGroup grup
					ON grup.DocumentGroup_ID=header.THROONLD_DocumentGroupID
				  WHERE header.THROONLD_RegistrationCode='$code'
				  AND header.THROONLD_Delete_Time IS NULL";
		$field = mysql_fetch_array(mysql_query($query));

		$DocGroup=$field['DocumentGroup_ID'];
		$regdate=strtotime($field['THROONLD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);

		$ActionContent .="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Lainnya (Di Luar Legal)</th>
		</tr>
		<tr>
			<td width='30'>No Pendaftaran</td>
			<td width='70%'>
				<input name='txtTDROONLD_THROONLD_ID' type='hidden' value='$field[THROONLD_ID]'/>
				<input type='hidden' name='txtTDROONLD_THROONLD_RegistrationCode' value='$field[THROONLD_RegistrationCode]' style='width:350px;'/>
				$field[THROONLD_RegistrationCode]
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
			<td>
				$field[PosName]
				<input type='hidden' id='txtCompID' name='txtCompID' value='$field[Company_ID]'/>
				<input type='hidden' id='txtCompArea' name='txtCompArea' value='$field[Company_Area]'/>
			</td>
		</tr>
		<!--<tr>
			<td>Grup Dokumen</td>
			<td>-->
				<input type='hidden' id='txtGrupID' name='txtGrupID' value='$field[DocumentGroup_ID]'/>
				<!--$field[DocumentGroup_Name]
			</td>
		</tr>-->
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHROONLD_Information' id='txtTHROONLD_Information' cols='50' rows='2'>$field[THROONLD_Information]</textarea>
			</td>
		</tr>
		<tr>
			<td>Upload File Excel</td>
			<td>
				<img id='loading' src='images/loading.gif' style='display:none;'><input name='fileToUpload' id='fileToUpload' type='file' size='20' /><input name='getExcel' type='button' onclick='return ajaxFileUpload();' value='Upload' class='button-small' />
				<a href='./sample/Sample_of_Excel_Reg_Other_Non_Legal_Doc.xlsx' target='_blank' class='underline'>[Download Format Excel]</a>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		// <table width='1000' id='detail' class='stripeMe'>
		// <tr>
		// 	<!--<th>PT</th>-->
		// 	<th>No. Dokumen</th>
		// 	<th>Nama Dokumen</th>
		// 	<th>Tahun Dokumen</th>
		// 	<th>Departemen</th>
		// </tr>
		// <tr>
		// 	<!--<td>
		// 		<select name='optTDROONLD_PT1' id='optTDROONLD_PT1'>
		// 			<option value='0'>--- Pilih PT ---</option>
		// 		</select>
		// 	</td>-->
		// 	<td>
		// 		<input type='text' name='txtTDROONLD_NoDokumen1' id='txtTDROONLD_NoDokumen1' />
		// 	</td>
		// 	<td>
		// 		<input type='text' name='txtTDROONLD_NamaDokumen1' id='txtTDROONLD_NamaDokumen1'/>
		// 	</td>
		// 	<td>
		// 		<input type='text' name='txtTDROONLD_TahunDokumen1' id='txtTDROONLD_TahunDokumen1' maxlength='4' />
		// 	</td>
		// 	<td>
		// 		<select name='optTDROONLD_Departemen1' id='optTDROONLD_Departemen1'>
		// 			<option value=''>--- Pilih Departemen ---</option>
		// 		</select>
		// 	</td>
		// </tr>
		// </table>
		//
		// <table width='1000'>
		// <th  class='bg-white'>
		// 	<input onclick='addRowToTable();' type='button' class='addrow'/>
		// 	<input onclick='removeRowFromTable();' type='button' class='deleterow'/>
		// 	<input type='hidden' value='1' id='countRow' name='countRow' />
		// </th>
		// </table>
		$ActionContent .="
		<div id='row1'>
		</div>
		<table width='100%'>
		<tr>
			<td>";
			/* PROSES APPROVAL */
			$user=$mv_UserID;

			$result = array();

			for($sApp=1;$sApp<2;$sApp++) {
				//ATASAN LANGSUNG
				$query="SELECT User_SPV1,User_SPV2
						FROM M_User
						WHERE User_ID='$user'";
				$obj=mysql_fetch_object(mysql_query($query));
				$atasan1=$obj->User_SPV1;
				$atasan2=$obj->User_SPV2;

				if($atasan2){
					$sApp=3;
					$atasan=$atasan2;
				}else{
					$atasan=$atasan1;
				}

				$query="SELECT Employee_NIK
						FROM db_master.M_Employee
						WHERE Employee_NIK='".$atasan."'
						AND Employee_Position NOT LIKE '%SECTION%'
						AND Employee_Position NOT LIKE '%SUB DEP%'";
				$canApprove=mysql_num_rows(mysql_query($query));

				if($canApprove){
					//$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$atasan' readonly='true' class='readonly'/>";
				}else{
					$sApp=3;
				}

				$user=$atasan1;
				$result[] = $user;
			}

			$jenis = "19"; //Dokumen Lainnya (Di Luar Legal) - Semua Tipe Dokumen

			$query = "
				SELECT ma.Approver_UserID, rads.RADS_StepID, rads.RADS_RA_ID, ra.RA_Name
				FROM M_Role_ApproverDocStepStatus rads
				LEFT JOIN M_Role_Approver ra
					ON rads.RADS_RA_ID = ra.RA_ID
				LEFT JOIN M_Approver ma
					ON ra.RA_ID = ma.Approver_RoleID
				WHERE rads.RADS_DocID = '{$jenis}'
					AND rads.RADS_ProsesID = '1'
					AND ma.Approver_Delete_Time IS NULL
					AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$field['Company_Area']}')
					ORDER BY rads.RADS_StepID
			";
			$sql=mysql_query($query);

			$output = array();
			$approve_dept_head_custodian = 0;
			while($obj=mysql_fetch_object($sql)){
				$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				// if($obj->RA_Name=="Section Head Custodian" && $obj->Approver_UserID == 0){
				// 	$approve_dept_head_custodian = 1;
				// }
				// if($obj->RA_Name=="Custodian Head" && $approve_dept_head_custodian == 1){
				// 	$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				// 	//Perlu Approval Dept Head Custodian karena tidak ada Section Head Custodian
				// }elseif($obj->RA_Name=="Custodian Head" && $approve_dept_head_custodian == 0){
				// 	//Tidak perlu Approval Dept Head Custodian karena ada Section Head Custodian
				// }else{
				// 	$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				// }
				//$ActionContent .="
				//<input type='text' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}
			//print_r ($output);
			// AKHIR PROSES APPROVAL

			$i = 0;
			$newArray = array();
			foreach ($output as $k => $v) {
				if ($v == '0') { $newArray[$k] = $result[$i]; $i++; } else { $newArray[$k] = $v; }
			}

			$key = array_search('', $newArray);
			if (false !== $key) unset($newArray[$key]);

			foreach ($newArray as $key => $value) {
				$ActionContent .= "<input type='hidden' name='txtA_ApproverID[$key]' value='$value' readonly='true' class='readonly' />";
			}
			/*while($obj=mysql_fetch_object($sql)){
				$ActionContent .="
				<input type='hidden' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}*/
			// AKHIR PROSES APPROVAL

		$ActionContent .="
			</td>
		</tr>
		<tr>
			<th>
				<input name='adddetail' type='submit' value='Daftar' id='button' style='display:none;' class='button' onclick='return validateInputDetail(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>
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
		mail_registration_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=registration-of-other-non-legal-documents.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT DISTINCT THROONLD.THROONLD_ID, THROONLD.THROONLD_RegistrationCode, THROONLD.THROONLD_RegistrationDate, u.User_FullName,
 		  		 c.Company_Name,drs.DRS_Description,THROONLD.THROONLD_Status
		  FROM TH_RegistrationOfOtherNonLegalDocuments THROONLD, M_User u, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE THROONLD.THROONLD_Delete_Time is NULL
		  AND THROONLD.THROONLD_CompanyID=c.Company_ID
		  AND THROONLD.THROONLD_UserID=u.User_ID
		  AND u.User_ID='$mv_UserID'
		  AND THROONLD.THROONLD_Status=drs.DRS_Name
		  AND (c.Company_Delete_Time is NULL OR c.Company_ID='88') /*National*/
		  ORDER BY THROONLD.THROONLD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='25%'>Kode Pendaftaran</th>
	<th width='15%'>Tanggal Pendaftaran</th>
	<th width='20%'>Nama Pendaftar</th>
	<th width='20%'>Nama Perusahaan</th>
	<th width='15%'>Status</th>
	<th width='5%'></th>
</tr>";

if ($num==NULL) {
$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)){
		$regdate=strtotime($field['THROONLD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$resend=($field['THROONLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";
		if($field[5] == "Draft"){
			$link = "registration-of-other-non-legal-documents.php?act=adddetail&id=".$field[1];
		}else{
			$link = "detail-of-registration-other-non-legal-documents.php?id=".$decrp->encrypt($field[0]);
		}

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='".$link."' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$field[5]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT THROONLD.THROONLD_ID, THROONLD.THROONLD_RegistrationCode, THROONLD.THROONLD_RegistrationDate, u.User_FullName,
		   		  c.Company_Name, THROONLD.THROONLD_Status
		   FROM TH_RegistrationOfOtherNonLegalDocuments THROONLD, M_User u, M_Company c
		   WHERE THROONLD.THROONLD_Delete_Time is NULL
		   AND THROONLD.THROONLD_CompanyID=c.Company_ID
		   AND THROONLD.THROONLD_UserID=u.User_ID
		   AND u.User_ID='$mv_UserID'";
$num1 = mysql_num_rows(mysql_query($query1));

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
	echo "<meta http-equiv='refresh' content='0; url=registration-of-other-non-legal-documents.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_RegistrationOfOtherNonLegalDocuments THROONLD
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       THROONLD.THROONLD_Delete_UserID='$mv_UserID',THROONLD.THROONLD_Delete_Time=sysdate(),
				   THROONLD.THROONLD_Update_UserID='$mv_UserID',THROONLD.THROONLD_Update_Time=sysdate()
			   WHERE THROONLD.THROONLD_ID='$_POST[txtTDROONLD_THROONLD_ID]'
			   AND THROONLD.THROONLD_RegistrationCode=ct.CT_Code
			   AND THROONLD.THROONLD_Delete_Time IS NULL";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=registration-of-other-non-legal-documents.php'>";
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
	// $query = "SELECT e.Employee_CompanyCode, c.Company_ID
	// 		FROM db_master.M_Employee e
	// 		INNER JOIN M_Company c
	// 			ON e.Employee_CompanyCode=c.Company_Code
	// 		WHERE Employee_NIK='$mv_UserID'";
	$query = "SELECT Company_Code, Company_ID
			  FROM M_Company
			  WHERE Company_Code='ALL'";
	$field = mysql_fetch_array(mysql_query($query));
	$Company_Code = $field['Company_Code'];
	$Company_ID  = $field['Company_ID'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup
			  WHERE DocumentGroup_ID ='$_POST[txtTHROONLD_DocumentGroupID]'";
	$field = mysql_fetch_array(mysql_query($query));
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Registrasi Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='INS'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$field = mysql_fetch_array(mysql_query($query));

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Registrasi Dokumen
	$CT_Code="$newnum/INS/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode registrasi dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','INS','ALL','$DocumentGroup_Code','$rmonth','$regyear',
			  	   '$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$info = str_replace("<br>", "\n", $_POST['txtTHROONLD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_RegistrationOfOtherNonLegalDocuments
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID','$Company_ID',
				        '$info','$_POST[txtTHROONLD_DocumentGroupID]',
						'0',NULL,'$mv_UserID', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=registration-of-other-non-legal-documents.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST['adddetail'])) {
	// foreach ($_POST as $key => $value) {
	//     echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
	// }
	// exit();
	$A_TransactionCode = $_POST['txtTDROONLD_THROONLD_RegistrationCode'];
	$A_ApproverID=$mv_UserID;
	$txtTHROONLD_Information=str_replace("<br>", "\n", $_POST['txtTHROONLD_Information']);
	// $count=$_POST['countRow'];

	//Phase 2
	$count_company = $_POST['count_core_companyid'];

	for($c = 1; $c <= $count_company; $c++){
		$Core_CompanyID = $_POST['optTHROONLD_Core_CompanyID'.$c];
		foreach($_POST['txtTDROONLD_NoDokumen'.$c] as $key => $value){
			// $optTDROONLD_PT=$_POST["optTDROONLD_PT".$i];
			$optTDROONLD_PT='0';
			$txtTDROONLD_NoDokumen=$_POST["txtTDROONLD_NoDokumen".$c][$key];
			$txtTDROONLD_NamaDokumen=$_POST["txtTDROONLD_NamaDokumen".$c][$key];
			$txtTDROONLD_TahunDokumen=$_POST["txtTDROONLD_TahunDokumen".$c][$key];
			$optTDROONLD_Departemen=$_POST["optTDROONLD_Departemen".$c][$key];
			$txtTDROONLD_Keterangan = $_POST['txtTDROONLD_Keterangan'.$c][$key];

			$sql1= "INSERT INTO TD_RegistrationOfOtherNonLegalDocuments
					VALUES (NULL,'$_POST[txtTDROONLD_THROONLD_ID]', '$Core_CompanyID',
						 	'$optTDROONLD_PT', '$txtTDROONLD_NoDokumen', '$txtTDROONLD_NamaDokumen',
							'$txtTDROONLD_TahunDokumen', '$optTDROONLD_Departemen', '$txtTDROONLD_Keterangan',
							'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
			$mysqli->query($sql1);
		}
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	foreach ($txtA_ApproverID as $k=>$v) {
		if ($txtA_ApproverID[$k] <> NULL) {
			if ($txtA_ApproverID[$k] <> $mv_UserID) {
				$appbefquery = "SELECT *
						FROM M_Approval
						WHERE A_TransactionCode='{$_POST['txtTDROONLD_THROONLD_RegistrationCode']}'
						AND A_ApproverID = '{$txtA_ApproverID[$k]}'";
				$numappbef = mysql_fetch_row(mysql_query($appbefquery));

				if ($numappbef == '0') {
					$step=$step+1;
					$sql2 = "INSERT INTO M_Approval
							VALUES (NULL, '$A_TransactionCode', '$txtA_ApproverID[$k]', '$k', '1', NULL, '$A_ApproverID', sysdate(),
							'$mv_UserID', sysdate(), NULL, NULL)";
					$mysqli->query($sql2);
					$sa_query = "SELECT * FROM M_Approval
								WHERE A_TransactionCode='$A_TransactionCode' AND A_ApproverID='$txtA_ApproverID[$k]'
								AND A_Delete_Time IS NULL";
					$sa_arr = mysql_fetch_array(mysql_query($sa_query));
					$ARC_AID = $sa_arr['A_ID'];
					$str = rand(1,100);
					$RandomCode = crypt('T4pagri'.$str);
					$iSQL="INSERT INTO L_ApprovalRandomCode VALUES ('$ARC_AID', '$RandomCode')";
					$mysqli->query($iSQL);
				}
			}
		}
	}

//
	// MENCARI JUMLAH APPROVAL
	$query = "SELECT MAX(A_Step) AS jStep
				FROM M_Approval
				WHERE A_TransactionCode='$A_TransactionCode'";
	$arr = mysql_fetch_array(mysql_query($query));
	$jStep=$arr['jStep'];

	$jenis = "19"; //Dokumen Lainnya (Di Luar Legal) - Semua Tipe Dokumen

	for ($i=1; $i<=$jStep; $i++) {
		$query ="
			SELECT rads.RADS_StatusID, ma.A_ApproverID
			FROM M_Approval ma
			JOIN M_Role_ApproverDocStepStatus rads
				ON ma.A_Step = rads.RADS_StepID
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			WHERE ma.A_Step = '{$i}'
				AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$_POST['txtCompArea']}')
				AND ma.A_TransactionCode = '{$A_TransactionCode}'
				AND rads.RADS_DocID = '{$jenis}'
				AND rads.RADS_ProsesID = '1'
		";
		$result = mysql_fetch_array(mysql_query($query));

		if ($result['RADS_StatusID'] == '1') {
			$query = "UPDATE M_Approval
					SET A_Status = '2', A_Update_UserID = '$A_ApproverID', A_Update_Time = sysdate()
					WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_registration_doc($A_TransactionCode);
			}
			break;
		} else if ($result['RADS_StatusID'] == '2') {
			$query = "UPDATE M_Approval
					SET A_Status = '3', A_Update_UserID = '$A_ApproverID', A_ApprovalDate = sysdate(), A_Update_Time = sysdate()
					WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_notif_registration_doc($A_TransactionCode, $result['A_ApproverID'], 3);
			}
		}
	}

	/*$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$mv_UserID){
				$appbefquery="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$_POST[txtTDROONLD_THROONLD_RegistrationCode]'
							  AND A_ApproverID='$txtA_ApproverID[$i]'";
				$appbefsql = mysql_query($appbefquery);
				$numappbef=mysql_fetch_row($appbefsql);

				if ($numappbef==0) {
					$sc_query="SELECT *
							   FROM M_Approver a, M_Role_Approver ra
							   WHERE a.Approver_UserID='$txtA_ApproverID[$i]'
							   AND a.Approver_Delete_Time is NULL
							   AND ra.RA_ID=a.Approver_RoleID
							   AND ra.RA_Name LIKE '%Custodian%'";
					$sc_sql=mysql_query($sc_query);
					$sc_app=mysql_num_rows($sc_sql);
					if ($step==0 || $sc_app==1) {
						$step=$step+1;
						if ($step == '1') {
							$sql2 = "INSERT INTO M_Approval
									VALUES (NULL, '$_POST[txtTDROONLD_THROONLD_RegistrationCode]', '$txtA_ApproverID[$i]',
											'$step', '3', sysdate(), '$mv_UserID', sysdate(), '$mv_UserID',
											sysdate(), NULL, NULL)";
						} else {
							$sql2= "INSERT INTO M_Approval
									VALUES (NULL,'$_POST[txtTDROONLD_THROONLD_RegistrationCode]', '$txtA_ApproverID[$i]',
									        '$step', '1',NULL,'$mv_UserID', sysdate(),'$mv_UserID',
											sysdate(),NULL,NULL)";
						}
						$mysqli->query($sql2);
						$sa_query="SELECT *
								   FROM M_Approval
								   WHERE A_TransactionCode='$_POST[txtTDROONLD_THROONLD_RegistrationCode]'
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
				}
			}
		}
	}*/
	/*$sql3= "UPDATE M_Approval
			SET A_Status='2', A_Update_UserID='$mv_UserID',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDROONLD_THROONLD_RegistrationCode]'
			AND A_Step='2'";*/

	$sql4= "UPDATE TH_RegistrationOfOtherNonLegalDocuments
			SET THROONLD_Status='waiting', THROONLD_Information='$txtTHROONLD_Information',
			THROONLD_Update_UserID='$mv_UserID',THROONLD_Update_Time=sysdate()
			WHERE THROONLD_RegistrationCode='$_POST[txtTDROONLD_THROONLD_RegistrationCode]'
			AND THROONLD_Delete_Time IS NULL";
	$mysqli->query($sql4);

	/*if($mysqli->query($sql4)) {
		// Kirim Email ke Approver 1
		mail_registration_doc($_POST['txtTDROONLD_THROONLD_RegistrationCode']);
		mail_notif_registration_doc($_POST['txtTDROONLD_THROONLD_RegistrationCode'], $txtA_ApproverID[0], 3);

	}*/

	echo "<meta http-equiv='refresh' content='0; url=registration-of-other-non-legal-documents.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
// document.getElementById('optTDROONLD_PT1').innerHTML = $('#Daftar_PT').html();
// document.getElementById('optTDROONLD_Departemen1').innerHTML = $('#Daftar_Departemen').html();

// UNTUK DATETIME PICKER
function getDatePublication(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROONLD_DatePublication"+i, "txtTDROONLD_DatePublication"+i, "%m/%d/%Y");
	}

}
function getDateExpired(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROONLD_DateExpired"+i, "txtTDROONLD_DateExpired"+i, "%m/%d/%Y");
	}

}

// TAMBAH BARIS
function addRowToTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	document.getElementById('countRow').value = (document.getElementById('countRow').value*1) + 1;
	var iteration = lastRow;
	// alert(lastrow);
	var row = tbl.insertRow(lastRow);

	// Nama PT
	// var cell0 = row.insertCell(0);
	// var sel = document.createElement('select');
	// sel.name = 'optTDROONLD_PT' + iteration;
	// sel.id = 'optTDROONLD_PT' + iteration;
	// sel.innerHTML = $('#Daftar_PT').html();
	// //sel.setAttribute("onchange","javascript:showType(this.value);");
	// // sel.onchange=function(){ showType(this.value);  };
	// cell0.appendChild(sel);

	// Nomor Dokumen
	var cell0 = row.insertCell(0);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROONLD_NoDokumen' + iteration;
	el.id = 'txtTDROONLD_NoDokumen' + iteration;
	cell0.appendChild(el);

	// Nama Dokumen
	var cell1 = row.insertCell(1);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROONLD_NamaDokumen' + iteration;
	el.id = 'txtTDROONLD_NamaDokumen' + iteration;
	cell1.appendChild(el);

	// Tahun Dokumen
	var cell2 = row.insertCell(2);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROONLD_TahunDokumen' + iteration;
	el.id = 'txtTDROONLD_TahunDokumen' + iteration;
	cell2.appendChild(el);

	// Departemen
	var cell3 = row.insertCell(3);
	var sel = document.createElement('select');
	sel.name = 'optTDROONLD_Departemen' + iteration;
	sel.id = 'optTDROONLD_Departemen' + iteration;
	sel.innerHTML = $('#Daftar_Departemen').html();
	cell3.appendChild(sel);
}

// HAPUS BARIS
function removeRowFromTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	if(document.getElementById('countRow').value > 1)
		document.getElementById('countRow').value -= 1;
	if (lastRow > 2)
		tbl.deleteRow(lastRow - 1);
}

//untuk upload dokumen xls, temp
function ajaxFileUpload()
{
	$("#loading")
	.ajaxStart(function(){
		$(this).show();
	})
	.ajaxComplete(function(){
		$(this).hide();
	});

	$.ajaxFileUpload
	(
		{
			url:'uploaddokumen/doajaxfileupload.php',
			secureuri:false,
			fileElementId:'fileToUpload',
			dataType: 'json',
			data:{name:'logan', id:'id'},
			success: function (data, status)
			{
				if(typeof(data.error) != 'undefined')
				{
					if(data.error != '')
					{
						alert(data.error);
					}else
					{
						//alert(data.filename);
						ajaxReadFile(data.filename);
					}
				}
			},
			error: function (data, status, e)
			{
				alert(e);
			}
		}
	)
	return false;
}

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

//untuk membaca file excel yang diupload
function ajaxReadFile(filename)
{
	$.getJSON("uploaddokumen/readxls.php", { filename: escape(filename) },
		function(data){
			var item = data;
			$("#button").show();
			if ( (data["1"]["A"]!="Perusahaan")||(data["1"]["B"]!="No. Dokumen")||(data["1"]["C"]!="Nama Dokumen")
				||(data["1"]["D"]!="Tahun Dokumen")||(data["1"]["E"]!="Departemen")||(data["1"]["F"]!="Keterangan")
			){
				alert ("Format Excel Salah!!");
				$("#button").hide();
			}
			else {
				// console.log("bisa njing");
				// return false;
				var array_dept_code = [];
				var array_dept_name = [];
				<?php
				$query_dept="SELECT Department_ID, Department_Name
					FROM M_Department
					WHERE Department_Delete_Time IS NULL
					ORDER BY Department_Name";
				$sql_dept = mysql_query($query_dept);

				while ($field_dept = mysql_fetch_array($sql_dept) ){
					?>
					array_dept_code.push("<?=$field_dept['Department_ID'];?>");
					array_dept_name.push("<?=$field_dept['Department_Name'];?>");
					<?php
				}
				?>

				var array_company_id = [];
				var array_company_name = [];

				<?php
				$query_company_id = "SELECT Company_ID, UPPER(Company_Name) AS Company_Name FROM M_Company
						  WHERE Company_Delete_Time is NULL
						  ORDER BY Company_Name ASC";
				$sql_company_id = mysql_query($query_company_id);

				while ($field_ci = mysql_fetch_array($sql_company_id) ){
					?>
					array_company_id.push("<?=$field_ci['Company_ID'];?>");
					array_company_name.push("<?=strtoupper($field_ci['Company_Name']);?>");
					<?php
				}
				?>
				var content = "";
				var header_ke = 0;
				var pt_ke = 0;
				var array_row_ke = [];
				for (var i in data) {
					if (i > 1){
						// data[row][column] references cell from excel document.
						var Nama_Perusahaan = data[i]["A"];
						var No_Dokumen = data[i]["B"];
						var Nama_Dokumen = data[i]["C"];
						var Tahun_Dokumen = data[i]["D"];
						var Departemen = data[i]["E"];
						var Keterangan = data[i]["F"];

						if (Nama_Perusahaan.replace(" ", "") == "")  {
						}else{
							var row_ke = 0;
							pt_ke = parseInt(pt_ke)+parseInt(1);
							header_ke = parseInt(header_ke)+parseInt(1);
							content += "<table width='100%' id='mytable' class='stripeMe'>";
							content += "<tr>";
							content += "<th>Perusahaan</th>";
							content += "<td><select name='optTHROONLD_Core_CompanyID"+pt_ke+"' id='optTHROONLD_Core_CompanyID"+pt_ke+"'>\
								<option value='0'>--- Pilih Perusahaan ---</option>";
							for(x = 0; x < array_company_id.length; x++){
								if( array_company_name[x] == Nama_Perusahaan.toUpperCase() ){
									var selected = " selected";
								}else{
									var selected = "";
								}
								content += "<option value='"+array_company_id[x]+"'"+selected+">"+array_company_name[x]+"</option>";
							}
							content += "</select></td>";
							content += "</tr>";
							content += "<tr>\
								<td><input type='hidden' id='flag_detail"+header_ke+"' value='0' /></td>\
								<td><a class='btn-show-detail' onclick='show_tbl_detail(\""+header_ke+"\")' id='btn-show-detail"+header_ke+"'>Show</a>\
							</tr>";
							content += "</table>";

							content += "<table width='1000' id='detail"+header_ke+"' class='stripeMe' style='display:none;padding-bottom:10px;'>";
							content += "<tr>";
							content += "<th>No. Dokumen</th>";
							content += "<th>Nama Dokumen</th>";
							content += "<th>Tahun Dokumen</th>";
							content += "<th>Departemen</th>";
							content += "<th>Keterangan</th>";
							content += "</tr>";
						}
						if(No_Dokumen != "" || Nama_Dokumen != "" || Nama_Dokumen != "" || Departemen != "" || Keterangan != ""
						){
							row_ke = parseInt(row_ke)+parseInt(1);
							array_row_ke["count_row_per_pt"+pt_ke] = row_ke;
							content += "<tr>";
							content += "<td><input type=text name=txtTDROONLD_NoDokumen"+pt_ke+"[] id=txtTDROONLD_NoDokumen"+pt_ke+"_"+row_ke+" value='"+No_Dokumen+"'></td>";

							content += "<td><input type=text name=txtTDROONLD_NamaDokumen"+pt_ke+"[] id=txtTDROONLD_NamaDokumen"+pt_ke+"_"+row_ke+" value='"+Nama_Dokumen+"'></td>";
							// content += "<td><input type=text name=txtTDROONLD_TahunDokumen"+pt_ke+"[] id=txtTDROONLD_TahunDokumen"+pt_ke+"_"+row_ke+" value='"+Tahun_Dokumen+"'></td>";
							content += "<td><select name='txtTDROONLD_TahunDokumen"+pt_ke+"[]' id='txtTDROONLD_TahunDokumen"+pt_ke+"_"+row_ke+"'>\
								<option value='0'>--- Pilih Tahun Dokumen ---</option>";
							for(var t = 2019; t >= 1980; t--){
								if(t == Tahun_Dokumen){
									var selected1 = " selected";
								}else{
									var selected1 = "";
								}
								content += "<option value='"+t+"'"+selected1+">"+t+"</option>";
							}
							content += "</select></td>";
							content += "<td><select name='optTDROONLD_Departemen"+pt_ke+"[]' id='optTDROONLD_Departemen"+pt_ke+"_"+row_ke+"'>\
								<option value='0'>--- Pilih Departemen ---</option>";
							for(x = 0; x < array_dept_code.length; x++){
								if( array_dept_name[x] == Departemen ){
									var selected2 = " selected";
								}else{
									var selected2 = "";
								}
								content += "<option value='"+array_dept_code[x]+"'"+selected2+">"+array_dept_name[x]+"</option>";
							}
							content += "</select></td>";
							content += "<td><textarea name=txtTDROONLD_Keterangan"+pt_ke+"[] id=txtTDROONLD_Keterangan"+pt_ke+"_"+row_ke+">"+Keterangan+"</textarea></td>";
							content += "</tr>";

							// console.log("jumlah row pt ke "+pt_ke+" = "+row_ke);
							// var last_number_row_of_pt+pt_ke = row_ke;
						}else{
							content += "</table>";
						}
					}
				};
				content += "<input type='hidden' name='count_core_companyid' id='count_core_companyid' value='"+pt_ke+"'/>";
				for (var x = 0; x < pt_ke; x++) {
					var pt = parseInt(x)+parseInt(1);
					var total_row_per_pt = array_row_ke["count_row_per_pt"+pt];
					content += "<input type='hidden' name='count_row_per_pt"+pt+"' id='count_row_per_pt"+pt+"' value='"+total_row_per_pt+"'/>";
				}
				// content += "<input type='hidden' name='count_pt"+pt_ke+"' value='"+pt_ke+"'/>";
				// console.log(array_row_ke);
				$('#row1').html(content);
			}
		});
}

</script>
