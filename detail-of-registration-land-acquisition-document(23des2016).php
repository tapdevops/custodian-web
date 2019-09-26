<?PHP 
session_start(); 
session_register("Referer");$_SESSION['Referer'] = $_SERVER["REQUEST_URI"];
?>
<title>Custodian System | Detail Registrasi Dokumen Pembebasan Lahan</title>
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
			$("#rowButton").show();
			$("#button").hide();
			$("#txtTHRGOLAD_PhaseR").show();
			$("#txtTHRGOLAD_PeriodR").show();
			$("#detailtable").hide();
			$("#headerdetailtable").hide();
			$("#txtTHRGOLAD_Phase").hide();
			$("#txtTHRGOLAD_Period").hide();
			
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
				$("#rowButton").hide();
				$("#button").show();
				$("#txtTHRGOLAD_PhaseR").hide();
				$("#txtTHRGOLAD_PeriodR").hide();
				$("#detailtable").show();
				$("#headerdetailtable").show();
				$("#txtTHRGOLAD_Phase").show();
				$("#txtTHRGOLAD_Period").show();
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
						content += "<td width='50'><input type=text name=txt_Number"+i+" id=txt_Number"+i+" value='"+no+"' size='1' ></td>";
						content += "<td width='100'><input type=text name=txt_DocDate"+i+" id=txt_DocDate"+i+" value='"+TDRGOLAD_DocDate+"' size='7' onclick=\"javascript:NewCssCal('txt_DocDate"+i+"', 'MMddyyyy');\"></td>";
						content += "<td width='160'><input type=text name=txt_Block"+i+" id=txt_Block"+i+" value='"+TDRGOLAD_Block+"' size='15' ></td>";
						content += "<td width='160'><input type=text name=txt_Village"+i+" id=txt_Village"+i+" value='"+TDRGOLAD_Village+"' size='15' ></td>";
						content += "<td width='160'><input type=text name=txt_Owner"+i+" id=txt_Owner"+i+" value='"+TDRGOLAD_Owner+"' size='15' ></td>";
						content += "<td width='40'><input type=text name=txt_AreaClass"+i+" id=txt_AreaClass"+i+" value='"+TDRGOLAD_AreaClass+"' size='3'></td>";
						content += "<td width='40'><input type=text name=txt_AreaStatement"+i+" id=txt_AreaStatement"+i+" value='"+TDRGOLAD_AreaStatement+"' size='10' style='text-align:right' onchange='countTotalA("+i+");'></td>";
						content += "<td width='50'><input type=text name=txt_AreaPrice"+i+" id=txt_AreaPrice"+i+" value='"+TDRGOLAD_AreaPrice+"' size='10' style='text-align:right'  onchange='countTotalA("+i+");'></td>";
						content += "<td width='50'><input type=text name=txt_AreaTotalPrice"+i+" id=txt_AreaTotalPrice"+i+" value='"+TDRGOLAD_AreaTotalPrice+"' size='10' style='text-align:right' onchange='countGrandTotalA("+i+");'></td>";
						content += "<td width='50'><input type=text name=txt_PlantClass"+i+" id=txt_PlantClass"+i+" value='"+TDRGOLAD_PlantClass+"' size='3'> </td>";
						content += "<td width='50'><input type=text name=txt_PlantQuantity"+i+" id=txt_PlantQuantity"+i+" value='"+TDRGOLAD_PlantQuantity+"' size='10' style='text-align:right' onchange='countTotalA("+i+");'></td>";
						content += "<td width='50'><input type=text name=txt_PlantPrice"+i+" id=txt_PlantPrice"+i+" value='"+TDRGOLAD_PlantPrice+"' size='10' style='text-align:right' onchange='countTotalA("+i+");'></td>";
						content += "<td width='50'><input type=text name=txt_PlantTotalPrice"+i+" id=txt_PlantTotalPrice"+i+" value='"+TDRGOLAD_PlantTotalPrice+"' size='10' style='text-align:right' onchange='countGrandTotalA("+i+");'></td>";
						content += "<td width='50'><input type=text name=txt_GrandTotal"+i+" id=txt_GrandTotal"+i+" value='"+TDRGOLAD_GrandTotal+"' size='10' style='text-align:right' ></td>";
						content += "<td width='170'><textarea name=txt_Information"+i+" id=txt_Information"+i+"></textarea></td>";
		<?PHP				
		$query = "SELECT * FROM M_LandAcquisitionAttribute WHERE LAA_Delete_Time is NULL ORDER BY LAA_ID ";
		$sql = mysql_query($query);
		$count=mysql_num_rows($sql);
		while ($arr=mysql_fetch_array($sql)){
		?>
						var Jenis="<?PHP echo "$arr[LAA_ID]"; ?>";
						content += "<td width='50'><select name=kelengkapan"+Jenis+i+" id=kelengkapan"+Jenis+i+">";
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
				document.getElementById('jKelengkapanA').value=jKelengkapan;
				document.getElementById('maxValueA').value=i;
				document.getElementById('txtTHRGOLAD_Revision').value=parseInt(document.getElementById('txtTHRGOLAD_Revision').value)+parseInt(1);
						
				$('#row1').append(content);
			}
		});
		
}
// MENAMPILKAN BARIS UNTUK UPLOAD DOKUMEN EXCEL
function showUpload () {
	$("#rowUpload").show();
}
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;							

	var txtTHRGOLAD_Phase = document.getElementById('txtTHRGOLAD_Phase').value;
	var txtTHRGOLAD_Period = document.getElementById('txtTHRGOLAD_Period').value;
		
		if(txtTHRGOLAD_Phase.replace(" ", "") == "") {
			alert("Tahap Pembebasan Lahan Belum Ditentukan!");
			returnValue = false;
		}
		
		if (txtTHRGOLAD_Period.replace(" ", "") == "") {	
			alert("Periode Pembebasan Lahan Belum Ditentukan!");
			returnValue = false;
		}
	if (typeof  document.DLA_detail.optTHRGOLAD_RegStatus != 'undifined') {
		var optTHRGOLAD_RegStatus = document.getElementById('optTHRGOLAD_RegStatus').selectedIndex;
		var txtTHRGOLAD_RegStatusReason = document.getElementById('txtTHRGOLAD_RegStatusReason').value;
		
		if(optTHRGOLAD_RegStatus == 0) {			
			alert("Persetujuan Belum Dipilih!");
			returnValue = false;
		}
		if(optTHRGOLAD_RegStatus == 2) {
			if (txtTHRGOLAD_RegStatusReason.replace(" ", "") == "") {	
				alert("Keterangan Persetujuan Harus Diisi Apabila Anda Menolak Dokumen Ini!");
				returnValue = false;
			}
		}
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
	var confirmSave=confirm("Anda Yakin Ingin Menyimpan Transaksi Ini?\nTransaksi Yang Tersimpan Akan Langsung Ditambahkan Sebagai Dokumen Baru.");
	if(confirmSave==true){
		var returnValue;
		returnValue = true;							
		var maxValue = document.getElementById('maxValueA').value;
		var regDate = document.getElementById('txtDL_RegTime').value;
		
		for (i = 3; i <= maxValue; i++){
			var txt_DocDate = document.getElementById('txt_DocDate' + i).value;
			var txt_Block = document.getElementById('txt_Block' + i).value;
			var txt_Village = document.getElementById('txt_Village' + i).value;
			var txt_Owner = document.getElementById('txt_Owner' + i).value;
			var txt_AreaClass = document.getElementById('txt_AreaClass' + i).value;
			var txt_AreaStatement = document.getElementById('txt_AreaStatement' + i).value;
			var txt_AreaPrice = document.getElementById('txt_AreaPrice' + i).value;
			var Date1 = new Date(regDate);
			var Date2 = new Date();
			var row=i-2;
			
			
			if(txt_DocDate.replace(" ", "") == "") {
				alert("Tanggal Dokumen Pada Baris ke-" + row + " Belum Ditentukan!");
				return false
			}
			else {
				if (checkdate(txt_DocDate,row) == false) {
					return false
				}
			}
			if (txt_Block.replace(" ", "") == "")  {	
				alert("Blok pada baris ke-" + row + " Belum Terisi!");
				return false
			}
			if (txt_Village.replace(" ", "") == "")  {	
				alert("Desa pada baris ke-" + row + " Belum Terisi!");
				return false
			} 
			if (txt_Owner.replace(" ", "") == "")  {	
				alert("Nama Pemilik pada baris ke-" + row + " Belum Terisi!");
				return false
			}
			if (txt_AreaStatement.replace(" ", "") == "")  {	
				alert("Luas Area pada baris ke-" + row + " Belum Terisi!");
				return false
			}
			if (txt_AreaPrice.replace(" ", "") == "")  {	
				alert("Rp/Ha pada baris ke-" + row + " Belum Terisi!");
				return false
			}
		}
		return true
	}else{
		return false
	}
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

function countTotalA(rowNo){
	document.getElementById('txt_AreaTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txt_AreaStatement'+rowNo).value) * parseFloat(document.getElementById('txt_AreaPrice'+rowNo).value)).toFixed(2);
	document.getElementById('txt_PlantTotalPrice'+rowNo).value=parseFloat(parseFloat(document.getElementById('txt_PlantQuantity'+rowNo).value) * parseFloat(document.getElementById('txt_PlantPrice'+rowNo).value)).toFixed(2);
	countGrandTotalA(rowNo);
}
function countGrandTotalA(rowNo){
	document.getElementById('txt_GrandTotal'+rowNo).value=parseFloat(parseFloat(document.getElementById('txt_AreaTotalPrice'+rowNo).value) + parseFloat(document.getElementById('txt_PlantTotalPrice'+rowNo).value)).toFixed(2);
}
</script>
</head>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($_SESSION['User_ID'])) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
	
