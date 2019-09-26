<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_alat_kerja_panen_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>ALAT KERJA - PANEN</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE MATERIAL</td>
	<td>DESKRIPSI MATERIAL</td>
	<td>UOM</td>
	<td>HARGA</td>
	<td>ROTASI</td>
	<td>TOTAL</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['MATERIAL_CODE']."</td>";
		echo "<td>".$row['MATERIAL_NAME']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['HARGA_INFLASI']."</td>";
		echo "<td>".$row['ROTASI']."</td>";
		echo "<td>".$row['TOTAL']."</td>";
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