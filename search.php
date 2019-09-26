<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																				=
= Dibuat Tanggal	: 03 Okt 2018																						=
= Update Terakhir	: 																									=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Pencarian</title>
<?PHP include ("./config/config_db.php"); ?>
<style>

</style>
<script type="text/javascript" src="./js/datetimepicker.js"></script>
<script language="JavaScript" type="text/JavaScript">
<?php
$assetOwnershipOpt=new stdClass();
$assetOwnershipOpt->company = array();
$assetOwnershipOpt->requester = array();
$assetOwnershipOpt->year = array();
$assetOwnershipOpt->month = array();
$queryAssetOwnership="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(throaod.THROAOD_RegistrationDate) docMonth,YEAR(throaod.THROAOD_RegistrationDate) docYear
						FROM TH_RegistrationOfAssetOwnershipDocument throaod
						LEFT JOIN M_User mu
							ON throaod.THROAOD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON throaod.THROAOD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlAssetOwnership = mysql_query($queryAssetOwnership);
while ($dataAssetOwnership = mysql_fetch_array($sqlAssetOwnership)) {
	if(!array_key_exists($dataAssetOwnership["Company_ID"],$assetOwnershipOpt->company)){
		array_push($assetOwnershipOpt->company, array($dataAssetOwnership["Company_ID"]=>$dataAssetOwnership["Company_Name"]));
	}
	if(!in_array($dataAssetOwnership["docMonth"],$assetOwnershipOpt->company)){
		array_push($assetOwnershipOpt->month,$dataAssetOwnership["docMonth"]);
	}
	if(!in_array($dataAssetOwnership["docYear"],$assetOwnershipOpt->company)){
		array_push($assetOwnershipOpt->year,$dataAssetOwnership["docYear"]);
	}
	if(!array_key_exists($dataAssetOwnership["User_ID"],$assetOwnershipOpt->requester)){
		array_push($assetOwnershipOpt->requester, array($dataAssetOwnership["User_ID"]=>$dataAssetOwnership["User_FullName"]));
	}
}
$landAcquisitionOpt=new stdClass();
$landAcquisitionOpt->company = array();
$landAcquisitionOpt->requester = array();
$landAcquisitionOpt->year = array();
$landAcquisitionOpt->month = array();
$queryLandAcquisition="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(thrgolad.THRGOLAD_RegistrationDate) docMonth,YEAR(thrgolad.THRGOLAD_RegistrationDate) docYear
						FROM TH_RegistrationOfLandAcquisitionDocument thrgolad
						LEFT JOIN M_User mu
							ON thrgolad.THRGOLAD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON thrgolad.THRGOLAD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlLandAcquisition = mysql_query($queryLandAcquisition);
while ($dataLandAcquisition = mysql_fetch_array($sqlLandAcquisition)) {
	if(!array_key_exists($dataLandAcquisition["Company_ID"],$landAcquisitionOpt->company)){
		array_push($landAcquisitionOpt->company, array($dataLandAcquisition["Company_ID"]=>$dataLandAcquisition["Company_Name"]));
	}
	if(!in_array($dataLandAcquisition["docMonth"],$landAcquisitionOpt->company)){
		array_push($landAcquisitionOpt->month,$dataLandAcquisition["docMonth"]);
	}
	if(!in_array($dataLandAcquisition["docYear"],$landAcquisitionOpt->company)){
		array_push($landAcquisitionOpt->year,$dataLandAcquisition["docYear"]);
	}
	if(!array_key_exists($dataLandAcquisition["User_ID"],$landAcquisitionOpt->requester)){
		array_push($landAcquisitionOpt->requester, array($dataLandAcquisition["User_ID"]=>$dataLandAcquisition["User_FullName"]));
	}
}
$legalOpt=new stdClass();
$legalOpt->type = array();
$legalOpt->company = array();
$legalOpt->requester = array();
$legalOpt->year = array();
$legalOpt->month = array();
$licenseOpt=new stdClass();
$licenseOpt->type = array();
$licenseOpt->company = array();
$licenseOpt->requester = array();
$licenseOpt->year = array();
$licenseOpt->month = array();
$queryLegal="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
					throld.THROLD_DocumentGroupID,mdt.DocumentType_ID,mdt.DocumentType_Name,
					MONTH(throld.THROLD_RegistrationDate) docMonth,YEAR(throld.THROLD_RegistrationDate) docYear
				FROM TH_RegistrationOfLegalDocument throld
				LEFT JOIN M_User mu
					ON throld.THROLD_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				LEFT JOIN M_Company mc
					ON throld.THROLD_CompanyID=mc.Company_ID
					AND mc.Company_Delete_Time IS NULL
				LEFT JOIN TD_RegistrationOfLegalDocument tdrold
					ON tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
					AND tdrold.TDROLD_Delete_Time IS NULL
				LEFT JOIN M_DocumentType mdt
					ON tdrold.TDROLD_DocumentTypeID=mdt.DocumentType_ID
					AND mdt.DocumentType_Delete_Time IS NULL";
$sqlLegal = mysql_query($queryLegal);
while ($dataLegal = mysql_fetch_array($sqlLegal)) {
	if($dataLegal["THROLD_DocumentGroupID"]=='1'){
		if(!array_key_exists($dataLegal["DocumentType_ID"],$legalOpt->type)){
			array_push($legalOpt->type, array($dataLegal["DocumentType_ID"]=>$dataLegal["DocumentType_Name"]));
		}
		if(!array_key_exists($dataLegal["Company_ID"],$legalOpt->company)){
			array_push($legalOpt->company, array($dataLegal["Company_ID"]=>$dataLegal["Company_Name"]));
		}
		if(!in_array($dataLegal["docMonth"],$legalOpt->company)){
			array_push($legalOpt->month,$dataLegal["docMonth"]);
		}
		if(!in_array($dataLegal["docYear"],$legalOpt->company)){
			array_push($legalOpt->year,$dataLegal["docYear"]);
		}
		if(!array_key_exists($dataLegal["User_ID"],$legalOpt->requester)){
			array_push($legalOpt->requester, array($dataLegal["User_ID"]=>$dataLegal["User_FullName"]));
		}
	}
	else if($dataLegal["THROLD_DocumentGroupID"]=='2'){
		if(!array_key_exists($dataLegal["DocumentType_ID"],$licenseOpt->type)){
			array_push($licenseOpt->type, array($dataLegal["DocumentType_ID"]=>$dataLegal["DocumentType_Name"]));
		}
		if(!array_key_exists($dataLegal["Company_ID"],$licenseOpt->company)){
			array_push($licenseOpt->company, array($dataLegal["Company_ID"]=>$dataLegal["Company_Name"]));
		}
		if(!in_array($dataLegal["docMonth"],$licenseOpt->company)){
			array_push($licenseOpt->month,$dataLegal["docMonth"]);
		}
		if(!in_array($dataLegal["docYear"],$licenseOpt->company)){
			array_push($licenseOpt->year,$dataLegal["docYear"]);
		}
		if(!array_key_exists($dataLegal["User_ID"],$licenseOpt->requester)){
			array_push($licenseOpt->requester, array($dataLegal["User_ID"]=>$dataLegal["User_FullName"]));
		}
	}
}
$otherLegalOpt=new stdClass();
$otherLegalOpt->company = array();
$otherLegalOpt->requester = array();
$otherLegalOpt->year = array();
$otherLegalOpt->month = array();
$queryOtherLegal="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(throold.THROOLD_RegistrationDate) docMonth,YEAR(throold.THROOLD_RegistrationDate) docYear
						FROM TH_RegistrationOfOtherLegalDocuments throold
						LEFT JOIN M_User mu
							ON throold.THROOLD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON throold.THROOLD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlOtherLegal = mysql_query($queryOtherLegal);
while ($dataOtherLegal = mysql_fetch_array($sqlOtherLegal)) {
	if(!array_key_exists($dataOtherLegal["Company_ID"],$otherLegalOpt->company)){
		array_push($otherLegalOpt->company, array($dataOtherLegal["Company_ID"]=>$dataOtherLegal["Company_Name"]));
	}
	if(!in_array($dataOtherLegal["docMonth"],$otherLegalOpt->company)){
		array_push($otherLegalOpt->month,$dataOtherLegal["docMonth"]);
	}
	if(!in_array($dataOtherLegal["docYear"],$otherLegalOpt->company)){
		array_push($otherLegalOpt->year,$dataOtherLegal["docYear"]);
	}
	if(!array_key_exists($dataOtherLegal["User_ID"],$otherLegalOpt->requester)){
		array_push($otherLegalOpt->requester, array($dataOtherLegal["User_ID"]=>$dataOtherLegal["User_FullName"]));
	}
}
$otherNonLegalOpt=new stdClass();
$otherNonLegalOpt->company = array();
$otherNonLegalOpt->requester = array();
$otherNonLegalOpt->year = array();
$otherNonLegalOpt->month = array();
$queryOtherNonLegal="SELECT DISTINCT mu.User_ID,mu.User_FullName,mc.Company_Name,mc.Company_ID,
							MONTH(throonld.THROONLD_RegistrationDate) docMonth,YEAR(throonld.THROONLD_RegistrationDate) docYear
						FROM TH_RegistrationOfOtherNonLegalDocuments throonld
						LEFT JOIN M_User mu
							ON throonld.THROONLD_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON throonld.THROONLD_CompanyID=mc.Company_ID
							AND mc.Company_Delete_Time IS NULL";
$sqlOtherNonLegal = mysql_query($queryOtherNonLegal);
while ($dataOtherNonLegal = mysql_fetch_array($sqlOtherNonLegal)) {
	if(!array_key_exists($dataOtherNonLegal["Company_ID"],$otherNonLegalOpt->company)){
		array_push($otherNonLegalOpt->company, array($dataOtherNonLegal["Company_ID"]=>$dataOtherNonLegal["Company_Name"]));
	}
	if(!in_array($dataOtherNonLegal["docMonth"],$otherNonLegalOpt->company)){
		array_push($otherNonLegalOpt->month,$dataOtherNonLegal["docMonth"]);
	}
	if(!in_array($dataOtherNonLegal["docYear"],$otherNonLegalOpt->company)){
		array_push($otherNonLegalOpt->year,$dataOtherNonLegal["docYear"]);
	}
	if(!array_key_exists($dataOtherNonLegal["User_ID"],$otherNonLegalOpt->requester)){
		array_push($otherNonLegalOpt->requester, array($dataOtherNonLegal["User_ID"]=>$dataOtherNonLegal["User_FullName"]));
	}
}

$allOpt = array();
$allOpt[1]=$legalOpt;
$allOpt[2]=$licenseOpt;
$allOpt[3]=$landAcquisitionOpt;
$allOpt[4]=$assetOwnershipOpt;
$allOpt[5]=$sqlOtherLegal;
$allOpt[6]=$otherNonLegalOpt;
$encoded = json_encode($allOpt);
?>
var filterObject = <?=($encoded==""?"''":$encoded)?>;

</script>

<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT PEMILIHAN GRUP DOKUMEN
function validateInput(elem) {
	var optTHROLD_DocumentGroupID = document.getElementById('optTHROLD_DocumentGroupID').value;
	if(optTHROLD_DocumentGroupID == -1) {
		alert("Grup Dokumen Belum Dipilih!");
		return false;
	}
	/*else if(optTHROLD_DocumentGroupID == 3) {
		var phase = document.getElementById('phase').value;

		if (phase.replace(" ", "") != "") {
			if(isNaN(phase)){
				alert ("Tahap Harus Berupa Angka [0-9]!");
				return false;
			}
		}
	}*/
	return true;
}

// VALIDASI INPUT PEMILIHAN DOKUMEN YANG AKAN DICETAK BARCODE NYA
function validateBarcodePrint(elem) {
	var returnValue;
	returnValue = false;

	var cBarcodePrint = document.getElementsByName('cBarcodePrint[]');

	for (var i = 0; i < cBarcodePrint.length; i++){
		if (cBarcodePrint[i].checked) {
			returnValue = true;
			break;
		}
	}
	if (!returnValue) {
		alert("Anda Belum Memilih Dokumen Yang Akan Dicetak Barcodenya!");
	}
	return returnValue;
}

// JQUERY UNTUK MENAMPILKAN TIPE DOKUMEN DARI KATEGORI DOKUMEN YANG DIPILIH
function showType(){
	var txtGrupID = document.getElementById('optFilterGrupDokumen').value;
 		$.post("jQuery.DocumentType.php", {
			CategoryID: $('#optFilterGrupDokumen').val(),
			GroupID: txtGrupID
		}, function(response){

			setTimeout("finishAjax('optFilterTipeDokumen', '"+escape(response)+"')", 400);
		});
}

