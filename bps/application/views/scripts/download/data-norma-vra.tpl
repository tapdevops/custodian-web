<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_vra_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA VRA</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE VRA</td>
	<td>DESKRIPSI VRA</td>
	<td>MIN UMUR</td>
	<td>MAX UMUR</td>
	<td>QTY/HARI</td>
	<td>HARI VRA/TAHUN</td>
	<td>UOM</td>
	<td>KODE RVRA</td>
	<td>DESKRIPSI RVRA</td>
	<td>KODE MATERIAL</td>
	<td>DESKRIPSI MATERIAL</td>
	<td>QTY/SAT</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['VRA_CODE']."</td>";
		echo "<td>".$row['VRA_TYPE']."</td>";
		echo "<td>".$row['MIN_YEAR']."</td>";
		echo "<td>".$row['MAX_YEAR']."</td>";
		echo "<td>".$row['QTY_DAY']."</td>";
		echo "<td>".$row['DAY_YEAR_VRA']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['SUB_RVRA_CODE']."</td>";
		echo "<td>".$row['SUB_RVRA_DESCRIPTION']."</td>";
		echo "<td>".$row['MATERIAL_CODE']."</td>";
		echo "<td>".$row['MATERIAL_NAME']."</td>";
		echo "<td>".$row['QTY_UOM']."</td>";
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