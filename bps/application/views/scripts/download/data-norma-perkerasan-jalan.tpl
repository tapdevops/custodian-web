<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_perkerasan_jalan_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA PERKERASAN JALAN</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>ACTIVITY_CODE</td>
	<td>DESCRIPTION</td>
	<td>LEBAR</td>
	<td>PANJANG</td>
	<td>TEBAL</td>
	<td>VOLUME MATERIAL</td>
	<td>PRICE</td>
	<td>KODE VRA DT</td>
	<td>RP/KM DT</td>
	<td>KAPASITAS DT</td>
	<td>KECEPATAN DT</td>
	<td>JAM KERJA DT</td>
	<td>KODE VRA EXCAV</td>
	<td>RP/HM EXCAV</td>
	<td>KAPASITAS EXCAV</td>
	<td>KODE VRA COMPACTOR</td>
	<td>RP/HM COMPACTOR</td>
	<td>KAPASITAS COMPACTOR</td>
	<td>KODE VRA GRADER</td>
	<td>RP/HM GRADER</td>
	<td>KAPASITAS GRADER</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['DESCRIPTION']."</td>";
		echo "<td>".$row['LEBAR']."</td>";
		echo "<td>".$row['PANJANG']."</td>";
		echo "<td>".$row['TEBAL']."</td>";
		echo "<td>".$row['VOLUME_MATERIAL']."</td>";
		echo "<td>".$row['PRICE']."</td>";
		echo "<td>".$row['VRA_CODE_DT']."</td>";
		echo "<td>".$row['RP_KM_DT']."</td>";
		echo "<td>".$row['KAPASITAS_DT']."</td>";
		echo "<td>".$row['KECEPATAN_DT']."</td>";
		echo "<td>".$row['JAM_KERJA_DT']."</td>";
		echo "<td>".$row['VRA_CODE_EXCAV']."</td>";
		echo "<td>".$row['RP_HM_EXCAV']."</td>";
		echo "<td>".$row['KAPASITAS_EXCAV']."</td>";
		echo "<td>".$row['VRA_CODE_COMPACTOR']."</td>";
		echo "<td>".$row['RP_HM_COMP']."</td>";
		echo "<td>".$row['KAPASITAS_COMPACTOR']."</td>";
		echo "<td>".$row['VRA_CODE_GRADER']."</td>";
		echo "<td>".$row['RP_HM_GRADER']."</td>";
		echo "<td>".$row['KAPASITAS_GRADER']."</td>";
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