function showCompany(){
	var txtGroupDocID = document.getElementById('optTHROLD_DocumentGroupID').value;
	if(txtGroupDocID == 1 || txtGroupDocID == 2){
		document.getElementById('optDocumentCategory').style.display = "block";
		document.getElementById('td-kategori-dokumen').innerHTML = "Kategori Dokumen";
		$.post("jQuery.TransactionListFilter.php", {
			optTHROLD_DocumentGroupID : txtGroupDocID,
			optFilterHeader : 2
		}, function(response){
			setTimeout("finishAjax('optDocumentCategory', '"+escape(response)+"')", 400);
		});
	}else{
		document.getElementById('optDocumentCategory').style.display = "none";
		document.getElementById('td-kategori-dokumen').innerHTML = "";
	}
	$.post("jQuery.CompanyName.php", {
		GroupDocID: txtGroupDocID
	}, function(response){
		setTimeout("finishAjax('optCompanyID', '"+escape(response)+"')", 400);
	});
	if(txtGroupDocID == 6){
		$('#td-tgl-or-thn-doc').html("Tahun Dokumen");
		$('#tanggal-dokumen').css('display', 'none');
		$('#optTahunDokumen').css('display', 'block');

		$.post("jQuery.YearOfDocument.php", {
			GroupDocID: txtGroupDocID
		}, function(response){
			setTimeout("finishAjax('optTahunDokumen', '"+escape(response)+"')", 400);
		});

	}else{
		if(txtGroupDocID == 5){
			$('#td-tgl-or-thn-doc').html("Tanggal Terbit");
		}else{
			$('#td-tgl-or-thn-doc').html("Tanggal");
		}
		$('#tanggal-dokumen').css('display', 'block');
		$('#optTahunDokumen').css('display', 'none');
	}
}

// MENAMPILKAN DETAIL FILTER
function showFilterDetail() {
	$.post("jQuery.TransactionListFilter.php", {
		optTHROLD_DocumentGroupID : $('#optTHROLD_DocumentGroupID').val(),
		optFilterHeader : $('#optFilterHeader').val()
	}, function(response){

		setTimeout("finishAjax('optFilterDetail', '"+escape(response)+"')", 400);
	});
}
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
}

//MENAMPILKAN DAFTAR FASE BILA GRUP DOKUMEN ADALAH GRL
function showFilter(){
	if (document.getElementById('optTHROLD_DocumentGroupID').value=="3"){
		document.getElementById('optPhase').style.display = "inline";
		document.getElementById('optFilterHeader').options[0]=new Option('--- Pilih Keterangan Dokumen ---', '0');
		document.getElementById('optFilterHeader').options[1]=new Option('Perusahaan', '1');
		document.getElementById('optFilterHeader').options[2]=new Option('Status', '5');
	}
	else {
		document.getElementById('optPhase').style.display = "none";
		document.getElementById('optFilterHeader').options[0]=new Option('--- Pilih Keterangan Dokumen ---', '0');
		document.getElementById('optFilterHeader').options[1]=new Option('Perusahaan', '1');
		document.getElementById('optFilterHeader').options[2]=new Option('Kategori Dokumen', '2');
		document.getElementById('optFilterHeader').options[3]=new Option('Tipe Dokumen', '3');
		document.getElementById('optFilterHeader').options[4]=new Option('Status', '5');
	}
}

