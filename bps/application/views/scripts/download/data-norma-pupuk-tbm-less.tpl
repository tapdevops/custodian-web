<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_pupuk_tbm_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PUPUK TBM</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>LAND TYPE</td>
	<td>PALM AGE</td>
	<td>MATURITY STATUS</td>
	<td>KODE MATERIAL</td>
	<td>DESKRIPSI MATERIAL</td>
	<td>ROTASI/BULAN</td>
	<td>DOSIS/POKOK (KG)</td>
	<td>JUMLAH</td>
	<td>HARGA</td>
	<td>RP/ROTASI</td>
	<td>RP/ROTASI/TAHUN</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['LAND_TYPE']."</td>";
		echo "<td>".$row['PALM_AGE']."</td>";
		echo "<td>".$row['MATURITY_STAGE']."</td>";
		echo "<td>".$row['MATERIAL_CODE']."</td>";
		echo "<td>".$row['MATERIAL_NAME']."</td>";
		echo "<td>".$row['ROTASI']."</td>";
		echo "<td>".$row['DOSIS']."</td>";
		echo "<td>".$row['JUMLAH']."</td>";
		echo "<td>".$row['PRICE']."</td>";
		echo "<td>".$row['PRICE_ROTASI']."</td>";
		echo "<td>".$row['PRICE_YEAR']."</td>";
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