<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_sebaran_ha_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>REKAP HECTARE STATEMENT</div>
<table border=1>";

//data
$lastBa = $lastTipeTransaksi = '';
$sms1_tbm0 = 0 ;
$sms1_tbm1 = 0 ;
$sms1_tbm2 = 0 ;
$sms1_tbm3 = 0 ;
$sms1_tm = 0 ;
$total_sms1 = 0 ;
$sms2_tbm0 = 0 ;
$sms2_tbm1 = 0 ;
$sms2_tbm2 = 0 ;
$sms2_tbm3 = 0 ;
$sms2_tm = 0 ;
$total_sms2 = 0 ;
$total_ha = 0 ;

if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		if( ($row['TIPE_TRANSAKSI'] == '01_SEBARAN_HA_STATEMENT') && ($lastTipeTransaksi <> '01_SEBARAN_HA_STATEMENT') ){
			//header tabel 1
			echo"
				<tr></tr>
				<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
					<td rowspan='2'>BA CODE</td>
					<td rowspan='2'>COMPANY NAME</td>
					<td rowspan='2'>TIPE</td>
					<td rowspan='2'>AFD</td>
					<td colspan='6'>LUAS TANAM SMS 1 ".$this->period."</td>
					<td colspan='6'>LUAS TANAM SMS 2 ".$this->period."</td>
					<td rowspan='2'></td>
				</tr>
				<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
					<td>TBM 0</td>
					<td>TBM 1</td>
					<td>TBM 2</td>
					<td>TBM 3</td>
					<td>TM</td>
					<td>TOTAL</td>
					<td>TBM 0</td>
					<td>TBM 1</td>
					<td>TBM 2</td>
					<td>TBM 3</td>
					<td>TM</td>
					<td>TOTAL</td>
				</tr>
			";
		}
		if(($lastBa) && ($lastTipeTransaksi) && ($row['BA_CODE'].$row['TIPE_TRANSAKSI'] <> $lastBa.$lastTipeTransaksi) && ($lastTipeTransaksi == '01_SEBARAN_HA_STATEMENT')){
			//total
			echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
			echo "<td colspan='4'>TOTAL</td>";
			echo "<td>".$sms1_tbm0."</td>";
			echo "<td>".$sms1_tbm1."</td>";
			echo "<td>".$sms1_tbm2."</td>";
			echo "<td>".$sms1_tbm3."</td>";
			echo "<td>".$sms1_tm."</td>";
			echo "<td>".$total_sms1."</td>";
			echo "<td>".$sms2_tbm0."</td>";
			echo "<td>".$sms2_tbm1."</td>";
			echo "<td>".$sms2_tbm2."</td>";
			echo "<td>".$sms2_tbm3."</td>";
			echo "<td>".$sms2_tm."</td>";
			echo "<td>".$total_sms2."</td>";
			echo "<td></td>";
			echo "</tr>";
			
			//reset total
			$sms1_tbm0 = 0 ;
			$sms1_tbm1 = 0 ;
			$sms1_tbm2 = 0 ;
			$sms1_tbm3 = 0 ;
			$sms1_tm = 0 ;			
			$total_sms1 = 0 ;
			$sms2_tbm0 = 0 ;
			$sms2_tbm1 = 0 ;
			$sms2_tbm2 = 0 ;
			$sms2_tbm3 = 0 ;
			$sms2_tm = 0 ;		
			$total_sms2 = 0 ;
			$total_ha = 0 ;
		}
		
		if(($row['BA_CODE'].$row['TIPE_TRANSAKSI'] <> $lastBa.$lastTipeTransaksi) && ($lastTipeTransaksi == '01_SEBARAN_HA_STATEMENT') && ($row['TIPE_TRANSAKSI'] <> '01_SEBARAN_HA_STATEMENT')){
			//header tabel 2
			echo "
			<tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td>BA CODE</td>
				<td>COMPANY NAME</td>
				<td>TIPE</td>
				<td>UOM</td>
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
				<td>TOTAL</td>
			</tr>";
		}
		
		echo "<tr style='vertical-align:top;'>";
		if($row['BA_CODE'].$row['TIPE_TRANSAKSI'] <> $lastBa.$lastTipeTransaksi){
			echo "<td><b>".$row['BA_CODE']."</b></td>";
			echo "<td><b>".$row['COMPANY_NAME']."</b></td>";
			echo "<td><b>".str_replace("_", " ",(substr($row['TIPE_TRANSAKSI'], 3)))."</b></td>";
		}else{
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
			echo "<td>&nbsp;</td>";
		}
			
		echo "<td>".$row['AFD_CODE']."</td>";
		echo "<td>".$row['SMS1_TBM0']."</td>";
		echo "<td>".$row['SMS1_TBM1']."</td>";
		echo "<td>".$row['SMS1_TBM2']."</td>";
		echo "<td>".$row['SMS1_TBM3']."</td>";
		echo "<td>".$row['SMS1_TM']."</td>";
		echo "<td>".$row['TOTAL_HA_SMS1']."</td>";
		echo "<td>".$row['SMS2_TBM0']."</td>";
		echo "<td>".$row['SMS2_TBM1']."</td>";
		echo "<td>".$row['SMS2_TBM2']."</td>";
		echo "<td>".$row['SMS2_TBM3']."</td>";
		echo "<td>".$row['SMS2_TM']."</td>";
		echo "<td>".$row['TOTAL_HA_SMS2']."</td>";
		if($row['TIPE_TRANSAKSI'] == '01_SEBARAN_HA_STATEMENT'){
			echo "<td></td>";
		}else{
			echo "<td>".$row['TOTAL_HA']."</td>";
		}
		echo "</tr>";
		
		$lastBa = $row['BA_CODE'];
		$lastTipeTransaksi = $row['TIPE_TRANSAKSI'];
		
		//sub total
		if($row['TIPE_TRANSAKSI'] == '01_SEBARAN_HA_STATEMENT') {
			$sms1_tbm0 += $row['SMS1_TBM0'];
			$sms1_tbm1 += $row['SMS1_TBM1'];
			$sms1_tbm2 += $row['SMS1_TBM2'];
			$sms1_tbm3 += $row['SMS1_TBM3'];
			$sms1_tm += $row['SMS1_TM'];
			$total_sms1 += $row['TOTAL_HA_SMS1'];
			$sms2_tbm0 += $row['SMS2_TBM0'];
			$sms2_tbm1 += $row['SMS2_TBM1'];
			$sms2_tbm2 += $row['SMS2_TBM2'];
			$sms2_tbm3 += $row['SMS2_TBM3'];
			$sms2_tm += $row['SMS2_TM'];
			$total_sms2 += $row['TOTAL_HA_SMS2'];
			$total_ha += $row['TOTAL_HA'];
		}
	}
	//total
	if($lastTipeTransaksi == '01_SEBARAN_HA_STATEMENT') {
		echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
		echo "<td colspan='4'>TOTAL</td>";
		echo "<td>".$sms1_tbm0."</td>";
		echo "<td>".$sms1_tbm1."</td>";
		echo "<td>".$sms1_tbm2."</td>";
		echo "<td>".$sms1_tbm3."</td>";
		echo "<td>".$sms1_tm."</td>";
		echo "<td>".$total_sms1."</td>";
		echo "<td>".$sms2_tbm0."</td>";
		echo "<td>".$sms2_tbm1."</td>";
		echo "<td>".$sms2_tbm2."</td>";
		echo "<td>".$sms2_tbm3."</td>";
		echo "<td>".$sms2_tm."</td>";
		echo "<td>".$total_sms2."</td>";
		echo "<td>".$total_ha."</td>";
		echo "</tr>";
	}
}

echo"
</table>
</body>
</html>
";
?>