<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_sum_development_cost_afd_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>SUMMARY DEVELOPMENT COST per AFD</div>
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
	<td colspan='2'>HA TANAM</td>
	<td colspan='3'>TOTAL BIAYA</td>
	<td colspan='3'>RP/HA</td>
</tr>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>SMS 1</td>
	<td>SMS 2</td>
	<td>SMS 1</td>
	<td>SMS 2</td>
	<td>TOTAL</td>
	<td>SMS 1</td>
	<td>SMS 2</td>
	<td>TOTAL</td>
</tr>";

//data
$lastGroup = $lastActivity = $lastBa = $lastAfd = $lastTipeTransaksi = '';
$qty_sms1 = 0 ;
$qty_sms2 = 0 ;
$cost_sms1 = 0 ;
$cost_sms2 = 0 ;
$cost = 0 ;
$rpha_sms1 = 0 ;
$rpha_sms2 = 0 ;
$rpha = 0 ;
$total_qty_sms1 = 0 ;
$total_qty_sms2 = 0 ;
$total_cost_sms1 = 0 ;
$total_cost_sms2 = 0 ;
$total_cost = 0 ;
$total_rpha_sms1 = 0 ;
$total_rpha_sms2 = 0 ;
$total_rpha = 0 ;
$colspan = 5 + (int)($this->data['max_group']);

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		$curGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$curGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
		
		//total per AFD
		if( ($lastBa) && ($lastTipeTransaksi) && ($lastAfd) &&($row['BA_CODE'].$row['GROUP01'].$row['AFD_CODE'] <> $lastBa.$lastTipeTransaksi.$lastAfd) ){
			//sub total
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='$colspan'>SUB TOTAL AFD</td>";
			echo "<td>".$qty_sms1."</td>";
			echo "<td>".$qty_sms2."</td>";
			echo "<td>".$cost_sms1."</td>";
			echo "<td>".$cost_sms2."</td>";
			echo "<td>".$cost."</td>";
			echo "<td>".$rpha_sms1."</td>";
			echo "<td>".$rpha_sms2."</td>";
			echo "<td>".$rpha."</td>";
			echo "</tr>";
			
			//reset sub total
			$total_qty_sms1 += $qty_sms1 ;
			$total_qty_sms2 += $qty_sms2 ;
			$qty_sms1 = 0 ;
			$qty_sms2 = 0 ;
			$cost_sms1 = 0 ;
			$cost_sms2 = 0 ;
			$cost = 0 ;
			$rpha_sms1 = 0 ;
			$rpha_sms2 = 0 ;
			$rpha = 0 ;
		}
		
		//total per tipe
		if( ($lastBa) && ($lastTipeTransaksi) &&($row['BA_CODE'].$row['GROUP01'] <> $lastBa.$lastTipeTransaksi) ){
			//sub total
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='$colspan'>SUB TOTAL TIPE</td>";
			echo "<td>".$sub_qty_sms1."</td>";
			echo "<td>".$sub_qty_sms2."</td>";
			echo "<td>".$sub_cost_sms1."</td>";
			echo "<td>".$sub_cost_sms2."</td>";
			echo "<td>".$sub_cost."</td>";
			echo "<td>".$sub_rpha_sms1."</td>";
			echo "<td>".$sub_rpha_sms2."</td>";
			echo "<td>".$sub_rpha."</td>";
			echo "</tr>";
			
			//reset sub total
			$sub_qty_sms1 = 0 ;
			$sub_qty_sms2 = 0 ;
			$sub_cost_sms1 = 0 ;
			$sub_cost_sms2 = 0 ;
			$sub_cost = 0 ;
			$sub_rpha_sms1 = 0 ;
			$sub_rpha_sms2 = 0 ;
			$sub_rpha = 0 ;
		}
		
		//total per BA
		if(($lastBa) && ($row['BA_CODE'] <> $lastBa)){
			//total
			echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
			echo "<td colspan='$colspan'>TOTAL</td>";
			echo "<td>".$total_qty_sms1."</td>";
			echo "<td>".$total_qty_sms2."</td>";
			echo "<td>".$total_cost_sms1."</td>";
			echo "<td>".$total_cost_sms2."</td>";
			echo "<td>".$total_cost."</td>";
			echo "<td>".$total_rpha_sms1."</td>";
			echo "<td>".$total_rpha_sms2."</td>";
			echo "<td>".$total_rpha."</td>";
			echo "</tr>";
			
			//reset total
			$total_qty_sms1 = 0 ;
			$total_qty_sms2 = 0 ;
			$total_cost_sms1 = 0 ;
			$total_cost_sms2 = 0 ;
			$total_cost = 0 ;
			$total_rpha_sms1 = 0 ;
			$total_rpha_sms2 = 0 ;
			$total_rpha = 0 ;
		}
		
		echo "<tr style='vertical-align:top;'>";
		
			if($row['BA_CODE'].$row['GROUP01'] <> $lastBa.$lastTipeTransaksi){
				echo "<td><b>".$row['BA_CODE']."</b></td>";
				echo "<td><b>".$row['ESTATE_NAME']."</b></td>";
			}else{
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
			}
			
			//if($row['BA_CODE'].$curGroup.$row['AFD_CODE'] <> $lastBa.$lastGroup.$lastAfd){
				echo "<td><b>".$row['AFD_CODE']."</b></td>";
				for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
					echo "<td><b>".$row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT).'_DESC']."</b></td>";
				}
			//}else{
			//	echo "<td>&nbsp;</td>";
			//	for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			//		echo "<td>&nbsp;</td>";
			//	}
			//}
			
			if($row['BA_CODE'].$curGroup.$row['ACTIVITY_CODE'] <> $lastBa.$lastGroup.$lastActivity){
				echo "<td>".$row['ACTIVITY_CODE']."</td>";
				echo "<td>".$row['ACTIVITY_DESC']."</td>";
			}else{
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
			}
			
		echo "<td>".$row['QTY_SMS1']."</td>";
		echo "<td>".$row['QTY_SMS2']."</td>";		
		echo "<td>".$row['COST_SMS1']."</td>";
		echo "<td>".$row['COST_SMS2']."</td>";
		echo "<td>".$row['COST_SETAHUN']."</td>";
		echo "<td>".$row['RP_HA_SMS1']."</td>";
		echo "<td>".$row['RP_HA_SMS2']."</td>";
		echo "<td>".$row['RP_HA_SETAHUN']."</td>";
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
		$qty_sms1 += $row['QTY_SMS1'] ;
		$qty_sms2 += $row['QTY_SMS2'] ;
		$cost_sms1 += $row['COST_SMS1'] ;
		$cost_sms2 += $row['COST_SMS2'] ;
		$cost += $row['COST_SETAHUN'] ;
		$rpha_sms1 += $row['RP_HA_SMS1'] ;
		$rpha_sms2 += $row['RP_HA_SMS2'] ;
		$rpha += $row['RP_HA_SETAHUN'] ;
		
		$sub_qty_sms1 += $row['QTY_SMS1'] ;
		$sub_qty_sms2 += $row['QTY_SMS2'] ;
		$sub_cost_sms1 += $row['COST_SMS1'] ;
		$sub_cost_sms2 += $row['COST_SMS2'] ;
		$sub_cost += $row['COST_SETAHUN'] ;
		$sub_rpha_sms1 += $row['RP_HA_SMS1'] ;
		$sub_rpha_sms2 += $row['RP_HA_SMS2'] ;
		$sub_rpha += $row['RP_HA_SETAHUN'] ;
		
		//total per BA
		$total_cost_sms1 += $row['COST_SMS1'] ;
		$total_cost_sms2 += $row['COST_SMS2'] ;
		$total_cost += $row['COST_SETAHUN'] ;
		$total_rpha_sms1 += $row['RP_HA_SMS1'] ;
		$total_rpha_sms2 += $row['RP_HA_SMS2'] ;
		$total_rpha += $row['RP_HA_SETAHUN'] ;
	}
	//sub total
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='$colspan'>SUB TOTAL</td>";
	echo "<td>".$qty_sms1."</td>";
	echo "<td>".$qty_sms2."</td>";
	echo "<td>".$cost_sms1."</td>";
	echo "<td>".$cost_sms2."</td>";
	echo "<td>".$cost."</td>";
	echo "<td>".$rpha_sms1."</td>";
	echo "<td>".$rpha_sms2."</td>";
	echo "<td>".$rpha."</td>";
	echo "</tr>";
	
	//total
	$total_qty_sms1 += $qty_sms1 ;
	$total_qty_sms2 += $qty_sms2 ;
	echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
	echo "<td colspan='$colspan'>TOTAL</td>";
	echo "<td>".$total_qty_sms1."</td>";
	echo "<td>".$total_qty_sms2."</td>";
	echo "<td>".$total_cost_sms1."</td>";
	echo "<td>".$total_cost_sms2."</td>";
	echo "<td>".$total_cost."</td>";
	echo "<td>".$total_rpha_sms1."</td>";
	echo "<td>".$total_rpha_sms2."</td>";
	echo "<td>".$total_rpha."</td>";
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>