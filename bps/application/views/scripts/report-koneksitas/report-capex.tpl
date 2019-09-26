<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_capex_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>REKAP CAPEX</div>
<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>BA CODE</td>
	<td rowspan='2'>KODE</td>
	<td rowspan='2'>JENIS CAPEX</td>
	<td colspan='13'>DISTRIBUSI BIAYA</td>
	<td colspan='13'>DISTRIBUSI QTY</td>
</tr>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>YEAR</td>
	<td>JAN</td>
	<td>FEB</td>
	<td>MAR</td>
	<td>APR</td>
	<td>MAY</td>
	<td>JUN</td>
	<td>JUL</td>
	<td>AUG</td>
	<td>SEP</td>
	<td>OCT</td>
	<td>NOV</td>
	<td>DEC</td>
	<td>YEAR</td>
	<td>JAN</td>
	<td>FEB</td>
	<td>MAR</td>
	<td>APR</td>
	<td>MAY</td>
	<td>JUN</td>
	<td>JUL</td>
	<td>AUG</td>
	<td>SEP</td>
	<td>OCT</td>
	<td>NOV</td>
	<td>DEC</td>
</tr>";

//data
$lastCoa = '';
$lastBa = '';
$dist_jan = 0 ;
$dist_feb = 0 ;
$dist_mar = 0 ;
$dist_apr = 0 ;
$dist_may = 0 ;
$dist_jun = 0 ;
$dist_jul = 0 ;
$dist_aug = 0 ;
$dist_sep = 0 ;
$dist_oct = 0 ;
$dist_nov = 0 ;
$dist_dec = 0 ;
$dist_year = 0 ;
$cost_jan = 0 ;
$cost_feb = 0 ;
$cost_mar = 0 ;
$cost_apr = 0 ;
$cost_may = 0 ;
$cost_jun = 0 ;
$cost_jul = 0 ;
$cost_aug = 0 ;
$cost_sep = 0 ;
$cost_oct = 0 ;
$cost_nov = 0 ;
$cost_dec = 0 ;
$cost_year = 0 ;
$total_dist_jan = 0 ;
$total_dist_feb = 0 ;
$total_dist_mar = 0 ;
$total_dist_apr = 0 ;
$total_dist_may = 0 ;
$total_dist_jun = 0 ;
$total_dist_jul = 0 ;
$total_dist_aug = 0 ;
$total_dist_sep = 0 ;
$total_dist_oct = 0 ;
$total_dist_nov = 0 ;
$total_dist_dec = 0 ;
$total_dist_year = 0 ;
$total_cost_jan = 0 ;
$total_cost_feb = 0 ;
$total_cost_mar = 0 ;
$total_cost_apr = 0 ;
$total_cost_may = 0 ;
$total_cost_jun = 0 ;
$total_cost_jul = 0 ;
$total_cost_aug = 0 ;
$total_cost_sep = 0 ;
$total_cost_oct = 0 ;
$total_cost_nov = 0 ;
$total_cost_dec = 0 ;
$total_cost_year = 0 ;

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		if( ($lastBa) && ($lastCoa) && ($row['BA_CODE'].$row['COA_CODE'] <> $lastBa.$lastCoa) ){
			//subtotal
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='3'>SUB TOTAL</td>";
			echo "<td>".$cost_year."</td>";
			echo "<td>".$cost_jan."</td>";
			echo "<td>".$cost_feb."</td>";
			echo "<td>".$cost_mar."</td>";
			echo "<td>".$cost_apr."</td>";
			echo "<td>".$cost_may."</td>";
			echo "<td>".$cost_jun."</td>";
			echo "<td>".$cost_jul."</td>";
			echo "<td>".$cost_aug."</td>";
			echo "<td>".$cost_sep."</td>";
			echo "<td>".$cost_oct."</td>";
			echo "<td>".$cost_nov."</td>";
			echo "<td>".$cost_dec."</td>";
			echo "<td>".$dist_year."</td>";
			echo "<td>".$dist_jan."</td>";
			echo "<td>".$dist_feb."</td>";
			echo "<td>".$dist_mar."</td>";
			echo "<td>".$dist_apr."</td>";
			echo "<td>".$dist_may."</td>";
			echo "<td>".$dist_jun."</td>";
			echo "<td>".$dist_jul."</td>";
			echo "<td>".$dist_aug."</td>";
			echo "<td>".$dist_sep."</td>";
			echo "<td>".$dist_oct."</td>";
			echo "<td>".$dist_nov."</td>";
			echo "<td>".$dist_dec."</td>";
			echo "</tr>";
			
			//reset data
			$dist_jan = 0 ;
			$dist_feb = 0 ;
			$dist_mar = 0 ;
			$dist_apr = 0 ;
			$dist_may = 0 ;
			$dist_jun = 0 ;
			$dist_jul = 0 ;
			$dist_aug = 0 ;
			$dist_sep = 0 ;
			$dist_oct = 0 ;
			$dist_nov = 0 ;
			$dist_dec = 0 ;
			$dist_year = 0 ;
			$cost_jan = 0 ;
			$cost_feb = 0 ;
			$cost_mar = 0 ;
			$cost_apr = 0 ;
			$cost_may = 0 ;
			$cost_jun = 0 ;
			$cost_jul = 0 ;
			$cost_aug = 0 ;
			$cost_sep = 0 ;
			$cost_oct = 0 ;
			$cost_nov = 0 ;
			$cost_dec = 0 ;
			$cost_year = 0 ;
		}	
		
		if(($lastBa) && ($row['BA_CODE'] <> $lastBa)){
			//total
			echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
			echo "<td colspan='3'>TOTAL</td>";
			echo "<td>".$total_cost_year."</td>";
			echo "<td>".$total_cost_jan."</td>";
			echo "<td>".$total_cost_feb."</td>";
			echo "<td>".$total_cost_mar."</td>";
			echo "<td>".$total_cost_apr."</td>";
			echo "<td>".$total_cost_may."</td>";
			echo "<td>".$total_cost_jun."</td>";
			echo "<td>".$total_cost_jul."</td>";
			echo "<td>".$total_cost_aug."</td>";
			echo "<td>".$total_cost_sep."</td>";
			echo "<td>".$total_cost_oct."</td>";
			echo "<td>".$total_cost_nov."</td>";
			echo "<td>".$total_cost_dec."</td>";
			echo "<td>".$total_dist_year."</td>";
			echo "<td>".$total_dist_jan."</td>";
			echo "<td>".$total_dist_feb."</td>";
			echo "<td>".$total_dist_mar."</td>";
			echo "<td>".$total_dist_apr."</td>";
			echo "<td>".$total_dist_may."</td>";
			echo "<td>".$total_dist_jun."</td>";
			echo "<td>".$total_dist_jul."</td>";
			echo "<td>".$total_dist_aug."</td>";
			echo "<td>".$total_dist_sep."</td>";
			echo "<td>".$total_dist_oct."</td>";
			echo "<td>".$total_dist_nov."</td>";
			echo "<td>".$total_dist_dec."</td>";
			echo "</tr>";
			
			//reset total
			$total_dist_jan = 0 ;
			$total_dist_feb = 0 ;
			$total_dist_mar = 0 ;
			$total_dist_apr = 0 ;
			$total_dist_may = 0 ;
			$total_dist_jun = 0 ;
			$total_dist_jul = 0 ;
			$total_dist_aug = 0 ;
			$total_dist_sep = 0 ;
			$total_dist_oct = 0 ;
			$total_dist_nov = 0 ;
			$total_dist_dec = 0 ;
			$total_dist_year = 0 ;
			$total_cost_jan = 0 ;
			$total_cost_feb = 0 ;
			$total_cost_mar = 0 ;
			$total_cost_apr = 0 ;
			$total_cost_may = 0 ;
			$total_cost_jun = 0 ;
			$total_cost_jul = 0 ;
			$total_cost_aug = 0 ;
			$total_cost_sep = 0 ;
			$total_cost_oct = 0 ;
			$total_cost_nov = 0 ;
			$total_cost_dec = 0 ;
			$total_cost_year = 0 ;
		}
		
		if($row['BA_CODE'].$row['COA_CODE'] <> $lastBa.$lastCoa){
			echo "<tr style='vertical-align:top; font-weight:bolder;'>";
			echo "<td>".$row['BA_CODE']."</td>";
			echo "<td>".$row['COA_CODE']."</td>";
			echo "<td colspan='27'>".$row['COA_DESC']."</td>";
			echo "</tr>";
		}
		
		echo "<tr style='vertical-align:top;'>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td valign='top'>".$row['ASSET_DESC']."</td>";
		echo "<td>".$row['DIS_BIAYA_TOTAL']."</td>";
		echo "<td>".$row['DIS_BIAYA_JAN']."</td>";
		echo "<td>".$row['DIS_BIAYA_FEB']."</td>";
		echo "<td>".$row['DIS_BIAYA_MAR']."</td>";
		echo "<td>".$row['DIS_BIAYA_APR']."</td>";
		echo "<td>".$row['DIS_BIAYA_MAY']."</td>";
		echo "<td>".$row['DIS_BIAYA_JUN']."</td>";
		echo "<td>".$row['DIS_BIAYA_JUL']."</td>";
		echo "<td>".$row['DIS_BIAYA_AUG']."</td>";
		echo "<td>".$row['DIS_BIAYA_SEP']."</td>";
		echo "<td>".$row['DIS_BIAYA_OCT']."</td>";
		echo "<td>".$row['DIS_BIAYA_NOV']."</td>";
		echo "<td>".$row['DIS_BIAYA_DEC']."</td>";
		echo "<td>".$row['DIS_TAHUN_BERJALAN']."</td>";
		echo "<td>".$row['DIS_JAN']."</td>";
		echo "<td>".$row['DIS_FEB']."</td>";
		echo "<td>".$row['DIS_MAR']."</td>";
		echo "<td>".$row['DIS_APR']."</td>";
		echo "<td>".$row['DIS_MAY']."</td>";
		echo "<td>".$row['DIS_JUN']."</td>";
		echo "<td>".$row['DIS_JUL']."</td>";
		echo "<td>".$row['DIS_AUG']."</td>";
		echo "<td>".$row['DIS_SEP']."</td>";
		echo "<td>".$row['DIS_OCT']."</td>";
		echo "<td>".$row['DIS_NOV']."</td>";
		echo "<td>".$row['DIS_DEC']."</td>";
		echo "</tr>";
		
		$lastCoa = $row['COA_CODE'];
		$lastBa = $row['BA_CODE'];
		
		//sub total
		$dist_jan += $row['DIS_JAN'] ;
		$dist_feb += $row['DIS_FEB'] ;
		$dist_mar += $row['DIS_MAR'] ;
		$dist_apr += $row['DIS_APR'] ;
		$dist_may += $row['DIS_MAY'] ;
		$dist_jun += $row['DIS_JUN'] ;
		$dist_jul += $row['DIS_JUL'] ;
		$dist_aug += $row['DIS_AUG'] ;
		$dist_sep += $row['DIS_SEP'] ;
		$dist_oct += $row['DIS_OCT'] ;
		$dist_nov += $row['DIS_NOV'] ;
		$dist_dec += $row['DIS_DEC'] ;
		$dist_year += $row['DIS_TAHUN_BERJALAN'] ;
		$cost_jan += $row['DIS_BIAYA_JAN'] ;
		$cost_feb += $row['DIS_BIAYA_FEB'] ;
		$cost_mar += $row['DIS_BIAYA_MAR'] ;
		$cost_apr += $row['DIS_BIAYA_APR'] ;
		$cost_may += $row['DIS_BIAYA_MAY'] ;
		$cost_jun += $row['DIS_BIAYA_JUN'] ;
		$cost_jul += $row['DIS_BIAYA_JUL'] ;
		$cost_aug += $row['DIS_BIAYA_AUG'] ;
		$cost_sep += $row['DIS_BIAYA_SEP'] ;
		$cost_oct += $row['DIS_BIAYA_OCT'] ;
		$cost_nov += $row['DIS_BIAYA_NOV'] ;
		$cost_dec += $row['DIS_BIAYA_DEC'] ;
		$cost_year += $row['DIS_BIAYA_TOTAL'] ;
		
		//total
		$total_dist_jan += $row['DIS_JAN'] ;
		$total_dist_feb += $row['DIS_FEB'] ;
		$total_dist_mar += $row['DIS_MAR'] ;
		$total_dist_apr += $row['DIS_APR'] ;
		$total_dist_may += $row['DIS_MAY'] ;
		$total_dist_jun += $row['DIS_JUN'] ;
		$total_dist_jul += $row['DIS_JUL'] ;
		$total_dist_aug += $row['DIS_AUG'] ;
		$total_dist_sep += $row['DIS_SEP'] ;
		$total_dist_oct += $row['DIS_OCT'] ;
		$total_dist_nov += $row['DIS_NOV'] ;
		$total_dist_dec += $row['DIS_DEC'] ;
		$total_dist_year += $row['DIS_TAHUN_BERJALAN'] ;
		$total_cost_jan += $row['DIS_BIAYA_JAN'] ;
		$total_cost_feb += $row['DIS_BIAYA_FEB'] ;
		$total_cost_mar += $row['DIS_BIAYA_MAR'] ;
		$total_cost_apr += $row['DIS_BIAYA_APR'] ;
		$total_cost_may += $row['DIS_BIAYA_MAY'] ;
		$total_cost_jun += $row['DIS_BIAYA_JUN'] ;
		$total_cost_jul += $row['DIS_BIAYA_JUL'] ;
		$total_cost_aug += $row['DIS_BIAYA_AUG'] ;
		$total_cost_sep += $row['DIS_BIAYA_SEP'] ;
		$total_cost_oct += $row['DIS_BIAYA_OCT'] ;
		$total_cost_nov += $row['DIS_BIAYA_NOV'] ;
		$total_cost_dec += $row['DIS_BIAYA_DEC'] ;
		$total_cost_year += $row['DIS_BIAYA_TOTAL'] ;
	}
	//sub total
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='3'>SUB TOTAL</td>";
	echo "<td>".$cost_year."</td>";
	echo "<td>".$cost_jan."</td>";
	echo "<td>".$cost_feb."</td>";
	echo "<td>".$cost_mar."</td>";
	echo "<td>".$cost_apr."</td>";
	echo "<td>".$cost_may."</td>";
	echo "<td>".$cost_jun."</td>";
	echo "<td>".$cost_jul."</td>";
	echo "<td>".$cost_aug."</td>";
	echo "<td>".$cost_sep."</td>";
	echo "<td>".$cost_oct."</td>";
	echo "<td>".$cost_nov."</td>";
	echo "<td>".$cost_dec."</td>";
	echo "<td>".$dist_year."</td>";
	echo "<td>".$dist_jan."</td>";
	echo "<td>".$dist_feb."</td>";
	echo "<td>".$dist_mar."</td>";
	echo "<td>".$dist_apr."</td>";
	echo "<td>".$dist_may."</td>";
	echo "<td>".$dist_jun."</td>";
	echo "<td>".$dist_jul."</td>";
	echo "<td>".$dist_aug."</td>";
	echo "<td>".$dist_sep."</td>";
	echo "<td>".$dist_oct."</td>";
	echo "<td>".$dist_nov."</td>";
	echo "<td>".$dist_dec."</td>";
	echo "</tr>";
	
	//total
	echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
	echo "<td colspan='3'>TOTAL</td>";
	echo "<td>".$total_cost_year."</td>";
	echo "<td>".$total_cost_jan."</td>";
	echo "<td>".$total_cost_feb."</td>";
	echo "<td>".$total_cost_mar."</td>";
	echo "<td>".$total_cost_apr."</td>";
	echo "<td>".$total_cost_may."</td>";
	echo "<td>".$total_cost_jun."</td>";
	echo "<td>".$total_cost_jul."</td>";
	echo "<td>".$total_cost_aug."</td>";
	echo "<td>".$total_cost_sep."</td>";
	echo "<td>".$total_cost_oct."</td>";
	echo "<td>".$total_cost_nov."</td>";
	echo "<td>".$total_cost_dec."</td>";
	echo "<td>".$total_dist_year."</td>";
	echo "<td>".$total_dist_jan."</td>";
	echo "<td>".$total_dist_feb."</td>";
	echo "<td>".$total_dist_mar."</td>";
	echo "<td>".$total_dist_apr."</td>";
	echo "<td>".$total_dist_may."</td>";
	echo "<td>".$total_dist_jun."</td>";
	echo "<td>".$total_dist_jul."</td>";
	echo "<td>".$total_dist_aug."</td>";
	echo "<td>".$total_dist_sep."</td>";
	echo "<td>".$total_dist_oct."</td>";
	echo "<td>".$total_dist_nov."</td>";
	echo "<td>".$total_dist_dec."</td>";
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>