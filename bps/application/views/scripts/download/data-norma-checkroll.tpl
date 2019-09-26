<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_checkroll_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>RKT CHECKROLL (MPP)</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>JOB CODE</td>
	<td>DESKRIPSI GRUP CHECKROLL</td>
	<td>JOB DESCRIPTION</td>
	<td>STATUS KARYAWAN</td>
	<td>GAJI POKOK</td>
	<td>MPP AKTUAL</td>
	<td>MPP PERIODE BUDGET</td>
	<td>REKRUT</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['JOB_CODE']."</td>";
		echo "<td>".$row['GROUP_CHECKROLL_DESC']."</td>";
		echo "<td>".$row['JOB_DESCRIPTION']."</td>";
		echo "<td>".$row['EMPLOYEE_STATUS']."</td>";
		echo "<td>".$row['GP']."</td>";
		echo "<td>".$row['MPP_AKTUAL']."</td>";
		echo "<td>".$row['MPP_PERIOD_BUDGET']."</td>";
		echo "<td>".$row['MPP_REKRUT']."</td>";
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