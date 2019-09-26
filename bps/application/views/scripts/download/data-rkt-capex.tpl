<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_rkt_capex_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>RKT CAPEX</div><br>
";

// HEADER TABLE
$period_budget = $this->data['rows'][0]['PERIOD_BUDGET'];
$period_before = $period_budget - 1;

echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIOD BUDGET</td>
	<td>BA CODE</td>
	<td>COMPANY NAME</td>
	<td>GROUP CAPEX</td>
	<td>DESKRIPSI GROUP CAPEX</td>
	<td>KODE ASSET</td>
	<td>DESKRIPSI ASSET</td>
	<td>DETAIL SPESIFIKASI</td>
	<td>URGENCY CAPEX</td>
	<td>HARGA</td>
	<td>UOM</td>
	<td>QTY AKTUAL $period_before</td>
	<td>QTY YEAR $period_budget</td>
	<td>QTY JAN $period_budget</td>
	<td>QTY FEB $period_budget</td>
	<td>QTY MAR $period_budget</td>
	<td>QTY APR $period_budget</td>
	<td>QTY MAY $period_budget</td>
	<td>QTY JUN $period_budget</td>
	<td>QTY JUL $period_budget</td>
	<td>QTY AUG $period_budget</td>
	<td>QTY SEP $period_budget</td>
	<td>QTY OCT $period_budget</td>
	<td>QTY NOV $period_budget</td>
	<td>QTY DEC $period_budget</td>
	<td>BIAYA TOTAL</td>
	<td>BIAYA JAN</td>
	<td>BIAYA FEB</td>
	<td>BIAYA MAR</td>
	<td>BIAYA APR</td>
	<td>BIAYA MAY</td>
	<td>BIAYA JUN</td>
	<td>BIAYA JUL</td>
	<td>BIAYA AUG</td>
	<td>BIAYA SEP</td>
	<td>BIAYA OCT</td>
	<td>BIAYA NOV</td>
	<td>BIAYA DEC</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['COMPANY_NAME']."</td>";
		echo "<td>".$row['COA_CODE']."</td>";
		echo "<td>".$row['COA_DESC']."</td>";
		echo "<td>".$row['ASSET_CODE']."</td>";
		echo "<td>".$row['ASSET_DESC']."</td>";
		echo "<td>".$row['DETAIL_SPESIFICATION']."</td>";
		echo "<td>".$row['URGENCY_CAPEX']."</td>";
		echo "<td>".$row['PRICE']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['QTY_ACTUAL']."</td>";
		echo "<td>".$row['DIS_TAHUN_BERJALAN']."</td>";
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