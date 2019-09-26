<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_hk_vs_mpp_ba".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>LAPORAN HK VS MPP (LC, RAWAT & PANEN) ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>".$this->data['BA']."</div>
<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>BA CODE</td>
	<td>JENIS GRUP</td>
	<td>HK</td>
	<td>MPP</td>
	<td>SELISIH</td>
</tr>";

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['JOB_DESCRIPTION']."</td>";
		echo "<td>".$row['HHK']."</td>";
		echo "<td>".$row['MPP']."</td>";
		echo "<td>".$row['SELISIH']."</td>";
		echo "</tr>";
		
		$tot_hk = $tot_hk + $row['HHK'];
		$tot_mpp = $tot_mpp + $row['MPP'];
		$tot_selisih = $tot_selisih + $row['SELISIH'];
	}
	
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='2'>TOTAL</td>";
	echo "<td>".$tot_hk."</td>";
	echo "<td>".$tot_mpp."</td>";
	echo "<td>".$tot_selisih."</td>";
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>