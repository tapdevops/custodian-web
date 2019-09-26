<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_mapping_aktivitas_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>MAPPING AKTIFITAS UNTUK PENGGUNAAN RKT</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>GRUP AKTIVITAS</td>
	<td>TIPE GRUP AKTIVITAS</td>
	<td>KODE RKT</td>
	<td>DESKRIPSI RKT</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_DESC']."</td>";
		echo "<td>".$row['ACTIVITY_GROUP_TYPE_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_GROUP_TYPE']."</td>";
		echo "<td>".$row['UI_RKT_CODE']."</td>";
		echo "<td>".$row['UI_RKT']."</td>";
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