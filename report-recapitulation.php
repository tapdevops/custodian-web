<?php
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Doni Romdoni																						=
= Dibuat Tanggal	: 06 Juni 2012																						=
= Update Terakhir	: 06 Juni 2012																						=
= Revisi			:																									=
= Purpose			: Pembuatan report Rekapitulasi kekurangan Dokumen Pembebasan Lahan yang sudah diterima				=																					=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
//cek user login session
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
}
?>
<script language="JavaScript" type="text/JavaScript">
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
}

function showCompany(){
	$.post("jQuery.CompanyNameReport.php", {
		CompanyAreaID: document.getElementById('optArea').value
	}, function(response){
		setTimeout("finishAjax('optCompany', '"+escape(response)+"')", 400);
	});
}
</script>
</head>
<?php

if (empty($_POST['btnSubmit'])) {
//load View Template
require_once "./include/template.inc";
$page=new Template();
}

//load report class
require_once "./include/class.report-recapitulation.php";
$reportClass = new reportRekapitulasi();

//connection to database
include ("./config/config_db.php");


//get PT
$ptOption = $reportClass->getPTOption( $_POST['optPT']);

//get Area
$areaOption = $reportClass->getAreaOption($_POST['optArea']);

//GET year option
$yearOption = $reportClass->getYearOption( $_POST['optTahun']);

//check if submit data
if ($_POST['btnSubmit']) {
	echo "<link href='./css/style-print-a3.css' rel='stylesheet' type='text/css'>";
	echo "<SCRIPT>
				function printPage(){
				document.getElementById('PrintButton').style.display = 'none'
				window.print()
				document.getElementById('PrintButton').style.display = 'block'
				}
		  </SCRIPT>";
	if ($_POST['optTipe']=="kekurangan") {
		$data= $reportClass->getDataReport($_POST['optPT'],$_POST['optArea'], $_POST['optTahun']);
		echo "<title>Laporan Rekapitulasi Kekurangan Pembebasan Lahan</title>";
	}
	else if ($_POST['optTipe']=="ketersediaan"){
		$data= $reportClass->getDataReportRekapitulasi($_POST['optPT'],$_POST['optArea'], $_POST['optTahun']);
		echo "<title>Laporan Rekapitulasi Pembebasan Lahan</title>";
	}
	$table = $reportClass->drawTableHeader($data,$_POST['optTipe']);
	print_r ($table);
}
?>
<title>Custodian System | Laporan Rekapitulasi Pembebasan Lahan</title>
</form>
<?php
$ActionContent = "
	<form name='list' method='post' target='_blank' >
	<table width='100%'>
	<tr>
		<td align='left'>Area</td>
		<td>:</td>
		<td align='left'>
			 <select name='optArea' id='optArea' onchange='return showCompany()'>$areaOption</select>
		</td>
		<td width='25%' align='right'>
			<input name='btnSubmit' type='submit' value='Cari' class='button-small'>
		</td>
	</tr>
	<tr>
		<td align='left'>PT</td>
		<td>:</td>
		<td>
			 <select name='optPT' id='optCompany'>$ptOption</select>
		</td>
		<td>
			<input name='export_to_excel' formaction='result-report-recapitulation-export-to-excel.php' type='submit' value='&nbsp;Export to Excel&nbsp;' class='button-small-blue' onclick='return validateInput(this);' />
		</td>
	</tr>
	<tr>
		<td align='left'>Tahun</td>
		<td>:</td>
		<td colspan='2' align='left'>
			 <select name='optTahun'>$yearOption</select>
		</td>
	</tr>
	<tr>
		<td align='left'>Tipe</td>
		<td>:</td>
		<td colspan='2' align='left'>
			 <select name='optTipe'>
				<option value='kekurangan'>Kekurangan</option>
				<option value='ketersediaan'>Ketersediaan</option>
			 </select>
		</td>
	</tr>
	</table>
	</form>
";
?>


<?php
if (!$_POST['btnSubmit']) {
	$page->ActContent($ActionContent);
	$page->Content($MainContent);
	$page->Pagers($Pager);
	$page->ShowWTopMenu();
}
?>
