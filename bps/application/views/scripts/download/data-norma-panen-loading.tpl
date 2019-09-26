<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_panen_loading_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PANEN LOADING</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>MIN<BR>JARAK PKS</td>
	<td>MAX<BR>JARAK PKS</td>
	<td>TARGET ANGKUT</td>
	<td>BASIS</td>
	<td>JUMLAH<BR>TUKANG MUAT</td>
	<td>SELISIH BASIS<BR>TUKANG MUAT</td>
	<td>TARIF<BR>TUKANG MUAT</td>
	<td>RP/HK<BR>TUKANG MUAT</td>
	<td>RP BASIS<BR>TUKANG MUAT</td>
	<td>RP/KG BASIS<BR>TUKANG MUAT</td>
	<td>RP PREMI<BR>TUKANG MUAT</td>
	<td>RP/KG PREMI<BR>TUKANG MUAT</td>
	<td>TARIF SUPIR</td>
	<td>RP PREMI SUPIR</td>
	<td>RP/KG PREMI SUPIR</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['JARAK_PKS_MIN']."</td>";
		echo "<td>".$row['JARAK_PKS_MAX']."</td>";
		echo "<td>".$row['TARGET_ANGKUT_TM_SUPIR']."</td>";
		echo "<td>".$row['BASIS_TM_SUPIR']."</td>";
		echo "<td>".$row['JUMLAH_TM']."</td>";
		echo "<td>".$row['SELISIH_TM']."</td>";
		echo "<td>".$row['TARIF_TM']."</td>";
		echo "<td>".$row['RP_HK_TM']."</td>";
		echo "<td>".$row['RP_BASIS_TM']."</td>";
		echo "<td>".$row['RP_KG_BASIS_TM']."</td>";
		echo "<td>".$row['RP_PREMI_TM']."</td>";
		echo "<td>".$row['RP_KG_PREMI_TM']."</td>";
		echo "<td>".$row['TARIF_SUPIR']."</td>";
		echo "<td>".$row['RP_PREMI_SUPIR']."</td>";
		echo "<td>".$row['RP_KG_PREMI_SUPIR']."</td>";
		echo "</tr>";
	}
}

echo"
</table>";

echo"
</body>
</html>
";
?>