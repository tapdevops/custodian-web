<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_biaya_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA BIAYA</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>GRUP AKTIVITAS</td>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>ACTIVITY CLASS</td>
	<td>PALM AGE</td>
	<td>LAND TYPE</td>
	<td>TOPOGRAPHY</td>
	<td>COST ELEMENT</td>
	<td>KODE SUB COST ELEMENT</td>
	<td>DESKRIPSI SUB COST ELEMENT</td>
	<td>UOM</td>
	<td>QTY<BR>UMUM</td>
	<td>ROTASI<BR>UMUM</td>
	<td>VOLUME (%)<BR>UMUM</td>
	<td>QTY / HA<BR>UMUM</td>
	<td>HARGA<BR>UMUM</td>
	<td>RP / HA<BR>UMUM</td>
	<td>RP / HA / ROTASI<BR>UMUM</td>
	<td>QTY<BR>KHUSUS</td>
	<td>ROTASI<BR>KHUSUS</td>
	<td>VOLUME (%)<BR>KHUSUS</td>
	<td>QTY / HA<BR>KHUSUS</td>
	<td>HARGA<BR>KHUSUS</td>
	<td>RP / HA<BR>KHUSUS</td>
	<td>RP / HA / ROTASI<BR>KHUSUS</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_GROUP']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_DESC']."</td>";
		echo "<td>".$row['ACTIVITY_CLASS']."</td>";
		echo "<td>".$row['PALM_AGE']."</td>";
		echo "<td>".$row['LAND_TYPE']."</td>";
		echo "<td>".$row['TOPOGRAPHY']."</td>";
		echo "<td>".$row['COST_ELEMENT']."</td>";
		echo "<td>".$row['SUB_COST_ELEMENT']."</td>";
		echo "<td>".$row['SUB_COST_ELEMENT_DESC']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['QTY']."</td>";
		echo "<td>".$row['ROTASI']."</td>";
		echo "<td>".$row['VOLUME']."</td>";
		echo "<td>".$row['QTY_HA']."</td>";
		echo "<td>".$row['PRICE']."</td>";
		echo "<td>".$row['PRICE_HA']."</td>";
		echo "<td>".$row['PRICE_ROTASI']."</td>";
		echo "<td>".$row['QTY_SITE']."</td>";
		echo "<td>".$row['ROTASI_SITE']."</td>";
		echo "<td>".$row['VOLUME_SITE']."</td>";
		echo "<td>".$row['QTY_HA_SITE']."</td>";
		echo "<td>".$row['PRICE_SITE']."</td>";
		echo "<td>".$row['PRICE_HA_SITE']."</td>";
		echo "<td>".$row['PRICE_ROTASI_SITE']."</td>";
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