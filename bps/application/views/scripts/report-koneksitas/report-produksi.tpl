<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_koneksitas_produksi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>REPORT KONEKSITAS PRODUKSI ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:20px;'>BA : ". $this->data['rows'][0]['BA_CODE'] ."</div>
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
		if($row[SELISIH_TON] != 0) {
			$selisih_ton = $row[SELISIH_TON] * -1;
			$selisih_ton = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_ton . ")</td>";
		}else $selisih_ton = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TON] . "</td>";
		if($row[SELISIH_JANJANG] != 0) {
			$selisih_janjang = $row[SELISIH_JANJANG] * -1;
			$selisih_janjang = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_janjang . ")</td>";
		}else $selisih_janjang = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_JANJANG] . "</td>";
		if($row[SELISIH_HA_PANEN] != 0) {
			$selisih_ha_panen = $row[SELISIH_HA_PANEN] * -1;
			$selisih_ha_panen = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_ha_panen . ")</td>";
		}else $selisih_ha_panen = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_HA_PANEN] . "</td>";
		if($row[SELISIH_JAN] != 0) {
			$selisih_jan = $row[SELISIH_JAN] * -1;
			$selisih_jan = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_jan . ")</td>";
		}else $selisih_jan = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_JAN] . "</td>";
		if($row[SELISIH_FEB] != 0) {
			$selisih_feb = $row[SELISIH_FEB] * -1;
			$selisih_feb = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_feb . ")</td>";
		}else $selisih_feb = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_FEB] . "</td>";
		if($row[SELISIH_MAR] != 0) {
			$selisih_mar = $row[SELISIH_MAR] * -1;
			$selisih_mar = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_mar . ")</td>";
		}else $selisih_mar = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_MAR] . "</td>";
		if($row[SELISIH_APR] != 0) {
			$selisih_apr = $row[SELISIH_APR] * -1;
			$selisih_apr = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_apr . ")</td>";
		}else $selisih_apr = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_APR] . "</td>";
		if($row[SELISIH_MAY] != 0) {
			$selisih_may = $row[SELISIH_MAY] * -1;
			$selisih_may = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_may . ")</td>";
		}else $selisih_may = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_MAY] . "</td>";
		if($row[SELISIH_JUN] != 0) {
			$selisih_jun = $row[SELISIH_JUN] * -1;
			$selisih_jun = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_jun . ")</td>";
		}else $selisih_jun = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_JUN] . "</td>";
		if($row[SELISIH_JUL] != 0) {
			$selisih_jul = $row[SELISIH_JUL] * -1;
			$selisih_jul = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_jul . ")</td>";
		}else $selisih_jul = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_JAN] . "</td>";
		if($row[SELISIH_AUG] != 0) {
			$selisih_aug = $row[SELISIH_AUG] * -1;
			$selisih_aug = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_aug . ")</td>";
		}else $selisih_aug = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_AUG] . "</td>";
		if($row[SELISIH_SEP] != 0) {
			$selisih_sep = $row[SELISIH_SEP] * -1;
			$selisih_sep = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_sep . ")</td>";
		}else $selisih_sep = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_SEP] . "</td>";
		if($row[SELISIH_OCT] != 0) {
			$selisih_oct = $row[SELISIH_OCT] * -1;
			$selisih_oct = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_oct . ")</td>";
		}else $selisih_oct = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_OCT] . "</td>";
		if($row[SELISIH_NOV] != 0) {
			$selisih_nov = $row[SELISIH_NOV] * -1;
			$selisih_nov = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_nov . ")</td>";
		}else $selisih_nov = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_NOV] . "</td>";
		if($row[SELISIH_DEC] != 0) {
			$selisih_dec = $row[SELISIH_DEC] * -1;
			$selisih_dec = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_dec . ")</td>";
		}else $selisih_dec = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_DEC] . "</td>";
		if($row[SELISIH_JML_BLCK] != 0) {
			$selisih_jml_blck = $row[SELISIH_JML_BLCK] * -1;
			$selisih_jml_blck = "<td rowspan='2' style='background:#999; color:#FF0000; font-weight:bolder; text-align:center; vertical-align:middle;'>(" . $selisih_jml_blck . ")</td>";
		}else $selisih_jml_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_JML_BLCK] . "</td>";
		
		echo"
			<tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>1</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Jumlah Produksi</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Ton</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Janjang</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>Perencanaan Produksi</td>
				<td rowspan='2'>". $row[TON_P_PROD] ."</td>
				<td rowspan='2'>". $row[JANJANG_P_PROD] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>Sebaran HA</td>
				<td rowspan='2'>". $row[TOTAL_SEBARAN_HA] ."</td>
				<td rowspan='2'>TDA</td>
			</tr><tr></tr>
			
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>RKT Panen</td>
				<td rowspan='2'>". $row[TON_PANEN] ."</td>
				<td rowspan='2'>". $row[JANJANG_PANEN] ."</td>
			</tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_ton
				$selisih_janjang
			</tr>
			
			
			<tr></tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>2</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>HA Panen</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Ha</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>Perencanaan Produksi</td>
				<td rowspan='2'>". $row[TOTAL_HA_P_PROD] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>Sebaran HA</td>
				<td rowspan='2'>". $row[TTL_HA_P_SEBARAN] ."</td>
			</tr><tr></tr>				
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_ha_panen
			</tr>
			
			
			<tr></tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>3</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Master Sebaran VS RKT Panen</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Jan</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Feb</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Mar</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Apr</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>May</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Jun</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Jul</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Aug</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Sep</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Oct</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Nov</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Dec</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>Perencanaan Produksi</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_JAN] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_FEB] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_MAR] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_APR] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_MAY] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_JUN] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_JUL] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_AUG] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_SEP] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_OCT] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_NOV] ."</td>
				<td rowspan='2'>". $row[PERSEN_P_PROD_DEC] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>Sebaran Biaya RKT Panen</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_JAN] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_FEB] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_MAR] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_APR] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_MAY] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_JUN] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_JUL] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_AUG] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_SEP] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_OCT] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_NOV] ."</td>
				<td rowspan='2'>". $row[PERSEN_BIAYA_DEC] ."</td>
			</tr><tr></tr>				
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_jan
				$selisih_feb
				$selisih_mar
				$selisih_apr
				$selisih_may
				$selisih_jun
				$selisih_jul
				$selisih_aug
				$selisih_sep
				$selisih_oct
				$selisih_nov
				$selisih_dec
			</tr>
		
			
			<tr></tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>4</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Banyak Blok HS</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>Perencanaan Produksi yang ada Budget tonasenya</td>
				<td rowspan='2'>". $row[JML_BLCK_P_PROD] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>RKT Panen</td>
				<td rowspan='2'>". $row[JML_BLCK_PANEN] ."</td>
			</tr><tr></tr>				
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_jml_blck
			</tr>
			
			";
	}
}

echo"
</table>
</body>
</html>
";
?>