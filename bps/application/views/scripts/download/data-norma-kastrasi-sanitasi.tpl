<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_kastrasi_sanitasi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA KASTRASI SANITASI</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>KELAS TANAH</td>
	<td>UMUR TANAM (BULAN)</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_DESC']."</td>";
		echo "<td>".$row['LAND_SUITABILITY']."</td>";
		echo "<td>".$row['UMUR']."</td>";
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