// JQUERY UNTUK MENAMPILKAN TIPE DOKUMEN DARI KATEGORI DOKUMEN YANG DIPILIH
function showType(){
	var txtDL_GroupDocID = document.getElementById('DocumentGroup_ID').value;
		$.post("jQuery.DocumentType.php", {
			CategoryID: $('#txtDL_CategoryDocID').val(),
			GroupID: txtDL_GroupDocID
		}, function(response){

			setTimeout("finishAjax('txtDL_TypeDocID', '"+escape(response)+"')", 400);
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

// VALIDASI BAGIAN DETAIL SAAT EDIT DOKUMEn
function validateInputEdit(elem) {
	var returnValue;
	returnValue = true;

		var txtDL_CategoryDocID = document.getElementById('txtDL_CategoryDocID').selectedIndex;
		var txtDL_TypeDocID = document.getElementById('txtDL_TypeDocID').selectedIndex;
		var txtDL_Instance = document.getElementById('txtDL_Instance').value;
		var txtDL_NoDoc = document.getElementById('txtDL_NoDoc').value;
		var txtDL_RegDate = document.getElementById('txtDL_RegDate').value;
		var txtDL_ExpDate = document.getElementById('txtDL_ExpDate').value;
		var txtDL_Information1 = document.getElementById('txtDL_Information1').selectedIndex;
		var txtDL_Information2 = document.getElementById('txtDL_Information2').selectedIndex;
		var Date1 = new Date(txtDL_RegDate);
		var Date2 = new Date(txtDL_ExpDate);

		if(txtDL_CategoryDocID == 0) {
			alert("Kategori Dokumen Belum Dipilih!");
			returnValue = false;
		}
		if(txtDL_TypeDocID == 0) {
			alert("Tipe Dokumen Belum Dipilih!");
			returnValue = false;
		}
		if (txtDL_Instance.replace(" ", "") == "")  {
			alert("Nama Instansi Belum Terisi!");
			returnValue = false;
		}
		if (txtDL_NoDoc.replace(" ", "") == "")  {
			alert("Nomor Dokumen Belum Terisi!");
			returnValue = false;
		}
		if (txtDL_RegDate.replace(" ", "") == "")  {
			alert("Tanggal Publikasi Belum Terisi!");
			returnValue = false;
		}
		if (txtDL_RegDate.replace(" ", "") != "")  {
			if (checkdate(txtDL_RegDate) == false) {
				returnValue = false;
			}
		}
		if (txtDL_ExpDate.replace(" ", "") != "")  {
			if (checkdate(txtDL_ExpDate) == false) {
				returnValue = false;
			}
			else {
				if (Date2 < Date1) {
				alert("Tanggal Habis Masa Berlaku Lebih Kecil Daripada Tanggal Publikasi!");
				returnValue = false;
				}
			}
		}
		if(txtDL_Information1 == 0) {
			alert("Informasi Dokumen 1 Belum Dipilih!");
			returnValue = false;
		}
		if(txtDL_Information2 == 0) {
			alert("Informasi Dokumen 2 Belum Dipilih!");
			returnValue = false;
		}
	return returnValue;
}

// VALIDASI BAGIAN DETAIL SAAT EDIT DOKUMEn
function validateInputEditLA(elem) {
	var returnValue;
	returnValue = true;

		var txtDLA_Phase = document.getElementById('txtDLA_Phase').value;
		var txtDLA_Village = document.getElementById('txtDLA_Village').value;
		var txtDLA_Block = document.getElementById('txtDLA_Block').value;
		var txtDLA_Owner = document.getElementById('txtDLA_Owner').value;
		var txtDLA_Period = document.getElementById('txtDLA_Period').value;
		var txtDLA_DocDate = document.getElementById('txtDLA_DocDate').value;

		if (txtDLA_Phase.replace(" ", "") == "")  {
			alert("Tahap Pembebasan Lahan Belum Terisi!");
			returnValue = false;
		}
		if (txtDLA_Village.replace(" ", "") == "")  {
			alert("Nama Desa Belum Terisi!");
			returnValue = false;
		}
		if (txtDLA_Block.replace(" ", "") == "")  {
			alert("Blok Belum Terisi!");
			returnValue = false;
		}
		if (txtDLA_Owner.replace(" ", "") == "")  {
			alert("Nama Pemilik Belum Terisi!");
			returnValue = false;
		}
		if (txtDLA_Period.replace(" ", "") == "")  {
			alert("Periode Ganti Rugi Belum Terisi!");
			returnValue = false;
		}
		if (txtDLA_Period.replace(" ", "") != "")  {
			if (checkdate(txtDLA_Period) == false) {
				returnValue = false;
			}
		}
		if (txtDLA_DocDate.replace(" ", "") == "")  {
			alert("Tanggal Dokumen Belum Terisi!");
			returnValue = false;
		}
		if (txtDLA_DocDate.replace(" ", "") != "")  {
			if (checkdate(txtDLA_DocDate) == false) {
				returnValue = false;
			}
		}
	return returnValue;
}

//PERHITUNGAN TOTAL
function countTotal(){
	document.getElementById('txtDLA_AreaTotalPrice').value=document.getElementById('txtDLA_AreaStatement').value * document.getElementById('txtDLA_AreaPrice').value;
	document.getElementById('txtDLA_PlantTotalPrice').value=document.getElementById('txtDLA_PlantQuantity').value * document.getElementById('txtDLA_PlantPrice').value;
	document.getElementById('txtDLA_GrandTotal').value=parseInt(document.getElementById('txtDLA_AreaTotalPrice').value) + parseInt(document.getElementById('txtDLA_PlantTotalPrice').value);
}
</script>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID)){
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$ActionContent ="
	<form name='list' method='GET' action='search.php'>
	<table width='100%' id='mytable' class='stripeMe'>
	<tr>
		<th colspan=4>Pencarian Dokumen</th>
	</tr>
	<tr>
		<td>Grup Dokumen</td>
		<td>
			<select name='optTHROLD_DocumentGroupID' id='optTHROLD_DocumentGroupID' onchange='showCompany();'>
				<option value='-1'>--- Pilih Grup Dokumen ---</option>";

			$query = "SELECT DISTINCT DocumentGroup_ID,DocumentGroup_Name
					  FROM M_DocumentGroup
					  WHERE DocumentGroup_Delete_Time is NULL";
			$sql = mysql_query($query);

			while ($field = mysql_fetch_object($sql) ){
				$selected_grupDok = "";
				if(!empty($_GET['optTHROLD_DocumentGroupID']) && $_GET['optTHROLD_DocumentGroupID'] == $field->DocumentGroup_ID){
					$selected_grupDok = "selected";
				}

$ActionContent .="
				<option value='".$field->DocumentGroup_ID."' $selected_grupDok>".$field->DocumentGroup_Name."</option>";
			}
$ActionContent .="
			</select>
		</td>
		<!--<td width='25%'>
			<input name='listdocument' type='submit' value='Cari' class='button-small' onclick='return validateInput(this);'/><input name='filter' type='submit' value='Filter' class='button-small'/>
		</td>-->
	</tr>
	<tr>
		<td id='td-kategori-dokumen'>";
		if( !empty($_GET['optTHROLD_DocumentGroupID']) && ($_GET['optTHROLD_DocumentGroupID'] == 1 ||  $_GET['optTHROLD_DocumentGroupID'] == 2) ){
			$ActionContent .="Kategori Dokumen";
		}
		$ActionContent .="</td>
		<td>
			<select name='optDocumentCategory' id='optDocumentCategory'";
				if( !empty($_GET['optTHROLD_DocumentGroupID']) && ($_GET['optTHROLD_DocumentGroupID'] == 1 ||  $_GET['optTHROLD_DocumentGroupID'] == 2) ){
					$ActionContent .=">";
					$query="SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
							FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
							WHERE dgct.DGCT_Delete_Time is NULL
							AND dgct.DGCT_DocumentGroupID=".$_GET['optTHROLD_DocumentGroupID']."
							AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
							ORDER BY dc.DocumentCategory_Name";
					$sql = mysql_query($query);
					$ActionContent .="<option value=\"0\">--- Pilih Kategori Dokumen ---</option>";
					while ($arr = mysql_fetch_array($sql)) {
						$selected_CategoryDok = "";
						if(!empty($_GET['optDocumentCategory']) && $_GET['optDocumentCategory'] == $arr['DocumentCategory_ID']){
							$selected_CategoryDok = " selected";
						}
						$ActionContent .="<option value=\"$arr[DocumentCategory_ID]\" $selected_CategoryDok>
							$arr[DocumentCategory_Name]
						</option>";
					}
				}else{
					$ActionContent .=" style='display:none;'>
					<option value='-1'>--- Pilih Grup Dokumen Dahulu ---</option>";
				}
				$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Perusahaan</td>
		<td>
			<select name='optCompanyID' id='optCompanyID' onchange='showFilter();'>";
				if($_GET['optTHROLD_DocumentGroupID'] == "-1" || $_GET['optTHROLD_DocumentGroupID'] == "0"){
					$ActionContent .= "<option value='-1'>--- Pilih Grup Dokumen Dahulu ---</option>";
				}else{
					if($_GET['optTHROLD_DocumentGroupID'] == "1" or $_GET['optTHROLD_DocumentGroupID'] == "2"){
				        $query = "SELECT DISTINCT DL_CompanyID as Company_ID, Company_Name FROM M_DocumentLegal
				            LEFT JOIN M_Company ON DL_CompanyID=Company_ID
							WHERE DL_GroupDocID='$_GET[optTHROLD_DocumentGroupID]'";
				    }elseif($_GET['optTHROLD_DocumentGroupID'] == "3"){
				        $query = "SELECT DISTINCT DLA_CompanyID as Company_ID, Company_Name FROM M_DocumentLandAcquisition
				            LEFT JOIN M_Company ON DLA_CompanyID=Company_ID";
				    }elseif($_GET['optTHROLD_DocumentGroupID'] == "4"){
				        $query = "SELECT DISTINCT DAO_CompanyID as Company_ID, Company_Name FROM M_DocumentAssetOwnership
				            LEFT JOIN M_Company ON DAO_CompanyID=Company_ID";
				    }elseif($_GET['optTHROLD_DocumentGroupID'] == "5"){
				        $query = "SELECT DISTINCT DOL_CompanyID as Company_ID, Company_Name FROM M_DocumentsOtherLegal
				            LEFT JOIN M_Company ON DOL_CompanyID=Company_ID";
				    }elseif($_GET['optTHROLD_DocumentGroupID'] == "6"){
				        $query = "SELECT DISTINCT DONL_CompanyID as Company_ID, Company_Name FROM M_DocumentsOtherNonLegal
				            LEFT JOIN M_Company ON DONL_CompanyID=Company_ID";
				    }else{
				        $query = "";
				    }
					$sql = mysql_query($query);
					$num=mysql_num_rows($query);
					if ($num=="0"){
						$ActionContent .="<option value=\"-1\">--- Tidak Ada Daftar Perusahaan ---</option>";
					}else{
						$ActionContent .="<option value=\"-1\">--- Pilih Perusahaan ---</option>";

						while ($arr = mysql_fetch_array($sql)) {
							$selected_companyDok = "";
							if(!empty($_GET['optCompanyID']) && $_GET['optCompanyID'] == $arr['Company_ID']){
								$selected_companyDok = "selected";
							}
							$ActionContent .="<option value=".$arr['Company_ID']." style=\"width:500px\" $selected_companyDok>
							 	$arr[Company_Name]
							</option>";
						}
					}
				}
			$ActionContent .="</select>
		</td>
	</tr>
	<!--tr>
		<td>Tahap Dokumen</td>
		<td>
			<select name='optDocumentStepID' id='optDocumentStepID' onchange='showFilter();'>
				<option value='-1'>--- Pilih Tahap Dokumen ---</option>
				<option value='1'";
					if(!empty($_GET['optDocumentStepID']) && $_GET['optDocumentStepID'] == "1") $ActionContent .= " selected";
					$ActionContent .=">Registrasi</option>
				<option value='2'";
					if(!empty($_GET['optDocumentStepID']) && $_GET['optDocumentStepID'] == "2") $ActionContent .= " selected";
					$ActionContent .=">Permintaan</option>
				<option value='3'";
					if(!empty($_GET['optDocumentStepID']) && $_GET['optDocumentStepID'] == "3") $ActionContent .= " selected";
					$ActionContent .=">Pengeluaran</option>
				<option value='4'";
					if(!empty($_GET['optDocumentStepID']) && $_GET['optDocumentStepID'] == "4") $ActionContent .= " selected";
					$ActionContent .=">Pengembalian</option>
			</select>
		</td>
	</tr-->
	<tr>
		<td id='td-tgl-or-thn-doc'>Tanggal</td>
		<td>
			<span id='tanggal-dokumen'>
				<input type='text' size='10' readonly='readonly' name='txtDateStart' id='txtDateStart'
					placeholder='Tanggal awal' onclick=\"javascript:NewCssCal('txtDateStart', 'MMddyyyy');\"
					";
					if(!empty($_GET['txtDateStart'])) $ActionContent .= " value='$_GET[txtDateStart]'";
					$ActionContent .="/>
				<span>sampai</span>
				<input type='text' size='10' readonly='readonly' name='txtDateEnd' id='txtDateEnd'
					placeholder='Tanggal akhir' onclick=\"javascript:NewCssCal('txtDateEnd', 'MMddyyyy');\"
					";
					if(!empty($_GET['txtDateEnd'])) $ActionContent .= " value='$_GET[txtDateEnd]'";
					$ActionContent .="/>
			</span>
			<select name='optTahunDokumen' id='optTahunDokumen' style='display:none'>
				<option value='-1'>--- Pilih Grup Dokumen Dahulu ---</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Status Dokumen</td>
		<td>
			<select name='optDocumentStatusID' id='optDocumentStatusID' onchange='showFilter();'>
				<option value='-1'>--- Pilih Status Dokumen ---</option>";

			$query = "SELECT DISTINCT LDS_Name
						FROM M_LoanDetailStatus
						WHERE LDS_Delete_Time IS NULL";
			$sql = mysql_query($query);

			while ($field = mysql_fetch_object($sql) ){
				$selected_statusDok = "";
				if(!empty($_GET['optDocumentStatusID']) && $_GET['optDocumentStatusID'] == $field->LDS_Name){
					$selected_statusDok = "selected";
				}
$ActionContent .="
				<option value='".$field->LDS_Name."' $selected_statusDok>".$field->LDS_Name."</option>";
			}
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>SEARCH</td>
		<td>
			<input name='txtSearch' type='text' placeholder='Filter' ";
				if(!empty($_GET['txtSearch'])) $ActionContent .= " value='$_GET[txtSearch]'";
				$ActionContent .="/>
		</td>
	</tr>
	<tr>
		<th colspan='2'>
			<input name='listdocument' formaction='search.php' type='submit' value='Cari' class='button' onclick='return validateInput(this);'/>
			<input name='resetFilter' type='reset' value='reset' class='button'/>
			<input name='export_to_excel' formaction='result-search-export-to-excel.php' type='submit' value='&nbsp;Export to Excel&nbsp;' class='button-blue' onclick='return validateInput(this);' />
		</th>
	</tr>";
$ActionContent .="
	</table>
	</form>
";

/* ====== */
/* ACTION */
/* ====== */

	if(isset($_GET['listdocument'])) {

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;
	if ($_GET['optTHROLD_DocumentGroupID'] ==  '1' or $_GET['optTHROLD_DocumentGroupID'] == '2'){
		$query = "SELECT dl.DL_ID, dl.DL_DocCode, c.Company_Name, dc.DocumentCategory_Name,
		dt.DocumentType_Name, dl.DL_PubDate, dl.DL_ExpDate, lds.LDS_Name, dg.DocumentGroup_Name,
		dl.DL_NoDoc, dl.DL_Location, u.User_FullName
		FROM
		M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt, M_LoanDetailStatus lds,
		M_DocumentGroup dg, M_DocumentInformation1 di1, M_DocumentInformation2 di2, M_User u
		WHERE
		dl.DL_GroupDocID = '$_GET[optTHROLD_DocumentGroupID]'
		AND dl.DL_GroupDocID = dg.DocumentGroup_ID
		AND dl.DL_CompanyID = c.Company_ID
		AND dl.DL_CategoryDocID = dc.DocumentCategory_ID
		AND dl.DL_TypeDocID = dt.DocumentType_ID
		AND lds.LDS_ID = dl.DL_Status
		AND dl.DL_RegUserID = u.User_ID
		AND dl.DL_Information1 = di1.DocumentInformation1_ID
		AND dl.DL_Information2 = di2.DocumentInformation2_ID
		AND dl.DL_Delete_Time IS NULL ";

		if ($_GET['txtSearch']) {
			$search=$_GET['txtSearch'];
			$query .="AND (
						dl.DL_DocCode LIKE '%$search%'
						OR dl.DL_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR dl.DL_CategoryDocID LIKE '%$search%'
						OR dc.DocumentCategory_Name LIKE '%$search%'
						OR dl.DL_TypeDocID LIKE '%$search%'
						OR dt.DocumentType_Name LIKE '%$search%'
						OR dl.DL_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dl.DL_Information1 LIKE '%$search%'
						OR di1.DocumentInformation1_Name LIKE '%$search%'
						OR dl.DL_Information2 LIKE '%$search%'
						OR di2.DocumentInformation2_Name LIKE '%$search%'
						OR dl.DL_Information3 LIKE '%$search%'
						OR dl.DL_Instance LIKE '%$search%'
						OR dl.DL_RegTime LIKE '%$search%'
						OR dl.DL_NoDoc LIKE '%$search%'
						OR dl.DL_PubDate LIKE '%$search%'
						OR dl.DL_ExpDate LIKE '%$search%'
						OR lds.LDS_Name LIKE '%$search%'
					)";
		}
		if ($_GET['optDocumentCategory']) {
			$query .="AND dl.DL_CategoryDocID='".$_GET['optDocumentCategory']."' ";
		}
		if ($_GET['optCompanyID']!=-1) {
			$query .="AND dl.DL_CompanyID='".$_GET['optCompanyID']."' ";
		}
		if ($_GET['optDocumentStatusID']!=-1) {
			$query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
		}
		if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
			$query .="AND (dl.DL_PubDate BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y')
			)";
		}
		else if($_GET['txtDateStart']!=""){
			$query .="AND (dl.DL_PubDate > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y'))";
		}
		else if($_GET['txtDateEnd']!=""){
			$query .="AND (dl.DL_PubDate < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
		}

		$querylimit .="ORDER BY dl.DL_ID LIMIT $offset, $dataPerPage";
	}
	elseif ($_GET['optTHROLD_DocumentGroupID']=='3'){
		$query = "SELECT dla.DLA_ID, c.Company_Name, dla.DLA_Phase, dla.DLA_Period, dla.DLA_DocRevision, lds.LDS_Name,
						 dla.DLA_Code, dla.DLA_Location, u.User_FullName
				  FROM M_DocumentLandAcquisition dla, M_Company c, M_User u,  M_LoanDetailStatus lds
				  WHERE c.Company_ID=dla.DLA_CompanyID
				  AND dla.DLA_Delete_Time IS NULL
				  AND dla.DLA_Status=lds.LDS_ID
				  AND dla.DLA_RegUserID=u.User_ID ";

		if ($_GET['txtSearch']) {
			$search=$_GET['txtSearch'];
			$query .="AND (
						dla.DLA_Code LIKE '%$search%'
						OR dla.DLA_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR dla.DLA_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dla.DLA_Information LIKE '%$search%'
						OR dla.DLA_RegTime LIKE '%$search%'
						OR dla.DLA_Phase LIKE '%$search%'
						OR dla.DLA_Period LIKE '%$search%'
						OR dla.DLA_DocDate LIKE '%$search%'
						OR dla.DLA_Block LIKE '%$search%'
						OR dla.DLA_Village LIKE '%$search%'
						OR dla.DLA_Owner LIKE '%$search%'
						OR dla.DLA_Information LIKE '%$search%'
						OR dla.DLA_AreaClass LIKE '%$search%'
						OR dla.DLA_AreaStatement LIKE '%$search%'
						OR dla.DLA_AreaPrice LIKE '%$search%'
						OR dla.DLA_AreaTotalPrice LIKE '%$search%'
						OR dla.DLA_PlantClass LIKE '%$search%'
						OR dla.DLA_PlantQuantity LIKE '%$search%'
						OR dla.DLA_PlantPrice LIKE '%$search%'
						OR dla.DLA_PlantTotalPrice LIKE '%$search%'
						OR dla.DLA_GrandTotal LIKE '%$search%'
						OR lds.LDS_Name LIKE '%$search%'
					)";
		}
		if ($_GET['optCompanyID']!=-1) {
			$query .="AND dla.DLA_CompanyID='".$_GET['optCompanyID']."' ";
		}
		if ($_GET['optDocumentStatusID']!=-1) {
			$query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
		}
		if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
			$query .="AND (dl.DLA_Period BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
		}
		else if($_GET['txtDateStart']!=""){
			$query .="AND (dl.DLA_Period > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y'))";
		}
		else if($_GET['txtDateEnd']!=""){
			$query .="AND (dl.DLA_Period < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
		}
		// elseif ($_GET[phase]<>NULL) {
			// $query .="AND dla.DLA_Phase='$_GET[phase]' ";
		// }
		$querylimit .="ORDER BY dla.DLA_ID LIMIT $offset, $dataPerPage";
	}
	elseif ($_GET['optTHROLD_DocumentGroupID']=='4'){
		$query = "SELECT dao.DAO_ID, m_mk.MK_Name,
						 CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
						  THEN
							(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
						  ELSE
							(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
						 END nama_pemilik,
						 dao.DAO_Employee_NIK,
						 dao.DAO_NoPolisi,
						 CASE WHEN dao.DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
							 WHEN dao.DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
							 ELSE DATE_FORMAT(dao.DAO_STNK_StartDate, '%d/%m/%Y')
						 END AS start_stnk,
						 CASE WHEN dao.DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
							 WHEN dao.DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
							 ELSE DATE_FORMAT(dao.DAO_STNK_ExpiredDate, '%d/%m/%Y')
						 END AS expired_stnk,
						 CASE WHEN dao.DAO_Pajak_StartDate LIKE '%0000-00-00%' THEN '-'
							 WHEN dao.DAO_Pajak_StartDate LIKE '%1970-01-01%' THEN '-'
							 ELSE DATE_FORMAT(dao.DAO_Pajak_StartDate, '%d/%m/%Y')
						 END AS start_pajak,
						 CASE WHEN dao.DAO_Pajak_ExpiredDate LIKE '%0000-00-00%' THEN '-'
							 WHEN dao.DAO_Pajak_ExpiredDate LIKE '%1970-01-01%' THEN '-'
							 ELSE DATE_FORMAT(dao.DAO_Pajak_ExpiredDate, '%d/%m/%Y')
						 END AS expired_pajak,
						 dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate, dao.DAO_Pajak_StartDate, dao.DAO_Pajak_ExpiredDate,
						 lds.LDS_Name, dao.DAO_DocCode, dao.DAO_Location, u.User_FullName
				  FROM M_DocumentAssetOwnership dao, M_User u, M_LoanDetailStatus lds, db_master.M_MerkKendaraan m_mk
				  WHERE dao.DAO_Delete_Time IS NULL
				  AND dao.DAO_Status=lds.LDS_ID
				  AND dao.DAO_RegUserID=u.User_ID
				  AND m_mk.MK_ID=dao.DAO_MK_ID
				";

		if ($_GET['txtSearch']) {
			$search=$_GET['txtSearch'];
			$query .="AND (
						dao.DAO_DocCode LIKE '%$search%'
						OR dao.DAO_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dao.DAO_RegTime LIKE '%$search%'
						OR lds.LDS_Name LIKE '%$search%'
						OR dao.DAO_STNK_StartDate LIKE '%$search%'
						OR dao.DAO_STNK_ExpiredDate LIKE '%$search%'
						OR dao.DAO_Pajak_StartDate LIKE '%$search%'
						OR dao.DAO_Pajak_ExpiredDate LIKE '%$search%'
						OR (dao.DAO_Employee_NIK LIKE '%$search%' OR e.Employee_FullName LIKE '%$search%' OR c.Company_Name LIKE '%$search%')
						OR m_mk.MK_Name LIKE '%$search%'
						OR dao.DAO_NoPolisi LIKE '%$search%'
						OR dao.DAO_NoBPKB LIKE '%$search%'
						OR dao.DAO_NoMesin LIKE '%$search%'
						OR dao.DAO_NoRangka LIKE '%$search%'
						OR dao.DAO_Type LIKE '%$search%'
						OR dao.DAO_Jenis LIKE '%$search%'
						OR dao.DAO_Lokasi_PT LIKE '%$search%'
						OR dao.DAO_Region LIKE '%$search%'
						OR dao.DAO_Keterangan LIKE '%$search%'
					)";
		}
		if ($_GET['optCompanyID']!=-1) {
			$query .="AND dao.DAO_CompanyID='".$_GET['optCompanyID']."' ";
		}
		if ($_GET['optDocumentStatusID']!=-1) {
			$query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
		}
		if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
			$query .="AND ((dao.DAO_STNK_ExpiredDate BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y') OR (dao.DAO_Pajak_ExpiredDate BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
		}
		else if($_GET['txtDateStart']!=""){
			$query .="AND (
				dao.DAO_STNK_ExpiredDate > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y')
				OR dao.DAO_Pajak_ExpiredDate > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y')
			)";
		}
		else if($_GET['txtDateEnd']!=""){
			$query .="AND (
				dao.DAO_STNK_ExpiredDate < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y')
				OR dao.DAO_Pajak_ExpiredDate < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y')
			)";
		}
		$querylimit .="ORDER BY dao.DAO_ID LIMIT $offset, $dataPerPage";
	}
	elseif ($_GET['optTHROLD_DocumentGroupID']=='5'){
		$query = "SELECT dol.DOL_ID, c.Company_Name, m_dc.DocumentCategory_Name,
						 dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
						 dol.DOL_TglTerbit, dol.DOL_TglBerakhir, lds.LDS_Name, dol.DOL_DocCode,
						 dol.DOL_Location, u.User_FullName
				  FROM M_DocumentsOtherLegal dol, M_Company c, M_User u,  M_LoanDetailStatus lds,
				  	db_master.M_DocumentCategory m_dc
				  WHERE c.Company_ID=dol.DOL_CompanyID
				  AND dol.DOL_Delete_Time IS NULL
				  AND dol.DOL_Status=lds.LDS_ID
				  AND dol.DOL_RegUserID=u.User_ID
				  AND m_dc.DocumentCategory_ID=DOL_CategoryDocID
				 ";

		if ($_GET['txtSearch']) {
			$search=$_GET['txtSearch'];
			$query .="AND (
						dol.DOL_DocCode LIKE '%$search%'
						OR dol.DOL_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR dol.DOL_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dol.DOL_RegTime LIKE '%$search%'
						OR lds.LDS_Name LIKE '%$search%'
						OR m_dc.DocumentCategory_Name LIKE '%$search%'
						OR dol.DOL_NamaDokumen LIKE '%$search%'
						OR dol.DOL_InstansiTerkait LIKE '%$search%'
						OR dol.DOL_NoDokumen LIKE '%$search%'
						OR dol.DOL_TglTerbit LIKE '%$search%'
						OR dol.DOL_TglBerakhir LIKE '%$search%'
					)";
		}
		if ($_GET['optCompanyID']!=-1) {
			$query .="AND dol.DOL_CompanyID='".$_GET['optCompanyID']."' ";
		}
		if ($_GET['optDocumentStatusID']!=-1) {
			$query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
		}
		if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
			$query .="AND (dol.DOL_TglTerbit BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
		}
		else if($_GET['txtDateStart']!=""){
			$query .="AND (dol.DOL_TglTerbit > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y'))";
		}
		else if($_GET['txtDateEnd']!=""){
			$query .="AND (dol.DOL_TglTerbit < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
		}
		$querylimit .="ORDER BY dol.DOL_ID LIMIT $offset, $dataPerPage";
	}
	elseif ($_GET['optTHROLD_DocumentGroupID']=='6'){
		$query = "SELECT donl.DONL_ID, c.Company_Name, donl.DONL_NoDokumen, donl.DONL_NamaDokumen,
					donl.DONL_TahunDokumen, m_d.Department_Name,
					lds.LDS_Name, donl.DONL_DocCode, donl.DONL_Location, u.User_FullName
				  FROM M_DocumentsOtherNonLegal donl, M_Company c, M_User u, M_LoanDetailStatus lds,
				  	db_master.M_Department m_d
				  WHERE c.Company_ID=donl.DONL_CompanyID
				  AND donl.DONL_Delete_Time IS NULL
				  AND donl.DONL_Status=lds.LDS_ID
				  AND donl.DONL_RegUserID=u.User_ID
				  AND m_d.Department_Code=donl.DONL_Dept_Code
				 ";

		if ($_GET['txtSearch']) {
			$search=$_GET['txtSearch'];
			$query .="AND (
						donl.DONL_DocCode LIKE '%$search%'
						OR donl.DONL_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR donl.DONL_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR donl.DONL_RegTime LIKE '%$search%'
						OR lds.LDS_Name LIKE '%$search%'
						OR donl.DONL_NoDokumen LIKE '%$search%'
						OR donl.DONL_NamaDokumen LIKE '%$search%'
						OR donl.DONL_TahunDokumen LIKE '%$search%'
						OR m_d.Department_Name LIKE '%$search%'
						OR donl.DONL_Dept_Code LIKE '%$search%'
					) ";
		}
		if ($_GET['optCompanyID']!=-1) {
			$query .="AND donl.DONL_CompanyID='".$_GET['optCompanyID']."' ";
		}
		if ($_GET['optDocumentStatusID']!=-1) {
			$query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
		}
		else if($_GET['optTahunDokumen']!=-1){
			$query .="AND donl.DONL_TahunDokumen = STR_TO_DATE('".$_GET['optTahunDokumen']."', '%Y')";
		}
		$querylimit .="ORDER BY donl.DONL_ID LIMIT $offset, $dataPerPage";
	}

