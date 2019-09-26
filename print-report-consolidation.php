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
<title>Laporan Konsolidasi Dokumen</title>
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
document.getElementById('pager').style.display = "none"
window.print()
document.getElementById('PrintButton').style.display = "block"
document.getElementById('pager').style.display = "block"
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
$txtStart=(!$_GET['txtStart'])?"1":date('Y-m-d H:i:s', strtotime($_GET['txtStart']));
$txtEnd=(!$_GET['txtEnd'])?"1":date('Y-m-d H:i:s', strtotime($_GET['txtEnd']));
$start=date('j M Y', strtotime($_GET['txtStart']));
$end=date('j M Y', strtotime($_GET['txtEnd']));
$periode=((!$_GET['txtStart'])&&(!$_GET['txtEnd']))?"":"Periode $start s/d $end";

$qgrup=(!$_GET['optDocumentGroup'])?"":"AND DocumentGroup_ID='$_GET[optDocumentGroup]'";
$qcategory=(!$_GET['optDocumentCategory'])?"":"AND dc.DocumentCategory_ID='$_GET[optDocumentCategory]'";
$qarea=(!$_GET['optArea'])?"":"AND Company_ID_Area='$_GET[optArea]'";
echo"
<div id='title'>Laporan Konsolidasi Dokumen</div>
<div class='h2'>$periode</div>
<div class='h5'>Tanggal Cetak : ".date('j M Y')."</div>";

$dataPerPage = 30;
if(isset($_GET['page'])) {
    $noPage = $_GET['page'];
}
else
	$noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query1="SELECT *
		 FROM M_Company
		 $qarea
		 WHERE Company_Delete_Time is NULL";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;

echo"<div id='pager'>";
if ($noPage > 1)
	echo"<a href=$PHP_SELF?page=$prev&optArea=".$_GET['optArea']."&consolidation=".$_GET['consolidation']."&optDocumentGroup=".$_GET['optDocumentGroup']."&optDocumentCategory=".$_GET['optDocumentCategory']."&txtStart=".$_GET['txtStart']."&txtEnd=".$_GET['txtEnd'].">&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
        if (($showPage == 1) && ($p != 2))
			echo"...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			echo"...";
        if ($p == $noPage)
			echo"<b><u>$p</b></u> ";
        else
			echo"<a href=$_SERVER[PHP_SELF]?page=$p&optArea=".$_GET['optArea']."&consolidation=".$_GET['consolidation']."&optDocumentGroup=".$_GET['optDocumentGroup']."&optDocumentCategory=".$_GET['optDocumentCategory']."&txtStart=".$_GET['txtStart']."&txtEnd=".$_GET['txtEnd'].">$p</a> ";
        $showPage = $p;
    }
}
if ($noPage < $jumPage)
	echo"<a href=$PHP_SELF?page=$next&optArea=".$_GET['optArea']."&consolidation=".$_GET['consolidation']."&optDocumentGroup=".$_GET['optDocumentGroup']."&optDocumentCategory=".$_GET['optDocumentCategory']."&txtStart=".$_GET['txtStart']."&txtEnd=".$_GET['txtEnd'].">Next &gt;&gt;</a> ";
echo"</div>";

$g_query="SELECT *
		  FROM M_DocumentGroup
		  WHERE DocumentGroup_Delete_Time IS NULL
		  AND DocumentGroup_ID IN ('1', '2')
		  $qgrup
		  ORDER BY DocumentGroup_ID";
$g_sql = mysql_query($g_query);

while ($g_arr=mysql_fetch_array($g_sql)){
	echo "
	<div class='title'>Grup : $g_arr[DocumentGroup_Name]</div>
	<table width='100%' cellpadding='0' cellspacing='0' border='1' style='border:3px solid #000;'>
	<tr>
		<td></td>";

	$co_query="SELECT *
			   FROM M_Company
			   WHERE Company_Delete_Time IS NULL
			   $qarea
			   ORDER BY Company_Area DESC, Company_Name
			   LIMIT $offset, $dataPerPage";
	$co_sql = mysql_query($co_query);
	$coNum = mysql_num_rows($co_sql);
	$colspanNum=$coNum+1;
	$jumCol=0;

	while ($co_arr=mysql_fetch_array($co_sql)){
		if ($jumCol==30) {
			$style="style='page-break-after:always'";
			$jumCol=0;
		}
		else{
			$jumCol="";
		}
		echo"<td height='50' width='30'><div class='vertical-text'>$co_arr[Company_Code]</div></td>";
		$jumCol++;
	}
	echo"</tr>";
	$ca_query="SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
			   FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
			   WHERE dgct.DGCT_Delete_Time IS NULL
			   AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
			   $qcategory
			   AND dgct.DGCT_DocumentGroupID='$g_arr[DocumentGroup_ID]'
			   ORDER BY dc.DocumentCategory_ID";
	$ca_sql = mysql_query($ca_query);
	while ($ca_arr=mysql_fetch_array($ca_sql)){
		echo"
		<tr>
			<td colspan='$colspanNum' class='category'>Kategori : $ca_arr[DocumentCategory_Name]</td>
		</tr>
		";
		$ty_query="SELECT DISTINCT dt.DocumentType_ID, dt.DocumentType_Name
				   FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
				   WHERE dgct.DGCT_Delete_Time IS NULL
				   AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
				   AND dgct.DGCT_DocumentCategoryID='$ca_arr[DocumentCategory_ID]'
				   AND dgct.DGCT_DocumentGroupID='$g_arr[DocumentGroup_ID]'
				   ORDER BY dt.DocumentType_ID";
		$ty_sql = mysql_query($ty_query);
		while ($ty_arr=mysql_fetch_array($ty_sql)){
			echo"
			<tr>
				<td width='300'>$ty_arr[DocumentType_Name]</td>
			";
			$co_query="SELECT Company_ID, Company_Code
		   			   FROM M_Company
		   			   WHERE Company_Delete_Time IS NULL
					   $qarea
				       ORDER BY Company_Area DESC, Company_Name
					   LIMIT $offset, $dataPerPage";
			$co_sql = mysql_query($co_query);

			while($co_arr=mysql_fetch_array($co_sql)){
				$qperiod=((!$_GET['txtStart'])&&(!$_GET['txtEnd']))?"":"AND DL_RegTime BETWEEN '$txtStart' AND '$txtEnd'";
				$stat_query="SELECT *
							 FROM M_DocumentLegal
							 WHERE DL_CompanyID='$co_arr[Company_ID]'
							 AND DL_GroupDocID='$g_arr[DocumentGroup_ID]'
							 AND DL_CategoryDocID='$ca_arr[DocumentCategory_ID]'
							 AND DL_TypeDocID='$ty_arr[DocumentType_ID]'
							 $qperiod";
							 //echo "$stat_query<br><br>";
				$stat_sql=mysql_query($stat_query);
				$stat_num=mysql_num_rows($stat_sql);
				if($stat_num==0)
					echo"<td></td>";
				else
					echo"<td bgcolor='#CCFF99' align='center' valign='middle'>$stat_num</td>";
			}

			echo"</tr>";
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
