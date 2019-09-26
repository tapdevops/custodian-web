<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_development_cost_afd_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>DEVELOPMENT COST Per AFD</div>
<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>BA CODE</td>
	<td rowspan='2'>BA NAME</td>
	<td rowspan='2'>AFD CODE</td>";

//cari jumlah group report	
for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
	echo "<td rowspan='2'>GROUP".str_pad($i,2,'0',STR_PAD_LEFT)."</td>";
}

echo "
	<td rowspan='2'>KODE</td>
	<td rowspan='2'>AKTIVITAS</td>
	<td rowspan='2'>COST ELEMENT</td>
	<td rowspan='2'>KETERANGAN</td>
	<td colspan='13'>DISTRIBUSI BIAYA</td>
	<td rowspan='2'>SATUAN</td>
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
$lastGroup = $lastActivity = $lastBa = $lastTipeTransaksi = $lastAfd = '';
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
$colspan = 7 + (int)($this->data['max_group']);

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		//current group combination
		$curGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$curGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
		
		//total per tipe
		if( ($lastBa) && ($lastTipeTransaksi) &&($row['BA_CODE'].$row['GROUP01'].$row['AFD_CODE'] <> $lastBa.$lastTipeTransaksi.$lastAfd) ){
			//sub total
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='$colspan'>SUB TOTAL</td>";
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
			echo "<td>&nbsp;</td>";
			echo "<td>".$qty_year."</td>";
			echo "<td>".$qty_jan."</td>";
			echo "<td>".$qty_feb."</td>";
			echo "<td>".$qty_mar."</td>";
			echo "<td>".$qty_apr."</td>";
			echo "<td>".$qty_may."</td>";
			echo "<td>".$qty_jun."</td>";
			echo "<td>".$qty_jul."</td>";
			echo "<td>".$qty_aug."</td>";
			echo "<td>".$qty_sep."</td>";
			echo "<td>".$qty_oct."</td>";
			echo "<td>".$qty_nov."</td>";
			echo "<td>".$qty_dec."</td>";
			echo "</tr>";
			
			//reset sub total
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
		}
		
		//total per BA
		if(($lastBa) && ($row['BA_CODE'] <> $lastBa)){
			//total
			echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
			echo "<td colspan='$colspan'>TOTAL</td>";
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
			echo "<td>&nbsp;</td>";
			echo "<td>".$total_qty_year."</td>";
			echo "<td>".$total_qty_jan."</td>";
			echo "<td>".$total_qty_feb."</td>";
			echo "<td>".$total_qty_mar."</td>";
			echo "<td>".$total_qty_apr."</td>";
			echo "<td>".$total_qty_may."</td>";
			echo "<td>".$total_qty_jun."</td>";
			echo "<td>".$total_qty_jul."</td>";
			echo "<td>".$total_qty_aug."</td>";
			echo "<td>".$total_qty_sep."</td>";
			echo "<td>".$total_qty_oct."</td>";
			echo "<td>".$total_qty_nov."</td>";
			echo "<td>".$total_qty_dec."</td>";
			echo "</tr>";
			
			//reset total
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
		}
	
		echo "<tr style='vertical-align:top;'>";
		
			if($row['BA_CODE'].$row['GROUP01'].$row['AFD_CODE'] <> $lastBa.$lastTipeTransaksi.$lastAfd){
				echo "<td><b>".$row['BA_CODE']."</b></td>";
				echo "<td><b>".$row['ESTATE_NAME']."</b></td>";
				echo "<td><b>".$row['AFD_CODE']."</b></td>";
			}else{
				echo "<td>&nbsp;</td>";
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
		
		echo "<td>".$row['COST_ELEMENT']."</td>";
		echo "<td>".$row['KETERANGAN']."</td>";
		echo "<td>".$row['COST_SETAHUN']."</td>";
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
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['QTY_SETAHUN']."</td>";
		echo "<td>".$row['QTY_JAN']."</td>";
		echo "<td>".$row['QTY_FEB']."</td>";
		echo "<td>".$row['QTY_MAR']."</td>";
		echo "<td>".$row['QTY_APR']."</td>";
		echo "<td>".$row['QTY_MAY']."</td>";
		echo "<td>".$row['QTY_JUN']."</td>";
		echo "<td>".$row['QTY_JUL']."</td>";
		echo "<td>".$row['QTY_AUG']."</td>";
		echo "<td>".$row['QTY_SEP']."</td>";
		echo "<td>".$row['QTY_OCT']."</td>";
		echo "<td>".$row['QTY_NOV']."</td>";
		echo "<td>".$row['QTY_DEC']."</td>";
		echo "</tr>";
		
		$lastBa = $row['BA_CODE'];
		$lastAfd = $row['AFD_CODE'];
		$lastTipeTransaksi = $row['GROUP01'];
		$lastActivity = $row['ACTIVITY_CODE'];
		$lastGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$lastGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
		
		
		//subtotal
		$qty_jan += $row['QTY_JAN'];
		$qty_feb += $row['QTY_FEB'];
		$qty_mar += $row['QTY_MAR'];
		$qty_apr += $row['QTY_APR'];
		$qty_may += $row['QTY_MAY'];
		$qty_jun += $row['QTY_JUN'];
		$qty_jul += $row['QTY_JUL'];
		$qty_aug += $row['QTY_AUG'];
		$qty_sep += $row['QTY_SEP'];
		$qty_oct += $row['QTY_OCT'];
		$qty_nov += $row['QTY_NOV'];
		$qty_dec += $row['QTY_DEC'];
		$qty_year += $row['QTY_SETAHUN'];
		$cost_jan += $row['COST_JAN'];
		$cost_feb += $row['COST_FEB'];
		$cost_mar += $row['COST_MAR'];
		$cost_apr += $row['COST_APR'];
		$cost_may += $row['COST_MAY'];
		$cost_jun += $row['COST_JUN'];
		$cost_jul += $row['COST_JUL'];
		$cost_aug += $row['COST_AUG'];
		$cost_sep += $row['COST_SEP'];
		$cost_oct += $row['COST_OCT'];
		$cost_nov += $row['COST_NOV'];
		$cost_dec += $row['COST_DEC'];
		$cost_year += $row['COST_SETAHUN'];
		
		//total per BA
		$total_qty_jan += $row['QTY_JAN'];
		$total_qty_feb += $row['QTY_FEB'];
		$total_qty_mar += $row['QTY_MAR'];
		$total_qty_apr += $row['QTY_APR'];
		$total_qty_may += $row['QTY_MAY'];
		$total_qty_jun += $row['QTY_JUN'];
		$total_qty_jul += $row['QTY_JUL'];
		$total_qty_aug += $row['QTY_AUG'];
		$total_qty_sep += $row['QTY_SEP'];
		$total_qty_oct += $row['QTY_OCT'];
		$total_qty_nov += $row['QTY_NOV'];
		$total_qty_dec += $row['QTY_DEC'];
		$total_qty_year += $row['QTY_SETAHUN'];
		$total_cost_jan += $row['COST_JAN'];
		$total_cost_feb += $row['COST_FEB'];
		$total_cost_mar += $row['COST_MAR'];
		$total_cost_apr += $row['COST_APR'];
		$total_cost_may += $row['COST_MAY'];
		$total_cost_jun += $row['COST_JUN'];
		$total_cost_jul += $row['COST_JUL'];
		$total_cost_aug += $row['COST_AUG'];
		$total_cost_sep += $row['COST_SEP'];
		$total_cost_oct += $row['COST_OCT'];
		$total_cost_nov += $row['COST_NOV'];
		$total_cost_dec += $row['COST_DEC'];
		$total_cost_year += $row['COST_SETAHUN'];
	}
	//sub total
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='$colspan'>SUB TOTAL</td>";
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
	echo "<td>&nbsp;</td>";
	echo "<td>".$qty_year."</td>";
	echo "<td>".$qty_jan."</td>";
	echo "<td>".$qty_feb."</td>";
	echo "<td>".$qty_mar."</td>";
	echo "<td>".$qty_apr."</td>";
	echo "<td>".$qty_may."</td>";
	echo "<td>".$qty_jun."</td>";
	echo "<td>".$qty_jul."</td>";
	echo "<td>".$qty_aug."</td>";
	echo "<td>".$qty_sep."</td>";
	echo "<td>".$qty_oct."</td>";
	echo "<td>".$qty_nov."</td>";
	echo "<td>".$qty_dec."</td>";
	echo "</tr>";
	
	//total
	echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
	echo "<td colspan='$colspan'>TOTAL</td>";
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
	echo "<td>&nbsp;</td>";
	echo "<td>".$total_qty_year."</td>";
	echo "<td>".$total_qty_jan."</td>";
	echo "<td>".$total_qty_feb."</td>";
	echo "<td>".$total_qty_mar."</td>";
	echo "<td>".$total_qty_apr."</td>";
	echo "<td>".$total_qty_may."</td>";
	echo "<td>".$total_qty_jun."</td>";
	echo "<td>".$total_qty_jul."</td>";
	echo "<td>".$total_qty_aug."</td>";
	echo "<td>".$total_qty_sep."</td>";
	echo "<td>".$total_qty_oct."</td>";
	echo "<td>".$total_qty_nov."</td>";
	echo "<td>".$total_qty_dec."</td>";
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>