<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_infrastruktur_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA INFRASTRUKTUR</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>ACTIVITY CLASS</td>
	<td>LAND TYPE</td>
	<td>TOPOGRAFI</td>
	<td>COST ELEMENT</td>
	<td>KODE SUB COST ELEMENT</td>
	<td>DESKRIPSI SUB COST ELEMENT</td>
	<td>UOM SUB COST ELEMENT</td>
	<td>QTY INFRA</td>
	<td>QTY</td>
	<td>ROTASI</td>
	<td>VOLUME</td>
	<td>QTY/HA</td>
	<td>HARGA EXTERNAL</td>
	<td>RP/HA EXTERNAL</td>
	<td>HARGA INTERNAL</td>
	<td>RP/QTY INTERNAL</td>
	<td>RP/HA INTERNAL</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_DESC']."</td>";
		echo "<td>".$row['ACTIVITY_CLASS']."</td>";
		echo "<td>".$row['LAND_TYPE']."</td>";
		echo "<td>".$row['TOPOGRAPHY']."</td>";
		echo "<td>".$row['COST_ELEMENT']."</td>";
		echo "<td>".$row['SUB_COST_ELEMENT']."</td>";
		echo "<td>".$row['SUB_COST_ELEMENT_DESC']."</td>";
		echo "<td>".$row['UOM_ACTIVITY']."</td>";
		echo "<td>".$row['QTY_INFRA']."</td>";
		echo "<td>".$row['QTY']."</td>";
		echo "<td>".$row['ROTASI']."</td>";
		echo "<td>".$row['VOLUME']."</td>";
		echo "<td>".$row['QTY_HA']."</td>";
		echo "<td>".$row['PRICE']."</td>";
		echo "<td>".$row['RP_HA_EXTERNAL']."</td>";
		echo "<td>".$row['HARGA_INTERNAL']."</td>";
		echo "<td>".$row['RP_QTY_INTERNAL']."</td>";
		echo "<td>".$row['RP_HA_INTERNAL']."</td>";
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