require_once "./include/template.inc";

$decrp = new custodian_encryp;
$page=new Template();

$act=$decrp->decrypt($_GET["act"]);
$DocID=$decrp->decrypt($_GET["id"]);

if (($_GET['ati'])&&($_GET['rdm'])){
	$A_ID=$decrp->decrypt($_GET['ati']);
	$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);
	
	$query = "SELECT *
			  FROM L_ApprovalRandomCode
			  WHERE ARC_AID='$A_ID'
			  AND ARC_RandomCode='$ARC_RandomCode'";
	$sql = mysql_query($query);
	$num = mysql_num_rows($sql);
	if ($num==0)
		echo "<meta http-equiv='refresh' content='0; url=home.php'>";	
}
		

// Cek apakah user berikut memiliki hak untuk approval
$cApp_query="SELECT DISTINCT dra.A_ApproverID
		  	 FROM TH_RegistrationOfLandAcquisitionDocument throld, M_Approval dra
			 WHERE throld.THRGOLAD_Delete_Time is NULL 
			 AND dra.A_ApproverID='$_SESSION[User_ID]' 
			 AND dra.A_Status='2' 
			 AND dra.A_TransactionCode=throld.THRGOLAD_RegistrationCode 
			 AND throld.THRGOLAD_ID='$DocID'";
$cApp_sql=mysql_query($cApp_query);
$approver=mysql_num_rows($cApp_sql);

$appQuery=(($act=='approve')&&($approver=="1"))?"AND dra.A_ApproverID='$_SESSION[User_ID]'":"AND dra.A_Status='2'";

$query = "SELECT DISTINCT thrgolad.THRGOLAD_ID, 
						  thrgolad.THRGOLAD_RegistrationCode, 
						  thrgolad.THRGOLAD_RegistrationDate,
		  				  u.User_ID, 
						  u.User_FullName, 
						  thrgolad.THRGOLAD_RegStatus, 
						  thrgolad.THRGOLAD_Information, 
		  				  thrgolad.THRGOLAD_RegStatusReason,
						  c.Company_ID, 
						  c.Company_Name, 
						  thrgolad.THRGOLAD_Phase,
		  	   			  thrgolad.THRGOLAD_Period, 
						  thrgolad.THRGOLAD_Revision,
						  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=dra.A_ApproverID) waitingApproval
		  FROM TH_RegistrationOfLandAcquisitionDocument thrgolad
		  LEFT JOIN M_User u
			ON thrgolad.THRGOLAD_UserID=u.User_ID 
		  LEFT JOIN M_Company c
			ON thrgolad.THRGOLAD_CompanyID=c.Company_ID 
		  LEFT JOIN M_Approval dra
			ON dra.A_TransactionCode=thrgolad.THRGOLAD_RegistrationCode
			$appQuery
		  WHERE thrgolad.THRGOLAD_Delete_Time is NULL 	  
		  AND thrgolad.THRGOLAD_ID='$DocID'
		  ORDER BY waitingApproval DESC";
$sql = mysql_query($query);
$arr = mysql_fetch_array($sql);

$fregdate=date("j M Y", strtotime($arr['THRGOLAD_RegistrationDate']));
$regUser=$arr['User_ID'];
$fperioddate=date("j M Y", strtotime($arr['THRGOLAD_Period']));
$f1perioddate=date("m/d/Y", strtotime($arr['THRGOLAD_Period']));

// Cek apakah Staff Custodian atau bukan. 
// Staff Custodian memiliki wewenang untuk print registrasi dokumen.
$cs_query = "SELECT *
			 FROM M_DivisionDepartmentPosition ddp, M_Department d
			 WHERE ddp.DDP_DeptID=d.Department_ID
			 AND ddp.DDP_UserID='$_SESSION[User_ID]'
			 AND d.Department_Name LIKE '%Custodian%'";
$cs_sql = mysql_query($cs_query);
$custodian = mysql_num_rows($cs_sql);
		
// Cek apakah Administrator atau bukan. 
// Administrator memiliki hak untuk upload softcopy & edit dokumen.
$query = "SELECT *
		  FROM M_UserRole
		  WHERE MUR_RoleID='1'
		  AND MUR_UserID='$_SESSION[User_ID]'
		  AND MUR_Delete_Time IS NULL";
$sql = mysql_query($query);
$admin = mysql_num_rows($sql);

$MainContent ="
<form name='DLA_detail' method='post' action='$PHP_SELF'>
<table width='100%' id='mytable' class='stripeMe'>";

if(($act=='approve')&&(($custodian==1)||($admin=="1")))
	$MainContent .="<th colspan=3>Persetujuan Pendaftaran Dokumen Pembebasan Lahan</th>";
else
	$MainContent .="<th colspan=3>Pendaftaran Dokumen Pembebasan Lahan</th>";

if((($arr[THRGOLAD_RegStatus]=="accept")||($arr[THRGOLAD_RegStatus]=="waiting")) && ((($custodian==1)||($admin=="1")) || ($regUser==$_SESSION['User_ID']))){
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='67%'>
			<input name='txtTHRGOLAD_ID' type='hidden' value='$arr[THRGOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THRGOLAD_RegistrationCode]'/>
			$arr[THRGOLAD_RegistrationCode]
		</td>
		<td width='3%'>
			<a href='print-registration-of-land-acquisition-document.php?id=$arr[THRGOLAD_RegistrationCode]' target='_blank'><img src='./images/icon-print.png'></a>
		</td>
	</tr>";
}else{
	$documentRevision=($arr[THRGOLAD_Revision]<>"0")?"(Revisi $arr[THRGOLAD_Revision])":"";
	$MainContent .="
	<tr>
		<td width='30%'>Kode Pendaftaran</td>
		<td width='70%'colspan='2'>
			<input name='txtTHRGOLAD_ID' type='hidden' value='$arr[THRGOLAD_ID]'/>
			<input name='txtA_TransactionCode' type='hidden' value='$arr[THRGOLAD_RegistrationCode]'/>
			$arr[THRGOLAD_RegistrationCode] $documentRevision
		</td>
	</tr>";
}

