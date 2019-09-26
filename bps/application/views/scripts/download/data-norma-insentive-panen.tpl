<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_insentive_panen_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>NORMA INSENTIVE PANEN</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
  <td>PERCENTAGE_INCENTIVE_1</td>
  <td>INCENTIVE_1</td>
  <td>PERCENTAGE_INCENTIVE_2</td>
  <td>INCENTIVE_2</td>
  <td>PERCENTAGE_INCENTIVE_3</td>
  <td>INCENTIVE_3</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['PERCENTAGE_INCENTIVE_1']."</td>";
		echo "<td>".$row['INCENTIVE_1']."</td>";
		echo "<td>".$row['PERCENTAGE_INCENTIVE_2']."</td>";
		echo "<td>".$row['INCENTIVE_2']."</td>";
		echo "<td>".$row['PERCENTAGE_INCENTIVE_3']."</td>";
		echo "<td>".$row['INCENTIVE_3']."</td>";
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
