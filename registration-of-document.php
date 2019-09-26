<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2.3																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 04 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
= 		23/05/2012	: Validasi keterangan, masa habis berlaku dihilangkan. (OK)											=
=					  Approval hanya ke 1 level di atasnya, custodian, dan custodian head (OK)							=
=					  Kategori dokumen dipindahkan ke bagian detail registrasi -> Perubahan Struktur DB (OK)			=
=					  Letak kolom di detail : Kategori - Tipe - Instansi Terkait - No Dokumen (OK)						=
=					  Letak DatePicker di bagian detail (OK)															=
=					  Button "Cancel" untuk detail transaksi (OK)														=
= 		31/05/2012	: Persetujuan transaksi via email & email notifikasi. (OK)											=
=		19/09/2012	: Perubahan Reminder Email																			=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Registrasi Dokumen</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdoc.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
// JQUERY UNTUK MENAMPILKAN TIPE DOKUMEN DARI KATEGORI DOKUMEN YANG DIPILIH
function showType(x, row){
	// var i=document.getElementById('countRow').value;
	if($('#optTDROLD_DocumentCategoryID'+row).val() == '0'){
		$('#optTDROLD_DocumentTypeID'+row).html("<option value='0'>--- Pilih Kategori Dokumen Terlebih Dahulu ---</option>");
	}else{
		var txtGrupID = document.getElementById('txtGrupID').value;
 		$.post("jQuery.DocumentType.php", {
			CategoryID: $('#optTDROLD_DocumentCategoryID'+row).val(),
			GroupID: txtGrupID
		}, function(response){

			setTimeout("finishAjax('optTDROLD_DocumentTypeID"+row+"', '"+escape(response)+"')", 400);
		});
	}
}
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
}

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var optTHROLD_CompanyID = document.getElementById('optTHROLD_CompanyID').selectedIndex;
	var optTHROLD_DocumentGroupID = document.getElementById('optTHROLD_DocumentGroupID').selectedIndex;

		if(optTHROLD_CompanyID == 0) {
			alert("Perusahaan Belum Dipilih!");
			return false;
		}
		if(optTHROLD_DocumentGroupID == 0) {
			alert("Grup Dokumen Belum Dipilih!");
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
		var optTHROLD_Core_CompanyID = document.getElementById('optTHROLD_Core_CompanyID' + i).selectedIndex;
		if(optTHROLD_Core_CompanyID == 0) {
			alert("Nama Perusahaan Pada Baris pada Perusahaan ke-"+i+" Belum Dipilih!");
			return false;
		}
		var jrow = document.getElementById('count_row_per_pt'+i).value;
		for(n = 1; n <= jrow; n++){
			var optTDROLD_DocumentTypeID = document.getElementById('optTDROLD_DocumentTypeID' + i+"_"+n).selectedIndex;
			var optTDROLD_DocumentCategoryID = document.getElementById('optTDROLD_DocumentCategoryID' + i+"_"+n).selectedIndex;
			var txtTDROLD_DocumentNo = document.getElementById('txtTDROLD_DocumentNo' + i+"_"+n).value;
			var txtTDROLD_DatePublication = document.getElementById('txtTDROLD_DatePublication' + i+"_"+n).value;
			var txtTDROLD_DateExpired = document.getElementById('txtTDROLD_DateExpired' + i+"_"+n).value;
			var optTDROLD_DocumentInformation1ID = document.getElementById('optTDROLD_DocumentInformation1ID' + i+"_"+n).selectedIndex;
			var optTDROLD_DocumentInformation2ID = document.getElementById('optTDROLD_DocumentInformation2ID' + i+"_"+n).selectedIndex;
			var txtTDROLD_DocumentInformation3 = document.getElementById('txtTDROLD_DocumentInformation3'+i+"_"+n).value;
			var txtTDROLD_Instance = document.getElementById('txtTDROLD_Instance' + i+"_"+n).value;
			var Date1 = new Date(txtTDROLD_DatePublication);
			var Date2 = new Date(txtTDROLD_DateExpired);

			if(optTDROLD_DocumentCategoryID == 0) {
				alert("Kategori Dokumen Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false;
			}
			if(optTDROLD_DocumentTypeID == 0) {
				alert("Tipe Dokumen Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false;
			}
			if (txtTDROLD_Instance.replace(" ", "") == "")  {
				alert("Nama Instansi pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROLD_DocumentNo.replace(" ", "") == "")  {
				alert("Nomor Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROLD_DatePublication.replace(" ", "") == "")  {
				alert("Tanggal Publikasi pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROLD_DatePublication.replace(" ", "") != "")  {
				if (checkdate(txtTDROLD_DatePublication) == false) {
					return false;
				}
			}
			if (txtTDROLD_DateExpired.replace(" ", "") != "")  {
				if (checkdate(txtTDROLD_DateExpired) == false) {
					return false;
				}
				else {
					if (Date2 < Date1) {
					alert("Tanggal Habis Masa Berlaku pada baris ke-" + n + " pada Perusahaan ke-"+i+" Lebih Kecil Daripada Tanggal Publikasi!");
					return false;
					}
				}
			}
			if(optTDROLD_DocumentInformation1ID == 0) {
				alert("Informasi Dokumen 1 Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false;
			}
			if(optTDROLD_DocumentInformation2ID == 0) {
				alert("Informasi Dokumen 2 Pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false;
			}
			if (txtTDROLD_DocumentInformation3.replace(" ", "") == "")  {
				alert("Keterangan 3 pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi! Isi dengan Resume Dokumen terkait");
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
			<th colspan=3>Registrasi Dokumen</th>
		</tr>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2,
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
				  LEFT JOIN db_master.M_Employee AS e
                	ON u.User_ID = e.Employee_NIK
                	AND e.Employee_GradeCode IN ('0000000005','06','0000000003','05','04','0000000004')
				  WHERE u.User_ID='$mv_UserID'";
		$field = mysql_fetch_array(mysql_query($query));

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHROLD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtTHROLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtTHROLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input name='txtTHROLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";

		if(!empty($field['User_SPV1']) || !empty($field['User_SPV2'])){
			if( !empty($field['Employee_GradeCode']) && !empty($field['Employee_Grade']) ){
				// $ActionContent .="
				// <tr>
				// 	<td>Perusahaan</td>
				// 	<td>
				// 		<select name='optTHROLD_CompanyID' id='optTHROLD_CompanyID' style='width:350px'>
				// 			<option value='0'>--- Pilih Perusahan ---</option>";
				//
				// 		$query = "SELECT Company_ID, Company_Name
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
					<td>Grup Dokumen</td>
					<td>
						<select name='optTHROLD_DocumentGroupID' id='optTHROLD_DocumentGroupID' style='width:150px'>
							<option value='0'>--- Pilih Grup ---</option>";

						$query = "SELECT *
								  FROM M_DocumentGroup
								  WHERE DocumentGroup_Delete_Time is NULL
								  AND DocumentGroup_ID IN ('1', '2')"; //Arief F - 29082018
						$sql = mysql_query($query);

						while ($field = mysql_fetch_array($sql) ){
							$ActionContent .="
							<option value='$field[DocumentGroup_ID]'>$field[DocumentGroup_Name]</option>";
						}
				$ActionContent .="
						</select>
					</td>
				</tr>
				<tr>
					<td>Keterangan</td>
					<td><textarea name='txtTHROLD_Information' id='txtTHROLD_Information' cols='50' rows='2'></textarea></td>
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
		$code=$_GET["id"];

		$query = "SELECT header.THROLD_ID,
						 header.THROLD_RegistrationCode,
						 header.THROLD_RegistrationDate,
						 header.THROLD_Information,
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
				  FROM TH_RegistrationOfLegalDocument header
				  LEFT JOIN M_User u
					ON u.User_ID=header.THROLD_UserID
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
					ON comp.Company_ID=header.THROLD_CompanyID
				  LEFT JOIN M_DocumentGroup grup
					ON grup.DocumentGroup_ID=header.THROLD_DocumentGroupID
				  WHERE header.THROLD_RegistrationCode='$code'
				  AND header.THROLD_Delete_Time IS NULL";
		$field = mysql_fetch_array(mysql_query($query));

		$txtGrupID = $field['DocumentGroup_ID'];

		$DocGroup=$field['DocumentGroup_ID'];
		$regdate=strtotime($field['THROLD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);

		if($field['DocumentGroup_ID'] == '1'){
			$Sample_Doc = "Sample_of_Excel_Reg_Legal_Doc.xlsx";
		}elseif($field['DocumentGroup_ID'] == '2'){
			$Sample_Doc = "Sample_of_Excel_Reg_License_Doc.xlsx";
		}else{
			$Sample_Doc = "SampleExcelRegDoc.xlsx";
		}

		$ActionContent ="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen</th>
		</tr>
		<tr>
			<td width='30'>No Pendaftaran</td>
			<td width='70%'>
				<input name='txtTDROLD_THROLD_ID' type='hidden' value='$field[THROLD_ID]'/>
				<input type='hidden' name='txtTDROLD_THROLD_RegistrationCode' value='$field[THROLD_RegistrationCode]' style='width:350px;'/>
				$field[THROLD_RegistrationCode]
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
		<!--<tr>
			<td>Perusahaan</td>
			<td>-->
				<input type='hidden' id='txtCompID' name='txtCompID' value='$field[Company_ID]'/>
				<input type='hidden' id='txtCompArea' name='txtCompArea' value='$field[Company_Area]'/>
				<!--$field[Company_Name]
			</td>
		</tr>-->
		<tr>
			<td>Grup Dokumen</td>
			<td>
				<input type='hidden' id='txtGrupID' name='txtGrupID' value='$field[DocumentGroup_ID]'/>
				$field[DocumentGroup_Name]
			</td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHROLD_Information' id='txtTHROLD_Information' cols='50' rows='2'>$field[THROLD_Information]</textarea>
			</td>
		</tr>
		<tr>
			<td>Upload File Excel</td>
			<td>
				<img id='loading' src='images/loading.gif' style='display:none;'><input name='fileToUpload' id='fileToUpload' type='file' size='20' /><input name='getExcel' type='button' onclick='return ajaxFileUpload();' value='Upload' class='button-small' />
				<a href='./sample/".$Sample_Doc."' target='_blank' class='underline'>[Download Format Excel]</a>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		// <table width='2000' id='detail' class='stripeMe'>
		// <tr>
		// 	<th>Kategori Dokumen</th>
		// 	<th>Tipe Dokumen</th>
		// 	<th>Instansi Terkait</th>
		// 	<th>Nomor Dokumen</th>
		// 	<th>Tanggal Terbit<br>(MM/DD/YYYY)</th>
		// 	<th>Tanggal Habis Berlaku<br>(MM/DD/YYYY)</th>
		// 	<th>Keterangan 1</th>
		// 	<th>Keterangan 2</th>
		// 	<th>Keterangan 3</th>
		// </tr>
		// <tr>
		// 	<td>
		// 		<select name='optTDROLD_DocumentCategoryID1' id='optTDROLD_DocumentCategoryID1' onchange='showType(this.value);'>
		// 			<option value='0'>--- Pilih Kategori Dokumen ---</option>";

		// 		$query5="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name
		// 				 FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
		// 				 WHERE dgct.DGCT_DocumentGroupID='$DocGroup'
		// 				 AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
		// 				 AND dgct.DGCT_Delete_Time is NULL";
		// 		$sql5 = mysql_query($query5);
		//
		// 		while ($field5=mysql_fetch_array($sql5)) {
		// 			$ActionContent .="
		// 			<option value='$field5[DocumentCategory_ID]'>$field5[DocumentCategory_Name]</option>";
		// 		}
		// $ActionContent .="
		// 		</select>
		// 	</td>
		// 	<td>
		// 		<select id='optTDROLD_DocumentTypeID1' name='optTDROLD_DocumentTypeID1'>
		// 			<option value='0'>--- Pilih Kategori Dokumen Terlebih Dahulu ---</option>
		// 		</select>
		// 	</td>
		// 	<td>
		// 		<input type='text' name='txtTDROLD_Instance1' id='txtTDROLD_Instance1'/>
		// 	</td>
		// 	<td>
		// 		<input type='text' name='txtTDROLD_DocumentNo1' id='txtTDROLD_DocumentNo1'/>
		// 	</td>
		// 	<td>
		// 		<input type='text' size='10' readonly='readonly' name='txtTDROLD_DatePublication1' id='txtTDROLD_DatePublication1' onclick=\"javascript:NewCssCal('txtTDROLD_DatePublication1', 'MMddyyyy');\"/>
		// 	</td>
		// 	<td>
		// 		<input type='text' size='10' readonly='readonly' name='txtTDROLD_DateExpired1' id='txtTDROLD_DateExpired1' onclick=\"javascript:NewCssCal('txtTDROLD_DateExpired1', 'MMddyyyy');\"/>
		// 	</td>
		// 	<td>
		// 		<select name='optTDROLD_DocumentInformation1ID1' id='optTDROLD_DocumentInformation1ID1'>
		// 			<option value='0'>--- Pilih Keterangan Dokumen ---</option>";
		//
		// 		$query6="SELECT *
		// 				 FROM M_DocumentInformation1
		// 				 WHERE DocumentInformation1_Delete_Time is NULL";
		// 		$sql6 = mysql_query($query6);
		//
		// 		while ($field6=mysql_fetch_array($sql6)) {
		// 			$ActionContent .="
		// 			<option value='$field6[DocumentInformation1_ID]'>$field6[DocumentInformation1_Name]</option>";
		// 		}
		// $ActionContent .="
		// 		</select>
		// 	</td>
		// 	<td>
		// 		<select name='optTDROLD_DocumentInformation2ID1' id='optTDROLD_DocumentInformation2ID1'>
		// 			<option value='0'>--- Pilih Keterangan Dokumen ---</option>";
		//
		// 		$query7="SELECT *
		// 				 FROM M_DocumentInformation2
		// 				 WHERE DocumentInformation2_Delete_Time is NULL";
		// 		$sql7 = mysql_query($query7);
		//
		// 		while ($field7=mysql_fetch_array($sql7)) {
		// 			$ActionContent .="
		// 			<option value='$field7[DocumentInformation2_ID]'>$field7[DocumentInformation2_Name]</option>";
		// 		}
		// $ActionContent .="
		// 		</select>
		// 	</td>
		// 	<td>
		// 		<textarea name='txtTDROLD_DocumentInformation31' id='txtTDROLD_DocumentInformation31' cols='20' rows='1'></textarea>
		// 	</td>
		// </tr>
		// </table>
		//
		// <table width='2000'>
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
					// $ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$atasan' readonly='true' class='readonly'/>";
				}else{
					$sApp=3;
				}

				$user=$atasan1;
				$result[] = $user;
			}

			// if ($field['DocumentGroup_ID'] == '1') { $jenis = '1'; $proses = '1'; }
			// else if ($field['DocumentGroup_ID'] != '1') { $jenis = '2'; $proses = '1'; }
			// else;
			$jenis = "7"; //Legal & Lisensi - Semua //Arief F - 24082018

			/*$query="SELECT a.Approver_UserID
					FROM M_Approver a
					LEFT JOIN M_Role_Approver ra
						ON ra.RA_ID=a.Approver_RoleID
						AND a.Approver_Delete_Time is NULL
					WHERE ra.RA_Name LIKE '%custodian%'
					ORDER BY ra.RA_ID";
			$sql=mysql_query($query);*/
			$query = "
				SELECT ma.Approver_UserID, rads.RADS_StepID
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
			while($obj=mysql_fetch_object($sql)){
				$output[$obj->RADS_StepID] = $obj->Approver_UserID;
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
		echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT DISTINCT throld.THROLD_ID, throld.THROLD_RegistrationCode, throld.THROLD_RegistrationDate, u.User_FullName,
 		  		 c.Company_Name,drs.DRS_Description,throld.THROLD_Status
		  FROM TH_RegistrationOfLegalDocument throld, M_User u, M_Company c,M_DocumentRegistrationStatus drs
		  WHERE throld.THROLD_Delete_Time is NULL
		  AND throld.THROLD_CompanyID=c.Company_ID
		  AND throld.THROLD_UserID=u.User_ID
		  AND u.User_ID='$mv_UserID'
		  AND throld.THROLD_Status=drs.DRS_Name
		  AND (c.Company_Delete_Time is NULL OR c.Company_ID='88') /*National*/
		  ORDER BY throld.THROLD_ID DESC
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
		$regdate=strtotime($field['THROLD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$resend=($field['THROLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";
		if($field[5] == "Draft"){
			$link = "registration-of-document.php?act=adddetail&id=".$field[1];
		}else{
			$link = "detail-of-registration-document.php?id=".$decrp->encrypt($field[0]);
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

$query1 = "SELECT throld.THROLD_ID, throld.THROLD_RegistrationCode, throld.THROLD_RegistrationDate, u.User_FullName,
		   		  c.Company_Name, throld.THROLD_Status
		   FROM TH_RegistrationOfLegalDocument throld, M_User u, M_Company c
		   WHERE throld.THROLD_Delete_Time is NULL
		   AND throld.THROLD_CompanyID=c.Company_ID
		   AND c.Company_Delete_Time is NULL
		   AND throld.THROLD_UserID=u.User_ID
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
	echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_RegistrationOfLegalDocument throld
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       throld.THROLD_Delete_UserID='$mv_UserID',throld.THROLD_Delete_Time=sysdate(),
				   throld.THROLD_Update_UserID='$mv_UserID',throld.THROLD_Update_Time=sysdate()
			   WHERE throld.THROLD_ID='$_POST[txtTDROLD_THROLD_ID]'
			   AND throld.THROLD_RegistrationCode=ct.CT_Code
			   AND throld.THROLD_Delete_Time IS NULL";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php'>";
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
	$query = "SELECT Company_Code, Company_ID
			  FROM M_Company
			  WHERE Company_Code='ALL'";
	$field = mysql_fetch_array(mysql_query($query));
	$Company_Code = $field['Company_Code'];
	$Company_ID = $field['Company_ID'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup
			  WHERE DocumentGroup_ID ='$_POST[optTHROLD_DocumentGroupID]'";
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
		$info = str_replace("<br>", "\n", $_POST['txtTHROLD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_RegistrationOfLegalDocument
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID','$Company_ID',
				        '$info','$_POST[optTHROLD_DocumentGroupID]',
						'0',NULL,'$mv_UserID', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST['adddetail'])) {
	// echo $_POST['optTHROLD_Core_CompanyID1']."<hr>";
	// echo $_POST['txtTDROLD_DocumentNo1'][0]."<hr>";
	// echo $_POST['txtTDROLD_THROLD_ID']."<hr>";
	// echo $mv_UserID."<hr>";
	// foreach ($_POST as $key => $value) {
	//     echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
	// }
	// exit();
	$A_TransactionCode = $_POST['txtTDROLD_THROLD_RegistrationCode'];
	$A_ApproverID=$mv_UserID;

	$count=$_POST['countRow'];
	$txtTHROLD_Information=str_replace("<br>", "\n", $_POST['txtTHROLD_Information']);

	//Phase 2
	$count_company = $_POST['count_core_companyid'];

	for ($c=1; $c<=$count_company; $c++) {
		$Core_CompanyID = $_POST['optTHROLD_Core_CompanyID'.$c];
		foreach($_POST['txtTDROLD_DocumentNo'.$c] as $key => $value){
			$optTDROLD_DocumentCategoryID=$_POST["optTDROLD_DocumentCategoryID".$c][$key];
			$optTDROLD_DocumentTypeID=$_POST["optTDROLD_DocumentTypeID".$c][$key];
			$txtTDROLD_DocumentNo=$_POST["txtTDROLD_DocumentNo".$c][$key];
			$txtTDROLD_DatePublication=$_POST["txtTDROLD_DatePublication".$c][$key];
			$txtTDROLD_DatePublication=date('Y-m-d H:i:s', strtotime($txtTDROLD_DatePublication));
			$txtTDROLD_DateExpired=$_POST["txtTDROLD_DateExpired".$c][$key];
			$txtTDROLD_DateExpired=date('Y-m-d H:i:s', strtotime($txtTDROLD_DateExpired));
			if 	(strstr($txtTDROLD_DateExpired, ' ', true)=="1970-01-01"){
				$txtTDROLD_DateExpired=NULL;
			}
			$optTDROLD_DocumentInformation1ID=$_POST["optTDROLD_DocumentInformation1ID".$c][$key];
			$optTDROLD_DocumentInformation2ID=$_POST["optTDROLD_DocumentInformation2ID".$c][$key];
			$txtTDROLD_DocumentInformation3=str_replace("<br>", "\n", $_POST["txtTDROLD_DocumentInformation3".$c][$key]);
			$txtTDROLD_Instance=$_POST["txtTDROLD_Instance".$c][$key];

			$sql1= "INSERT INTO TD_RegistrationOfLegalDocument
				SET TDROLD_THROLD_ID='$_POST[txtTDROLD_THROLD_ID]', TDROLD_Core_CompanyID='$Core_CompanyID',
					TDROLD_DocumentCategoryID='$optTDROLD_DocumentCategoryID', TDROLD_DocumentTypeID='$optTDROLD_DocumentTypeID',
					TDROLD_DocumentNo='$txtTDROLD_DocumentNo', TDROLD_DatePublication='$txtTDROLD_DatePublication',
					TDROLD_DateExpired='$txtTDROLD_DateExpired', TDROLD_DocumentInformation1ID='$optTDROLD_DocumentInformation1ID',
					TDROLD_DocumentInformation2ID='$optTDROLD_DocumentInformation2ID',
					TDROLD_DocumentInformation3='$txtTDROLD_DocumentInformation3', TDROLD_Instance='$txtTDROLD_Instance',
					TDROLD_Insert_UserID='$mv_UserID', TDROLD_Insert_Time=sysdate(),
					TDROLD_Update_UserID='$mv_UserID', TDROLD_Update_Time=sysdate()";
					// VALUES (NULL,'$_POST[txtTDROLD_THROLD_ID]', '$optTDROLD_DocumentCategoryID',
					// 		'$optTDROLD_DocumentTypeID', '$txtTDROLD_DocumentNo',
					//         '$txtTDROLD_DatePublication', '$txtTDROLD_DateExpired', '$optTDROLD_DocumentInformation1ID',
					// 		'$optTDROLD_DocumentInformation2ID', '$txtTDROLD_DocumentInformation3',
					// 		'$txtTDROLD_Instance','$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
					// echo $sql1."<hr>";
			$mysqli->query($sql1);
		}
	}
	// exit();

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	foreach ($txtA_ApproverID as $k=>$v) {
		if ($txtA_ApproverID[$k] <> NULL) {
			if ($txtA_ApproverID[$k] <> $mv_UserID) {
				$appbefquery = "SELECT *
						FROM M_Approval
						WHERE A_TransactionCode='{$_POST['txtTDROLD_THROLD_RegistrationCode']}'
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

	// if ($_POST['txtGrupID'] == '1' && $_POST['optTDROLD_DocumentCategoryID1'] == '1') { $jenis = '1'; }
	// else if ($_POST['txtGrupID'] == '1' && $_POST['optTDROLD_DocumentCategoryID1'] != '1') { $jenis = '2'; }
	// else if ($_POST['txtGrupID'] == '2' && $_POST['optTDROLD_DocumentCategoryID1'] == '1') { $jenis = '3'; }
	// else if ($_POST['txtGrupID'] == '2' && $_POST['optTDROLD_DocumentCategoryID1'] != '1') { $jenis = '4'; }
	// else;
	$jenis = "7"; //Legal & Lisensi - Semua //Arief F - 24082018

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
							  WHERE A_TransactionCode='$_POST[txtTDROLD_THROLD_RegistrationCode]'
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
									VALUES (NULL, '$_POST[txtTDROLD_THROLD_RegistrationCode]', '$txtA_ApproverID[$i]',
											'$step', '3', sysdate(), '$mv_UserID', sysdate(), '$mv_UserID',
											sysdate(), NULL, NULL)";
						} else {
							$sql2= "INSERT INTO M_Approval
									VALUES (NULL,'$_POST[txtTDROLD_THROLD_RegistrationCode]', '$txtA_ApproverID[$i]',
									        '$step', '1',NULL,'$mv_UserID', sysdate(),'$mv_UserID',
											sysdate(),NULL,NULL)";
						}
						$mysqli->query($sql2);
						$sa_query="SELECT *
								   FROM M_Approval
								   WHERE A_TransactionCode='$_POST[txtTDROLD_THROLD_RegistrationCode]'
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
			WHERE A_TransactionCode ='$_POST[txtTDROLD_THROLD_RegistrationCode]'
			AND A_Step='2'";*/

	$sql4= "UPDATE TH_RegistrationOfLegalDocument
			SET THROLD_Status='waiting', THROLD_Information='$txtTHROLD_Information',
			THROLD_Update_UserID='$mv_UserID',THROLD_Update_Time=sysdate()
			WHERE THROLD_RegistrationCode='$_POST[txtTDROLD_THROLD_RegistrationCode]'
			AND THROLD_Delete_Time IS NULL";
	$mysqli->query($sql4);

	/*if($mysqli->query($sql4)) {
		// Kirim Email ke Approver 1
		mail_registration_doc($_POST['txtTDROLD_THROLD_RegistrationCode']);
		mail_notif_registration_doc($_POST['txtTDROLD_THROLD_RegistrationCode'], $txtA_ApproverID[0], 3);

	}*/

	echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
// UNTUK DATETIME PICKER
function getDatePublication(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROLD_DatePublication"+i, "txtTDROLD_DatePublication"+i, "%m/%d/%Y");
	}

}
function getDateExpired(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROLD_DateExpired"+i, "txtTDROLD_DateExpired"+i, "%m/%d/%Y");
	}

}

// TAMBAH BARIS
function addRowToTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	document.getElementById('countRow').value = (document.getElementById('countRow').value*1) + 1;
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);

	// KATEGORI DOKUMEN
	var cell0 = row.insertCell(0);
	var sel = document.createElement('select');
	sel.name = 'optTDROLD_DocumentCategoryID' + iteration;
	sel.id = 'optTDROLD_DocumentCategoryID' + iteration;
	sel.options[0] = new Option('--- Pilih Kategori Dokumen ---', '0');
	<?PHP
		$query5="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name
				 FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
				 WHERE dgct.DGCT_DocumentGroupID='$DocGroup'
				 AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
				 AND dgct.DGCT_Delete_Time is NULL";
 		$sql5 = mysql_query($query5);
		$i = 1;

		while ($field5=mysql_fetch_array($sql5)) {
			$s_tmp = "sel.options[$i] = new Option('$field5[1]','$field5[0]');";
			echo $s_tmp;
			$i++;
		}
	?>
	//sel.setAttribute("onchange","javascript:showType(this.value);");
	sel.onchange=function(){ showType(this.value);  };
	cell0.appendChild(sel);

	// TIPE DOKUMEN
	var cell1 = row.insertCell(1);
	var sel = document.createElement('select');
	sel.name = 'optTDROLD_DocumentTypeID' + iteration;
	sel.id = 'optTDROLD_DocumentTypeID' + iteration;
	sel.options[0] = new Option('--- Pilih Kategori Dokumen Terlebih Dahulu ---', '0');
	cell1.appendChild(sel);

	// INSTANSI
	var cell2 = row.insertCell(2);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROLD_Instance' + iteration;
	el.id = 'txtTDROLD_Instance' + iteration;
	cell2.appendChild(el);

	// NOMOR DOKUMEN
	var cell3 = row.insertCell(3);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROLD_DocumentNo' + iteration;
	el.id = 'txtTDROLD_DocumentNo' + iteration;
	cell3.appendChild(el);

	// TGL TERBIT
	var cell4 = row.insertCell(4);
	var elPubDate = document.createElement('input');
	elPubDate.type = 'text';
	elPubDate.name = 'txtTDROLD_DatePublication' + iteration;
	elPubDate.id = 'txtTDROLD_DatePublication' + iteration;
	elPubDate.size = '10';
	//el.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	elPubDate.onclick=function(){ NewCssCal(elPubDate.id, 'MMddyyyy');  };
	cell4.appendChild(elPubDate);

	// TGL EXPIRED
	var cell5 = row.insertCell(5);
	var elExpDate = document.createElement('input');
	elExpDate.type = 'text';
	elExpDate.name = 'txtTDROLD_DateExpired' + iteration;
	elExpDate.id = 'txtTDROLD_DateExpired' + iteration;
	elExpDate.size = '10';
	//elExpDate.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	elExpDate.onclick=function(){ NewCssCal(elExpDate.id, 'MMddyyyy');  };
	cell5.appendChild(elExpDate);

	// KETERANGAN DOKUMEN 1
	var cell6 = row.insertCell(6);
	var sel = document.createElement('select');
	sel.name = 'optTDROLD_DocumentInformation1ID' + iteration;
	sel.id = 'optTDROLD_DocumentInformation1ID' + iteration;
	sel.options[0] = new Option('--- Pilih Keterangan Dokumen ---', '0');
	<?PHP
		$query5="SELECT *
				 FROM M_DocumentInformation1
				 WHERE DocumentInformation1_Delete_Time is NULL";
 		$sql5 = mysql_query($query5);
		$i = 1;

		while ($field5=mysql_fetch_array($sql5)) {
			$s_tmp = "sel.options[$i] = new Option('$field5[1]','$field5[0]');";
			echo $s_tmp;
			$i++;
		}
	?>
	cell6.appendChild(sel);

	// KETERANGAN DOKUMEN 2
	var cell7 = row.insertCell(7);
	var sel = document.createElement('select');
	sel.name = 'optTDROLD_DocumentInformation2ID' + iteration;
	sel.id = 'optTDROLD_DocumentInformation2ID' + iteration;
	sel.options[0] = new Option('--- Pilih Keterangan Dokumen ---', '0');
	<?PHP
		$query5="SELECT *
				 FROM M_DocumentInformation2
				 WHERE DocumentInformation2_Delete_Time is NULL";
 		$sql5 = mysql_query($query5);
		$i = 1;

		while ($field5=mysql_fetch_array($sql5)) {
			$s_tmp = "sel.options[$i] = new Option('$field5[1]','$field5[0]');";
			echo $s_tmp;
			$i++;
		}
	?>
	cell7.appendChild(sel);

	// KETERANGAN DOKUMEN 3
	var cell8 = row.insertCell(8);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDROLD_DocumentInformation3' + iteration;
	el.id = 'txtTDROLD_DocumentInformation3' + iteration;
	cell8.appendChild(el);
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
			if ( (data["1"]["A"]!="Perusahaan")||(data["1"]["B"]!="Kategori Dokumen")||(data["1"]["C"]!="Tipe Dokumen")
				||(data["1"]["D"]!="Instansi Terkait")||(data["1"]["E"]!="Nomor Dokumen")||(data["1"]["F"]!="Tanggal Terbit (MM/DD/YYYY)")
				||(data["1"]["G"]!="Tanggal Habis Berlaku (MM/DD/YYYY)")
				||(data["1"]["H"]!="Keterangan 1")||(data["1"]["I"]!="Keterangan 2")||(data["1"]["J"]!="Keterangan 3")
			){
				alert ("Format Excel Salah!!");
				$("#button").hide();
			}
			else {
				var array_kategori_dokumen_id = [];
				var array_kategori_dokumen_name = [];

				var array_tipe_dokumen_id = [];
				var array_tipe_dokumen_name = [];
				<?php
				$query_kategori_dokumen="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name
						 FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
						 WHERE dgct.DGCT_DocumentGroupID='$DocGroup'
						 AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
						 AND dgct.DGCT_Delete_Time is NULL
						 ORDER BY dc.DocumentCategory_Name";
				$sql_kategori_dokumen = mysql_query($query_kategori_dokumen);
				while ($field_kd=mysql_fetch_array($sql_kategori_dokumen)) {
					$CategoryID = $field_kd['DocumentCategory_ID'];
					?>
					array_kategori_dokumen_id.push("<?=$field_kd['DocumentCategory_ID'];?>");
					array_kategori_dokumen_name.push("<?=$field_kd['DocumentCategory_Name'];?>");

					array_tipe_dokumen_id['<?=$CategoryID;?>'] = [];
					array_tipe_dokumen_name['<?=$CategoryID;?>'] = [];

					<?php
					$query_tipe_dokumen="SELECT DISTINCT dt.DocumentType_ID,dt.DocumentType_Name
							FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
						   	WHERE dgct.DGCT_DocumentGroupID='$DocGroup'
									AND dgct.DGCT_DocumentCategoryID='$CategoryID'
						   	AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
						   	AND dgct.DGCT_Delete_Time is NULL
						   	ORDER BY DocumentType_Name";
					$sql_tipe_dokumen = mysql_query($query_tipe_dokumen);
					while ($field_td=mysql_fetch_array($sql_tipe_dokumen)) {
						?>
						array_tipe_dokumen_id['<?=$CategoryID;?>'].push('<?=$field_td['DocumentType_ID'];?>');
						array_tipe_dokumen_name['<?=$CategoryID;?>'].push('<?=$field_td['DocumentType_Name'];?>');
						<?php
					}
				}
				?>
				// console.log(array_tipe_dokumen_name['5']);
				// return false;

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

				var array_keterangan1_id = [];
				var array_keterangan1_name = [];
				<?php
				$query_ket1_dokumen="SELECT DocumentInformation1_ID, DocumentInformation1_Name
						 FROM M_DocumentInformation1
						 WHERE DocumentInformation1_Delete_Time is NULL";
				$sql_ket1_dokumen = mysql_query($query_ket1_dokumen);
				while ($field_k1=mysql_fetch_array($sql_ket1_dokumen)) {
					?>
					array_keterangan1_id.push("<?=$field_k1['DocumentInformation1_ID'];?>");
					array_keterangan1_name.push("<?=$field_k1['DocumentInformation1_Name'];?>");
					<?php
				}
				?>

				var array_keterangan2_id = [];
				var array_keterangan2_name = [];
				<?php
				$query_ket2_dokumen="SELECT DocumentInformation2_ID, DocumentInformation2_Name
						 FROM M_DocumentInformation2
						 WHERE DocumentInformation2_Delete_Time is NULL";
				$sql_ket2_dokumen = mysql_query($query_ket2_dokumen);
				while ($field_k2=mysql_fetch_array($sql_ket2_dokumen)) {
					?>
					array_keterangan2_id.push("<?=$field_k2['DocumentInformation2_ID'];?>");
					array_keterangan2_name.push("<?=$field_k2['DocumentInformation2_Name'];?>");
					<?php
				}
				?>
				var content = "";
				var header_ke = 0;
				var pt_ke = 0;
				var array_row_ke = [];
				var array_kategori_dok_selected = [];
				var array_tipe_dok_selected = [];
				for (var i in data) {
					if (i > 1){
						// data[row][column] references cell from excel document.
						var Nama_Perusahaan = data[i]["A"];
						var Kategori_Dokumen = data[i]["B"];
						var Tipe_Dokumen = data[i]["C"];
						var Instansi_Terkait = data[i]["D"];
						var No_Dokumen = data[i]["E"];
						var Tanggal_Terbit = data[i]["F"];
						var Tanggal_Berakhir = data[i]["G"];
						var Keterangan1 = data[i]["H"];
						var Keterangan2 = data[i]["I"];
						var Keterangan3 = data[i]["J"];

						if (Nama_Perusahaan.replace(" ", "") == "")  {
						}else{
							var row_ke = 0;
							pt_ke = parseInt(pt_ke)+parseInt(1);
							header_ke = parseInt(header_ke)+parseInt(1);
							content += "<table width='100%' id='mytable' class='stripeMe'>";
							content += "<tr>";
							content += "<th>Perusahaan</th>";
							content += "<td><select name='optTHROLD_Core_CompanyID"+pt_ke+"' id='optTHROLD_Core_CompanyID"+pt_ke+"'>\
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
							// content += "<td><input type='text' name='optTHROLD_Core_CompanyID' value='"+Nama_Perusahaan+"' ></td>";
							content += "</tr>";
							content += "<tr>\
								<td><input type='hidden' id='flag_detail"+header_ke+"' value='0' /></td>\
								<td><a class='btn-show-detail' onclick='show_tbl_detail(\""+header_ke+"\")' id='btn-show-detail"+header_ke+"'>Show</a>\
							</tr>";
							content += "</table>";

							content += "<table width='1000' id='detail"+header_ke+"' class='stripeMe' style='display:none;padding-bottom:10px;'>";
							content += "<tr>";
							content += "<th>Kategori Dokumen</th>";
							content += "<th>Tipe Dokumen</th>";
							content += "<th>Instansi Terkait</th>";
							content += "<th>Nomor Dokumen</th>";
							content += "<th>Tanggal Terbit (MM/DD/YYY)</th>";
							content += "<th>Tanggal Berakhir (MM/DD/YYY)</th>";
							content += "<th>Keterangan 1</th>";
							content += "<th>Keterangan 2</th>";
							content += "<th>Keterangan 3</th>";
							content += "</tr>";
						}
						if(Kategori_Dokumen != "" || Tipe_Dokumen != "" || Instansi_Terkait != "" || No_Dokumen != "" || Tanggal_Terbit != ""
							 || Tanggal_Berakhir != "" || Keterangan1 != "" || Keterangan2 != "" || Keterangan3 != ""
						){
							var selected_id_kategoridokumen = "";
							var selected_id_tipedokumen = "";
							row_ke = parseInt(row_ke)+parseInt(1);
							array_row_ke["count_row_per_pt"+pt_ke] = row_ke;
							// var kategori_dokumen_id_selected;
							// var tipe_dokumen_id_selected;
							content += "<tr>";
							content += "<td>\
								<select name='optTDROLD_DocumentCategoryID"+pt_ke+"[]' id='optTDROLD_DocumentCategoryID"+pt_ke+"_"+row_ke+"' onchange='showType(this.value, \""+pt_ke+"_"+row_ke+"\");'>\
								<option value='0'>--- Pilih Kategori Dokumen ---</option>";
							for(x = 0; x < array_kategori_dokumen_id.length; x++){
								if( array_kategori_dokumen_name[x] == Kategori_Dokumen ){
									var selected1 = " selected";
									selected_id_kategoridokumen = array_kategori_dokumen_id[x];
								}else{
									var selected1 = "";
								}
								content += "<option value='"+array_kategori_dokumen_id[x]+"'"+selected1+">"+array_kategori_dokumen_name[x]+"</option>";
							}
							// array_kategori_dok_selected[pt_ke+"_"+row_ke] = selected_id_kategoridokumen;
							content += "</select></td>";
							content += "<td><select name=optTDROLD_DocumentTypeID"+pt_ke+"[] id=optTDROLD_DocumentTypeID"+pt_ke+"_"+row_ke+">";
							if(selected_id_kategoridokumen == ""){
								content += "<option value='0'>--- Pilih Kategori Dokumen Terlebih Dahulu ---</option>";
							}else{
								content += "<option value='0'>--- Pilih Tipe Dokumen ---</option>";
								for(x = 0; x < array_tipe_dokumen_id[selected_id_kategoridokumen].length; x++){
									if( array_tipe_dokumen_name[selected_id_kategoridokumen][x] == Tipe_Dokumen ){
										var selected2 = " selected";
										selected_id_tipedokumen = array_tipe_dokumen_id[selected_id_kategoridokumen][x];
									}else{
										var selected2 = "";
									}
									content += "<option value='"+array_tipe_dokumen_id[selected_id_kategoridokumen][x]+"'"+selected2+">\
										"+array_tipe_dokumen_name[selected_id_kategoridokumen][x]+"\
									</option>";
								}
							}
							// array_tipe_dok_selected[pt_ke+"_"+row_ke] = selected_id_tipedokumen;
							content += "</select></td>";
							content += "<td><input type=text name=txtTDROLD_Instance"+pt_ke+"[] id=txtTDROLD_Instance"+pt_ke+"_"+row_ke+" value='"+Instansi_Terkait+"'></td>";
							content += "<td><input type=text name=txtTDROLD_DocumentNo"+pt_ke+"[] id=txtTDROLD_DocumentNo"+pt_ke+"_"+row_ke+" value='"+No_Dokumen+"'></td>";
							content += "<td><input type=text name=txtTDROLD_DatePublication"+pt_ke+"[] id=txtTDROLD_DatePublication"+pt_ke+"_"+row_ke+" value='"+Tanggal_Terbit+"' onclick=\"javascript:NewCssCal('txtTDROLD_DatePublication"+pt_ke+"_"+row_ke+"', 'MMddyyyy');\"></td>";
							content += "<td><input type=text name=txtTDROLD_DateExpired"+pt_ke+"[] id=txtTDROLD_DateExpired"+pt_ke+"_"+row_ke+" value='"+Tanggal_Berakhir+"' onclick=\"javascript:NewCssCal('txtTDROLD_DateExpired"+pt_ke+"_"+row_ke+"', 'MMddyyyy');\"></td>";
							content += "<td><select name='optTDROLD_DocumentInformation1ID"+pt_ke+"[]' id='optTDROLD_DocumentInformation1ID"+pt_ke+"_"+row_ke+"'>\
								<option value='0'>--- Pilih Keterangan Dokumen ---</option>";
							for(x = 0; x < array_keterangan1_id.length; x++){
								if( array_keterangan1_name[x] == Keterangan1 ){
									var selected3 = " selected";
								}else{
									var selected3 = "";
								}
								content += "<option value='"+array_keterangan1_id[x]+"'"+selected3+">"+array_keterangan1_name[x]+"</option>";
							}
							content += "</select></td>";
							content += "<td><select name='optTDROLD_DocumentInformation2ID"+pt_ke+"[]' id='optTDROLD_DocumentInformation2ID"+pt_ke+"_"+row_ke+"'>\
								<option value='0'>--- Pilih Keterangan Dokumen ---</option>";
							for(x = 0; x < array_keterangan2_id.length; x++){
								if( array_keterangan2_name[x] == Keterangan2 ){
									var selected4 = " selected";
								}else{
									var selected4 = "";
								}
								content += "<option value='"+array_keterangan2_id[x]+"'"+selected4+">"+array_keterangan2_name[x]+"</option>";
							}
							content += "</select></td>";
							content += "<td><textarea name=txtTDROLD_DocumentInformation3"+pt_ke+"[] id=txtTDROLD_DocumentInformation3"+pt_ke+"_"+row_ke+">"+Keterangan3+"</textarea></td>";
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
				$('#row1').html(content);

			}
		});
}
</script>