$MainContent .="	
<tr>
	<td>Tanggal Pendaftaran</td>
	<td colspan='2'><input name='txtDL_RegTime' id='txtDL_RegTime' type='hidden' value='$arr[THRGOLAD_RegistrationDate]'>$fregdate</td>
</tr>
<tr>
	<td>Nama Pendaftar</td>
	<td colspan='2'><input name='txtDL_RegUserID' type='hidden' value='$arr[User_ID]'>$arr[User_FullName]</td>
</tr>
<tr>
	<td>Nama Perusahaan</td>
	<td colspan='2'><input type='hidden' name='txtCompany_ID' value='$arr[Company_ID]' readonly='true' class='readonly'/>$arr[Company_Name]</td>
</tr>
<tr>
	<td>Tahap</td>
	<td colspan='2'>";
	
if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))) {		
	$MainContent .="	
		<input name='txtTHRGOLAD_Phase' id='txtTHRGOLAD_Phase' type='text' value='$arr[THRGOLAD_Phase]' size='3'>
		<input name='txtTHRGOLAD_PhaseR' id='txtTHRGOLAD_PhaseR' type='text' value='$arr[THRGOLAD_Phase]' size='3' readonly='readonly' class='readonly' style='display:none;'>";
}else {		
	$MainContent .="	
		<input name='txtTHRGOLAD_Phase' type='hidden' value='$arr[THRGOLAD_Phase]'>$arr[THRGOLAD_Phase]";
}
	
$MainContent .="			
	</td>
</tr>
<tr>
	<td>Periode</td>
	<td colspan='2'>";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&&($custodian==1))) {		
	$MainContent .="					
		<input name='txtTHRGOLAD_Period' id='txtTHRGOLAD_Period' type='text' value='$f1perioddate' size='7' onclick=\"javascript:NewCssCal('txtTHRGOLAD_Period', 'MMddyyyy');\">
		<input name='txtTHRGOLAD_PeriodR' id='txtTHRGOLAD_PeriodR' type='text' value='$f1perioddate' size='7' readonly='readonly' class='readonly' style='display:none;'";
}else {		
	$MainContent .="					
		<input name='txtTHRGOLAD_Period' type='hidden' value='$arr[THRGOLAD_Period]'>$fperioddate";
}

$MainContent .="	
	</td>		
</tr>
<tr>
	<td>Revisi</td>
	<td colspan='2'><input name='txtTHRGOLAD_Revision' id='txtTHRGOLAD_Revision' type='hidden' value='$arr[THRGOLAD_Revision]'>$arr[THRGOLAD_Revision]</td>
</tr>
<tr>
	<td>Keterangan</td>
	<td colspan='2'>";

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))){		
	$MainContent .="	
		<textarea name='txtTHRGOLAD_Information' id='txtTHRGOLAD_Information' cols='50' rows='2'>$arr[THRGOLAD_Information]</textarea>";
}else {		
	$MainContent .="	
		$arr[THRGOLAD_Information]";
}
$MainContent .="</td></tr>";
	
// APABILA MEMILIKI HAK UNTUK APPROVAL DOKUMEN
if(($act=='approve')&&($approver=="1")) {
	$MainContent .="	
	<tr>
		<td>Persetujuan</td>
		<td colspan='2'>
			<select name='optTHRGOLAD_RegStatus' id='optTHRGOLAD_RegStatus'>
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
			<textarea name='txtTHRGOLAD_RegStatusReason' id='txtTHRGOLAD_RegStatusReason' cols='50' rows='2'>$arr[THRGOLAD_RegStatusReason]</textarea>
			<br>*Wajib Diisi Apabila Dokumen Ditolak.
		</td>
	</tr>";
}else {
	$MainContent .="
	<tr>
		<td>Status Dokumen</td>";
	if($arr[THRGOLAD_RegStatus]=="waiting"){
		$MainContent .="
		<td colspan='2'><input type='hidden' name='txtTHRGOLAD_RegStatus' value='$arr[THRGOLAD_RegStatus]'>Menunggu Persetujuan $arr[waitingApproval]</td></tr>";
	}else if($arr[THRGOLAD_RegStatus]=="accept") {
		$MainContent .="
			<td colspan='2'><input type='hidden' name='txtTHRGOLAD_RegStatus' value='$arr[THRGOLAD_RegStatus]'>Disetujui</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THRGOLAD_RegStatusReason]</td>
		</tr>";
	}else if($arr[THRGOLAD_RegStatus]=="reject") {
		$MainContent .="
			<td colspan='2'>Ditolak</td>
		</tr>
		<tr>
			<td>Alasan</td>
			<td colspan='2'>$arr[THRGOLAD_RegStatusReason]</td>
		</tr>
		";
	}else {
		$MainContent .="
		<td colspan='2'>Draft</td></tr>";
	}
}

if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))) {
	$MainContent .="
	<tr id='rowUpload' style='display:none;'>
		<td>Upload File Excel</td>
		<td colspan=2>
			<img id='loading' src='images/loading.gif' style='display:none;'><input name='fileToUpload' id='fileToUpload' type='file' size='20' /><input name='getExcel' type='button' onclick='return ajaxFileUpload();' value='Upload' class='button-small' />
			<a href='./sample/SampleExcelRegLADoc.xlsx' target='_blank' class='underline'>[Download Format Excel]</a>
		</td>
	</tr>";
	
	if((($arr[THRGOLAD_RegStatus]=="accept") || ($arr[THRGOLAD_RegStatus]=="waiting"))&&($act<>'approve')) {
		$MainContent .="
		<th colspan=20>
			<input name='add Detail' type='button' value='Tambah Detail' class='button' onclick=\"showUpload();\"/>
		</th>";
	}
	
	$MainContent .="
	</table>
	<table width='2220' id='row1' class='stripeMe' border=1>
		<input type=hidden name='maxValueA' id='maxValueA'>
		<input type=hidden name='jKelengkapanA' id='jKelengkapanA'>
	</table>
	<table width='2220'>
	<th colspan='50' id='rowButton' style='display:none;'>
		<input name='saveUpload' id='saveUpload' type='submit' value='Tambah Detail' class='button' onclick='return validateInputDetail(this);'/>
		<input name='cancelUpload' id='cancelUpload' type='submit' value='Batal' class='button'/>
	</th>";
}
$MainContent .="</table>";


$query="SELECT * 
		FROM M_LandAcquisitionAttribute 
		WHERE LAA_Delete_Time is NULL 
		ORDER BY LAA_ID ";
$sql = mysql_query($query);
$counts=mysql_num_rows($sql);

// DETAIL DOKUMEN GRL	
$MainContent .="
<div id='headerdetailtable' class='detail-title'>Daftar Dokumen</div>
<table width='100%' id='detailtable' class='stripeMe'>
<tr>
   	<th rowspan='2'>No</th>
    <th rowspan='2'>Tanggal</th>
    <th rowspan='2'>Revisi</th>
    <th rowspan='2'>Blok</th>
    <th rowspan='2'>Desa</th>
    <th rowspan='2'>Nama Pemilik</th>
    <th rowspan='2'>Kelas</th>
    <th colspan='3'>Lahan</th>
   	<th rowspan='2'>Kelas</th>
    <th colspan='3'>Tanam Tumbuh</th>
   	<th rowspan='2'>Total</th>
	<th rowspan='2'>Keterangan</th>
	<th colspan='$counts'>Kelengkapan Dokumen</th>
</tr>
<tr>
	<th>Ha</th>
	<th>Rp/Ha</th>
	<th>Nilai (Rp)</th>
	<th>Qty</th>
	<th>Rp/Pkk</th>
	<th>Nilai (Rp)</th>";
	while ($arr = mysql_fetch_array($sql)){
		$MainContent .="
		<th>$arr[LAA_ID]</th>";
	}
