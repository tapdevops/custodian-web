<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_panen_supervisi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PANEN SUPERVISI</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>COMPANY NAME</td>
	<td>MIN RANGE BJR</td>
	<td>MAX RANGE BJR</td>
	<td>JANJANG BASIS MANDOR DAN PEMANEN</td>
	<td>BJR BUDGET</td>
	<td>JANJANG OPERATION</td>
	<td>OVER BASIS JANJANG</td>
	<td>RP/KG</td>
	<td>ASUMSI OVER BASIS</td>
	<td>AVG MANDOR(CR)</td>
	<td>RATIO / PEMANEN</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['COMPANY_NAME']."</td>";
		echo "<td>".$row['MIN_BJR']."</td>";
		echo "<td>".$row['MAX_BJR']."</td>";
		echo "<td>".$row['JANJANG_BASIS_MANDOR']."</td>";
		echo "<td>".$row['BJR_BUDGET']."</td>";
		echo "<td>".$row['JANJANG_OPERATION']."</td>";
		echo "<td>".$row['OVER_BASIS_JANJANG']."</td>";
		echo "<td>".$row['RP_KG']."</td>";
		echo "<td>".$row['ASUMSI_OVER_BASIS']."</td>";
		echo "<td>".$row['AVG_MANDOR']."</td>";
		echo "<td>".$row['RATIO_PEMANEN']."</td>";
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