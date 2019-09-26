<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_distribusi_vra_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>DISTRIBUSI VRA - INFRA</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>KODE VRA</td>
	<td>TIPE VRA</td>
	<td>UOM</td>";
foreach($this->data['tabs'] as $idx => $row){
	echo "<td>".$row['LOCATION_CODE']."</td>";
}	
echo "
	<td>TOTAL</td>
	<td>TOTAL COST</td>
</tr>
";

//data

	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['DESCRIPTION']."</td>";
		echo "<td>".$row['VRA_CODE']."</td>";
		echo "<td>".$row['VRA_SUB_CAT_DESCRIPTION']."</td>";
		echo "<td>".$row['UOM']."</td>";
		
		// loop to each AFDELINGS
		foreach($this->data['tabs'] as $idx => $tab){
		  if($row[$tab['LOCATION_CODE']] == '') {
				echo "<td>0</td>";
		  } else {
				echo "<td>".$row[$tab['LOCATION_CODE']]."</td>";
		  }
		}
		
		echo "<td>".$row['SUB_TOTAL']."</td>";
		echo "<td>".$row['PRICE_TOTAL']."</td>";
		echo "</tr>";
	}

echo"
</table>";

echo"
</body>
</html>
";
?>
