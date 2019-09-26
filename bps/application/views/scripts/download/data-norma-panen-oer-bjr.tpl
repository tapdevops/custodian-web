<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_panen_oerbjr_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PANEN OER BJR</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>ASUMSI OVER BASIS</td>
	<td>MIN BJR</td>
	<td>MAX BJR</td>
	<td>JANJANG BASIS MANDOR</td>
	<td>BJR BUDGET</td>
	<td>JANJANG OPERATION</td>
	<td>OVER BASIS JANJANG</td>
	<td>MIN OER</td>
	<td>MAX OER</td>
	<td>RP/JANJANG<BR>PREMI PEMANEN</td>
	<td>RP/KG</td>
	<td>OER</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ASUMSI_OVER_BASIS']."</td>";
		echo "<td>".$row['BJR_MIN']."</td>";
		echo "<td>".$row['BJR_MAX']."</td>";
		echo "<td>".$row['JANJANG_BASIS_MANDOR']."</td>";
		echo "<td>".$row['BJR_BUDGET']."</td>";
		echo "<td>".$row['JANJANG_OPERATION']."</td>";
		echo "<td>".$row['OVER_BASIS_JANJANG']."</td>";
		echo "<td>".$row['OER_MIN']."</td>";
		echo "<td>".$row['OER_MAX']."</td>";
		echo "<td>".$row['PREMI_PANEN']."</td>";
		echo "<td>".$row['NILAI']."</td>";
		echo "<td>".$row['OER']."</td>";
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