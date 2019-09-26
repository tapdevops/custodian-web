<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_harga_borong_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA HARGA BORONG</div><br>
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
	<td>SPESIFIKASI</td>
	<td>UOM</td>
	<td>UMUM
HARGA EXTERNAL
(RP/QTY)</td>
	<td>RATA2 REGION</td>
	<td>RATA2 NASIONAL</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_DESCRIPTION']."</td>";
		echo "<td>".$row['ACTIVITY_CLASS']."</td>";
		echo "<td>".$row['SPESIFICATION']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".number_format($row['PRICE'],2)."</td>";
		echo "<td>".number_format($row['AVG_REGION'],2)."</td>";
		echo "<td>".number_format($row['AVG_PT'],2)."</td>";
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