<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=mod_review_produksi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>MODULE REVIEW PRODUKSI</div>
<div style='font-weight:bolder; font-size:12px;'>PERIODE : ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:12px;'>REGION : ".$this->data['rows'][0]['REGION_NAME']."</div>
<div style='font-weight:bolder; font-size:12px;'>COMPANY : ".$this->data['rows'][0]['COMPANY_NAME']."</div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>BUSINESS AREA</td>
	<td rowspan='2'>HA PANEN</td>
	<td rowspan='2'>POKOK PANEN</td>
	<td rowspan='2'>SPH PANEN</td>
	<td colspan='2'>YIELD PROFILE</td>
	<td colspan='2'>POTENSI</td>
	<td colspan='2'>BUDGET</td>
	<td colspan='2'>VARIANCE (%)</td>
</tr>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>YPH</td>
	<td>TON</td>
	<td>YPH</td>
	<td>TON</td>
	<td>YPH</td>
	<td>TON</td>
	<td>YPH</td>
	<td>TON</td>
</tr>
";

//data
$ha_panen = 0;
$pokok_panen = 0;
$total_yield_profile_yph = 0;
$total_yield_profile_ton = 0;
$total_potensi_yph = 0;
$total_potensi_ton = 0;
$total_budget_yph = 0;
$total_budget_ton = 0;
$total_var_yph = 0;
$total_var_ton = 0;
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['ESTATE_NAME']."</td>";
		echo "<td>".$row['HA_PANEN']."</td>";
		echo "<td>".$row['POKOK_PANEN']."</td>";
		echo "<td>".$row['SPH_PANEN']."</td>";
		echo "<td>".$row['YIELD_PROFILE_YPH']."</td>";
		echo "<td>".$row['YIELD_PROFILE_TON']."</td>";
		echo "<td>".$row['POTENSI_YPH']."</td>";
		echo "<td>".$row['POTENSI_TON']."</td>";
		echo "<td>".$row['BUDGET_YPH']."</td>";
		echo "<td>".$row['BUDGET_TON']."</td>";
		echo "<td>".$row['VAR_YPH']."</td>";
		echo "<td>".$row['VAR_TON']."</td>";
		echo "</tr>";
		
		//total
		$ha_panen += $row['HA_PANEN'];
		$pokok_panen += $row['POKOK_PANEN'];
		$total_yield_profile_yph += $row['YIELD_PROFILE_YPH'];
		$total_yield_profile_ton += $row['YIELD_PROFILE_TON'];
		$total_potensi_yph += $row['POTENSI_YPH'];
		$total_potensi_ton += $row['POTENSI_TON'];
		$total_budget_yph += $row['BUDGET_YPH'];
		$total_budget_ton += $row['BUDGET_TON'];
		$total_var_yph += $row['VAR_YPH'];
		$total_var_ton += $row['VAR_TON'];
	}
	$total_yield_profile_yph = $total_yield_profile_ton / $ha_panen;
	$total_potensi_yph = $total_potensi_ton / $ha_panen;
	$total_budget_yph = $total_budget_ton / $ha_panen;
	$total_var_yph = $total_var_ton / $ha_panen;
	//total
	$sph = ($ha_panen) ? ($pokok_panen / $ha_panen) : 0;
	echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
	echo "<td>TOTAL</td>";
	echo "<td>".$ha_panen."</td>";
	echo "<td>".$pokok_panen."</td>";
	echo "<td>".round($sph)."</td>";
	echo "<td>".round($total_yield_profile_yph,2)."</td>";
	echo "<td>".$total_yield_profile_ton."</td>";
	echo "<td>".round($total_potensi_yph,2)."</td>";
	echo "<td>".$total_potensi_ton."</td>";
	echo "<td>".round($total_budget_yph,2)."</td>";
	echo "<td>".$total_budget_ton."</td>";
	echo "<td>".round($total_var_yph,2)."</td>";
	echo "<td>".$total_var_ton."</td>";
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>