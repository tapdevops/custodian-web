<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_report_rkt_capex_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>REPORT RKT CAPEX</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>PERIODE BUDGET</td>
	<td rowspan='2'>BA CODE</td>
	<td rowspan='2'>KODE GROUP CAPEX</td>
	<td rowspan='2'>DESKRIPSI GROUP CAPEX</td>
	<td rowspan='2'>KODE ASET</td>
	<td rowspan='2'>DESKRIPSI ASET</td>
	<td rowspan='2'>DETAIL SPESIFIKASI</td>
	<td rowspan='2'>TOTAL BIAYA</td>
	<td colspan='12'>DISTRIBUSI BIAYA</td>
</tr>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
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
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['COA_CODE']."</td>";
		echo "<td>".$row['COA_DESC']."</td>";
		echo "<td>".$row['ASSET_CODE']."</td>";
		echo "<td>".$row['ASSET_DESC']."</td>";
		echo "<td>".$row['DETAIL_SPESIFICATION']."</td>";
		echo "<td>".$row['DIS_BIAYA_TOTAL']."</td>";
		echo "<td>".$row['DIS_BIAYA_JAN']."</td>";
		echo "<td>".$row['DIS_BIAYA_FEB']."</td>";
		echo "<td>".$row['DIS_BIAYA_MAR']."</td>";
		echo "<td>".$row['DIS_BIAYA_APR']."</td>";
		echo "<td>".$row['DIS_BIAYA_MAY']."</td>";
		echo "<td>".$row['DIS_BIAYA_JUN']."</td>";
		echo "<td>".$row['DIS_BIAYA_JUL']."</td>";
		echo "<td>".$row['DIS_BIAYA_AUG']."</td>";
		echo "<td>".$row['DIS_BIAYA_SEP']."</td>";
		echo "<td>".$row['DIS_BIAYA_OCT']."</td>";
		echo "<td>".$row['DIS_BIAYA_NOV']."</td>";
		echo "<td>".$row['DIS_BIAYA_DEC']."</td>";
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