$MainContent .="</tr>";
	
$query = "SELECT DISTINCT tdrgolad.TDRGOLAD_ID, tdrgolad.TDRGOLAD_DocDate, tdrgolad.TDRGOLAD_Block, 
						  tdrgolad.TDRGOLAD_Village, tdrgolad.TDRGOLAD_Owner, tdrgolad.TDRGOLAD_AreaClass, 
						  tdrgolad.TDRGOLAD_AreaPrice, tdrgolad.TDRGOLAD_AreaStatement, 	
						  tdrgolad.TDRGOLAD_AreaTotalPrice, tdrgolad.TDRGOLAD_PlantClass, 
						  tdrgolad.TDRGOLAD_PlantQuantity, tdrgolad.TDRGOLAD_PlantPrice,
						  tdrgolad.TDRGOLAD_PlantTotalPrice, tdrgolad.TDRGOLAD_GrandTotal, 
						  tdrgolad.TDRGOLAD_Information, tdrgolad.TDRGOLAD_Revision
		  FROM TD_RegistrationOfLandAcquisitionDocument tdrgolad, TH_RegistrationOfLandAcquisitionDocument thrgolad
		  WHERE tdrgolad.TDRGOLAD_THRGOLAD_ID='$DocID' 
		  AND thrgolad.THRGOLAD_Delete_Time IS NULL
		  AND tdrgolad.TDRGOLAD_Delete_Time IS NULL";
$sql = mysql_query($query);
$no=1;

while ($arr = mysql_fetch_array($sql)) {		
	if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1")&& ($custodian==1))) {
		$fdocdate=date("m/d/Y", strtotime($arr['TDRGOLAD_DocDate']));
		
		$MainContent .="
		<tr>
			<td class='center'>
				<input type='hidden' name='txtTDRGOLAD_ID$no' value='$arr[TDRGOLAD_ID]'/>$no
			</td>
			<td class='center'><input name='txtTDRGOLAD_DocDate$no' id='txtTDRGOLAD_DocDate$no' type='text' value='$fdocdate' onclick=\"javascript:NewCssCal('txtTDRGOLAD_DocDate$no', 'MMddyyyy');\" size='7'></td>
			<td class='center'>$arr[TDRGOLAD_Revision]</td>
			<td class='center'><input name='txtTDRGOLAD_Block$no' id='txtTDRGOLAD_Block$no' type='text' value='$arr[TDRGOLAD_Block]'></td>
			<td class='center'><input name='txtTDRGOLAD_Village$no' id='txtTDRGOLAD_Village$no' type='text' value='$arr[TDRGOLAD_Village]'></td>
			<td class='center'><input name='txtTDRGOLAD_Owner$no' id='txtTDRGOLAD_Owner$no' type='text' value='$arr[TDRGOLAD_Owner]'></td>
			<td class='center'><input name='txtTDRGOLAD_AreaClass$no' id='txtTDRGOLAD_AreaClass$no' type='text' value='$arr[TDRGOLAD_AreaClass]' size='3'></td>
			<td class='center'><input name='txtTDRGOLAD_AreaStatement$no' id='txtTDRGOLAD_AreaStatement$no' type='text' value='$arr[TDRGOLAD_AreaStatement]' size='5' onchange='countTotal($no);'></td>
			<td class='center'><input name='txtTDRGOLAD_AreaPrice$no' id='txtTDRGOLAD_AreaPrice$no' type='text' value='$arr[TDRGOLAD_AreaPrice]' size='10' onchange='countTotal($no);'></td>
			<td class='center'><input name='txtTDRGOLAD_AreaTotalPrice$no' id='txtTDRGOLAD_AreaTotalPrice$no' type='text' value='$arr[TDRGOLAD_AreaTotalPrice]' size='10' onchange='countGrandTotal($no);'></td>
			<td class='center'><input name='txtTDRGOLAD_PlantClass$no' id='txtTDRGOLAD_PlantClass$no' type='text' value='$arr[TDRGOLAD_PlantClass]' size='3'></td>
			<td class='center'><input name='txtTDRGOLAD_PlantQuantity$no' id='txtTDRGOLAD_PlantQuantity$no' type='text' value='$arr[TDRGOLAD_PlantQuantity]' size='5' onchange='countTotal($no);'></td>
			<td class='center'><input name='txtTDRGOLAD_PlantPrice$no' id='txtTDRGOLAD_PlantPrice$no' type='text' value='$arr[TDRGOLAD_PlantPrice]' size='10' onchange='countTotal($no);'></td>
			<td class='center'><input name='txtTDRGOLAD_PlantTotalPrice$no' id='txtTDRGOLAD_PlantTotalPrice$no' type='text' value='$arr[TDRGOLAD_PlantTotalPrice]' size='10' onchange='countGrandTotal($no);'></td>
			<td class='center'><input name='txtTDRGOLAD_GrandTotal$no' id='txtTDRGOLAD_GrandTotal$no' type='text' value='$arr[TDRGOLAD_GrandTotal]' size='10' ></td>
			<td class='center'><textarea name='txtTDRGOLAD_Information$no' id='txtTDRGOLAD_Information$no'>$arr[TDRGOLAD_Information]</textarea></td>";
			
			$at_query = "SELECT laas.LAAS_Symbol,laas.LAAS_ID
					  FROM TD_RegistrationOfLandAcquisitionDocumentDetail tdrgoladd, M_LandAcquisitionAttributeStatus laas
					  WHERE tdrgoladd.TDRGOLADD_TDRGOLAD_ID='$arr[TDRGOLAD_ID]' 
					  AND tdrgoladd.TDRGOLADD_Delete_Time IS NULL
					  AND tdrgoladd.TDRGOLADD_AttributeStatusID=laas.LAAS_ID
					  ORDER BY tdrgoladd.TDRGOLADD_AttibuteID";
			$at_sql = mysql_query($at_query);
			$idKelengkapan=1;
			while (($at_arr = mysql_fetch_array($at_sql))&&($idKelengkapan<'15')) {
				$row=$arr['TDRGOLAD_ID'];
				$MainContent .="<td class='center'>
									<select name='optKelengkapan$row$idKelengkapan'>";
				$s_query="SELECT *
				 		  FROM M_LandAcquisitionAttributeStatus
				 		  WHERE LAAS_Delete_Time IS NULL";
				$s_sql=mysql_query($s_query);
				while ($s_arr=mysql_fetch_array($s_sql)) {
					$selected=($at_arr[LAAS_ID]==$s_arr[LAAS_ID])?"selected='selected'":"";
					$MainContent .="		
						<option value='$s_arr[LAAS_ID]' $selected>$s_arr[LAAS_Symbol]</option>";
				}
				$MainContent .="</select></td>";
				$idKelengkapan++;
			}
		$MainContent .="</tr>";
		$no=$no+1;
	}else {
		$fdocdate=date("j M Y", strtotime($arr['TDRGOLAD_DocDate']));

		$MainContent .="
		<tr>
			<td class='center'>
				<input type='hidden' name='txtTDRGOLAD_ID$no' value='$arr[TDRGOLAD_ID]'/>$no
			</td>
			<td class='center'><input name='txtTDRGOLAD_DocDate$no' type='hidden' value='$arr[TDRGOLAD_DocDate]'>$fdocdate</td>
			<td class='center'><input type='hidden' name='txtTDRGOLAD_Revision$no' value='$arr[TDRGOLAD_Revision]'/>$arr[TDRGOLAD_Revision]</td>
			<td class='center'><input name='txtTDRGOLAD_Block$no' type='hidden' value='$arr[TDRGOLAD_Block]'>$arr[TDRGOLAD_Block]</td>
			<td class='center'><input name='txtTDRGOLAD_Village$no' type='hidden' value='$arr[TDRGOLAD_Village]'>$arr[TDRGOLAD_Village]</td>
			<td class='center'><input name='txtTDRGOLAD_Owner$no' type='hidden' value='$arr[TDRGOLAD_Owner]'>$arr[TDRGOLAD_Owner]</td>
			<td class='center'><input name='txtTDRGOLAD_AreaClass$no' type='hidden' value='$arr[TDRGOLAD_AreaClass]'>$arr[TDRGOLAD_AreaClass]</td>
			<td class='center'><input name='txtTDRGOLAD_AreaStatement$no' type='hidden' value='$arr[TDRGOLAD_AreaStatement]'>$arr[TDRGOLAD_AreaStatement]</td>
			<td class='center'><input name='txtTDRGOLAD_AreaPrice$no' type='hidden' value='$arr[TDRGOLAD_AreaPrice]'>$arr[TDRGOLAD_AreaPrice]</td>
			<td class='center'><input name='txtTDRGOLAD_AreaTotalPrice$no' type='hidden' value='$arr[TDRGOLAD_AreaTotalPrice]'>$arr[TDRGOLAD_AreaTotalPrice]</td>
			<td class='center'><input name='txtTDRGOLAD_PlantClass$no' type='hidden' value='$arr[TDRGOLAD_PlantClass]'>$arr[TDRGOLAD_PlantClass]</td>
			<td class='center'><input name='txtTDRGOLAD_PlantQuantity$no' type='hidden' value='$arr[TDRGOLAD_PlantQuantity]'>$arr[TDRGOLAD_PlantQuantity]</td>
			<td class='center'><input name='txtTDRGOLAD_PlantPrice$no' type='hidden' value='$arr[TDRGOLAD_PlantPrice]'>$arr[TDRGOLAD_PlantPrice]</td>
			<td class='center'><input name='txtTDRGOLAD_PlantTotalPrice$no' type='hidden' value='$arr[TDRGOLAD_PlantTotalPrice]'>$arr[TDRGOLAD_PlantTotalPrice]</td>
			<td class='center'><input name='txtTDRGOLAD_GrandTotal$no' type='hidden' value='$arr[TDRGOLAD_GrandTotal]'>$arr[TDRGOLAD_GrandTotal]</td>
			<td class='center'><input name='txtTDRGOLAD_Information$no' type='hidden' value='$arr[TDRGOLAD_Information]'>$arr[TDRGOLAD_Information]</td>";
			$at_query = "SELECT laas.LAAS_Symbol,laas.LAAS_ID
					  FROM TD_RegistrationOfLandAcquisitionDocumentDetail tdrgoladd, M_LandAcquisitionAttributeStatus laas
					  WHERE tdrgoladd.TDRGOLADD_TDRGOLAD_ID='$arr[TDRGOLAD_ID]' 
					  AND tdrgoladd.TDRGOLADD_Delete_Time IS NULL
					  AND tdrgoladd.TDRGOLADD_AttributeStatusID=laas.LAAS_ID
					  ORDER BY tdrgoladd.TDRGOLADD_AttibuteID";
			$at_sql = mysql_query($at_query);
			$idKelengkapan=1;
			while (($at_arr = mysql_fetch_array($at_sql))&&($idKelengkapan<'15')) {
				$row=$arr['TDRGOLAD_ID'];
				$MainContent .="<td class='center'><input name='optKelengkapan$row$idKelengkapan' type='hidden' value='$at_arr[LAAS_ID]'>$at_arr[LAAS_Symbol]</td>";
				$idKelengkapan++;
			}
		$MainContent .="</tr>";
		$no=$no+1;
	}
}

