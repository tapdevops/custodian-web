<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_rkt_vra_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>RKT VRA</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>VRA SUB CATEGORY DESCRIPTION</td>
	<td>VRA CODE</td>
	<td>SAP CODE</td>
	<td>VRA TYPE</td>
	<td>DESCRIPTION <br/> VRA TYPE</td>
	<td>JUMLAH ALAT</td>
	<td>TAHUN ALAT</td>
	<td>UOM</td>
	<td>QTY DAY</td>
	<td>DAY YEAR VRA</td>
	<td>QTY YEAR</td>
	<td>KOMPARISON DG <br/>OUTLOOK HM DAN KM</td>
	<td>TOTAL QTY TAHUN</td>
	<td>JUMLAH OPERATOR</td>
	<td>JUMLAH HELPER</td>
	<td>HARGA PAJAK</td>
	<td>QTY/SAT RENTAL</td>
	<td>HARGA RENTAL</td>
	<td>HARGA GANTI SPAREPART</td>
	<td>HARGA OVERHAUL</td>
	<td>JAM KERJA WORKSHOP</td>
	<td>HARGA SERVIS BENGKEL LUAR</td>
	<td>RP / QTY SD BULAN <br/> DIBUAT BUDGET</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['VRA_SUB_CAT_DESCRIPTION']."</td>";
		echo "<td>".$row['VRA_CODE']."</td>";
		echo "<td>".$row['INTERNAL_ORDER']."</td>";
        echo "<td>".$row['VRA_TYPE']."</td>";
        echo "<td>".$row['DESCRIPTION_VRA']."</td>";
		echo "<td>".$row['JUMLAH_ALAT']."</td>";
		echo "<td>".$row['TAHUN_ALAT']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['QTY_DAY']."</td>";
		echo "<td>".$row['DAY_YEAR_VRA']."</td>";
		echo "<td>".$row['QTY_YEAR']."</td>";
		echo "<td>".$row['KOMPARISON_OUT_HM_KM']."</td>";
		echo "<td>".$row['TOTAL_QTY_TAHUN']."</td>";
		echo "<td>".$row['JUMLAH_OPERATOR']."</td>";
		echo "<td>".$row['JUMLAH_HELPER']."</td>";
		echo "<td>".$row['RVRA1_VALUE2']."</td>";
		echo "<td>".$row['RVRA17_VALUE1']."</td>";
		echo "<td>".$row['RVRA17_VALUE2']."</td>";
		echo "<td>".$row['RVRA12_VALUE2']."</td>";
		echo "<td>".$row['RVRA16_VALUE2']."</td>";
		echo "<td>".$row['RVRA15_VALUE1']."</td>";
		echo "<td>".$row['RVRA18_VALUE2']."</td>";
		echo "<td>".$row['RP_QTY_BULAN_BUDGET']."</td>";
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