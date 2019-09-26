<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2.3																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Agustus 2018																					=
= Update Terakhir	: -																						            =
= Revisi			: -																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Detail Registrasi Dokumen Lainnya (Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdocol.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
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
			if ( (data["1"]["A"]!="Perusahaan")||(data["1"]["B"]!="Kategori Dokumen")||(data["1"]["C"]!="Nama Dokumen")
				||(data["1"]["D"]!="Instansi Terkait")||(data["1"]["E"]!="No. Dokumen")||(data["1"]["F"]!="Tanggal Terbit")||(data["1"]["G"]!="Tanggal Berakhir")
				||(data["1"]["H"]!="Keterangan")
			){
				alert ("Format Excel Salah!!");
				$("#button").hide();
			}
			else {
				var array_kategori_dokumen_id = [];
				var array_kategori_dokumen_name = [];
				<?php
				$query_kategori_dokumen="SELECT DocumentCategory_ID, DocumentCategory_Name
					FROM db_master.M_DocumentCategory
					WHERE DocumentCategory_Delete_Time IS NULL";
				$sql_kategori_dokumen = mysql_query($query_kategori_dokumen);
				while ($field_kd=mysql_fetch_array($sql_kategori_dokumen)) {
					?>
					array_kategori_dokumen_id.push("<?=$field_kd['DocumentCategory_ID'];?>");
					array_kategori_dokumen_name.push("<?=$field_kd['DocumentCategory_Name'];?>");
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
						var Kategori_Dokumen = data[i]["B"];
						var Nama_Dokumen = data[i]["C"];
						var Instansi_Terkait = data[i]["D"];
						var No_Dokumen = data[i]["E"];
						var Tanggal_Terbit = data[i]["F"];
						var Tanggal_Berakhir = data[i]["G"];
						var Keterangan = data[i]["H"];

						if (Nama_Perusahaan.replace(" ", "") == "")  {
						}else{
							var row_ke = 0;
							pt_ke = parseInt(pt_ke)+parseInt(1);
							header_ke = parseInt(header_ke)+parseInt(1);
							content += "<table width='100%' id='mytable' class='stripeMe'>";
							content += "<tr>";
							content += "<th>Perusahaan</th>";
							content += "<td><select name='optTHROOLD_Core_CompanyID"+pt_ke+"' id='optTHROOLD_Core_CompanyID"+pt_ke+"'>\
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
							content += "<th>Kategori Dokumen</th>";
							content += "<th>Nama Dokumen</th>";
							content += "<th>Instansi Terkait</th>";
							content += "<th>No. Dokumen</th>";
							content += "<th>Tanggal Terbit</th>";
							content += "<th>Tanggal Berakhir</th>";
							content += "<th>Keterangan</th>";
							content += "</tr>";
						}
						if(Kategori_Dokumen != "" || Nama_Dokumen != "" || Instansi_Terkait != "" || No_Dokumen != ""
						 	|| Tanggal_Terbit != "" || Tanggal_Berakhir != "" || Keterangan != ""
						){
							row_ke = parseInt(row_ke)+parseInt(1);
							array_row_ke["count_row_per_pt"+pt_ke] = row_ke;
							content += "<tr>";
							content += "<td><select name='optTDROOLD_KategoriDokumen"+pt_ke+"[]' id='optTDROOLD_KategoriDokumen"+pt_ke+"_"+row_ke+"'>\
								<option value='0'>--- Pilih Kategori Dokumen ---</option>";
							for(x = 0; x < array_kategori_dokumen_id.length; x++){
								if( array_kategori_dokumen_name[x] == Kategori_Dokumen ){
									var selected = " selected";
								}else{
									var selected = "";
								}
								content += "<option value='"+array_kategori_dokumen_id[x]+"'"+selected+">"+array_kategori_dokumen_name[x]+"</option>";
							}
							content += "</select></td>";
							content += "<td><input type=text name=txtTDROOLD_NamaDokumen"+pt_ke+"[] id=txtTDROOLD_NamaDokumen"+pt_ke+"_"+row_ke+" value='"+Nama_Dokumen+"'></td>";
							content += "<td><input type=text name=txtTDROOLD_InstansiTerkait"+pt_ke+"[] id=txtTDROOLD_InstansiTerkait"+pt_ke+"_"+row_ke+" value='"+Instansi_Terkait+"'></td>";
							content += "<td><input type=text name=txtTDROOLD_NoDokumen"+pt_ke+"[] id=txtTDROOLD_NoDokumen"+pt_ke+"_"+row_ke+" value='"+No_Dokumen+"'></td>";
							content += "<td><input type=text name=txtTDROOLD_TglTerbit"+pt_ke+"[] id=txtTDROOLD_TglTerbit"+pt_ke+"_"+row_ke+" value='"+Tanggal_Terbit+"' onclick=\"javascript:NewCssCal('txtTDROOLD_TglTerbit"+pt_ke+"_"+row_ke+"', 'MMddyyyy');\"></td>";
							content += "<td><input type=text name=txtTDROOLD_TglBerakhir"+pt_ke+"[] id=txtTDROOLD_TglBerakhir"+pt_ke+"_"+row_ke+" value='"+Tanggal_Berakhir+"' onclick=\"javascript:NewCssCal('txtTDROOLD_TglBerakhir"+pt_ke+"_"+row_ke+"', 'MMddyyyy');\"></td>";
							content += "<td><textarea name=txtTDROOLD_Keterangan"+pt_ke+"[] id=txtTDROOLD_Keterangan"+pt_ke+"_"+row_ke+">"+Keterangan+"</textarea></td>";
							content += "</tr>";
							// console.log("jumlah row pt ke "+pt_ke+" = "+row_ke);
							// var last_number_row_of_pt+pt_ke = row_ke;
							// content += "<input type='hidden' name='count_row_per_pt"+pt_ke+"' id='count_row_per_pt"+pt_ke+"' value='"+row_ke+"'/>";
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

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var optTHROOLD_Core_CompanyID = document.getElementById('optTHROOLD_Core_CompanyID').selectedIndex;
		if(optTHROOLD_Core_CompanyID == 0) {
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
		var optTHROOLD_Core_CompanyID = document.getElementById('optTHROOLD_Core_CompanyID' + i).selectedIndex;
		if(optTHROOLD_Core_CompanyID == 0) {
			alert("Nama Perusahaan Pada Baris pada Perusahaan ke-"+i+" Belum Dipilih!");
			return false;
		}
		var jrow = document.getElementById('count_row_per_pt'+i).value;
		for(n = 1; n <= jrow; n++){
			var optTDROOLD_KategoriDokumen = document.getElementById('optTDROOLD_KategoriDokumen' + i+"_"+n).selectedIndex;
			var txtTDROOLD_NamaDokumen = document.getElementById('txtTDROOLD_NamaDokumen' + i+"_"+n).value;
			var txtTDROOLD_InstansiTerkait = document.getElementById('txtTDROOLD_InstansiTerkait' + i+"_"+n).value;
			var txtTDROOLD_NoDokumen = document.getElementById('txtTDROOLD_NoDokumen' + i+"_"+n).value;
			var txtTDROOLD_TglTerbit = document.getElementById('txtTDROOLD_TglTerbit' + i+"_"+n).value;
			var txtTDROOLD_TglBerakhir = document.getElementById('txtTDROOLD_TglBerakhir' + i+"_"+n).value;
			var txtTDROOLD_Keterangan = document.getElementById('txtTDROOLD_Keterangan' + i+"_"+n).value;
			var Date1 = new Date(txtTDROOLD_TglTerbit);
			var Date2 = new Date(txtTDROOLD_TglBerakhir);

			if(optTDROOLD_KategoriDokumen == 0) {
				alert("Kategori Dokumen pada Baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Dipilih!");
				return false;
			}
			if (txtTDROOLD_NamaDokumen.replace(" ", "") == "")  {
				alert("Nama Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROOLD_InstansiTerkait.replace(" ", "") == "")  {
				alert("Instansi Terkait pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROOLD_NoDokumen.replace(" ", "") == "")  {
				alert("Nomor Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROOLD_TglTerbit.replace(" ", "") == "")  {
				alert("Tanggal Terbit pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi!");
				return false;
			}
			if (txtTDROOLD_TglTerbit.replace(" ", "") != "")  {
				if (checkdate(txtTDROOLD_TglTerbit) == false) {
					return false;
				}
			}
			if (txtTDROOLD_TglBerakhir.replace(" ", "") != "")  {
				if (checkdate(txtTDROOLD_TglBerakhir) == false) {
					return false;
				}
				else {
					if (Date2 < Date1) {
					alert("Tanggal Berakhir Dokumen pada baris ke-" + n + " pada Perusahaan ke-"+i+" Lebih Kecil Daripada Tanggal Terbit Dokumen!");
					return false;
					}
				}
			}
			if (txtTDROOLD_Keterangan.replace(" ", "") == "")  {
				alert("Keterangan pada baris ke-" + n + " pada Perusahaan ke-"+i+" Belum Terisi! Isi dengan Resume Dokumen terkait");
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
			<th colspan=3>Registrasi Dokumen Lainnya (Legal)</th>
		</tr>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2, grup.DocumentGroup_Name,grup.DocumentGroup_ID,
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
				  	ON grup.DocumentGroup_ID='5'
				  LEFT JOIN db_master.M_Employee AS e
                	ON u.User_ID = e.Employee_NIK
                	AND e.Employee_GradeCode IN ('0000000005','06','0000000003','05','04','0000000004')
				  WHERE u.User_ID='$mv_UserID'";
		$field = mysql_fetch_array(mysql_query($query));

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHROOLD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtTHROOLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtTHROOLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input name='txtTHROOLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>
		<!--<tr>
			<td>Grup Dokumen</td>
			<td>-->
				<input name='txtTHROOLD_DocumentGroupID' type='hidden' value='$field[DocumentGroup_ID]'/>
				<!--Dokumen $field[DocumentGroup_Name]
			</td>
		</tr>-->";

		if(!empty($field['User_SPV1']) || !empty($field['User_SPV2'])){
			if( !empty($field['Employee_GradeCode']) && !empty($field['Employee_Grade']) ){
				// $ActionContent .="
				// <tr>
				// 	<td>Perusahaan</td>
				// 	<td>
				// 		<select name='optTHROOLD_Core_CompanyID' id='optTHROOLD_Core_CompanyID' style='width:350px'>
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
				// </tr>";
				$ActionContent .="
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
		// $ActionContent .="<select id='Daftar_KategoriDokumen' style='display:none;'>
		// 	<option value='0'>--- Pilih Kategori Dokumen ---</option>";
		// $query5="SELECT DocumentCategory_ID, DocumentCategory_Name
		// 	FROM db_master.M_DocumentCategory
		// 	WHERE DocumentCategory_Delete_Time IS NULL";
		// $sql5 = mysql_query($query5);
		//
		// while ($field5=mysql_fetch_array($sql5)) {
		// 	$ActionContent .="
		// 	<option value='$field5[DocumentCategory_ID]'>$field5[DocumentCategory_Name]</option>";
		// }
		// $ActionContent .="</select>";

		$code=$_GET["id"];
		$query = "SELECT header.THROOLD_ID,
						 header.THROOLD_RegistrationCode,
						 header.THROOLD_RegistrationDate,
						 header.THROOLD_Information,
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
				  FROM TH_RegistrationOfOtherLegalDocuments header
				  LEFT JOIN M_User u
					ON u.User_ID=header.THROOLD_UserID
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
					ON comp.Company_ID=header.THROOLD_CompanyID
				  LEFT JOIN M_DocumentGroup grup
					ON grup.DocumentGroup_ID=header.THROOLD_DocumentGroupID
				  WHERE header.THROOLD_RegistrationCode='$code'
				  AND header.THROOLD_Delete_Time IS NULL";
		$field = mysql_fetch_array(mysql_query($query));

		$DocGroup=$field['DocumentGroup_ID'];
		$regdate=strtotime($field['THROOLD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);

		$ActionContent .="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Lainnya (Legal)</th>
		</tr>
		<tr>
			<td width='30'>No Pendaftaran</td>
			<td width='70%'>
				<input name='txtTDROOLD_THROOLD_ID' type='hidden' value='$field[THROOLD_ID]'/>
				<input type='hidden' name='txtTDROOLD_THROOLD_RegistrationCode' value='$field[THROOLD_RegistrationCode]' style='width:350px;'/>
				$field[THROOLD_RegistrationCode]
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
		</tr>";
		// <tr>
		// 	<td>Perusahaan</td>
		// 	<td>
		// 		<input type='hidden' id='txtCompID' name='txtCompID' value='$field[Company_ID]'/>
		// 		<input type='hidden' id='txtCompArea' name='txtCompArea' value='$field[Company_Area]'/>
		// 		$field[Company_Name]
		// 	</td>
		// </tr>
		// <tr>
		// 	<td>Grup Dokumen</td>
		// 	<td>
		// 		<input type='hidden' id='txtGrupID' name='txtGrupID' value='$field[DocumentGroup_ID]'/>
		// 		$field[DocumentGroup_Name]
		// 	</td>
		// </tr>
		$ActionContent .="
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHROOLD_Information' id='txtTHROOLD_Information' cols='50' rows='2'>$field[THROOLD_Information]</textarea>
			</td>
		</tr>
		<tr>
			<td>Upload File Excel</td>
			<td>
				<img id='loading' src='images/loading.gif' style='display:none;'><input name='fileToUpload' id='fileToUpload' type='file' size='20' /><input name='getExcel' type='button' onclick='return ajaxFileUpload();' value='Upload' class='button-small' />
				<a href='./sample/Sample_of_Excel_Reg_Other_Legal_Doc.xlsx' target='_blank' class='underline'>[Download Format Excel]</a>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		// <table width='1000' id='detail' class='stripeMe'>
		// <tr>
		// 	<th>Kategori Dokumen</th>
		// 	<th>Nama Dokumen</th>
		// 	<th>Instansi Terkait</th>
		// 	<th>No. Dokumen</th>
		// 	<th>Tanggal Terbit</th>
        //     <th>Tanggal Berakhir</th>
		// </tr>
		// <tr>
		// 	<td>
		// 		<select name='optTDROOLD_KategoriDokumen1' id='optTDROOLD_KategoriDokumen1'>
		// 			<option value='0'>--- Pilih Kategori Dokumen ---</option>
		// 		</select>
		// 	</td>
		// 	<td>
		// 		<input type='text' name='txtTDROOLD_NamaDokumen1' id='txtTDROOLD_NamaDokumen1'/>
		// 	</td>
		// 	<td>
		// 		<input type='text' name='txtTDROOLD_InstansiTerkait1' id='txtTDROOLD_InstansiTerkait1'/>
		// 	</td>
		// 	<td>
		// 		<input type='text' name='txtTDROOLD_NoDokumen1' id='txtTDROOLD_NoDokumen1'/>
		// 	</td>
		// 	<td>
		// 		<input type='text' size='10' readonly='readonly' name='txtTDROOLD_TglTerbit1' id='txtTDROOLD_TglTerbit1' onclick=\"javascript:NewCssCal('txtTDROOLD_TglTerbit1', 'MMddyyyy');\"/>
		// 	</td>
		// 	<td>
		// 		<input type='text' size='10' readonly='readonly' name='txtTDROOLD_TglBerakhir1' id='txtTDROOLD_TglBerakhir1' onclick=\"javascript:NewCssCal('txtTDROOLD_TglBerakhir1', 'MMddyyyy');\"/>
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

			$jenis = "16"; //Dokumen Lainnya (Legal) - Semua Tipe Dokumen

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
		echo "<meta http-equiv='refresh' content='0; url=registration-of-other-legal-documents.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT DISTINCT THROOLD.THROOLD_ID, THROOLD.THROOLD_RegistrationCode, THROOLD.THROOLD_RegistrationDate, u.User_FullName,
 		  		 c.Company_Name,drs.DRS_Description,THROOLD.THROOLD_Status
		  FROM TH_RegistrationOfOtherLegalDocuments THROOLD, M_User u, M_Company c,M_DocumentRegistrationStatus drs
		  WHERE THROOLD.THROOLD_Delete_Time is NULL
		  AND THROOLD.THROOLD_CompanyID=c.Company_ID
		  AND THROOLD.THROOLD_UserID=u.User_ID
		  AND u.User_ID='$mv_UserID'
		  AND THROOLD.THROOLD_Status=drs.DRS_Name
		  AND (c.Company_Delete_Time is NULL OR c.Company_ID='88') /*National*/
		  ORDER BY THROOLD.THROOLD_ID DESC
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
		$regdate=strtotime($field['THROOLD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$resend=($field['THROOLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";
		if($field[5] == "Draft"){
			$link = "registration-of-other-legal-documents.php?act=adddetail&id=".$field[1];
		}else{
			$link = "detail-of-registration-other-legal-documents.php?id=".$decrp->encrypt($field[0]);
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

$query1 = "SELECT THROOLD.THROOLD_ID, THROOLD.THROOLD_RegistrationCode, THROOLD.THROOLD_RegistrationDate, u.User_FullName,
		   		  c.Company_Name, THROOLD.THROOLD_Status
		   FROM TH_RegistrationOfOtherLegalDocuments THROOLD, M_User u, M_Company c
		   WHERE THROOLD.THROOLD_Delete_Time is NULL
		   AND THROOLD.THROOLD_CompanyID=c.Company_ID
		   AND THROOLD.THROOLD_UserID=u.User_ID
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
	echo "<meta http-equiv='refresh' content='0; url=registration-of-other-legal-documents.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_RegistrationOfOtherLegalDocuments THROOLD
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       THROOLD.THROOLD_Delete_UserID='$mv_UserID',THROOLD.THROOLD_Delete_Time=sysdate(),
				   THROOLD.THROOLD_Update_UserID='$mv_UserID',THROOLD.THROOLD_Update_Time=sysdate()
			   WHERE THROOLD.THROOLD_ID='$_POST[txtTDROOLD_THROOLD_ID]'
			   AND THROOLD.THROOLD_RegistrationCode=ct.CT_Code
			   AND THROOLD.THROOLD_Delete_Time IS NULL";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=registration-of-other-legal-documents.php'>";
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
	// $query = "SELECT *
	// 		  FROM M_Company
	// 		  WHERE Company_ID='$_POST[optTHROOLD_Core_CompanyID]'";
	$query = "SELECT Company_Code, Company_ID
			  FROM M_Company
			  WHERE Company_Code='ALL'";
	$field = mysql_fetch_array(mysql_query($query));
	$Company_Code = $field['Company_Code'];
	$Company_ID  = $field['Company_ID'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup
			  WHERE DocumentGroup_ID ='$_POST[txtTHROOLD_DocumentGroupID]'";
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
	//$CT_Code="$newnum/INS/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode registrasi dokumen baru
	// $sql= "INSERT INTO M_CodeTransaction
	// 	   VALUES (NULL,'$CT_Code','$nnum','INS','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
	// 		  	   '$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";

	// Phase 2
	// Kode Registrasi Dokumen
	$CT_Code="$newnum/INS/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode registrasi dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','INS','ALL','$DocumentGroup_Code','$rmonth','$regyear',
			  	   '$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$info = str_replace("<br>", "\n", $_POST['txtTHROOLD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_RegistrationOfOtherLegalDocuments
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID','$Company_ID',
				        '$info','$_POST[txtTHROOLD_DocumentGroupID]',
						'0',NULL,'$mv_UserID', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=registration-of-other-legal-documents.php?act=adddetail&id=$CT_Code'>";
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
	$A_TransactionCode = $_POST['txtTDROOLD_THROOLD_RegistrationCode'];
	$A_ApproverID=$mv_UserID;
	$txtTHROOLD_Information=str_replace("<br>", "\n", $_POST['txtTHROOLD_Information']);

	//Phase 2
	$count_company = $_POST['count_core_companyid'];
	for($c = 1; $c <= $count_company; $c++){
		$Core_CompanyID = $_POST['optTHROOLD_Core_CompanyID'.$c];
		foreach($_POST['optTDROOLD_KategoriDokumen'.$c] as $key => $value){
			$optTDROOLD_KategoriDokumen = $_POST['optTDROOLD_KategoriDokumen'.$c][$key];
			$txtTDROOLD_NamaDokumen = $_POST['txtTDROOLD_NamaDokumen'.$c][$key];
			$txtTDROOLD_InstansiTerkait = $_POST['txtTDROOLD_InstansiTerkait'.$c][$key];
			$txtTDROOLD_NoDokumen = $_POST['txtTDROOLD_NoDokumen'.$c][$key];
			$txtTDROOLD_TglTerbit=date('Y-m-d H:i:s', strtotime($_POST['txtTDROOLD_TglTerbit'.$c][$key]));
			$txtTDROOLD_TglBerakhir=date('Y-m-d H:i:s', strtotime($_POST['txtTDROOLD_TglBerakhir'.$c][$key]));
			if 	(strstr($txtTDROOLD_TglBerakhir, ' ', true)=="1970-01-01"){
				$txtTDROOLD_TglBerakhir=NULL;
			}
			$txtTDROOLD_Keterangan = $_POST['txtTDROOLD_Keterangan'.$c][$key];
			$sql1= "INSERT INTO TD_RegistrationOfOtherLegalDocuments
					SET TDROOLD_THROOLD_ID='$_POST[txtTDROOLD_THROOLD_ID]', TDROOLD_Core_CompanyID='$Core_CompanyID',
						TDROOLD_KategoriDokumenID='$optTDROOLD_KategoriDokumen', TDROOLD_NamaDokumen='$txtTDROOLD_NamaDokumen',
						TDROOLD_InstansiTerkait='$txtTDROOLD_InstansiTerkait', TDROOLD_NoDokumen='$txtTDROOLD_NoDokumen',
						TDROOLD_TglTerbit='$txtTDROOLD_TglTerbit', TDROOLD_TglBerakhir='$txtTDROOLD_TglBerakhir',
						TDROOLD_Keterangan='$txtTDROOLD_Keterangan',
						TDROOLD_Insert_UserID='$mv_UserID', TDROOLD_Insert_Time=sysdate(),
						TDROOLD_Update_UserID='$mv_UserID', TDROOLD_Update_Time=sysdate()";
			$mysqli->query($sql1);
		}
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	foreach ($txtA_ApproverID as $k=>$v) {
		if ($txtA_ApproverID[$k] <> NULL) {
			if ($txtA_ApproverID[$k] <> $mv_UserID) {
				$appbefquery = "SELECT *
						FROM M_Approval
						WHERE A_TransactionCode='{$_POST['txtTDROOLD_THROOLD_RegistrationCode']}'
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

	$jenis = "16"; //Dokumen Lainnya (Legal) - Semua Tipe Dokumen

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

	$sql4= "UPDATE TH_RegistrationOfOtherLegalDocuments
			SET THROOLD_Status='waiting', THROOLD_Information='$txtTHROOLD_Information',
			THROOLD_Update_UserID='$mv_UserID',THROOLD_Update_Time=sysdate()
			WHERE THROOLD_RegistrationCode='$_POST[txtTDROOLD_THROOLD_RegistrationCode]'
			AND THROOLD_Delete_Time IS NULL";
	$mysqli->query($sql4);

	// if($mysqli->query($sql4)) {
	// 	// Kirim Email ke Approver 1
	// 	mail_registration_doc($_POST['txtTDROOLD_THROOLD_RegistrationCode']);
	// 	mail_notif_registration_doc($_POST['txtTDROOLD_THROOLD_RegistrationCode'], $txtA_ApproverID[0], 3);
	// }

	echo "<meta http-equiv='refresh' content='0; url=registration-of-other-legal-documents.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
<script language="JavaScript" type="text/JavaScript">
// document.getElementById('optTDROOLD_KategoriDokumen1').innerHTML = $('#Daftar_KategoriDokumen').html();

// UNTUK DATETIME PICKER
function getDatePublication(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROOLD_DatePublication"+i, "txtTDROOLD_DatePublication"+i, "%m/%d/%Y");
	}

}
function getDateExpired(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROOLD_DateExpired"+i, "txtTDROOLD_DateExpired"+i, "%m/%d/%Y");
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

	// Kategori Dokumen
	var cell0 = row.insertCell(0);
	var sel = document.createElement('select');
	sel.name = 'optTDROOLD_KategoriDokumen' + iteration;
	sel.id = 'optTDROOLD_KategoriDokumen' + iteration;
	sel.innerHTML = $('#Daftar_KategoriDokumen').html();
	cell0.appendChild(sel);

	// Nama Dokumen
	var cell1 = row.insertCell(1);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROOLD_NamaDokumen' + iteration;
	el.id = 'txtTDROOLD_NamaDokumen' + iteration;
	cell1.appendChild(el);

	// Instansi Terkait
	var cell2 = row.insertCell(2);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROOLD_InstansiTerkait' + iteration;
	el.id = 'txtTDROOLD_InstansiTerkait' + iteration;
	cell2.appendChild(el);

    // No Dokumen
	var cell3 = row.insertCell(3);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROOLD_NoDokumen' + iteration;
	el.id = 'txtTDROOLD_NoDokumen' + iteration;
	cell3.appendChild(el);

	// Tanggal Terbit Dokumen
	var cell4 = row.insertCell(4);
	var tglTerbit = document.createElement('input');
	tglTerbit.type = 'text';
	tglTerbit.name = 'txtTDROOLD_TglTerbit' + iteration;
	tglTerbit.id = 'txtTDROOLD_TglTerbit' + iteration;
	tglTerbit.size = '10';
	//el.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	tglTerbit.onclick=function(){ NewCssCal(tglTerbit.id, 'MMddyyyy');  };
	cell4.appendChild(tglTerbit);

	// Tanggal Berakhir Dokumen
	var cell5 = row.insertCell(5);
	var tglBerakhir = document.createElement('input');
	tglBerakhir.type = 'text';
	tglBerakhir.name = 'txtTDROOLD_TglBerakhir' + iteration;
	tglBerakhir.id = 'txtTDROOLD_TglBerakhir' + iteration;
	tglBerakhir.size = '10';
	//elExpDate.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	tglBerakhir.onclick=function(){ NewCssCal(tglBerakhir.id, 'MMddyyyy');  };
	cell5.appendChild(tglBerakhir);
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