$MainContent .="
<input type=hidden name='maxValue' id='maxValue' value='$no'>
<input type=hidden name='jKelengkapan' id='jKelengkapan' value='$idKelengkapan'>
</table>";
	
if((($act=='edit') && (($custodian==1)||($admin=="1"))) || (($act=='approve')&&($approver=="1"))) {
	$MainContent .="
	<table width='100%' id='button'>
	<th>
		<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>";
}
$MainContent .="</form>";


/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}
elseif(isset($_POST[cancelUpload])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}
if(isset($_POST[edit])) {
	//Update Header
	$txtTHRGOLAD_Information=str_replace("<br>", "\n",$_POST[txtTHRGOLAD_Information]);
	$txtPerDate=date('Y-m-d H:i:s', strtotime($_POST['txtTHRGOLAD_Period']));
	$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
			  SET THRGOLAD_Phase='$_POST[txtTHRGOLAD_Phase]', THRGOLAD_Period='$txtPerDate', 
			      THRGOLAD_Information='$txtTHRGOLAD_Information',
			      THRGOLAD_Update_Time=sysdate(), THRGOLAD_Update_UserID='$_SESSION[User_ID]'
			  WHERE THRGOLAD_RegistrationCode='$_POST[txtA_TransactionCode]'";
	$mysqli->query($query);

	//Update Detail	
	$count=$_POST[maxValue];
	$jKelengkapan=$_POST[jKelengkapan];
	
	
	for ($i=1 ; $i<$count ; $i++) {
		$txtTDRGOLAD_ID=$_POST["txtTDRGOLAD_ID".$i];
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

		$sql1= "UPDATE TD_RegistrationOfLandAcquisitionDocument 
				SET TDRGOLAD_DocDate='$txtTDRGOLAD_DocDate', 
					TDRGOLAD_Block='$txtTDRGOLAD_Block', 
				    TDRGOLAD_Village='$txtTDRGOLAD_Village', 
					TDRGOLAD_Owner='$txtTDRGOLAD_Owner', 
					TDRGOLAD_AreaClass='$txtTDRGOLAD_AreaClass', 
					TDRGOLAD_AreaStatement=REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
					TDRGOLAD_AreaPrice=REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
					TDRGOLAD_AreaTotalPrice=REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
					TDRGOLAD_PlantClass='$txtTDRGOLAD_PlantClass',
					TDRGOLAD_PlantQuantity=REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
					TDRGOLAD_PlantPrice=REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
					TDRGOLAD_PlantTotalPrice=REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
					TDRGOLAD_GrandTotal=REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
					TDRGOLAD_Information='$txtTDRGOLAD_Information',
					TDRGOLAD_Update_UserID='$_SESSION[User_ID]', 
					TDRGOLAD_Update_Time=sysdate()
				WHERE TDRGOLAD_ID='$txtTDRGOLAD_ID' ";
		if($mysqli->query($sql1)){
			for ($j=1 ; $j<$jKelengkapan ; $j++) {
				$optKelengkapan=$_POST["optKelengkapan".$txtTDRGOLAD_ID.$j];
				
				$k_sql="UPDATE TD_RegistrationOfLandAcquisitionDocumentDetail 
						SET TDRGOLADD_AttributeStatusID='$optKelengkapan',
							TDRGOLADD_Update_UserID='$_SESSION[User_ID]',
							TDRGOLADD_Update_Time=sysdate()
						WHERE TDRGOLADD_TDRGOLAD_ID='$txtTDRGOLAD_ID'
						AND TDRGOLADD_AttibuteID='$j'";
				$mysqli->query($k_sql);
			}
		}
	}
	if ($_POST['optTHRGOLAD_RegStatus']){
		$A_TransactionCode=$_POST['txtA_TransactionCode'];
		$A_ApproverID=$_SESSION['User_ID'];
		$A_Status=$_POST['optTHRGOLAD_RegStatus'];
		$THRGOLAD_RegStatusReason=str_replace("<br>", "\n",$_POST['txtTHRGOLAD_RegStatusReason']);
					
		// MENCARI TAHAP APPROVAL USER TERSEBUT
		$query = "SELECT *
					FROM M_Approval
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_ApproverID='$A_ApproverID'";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
		$step=$arr['A_Step'];
		$AppDate=$arr['A_ApprovalDate'];
		
		if ($AppDate==NULL) {
		
		// MENCARI JUMLAH APPROVAL
		$query = "SELECT MAX(A_Step) AS jStep
					FROM M_Approval
					WHERE A_TransactionCode='$A_TransactionCode'";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
		$jStep=$arr['jStep'];
				
		// PROSES BILA "SETUJU"
		if ($A_Status=='3') {
			// CEK APAKAH MERUPAKAN APPROVAL FINAL
			if ($step <> $jStep) {
				// UPDATE APPROVAL
				$query = "UPDATE M_Approval
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
				if ($sql = mysql_query($query)) {
					mail_registration_doc($A_TransactionCode);
					if($step=='1'){
						mail_notif_registration_doc($A_TransactionCode, $_POST['txtDL_RegUserID'], 3 );
						mail_notif_registration_doc($A_TransactionCode, "cust0002", 3 );
					}
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";			
				}
			}
			else {
				$jumlahRow=$_POST[maxValue];
				// ACTION UNTUK MENENTUKAN JUMLAH LOKASI DOKUMEN YANG TERSEDIA
				$query = "SELECT *
						  FROM L_DocumentLocation 
						  WHERE DL_Status='1'
						  AND DL_CompanyID='$_POST[txtCompany_ID]'
						  AND DL_DocGroupID='grl'
						  AND DL_Delete_Time is NULL";
				$sql = mysql_query($query);
				$avLoc = mysql_num_rows($sql);				
				
				if((!$avLoc)||($avLoc<$jumlahRow)){
					?>
                    <script language="JavaScript" type="text/JavaScript">
					alert("Lokasi Untuk Dokumen Tidak Tersedia.\nLokasi yang Tersedia : <?PHP echo $avLoc ?>.\nHubungi Custodian System Administrator untuk Mengatur Lokasi dan Lakukan Persetujuan Ulang.");
					</script>
                    <?PHP
					echo "<meta http-equiv='refresh' content='0; url=home.php'>";	
				}
				else {
					// UPDATE APPROVAL
					$query = "UPDATE M_Approval
								SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
									A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_ApproverID='$A_ApproverID'";
					$sql = mysql_query($query);
					
					//UPDATE STATUS REGISTRASI
					$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
								SET THRGOLAD_RegStatus='accept', THRGOLAD_Update_UserID='$A_ApproverID', 
									THRGOLAD_Update_Time=sysdate()
								WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'";
					$sql = mysql_query($query);
					
					// ACTION UNTUK GENERATE NO DOKUMEN
					$regyear=date("y");
					$regmonth=date("m");
					// Cari Kode Perusahaan
					$query = "SELECT *
								FROM M_Company 
								WHERE Company_ID='$_POST[txtCompany_ID]'";
					$sql = mysql_query($query);
					$field = mysql_fetch_array($sql);
					$Company_Code=$field['Company_Code'];
					
					// Cari Kode Dokumen Grup
					$query = "SELECT *
								FROM M_DocumentGroup 
								WHERE DocumentGroup_ID='3'";
					$sql = mysql_query($query);
					$field = mysql_fetch_array($sql);
					$DocumentGroup_Code=$field['DocumentGroup_Code'];
		
					// Cari No Dokumen Terakhir
					$query = "SELECT MAX(CD_SeqNo) 
								FROM M_CodeDocument 
								WHERE CD_Year='$regyear' 
								AND CD_GroupDocCode='$DocumentGroup_Code'
								AND CD_CompanyCode='$Company_Code'
								AND CD_Delete_Time is NULL";
					$sql = mysql_query($query);
					$field = mysql_fetch_array($sql);
		
					if($field[0]==NULL)
						$maxnum=0;
					else
						$maxnum=$field[0];
					$nnum=$maxnum+1;
					
					
					$jKelengkapan=$_POST[jKelengkapan];
					
					for ($i=1 ; $i<$jumlahRow ; $i++) {
						// Menentukan Lokasi Dokumen
						$query = "SELECT *
								  FROM L_DocumentLocation 
								  WHERE DL_Status='1'
								  AND DL_CompanyID='$_POST[txtCompany_ID]'
								  AND DL_DocGroupID='grl'
								  AND DL_Delete_Time is NULL
								  AND DL_ID=(SELECT MIN(DL_ID)
											 FROM L_DocumentLocation 
											 WHERE DL_Status='1'
											 AND DL_CompanyID='$_POST[txtCompany_ID]'
											 AND DL_DocGroupID='grl'
											 AND DL_Delete_Time is NULL)";
						$sql = mysql_query($query);
						$arr = mysql_fetch_array($sql);
						$DLIU_LocationCode=$arr[DL_Code];	
						
						$txtDL_RegTime=$_POST['txtDL_RegTime'];
						$txtDL_RegUserID=$_POST['txtDL_RegUserID'];
						$txtCompany_ID=$_POST['txtCompany_ID'];
						$txtTHRGOLAD_Phase=$_POST['txtTHRGOLAD_Phase'];
						$txtTHRGOLAD_Period=date('Y-m-d H:i:s', strtotime($_POST['txtTHRGOLAD_Period']));
						$txtTDRGOLAD_ID=$_POST['txtTDRGOLAD_ID'.$i];
						$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($_POST['txtTDRGOLAD_DocDate'.$i]));
						$txtTDRGOLAD_Revision=$_POST['txtTDRGOLAD_Revision'.$i];
						$txtTDRGOLAD_Block=$_POST['txtTDRGOLAD_Block'.$i];
						$txtTDRGOLAD_Village=$_POST['txtTDRGOLAD_Village'.$i];
						$txtTDRGOLAD_Owner=$_POST['txtTDRGOLAD_Owner'.$i];
						$txtTDRGOLAD_AreaClass=$_POST['txtTDRGOLAD_AreaClass'.$i];
						$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaStatement".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaStatement".$i])==false))?"0":$_POST["txtTDRGOLAD_AreaStatement".$i];
						$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_AreaPrice".$i];
						$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_AreaTotalPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_AreaTotalPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_AreaTotalPrice".$i];
						$txtTDRGOLAD_PlantClass=$_POST["txtTDRGOLAD_PlantClass".$i];
						$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantQuantity".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantQuantity".$i])==false))?"0":$_POST["txtTDRGOLAD_PlantQuantity".$i];
						$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_PlantPrice".$i];
						$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txtTDRGOLAD_PlantTotalPrice".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_PlantTotalPrice".$i])==false))?"0":$_POST["txtTDRGOLAD_PlantTotalPrice".$i];
						$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txtTDRGOLAD_GrandTotal".$i])=="")||(is_numeric($_POST["txtTDRGOLAD_GrandTotal".$i])==false))?"0":$_POST["txtTDRGOLAD_GrandTotal".$i];
						$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txtTDRGOLAD_Information".$i]);
						
						$txtDLAA_LAAS_ID=$_POST['txtDLAA_LAAS_ID'.$i];
			
						$query = "UPDATE L_DocumentLocation
								  SET DL_Status='0', DL_Update_UserID='$_SESSION[User_ID]', DL_Update_Time=sysdate()
								  WHERE DL_Code='$DLIU_LocationCode';";	
																
						$newnum=str_pad($nnum,4,"0",STR_PAD_LEFT);
						$CD_Code_H="$newnum$Company_Code$DocumentGroup_Code$regmonth$regyear";	
						$sql2= "INSERT INTO M_CodeDocument 
								VALUES ('$CD_Code_H','$nnum','$Company_Code','$DocumentGroup_Code','$regmonth',
										'$regyear','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
										sysdate(),NULL,NULL)";
						$mysqli->query($sql2);
						
						// Memindahkan Pendaftaran Dokumen ke M_DocumentLandAcquisition
						$sql3= "INSERT INTO M_DocumentLandAcquisition
								VALUES (NULL,
										'$CD_Code_H',
										'$txtDL_RegUserID',
										'$txtDL_RegTime',
										'$txtCompany_ID',
										'$txtTHRGOLAD_Phase',
										'$txtTHRGOLAD_Period',
										'$txtTDRGOLAD_Revision',
										'$txtTDRGOLAD_DocDate',
										'$txtTDRGOLAD_Block',
										'$txtTDRGOLAD_Village',
										'$txtTDRGOLAD_Owner',
										'$txtTDRGOLAD_AreaClass',
										REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
										REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
										REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
										'$txtTDRGOLAD_PlantClass',
										REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
										REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
										REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
										REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
										'$txtTDRGOLAD_Information',
										'$DLIU_LocationCode','1', NULL,
										'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
										sysdate(),NULL,NULL);";
										
						if(($mysqli->query($sql3)) && ($mysqli->query($query)) ){
							$period=
							$s_sql="SELECT *
									FROM M_DocumentLandAcquisition
									WHERE DLA_Code='$CD_Code_H'";
							$s_query=mysql_query($s_sql);
							$s_arr=mysql_fetch_array($s_query);
							$DLA_ID=$s_arr['DLA_ID'];
							
							for ($j=1 ; $j<$jKelengkapan ; $j++) {
								$optKelengkapan=$_POST["optKelengkapan".$txtTDRGOLAD_ID.$j];
								$dnewnum=str_pad($j,2,"0",STR_PAD_LEFT);
								$CD_Code="$newnum$dnewnum$Company_Code$DocumentGroup_Code$regmonth$regyear";
															
								$i_sql="INSERT INTO M_DocumentLandAcquisitionAttribute
										VALUES (NULL,'$CD_Code','$DLA_ID','$j','$optKelengkapan',
												'1','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
												sysdate(),NULL,NULL)";
								$mysqli->query($i_sql);
							}
						}
						$nnum=$nnum+1;
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

			//UPDATE STATUS REGISTRASI DOKUMEN
			$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
						SET THRGOLAD_RegStatus='reject', THRGOLAD_RegStatusReason='$THRGOLAD_RegStatusReason',
							THRGOLAD_Update_Time=sysdate(), THRGOLAD_Update_UserID='$A_ApproverID'
						WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'";
			
			$query1 = "UPDATE M_Approval
						SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID', 
							A_Status='$A_Status'
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_Step>'$step'";
			if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
				$h_query="SELECT *
							  FROM TH_RegistrationOfLandAcquisitionDocument
							  WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
							  AND THRGOLAD_Delete_Time IS NULL";
				$h_sql=mysql_query($h_query);
				$h_arr=mysql_fetch_array($h_sql);
				mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 4 );
				
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
	else {
		echo "<meta http-equiv='refresh' content='0; url=$PHP_SELF'>";
	}
}

