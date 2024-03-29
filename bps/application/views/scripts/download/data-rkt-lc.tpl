<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_rkt_lc_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>RKT LC</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>AFD CODE</td>
	<td>LAND TYPE</td>
	<td>TOPOGRAPHY</td>
	<td>SUMBER BIAYA</td>
	<td>ACTIVITY CLASS</td>
	<td>PLAN JAN</td>
	<td>PLAN FEB</td>
	<td>PLAN MAR</td>
	<td>PLAN APR</td>
	<td>PLAN MAY</td>
	<td>PLAN JUN</td>
	<td>PLAN JUL</td>
	<td>PLAN AUG</td>
	<td>PLAN SEP</td>
	<td>PLAN OCT</td>
	<td>PLAN NOV</td>
	<td>PLAN DEC</td>
	<td>PLAN SETAHUN</td>
	<td>TOTAL RP/QTY</td>
	<td>COST JAN</td>
	<td>COST FEB</td>
	<td>COST MAR</td>
	<td>COST APR</td>
	<td>COST MAY</td>
	<td>COST JUN</td>
	<td>COST JUL</td>
	<td>COST AUG</td>
	<td>COST SEP</td>
	<td>COST OCT</td>
	<td>COST NOV</td>
	<td>COST DEC</td>
	<td>TOTAL RP/SETAHUN</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_DESC']."</td>";
		echo "<td>".$row['AFD_CODE']."</td>";
		echo "<td>".$row['LAND_TYPE_DESC']."</td>";
		echo "<td>".$row['TOPOGRAPHY_DESC']."</td>";
		echo "<td>".$row['SUMBER_BIAYA']."</td>";
		echo "<td>".$row['ACTIVITY_CLASS']."</td>";
		echo "<td>".$row['PLAN_JAN']."</td>";
		echo "<td>".$row['PLAN_FEB']."</td>";
		echo "<td>".$row['PLAN_MAR']."</td>";
		echo "<td>".$row['PLAN_APR']."</td>";
		echo "<td>".$row['PLAN_MAY']."</td>";
		echo "<td>".$row['PLAN_JUN']."</td>";
		echo "<td>".$row['PLAN_JUL']."</td>";
		echo "<td>".$row['PLAN_AUG']."</td>";
		echo "<td>".$row['PLAN_SEP']."</td>";
		echo "<td>".$row['PLAN_OCT']."</td>";
		echo "<td>".$row['PLAN_NOV']."</td>";
		echo "<td>".$row['PLAN_DEC']."</td>";
		echo "<td>".$row['PLAN_SETAHUN']."</td>";
		echo "<td>".$row['TOTALRPQTY']."</td>";
		echo "<td>".$row['COST_JAN']."</td>";
		echo "<td>".$row['COST_FEB']."</td>";
		echo "<td>".$row['COST_MAR']."</td>";
		echo "<td>".$row['COST_APR']."</td>";
		echo "<td>".$row['COST_MAY']."</td>";
		echo "<td>".$row['COST_JUN']."</td>";
		echo "<td>".$row['COST_JUL']."</td>";
		echo "<td>".$row['COST_AUG']."</td>";
		echo "<td>".$row['COST_SEP']."</td>";
		echo "<td>".$row['COST_OCT']."</td>";
		echo "<td>".$row['COST_NOV']."</td>";
		echo "<td>".$row['COST_DEC']."</td>";
		echo "<td>".$row['COST_SETAHUN']."</td>";
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