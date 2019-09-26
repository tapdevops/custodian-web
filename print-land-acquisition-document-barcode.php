<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cetak Barcode</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico">
<link href="./css/style-print.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="barcode/jzebra.js"></script>
<script type="text/javascript">
    var sleepCounter = 0;

    function print(randomFile) {
		//alert("Masuk antrian printer");
        var applet = document.jZebra;
        if (applet != null) {
			//find printer
            applet.findPrinter("zebra");
			monitorFinding();

			applet.appendFile(getPath() + "barcode/format_barcode"+randomFile+".txt");
	        if (confirm("Anda yakin ingin mencetak barcode ini?")){
				applet.print();
			}
			monitorPrinting();
			alert("Masuk antrian printer");
			deleteFile(randomFile);
		}

    }
	function deleteFile(randomFile)
	{
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
		}
	  }
	xmlhttp.open("GET","/deleteFile.php?randomFile="+randomFile,true);
	xmlhttp.send();
	}
    function getPath() {
          var path = window.location.href;
          return path.substring(0, path.lastIndexOf("/")) + "/";
      }

    function monitorPrinting() {
		var applet = document.jZebra;
		if (applet != null) {
		   if (!applet.isDonePrinting()) {
			  window.setTimeout('monitorPrinting()', 100);
		   } else {
			  var e = applet.getException();
			  alert(e == null ? "Printed Successfully" : "Exception occured: " + e.getLocalizedMessage());
		   }
		} else {
				alert("Applet not loaded!");
			}
    }

    function monitorFinding() {
		var applet = document.jZebra;
		var printer ;
		if (applet != null) {
		   if (!applet.isDoneFinding()) {
			  window.setTimeout('monitorFinding()', 100);
		   } else {
			  printer = applet.getPrinterName();
		     //alert(printer == null ? "Printer tidak tersedia" : "Printer \"" + printer + "\" tersedia");
			 if (printer == null ) alert( "Printer tidak tersedia" );
		   }
		} else {
				alert("Applet not loaded!");
		}
		return printer;
    }

	function printStruk(str){
        var applet = document.jZebra;
        if (applet != null){
            // Plain Text
            str=returnEnter(str);
            applet.append(str);
            // Send to the printer
            applet.print();
            while (!applet.isDonePrinting())
            {
            // Wait
            }
            var e = applet.getException();
            if (e == null) var info="Printed Successfully";
            else var info="Error: " + e.getLocalizedMessage();
        }else{
            var info="Printer belum siap";
        }
        document.getElementById("printerStatusBar").innerHTML=info;
    }

    function returnEnter(dataStr){
        return dataStr.replace(/(\r\n|\r|\n)/g, "\n");
    }

    function detectPrinter(){
        var backgroundInfo = "#d35400";
        var applet = document.jZebra;
        if (applet != null){
            applet.findPrinter("zebra");
            while (!applet.isDoneFinding()){
            // Wait
            }
            var ps = applet.getPrintService();
            if (ps == null) var info="Printer belum siap";
            else var info="Printer \"" + ps.getName() + "\" siap";
            var backgroundInfo = "#27ae60";
        }else{
            var info="Java Runtime belum siap!";
            var backgroundInfo = "#c0392b";
        }
        document.getElementById("printerStatusBar").innerHTML=info;
        document.getElementById("printerStatusBar").style.backgroundColor = backgroundInfo;
        window.setTimeout('detectPrinter()',5000);
    }

</SCRIPT>

<style>
    #printerStatusBar{
        background:#d35400;
        color:#fff;
        font-weight: bold;
        padding:5px 10px;
    }
</style>
</head>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
?>
<!-- <body style="width:620px; border:none;" onLoad="alert('Dokumen Sudah Masuk Antrian Printer');" > -->
<body style="width:620px; border:none;" onLoad="detectPrinter()">

<div id="printerStatusBar">
    Loading the Printer Status.......
</div>
<br>

<applet name="jZebra" code="jzebra.RawPrintApplet.class" archive="barcode/jzebra.jar" width="100" height="100">
  <param name="printer" value="zebra">
  <param name="sleep" value="200">
</applet>

<div id='content'>
<?php
	$randomFile = rand();
	//echo "<input type='button' name='PrintButton' id='PrintButton' onclick=\"print('".$randomFile."')\" value='CETAK' class='print-button' />" ;
?>
<div style="height:30px;">&nbsp;</div>
<?PHP
$DocID=$_GET["cBarcodePrint"];
$jumlah=count($DocID);

echo "<table cellpadding='0' cellspacing='0'  width='600' align='center'>";
echo "<tr>";

//buat file text untuk simpan format barcode
$myFile = "barcode/format_barcode".$randomFile.".txt";
$fh = fopen($myFile, 'w') or die("Tidak Bisa Membuka File Format Barcode");

