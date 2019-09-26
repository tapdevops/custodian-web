<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_panen_premi_langsir_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PANEN PREMI LANGSIR</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE VRA</td>
	<td>DESKRIPSI VRA</td>
	<td>TON/TRIP</td>
	<td>TRIP/HARI</td>
	<td>TON/HARI</td>
	<td>HM/TRIP</td>
	<td>RP/HM</td>
	<td>RP/TRIP</td>
	<td>RP/KG</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['VRA_CODE']."</td>";
		echo "<td>".$row['VRA_TYPE']."</td>";
		echo "<td>".$row['TON_TRIP']."</td>";
		echo "<td>".$row['TRIP_HARI']."</td>";
		echo "<td>".$row['TON_HARI']."</td>";
		echo "<td>".$row['HM_TRIP']."</td>";
		echo "<td>".$row['RP_HM']."</td>";
		echo "<td>".$row['RP_TRIP']."</td>";
		echo "<td>".$row['RP_KG']."</td>";
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