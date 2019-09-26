<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_ha_statement_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>HA STATEMENT</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>AFD CODE</td>
	<td>BLOCK - DESC</td>
	<td>HA PLANTED</td>
	<td>TOPOGRAFI</td>
	<td>LAND TYPE</td>
	<td>PROGENY</td>
	<td>LAND SUITABILITY</td>
	<td>BULAN TANAM</td>
	<td>TAHUN TANAM</td>
	<td>STATUS SMS1</td>
	<td>STATUS SMS2</td>
	<td>POKOK TANAM</td>
	<td>SPH</td>
	<td>STATUS</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['AFD_CODE']."</td>";
		echo "<td>'".$row['BLOCK_CODE'] . " - " . $row['BLOCK_DESC'] ."</td>";
		echo "<td>".$row['HA_PLANTED']."</td>";
		echo "<td>".$row['TOPOGRAPHY']."</td>";
		echo "<td>".$row['LAND_TYPE']."</td>";
		echo "<td>".$row['PROGENY']."</td>";
		echo "<td>".$row['LAND_SUITABILITY']."</td>";
		echo "<td>".$row['TAHUN_TANAM_M']."</td>";
		echo "<td>".$row['TAHUN_TANAM_Y']."</td>";
		echo "<td>".$row['MATURITY_STAGE_SMS1']."</td>";
		echo "<td>".$row['MATURITY_STAGE_SMS2']."</td>";
		echo "<td>".$row['POKOK_TANAM']."</td>";
		echo "<td>".$row['SPH']."</td>";
		echo "<td>".$row['STATUS']."</td>";
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