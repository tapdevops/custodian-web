<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_panen_actual_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>PANEN AKTUAL</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>REGION CODE</td>
	<td>BA CODE</td>
	<td>AFD CODE</td>
	<td>BLOCK CODE</td>
	<td>LAND CATEGORY</td>
	<td>HA PANEN</td>
	<td>POKOK PANEN</td>
	<td>SPH</td>
	<td>TON PANEN</td>
	<td>YIELD HA</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['REGION_CODE']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['AFD_CODE']."</td>";
		echo "<td>".$row['BLOCK_CODE']."</td>";
		echo "<td>".$row['LAND_CATEGORY']."</td>";
		echo "<td>".$row['HA_PANEN']."</td>";
		echo "<td>".$row['POKOK_PANEN']."</td>";
		echo "<td>".$row['SPH']."</td>";
		echo "<td>".$row['TON_PANEN']."</td>";
		echo "<td>".$row['YIELD_HA']."</td>";
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