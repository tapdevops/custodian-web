<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_ho_act_outlook_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>HO ACTUAL OUTLOOK</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>COST CENTER</td>
	<td>KODE COA</td>
	<td>DESKRIPSI COA</td>
	<td>KETERANGAN TRANSAKSI</td>
	<td>CORE</td>
	<td>PT</td>
	<td>JAN</td>
	<td>FEB</td>
	<td>MAR</td>
	<td>APR</td>
	<td>MAY</td>
	<td>JUN</td>
	<td>JUL</td>
	<td>AUG</td>
	<td>SEP</td>
	<td>OCT</td>
	<td>NOV</td>
	<td>DEC</td>
	<td>YTD<br />ACTUAL</td>
	<td>ADJ</td>
	<td>OUTLOOK</td>
	<td>2018</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['HCC_CC'].' - '.$row['HCC_COST_CENTER']."</td>";
		echo "<td>".$row['COA_CODE']."</td>";
		echo "<td>".$row['COA_NAME']."</td>";
		echo "<td>".$row['TRANSACTION_DESC']."</td>";
		echo "<td>".$row['CORE_CODE']."</td>";
		echo "<td>".$row['COMPANY_NAME']."</td>";
		echo "<td>".$row['ACT_JAN']."</td>";
		echo "<td>".$row['ACT_FEB']."</td>";
		echo "<td>".$row['ACT_MAR']."</td>";
		echo "<td>".$row['ACT_APR']."</td>";
		echo "<td>".$row['ACT_MAY']."</td>";
		echo "<td>".$row['ACT_JUN']."</td>";
		echo "<td>".$row['ACT_JUL']."</td>";
		echo "<td>".$row['ACT_AUG']."</td>";
		echo "<td>".$row['OUTLOOK_SEP']."</td>";
		echo "<td>".$row['OUTLOOK_OCT']."</td>";
		echo "<td>".$row['OUTLOOK_NOV']."</td>";
		echo "<td>".$row['OUTLOOK_DEC']."</td>";
		echo "<td>".$row['YTD_ACTUAL']."</td>";
		echo "<td>".$row['ADJ']."</td>";
		echo "<td>".$row['OUTLOOK']."</td>";
		echo "<td>".$row['TOTAL_ACTUAL']."</td>";
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