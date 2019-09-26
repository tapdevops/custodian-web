<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_material_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>MATERIAL</div><br>
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
	<td>VALUATION CLASS</td>
	<td>KODE COA</td>
	<td>DESKRIPSI COA</td>
	<td>HARGA</td>
	<td>KODE NORMA DASAR</td>
	<td>DESKRIPSI NORMA DASAR</td>
	<td>% KENAIKAN</td>
	<td>FLAG</td>
	<td>KODE DETAIL KATEGORI</td>
	<td>DESKRIPSI DETAIL KATEGORI</td>
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
		echo "<td>".$row['VALUATION_CLASS']."</td>";
		echo "<td>".$row['COA_CODE']."</td>";
		echo "<td>".$row['COA_DESC']."</td>";
		echo "<td>".$row['PRICE']."</td>";
		echo "<td>".$row['BASIC_NORMA_CODE']."</td>";
		echo "<td>".$row['NORMA_DESCRIPTION']."</td>";
		echo "<td>".$row['PERCENT_INCREASE']."</td>";
		echo "<td>".$row['FLAG']."</td>";
		echo "<td>".$row['DETAIL_CAT_CODE']."</td>";
		echo "<td>".$row['DETAIL_CAT_DESC']."</td>";
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