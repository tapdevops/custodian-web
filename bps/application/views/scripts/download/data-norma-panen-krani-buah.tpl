<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_panen_krani_buah_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PANEN KRANI BUAH</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>TARGET<BR>KRANI BUAH</td>
	<td>BASIS<BR>KRANI BUAH</td>
	<td>TARIF BASIS<BR>KRANI BUAH</td>
	<td>SELISIH OVER BASIS<BR>KRANI BUAH</td>
	<td>RP/HK<BR>KRANI BUAH</td>
	<td>RP/KG BASIS<BR>KRANI BUAH</td>
	<td>TOTAL RP PREMI<BR>KRANI BUAH</td>
	<td>Rp/KG PREMI<BR>KRANI BUAH</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['TARGET']."</td>";
		echo "<td>".$row['BASIS']."</td>";
		echo "<td>".$row['TARIF_BASIS']."</td>";
		echo "<td>".$row['SELISIH_OVER_BASIS']."</td>";
		echo "<td>".$row['RP_HK']."</td>";
		echo "<td>".$row['RP_KG_BASIS']."</td>";
		echo "<td>".$row['TOTAL_RP_PREMI']."</td>";
		echo "<td>".$row['RP_KG_PREMI']."</td>";
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