$queryAll=$query.$querylimit;
// echo $queryAll;
$sql = mysql_query($queryAll);
$num = mysql_num_rows($sql);
$sqldg = mysql_query($queryAll);
$arr = mysql_fetch_array($sqldg);
//echo $queryAll;
	if ($_GET['optTHROLD_DocumentGroupID'] == '1' or $_GET['optTHROLD_DocumentGroupID'] == '2'){
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Kategori</th>
				<th>Tipe</th>
				<th>Tanggal Terbit</th>
				<th>Tanggal Habis Masa Berlaku</th>
				<th>Status</th>
				<th>No. Dokumen</th>
				<th>Pendaftar</th>
			</tr>
			<tr>
				<td colspan=7 align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='GET' target='_blank' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th colspan='10' align='center'>Daftar Dokumen $arr[DocumentGroup_Name]</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Kategori</th>
				<th>Tipe</th>
				<th>Tanggal Terbit</th>
				<th>Tanggal Habis Masa Berlaku</th>
				<th>Status</th>
				<th>No. Dokumen</th>
				<th>Pendaftar</th>
			</tr>
		";

			while ($field = mysql_fetch_array($sql)) {
				$fpubdate=(strpos($field['DL_PubDate'], '0000-00-00') !== false || strpos($field['DL_PubDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field['DL_PubDate']));
				$fexpdate=(strpos($field['DL_ExpDate'], '0000-00-00') !== false || strpos($field['DL_ExpDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field['DL_ExpDate']));
		$MainContent .="
			<tr>
				<td class='center'>$field[DL_ID]</td>
				<td class='center'>
					<a href='$PHP_SELF?act=detail& id=$field[DL_DocCode]' class='underline'>$field[DL_DocCode]</a>
				</td>
				<td class='center'>$field[Company_Name]</td>
				<td class='center'>$field[DocumentCategory_Name]</td>
				<td class='center'>$field[DocumentType_Name]</td>
				<td class='center'>$fpubdate</td>
				<td class='center'>$fexpdate</td>
				<td class='center'>$field[LDS_Name]</td>
				<td class='center'>$field[DL_NoDoc]</td>
				<td class='center'>$field[User_FullName]</td>
			</tr>
		";
			$no=$no+1;
			}
		$MainContent .="
			</table>
			</form>
		";
		}
	}

	elseif ($_GET['optTHROLD_DocumentGroupID']=='3'){
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Tahap</th>
				<th>Periode</th>
				<th>Revisi</th>
				<th>Status</th>
				<th>Pendaftar</th>
			</tr>
			<tr>
				<td colspan=8 align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='GET' action='print-land-acquisition-document-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th colspan='8' align='center'>Daftar Dokumen Pembebasan Lahan</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Tahap</th>
				<th>Periode</th>
				<th>Revisi</th>
				<th>Status</th>
				<th>Pendaftar</th>
			</tr>
		";

			while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field[3]);
				$fregdate=date("j M Y", $regdate);
		$MainContent .="
			<tr>
				<td class='center'>$field[DLA_ID]</td>
				<td class='center'>
					<a href='$PHP_SELF?act=detailLA&id=$field[0]' class='underline'>$field[DLA_Code]</a></td>
				<td class='center'>$field[1]</td>
				<td class='center'>$field[2]</td>
				<td class='center'>$fregdate</td>
				<td class='center'>$field[4]</td>
				<td class='center'>$field[5]</td>
				<td class='center'>$field[User_FullName]</td>
			</tr>
		";
			$no=$no+1;
			}
		$MainContent .="
			</table>
			</form>
		";
		}
	}

	elseif ($_GET['optTHROLD_DocumentGroupID']=='4'){
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th rowspan='2'>ID</th>
				<th rowspan='2'>Kode Dokumen</th>
				<th rowspan='2'>Merk Kendaraan</th>
				<th rowspan='2'>Nama Pemilik</th>
				<th rowspan='2'>No. Polisi</th>
				<th colspan='2'>STNK</th>
				<th colspan='2'>Pajak</th>
				<th rowspan='2'>Status</th>
				<th rowspan='2'>Pendaftar</th>
			</tr>
			<tr>
				<th>Start Date</th>
				<th>Expired Date</th>
				<th>Start Date</th>
				<th>Expired Date</th>
			</tr>
			<tr>
				<td colspan=12 align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='GET' action='print-asset-ownership-document-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th colspan='12' align='center'>Daftar Dokumen Kepemilikan Aset</th>
			</tr>
			<tr>
				<th rowspan='2'>ID</th>
				<th rowspan='2'>Kode Dokumen</th>
				<th rowspan='2'>Merk Kendaraan</th>
				<th rowspan='2'>Nama Pemilik</th>
				<th rowspan='2'>No. Polisi</th>
				<th colspan='2'>STNK</th>
				<th colspan='2'>Pajak</th>
				<th rowspan='2'>Status</th>
				<th rowspan='2'>Pendaftar</th>
			</tr>
			<tr>
				<th>Start Date</th>
				<th>Expired Date</th>
				<th>Start Date</th>
				<th>Expired Date</th>
			</tr>
		";

			while ($field = mysql_fetch_array($sql)) {
		$MainContent .="
			<tr>
				<td class='center'>$field[DAO_ID]</td>
				<td class='center'>
					<a href='$PHP_SELF?act=detailAO&id=$field[DAO_DocCode]' class='underline'>$field[DAO_DocCode]</a></td>
				<td class='center'>$field[MK_Name]</td>
				<td class='center'>$field[nama_pemilik]</td>
				<td class='center'>$field[DAO_NoPolisi]</td>
				<td class='center'>$field[start_stnk]</td>
				<td class='center'>$field[expired_stnk]</td>
				<td class='center'>$field[start_pajak]</td>
				<td class='center'>$field[expired_pajak]</td>
				<td class='center'>$field[LDS_Name]</td>
				<td class='center'>$field[User_FullName]</td>
			</tr>
		";
			$no=$no+1;
			}
		$MainContent .="
			</table>
			</form>
		";
		}
	}

	elseif ($_GET['optTHROLD_DocumentGroupID']=='5'){
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Kategori Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
			    <th>No. Dokumen</th>
			    <th>Tanggal Terbit</th>
			    <th>Tanggal Berakhir</th>
				<th>Status</th>
				<th>Pendaftar</th>
			</tr>
			<tr>
				<td colspan=11 align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='GET' action='print-other-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th colspan='11' align='center'>Daftar Dokumen Lainnya (Legal)</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>Kategori Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
			    <th>No. Dokumen</th>
			    <th>Tanggal Terbit</th>
			    <th>Tanggal Berakhir</th>
				<th>Status</th>
				<th>Pendaftar</th>
			</tr>
		";

			while ($field = mysql_fetch_array($sql)) {

				$tgl_terbit=(strpos($field[6], '0000-00-00') !== false || strpos($field[6], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field[6]));
				$tgl_berakhir=(strpos($field[7], '0000-00-00') !== false || strpos($field[7], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field[7]));
		$MainContent .="
			<tr>
				<td class='center'>$field[DOL_ID]</td>
				<td class='center'>
					<a href='$PHP_SELF?act=detailOL&id=$field[DOL_DocCode]' class='underline'>$field[DOL_DocCode]</a></td>
				<td class='center'>$field[1]</td>
				<td class='center'>$field[2]</td>
				<td class='center'>$field[3]</td>
				<td class='center'>$field[4]</td>
				<td class='center'>$field[5]</td>
				<td class='center'>$tgl_terbit</td>
				<td class='center'>$tgl_berakhir</td>
				<td class='center'>$field[8]</td>
				<td class='center'>$field[User_FullName]</td>
			</tr>
		";
			$no=$no+1;
			}
		$MainContent .="
			</table>
			</form>
		";
		}
	}

	elseif ($_GET['optTHROLD_DocumentGroupID']=='6'){
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>No. Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Tahun Dokumen</th>
				<th>Departemen</th>
				<th>Status</th>
				<th>Pendaftar</th>
			</tr>
			<tr>
				<td colspan=9 align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='GET' action='print-other-non-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th colspan='9' align='center'>Daftar Dokumen Lainnya (Di Luar Legal)</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>Kode Dokumen</th>
				<th>Perusahaan</th>
				<th>No. Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Tahun Dokumen</th>
				<th>Departemen</th>
				<th>Status</th>
				<th>Pendaftar</th>
			</tr>
		";

			while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field[3]);
				$fregdate=date("j M Y", $regdate);
		$MainContent .="
			<tr>
				<td class='center'>$field[DONL_ID]</td>
				<td class='center'>
					<a href='$PHP_SELF?act=detailONL&id=$field[DONL_DocCode]' class='underline'>$field[DONL_DocCode]</a></td>
				<td class='center'>$field[1]</td>
				<td class='center'>$field[2]</td>
				<td class='center'>$field[3]</td>
				<td class='center'>$field[4]</td>
				<td class='center'>$field[5]</td>
				<td class='center'>$field[6]</td>
				<td class='center'>$field[User_FullName]</td>
			</tr>
		";
			$no=$no+1;
			}
		$MainContent .="
			</table>
			</form>
		";
		}
	}

		$sql1 = mysql_query($query);
		$num1 = mysql_num_rows($sql1);

		$getLink=$_SERVER["REQUEST_URI"];
		$arr = explode("&page=", $getLink);
		$link = $arr[0];

		$jumData = $num1;
		$jumPage = ceil($jumData/$dataPerPage);

		$prev=$noPage-1;
		$next=$noPage+1;

		if ($noPage > 1)
			$Pager.="<a href='$link&page=$prev'>&lt;&lt; Prev</a> ";
		for($p=1; $p<=$jumPage; $p++) {
			if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
				if (($showPage == 1) && ($p != 2))
					$Pager.="...";
				if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
					$Pager.="...";
				if ($p == $noPage)
					$Pager.="<b><u>$p</b></u> ";
				else
					$Pager.="<a href='$link&page=$p'>$p</a> ";

				$showPage = $p;
			}
		}

		if ($noPage < $jumPage)
			$Pager .= "<a href='$link&page=$next'>Next &gt;&gt;</a> ";
	}

