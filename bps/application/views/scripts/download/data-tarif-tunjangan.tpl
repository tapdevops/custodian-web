<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_tarif_tunjangan_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>TARIF TUNJANGAN</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>JOB CODE</td>
	<td>JOB DESC</td>
	<td>EMPLOYEE STATUS</td>
	<td>GENDER</td>
	<td>TIPE TUNJANGAN</td>
	<td>JENIS TUNJANGAN</td>
	<td>VALUE</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['JOB_CODE']."</td>";
		echo "<td>".$row['JOB_DESC']."</td>";
		echo "<td>".$row['EMPLOYEE_STATUS']."</td>";
		echo "<td>".$row['GENDER']."</td>";
		echo "<td>".$row['TUNJANGAN_TYPE']."</td>";
		echo "<td>".$row['JENIS_TUNJANGAN']."</td>";
		echo "<td>".$row['VALUE']."</td>";
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