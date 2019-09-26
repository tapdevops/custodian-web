<?php
// foreach ($_GET as $key => $value) {
//     $Output .="Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
// }
// exit();
switch($_GET['optDocumentGroup']){
    case "1" : $GrupDokumen = "Legal"; break;
    case "2" : $GrupDokumen = "Lisensi"; break;
}

$Output = "";

$txtStart=(!$_GET['txtStart'])?"1":date('Y-m-d H:i:s', strtotime($_GET['txtStart']));
$txtEnd=(!$_GET['txtEnd'])?"1":date('Y-m-d H:i:s', strtotime($_GET['txtEnd']));
$start=date('j M Y', strtotime($_GET['txtStart']));
$end=date('j M Y', strtotime($_GET['txtEnd']));
$periode=((!$_GET['txtStart'])&&(!$_GET['txtEnd']))?"":"<tr><td>Periode $start s/d $end</td></tr>";

$qgrup=(!$_GET['optDocumentGroup'])?"":"AND DocumentGroup_ID='$_GET[optDocumentGroup]'";
$qcategory=(!$_GET['optDocumentCategory'])?"":"AND dc.DocumentCategory_ID='$_GET[optDocumentCategory]'";
$qarea=(!$_GET['optArea'])?"":"AND Company_ID_Area='$_GET[optArea]'";

$Output .="
<table>
<tr>
    <td><h3>Laporan Konsolidasi Dokumen</h3></td>
</tr>
$periode
<tr>
    <td>Tanggal Cetak : ".date('j M Y')."</td>
</tr>
</table>";

// Fungsi header dengan mengirimkan raw data excel
header("Content-type: application/vnd-ms-excel");
// Mendefinisikan nama file ekspor
header("Content-Disposition: attachment; filename=Result_Laporan_Konsolidasi_-_Dokumen_$GrupDokumen.xls");

include ("./config/config_db.php");
error_reporting(E_ALL);
$PHP_SELF = "http://".$_SERVER['HTTP_HOST'];

$g_query="SELECT *
		  FROM M_DocumentGroup
		  WHERE DocumentGroup_Delete_Time IS NULL
          AND DocumentGroup_ID IN ('1', '2')
		  $qgrup
		  ORDER BY DocumentGroup_ID";
          // echo $g_query;
$g_sql = mysql_query($g_query);

while ($g_arr=mysql_fetch_array($g_sql)){
	$Output .="
	<div class='title'>Grup : $g_arr[DocumentGroup_Name]</div>
	<table width='100%' cellpadding='0' cellspacing='0' border='1' style='border:3px solid #000;'>
	<tr>
		<td></td>";

	$co_query="SELECT *
			   FROM M_Company
			   WHERE Company_Delete_Time IS NULL
			   $qarea
			   ORDER BY Company_Area DESC, Company_Name";
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
		$Output .="<td height='50' width='30'><div class='vertical-text'>$co_arr[Company_Code]</div></td>";
		$jumCol++;
	}
	$Output .="</tr>";
	$ca_query="SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
			   FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
			   WHERE dgct.DGCT_Delete_Time IS NULL
			   AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
			   $qcategory
			   AND dgct.DGCT_DocumentGroupID='$g_arr[DocumentGroup_ID]'
			   ORDER BY dc.DocumentCategory_ID";
	$ca_sql = mysql_query($ca_query);
	while ($ca_arr=mysql_fetch_array($ca_sql)){
		$Output .="
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
			$Output .="
			<tr>
				<td width='300'>$ty_arr[DocumentType_Name]</td>
			";
			$co_query="SELECT Company_ID, Company_Code
		   			   FROM M_Company
		   			   WHERE Company_Delete_Time IS NULL
					   $qarea
				       ORDER BY Company_Area DESC, Company_Name";
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
							 //$Output .="$stat_query<br><br>";
				$stat_sql=mysql_query($stat_query);
				$stat_num=mysql_num_rows($stat_sql);
				if($stat_num==0)
					$Output .="<td></td>";
				else
					$Output .="<td bgcolor='#CCFF99' align='center' valign='middle'>$stat_num</td>";
			}

			$Output .="</tr>";
		}

	}
	$Output .="</table>";
}

// Menampilkan Dokumen
// $Output .=$Output;

echo $Output;

exit();
?>