/* ================================ */
/* MELIHAT DETAIL DARI LIST DOKUMEN */
/* ================================ */


if($_GET["act"]){
	$act=$_GET["act"];

	$ActionContent =" ";
		// Cek apakah Staff Custodian atau bukan.
		// Staff Custodian memiliki hak untuk upload softcopy & edit dokumen.
		$query = "SELECT *
		  	FROM M_DivisionDepartmentPosition ddp, M_Department d
			WHERE ddp.DDP_DeptID=d.Department_ID
			AND ddp.DDP_UserID='$mv_UserID'
			AND d.Department_Name LIKE '%Custodian%'";
		$sql = mysql_query($query);
		$custodian = mysql_num_rows($sql);

		// Cek apakah Administrator atau bukan.
		// Administrator memiliki hak untuk upload softcopy & edit dokumen.
		$query = "SELECT *
				  FROM M_UserRole
				  WHERE MUR_RoleID='1'
				  AND MUR_UserID='$mv_UserID'
				  AND MUR_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$admin = mysql_num_rows($sql);

	//Melihat Detail Dokumen Legal, License, Others
	if(($act=='detail') || ($act=='edit') ){
		$id=$_GET["id"];
		$query = "SELECT dl.DL_DocCode,
						 u.User_FullName,
						 dl.DL_RegTime,
						 c.Company_Name,
						 c.Company_Code,
						 dc.DocumentCategory_ID,
						 dc.DocumentCategory_Name,
						 dt.DocumentType_ID,
						 dt.DocumentType_Name,
						 dl.DL_NoDoc,
						 dl.DL_PubDate,
						 dl.DL_ExpDate,
						 di1.DocumentInformation1_ID,
						 di1.DocumentInformation1_Name,
						 di2.DocumentInformation2_ID,
						 di2.DocumentInformation2_Name,
						 dl.DL_Information3,
						 dl.DL_Instance,
						 dl.DL_Location,
						 dl.DL_Softcopy,
						 lds.LDS_Name,
						 dg.DocumentGroup_Name,
						 dg.DocumentGroup_Code,
						 dg.DocumentGroup_ID
		  	FROM M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt, M_LoanDetailStatus lds,
				 M_DocumentInformation1 di1, M_DocumentInformation2 di2, M_User u, M_DocumentGroup dg
			WHERE dl.DL_DocCode='$id'
			AND dl.DL_GroupDocID=dg.DocumentGroup_ID
			AND dl.DL_CompanyID=c.Company_ID
			AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
			AND dl.DL_TypeDocID=dt.DocumentType_ID
			AND dl.DL_Status=lds.LDS_ID
			AND dl.DL_RegUserID=u.User_ID
			AND dl.DL_Information1=di1.DocumentInformation1_ID
			AND dl.DL_Information2=di2.DocumentInformation2_ID";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
	}
	if($act=='detail') {
		$regdate=strtotime($arr['DL_RegTime']);
		$fregdate=date("j M Y", $regdate);

		$fpubdate=(strpos($arr['DL_PubDate'], '0000-00-00') !== false || strpos($arr['DL_PubDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DL_PubDate']));
		$fexpdate=(strpos($arr['DL_ExpDate'], '0000-00-00') !== false || strpos($arr['DL_ExpDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DL_ExpDate']));

$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Detail Dokumen $arr[DocumentGroup_Name]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'><input type='hidden' name='DL_DocCode' value='$arr[DL_DocCode]'>$arr[DL_DocCode]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DL_RegTime' value='$arr[DL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Grup Dokumen</td>
		<td width='70%'><input type='hidden' name='DocumentGroup_Code' value='$arr[DocumentGroup_Code]'>$arr[DocumentGroup_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Kategori Dokumen</td>
		<td width='70%'>$arr[DocumentCategory_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Tipe Dokumen</td>
		<td width='70%'>$arr[DocumentType_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Nomor Dokumen</td>
		<td width='70%'>$arr[DL_NoDoc]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Publikasi</td>
		<td width='70%'>$fpubdate</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Habis Masa Berlaku</td>
		<td width='70%'>$fexpdate</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 1</td>
		<td width='70%'>$arr[DocumentInformation1_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 2</td>
		<td width='70%'>$arr[DocumentInformation2_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 3</td>
		<td width='70%'>$arr[DL_Information3]</td>
	</tr>
	<tr>
		<td width='30%'>Instansi Terkait</td>
		<td width='70%'>$arr[DL_Instance]</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DL_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
	if ((($custodian==1)||($admin=="1")) && ($arr['DL_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DL_Softcopy]' class='underline'>[Download Softcopy]</a>
		</td>
	</tr>";
	}
$MainContent .="
	</table>
";
	}

	if(($act=='edit') && (($custodian==1)||($admin=="1"))){
		$regdate=strtotime($arr['DL_RegTime']);
		$fregdate=date("j M Y", $regdate);
		$pubdate=strtotime($arr['DL_PubDate']);
		$fpubdate=date("m/d/Y", $pubdate);
		if ($arr['DL_ExpDate']=="0000-00-00 00:00:00"){
			$fexpdate="";
		}
		else {
		$expdate=strtotime($arr['DL_ExpDate']);
		$fexpdate=date("m/d/Y", $expdate);
		}

$MainContent ="
	<form enctype='multipart/form-data' action='' method='POST'>
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Detail Dokumen $arr[DocumentGroup_Name]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'><input type='hidden' name='DL_DocCode' value='$arr[DL_DocCode]'>$arr[DL_DocCode]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DL_RegTime' value='$arr[DL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Grup Dokumen</td>
		<td width='70%'><input type='hidden' name='DocumentGroup_Code' value='$arr[DocumentGroup_Code]'>$arr[DocumentGroup_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Kategori Dokumen</td>
		<td width='70%'>

				<select name='txtDL_CategoryDocID' id='txtDL_CategoryDocID' onchange='showType(this.value);'>
					<option value='0'>--- Pilih Kategori Dokumen ---</option>";
			$query5="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name
					 FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
					 WHERE dgct.DGCT_DocumentGroupID='$arr[DocumentGroup_ID]'
					 AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
					 AND dgct.DGCT_Delete_Time is NULL";
			$sql5 = mysql_query($query5);

			while ($field5=mysql_fetch_array($sql5)) {
				if ($field5["DocumentCategory_ID"]=="$arr[DocumentCategory_ID]"){
$MainContent .="
				<option value='$field5[DocumentCategory_ID]' selected='selected'>$field5[DocumentCategory_Name]</option>";
				}
				else{
$MainContent .="
				<option value='$field5[DocumentCategory_ID]'>$field5[DocumentCategory_Name]</option>";
				}
			}
$MainContent .="
			</select>
		</td>
	<tr>
		<td width='30%'>Tipe Dokumen</td>
		<td>
			<select name='txtDL_TypeDocID' id='txtDL_TypeDocID'>
					<option value='0'>--- Pilih Kategori Dokumen Terlebih Dahulu ---</option>";
			$query6="SELECT DISTINCT dt.DocumentType_ID,dt.DocumentType_Name
					 FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
					 WHERE dgct.DGCT_DocumentGroupID='$arr[DocumentGroup_ID]'
					 AND dgct.DGCT_DocumentCategoryID='$arr[DocumentCategory_ID]'
					 AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
					 AND dgct.DGCT_Delete_Time is NULL";
			$sql6 = mysql_query($query6);

			while ($field6=mysql_fetch_array($sql6)) {
				if ($field6["DocumentType_ID"]==$arr['DocumentType_ID']){
$MainContent .="
				<option value='$field6[DocumentType_ID]' selected='selected'>$field6[DocumentType_Name]</option>";
				}
				else{
$MainContent .="
				<option value='$field6[DocumentType_ID]'>$field6[DocumentType_Name]</option>";
				}
			}
$MainContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td width='30%'>Instansi Terkait</td>
		<td width='70%'><input name='txtDL_Instance' id='txtDL_Instance' type='text' value='$arr[DL_Instance]'></td>
	</tr>
	<tr>
		<td width='30%'>Nomor Dokumen</td>
		<td width='70%'><input type='text' name='txtDL_NoDoc' id='txtDL_NoDoc' value='$arr[DL_NoDoc]'></td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Publikasi</td>
		<td width='70%'>
			<input type='text' name='txtDL_RegDate' id='txtDL_RegDate' size='7' value='$fpubdate' onclick=\"javascript:NewCssCal('txtDL_RegDate', 'MMddyyyy');\">
		</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Habis Masa Berlaku</td>
		<td width='70%'>
			<input type='text' name='txtDL_ExpDate' id='txtDL_ExpDate' size='7' value='$fexpdate' onclick=\"javascript:NewCssCal('txtDL_ExpDate', 'MMddyyyy');\"><img src='images/icon_close.gif' onclick=\"document.getElementById('txtDL_ExpDate').value=''\" style='margin-left:5px'>
		</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 1</td>
		<td width='70%'>
				<select name='txtDL_Information1' id='txtDL_Information1'>
					<option value='0'>--- Pilih Keterangan Dokumen 1 ---</option>";
                 $query1 = "SELECT *
				 				FROM M_DocumentInformation1
								WHERE DocumentInformation1_Delete_Time is NULL
								ORDER BY DocumentInformation1_ID";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
					 if ($data[0]==$arr[DocumentInformation1_ID]){
$MainContent .="
					<option value='$data[0]' selected='selected'>$data[1]</option>";
					 }
					 else {
$MainContent .="
					<option value='$data[0]'>$data[1]</option>";
					 }
                 }
$MainContent .="
				</select>
		</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 2</td>
		<td width='70%'>
				<select name='txtDL_Information2' id='txtDL_Information2'>
					<option value='0'>--- Pilih Keterangan Dokumen 2 ---</option>";
                 $query1 = "SELECT *
				 				FROM M_DocumentInformation2
								WHERE DocumentInformation2_Delete_Time is NULL
								ORDER BY DocumentInformation2_ID";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
					 if ($data[0]==$arr[DocumentInformation2_ID]){
$MainContent .="
					<option value='$data[0]' selected='selected'>$data[1]</option>";
					 }
					 else {
$MainContent .="
					<option value='$data[0]'>$data[1]</option>";
					 }
                 }
$MainContent .="
				</select>
		<td>
	</tr>
	<tr>
		<td width='30%'>Keterangan 3</td>
		<td width='70%'><textarea name='txtDL_Information3' id='txtDL_Information3' cols='50' rows='2'>$arr[DL_Information3]</textarea></td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DL_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
		if ((($custodian==1)||($admin=="1")) && ($arr['DL_Softcopy']==NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Upload Softcopy Dokumen</td>
		<td width='70%'>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='upload' class='button-small' />
		</td>
	</tr>";
		}

		elseif ((($custodian==1)||($admin=="1")) && ($arr['DL_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DL_Softcopy]' class='underline'>[Download Softcopy]</a><br>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='upload' class='button-small' />
		</td>
	</tr>";
		}

$MainContent .="
	<th colspan='2'>
		<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInputEdit(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}

	//Melihat Detail Dokumen Pembebasan Lahan
	if(($act=='detailLA') || ($act=='editLA') ){
		$id=$_GET["id"];
		$query = "SELECT c.Company_Name,
						 c.Company_Code,
						 dla.DLA_Phase,
						 dla.DLA_Village,
						 dla.DLA_Owner,
						 dla.DLA_Block,
						 dla.DLA_AreaClass,
						 dla.DLA_AreaStatement,
					     dla.DLA_AreaPrice,
					     dla.DLA_AreaTotalPrice,
					     dla.DLA_PlantClass,
					     dla.DLA_PlantQuantity,
					     dla.DLA_PlantPrice,
					     dla.DLA_PlantTotalPrice,
					     dla.DLA_GrandTotal,
					     dla.DLA_Location,
						 dla.DLA_Period,
						 dla.DLA_DocRevision,
						 dla.DLA_Information,
						 u.User_FullName,
						 dla.DLA_RegTime,
						 dla.DLA_Softcopy,
						 dla.DLA_DocDate,
						 dla.DLA_ID,
						 dla.DLA_Code,
						 lds.LDS_Name
		  	FROM M_DocumentLandAcquisition dla, M_Company c, M_User u, M_LoanDetailStatus lds
			WHERE dla.DLA_ID='$id'
			AND dla.DLA_Delete_Time IS NULL
			AND dla.DLA_CompanyID=c.Company_ID
			AND dla.DLA_RegUserID=u.User_ID
			AND lds.LDS_ID=dla.DLA_Status";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
		//echo $query;
	}
	if($act=='detailLA') {
		$regdate=strtotime($arr['DLA_RegTime']);
		$fregdate=date("j M Y", $regdate);
		$perdate=strtotime($arr['DLA_Period']);
		$fperdate=date("j M Y", $perdate);
		$docdate=strtotime($arr['DLA_DocDate']);
		$fdocdate=date("j M Y", $docdate);
		$TotalArea=number_format($arr[DLA_AreaTotalPrice],2,'.',',');
		$TotalPlant=number_format($arr[DLA_PlantTotalPrice],2,'.',',');
		$TotalPrice=number_format($arr[DLA_GrandTotal],2,'.',',');
		$AreaPrice=number_format($arr[DLA_AreaPrice],2,'.',',');
		$PlantPrice=number_format($arr[DLA_PlantPrice],2,'.',',');
		$DLA_PlantQuantity=number_format($arr[DLA_PlantQuantity],2,'.',',');
		$DLA_AreaStatement=number_format($arr[DLA_AreaStatement],2,'.',',');

$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Dokumen Pembebasan Lahan<br>Revisi $arr[DLA_DocRevision]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'>$arr[DLA_Code]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DL_RegTime' value='$arr[DL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Tahap</td>
		<td width='70%'>$arr[DLA_Phase]</td>
	</tr>
	<tr>
		<td width='30%'>Desa</td>
		<td width='70%'>$arr[DLA_Village]</td>
	</tr>
	<tr>
		<td width='30%'>Blok</td>
		<td width='70%'>$arr[DLA_Block]</td>
	</tr>
	<tr>
		<td width='30%'>Pemilik</td>
		<td width='70%'>$arr[DLA_Owner]</td>
	</tr>
	<tr>
		<td width='30%'>Periode</td>
		<td width='70%'>$fperdate</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Dokumen</td>
		<td width='70%'>$fdocdate</td>
	</tr>
	<tr>
		<td width='30%'>Pembebasan Lahan</td>
		<td width='70%'>Kelas $arr[DLA_AreaClass] : $DLA_AreaStatement * Rp $AreaPrice = Rp $TotalArea</td>
	</tr>
	<tr>
		<td width='30%'>Tanam Tumbuh</td>
		<td width='70%'>Kelas $arr[DLA_PlantClass] : $DLA_PlantQuantity * Rp $PlantPrice = Rp $TotalPlant</td>
	</tr>
	<tr>
		<td width='30%'>Total</td>
		<td width='70%'>Rp $TotalPrice</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan</td>
		<td width='70%'>$arr[DLA_Information]</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DLA_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
	if ((($custodian==1)||($admin=="1")) && ($arr['DLA_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DLA_Softcopy]' class='underline'>[Download Softcopy]</a>
		</td>
	</tr>";
	}
$MainContent .="
	</table>

	<table width='100%' class='stripeMe'>
	<tr>
		<th colspan='4'>Detail Dokumen</th>
	</tr>
	<tr>
		<th width='20%'>Kode Dokumen</th>
		<th width='70%'>Jenis Atribut</th>
		<th width='10%'>Keterangan</th>
	</tr>";
	$dDoc_query="SELECT laa.LAA_ID, laa.LAA_Name, laas.LAAS_Name, dlaa.DLAA_DocCode
				 FROM M_DocumentLandAcquisitionAttribute dlaa, M_LandAcquisitionAttribute laa,
				 	  M_LandAcquisitionAttributeStatus laas
				 WHERE dlaa.DLAA_DLA_ID='$id'
				 AND dlaa.DLAA_LAA_ID=laa.LAA_ID
				 AND dlaa.DLAA_LAAS_ID=laas.LAAS_ID
				 AND dlaa.DLAA_Delete_Time IS NULL
				 ORDER BY laa.LAA_ID";
	$dDoc_sql=mysql_query($dDoc_query);
	while ($dDoc_arr=mysql_fetch_array($dDoc_sql)){
$MainContent .="
	<tr>
		<td align='center'>$dDoc_arr[DLAA_DocCode]</td>
		<td>$dDoc_arr[LAA_Name]</td>
		<td align='center'>$dDoc_arr[LAAS_Name]</td>
	</tr>
";
	}

$MainContent .="
	</table>
";
	}

	if(($act=='editLA') && (($custodian==1)||($admin=="1"))){
		$regdate=strtotime($arr['DLA_RegTime']);
		$fregdate=date("j M Y", $regdate);
		$perdate=strtotime($arr['DLA_Period']);
		$fperdate=date("m/d/Y", $perdate);
		$docdate=strtotime($arr['DLA_DocDate']);
		$fdocdate=date("m/d/Y", $docdate);

$MainContent ="
	<form enctype='multipart/form-data' action='$PHP_SELF' method='POST'>
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Dokumen Pembebasan Lahan<br>Revisi <input type='hidden' name='txtDLA_DocRevision' value='$arr[DLA_DocRevision]'>$arr[DLA_DocRevision]</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'>$arr[DLA_Code]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'><input type='hidden' name='txtDLA_ID' value='$arr[DLA_ID]'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DLA_RegTime' value='$fregdate'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Tahap</td>
		<td width='70%'><input type='text' name='txtDLA_Phase' id='txtDLA_Phase' value='$arr[DLA_Phase]'></td>
	</tr>
	<tr>
		<td width='30%'>Desa</td>
		<td width='70%'><input type='text' name='txtDLA_Village' id='txtDLA_Village' value='$arr[DLA_Village]'></td>
	</tr>
	<tr>
		<td width='30%'>Blok</td>
		<td width='70%'><input type='text' name='txtDLA_Block' id='txtDLA_Block' value='$arr[DLA_Block]'></td>
	</tr>
	<tr>
		<td width='30%'>Pemilik</td>
		<td width='70%'><input type='text' name='txtDLA_Owner' id='txtDLA_Owner' value='$arr[DLA_Owner]'></td>
	</tr>
	<tr>
		<td width='30%'>Periode</td>
		<td width='70%'><input type='text' name='txtDLA_Period' id='txtDLA_Period' value='$fperdate' size='7' onclick=\"javascript:NewCssCal('txtDLA_Period', 'MMddyyyy');\"></td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Dokumen</td>
		<td width='70%'><input type='text' name='txtDLA_DocDate' id='txtDLA_DocDate' value='$fdocdate' size='7' onclick=\"javascript:NewCssCal('txtDLA_DocDate', 'MMddyyyy');\"></td>
	</tr>
	<tr>
		<td width='30%'>Kelas Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaClass' id='txtDLA_AreaClass' value='$arr[DLA_AreaClass]' size='3'></td>
	</tr>
	<tr>
		<td width='30%'>Luas Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaStatement' id='txtDLA_AreaStatement' value='$arr[DLA_AreaStatement]' size='3' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Harga Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaPrice' id='txtDLA_AreaPrice' value='$arr[DLA_AreaPrice]' size='10' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Total Harga Lahan</td>
		<td width='70%'><input type='text' name='txtDLA_AreaTotalPrice' id='txtDLA_AreaTotalPrice' value='$arr[DLA_AreaTotalPrice]' size='10' onchange='countTotal();' readonly='true' class='readonly-right'></td>
	</tr>
	<tr>
		<td width='30%'>Kelas Tanam Tumbuh</td>
		<td width='70%'><input type='text' name='txtDLA_PlantClass' id='txtDLA_PlantClass' value='$arr[DLA_PlantClass]' size='3'></td>
	</tr>
	<tr>
		<td width='30%'>Jumlah Tumbuhan</td>
		<td width='70%'><input type='text' name='txtDLA_PlantQuantity' id='txtDLA_PlantQuantity' value='$arr[DLA_PlantQuantity]' size='3' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Harga Tumbuhan</td>
		<td width='70%'><input type='text' name='txtDLA_PlantPrice' id='txtDLA_PlantPrice' value='$arr[DLA_PlantPrice]' size='10' onchange='countTotal();'></td>
	</tr>
	<tr>
		<td width='30%'>Total Tanam Tumbuh</td>
		<td width='70%'><input type='text' name='txtDLA_PlantTotalPrice' id='txtDLA_PlantTotalPrice' value='$arr[DLA_PlantTotalPrice]' size='10' onchange='countTotal();' readonly='true' class='readonly-right'></td>
	</tr>
	<tr>
		<td width='30%'>Total</td>
		<td width='70%'><input type='text' name='txtDLA_GrandTotal' id='txtDLA_GrandTotal' value='$arr[DLA_GrandTotal]' size='10' readonly='true' class='readonly-right'></td>
	</tr>
	<tr>
		<td width='30%'>Keterangan</td>
		<td width='70%'><textarea name='txtDLA_Information' id='txtDLA_Information' cols='50' rows='2'>$arr[DLA_Information]</textarea></td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'><input type='hidden' name='txtDLA_Location' value='$arr[DLA_Location]'>$arr[DLA_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
		if ((($custodian==1)||($admin=="1")) && ($arr['DLA_Softcopy']==NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Upload Softcopy Dokumen</td>
		<td width='70%'>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='uploadLA' class='button-small' />
		</td>
	</tr>";
		}

		elseif ((($custodian==1)||($admin=="1")) && ($arr['DLA_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DLA_Softcopy]' class='underline'>[Download Softcopy]</a> <br>
			<input name='userfile' type='file' size='30'/>
			<input type='submit' value='Upload' name='uploadLA' class='button-small' />
		</td>
	</tr>";
		}
$MainContent .="
	</table>
	<table width='100%' class='stripeMe'>
	<tr>
		<th colspan='5'>Detail Dokumen</th>
	</tr>
	<tr>
		<th width='20%'>Kode Dokumen</th>
		<th width='70%'>Jenis Atribut</th>
		<th width='10%'>Keterangan</th>
	</tr>";
	$dDoc_query="SELECT laa.LAA_ID, laa.LAA_Name, laas.LAAS_Name, laas.LAAS_ID, dlaa.DLAA_DocCode
				 FROM M_DocumentLandAcquisitionAttribute dlaa, M_LandAcquisitionAttribute laa,
				 	  M_LandAcquisitionAttributeStatus laas
				 WHERE dlaa.DLAA_DLA_ID='$id'
				 AND dlaa.DLAA_LAA_ID=laa.LAA_ID
				 AND dlaa.DLAA_LAAS_ID=laas.LAAS_ID
				 AND dlaa.DLAA_Delete_Time IS NULL
				 ORDER BY laa.LAA_ID";
	$dDoc_sql=mysql_query($dDoc_query);
	while ($dDoc_arr=mysql_fetch_array($dDoc_sql)){
$MainContent .="
	<tr>
		<td align='center'>$dDoc_arr[DLAA_DocCode]</td>
		<td><input type='hidden' name='txtLAA_ID[]' id='txtLAA_ID[]' value='$dDoc_arr[LAA_ID]'>$dDoc_arr[LAA_Name]</td>
		<td align='center'>
			<select name='optLAAS_ID[]' id='optLAAS_ID[]'>";
		$s_query="SELECT *
				 FROM M_LandAcquisitionAttributeStatus
				 WHERE LAAS_Delete_Time IS NULL";
		$s_sql=mysql_query($s_query);
		while ($s_arr=mysql_fetch_array($s_sql)) {
			if ($s_arr[LAAS_ID]==$dDoc_arr[LAAS_ID]) {
$MainContent .="
				<option value='$s_arr[LAAS_ID]' selected='selected'>$s_arr[LAAS_Name]</option>";
			}
			else {
$MainContent .="
				<option value='$s_arr[LAAS_ID]'>$s_arr[LAAS_Name]</option>";
			}
		}
$MainContent .="
			</select>
		</td>
	</tr>
";
	}

$MainContent .="
	<th colspan='5'>
		<input name='editLA' type='submit' value='Simpan' class='button' onclick='return validateInputEditLA(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}

	//Melihat Detail Dokumen Kepemilikan Aset
	if(($act=='detailAO') || ($act=='edit') ){
		$id=$_GET["id"];
		$query = "SELECT dao.DAO_DocCode,
						 u.User_FullName,
						 dao.DAO_RegTime,
	                     CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
	                       THEN
	                         (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
	                       ELSE
	                         (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
	                     END nama_pemilik,
						 m_mk.MK_Name merk_kendaraan,
						 dao.DAO_Type, dao.DAO_Jenis,
						 dao.DAO_NoPolisi, dao.DAO_NoRangka, dao.DAO_NoMesin, dao.DAO_NoBPKB,
	                     CASE WHEN dao.DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
	                         WHEN dao.DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
	                         ELSE DATE_FORMAT(dao.DAO_STNK_StartDate, '%d/%m/%Y')
	                     END AS start_stnk,
	                     CASE WHEN dao.DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
	                         WHEN dao.DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
	                         ELSE DATE_FORMAT(dao.DAO_STNK_ExpiredDate, '%d/%m/%Y')
	                     END AS expired_stnk,
	                     CASE WHEN dao.DAO_Pajak_StartDate LIKE '%0000-00-00%' THEN '-'
	                         WHEN dao.DAO_Pajak_StartDate LIKE '%1970-01-01%' THEN '-'
	                         ELSE DATE_FORMAT(dao.DAO_Pajak_StartDate, '%d/%m/%Y')
	                     END AS start_pajak,
	                     CASE WHEN dao.DAO_Pajak_ExpiredDate LIKE '%0000-00-00%' THEN '-'
	                         WHEN dao.DAO_Pajak_ExpiredDate LIKE '%1970-01-01%' THEN '-'
	                         ELSE DATE_FORMAT(dao.DAO_Pajak_ExpiredDate, '%d/%m/%Y')
	                     END AS expired_pajak,
						 dao.DAO_Lokasi_PT, dao.DAO_Region, dao.DAO_Keterangan,
						 dao.DAO_Location, dao.DAO_Softcopy,
						 lds.LDS_Name
		  	FROM M_DocumentAssetOwnership dao, M_Company c, M_LoanDetailStatus lds,
				 M_User u, db_master.M_MerkKendaraan m_mk
			WHERE dao.DAO_DocCode='$id'
			AND dao.DAO_Status=lds.LDS_ID
			AND dao.DAO_RegUserID=u.User_ID
			AND m_mk.MK_ID=dao.DAO_MK_ID";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
	}
	if($act=='detailAO') {
		$fregdate = date("j M Y", strtotime($arr['DAO_RegTime']));

$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Detail Dokumen Kepemilikan Aset</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'><input type='hidden' name='DAO_DocCode' value='$arr[DAO_DocCode]'>$arr[DAO_DocCode]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DAO_RegTime' value='$arr[DAO_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pemilik</td>
		<td width='70%'>$arr[nama_pemilik]</td>
	</tr>
	<tr>
		<td width='30%'>Merk Kendaraan</td>
		<td width='70%'>$arr[merk_kendaraan]</td>
	</tr>
	<tr>
		<td width='30%'>Tipe Kendaraan</td>
		<td width='70%'>$arr[DAO_Type]</td>
	</tr>
	<tr>
		<td width='30%'>Jenis Kendaraan</td>
		<td width='70%'>$arr[DAO_Jenis]</td>
	</tr>
	<tr>
		<td width='30%'>No. Polisi</td>
		<td width='70%'>$arr[DAO_NoPolisi]</td>
	</tr>
	<tr>
		<td width='30%'>No. Rangka</td>
		<td width='70%'>$arr[DAO_NoRangka]</td>
	</tr>
	<tr>
		<td width='30%'>No. Mesin</td>
		<td width='70%'>$arr[DAO_NoMesin]</td>
	</tr>
	<tr>
		<td width='30%'>No. BPKB</td>
		<td width='70%'>$arr[DAO_NoBPKB]</td>
	</tr>
	<tr>
		<td width='30%'>Masa Berlaku STNK</td>
		<td width='70%'>$arr[start_stnk] s/d $arr[expired_stnk]</td>
	</tr>
	<tr>
		<td width='30%'>Masa Berlaku Pajak</td>
		<td width='70%'>$arr[start_pajak] s/d $arr[expired_pajak]</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Perusahan</td>
		<td width='70%'>$arr[DAO_Lokasi_PT]</td>
	</tr>
	<tr>
		<td width='30%'>Region Perusahan</td>
		<td width='70%'>$arr[DAO_Region]</td>
	</tr>
	<tr>
		<td width='30%'>Keterangan Dokumen</td>
		<td width='70%'>$arr[DAO_Keterangan]</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DAO_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
	if ((($custodian==1)||($admin=="1")) && ($arr['DAO_Softcopy']<> NULL) ) {
$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DAO_Softcopy]' class='underline'>[Download Softcopy]</a>
		</td>
	</tr>";
	}
$MainContent .="
	</table>
";
	}

	//Melihat Detail Dokumen Lainnya Legal
	if(($act=='detailOL') || ($act=='edit') ){
		$id=$_GET["id"];
		$query = "SELECT dol.DOL_DocCode,
						 u.User_FullName,
						 dol.DOL_RegTime,
						 c.Company_Name, c.Company_Code,
						 m_dc.DocumentCategory_Name kategori_dokumen, dol.DOL_NamaDokumen,
						 dol.DOL_InstansiTerkait, dol.DOL_NoDokumen, dol.DOL_TglTerbit, dol.DOL_TglBerakhir,
						 dol.DOL_Location, dol.DOL_Softcopy,
						 lds.LDS_Name
			FROM M_DocumentsOtherLegal dol, M_Company c, M_LoanDetailStatus lds,
				 M_User u, db_master.M_DocumentCategory m_dc
			WHERE dol.DOL_DocCode='$id'
			AND dol.DOL_CompanyID=c.Company_ID
			AND dol.DOL_Status=lds.LDS_ID
			AND dol.DOL_RegUserID=u.User_ID
			AND m_dc.DocumentCategory_ID=dol.DOL_CategoryDocID";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
	}
	if($act=='detailOL') {
		$fregdate = date("j M Y", strtotime($arr['DOL_RegTime']));

		$tgl_terbit=(strpos($arr['DOL_TglTerbit'], '0000-00-00') !== false || strpos($arr['DOL_TglTerbit'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DOL_TglTerbit']));
		$tgl_berakhir=(strpos($arr['DOL_TglBerakhir'], '0000-00-00') !== false || strpos($arr['DOL_TglBerakhir'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DOL_TglBerakhir']));

	$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Detail Dokumen Lainnya (Legal)</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'><input type='hidden' name='DOL_DocCode' value='$arr[DOL_DocCode]'>$arr[DOL_DocCode]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DOL_RegTime' value='$arr[DOL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Kategori Dokumen</td>
		<td width='70%'>$arr[kategori_dokumen]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Dokumen</td>
		<td width='70%'>$arr[DOL_NamaDokumen]</td>
	</tr>
	<tr>
		<td width='30%'>Instansi Terkait</td>
		<td width='70%'>$arr[DOL_InstansiTerkait]</td>
	</tr>
	<tr>
		<td width='30%'>No. Dokumen</td>
		<td width='70%'>$arr[DOL_NoDokumen]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Terbit Dokumen</td>
		<td width='70%'>$tgl_terbit</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Berakhir Dokumen</td>
		<td width='70%'>$tgl_berakhir</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DOL_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
	if ((($custodian==1)||($admin=="1")) && ($arr['DOL_Softcopy']<> NULL) ) {
	$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DOL_Softcopy]' class='underline'>[Download Softcopy]</a>
		</td>
	</tr>";
	}
	$MainContent .="
	</table>
	";
	}

	//Melihat Detail Dokumen Lainnya Di Luar Legal
	if(($act=='detailONL') || ($act=='edit') ){
		$id=$_GET["id"];
		$query = "SELECT donl.DONL_DocCode,
						 u.User_FullName,
						 donl.DONL_RegTime,
						 c.Company_Name, c.Company_Code,
						 donl.DONL_NoDokumen,
						 donl.DONL_NamaDokumen, donl.DONL_TahunDokumen, m_d.Department_Name nama_departemen,
						 donl.DONL_Location, donl.DONL_Softcopy,
						 lds.LDS_Name
				FROM M_DocumentsOtherNonLegal donl
	 			LEFT JOIN M_Company c
	 				ON donl.DONL_CompanyID=c.Company_ID
	 			LEFT JOIN M_LoanDetailStatus lds
	 				ON donl.DONL_Status=lds.LDS_ID
	 			LEFT JOIN M_User u
	 				ON donl.DONL_RegUserID=u.User_ID
	 			LEFT JOIN db_master.M_Department m_d
	 				ON m_d.Department_Code=donl.DONL_Dept_Code
	 			WHERE donl.DONL_DocCode='$id'";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
	}
	if($act=='detailONL') {
		$fregdate = date("j M Y", strtotime($arr['DONL_RegTime']));

	$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th colspan='2'>Detail Dokumen Lainnya (Di Luar Legal)</th>
	</tr>
	<tr>
		<td width='30%'>Kode Dokumen</td>
		<td width='70%'><input type='hidden' name='DONL_DocCode' value='$arr[DONL_DocCode]'>$arr[DONL_DocCode]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Pendaftar</td>
		<td width='70%'>$arr[User_FullName]</td>
	</tr>
	<tr>
		<td width='30%'>Tanggal Pendaftaran</td>
		<td width='70%'><input type='hidden' name='DONL_RegTime' value='$arr[DONL_RegTime]'>$fregdate</td>
	</tr>
	<tr>
		<td width='30%'>Perusahaan</td>
		<td width='70%'><input type='hidden' name='Company_Name' value='$arr[Company_Code]'>$arr[Company_Name]</td>
	</tr>
	<tr>
		<td width='30%'>Nomor Dokumen</td>
		<td width='70%'>$arr[DONL_NoDokumen]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Dokumen</td>
		<td width='70%'>$arr[DONL_NamaDokumen]</td>
	</tr>
	<tr>
		<td width='30%'>Tahun Dokumen</td>
		<td width='70%'>$arr[DONL_TahunDokumen]</td>
	</tr>
	<tr>
		<td width='30%'>Nama Departemen Pada Dokumen</td>
		<td width='70%'>$arr[nama_departemen]</td>
	</tr>
	<tr>
		<td width='30%'>Lokasi Dokumen</td>
		<td width='70%'>$arr[DONL_Location]</td>
	</tr>
	<tr>
		<td width='30%'>Status</td>
		<td width='70%'>$arr[LDS_Name]</td>
	</tr>";
	if ((($custodian==1)||($admin=="1")) && ($arr['DONL_Softcopy']<> NULL) ) {
	$MainContent .="
	<tr>
		<td width='30%'>Softcopy Dokumen</td>
		<td width='70%'>
			<a href='$arr[DONL_Softcopy]' class='underline'>[Download Softcopy]</a>
		</td>
	</tr>";
	}
	$MainContent .="
	</table>
	";
	}


/* ====== */
/* ACTION */
/* ====== */
//print_r($_GET);die();
if(isset($_POST['cancel'])) {
	echo "<meta http-equiv='refresh' content='0; url=document-list2.php'>";

}
else if($_POST['edit']) {
	$txtRegDate=date('Y-m-d H:i:s', strtotime($_POST['txtDL_RegDate']));
	$txtExpDate=date('Y-m-d H:i:s', strtotime($_POST['txtDL_ExpDate']));
	if 	($txtExpDate=="1970-01-01 08:00:00"){
		$txtExpDate="";
	}

	$query = "UPDATE M_DocumentLegal
			  SET DL_CategoryDocID='$_POST[txtDL_CategoryDocID]',
			  	  DL_TypeDocID='$_POST[txtDL_TypeDocID]',
			  	  DL_NoDoc='$_POST[txtDL_NoDoc]',
			  	  DL_PubDate='$txtRegDate',
				  DL_ExpDate='$txtExpDate',
				  DL_Information1='$_POST[txtDL_Information1]',
				  DL_Information2='$_POST[txtDL_Information2]',
				  DL_Information3='$_POST[txtDL_Information3]',
				  DL_Instance='$_POST[txtDL_Instance]',
			  	  DL_Update_Time=sysdate(),
			      DL_Update_UserID='$mv_UserID'
			  WHERE DL_DocCode='$_POST[DL_DocCode]'";
	if ($mysqli->query($query))
		echo "<meta http-equiv='refresh' content='0; url=document-list2.php'>";
}
else if($_POST['editLA']) {
	$txtDLA_Period=date('Y-m-d H:i:s', strtotime($_POST['txtDLA_Period']));
	$txtDLA_DocDate=date('Y-m-d H:i:s', strtotime($_POST['txtDLA_DocDate']));
	$DLA_DocRevision=$_POST['txtDLA_DocRevision'];
	$DLA_DocRevision=$DLA_DocRevision+1;
	$txtLAA_ID=$_POST['txtLAA_ID'];
	$optLAAS_ID=$_POST['optLAAS_ID'];
	$jRow=count($txtLAA_ID);

	$query = "UPDATE M_DocumentLandAcquisition
			  SET DLA_Phase='$_POST[txtDLA_Phase]',
			  	  DLA_Village='$_POST[txtDLA_Village]',
			  	  DLA_Block='$_POST[txtDLA_Block]',
			  	  DLA_Period='$txtDLA_Period',
				  DLA_DocDate='$txtDLA_DocDate',
				  DLA_Owner='$_POST[txtDLA_Owner]',
				  DLA_AreaClass='$_POST[txtDLA_AreaClass]',
				  DLA_AreaStatement='$_POST[txtDLA_AreaStatement]',
				  DLA_AreaPrice='$_POST[txtDLA_AreaPrice]',
				  DLA_AreaTotalPrice='$_POST[txtDLA_AreaTotalPrice]',
				  DLA_PlantClass='$_POST[txtDLA_PlantClass]',
				  DLA_PlantQuantity='$_POST[txtDLA_PlantQuantity]',
				  DLA_PlantPrice='$_POST[txtDLA_PlantPrice]',
				  DLA_PlantTotalPrice='$_POST[txtDLA_PlantTotalPrice]',
				  DLA_GrandTotal='$_POST[txtDLA_GrandTotal]',
				  DLA_Information='$_POST[txtDLA_Information]',
			  	  DLA_Update_Time=sysdate(),
			      DLA_Update_UserID='$mv_UserID'
			  WHERE DLA_ID='$_POST[txtDLA_ID]'";
	if ($mysqli->query($query)) {
		for ($i=0; $i<$jRow; $i++) {
			$d_query="UPDATE M_DocumentLandAcquisitionAttribute
					  SET DLAA_LAAS_ID='$optLAAS_ID[$i]',
						  DLAA_Update_Time=sysdate(),
						  DLAA_Update_UserID='$mv_UserID'
					  WHERE DLAA_DLA_ID='$_POST[txtDLA_ID]'
					  AND DLAA_LAA_ID='$txtLAA_ID[$i]'
					  AND DLAA_Delete_Time IS NULL";
			if ($mysqli->query($d_query)) {
				echo "<meta http-equiv='refresh' content='0; url=document-list2.php'>";
			}
		}
	}
}
else if($_POST['upload']){
	$DL_DocCode=$_POST[DL_DocCode];
	$Company_Name=$_POST[Company_Name];
	$DocumentGroup_Code=$_POST[DocumentGroup_Code];
	$regdate=strtotime($_POST['DL_RegTime']);
	$DL_RegTime=date("Y", $regdate);

	$uploaddir = "SOFTCOPY/$Company_Name/$DocumentGroup_Code/$DL_RegTime/";
	if ( ! is_dir($uploaddir)) {
		$oldumask = umask(0);
		mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
		chmod("/$Company_Name", 0777);
		chmod("/$DocumentGroup_Code", 0777);
		chmod("/$DL_RegTime", 0777);
		umask($oldumask);
	}
	$uploadFile = $_FILES['userfile'];
	$extractFile = pathinfo($uploadFile['name']);

	$newName = $DL_DocCode.'.'.$extractFile['extension'];
	$sameName = 0;
	if ($handle = opendir("$uploaddir")) {
		while (false !== ($file = readdir($handle))) {
			if ($file==$newName) {
				if(strpos($newName,$DL_DocCode) !== false)  {
					$sameName++; // Tambah data file yang sama
					$newName = $DL_DocCode.'('.$sameName.')'.'.'.$extractFile['extension'];
				}
			}
		}
		closedir($handle);
	}

	if ($DocumentGroup_Code<>'GRL') {
		$query="UPDATE M_DocumentLegal
				SET DL_Softcopy='$uploaddir$newName',
					DL_Update_UserID='$mv_UserID',
					DL_Update_Time=sysdate()
				WHERE DL_DocCode='$DL_DocCode'";
	}

	if ($mysqli->query($query)){
		if(move_uploaded_file($uploadFile['tmp_name'],$uploaddir.$newName)){
			echo "<meta http-equiv='refresh' content='0; url=document-list2.php?act=edit&id=$DL_DocCode'>";
		}
	}
}
else if($_POST['uploadLA']){
	$DLA_ID=$_POST[txtDLA_ID];
	$DLA_Location=$_POST[txtDLA_Location];
	$Company_Name=$_POST[Company_Name];
	$DocumentGroup_Code='GRL';
	$regdate=strtotime($_POST['DLA_RegTime']);
	$RegTime=date("Y", $regdate);

	$uploaddir = "SOFTCOPY/$Company_Name/$DocumentGroup_Code/$RegTime/";
	if ( ! is_dir($uploaddir)) {
		$oldumask = umask(0);
		mkdir("$uploaddir", 0777, true); // or even 01777 so you get the sticky bit set
		chmod("/$Company_Name", 0777);
		chmod("/$DocumentGroup_Code", 0777);
		chmod("/$RegTime", 0777);
		umask($oldumask);
	}
	$uploadFile = $_FILES['userfile'];
	$extractFile = pathinfo($uploadFile['name']);

	$newName = $DLA_Location.'.'.$extractFile['extension'];
	$sameName = 0;
	if ($handle = opendir("$uploaddir")) {
		while (false !== ($file = readdir($handle))) {
			if ($file==$newName) {
				if(strpos($newName,$DLA_Location) !== false)  {
					$sameName++; // Tambah data file yang sama
					$newName = $DLA_Location.'('.$sameName.')'.'.'.$extractFile['extension'];
				}
			}
		}
		closedir($handle);
	}

	if ($DocumentGroup_Code=='GRL') {
		$query="UPDATE M_DocumentLandAcquisition
				SET DLA_Softcopy='$uploaddir$newName',
					DLA_Update_UserID='$mv_UserID',
					DLA_Update_Time=sysdate()
				WHERE DLA_ID='$DLA_ID'";
	}

	if ($mysqli->query($query)){
		if(move_uploaded_file($uploadFile['tmp_name'],$uploaddir.$newName)){
			echo "<meta http-equiv='refresh' content='0; url=document-list2.php?act=editLA&id=$DLA_ID'>";
		}
	}
}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>
