<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=reportHK_development_cost_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>REPORT HK ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>DEVELOPMENT COST</div>
<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>BA CODE</td>
	<td rowspan='2'>BA NAME</td>";

//cari jumlah group report	
for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
	echo "<td rowspan='2'>GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."</td>";
}

echo "
	<td rowspan='2'>KODE</td>
	<td rowspan='2'>AKTIVITAS</td>
	<td colspan='12'>HK</td>
	<td rowspan='2'>HK SETAHUN</td>
	<td rowspan='2'>MPP SETAHUN</td>
	
</tr>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
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
$lastGroup = $lastActivity = $lastBa = $lastTipeTransaksi = '';
$qty_jan = 0 ;
$qty_feb = 0 ;
$qty_mar = 0 ;
$qty_apr = 0 ;
$qty_may = 0 ;
$qty_jun = 0 ;
$qty_jul = 0 ;
$qty_aug = 0 ;
$qty_sep = 0 ;
$qty_oct = 0 ;
$qty_nov = 0 ;
$qty_dec = 0 ;
$qty_year = 0 ;
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
$total_qty_jan = 0 ;
$total_qty_feb = 0 ;
$total_qty_mar = 0 ;
$total_qty_apr = 0 ;
$total_qty_may = 0 ;
$total_qty_jun = 0 ;
$total_qty_jul = 0 ;
$total_qty_aug = 0 ;
$total_qty_sep = 0 ;
$total_qty_oct = 0 ;
$total_qty_nov = 0 ;
$total_qty_dec = 0 ;
$total_qty_year = 0 ;
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
$Rp_hk = 0;
$colspan = 4 + (int)($this->data['max_group']);

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		if($row['COST_ELEMENT']=="LABOUR"){
			$Rp_hk = $row['RP_HK'];
			$hke = $row['HKE'];
			
		//current group combination
		$curGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$curGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
		
		//total per tipe
		if( ($lastBa) && ($lastTipeTransaksi) &&($row['BA_CODE'].$row['GROUP01'] <> $lastBa.$lastTipeTransaksi) ){
			//sub total
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='$colspan'>SUB TOTAL</td>";
			echo "<td>".round($cost_jan,2)."</td>";
			echo "<td>".round($cost_feb,2)."</td>";
			echo "<td>".round($cost_mar,2)."</td>";
			echo "<td>".round($cost_apr,2)."</td>";
			echo "<td>".round($cost_may,2)."</td>";
			echo "<td>".round($cost_jun,2)."</td>";
			echo "<td>".round($cost_jul,2)."</td>";
			echo "<td>".round($cost_aug,2)."</td>";
			echo "<td>".round($cost_sep,2)."</td>";
			echo "<td>".round($cost_oct,2)."</td>";
			echo "<td>".round($cost_nov,2)."</td>";
			echo "<td>".round($cost_dec,2)."</td>";
			echo "<td>".round($cost_year,2)."</td>";
			echo "<td>".round($cost_mpp,2)."</td>";
			echo "</tr>";
			
			//reset sub total
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
			$cost_mpp = 0 ;
		}
		
		//total per BA
		if(($lastBa) && ($row['BA_CODE'] <> $lastBa)){
			//total
			echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
			echo "<td colspan='$colspan'>TOTAL</td>";
			echo "<td>".round($total_cost_jan,2)."</td>";
			echo "<td>".round($total_cost_feb,2)."</td>";
			echo "<td>".round($total_cost_mar,2)."</td>";
			echo "<td>".round($total_cost_apr,2)."</td>";
			echo "<td>".round($total_cost_may,2)."</td>";
			echo "<td>".round($total_cost_jun,2)."</td>";
			echo "<td>".round($total_cost_jul,2)."</td>";
			echo "<td>".round($total_cost_aug,2)."</td>";
			echo "<td>".round($total_cost_sep,2)."</td>";
			echo "<td>".round($total_cost_oct,2)."</td>";
			echo "<td>".round($total_cost_nov,2)."</td>";
			echo "<td>".round($total_cost_dec,2)."</td>";
			echo "<td>".round($total_cost_year,2)."</td>";
			echo "<td>".round($total_cost_mpp,2)."</td>";
			echo "</tr>";
			
			//reset total
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
			$total_cost_mpp = 0 ;
		}
	
		echo "<tr style='vertical-align:top;'>";
		
			if($row['BA_CODE'].$row['GROUP01'] <> $lastBa.$lastTipeTransaksi){
				echo "<td><b>".$row['BA_CODE']."</b></td>";
				echo "<td><b>".$row['ESTATE_NAME']."</b></td>";
			}else{
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
			}
			
			if($row['BA_CODE'].$curGroup <> $lastBa.$lastGroup){
				for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
					echo "<td><b>".$row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT).'_DESC']."</b></td>";
				}
			}else{
				for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
					echo "<td>&nbsp;</td>";
				}
			}
			
			if($row['BA_CODE'].$curGroup.$row['ACTIVITY_CODE'] <> $lastBa.$lastGroup.$lastActivity){
				echo "<td><b>".$row['ACTIVITY_CODE']."</b></td>";
				echo "<td><b>".$row['ACTIVITY_DESC']."</b></td>";
			}else{
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
			}
		
		//echo "<td>".$row['COST_ELEMENT']."</td>";
		//echo "<td>".$row['KETERANGAN']."</td>";
		$cost_setahun = ($row['COST_JAN']/$Rp_hk) + ($row['COST_FEB']/$Rp_hk) + ($row['COST_MAR']/$Rp_hk) + 
		($row['COST_APR']/$Rp_hk) + ($row['COST_MAY']/$Rp_hk) + ($row['COST_JUN']/$Rp_hk) + ($row['COST_JUL']/$Rp_hk) + 
		($row['COST_AUG']/$Rp_hk) + ($row['COST_SEP']/$Rp_hk) + ($row['COST_OCT']/$Rp_hk) + ($row['COST_NOV']/$Rp_hk) + ($row['COST_DEC']/$Rp_hk);
		echo "<td>".round($row['COST_JAN']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_FEB']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_MAR']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_APR']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_MAY']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_JUN']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_JUL']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_AUG']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_SEP']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_OCT']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_NOV']/$Rp_hk,2)."</td>";
		echo "<td>".round($row['COST_DEC']/$Rp_hk,2)."</td>";
		echo "<td>".round($cost_setahun,2)."</td>";
		echo "<td>".number_format($cost_setahun/$hke,2)."</td>";
		echo "</tr>";
		
		$lastBa = $row['BA_CODE'];
		$lastTipeTransaksi = $row['GROUP01'];
		$lastActivity = $row['ACTIVITY_CODE'];
		$lastGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$lastGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
		
		
		//subtotal
		$cost_jan += $row['COST_JAN']/$Rp_hk;
		$cost_feb += $row['COST_FEB']/$Rp_hk;
		$cost_mar += $row['COST_MAR']/$Rp_hk;
		$cost_apr += $row['COST_APR']/$Rp_hk;
		$cost_may += $row['COST_MAY']/$Rp_hk;
		$cost_jun += $row['COST_JUN']/$Rp_hk;
		$cost_jul += $row['COST_JUL']/$Rp_hk;
		$cost_aug += $row['COST_AUG']/$Rp_hk;
		$cost_sep += $row['COST_SEP']/$Rp_hk;
		$cost_oct += $row['COST_OCT']/$Rp_hk;
		$cost_nov += $row['COST_NOV']/$Rp_hk;
		$cost_dec += $row['COST_DEC']/$Rp_hk;
		$cost_year += $cost_setahun;
		$cost_mpp += $cost_setahun/$hke;
		
		//total per BA
		$total_cost_jan += $row['COST_JAN']/$Rp_hk;
		$total_cost_feb += $row['COST_FEB']/$Rp_hk;
		$total_cost_mar += $row['COST_MAR']/$Rp_hk;
		$total_cost_apr += $row['COST_APR']/$Rp_hk;
		$total_cost_may += $row['COST_MAY']/$Rp_hk;
		$total_cost_jun += $row['COST_JUN']/$Rp_hk;
		$total_cost_jul += $row['COST_JUL']/$Rp_hk;
		$total_cost_aug += $row['COST_AUG']/$Rp_hk;
		$total_cost_sep += $row['COST_SEP']/$Rp_hk;
		$total_cost_oct += $row['COST_OCT']/$Rp_hk;
		$total_cost_nov += $row['COST_NOV']/$Rp_hk;
		$total_cost_dec += $row['COST_DEC']/$Rp_hk;
		$total_cost_year += $cost_setahun;
		$total_cost_mpp += $cost_setahun/$hke;
		}
	}
	//sub total
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='$colspan'>SUB TOTAL</td>";
	echo "<td>".round($cost_jan,2)."</td>";
	echo "<td>".round($cost_feb,2)."</td>";
	echo "<td>".round($cost_mar,2)."</td>";
	echo "<td>".round($cost_apr,2)."</td>";
	echo "<td>".round($cost_may,2)."</td>";
	echo "<td>".round($cost_jun,2)."</td>";
	echo "<td>".round($cost_jul,2)."</td>";
	echo "<td>".round($cost_aug,2)."</td>";
	echo "<td>".round($cost_sep,2)."</td>";
	echo "<td>".round($cost_oct,2)."</td>";
	echo "<td>".round($cost_nov,2)."</td>";
	echo "<td>".round($cost_dec,2)."</td>";
	echo "<td>".round($cost_year,2)."</td>";
	echo "<td>".round($cost_mpp,2)."</td>";
	echo "</tr>";
	
	//total
	echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
	echo "<td colspan='$colspan'>TOTAL</td>";
	echo "<td>".round($total_cost_jan,2)."</td>";
	echo "<td>".round($total_cost_feb,2)."</td>";
	echo "<td>".round($total_cost_mar,2)."</td>";
	echo "<td>".round($total_cost_apr,2)."</td>";
	echo "<td>".round($total_cost_may,2)."</td>";
	echo "<td>".round($total_cost_jun,2)."</td>";
	echo "<td>".round($total_cost_jul,2)."</td>";
	echo "<td>".round($total_cost_aug,2)."</td>";
	echo "<td>".round($total_cost_sep,2)."</td>";
	echo "<td>".round($total_cost_oct,2)."</td>";
	echo "<td>".round($total_cost_nov,2)."</td>";
	echo "<td>".round($total_cost_dec,2)."</td>";
	echo "<td>".round($total_cost_year,2)."</td>";
	echo "<td>".round($total_cost_mpp,2)."</td>";
	//echo "<td>&nbsp;</td>";
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>