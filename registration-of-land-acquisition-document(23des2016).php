<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.3																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 25 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
=		19/09/2012	: Perubahan Reminder Email																			=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start(); 
?>
<title>Custodian System | Registrasi Dokumen Pembebasan Lahan</title>
<head>
<?PHP 
include ("./config/config_db.php"); 
include ("./include/function.mail.regdocla.php");
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

//untuk membaca file excel yang diupload
function ajaxReadFile(filename)
{
	$.getJSON("uploaddokumen/readxls.php", { filename: escape(filename) }, 
		function(data){
			var item = data;
			$("#button").show();
			var content = "";
					content += "<tr>";	
					content += "<th rowspan='2'>No</th>";	
					content += "<th rowspan='2'>Tanggal</th>";	
					content += "<th rowspan='2'>Blok</th>";	
					content += "<th rowspan='2'>Desa</th>";	
					content += "<th rowspan='2'>Nama Pemilik</th>";	
					content += "<th rowspan='2'>Kelas</th>";	
					content += "<th colspan='3'>Lahan</th>";	
					content += "<th rowspan='2'>Kelas</th>";	
					content += "<th colspan='3'>Tanam Tumbuh</th>";	
					content += "<th rowspan='2'>Total</th>";	
					content += "<th rowspan='2'>Keterangan</th>";	
					<?PHP
						$query = "SELECT * 
									FROM M_LandAcquisitionAttribute 
									WHERE LAA_Delete_Time is NULL 
									ORDER BY LAA_ID ";
						$sql = mysql_query($query);
						$counts=mysql_num_rows($sql);				
					?>
					content += "<th colspan='<?PHP echo "$counts";?>'>Kelengkapan Dokumen</th>";	
					content += "</tr>";	
					content += "<tr>";	
					content += "<th>Ha</th>";	
					content += "<th>Rp/Ha</th>";	
					content += "<th>Nilai (Rp)</th>";	
					content += "<th>Qty</th>";	
					content += "<th>Rp/Pkk</th>";	
					content += "<th>Nilai (Rp)</th>";	
					<?PHP while ($arr = mysql_fetch_array($sql)){ ?>
					content += "<th><?PHP echo "$arr[LAA_ID]"; ?></th>";
					<?PHP } ?>
					content += "</tr>";	
			if ((data["1"]["A"]!="No")||(data["1"]["C"]!="Blok")||(data["1"]["E"]!="Nama Pemilik")||(data["1"]["G"]!="Lahan")||(data["1"]["J"]!="Kelas")||(data["1"]["N"]!="Total")){
				alert ("Format Excel Salah!!");
				$("#button").hide();
			}
			else {
				for (var i in data) {
		
						// data[row][column] references cell from excel document.
						var no = data[i]["A"];
						var TDRGOLAD_DocDate = data[i]["B"];
						var TDRGOLAD_Block = data[i]["C"];
						var TDRGOLAD_Village = data[i]["D"];
						var TDRGOLAD_Owner = data[i]["E"];
						var TDRGOLAD_AreaClass = data[i]["F"];
						var TDRGOLAD_AreaStatement = data[i]["G"];
						var TDRGOLAD_AreaPrice = data[i]["H"];
						var TDRGOLAD_AreaTotalPrice = data[i]["I"];
						var TDRGOLAD_PlantClass = data[i]["J"];
						var TDRGOLAD_PlantQuantity = data[i]["K"];
						var TDRGOLAD_PlantPrice = data[i]["L"];
						var TDRGOLAD_PlantTotalPrice = data[i]["M"];
						var TDRGOLAD_GrandTotal = data[i]["N"];
						
						if((TDRGOLAD_AreaStatement=="-")||(TDRGOLAD_AreaStatement=="")){
							TDRGOLAD_AreaStatement=0;
						}else{
							TDRGOLAD_AreaStatement=parseFloat(TDRGOLAD_AreaStatement).toFixed(2);
						}
						if((TDRGOLAD_AreaPrice=="-")||(TDRGOLAD_AreaPrice=="")){
							TDRGOLAD_AreaPrice=0;
						}else{
							TDRGOLAD_AreaPrice=parseFloat(TDRGOLAD_AreaPrice).toFixed(2);
						}
						if((TDRGOLAD_AreaTotalPrice=="-")||(TDRGOLAD_AreaTotalPrice=="")){
							TDRGOLAD_AreaTotalPrice=0;
						}else{
							TDRGOLAD_AreaTotalPrice=parseFloat(TDRGOLAD_AreaTotalPrice).toFixed(2);
						}			
						if((TDRGOLAD_PlantQuantity=="-")||(TDRGOLAD_PlantQuantity=="")){
							TDRGOLAD_PlantQuantity=0;
						}else{
							TDRGOLAD_PlantQuantity=parseFloat(TDRGOLAD_PlantQuantity).toFixed(2);
						}
						if((TDRGOLAD_PlantPrice=="-")||(TDRGOLAD_PlantPrice=="")){
							TDRGOLAD_PlantPrice=0;
						}else{
							TDRGOLAD_PlantPrice=parseFloat(TDRGOLAD_PlantPrice).toFixed(2);
						}
						if((TDRGOLAD_PlantTotalPrice=="-")||(TDRGOLAD_PlantTotalPrice=="")){
							TDRGOLAD_PlantTotalPrice=0;
						}else{
							TDRGOLAD_PlantTotalPrice=parseFloat(TDRGOLAD_PlantTotalPrice).toFixed(2);
						}
						if((TDRGOLAD_GrandTotal=="-")||(TDRGOLAD_GrandTotal=="")){
							TDRGOLAD_GrandTotal=0;
						}else{
							TDRGOLAD_GrandTotal=parseFloat(TDRGOLAD_GrandTotal).toFixed(2);
						}
					
					if (i > 2){	
						content += "<tr>";	
						content += "<td width='50'><input type=text name=txtNumber"+i+" id=txtNumber"+i+" value='"+no+"' size='1' ></td>";
						content += "<td width='100'><input type=text name=txtTDRGOLAD_DocDate"+i+" id=txtTDRGOLAD_DocDate"+i+" value='"+TDRGOLAD_DocDate+"' size='7' onclick=\"javascript:NewCssCal('txtTDRGOLAD_DocDate"+i+"', 'MMddyyyy');\"></td>";
						content += "<td width='160'><input type=text name=txtTDRGOLAD_Block"+i+" id=txtTDRGOLAD_Block"+i+" value='"+TDRGOLAD_Block+"' size='15' ></td>";
						content += "<td width='160'><input type=text name=txtTDRGOLAD_Village"+i+" id=txtTDRGOLAD_Village"+i+" value='"+TDRGOLAD_Village+"' size='15' ></td>";
						content += "<td width='160'><input type=text name=txtTDRGOLAD_Owner"+i+" id=txtTDRGOLAD_Owner"+i+" value='"+TDRGOLAD_Owner+"' size='15' ></td>";
						content += "<td width='40'><input type=text name=txtTDRGOLAD_AreaClass"+i+" id=txtTDRGOLAD_AreaClass"+i+" value='"+TDRGOLAD_AreaClass+"' size='3'></td>";
						content += "<td width='40'><input type=text name=txtTDRGOLAD_AreaStatement"+i+" id=txtTDRGOLAD_AreaStatement"+i+" value='"+TDRGOLAD_AreaStatement+"' size='10' style='text-align:right' onchange='countTotal("+i+");'></td>";
						content += "<td width='50'><input type=text name=txtTDRGOLAD_AreaPrice"+i+" id=txtTDRGOLAD_AreaPrice"+i+" value='"+TDRGOLAD_AreaPrice+"' size='10' style='text-align:right' onchange='countTotal("+i+");'></td>";
						content += "<td width='50'><input type=text name=txtTDRGOLAD_AreaTotalPrice"+i+" id=txtTDRGOLAD_AreaTotalPrice"+i+" value='"+TDRGOLAD_AreaTotalPrice+"' size='10' style='text-align:right' onchange='countGrandTotal("+i+");'></td>";
						content += "<td width='50'><input type=text name=txtTDRGOLAD_PlantClass"+i+" id=txtTDRGOLAD_PlantClass"+i+" value='"+TDRGOLAD_PlantClass+"' size='3' > </td>";
						content += "<td width='50'><input type=text name=txtTDRGOLAD_PlantQuantity"+i+" id=txtTDRGOLAD_PlantQuantity"+i+" value='"+TDRGOLAD_PlantQuantity+"' size='10' style='text-align:right' onchange='countTotal("+i+");'></td>";
						content += "<td width='50'><input type=text name=txtTDRGOLAD_PlantPrice"+i+" id=txtTDRGOLAD_PlantPrice"+i+" value='"+TDRGOLAD_PlantPrice+"' size='10' style='text-align:right' onchange='countTotal("+i+");'></td>";
						content += "<td width='50'><input type=text name=txtTDRGOLAD_PlantTotalPrice"+i+" id=txtTDRGOLAD_PlantTotalPrice"+i+" value='"+TDRGOLAD_PlantTotalPrice+"' size='10' style='text-align:right' onchange='countGrandTotal("+i+");'></td>";
						content += "<td width='50'><input type=text name=txtTDRGOLAD_GrandTotal"+i+" id=txtTDRGOLAD_GrandTotal"+i+" value='"+TDRGOLAD_GrandTotal+"' size='10' style='text-align:right' ></td>";
						content += "<td width='170'><textarea type=text name=txtTDRGOLAD_Information"+i+" id=txtTDRGOLAD_Information"+i+"></textarea></td>";
		<?PHP				
		$query = "SELECT * FROM M_LandAcquisitionAttribute WHERE LAA_Delete_Time is NULL ORDER BY LAA_ID ";
		$sql = mysql_query($query);
		$count=mysql_num_rows($sql);
		while ($arr=mysql_fetch_array($sql)){
		?>
						var Jenis="<?PHP echo "$arr[LAA_ID]"; ?>";
						content += "<td width='50'><select name=optKelengkapan"+Jenis+i+" id=optKelengkapan"+Jenis+i+">";
		<?PHP				
		$s_query = "SELECT * FROM M_LandAcquisitionAttributeStatus WHERE LAAS_Delete_Time is NULL ORDER BY LAAS_ID ";
		$s_sql = mysql_query($s_query);
		while ($s_arr=mysql_fetch_array($s_sql)){
		?>
						var optValue="<?PHP echo "$s_arr[LAAS_ID]"; ?>";
						var optText="<?PHP echo "$s_arr[LAAS_Symbol]"; ?>";
						content += "<option value='"+optValue+"' >"+optText+"</option>";
		<?PHP } ?>
						content += "</select></td>";
		<?PHP } ?>
						
						
						content += "</tr>";	
					}				
				};
			
				var jKelengkapan="<?PHP echo "$count"; ?>";
				document.getElementById('jKelengkapan').value=jKelengkapan;
				document.getElementById('maxValue').value=i;
				$('#row1').append(content);
			}
		});
}

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;							

	var optTHRGOLAD_CompanyID = document.getElementById('optTHRGOLAD_CompanyID').selectedIndex;
	var txtTHRGOLAD_Phase = document.getElementById('txtTHRGOLAD_Phase').value;
	var txtTHRGOLAD_Period = document.getElementById('txtTHRGOLAD_Period').value;
		
		if(optTHRGOLAD_CompanyID == 0) {
			alert("Perusahaan Belum Dipilih!");
			returnValue = false;
		}
		if (txtTHRGOLAD_Phase.replace(" ", "") == "") {	
			alert("Tahap Pembebasan Lahan Belum Terisi!");
			returnValue = false;
		}
		else {
			if(isNaN(txtTHRGOLAD_Phase)){
				alert ("Tahap Harus Berupa Angka [0-9]!");
				returnValue = false;
			}
		}
		if (txtTHRGOLAD_Period.replace(" ", "") == "") {	
			alert("Periode Pembebasan Lahan Belum Terisi!");
			returnValue = false;
		}

	return returnValue;
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

// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var returnValue;
	returnValue = true;							
	var maxValue = document.getElementById('maxValue').value;
	var regDate = document.getElementById('regDate').value;
	
	for (i = 3; i <= maxValue; i++){
		var txtTDRGOLAD_DocDate = document.getElementById('txtTDRGOLAD_DocDate' + i).value;
		var txtTDRGOLAD_Block = document.getElementById('txtTDRGOLAD_Block' + i).value;
		var txtTDRGOLAD_Village = document.getElementById('txtTDRGOLAD_Village' + i).value;
		var txtTDRGOLAD_Owner = document.getElementById('txtTDRGOLAD_Owner' + i).value;
		var txtTDRGOLAD_AreaClass = document.getElementById('txtTDRGOLAD_AreaClass' + i).value;
		var txtTDRGOLAD_AreaStatement = document.getElementById('txtTDRGOLAD_AreaStatement' + i).value;
		var txtTDRGOLAD_AreaPrice = document.getElementById('txtTDRGOLAD_AreaPrice' + i).value;
		var Date1 = new Date(regDate);
		var Date2 = new Date(txtTDRGOLAD_DocDate);
		var row=i-2;
		
		
		if(txtTDRGOLAD_DocDate.replace(" ", "") == "") {
			alert("Tanggal Dokumen Pada Baris ke-" + row + " Belum Ditentukan!");
			return false
		}
		else {
			if (checkdate(txtTDRGOLAD_DocDate,row) == false) {
				return false
			}
			else {
				if (Date2 > Date1) {
					alert("Tanggal Dokumen pada baris ke-" + row + " Lebih Besar Daripada Tanggal Registrasi!");
					return false
				}
			}
		}
		if (txtTDRGOLAD_Block.replace(" ", "") == "")  {	
			alert("Blok pada baris ke-" + row + " Belum Terisi!");
			return false
		}
		if (txtTDRGOLAD_Village.replace(" ", "") == "")  {	
			alert("Desa pada baris ke-" + row + " Belum Terisi!");
			return false
		} 
		if (txtTDRGOLAD_Owner.replace(" ", "") == "")  {	
			alert("Nama Pemilik pada baris ke-" + row + " Belum Terisi!");
			return false
		}
		if (txtTDRGOLAD_AreaClass.replace(" ", "") == "")  {	
			alert("Kelas Area pada baris ke-" + row + " Belum Terisi!");
			return false
		}
		if (txtTDRGOLAD_AreaStatement.replace(" ", "") == "")  {	
			alert("Luas Area pada baris ke-" + row + " Belum Terisi!");
			return false
		}
		if (txtTDRGOLAD_AreaPrice.replace(" ", "") == "")  {	
			alert("Rp/Ha pada baris ke-" + row + " Belum Terisi!");
			return false
		}
	}
	return true
}