if(isset($_POST[saveUpload])) {
	$count=$_POST[maxValueA];
	$jKelengkapan=$_POST[jKelengkapanA];

	if ($_POST['txtTHRGOLAD_RegStatus']=="accept"){
				// ACTION UNTUK GENERATE NO DOKUMEN
				$regyear=date("y");
				$regmonth=date("m");

				// Cari Kode Perusahaan
				$query = "SELECT *
							FROM M_Company 
							WHERE Company_ID='$_POST[txtCompany_ID]'";
				$sql = mysql_query($query);
				$field = mysql_fetch_array($sql);
				$Company_Code=$field['Company_Code'];
				
				// Cari Kode Dokumen Grup
				$query = "SELECT *
							FROM M_DocumentGroup 
							WHERE DocumentGroup_ID='3'";
				$sql = mysql_query($query);
				$field = mysql_fetch_array($sql);
				$DocumentGroup_Code=$field['DocumentGroup_Code'];
	
				// Cari No Dokumen Terakhir
				$query = "SELECT MAX(CD_SeqNo) 
							FROM M_CodeDocument 
							WHERE CD_Year='$regyear' 
							AND CD_GroupDocCode='$DocumentGroup_Code'
							AND CD_CompanyCode='$Company_Code'
							AND CD_Delete_Time is NULL";
				$sql = mysql_query($query);
				$field = mysql_fetch_array($sql);
	
				if($field[0]==NULL)
					$maxnum=0;
				else
					$maxnum=$field[0];
				$nnum=$maxnum+1;
				
				// ACTION UNTUK MENENTUKAN LOKASI DOKUMEN
				$query = "SELECT MIN(DL_ID) as minID
							FROM L_DocumentLocation 
							WHERE DL_Status='1'
							AND DL_CompanyID='$_POST[txtCompany_ID]'
							AND DL_Delete_Time is NULL";
				$sql = mysql_query($query);
				$arr = mysql_fetch_array($sql);
				$nID=$arr[minID];				

		for ($i=3 ; $i<=$count ; $i++) {
			// Menentukan Lokasi Dokumen
			$query = "SELECT DL_Code
					  FROM L_DocumentLocation
					  WHERE DL_ID='$nID'
					  AND DL_Delete_Time is NULL";
			$sql = mysql_query($query);
			$arr = mysql_fetch_array($sql);
			$DLIU_LocationCode=$arr[DL_Code];
			$query = "UPDATE L_DocumentLocation
					  SET DL_Status='0', DL_Update_UserID='$_SESSION[User_ID]', DL_Update_Time=sysdate()
					  WHERE DL_Code='$DLIU_LocationCode';";									
			$mysqli->query($query);
			
			$txtDL_RegUserID=$_SESSION["User_ID"];
			$txtDL_RegTime=date('Y-m-d H:i:s');
			$txtCompany_ID=$_POST["txtCompany_ID"];
			$txtTHRGOLAD_PhaseR=$_POST["txtTHRGOLAD_PhaseR"];
			$txtTHRGOLAD_PeriodR=$_POST["txtTHRGOLAD_PeriodR"];
			$txtTHRGOLAD_PeriodR=date('Y-m-d H:i:s', strtotime($txtTHRGOLAD_PeriodR));	
			$txtTDRGOLAD_DocDate=$_POST["txt_DocDate".$i];
			$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($txtTDRGOLAD_DocDate));	
			$txtTDRGOLAD_Block=$_POST["txt_Block".$i];
			$txtTDRGOLAD_Village=$_POST["txt_Village".$i];
			$txtTDRGOLAD_Owner=$_POST["txt_Owner".$i];
			$txtTDRGOLAD_AreaClass=$_POST["txt_AreaClass".$i];
			$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txt_AreaStatement".$i])=="")||(is_numeric($_POST["txt_AreaStatement".$i])==false))?"0":$_POST["txt_AreaStatement".$i];
			$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txt_AreaPrice".$i])=="")||(is_numeric($_POST["txt_AreaPrice".$i])==false))?"0":$_POST["txt_AreaPrice".$i];
			$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txt_AreaTotalPrice".$i])=="")||(is_numeric($_POST["txt_AreaTotalPrice".$i])==false))?"0":$_POST["txt_AreaTotalPrice".$i];
			$txtTDRGOLAD_PlantClass=$_POST["txt_PlantClass".$i];
			$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txt_PlantQuantity".$i])=="")||(is_numeric($_POST["txt_PlantQuantity".$i])==false))?"0":$_POST["txt_PlantQuantity".$i];
			$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txt_PlantPrice".$i])=="")||(is_numeric($_POST["txt_PlantPrice".$i])==false))?"0":$_POST["txt_PlantPrice".$i];
			$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txt_PlantTotalPrice".$i])=="")||(is_numeric($_POST["txt_PlantTotalPrice".$i])==false))?"0":$_POST["txt_PlantTotalPrice".$i];
			$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txt_GrandTotal".$i])=="")||(is_numeric($_POST["txt_GrandTotal".$i])==false))?"0":$_POST["txt_GrandTotal".$i];
			$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txt_Information".$i]);
			
			$newnum=str_pad($nnum,4,"0",STR_PAD_LEFT);		
			$CD_Code_H="$newnum$Company_Code$DocumentGroup_Code$regmonth$regyear";
			$sql2= "INSERT INTO M_CodeDocument 
					VALUES ('$CD_Code_H','$nnum','$Company_Code','$DocumentGroup_Code','$regmonth',
							'$regyear','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
							sysdate(),NULL,NULL)";
			$mysqli->query($sql2);
									
			$sql1= "INSERT INTO M_DocumentLandAcquisition 
					VALUES (NULL,'$CD_Code_H','$txtDL_RegUserID','$txtDL_RegTime','$txtCompany_ID', 
							'$txtTHRGOLAD_PhaseR',
							'$txtTHRGOLAD_PeriodR','$_POST[txtTHRGOLAD_Revision]', '$txtTDRGOLAD_DocDate',
							'$txtTDRGOLAD_Block', '$txtTDRGOLAD_Village', '$txtTDRGOLAD_Owner', '$txtTDRGOLAD_AreaClass',
							REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
							REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
							'$txtTDRGOLAD_PlantClass',
							REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
							REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
							'$txtTDRGOLAD_Information',
							'$DLIU_LocationCode','1',NULL, '$_SESSION[User_ID]',sysdate(),
							'$_SESSION[User_ID]',sysdate(),NULL,NULL)";

			if($mysqli->query($sql1)){
				$s_sql="SELECT *
						FROM M_DocumentLandAcquisition
						WHERE DLA_Code='$CD_Code_H'";
				$s_query=mysql_query($s_sql);
				$s_arr=mysql_fetch_array($s_query);
				$DLA_ID=$s_arr['DLA_ID'];

				for ($j=1 ; $j<=$jKelengkapan ; $j++) {
					$dnewnum=str_pad($j,2,"0",STR_PAD_LEFT);		
					$CD_Code="$newnum$dnewnum$Company_Code$DocumentGroup_Code$regmonth$regyear";
		
					$optKelengkapan=$_POST["kelengkapan".$j.$i];
					$i_sql="INSERT INTO M_DocumentLandAcquisitionAttribute
							VALUES (NULL,'$CD_Code','$DLA_ID','$j', '$optKelengkapan',
									'1','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
									sysdate(),NULL,NULL)";
					$mysqli->query($i_sql);
				}
			}
			$nnum=$nnum+1;
			$nID=$nID+1;
		}
	}

		for ($i=3 ; $i<=$count ; $i++) {
			$txtNumber=$_POST["txt_Number".$i];
			$txtTDRGOLAD_DocDate=$_POST["txt_DocDate".$i];
			$txtTDRGOLAD_DocDate=date('Y-m-d H:i:s', strtotime($txtTDRGOLAD_DocDate));	
			$txtTDRGOLAD_Block=$_POST["txt_Block".$i];
			$txtTDRGOLAD_Village=$_POST["txt_Village".$i];
			$txtTDRGOLAD_Owner=$_POST["txt_Owner".$i];
			$txtTDRGOLAD_AreaClass=$_POST["txt_AreaClass".$i];
			$txtTDRGOLAD_AreaStatement=((str_replace(" ", "", $_POST["txt_AreaStatement".$i])=="")||(is_numeric($_POST["txt_AreaStatement".$i])==false))?"0":$_POST["txt_AreaStatement".$i];
			$txtTDRGOLAD_AreaPrice=((str_replace(" ", "", $_POST["txt_AreaPrice".$i])=="")||(is_numeric($_POST["txt_AreaPrice".$i])==false))?"0":$_POST["txt_AreaPrice".$i];
			$txtTDRGOLAD_AreaTotalPrice=((str_replace(" ", "", $_POST["txt_AreaTotalPrice".$i])=="")||(is_numeric($_POST["txt_AreaTotalPrice".$i])==false))?"0":$_POST["txt_AreaTotalPrice".$i];
			$txtTDRGOLAD_PlantClass=$_POST["txt_PlantClass".$i];
			$txtTDRGOLAD_PlantQuantity=((str_replace(" ", "", $_POST["txt_PlantQuantity".$i])=="")||(is_numeric($_POST["txt_PlantQuantity".$i])==false))?"0":$_POST["txt_PlantQuantity".$i];
			$txtTDRGOLAD_PlantPrice=((str_replace(" ", "", $_POST["txt_PlantPrice".$i])=="")||(is_numeric($_POST["txt_PlantPrice".$i])==false))?"0":$_POST["txt_PlantPrice".$i];
			$txtTDRGOLAD_PlantTotalPrice=((str_replace(" ", "", $_POST["txt_PlantTotalPrice".$i])=="")||(is_numeric($_POST["txt_PlantTotalPrice".$i])==false))?"0":$_POST["txt_PlantTotalPrice".$i];
			$txtTDRGOLAD_GrandTotal=((str_replace(" ", "", $_POST["txt_GrandTotal".$i])=="")||(is_numeric($_POST["txt_GrandTotal".$i])==false))?"0":$_POST["txt_GrandTotal".$i];
			$txtTDRGOLAD_Information=str_replace("<br>", "\n",$_POST["txt_Information".$i]);
	
			$sql1= "INSERT INTO TD_RegistrationOfLandAcquisitionDocument 
					VALUES (NULL,'$_POST[txtTHRGOLAD_ID]', '$txtTDRGOLAD_DocDate', '$txtTDRGOLAD_Block', 
							'$txtTDRGOLAD_Village', '$txtTDRGOLAD_Owner', '$txtTDRGOLAD_AreaClass', 
							REPLACE('".$txtTDRGOLAD_AreaStatement."',',',''),
							REPLACE('".$txtTDRGOLAD_AreaPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_AreaTotalPrice."',',',''),
							'$txtTDRGOLAD_PlantClass',
							REPLACE('".$txtTDRGOLAD_PlantQuantity."',',',''),
							REPLACE('".$txtTDRGOLAD_PlantPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_PlantTotalPrice."',',',''),
							REPLACE('".$txtTDRGOLAD_GrandTotal."',',',''),
							'$_POST[txtTHRGOLAD_Revision]',
							'$txtTDRGOLAD_Information','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
							sysdate(),NULL,NULL)";
			if($mysqli->query($sql1)){
				$s_sql="SELECT * 
						FROM TD_RegistrationOfLandAcquisitionDocument
						WHERE TDRGOLAD_THRGOLAD_ID='$_POST[txtTHRGOLAD_ID]'
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
						AND TDRGOLAD_Revision='$_POST[txtTHRGOLAD_Revision]'
						AND TDRGOLAD_Information='$txtTDRGOLAD_Information'";
				$s_query=mysql_query($s_sql);
				$s_arr=mysql_fetch_array($s_query);
				for ($j=1 ; $j<=$jKelengkapan ; $j++) {
					$optKelengkapan=$_POST["kelengkapan".$j.$i];
					
					$k_sql="INSERT INTO TD_RegistrationOfLandAcquisitionDocumentDetail 
							VALUES (NULL,'$s_arr[TDRGOLAD_ID]', '$j', '$optKelengkapan',
							'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
					$mysqli->query($k_sql);
		
				}
			}
		}

	$sql3= "UPDATE TH_RegistrationOfLandAcquisitionDocument 
			SET THRGOLAD_Revision='$_POST[txtTHRGOLAD_Revision]',
				THRGOLAD_Update_UserID='$_SESSION[User_ID]',THRGOLAD_Update_Time=sysdate()
			WHERE THRGOLAD_ID='$_POST[txtTHRGOLAD_ID]'
			AND THRGOLAD_Delete_Time IS NULL";
	if ($mysqli->query($sql3)) {
		echo "<meta http-equiv='refresh' content='0; url=$PHP_SELF'>";
	}
}
 
$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>
</script>