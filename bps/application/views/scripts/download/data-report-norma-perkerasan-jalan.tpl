<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_harga_perkerasan_jalan_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>HARGA PERKERASAN JALAN</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>MIN JARAK (KM)</td>
	<td>MAX JARAK (KM)</td>
	<td>JARAK AVG (KM)</td>
	<td>JARAK PP (KM)</td>
	<td>JUMLAH MATERIAL</td>
	<td>TRIP MATERIAL</td>
	<td>BIAYA MATERIAL</td>
	<td>DT TRIP</td>
	<td>DT PRICE</td>
	<td>EXCAV HM</td>
	<td>EXCAV PRICE</td>
	<td>COMPACTOR HM</td>
	<td>COMPACTOR PRICE</td>
	<td>GRADER HM</td>
	<td>GRADER PRICE</td>
	<td>INTERNAL PRICE</td>
	<td>EXTERNAL PERCENT</td>
	<td>EXTERNAL BENEFIT</td>
	<td>EXTERNAL PRICE</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		$jarak = explode("-", $row['PARAMETER_VALUE']);
		
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['DESCRIPTION']."</td>";
		echo "<td>".str_replace(" ", "", $jarak[0])."</td>";
		echo "<td>".str_replace(" ", "", $jarak[1])."</td>";
		echo "<td>".$row['JARAK_AVG']."</td>";
		echo "<td>".$row['JARAK_PP']."</td>";
		echo "<td>".$row['MATERIAL_QTY']."</td>";
		echo "<td>".$row['TRIP_MATERIAL']."</td>";
		echo "<td>".$row['BIAYA_MATERIAL']."</td>";
		echo "<td>".$row['DT_TRIP']."</td>";
		echo "<td>".$row['DT_PRICE']."</td>";
		echo "<td>".$row['EXCAV_HM']."</td>";
		echo "<td>".$row['EXCAV_PRICE']."</td>";
		echo "<td>".$row['COMPACTOR_HM']."</td>";
		echo "<td>".$row['COMPACTOR_PRICE']."</td>";
		echo "<td>".$row['GRADER_HM']."</td>";
		echo "<td>".$row['GRADER_PRICE']."</td>";
		echo "<td>".$row['INTERNAL_PRICE']."</td>";
		echo "<td>".$row['EXTERNAL_PERCENT']."</td>";
		echo "<td>".$row['EXTERNAL_BENEFIT']."</td>";
		echo "<td>".$row['EXTERNAL_PRICE']."</td>";
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