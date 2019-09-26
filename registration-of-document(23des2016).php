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
function showType(){
	var i=document.getElementById('countRow').value;
	var txtGrupID = document.getElementById('txtGrupID').value;
 		$.post("jQuery.DocumentType.php", {
			CategoryID: $('#optTDROLD_DocumentCategoryID'+i).val(),
			GroupID: txtGrupID
		}, function(response){
			
			setTimeout("finishAjax('optTDROLD_DocumentTypeID"+i+"', '"+escape(response)+"')", 400);
		});
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
	var jrow = document.getElementById('countRow').value;
	for (i = 1; i <= jrow; i++){
		var optTDROLD_DocumentTypeID = document.getElementById('optTDROLD_DocumentTypeID' + i).selectedIndex;
		var optTDROLD_DocumentCategoryID = document.getElementById('optTDROLD_DocumentCategoryID' + i).selectedIndex;
		var txtTDROLD_DocumentNo = document.getElementById('txtTDROLD_DocumentNo' + i).value;
		var txtTDROLD_DatePublication = document.getElementById('txtTDROLD_DatePublication' + i).value;
		var txtTDROLD_DateExpired = document.getElementById('txtTDROLD_DateExpired' + i).value;
		var optTDROLD_DocumentInformation1ID = document.getElementById('optTDROLD_DocumentInformation1ID' + i).selectedIndex;
		var optTDROLD_DocumentInformation2ID = document.getElementById('optTDROLD_DocumentInformation2ID' + i).selectedIndex;
		var txtTDROLD_Instance = document.getElementById('txtTDROLD_Instance' + i).value;
		var Date1 = new Date(txtTDROLD_DatePublication);
		var Date2 = new Date(txtTDROLD_DateExpired);
				
		if(optTDROLD_DocumentCategoryID == 0) {
			alert("Kategori Dokumen Pada Baris ke-" + i + " Belum Dipilih!");
			return false;
		}
		if(optTDROLD_DocumentTypeID == 0) {
			alert("Tipe Dokumen Pada Baris ke-" + i + " Belum Dipilih!");
			return false;
		}
		if (txtTDROLD_Instance.replace(" ", "") == "")  {	
			alert("Nama Instansi pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROLD_DocumentNo.replace(" ", "") == "")  {	
			alert("Nomor Dokumen pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROLD_DatePublication.replace(" ", "") == "")  {	
			alert("Tanggal Publikasi pada baris ke-" + i + " Belum Terisi!");
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
				alert("Tanggal Habis Masa Berlaku pada baris ke-" + i + " Lebih Kecil Daripada Tanggal Publikasi!");
				return false;
				}
			}
		}
		if(optTDROLD_DocumentInformation1ID == 0) {
			alert("Informasi Dokumen 1 Pada Baris ke-" + i + " Belum Dipilih!");
			return false;
		}
		if(optTDROLD_DocumentInformation2ID == 0) {
			alert("Informasi Dokumen 2 Pada Baris ke-" + i + " Belum Dipilih!");
			return false;
		}
	}
	return true;
}
</script>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
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
				  WHERE u.User_ID='$_SESSION[User_ID]'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);
		
		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHROLD_UserID' type='hidden' value='$_SESSION[User_ID]'/>
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
		
		if($field['User_SPV1']||$field['User_SPV2']){
			$ActionContent .="
			<tr>
				<td>Perusahaan</td>
				<td> 
					<select name='optTHROLD_CompanyID' id='optTHROLD_CompanyID' style='width:350px'>
						<option value='0'>--- Pilih Perusahan ---</option>";
			
					$query = "SELECT * 
							  FROM M_Company 
							  WHERE Company_Delete_Time is NULL
							  ORDER BY Company_Name ASC";
					$sql = mysql_query($query);
					
					while ($field = mysql_fetch_array($sql) ){
						$ActionContent .="
						<option value='$field[Company_ID]'>$field[Company_Name]</option>";
					}
			$ActionContent .="	
					</select>
				</td>
			</tr>
			<tr>
				<td>Grup Dokumen</td>
				<td> 
					<select name='optTHROLD_DocumentGroupID' id='optTHROLD_DocumentGroupID' onChange='showCategory()' style='width:150px'>
						<option value='0'>--- Pilih Grup ---</option>";
						
					$query = "SELECT * 
							  FROM M_DocumentGroup 
							  WHERE DocumentGroup_Delete_Time is NULL 
							  AND DocumentGroup_ID<>'3'";
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
						 comp.Company_Name
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
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);
		
		$DocGroup=$field['DocumentGroup_ID'];
		$regdate=strtotime($field['THROLD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);

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
		<tr>
			<td>Perusahaan</td>
			<td>$field[Company_Name]</td>
		</tr>
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
		</table>
		
		<div style='space'>&nbsp;</div>
		
		<table width='2000' id='detail' class='stripeMe'>
		<tr>
			<th>Kategori Dokumen</th>
			<th>Tipe Dokumen</th>
			<th>Instansi Terkait</th>
			<th>Nomor Dokumen</th>
			<th>Tanggal Terbit<br>(MM/DD/YYYY)</th>
			<th>Tanggal Habis Berlaku<br>(MM/DD/YYYY)</th>
			<th>Keterangan 1</th>
			<th>Keterangan 2</th>
			<th>Keterangan 3</th>
		</tr>
		<tr>
			<td>
				<select name='optTDROLD_DocumentCategoryID1' id='optTDROLD_DocumentCategoryID1' onchange='showType(this.value);'>
					<option value='0'>--- Pilih Kategori Dokumen ---</option>";
				
				$query5="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name 
						 FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
						 WHERE dgct.DGCT_DocumentGroupID='$DocGroup' 
						 AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
						 AND dgct.DGCT_Delete_Time is NULL";
				$sql5 = mysql_query($query5);
				
				while ($field5=mysql_fetch_array($sql5)) {
					$ActionContent .="
					<option value='$field5[DocumentCategory_ID]'>$field5[DocumentCategory_Name]</option>";
				}
		$ActionContent .="
				</select>
			</td>
			<td>
				<select id='optTDROLD_DocumentTypeID1' name='optTDROLD_DocumentTypeID1'>
					<option value='0'>--- Pilih Kategori Dokumen Terlebih Dahulu ---</option>
				</select>
			</td>
			<td>
				<input type='text' name='txtTDROLD_Instance1' id='txtTDROLD_Instance1'/>
			</td>
			<td>
				<input type='text' name='txtTDROLD_DocumentNo1' id='txtTDROLD_DocumentNo1'/>
			</td>
			<td>
				<input type='text' size='10' readonly='readonly' name='txtTDROLD_DatePublication1' id='txtTDROLD_DatePublication1' onclick=\"javascript:NewCssCal('txtTDROLD_DatePublication1', 'MMddyyyy');\"/>
			</td>
			<td>
				<input type='text' size='10' readonly='readonly' name='txtTDROLD_DateExpired1' id='txtTDROLD_DateExpired1' onclick=\"javascript:NewCssCal('txtTDROLD_DateExpired1', 'MMddyyyy');\"/>
			</td>
			<td>
				<select name='optTDROLD_DocumentInformation1ID1' id='optTDROLD_DocumentInformation1ID1'>
					<option value='0'>--- Pilih Keterangan Dokumen ---</option>";
				
				$query6="SELECT * 
						 FROM M_DocumentInformation1 
						 WHERE DocumentInformation1_Delete_Time is NULL";
				$sql6 = mysql_query($query6);
				
				while ($field6=mysql_fetch_array($sql6)) {
					$ActionContent .="
					<option value='$field6[DocumentInformation1_ID]'>$field6[DocumentInformation1_Name]</option>";
				}
		$ActionContent .="
				</select>
			</td>
			<td>
				<select name='optTDROLD_DocumentInformation2ID1' id='optTDROLD_DocumentInformation2ID1'>
					<option value='0'>--- Pilih Keterangan Dokumen ---</option>";
				
				$query7="SELECT * 
						 FROM M_DocumentInformation2 
						 WHERE DocumentInformation2_Delete_Time is NULL";
				$sql7 = mysql_query($query7);
				
				while ($field7=mysql_fetch_array($sql7)) {
					$ActionContent .="
					<option value='$field7[DocumentInformation2_ID]'>$field7[DocumentInformation2_Name]</option>";
				}
		$ActionContent .="
				</select>
			</td>
			<td>
				<textarea name='txtTDROLD_DocumentInformation31' id='txtTDROLD_DocumentInformation31' cols='20' rows='1'></textarea>
			</td>
		</tr>
		</table>
		
		<table width='2000'>
		<th  class='bg-white'>
			<input onclick='addRowToTable();' type='button' class='addrow'/>
			<input onclick='removeRowFromTable();' type='button' class='deleterow'/>
			<input type='hidden' value='1' id='countRow' name='countRow' />
		</th>
		</table>
		
		<table width='100%'>
		<tr>
			<td>";

			/* PROSES APPROVAL */
			$user=$_SESSION['User_ID'];
			
			for($sApp=1;$sApp<2;$sApp++) {
				//ATASAN LANGSUNG
				$query="SELECT User_SPV1,User_SPV2
						FROM M_User
						WHERE User_ID='$user'";
				$sql=mysql_query($query);
				$obj=mysql_fetch_object($sql);
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
				$sql=mysql_query($query);
				$canApprove=mysql_num_rows($sql);
				
				if($canApprove){
					$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$atasan' readonly='true' class='readonly'/>";
				}else{
					$sApp=0;
				}						
				
				$user=$atasan1;
			}
			
			$query="SELECT a.Approver_UserID
					FROM M_Approver a
					LEFT JOIN M_Role_Approver ra
						ON ra.RA_ID=a.Approver_RoleID
						AND a.Approver_Delete_Time is NULL
					WHERE ra.RA_Name LIKE '%custodian%'
					ORDER BY ra.RA_ID";
			$sql=mysql_query($query);
			
			while($obj=mysql_fetch_object($sql)){
				$ActionContent .="
				<input type='hidden' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}
			// AKHIR PROSES APPROVAL
			
		$ActionContent .="
			</td>
		</tr>
		<tr>
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/>
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
		  AND u.User_ID='$_SESSION[User_ID]' 
		  AND throld.THROLD_Status=drs.DRS_Name
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

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-registration-document.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
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
		   AND throld.THROLD_UserID=u.User_ID 
		   AND u.User_ID='$_SESSION[User_ID]'";
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
	echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_RegistrationOfLegalDocument throld
			   SET ct.CT_Delete_UserID='$_SESSION[User_ID]',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$_SESSION[User_ID]',ct.CT_Update_Time=sysdate(),
			       throld.THROLD_Delete_UserID='$_SESSION[User_ID]',throld.THROLD_Delete_Time=sysdate(),
				   throld.THROLD_Update_UserID='$_SESSION[User_ID]',throld.THROLD_Update_Time=sysdate()
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
	$query = "SELECT *
			  FROM M_Company 
			  WHERE Company_ID='$_POST[optTHROLD_CompanyID]'";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$Company_Code=$field['Company_Code'];
	
	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup 
			  WHERE DocumentGroup_ID ='$_POST[optTHROLD_DocumentGroupID]'";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Registrasi Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo) 
			  FROM M_CodeTransaction 
			  WHERE CT_Year='$regyear' 
			  AND CT_Action='INS'
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
	
	// Kode Registrasi Dokumen	
	$CT_Code="$newnum/INS/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";
	
	// Insert kode registrasi dokumen baru
	$sql= "INSERT INTO M_CodeTransaction 
		   VALUES (NULL,'$CT_Code','$nnum','INS','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
			  	   '$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',sysdate(),NULL,NULL)";
					
	if($mysqli->query($sql)) {
		$info = str_replace("<br>", "\n", $_POST['txtTHROLD_Information']);
		//Insert Header Dokumen 
		$sql1= "INSERT INTO TH_RegistrationOfLegalDocument 
				VALUES (NULL,'$CT_Code',sysdate(),'$_SESSION[User_ID]','$_POST[optTHROLD_CompanyID]',
				        '$info','$_POST[optTHROLD_DocumentGroupID]',
						'0',NULL,'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$count=$_POST[countRow];
	$txtTHROLD_Information=str_replace("<br>", "\n", $_POST[txtTHROLD_Information]);
	
	for ($i=1 ; $i<=$count ; $i++) {
		$optTDROLD_DocumentCategoryID=$_POST["optTDROLD_DocumentCategoryID".$i];
		$optTDROLD_DocumentTypeID=$_POST["optTDROLD_DocumentTypeID".$i];
		$txtTDROLD_DocumentNo=$_POST["txtTDROLD_DocumentNo".$i];
		$txtTDROLD_DatePublication=$_POST["txtTDROLD_DatePublication".$i];
		$txtTDROLD_DatePublication=date('Y-m-d H:i:s', strtotime($txtTDROLD_DatePublication));
		$txtTDROLD_DateExpired=$_POST["txtTDROLD_DateExpired".$i];
		$txtTDROLD_DateExpired=date('Y-m-d H:i:s', strtotime($txtTDROLD_DateExpired));
		if 	(strstr($txtTDROLD_DateExpired, ' ', true)=="1970-01-01"){
			$txtTDROLD_DateExpired=NULL;
		}
		$optTDROLD_DocumentInformation1ID=$_POST["optTDROLD_DocumentInformation1ID".$i];
		$optTDROLD_DocumentInformation2ID=$_POST["optTDROLD_DocumentInformation2ID".$i];
		$txtTDROLD_DocumentInformation3=str_replace("<br>", "\n", $_POST["txtTDROLD_DocumentInformation3".$i]);
		$txtTDROLD_Instance=$_POST["txtTDROLD_Instance".$i];

		$sql1= "INSERT INTO TD_RegistrationOfLegalDocument 
				VALUES (NULL,'$_POST[txtTDROLD_THROLD_ID]', '$optTDROLD_DocumentCategoryID',
						'$optTDROLD_DocumentTypeID', '$txtTDROLD_DocumentNo', 
				        '$txtTDROLD_DatePublication', '$txtTDROLD_DateExpired', '$optTDROLD_DocumentInformation1ID', 
						'$optTDROLD_DocumentInformation2ID', '$txtTDROLD_DocumentInformation3',
						'$txtTDROLD_Instance','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		$mysqli->query($sql1);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$_SESSION['User_ID']){
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
						$sql2= "INSERT INTO M_Approval 
								VALUES (NULL,'$_POST[txtTDROLD_THROLD_RegistrationCode]', '$txtA_ApproverID[$i]', 
								        '$step', '1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
										sysdate(),NULL,NULL)";
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
	}
	$sql3= "UPDATE M_Approval 
			SET A_Status='2', A_Update_UserID='$_SESSION[User_ID]',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDROLD_THROLD_RegistrationCode]' 
			AND A_Step='1'";
			
	$sql4= "UPDATE TH_RegistrationOfLegalDocument 
			SET THROLD_Status='waiting', THROLD_Information='$txtTHROLD_Information',
			THROLD_Update_UserID='$_SESSION[User_ID]',THROLD_Update_Time=sysdate()
			WHERE THROLD_RegistrationCode='$_POST[txtTDROLD_THROLD_RegistrationCode]'
			AND THROLD_Delete_Time IS NULL";
			
	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		mail_registration_doc($_POST['txtTDROLD_THROLD_RegistrationCode']);
		
		echo "<meta http-equiv='refresh' content='0; url=registration-of-document.php'>";
	}
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
</script>