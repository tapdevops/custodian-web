<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 07 Juni 2012																						=
= Update Terakhir	: 07 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Working Paper</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico">
<link href="./css/style-print-a3.css" rel="stylesheet" type="text/css">
<script src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
   	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
   	$(".stripeMe tr:even").addClass("alt");
 	});
</script>
<SCRIPT>
function printPage(){
document.getElementById('PrintButton').style.display = "none"
window.print()
document.getElementById('PrintButton').style.display = "block"
}
</SCRIPT>

</head>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
?>

<body>
<div id='header'>
<input type='button' name="PrintButton" id="PrintButton" onclick='printPage()' value='CETAK' class='print-button' />
	<div id='header-inside'>
    	<div class="tap">PT Triputra Agro Persada </div>
        <div class="custodian">Custodian Department </div>
        <div class="alamat">Jalan DR.Ide Anak Agung Gde Agung Kav. E.3.2. No 1<br />
        Jakarta - 12950</div>
    </div>
</div>
<div id='content'>
<?PHP
$qcompany=($_POST['optCompany'] == "ALL")?"":"AND Company_ID='$_POST[optCompany]'";
$qarea=(!$_POST['optArea'])?"":"AND Company_ID_Area='$_POST[optArea]'";
// $CompanyID=$_POST['optCompany'];
$qgrup=(!$_POST['optDocumentGroup'])?"":"AND DocumentGroup_ID='$_POST[optDocumentGroup]'";
$qcategory=(!$_POST['optDocumentCategory'])?"":"AND dc.DocumentCategory_ID='$_POST[optDocumentCategory]'";

$dokumen = "";
$kategori = "";

echo "
<div id='title'>Laporan Working Paper</div>";

$c_query="SELECT *
		  FROM M_Company
		  WHERE
            Company_Delete_Time is NULL
            $qarea
            $qcompany";
$c_sql=mysql_query($c_query);

while($c_arr=mysql_fetch_array($c_sql)){

    echo "
    <br>
    <div class='h2'>$c_arr[Company_Name]</div>
    <div class='h5'>Tanggal Cetak : ".date('j M Y')."</div>";

    $g_query="SELECT *
    		  FROM M_DocumentGroup
    		  WHERE DocumentGroup_Delete_Time IS NULL
    		  AND (DocumentGroup_ID = '1' OR DocumentGroup_ID = '2')
    		  $qgrup
    		  ORDER BY DocumentGroup_ID";
    $g_sql = mysql_query($g_query);

    echo "
    	<table width='100%' cellpadding='0' cellspacing='0' border='1' style='border:3px solid #000;'>
    	<tr>
    		<th width=10%>Kategori</th>
    		<th width=3%>No</th>
    		<th width=16%>Tipe Dokumen</th>
    		<th width=16%>Keterangan 3</th>
    		<th width=10%>Instansi Terkait</th>
    		<th width=10%>No Dokumen</th>
    		<th width=10%>Tanggal Terbit</th>
    		<th width=10%>Tanggal Habis Berlaku</th>
    		<th width=10%>Keterangan 1</th>
    	</tr>";
    while ($g_arr=mysql_fetch_array($g_sql)){
    	echo "
    	<tr>
    		<td colspan=50><div class='title'>Grup : $g_arr[DocumentGroup_Name]</div></td>
    	</tr>";
    	$ca_query="SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
    			   FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
    			   WHERE dgct.DGCT_Delete_Time IS NULL
    			   AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
    			   AND dgct.DGCT_DocumentGroupID='$g_arr[DocumentGroup_ID]'
    			   $qcategory
    			   ORDER BY dc.DocumentCategory_ID";
    	$ca_sql = mysql_query($ca_query);
    	while ($ca_arr=mysql_fetch_array($ca_sql)){
    		$ty_query="SELECT DISTINCT dt.DocumentType_ID, dt.DocumentType_Name
    				   FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
    				   WHERE dgct.DGCT_Delete_Time IS NULL
    				   AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
    				   AND dgct.DGCT_DocumentCategoryID='$ca_arr[DocumentCategory_ID]'
    				   AND dgct.DGCT_DocumentGroupID='$g_arr[DocumentGroup_ID]'
    				   ORDER BY dt.DocumentType_ID";
    		$ty_sql = mysql_query($ty_query);
    		$type_sql = mysql_query($ty_query);
    		$rowspanNum = mysql_num_rows($type_sql);

    		$no=1;
    		while ($ty_arr=mysql_fetch_array($ty_sql)){
    			$dokumen.="
    			<td align='center'>$no</td>
    			<td>$ty_arr[DocumentType_Name]</td>
    			";
    			$no++;
    			$stat_query="SELECT *
    						 FROM M_DocumentLegal dl, M_DocumentInformation2 di2
    						 WHERE dl.DL_CompanyID='$c_arr[Company_ID]'
    						 AND dl.DL_GroupDocID='$g_arr[DocumentGroup_ID]'
    						 AND dl.DL_CategoryDocID='$ca_arr[DocumentCategory_ID]'
    						 AND dl.DL_TypeDocID='$ty_arr[DocumentType_ID]'
    						 AND dl.DL_Information2=di2.DocumentInformation2_ID";
    			$stat_sql=mysql_query($stat_query);
    			$stat_row=mysql_num_rows($stat_sql);
    			$a=1;
    			if (!$stat_row){
    				$dokumen.="<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
    			}
    			else {
    				while ($stat_arr=mysql_fetch_array ($stat_sql)) {
    					$dokumen.="<td align='center'>$stat_arr[DL_Information3]</td>";
    					$dokumen.="<td align='center'>$stat_arr[DL_Instance]</td>";
    					$dokumen.="<td align='center'>$stat_arr[DL_NoDoc]</td>";

    					$pubdate=(!$stat_arr['DL_PubDate'])? "":date("j M Y", strtotime("$stat_arr[DL_PubDate]"));
    					$expdate=($stat_arr['DL_ExpDate']=="0000-00-00 00:00:00")? "-":date("j M Y", strtotime("$stat_arr[DL_ExpDate]"));

    					$dokumen.="<td align='center'>$pubdate</td>";
    					$dokumen.="<td align='center'>$expdate</td>";
    					$dokumen.="<td align='center'>$stat_arr[DocumentInformation2_Name]</td>";

    					if($a<$stat_row) {
    						$dokumen.="</tr><tr><td colspan=2>&nbsp;</td>";
    						$rowspanNum=$rowspanNum+1;
    					}
    					else {
    						$dokumen.="</tr>";
    					}
    					$a++;
    				}
    			}
    		}

    		$kategori .="
    		<tr>
    			<td rowspan='$rowspanNum' class='category' align='center'>$ca_arr[DocumentCategory_Name]</td>
    		";
    		echo $kategori.$dokumen;
    		$kategori ="";
    		$dokumen = "";


    	}
    }
    echo"</table>";
}
?>
</table>
</div>
</body>
</html>
<?PHP } ?>
