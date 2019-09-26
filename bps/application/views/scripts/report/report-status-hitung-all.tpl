<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_status_hitung_all_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>REPORT STATUS HITUNG ALL ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>BUSINESS AREA : " . $this->data['rows'][0]['BA_CODE'] . "</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td></td>
	<td></td>
	<td>KODE</td>
	<td>AKTIVITAS</td>
	<td>LAST UPDATE</td>
	<td>ADA / TIDAK ADA COST</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['GROUP01']."</td>";
		echo "<td>".$row['GROUP02']."</td>";
		echo "<td>".$row['CODE']."</td>";
		echo "<td>".$row['AKTIVITAS']."</td>";
		echo "<td>".$row['LAST_UPDATE']."</td>";
		echo "<td>".$row['STATUS']."</td>";
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