for($i=1;$i<=$jumlah;$i++){
	$query="SELECT dlaa.DLAA_DocCode,
				   c.Company_Name,
				   dla.DLA_Phase,
				   dla.DLA_Village,
				   lla.LAA_Acronym,
				   dla.DLA_Owner,
				   dla.DLA_AreaStatement,
				   dla.DLA_PlantTotalPrice,
				   dla.DLA_GrandTotal,
				   dla.DLA_Location
		  	FROM M_DocumentLandAcquisition dla, M_DocumentLandAcquisitionAttribute dlaa, M_Company c,
				 M_LandAcquisitionAttribute lla
			WHERE dla.DLA_ID='".$DocID[$i-1]."'
			AND dlaa.DLAA_DLA_ID=dla.DLA_ID
			AND dla.DLA_Delete_Time IS NULL
			AND dlaa.DLAA_Delete_Time IS NULL
			AND dla.DLA_CompanyID=c.Company_ID
			AND lla.LAA_ID=dlaa.DLAA_LAA_ID";

	$sql = mysql_query($query);
	$jRow = mysql_num_rows($sql);
	$j=1;
	while($arr = mysql_fetch_array($sql)) {
	$TotalPlant=number_format($arr[DLA_PlantTotalPrice],2,'.',',');
	$TotalPrice=number_format($arr[DLA_GrandTotal],2,'.',',');

	if($j<=$jRow) {

	if ($j%2==0){
		echo"<td></td>";
		$stringData .= "^BY2,2.5,60^FT450,173^BAN,N,,N,N\n";
		$stringData .= "^FD".$arr[DLAA_DocCode]."^FS\n";
		$stringData .= "^FT450,23^A0N,17,16^FH\^FD".substr("$arr[Company_Name]",0,25)."^FS\n";
		$stringData .= "^FT820,23,1^A0N,17,16^FH\^FD".$arr[DLA_Location]."^FS\n";
		$stringData .= "^FT450,43^A0N,17,16^FH\^FD".$arr[LAA_Acronym]."^FS\n";
		$stringData .= "^FT450,63^A0N,17,16^FH\^FD Tahap ".$arr[DLA_Phase]." - ".$arr[DLA_Village]."^FS\n";
		$stringData .= "^FT450,83^A0N,17,16^FH\^FD".$arr[DLA_Owner]."^FS\n";
		$stringData .= "^FT450,103^A0N,17,16^FH\^FD".$arr[DLA_AreaStatement]."Ha - Rp ".$TotalPlant." - Rp ".$TotalPrice."^FS\n";
		$stringData .= "^PQ1,0,1,Y^XZ\n";
	}else{
		//header awal satu row format barcode
		$stringData .= "~CT~~CD,~CC^~CT~\n";
		$stringData .= "^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR2,2~SD30^JUS^LRN^CI0^XZ\n";
		$stringData .= "^XA\n";
		$stringData .= "^MMT\n";
		$stringData .= "^PW823\n";
		$stringData .= "^LL0200\n";
		$stringData .= "^LS0\n";
		$stringData .= "^BY2,2.5,60^FT25,173^BAN,N,,N,N\n";
		$stringData .= "^FD".$arr[DLAA_DocCode]."^FS\n";
		$stringData .= "^FT25,23^A0N,17,16^FH\^FD".substr("$arr[Company_Name]",0,25)."^FS\n";
		$stringData .= "^FT400,23,1^A0N,17,16^FH\^FD".$arr[DLA_Location]."^FS\n";
		$stringData .= "^FT25,43^A0N,17,16^FH\^FD".$arr[LAA_Acronym]."^FS\n";
		$stringData .= "^FT25,63^A0N,17,16^FH\^FD Tahap".$arr[DLA_Phase]." - ".$arr[DLA_Village]."^FS\n";
		$stringData .= "^FT25,83^A0N,17,16^FH\^FD".$arr[DLA_Owner]."^FS\n";
		$stringData .= "^FT25,103^A0N,17,16^FH\^FD".$arr[DLA_AreaStatement]."Ha - Rp ".$TotalPlant." - Rp ".$TotalPrice."^FS\n";
	}
	echo"
	<td align='center' width='300'>
	<table cellpadding='0' cellspacing='0'  width=100%>
	<tr>
		<td >".substr("$arr[Company_Name]",0,25)."</td>
		<td align='right'>$arr[DLA_Location]</td>
	</tr>
	<tr>
		<td colspan='2'>$arr[LAA_Acronym]</td>
	</tr>
	<tr>
		<td colspan='2'>Tahap $arr[DLA_Phase] - $arr[DLA_Village]</td>
	</tr>
	<tr>
		<td colspan='2'>$arr[DLA_Owner]</td>
	</tr>
	<tr>
		<td colspan='2'>$arr[DLA_AreaStatement]Ha - Rp $TotalPlant - Rp $TotalPrice</td>
	</tr>
	<tr>
		<td colspan='2' align='center'>
			<img src='barcode.php?text=$arr[DLAA_DocCode]'><br>
			$arr[DLAA_DocCode]
		</td>
	</tr>
	</table>
	</td>";
	if ($j%2==0){
		echo "</tr><tr>";
	}
}
if ($j%2==0){
		$stringData .= "^PQ1,0,1,Y^XZ\n";
	}
	$j=$j+1;
}}
fwrite($fh, $stringData);
fclose($fh);
echo "</table>";
?>
</div>
</body>
</html>
<?PHP } ?>
