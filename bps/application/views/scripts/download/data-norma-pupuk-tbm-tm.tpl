<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_pupuk_tm_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PUPUK TM</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>AFD</td>
	<td>BLOK</td>
	<td>STATUS SMS1</td>
	<td>STATUS SMS2</td>
	<td>BULAN TANAM</td>
	<td>TAHUN TANAM</td>
	<td>HA</td>
	<td>JENIS</td>
	<td>POKOK</td>
	<td>BULAN</td>
	<td>KODE MATERIAL</td>
	<td>DESKRIPSI MATERIAL</td>
	<td>DOSIS/POKOK</td>
	<td>JUMLAH KG</td>
	<td>HARGA</td>
	<td>BIAYA</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['AFD_CODE']."</td>";
		echo "<td>".$row['BLOCK_CODE']."</td>";
		echo "<td>".$row['MATURITY_STAGE_SMS1']."</td>";
		echo "<td>".$row['MATURITY_STAGE_SMS2']."</td>";
		echo "<td>".$row['TAHUN_TANAM_M']."</td>";
		echo "<td>".$row['TAHUN_TANAM_Y']."</td>";
		echo "<td>".$row['HA_PUPUK']."</td>";
		echo "<td>".$row['JENIS_TANAM']."</td>";
		echo "<td>".$row['POKOK']."</td>";
		echo "<td>".$row['BULAN_PEMUPUKAN']."</td>";
		echo "<td>".$row['MATERIAL_CODE']."</td>";
		echo "<td>".$row['MATERIAL_NAME']."</td>";
		echo "<td>".$row['DOSIS']."</td>";
		echo "<td>".$row['JUMLAH']."</td>";
		echo "<td>".$row['PRICE']."</td>";
		echo "<td>".$row['BIAYA']."</td>";
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