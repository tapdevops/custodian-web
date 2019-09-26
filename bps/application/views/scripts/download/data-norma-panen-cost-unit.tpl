<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_panen_cost_unit_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PANEN COST UNIT</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>JARAK ANGKUT</td>
	<td>TARGET</td>
	<td>RIT</td>
	<td>RP/KM INTERNAL</td>
	<td>RP/KM/KG INTERNAL</td>
	<td>RP/KM EXTERNAL</td>
	<td>RP/KM/KG EXTERNAL</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['JARAK_ANGKUT']."</td>";
		echo "<td>".$row['TARGET']."</td>";
		echo "<td>".$row['RIT']."</td>";
		echo "<td>".$row['RP_KM_INTERNAL']."</td>";
		echo "<td>".$row['RP_KG_INTERNAL']."</td>";
		echo "<td>".$row['RP_KM_EXTERNAL']."</td>";
		echo "<td>".$row['RP_KG_EXTERNAL']."</td>";
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