//PERHITUNGAN TOTAL
function countTotal(rowNo){
	document.getElementById('txtTDRGOLAD_AreaTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txtTDRGOLAD_AreaStatement'+rowNo).value) * parseFloat(document.getElementById('txtTDRGOLAD_AreaPrice'+rowNo).value)).toFixed(2);
	document.getElementById('txtTDRGOLAD_PlantTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txtTDRGOLAD_PlantQuantity'+rowNo).value) * parseFloat(document.getElementById('txtTDRGOLAD_PlantPrice'+rowNo).value)).toFixed(2);
	countGrandTotal(rowNo);
}
function countGrandTotal(rowNo){
	document.getElementById('txtTDRGOLAD_GrandTotal'+rowNo).value=parseFloat(parseFloat(document.getElementById('txtTDRGOLAD_AreaTotalPrice'+rowNo).value) + parseFloat(document.getElementById('txtTDRGOLAD_PlantTotalPrice'+rowNo).value)).toFixed(2);
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
			<th colspan=3>Registrasi Dokumen Pembebasan Lahan</th>
		</tr>";
		
		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID, 
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName, 
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2,grup.DocumentGroup_Name,grup.DocumentGroup_ID
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
					ON grup.DocumentGroup_ID='3'
				  WHERE u.User_ID='$_SESSION[User_ID]'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);
		
		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHRGOLAD_UserID' type='hidden' value='$_SESSION[User_ID]'/>
				$field[FullName]
			</td>
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
			<td>Grup Dokumen</td>
			<td>
				<input name='txtTHRGOLAD_DocumentGroupID' type='hidden' value='$field[DocumentGroup_ID]'/>
				$field[DocumentGroup_Name]
			</td>
		</tr>";
		
		if($field['User_SPV1']||$field['User_SPV2']){
			$ActionContent .="	
			<tr>
				<td>Perusahaan</td>
				<td> 
					<select name='optTHRGOLAD_CompanyID' id='optTHRGOLAD_CompanyID' style='width:350px'>
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
				<td>Tahap</td>
				<td>
					<input type='text' name='txtTHRGOLAD_Phase' id='txtTHRGOLAD_Phase' size='7'/>
				</td>
			</tr>
			<tr>
				<td>Periode (MM/DD/YYYY)</td>
				<td>
					<input type='text' name='txtTHRGOLAD_Period' id='txtTHRGOLAD_Period'  size='7' onclick=\"javascript:NewCssCal('txtTHRGOLAD_Period', 'MMddyyyy');\"/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name='txtTHRGOLAD_Information' id='txtTHRGOLAD_Information' cols='50' rows='2'></textarea></td>
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
	elseif($act=='adddetail') {
		$code=$_GET["id"];
		
		$query = "SELECT header.THRGOLAD_ID, 
						 header.THRGOLAD_RegistrationCode, 
						 header.THRGOLAD_RegistrationDate,
						 header.THRGOLAD_Period,
						 header.THRGOLAD_Phase,
						 header.THRGOLAD_Information,
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
				  FROM TH_RegistrationOfLandAcquisitionDocument header
				  LEFT JOIN M_User u
					ON u.User_ID=header.THRGOLAD_UserID
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
					ON comp.Company_ID=header.THRGOLAD_CompanyID
				  LEFT JOIN M_DocumentGroup grup
					ON grup.DocumentGroup_ID='3' 	
				  WHERE header.THRGOLAD_RegistrationCode='$code'
				  AND header.THRGOLAD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);
		
		$CompanyName=$field[Company_Name];
		$regdate=strtotime($field['THRGOLAD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$fregdate1=date("m/d/Y", $regdate);
		$perdate=strtotime($field['THRGOLAD_Period']);
		$fperdate=date("j M Y", $perdate);

		$ActionContent ="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Pembebasan Lahan</th>
		</tr>
		<tr>
			<td width='30'>No Pendaftaran</td>
			<td width='70%'>
				<input name='txtTDRGOLAD_THRGOLAD_ID' type='hidden' value='$field[THRGOLAD_ID]'/>
				<input type='hidden' name='txtTDRGOLAD_THRGOLAD_RegistrationCode' value='$field[THRGOLAD_RegistrationCode]'/>
				$field[THRGOLAD_RegistrationCode]
			</td>
		</tr>
		<tr>
			<td>Tanggal Pendaftaran</td>
			<td><input type='hidden' id='regDate' value='$fregdate1'/>$fregdate</td>
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
			<td>Grup</td>
			<td>$field[DocumentGroup_Name]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>$CompanyName</td>
		</tr>
		<tr>
			<td>Tahap</td>
			<td>$field[THRGOLAD_Phase]</td>
		</tr>
		<tr>
			<td>Periode</td>
			<td>$fperdate</td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHRGOLAD_Information' id='txtTHRGOLAD_Information' cols='50' rows='2'>$field[THRGOLAD_Information]</textarea>
			</td>
		</tr>
		<tr>
			<td>Upload File Excel</td>
			<td>
				<img id='loading' src='images/loading.gif' style='display:none;'><input name='fileToUpload' id='fileToUpload' type='file' size='20' /><input name='getExcel' type='button' onclick='return ajaxFileUpload();' value='Upload' class='button-small' />
				<a href='./sample/SampleExcelRegLADoc.xlsx' target='_blank' class='underline'>[Download Format Excel]</a>
			</td>
		</tr>
		</table>
		
		<div style='space'>&nbsp;</div>";
		
		// Bagian Judul Tabel
		$ActionContent .="
		<table width='2220' id='row1' class='stripeMe' border=1>";

		$ActionContent .="	
		<input type=hidden name='maxValue' id='maxValue'>
		<input type=hidden name='jKelengkapan' id='jKelengkapan'>
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
						-- AND Employee_Position NOT LIKE '%SECTION%'
						-- AND Employee_Position NOT LIKE '%SUB DEP%'";
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
		echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;
if(isset($_GET['page'])) 
    $noPage = $_GET['page'];
else 
	$noPage = 1;
	
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT throld.THRGOLAD_ID, throld.THRGOLAD_RegistrationCode, throld.THRGOLAD_RegistrationDate, u.User_FullName,
 		  		 c.Company_Name, drs.DRS_Description, throld.THRGOLAD_RegStatus 
		  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_User u, M_Company c,M_DocumentRegistrationStatus drs
		  WHERE throld.THRGOLAD_Delete_Time is NULL 
		  AND throld.THRGOLAD_CompanyID=c.Company_ID 
		  AND throld.THRGOLAD_UserID=u.User_ID 
		  AND u.User_ID='$_SESSION[User_ID]'
		  AND throld.THRGOLAD_RegStatus=drs.DRS_Name
		  ORDER BY throld.THRGOLAD_ID DESC
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
	while ($field = mysql_fetch_array($sql)) {
		$regdate=strtotime($field['THRGOLAD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$resend=($field['THRGOLAD_RegStatus']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-registration-land-acquisition-document.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$field[5]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="
	</table>
";

$query1= "SELECT throld.THRGOLAD_ID, throld.THRGOLAD_RegistrationCode, throld.THRGOLAD_RegistrationDate, 
			     u.User_FullName, c.Company_Name, throld.THRGOLAD_RegStatus 
		  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_User u, M_Company c 
		  WHERE throld.THRGOLAD_Delete_Time is NULL 
		  AND throld.THRGOLAD_CompanyID=c.Company_ID 
		  AND throld.THRGOLAD_UserID=u.User_ID 
		  AND u.User_ID='$_SESSION[User_ID]' 
		  ORDER BY throld.THRGOLAD_ID DESC";
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
/*echo "<script type='text/javascript'>
		alert('". $_POST[cancel] . "=" . $_POST[canceldetail] . "=" . $_POST[addheader] . "=" . $_POST[adddetail] . "=" ."');
		</script>";
*/
/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	
	$cd_query="UPDATE M_CodeTransaction ct, TH_RegistrationOfLandAcquisitionDocument throld
			   SET ct.CT_Delete_UserID='$_SESSION[User_ID]',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$_SESSION[User_ID]',ct.CT_Update_Time=sysdate(),
			       throld.THRGOLAD_Delete_UserID='$_SESSION[User_ID]',throld.THRGOLAD_Delete_Time=sysdate(),
				   throld.THRGOLAD_Update_UserID='$_SESSION[User_ID]',throld.THRGOLAD_Update_Time=sysdate()
			   WHERE throld.THRGOLAD_ID='$_POST[txtTDRGOLAD_THRGOLAD_ID]'
			   AND throld.THRGOLAD_RegistrationCode=ct.CT_Code
			   AND throld.THRGOLAD_Delete_Time IS NULL";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
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
			  WHERE Company_ID='$_POST[optTHRGOLAD_CompanyID]'";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$Company_Code=$field['Company_Code'];
	
	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup 
			  WHERE DocumentGroup_ID ='$_POST[txtTHRGOLAD_DocumentGroupID]'";
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
		$txtTHRGOLAD_Period=$_POST["txtTHRGOLAD_Period"];
		$txtTHRGOLAD_Period=date('Y-m-d H:i:s', strtotime($txtTHRGOLAD_Period));
		$info = str_replace("<br>", "\n", $_POST['txtTHRGOLAD_Information']);
	
		//Insert Header Dokumen 
		$sql1= "INSERT INTO TH_RegistrationOfLandAcquisitionDocument 
				VALUES (NULL,'$CT_Code',sysdate(),'$_SESSION[User_ID]','$_POST[optTHRGOLAD_CompanyID]',
				        '$_POST[txtTHRGOLAD_Phase]','$txtTHRGOLAD_Period','0','$info',
						'0',NULL,'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$count=$_POST[maxValue];
	$jKelengkapan=$_POST[jKelengkapan];
	$txtTHRGOLAD_Information=str_replace("<br>", "\n",$_POST[txtTHRGOLAD_Information]);
	//die();
	
	for ($i=3 ; $i<=$count ; $i++) {
		$txtNumber=$_POST["txtNumber".$i];
		$txtTDRGOLAD_DocDate=$_POST["txtTDRGOLAD_DocDate".$i];
		$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($txtTDRGOLAD_DocDate));	
		$txtTDRGOLAD_Block=$_POST["txtTDRGOLAD_Block".$i];
		$txtTDRGOLAD_Village=$_POST["txtTDRGOLAD_Village".$i];
		$txtTDRGOLAD_Owner=$_POST["txtTDRGOLAD_Owner".$i];
		$txtTDRGOLAD_AreaClass=$_POST["txtTDRGOLAD_AreaClass".$i];
		$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaStatement".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaStatement".$i])==false))?"0":$_POST["txtTDRGOLAD_AreaStatement".$i];
		$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_AreaPrice".$i];
		$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaTotalPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaTotalPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_AreaTotalPrice".$i];
		$txtTDRGOLAD_PlantClass=$_POST["txtTDRGOLAD_PlantClass".$i];
		$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantQuantity".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantQuantity".$i])==false))?"0":$_POST["txtTDRGOLAD_PlantQuantity".$i];
		$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_PlantPrice".$i];
		$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantTotalPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantTotalPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_PlantTotalPrice".$i];
		$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txtTDRGOLAD_GrandTotal".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_GrandTotal".$i])==false))?"0":$_POST["txtTDRGOLAD_GrandTotal".$i];
		$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txtTDRGOLAD_Information".$i]);

		$sql1= "INSERT INTO TD_RegistrationOfLandAcquisitionDocument 
				VALUES (NULL,'$_POST[txtTDRGOLAD_THRGOLAD_ID]', '$txtTDRGOLAD_DocDate', '$txtTDRGOLAD_Block', 
				        '$txtTDRGOLAD_Village', '$txtTDRGOLAD_Owner', '$txtTDRGOLAD_AreaClass', 
						REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
						'$txtTDRGOLAD_PlantClass',REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
						REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),'0','$txtTDRGOLAD_Information',
						'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)){
			$s_sql="SELECT * 
					FROM TD_RegistrationOfLandAcquisitionDocument
					WHERE TDRGOLAD_THRGOLAD_ID='$_POST[txtTDRGOLAD_THRGOLAD_ID]'
					AND TDRGOLAD_DocDate='$txtTDRGOLAD_DocDate'
					AND TDRGOLAD_Block='$txtTDRGOLAD_Block'
					AND TDRGOLAD_Village='$txtTDRGOLAD_Village'
					AND TDRGOLAD_Owner='$txtTDRGOLAD_Owner'
					AND TDRGOLAD_AreaClass='$txtTDRGOLAD_AreaClass'
					AND FORMAT(TDRGOLAD_AreaStatement,2)=FORMAT('$txtTDRGOLAD_AreaStatement',2)
					AND FORMAT(TDRGOLAD_AreaPrice,0)=FORMAT('$txtTDRGOLAD_AreaPrice',0)
					AND FORMAT(TDRGOLAD_AreaTotalPrice,0)=FORMAT('$txtTDRGOLAD_AreaTotalPrice',0)
					AND TDRGOLAD_PlantClass='$txtTDRGOLAD_PlantClass'
					AND FORMAT(TDRGOLAD_PlantQuantity,2)=FORMAT('$txtTDRGOLAD_PlantQuantity',2)
					AND FORMAT(TDRGOLAD_PlantPrice,0)=FORMAT('$txtTDRGOLAD_PlantPrice',0)
					AND FORMAT(TDRGOLAD_PlantTotalPrice,0)=FORMAT('$txtTDRGOLAD_PlantTotalPrice',0)
					AND FORMAT(TDRGOLAD_GrandTotal,0)=FORMAT('$txtTDRGOLAD_GrandTotal',0)
					AND TDRGOLAD_Information='$txtTDRGOLAD_Information'";					
					
			$s_query=mysql_query($s_sql);
			$s_arr=mysql_fetch_array($s_query);
			for ($j=1 ; $j<=$jKelengkapan ; $j++) {
				$optKelengkapan=$_POST["optKelengkapan".$j.$i];
				
				$k_sql="INSERT INTO TD_RegistrationOfLandAcquisitionDocumentDetail 
						VALUES (NULL,'$s_arr[TDRGOLAD_ID]', '$j', '$optKelengkapan',
						'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
				$mysqli->query($k_sql);
	
			}
		}
	}
	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$_SESSION['User_ID']){
				$appbefquery="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]'
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
							VALUES (NULL,'$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]', '$txtA_ApproverID[$i]', 
									'$step', '1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
									sysdate(),NULL,NULL)";
					$mysqli->query($sql2);
					
					$sa_query="SELECT *
								   FROM M_Approval
								   WHERE A_TransactionCode='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]'
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
			WHERE A_TransactionCode ='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]' 
			AND A_Step='1'";
			
	$sql4= "UPDATE TH_RegistrationOfLandAcquisitionDocument 
			SET THRGOLAD_RegStatus='waiting', THRGOLAD_Information='$txtTHRGOLAD_Information',
			THRGOLAD_Update_UserID='$_SESSION[User_ID]',THRGOLAD_Update_Time=sysdate()
			WHERE THRGOLAD_RegistrationCode='$_POST[txtTDRGOLAD_THRGOLAD_RegistrationCode]'
			AND THRGOLAD_Delete_Time IS NULL";
			
	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		$id=$_POST['txtTDRGOLAD_THRGOLAD_RegistrationCode'];
		mail_registration_doc($id);
		//echo "AAAA";
	
		echo "<meta http-equiv='refresh' content='0; url=registration-of-land-acquisition-document.php'>";
	}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>