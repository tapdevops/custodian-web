<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_vra_utilisasi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>REPORT VRA UTILISASI PER BA</div>
<div style='font-weight:bolder; font-size:20px;'>PERIODE ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>BA CODE</td>
	<td>COMPANY NAME</td>
	<td>VRA CODE</td>
	<td>VRA SUB CATEGORY</td>
	<td>VRA TYPE</td>
	<td>JUMLAH ALAT</td>
	<td>UOM</td>
	<td>TOTAL QTY/TAHUN</td>
	<td>TOTAL HM - KM SENDIRI</td>
	<td>TOTAL HM - KM PINJAM</td>
	<td>CEK SELISIH HM - KM</td>
</tr>";

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['COMPANY_NAME']."</td>";
		echo "<td>".$row['VRA_CODE']."</td>";
		echo "<td>".$row['VRA_SUB_CAT_DESCRIPTION']."</td>";
		echo "<td>".$row['TYPE']."</td>";
		echo "<td>".$row['JUMLAH_ALAT']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['TOTAL_HM_KM']."</td>";
		echo "<td>".$row['HM_KM_DIGUNAKAN_SENDIRI']."</td>";
		echo "<td>".$row['HM_KM_DIGUNAKAN_PINJAM']."</td>";
		echo "<td>".$row['SELISIH_HM_KM']."</td>";
		echo "</tr>";
	}
}

echo"
</table>
</body>
</html>
";
?>