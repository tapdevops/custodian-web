<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_report_checkroll_alokasi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>REPORT ALOKASI CHECKROLL</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>MATURITY STAGE</td>
	<td>TIPE TUNJANGAN</td>
	<td>TOTAL BIAYA</td>
	<td>DIS JAN</td>
	<td>DIS FEB</td>
	<td>DIS MAR</td>
	<td>DIS APR</td>
	<td>DIS MAY</td>
	<td>DIS JUN</td>
	<td>DIS JUL</td>
	<td>DIS AUG</td>
	<td>DIS SEP</td>
	<td>DIS OCT</td>
	<td>DIS NOV</td>
	<td>DIS DEC</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['MATURITY_STAGE']."</td>";
		echo "<td>".$row['TUNJANGAN_TYPE']."</td>";
		echo "<td>".$row['TOTAL_BIAYA']."</td>";
		echo "<td>".$row['DIS_JAN']."</td>";
		echo "<td>".$row['DIS_FEB']."</td>";
		echo "<td>".$row['DIS_MAR']."</td>";
		echo "<td>".$row['DIS_APR']."</td>";
		echo "<td>".$row['DIS_MAY']."</td>";
		echo "<td>".$row['DIS_JUN']."</td>";
		echo "<td>".$row['DIS_JUL']."</td>";
		echo "<td>".$row['DIS_AUG']."</td>";
		echo "<td>".$row['DIS_SEP']."</td>";
		echo "<td>".$row['DIS_OCT']."</td>";
		echo "<td>".$row['DIS_NOV']."</td>";
		echo "<td>".$row['DIS_DEC']."</td>";
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