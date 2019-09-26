<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_wra_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA WRA</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE GROUP WRA</td>
	<td>DESKRIPSI GROUP WRA</td>
	<td>KODE SUB GROUP WRA</td>
	<td>DESKRIPSI SUB GROUP WRA</td>
	<td>QTY/ROTASI</td>
	<td>ROTASI/TAHUN</td>
	<td>QTY/TAHUN</td>
	<td>HARGA</td>
	<td>HARGA/QTY/TAHUN</td>
	<td>RP/QTY</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['GROUP_WRA_CODE']."</td>";
		echo "<td>".$row['GROUP_WRA_DESC']."</td>";
		echo "<td>".$row['SUB_WRA_GROUP']."</td>";
		echo "<td>".$row['SUB_WRA_GROUP_DESC']."</td>";
		echo "<td>".$row['QTY_ROTASI']."</td>";
		echo "<td>".$row['ROTASI_TAHUN']."</td>";
		echo "<td>".$row['QTY_TAHUN']."</td>";
		echo "<td>".$row['HARGA_INFLASI']."</td>";
		echo "<td>".$row['PRICE_QTY_TAHUN']."</td>";
		echo "<td>".$row['RP_QTY']."</td>";
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