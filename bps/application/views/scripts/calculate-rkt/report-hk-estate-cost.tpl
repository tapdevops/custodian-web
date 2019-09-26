<?php
//header("Content-type: application/vnd.ms-excel");
//header("Content-Disposition: attachment;Filename=report_estate_cost_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>ESTATE COST</div>
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
	<td rowspan='2'>COST ELEMENT</td>
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
$colspan = 5 + (int)($this->data['max_group']);

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
	$Rp_hk = $row['RP_HK'];
	$hke = $row['HKE'];
			
		//current group combination
		$curGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$curGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
		
		//total per tipe
		if( ($lastBa) && ($lastTipeTransaksi) &&($row['BA_CODE'].$row['GROUP01'] <> $lastBa.$lastTipeTransaksi) && ($lastActivity <> 'HA_TM')){
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
			
			if($row['BA_CODE'].$curGroup.$row['ACTIVITY_CODE'].$row['ACTIVITY_DESC'] <> $lastBa.$lastGroup.$lastActivity){
				echo "<td><b>".$row['ACTIVITY_CODE']."</b></td>";
				echo "<td><b>".$row['ACTIVITY_DESC']."</b></td>";
			}else{
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
			}
		if($row['ACTIVITY_CODE']=="5101030101" && $row['ACTIVITY_DESC']=="BIAYA PEMANEN"){
			$acost_jan = round(($row['PEREN_COST_JAN']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_feb = round(($row['PEREN_COST_FEB']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_mar = round(($row['PEREN_COST_MAR']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_apr = round(($row['PEREN_COST_APR']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_may = round(($row['PEREN_COST_MAY']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_jun = round(($row['PEREN_COST_JUN']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_jul = round(($row['PEREN_COST_JUL']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_aug = round(($row['PEREN_COST_AUG']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_sep = round(($row['PEREN_COST_SEP']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_oct = round(($row['PEREN_COST_OCT']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_nov = round(($row['PEREN_COST_NOV']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
			$acost_dec = round(($row['PEREN_COST_DEC']/$row['TOTAL_COST_PERTAHUN'])*$row['HK_PANEN'],2);
		}else{
			$acost_jan = round($row['COST_JAN']/$Rp_hk,2);
			$acost_feb = round($row['COST_FEB']/$Rp_hk,2);
			$acost_mar = round($row['COST_MAR']/$Rp_hk,2);
			$acost_apr = round($row['COST_APR']/$Rp_hk,2);
			$acost_may = round($row['COST_MAY']/$Rp_hk,2);
			$acost_jun = round($row['COST_JUN']/$Rp_hk,2);
			$acost_jul = round($row['COST_JUL']/$Rp_hk,2);
			$acost_aug = round($row['COST_AUG']/$Rp_hk,2);
			$acost_sep = round($row['COST_SEP']/$Rp_hk,2);
			$acost_oct = round($row['COST_OCT']/$Rp_hk,2);
			$acost_nov = round($row['COST_NOV']/$Rp_hk,2);
			$acost_dec = round($row['COST_DEC']/$Rp_hk,2);
		}
		
		$cost_setahun = ($acost_jan) + ($acost_feb) + ($acost_mar) + ($acost_apr) + ($acost_may) + ($acost_jun) + ($acost_jul) + 
		($acost_aug) + ($acost_sep) + ($acost_oct) + ($acost_nov) + ($acost_dec);
		echo "<td>".$row['COST_ELEMENT']."</td>";
		echo "<td>".$acost_jan."</td>";
		echo "<td>".$acost_feb."</td>";
		echo "<td>".$acost_mar."</td>";
		echo "<td>".$acost_apr."</td>";
		echo "<td>".$acost_may."</td>";
		echo "<td>".$acost_jun."</td>";
		echo "<td>".$acost_jul."</td>";
		echo "<td>".$acost_aug."</td>";
		echo "<td>".$acost_sep."</td>";
		echo "<td>".$acost_oct."</td>";
		echo "<td>".$acost_nov."</td>";
		echo "<td>".$acost_dec."</td>";
		echo "<td>".round($cost_setahun,2)."</td>";
		echo "<td>".number_format($cost_setahun/$hke,2)."</td>";
		echo "</tr>";
		
		$lastBa = $row['BA_CODE'];
		$lastTipeTransaksi = $row['GROUP01'];
		$lastActivity = $row['ACTIVITY_CODE'].$row['ACTIVITY_DESC'];
		$lastGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$lastGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
		
		if($row['ACTIVITY_CODE'] <> 'HA_TM') {
			//subtotal
		$cost_jan += $acost_jan;
		$cost_feb += $acost_feb;
		$cost_mar += $acost_mar;
		$cost_apr += $acost_apr;
		$cost_may += $acost_may;
		$cost_jun += $acost_jun;
		$cost_jul += $acost_jul;
		$cost_aug += $acost_aug;
		$cost_sep += $acost_sep;
		$cost_oct += $acost_oct;
		$cost_nov += $acost_nov;
		$cost_dec += $acost_dec;
		$cost_year += $cost_setahun;
		$cost_mpp += $cost_setahun/$hke;
		
		//total per BA
		$total_cost_jan += $acost_jan;
		$total_cost_feb += $acost_feb;
		$total_cost_mar += $acost_mar;
		$total_cost_apr += $acost_apr;
		$total_cost_may += $acost_may;
		$total_cost_jun += $acost_jun;
		$total_cost_jul += $acost_jul;
		$total_cost_aug += $acost_aug;
		$total_cost_sep += $acost_sep;
		$total_cost_oct += $acost_oct;
		$total_cost_nov += $acost_nov;
		$total_cost_dec += $dec;
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