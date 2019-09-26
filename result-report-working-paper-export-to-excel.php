<?php
$Output = "";
// foreach ($_POST as $key => $value) {
//     $Output .="Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
// }
// echo $Output;
// exit();
switch($_POST['optDocumentGroup']){
    case "1" : $GrupDokumen = "Legal"; break;
    case "2" : $GrupDokumen = "Lisensi"; break;
}

// Fungsi header dengan mengirimkan raw data excel
header("Content-type: application/vnd-ms-excel");
// Mendefinisikan nama file ekspor
header("Content-Disposition: attachment; filename=Result_Laporan_Working_Paper_-_Dokumen_$GrupDokumen.xls");

error_reporting(E_ALL);
$PHP_SELF = "http://".$_SERVER['HTTP_HOST'];

include ("./config/config_db.php");

$qcompany=($_POST['optCompany'] == "ALL")?"":"AND Company_ID='$_POST[optCompany]'";
$qarea=(!$_POST['optArea'])?"":"AND Company_ID_Area='$_POST[optArea]'";
// $CompanyID=$_POST['optCompany'];
$qgrup=(!$_POST['optDocumentGroup'])?"":"AND DocumentGroup_ID='$_POST[optDocumentGroup]'";
$qcategory=(!$_POST['optDocumentCategory'])?"":"AND dc.DocumentCategory_ID='$_POST[optDocumentCategory]'";

$Output .="
<table>
<tr>
    <td colspan='100'><h3>Laporan Working Paper</h3></td>
</tr>";

$c_query="SELECT *
		  FROM M_Company
		  WHERE
              Company_Delete_Time is NULL
              $qarea
              $qcompany";
$c_sql=mysql_query($c_query);

while($c_arr=mysql_fetch_array($c_sql)){

    $Output .="
    <tr>
        <td colspan='100'>&nbsp;</td>
    </tr>
    <tr>
        <td colspan='100'><b>".$c_arr['Company_Name']."</b></td>
    </tr>
    <tr>
        <td colspan='100'>Tanggal Cetak : ".date('j M Y')."</td>
    </tr>
    </table>";

    $dokumen = "";
    $kategori = "";

    $g_query="SELECT *
    		  FROM M_DocumentGroup
    		  WHERE DocumentGroup_Delete_Time IS NULL
    		  AND (DocumentGroup_ID = '1' OR DocumentGroup_ID = '2')
    		  $qgrup
    		  ORDER BY DocumentGroup_ID";
    $g_sql = mysql_query($g_query);

    $Output .= "
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
    	$Output .= "
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
    		$Output .= $kategori.$dokumen;
    		$kategori ="";
    		$dokumen = "";


    	}
    }
    $Output .="</table>";
}

echo $Output;

exit();
?>
