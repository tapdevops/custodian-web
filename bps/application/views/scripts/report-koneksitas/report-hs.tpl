<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_koneksitas_ha_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>REPORT KONEKSITAS HA ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
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
		if($row[SELISIH_TBM0_HA] != 0) {
			$selisih_tbm0_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM0_HA] . "</td>";
		}else $selisih_tbm0_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM0_HA] . "</td>";
		if($row[SELISIH_TBM1_HA] != 0) {
			$selisih_tbm1_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM1_HA] . "</td>";
		}else $selisih_tbm1_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM1_HA] . "</td>";
		if($row[SELISIH_TBM2_HA] != 0) {
			$selisih_tbm2_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM2_HA] . "</td>";
		}else $selisih_tbm2_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM2_HA] . "</td>";
		if($row[SELISIH_TBM3_HA] != 0) {
			$selisih_tbm3_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM3_HA] . "</td>";
		}else $selisih_tbm3_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM3_HA] . "</td>";
		if($row[SELISIH_TM_HA] != 0) {
			$selisih_tm_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TM_HA] . "</td>";
		}else $selisih_tm_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TM_HA] . "</td>";
		if($row[SELISIH_TOTAL_HA] != 0) {
			$selisih_total_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_HA] . "</td>";
		}else $selisih_total_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_HA] . "</td>";
		
		if($row[SELISIH_TBM0_BLCK] != 0) {
			$selisih_tbm0_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM0_BLCK] . "</td>";
		}else $selisih_tbm0_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM0_BLCK] . "</td>";
		if($row[SELISIH_TBM1_BLCK] != 0) {
			$selisih_tbm1_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM1_BLCK] . "</td>";
		}else $selisih_tbm1_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM1_BLCK] . "</td>";
		if($row[SELISIH_TBM2_BLCK] != 0) {
			$selisih_tbm2_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM2_BLCK] . "</td>";
		}else $selisih_tbm2_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM2_BLCK] . "</td>";
		if($row[SELISIH_TBM3_BLCK] != 0) {
			$selisih_tbm3_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM3_BLCK] . "</td>";
		}else $selisih_tbm3_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TBM3_BLCK] . "</td>";
		if($row[SELISIH_TM_BLCK] != 0) {
			$selisih_tm_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TM_BLCK] . "</td>";
		}else $selisih_tm_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TM_BLCK] . "</td>";
		
		if($row[SELISIH_BLCK] != 0) {
			$selisih_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_BLCK] . "</td>";
		}else $selisih_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_BLCK] . "</td>";
		if($row[SELISIH_MINERAL_HA] != 0) {
			$selisih_mineral_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_MINERAL_HA] . "</td>";
		}else $selisih_mineral_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_MINERAL_HA] . "</td>";
		if($row[SELISIH_PASIR_HA] != 0) {
			$selisih_pasir_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_PASIR_HA] . "</td>";
		}else $selisih_pasir_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_PASIR_HA] . "</td>";
		if($row[SELISIH_GAMBUT_HA] != 0) {
			$selisih_gambut_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_GAMBUT_HA] . "</td>";
		}else $selisih_gambut_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_GAMBUT_HA] . "</td>";
		if($row[SELISIH_TOTAL_HA_LT] != 0) {
			$selisih_total_ha_lt = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_HA_LT] . "</td>";
		}else $selisih_total_ha_lt = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_HA_LT] . "</td>";
		if($row[SELISIH_MINERAL_BLCK] != 0) {
			$selisih_mineral_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_MINERAL_BLCK] . "</td>";
		}else $selisih_mineral_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_MINERAL_BLCK] . "</td>";
		if($row[SELISIH_PASIR_BLCK] != 0) {
			$selisih_pasir_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_PASIR_BLCK] . "</td>";
		}else $selisih_pasir_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_PASIR_BLCK] . "</td>";
		if($row[SELISIH_GAMBUT_BLCK] != 0) {
			$selisih_gambut_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_GAMBUT_BLCK] . "</td>";
		}else $selisih_gambut_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_GAMBUT_BLCK] . "</td>";
		if($row[SELISIH_TOTAL_BLCK_LT] != 0) {
			$selisih_total_blck_lt = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_BLCK_LT] . "</td>";
		}else $selisih_total_blck_lt = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_BLCK_LT] . "</td>";
		if($row[SELISIH_DATAR_HA] != 0) {
			$selisih_datar_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_DATAR_HA] . "</td>";
		}else $selisih_datar_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_DATAR_HA] . "</td>";		
		if($row[SELISIH_BUKIT_HA] != 0) {
			$selisih_bukit_ha = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_BUKIT_HA] . "</td>";
		}else $selisih_bukit_ha = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_BUKIT_HA] . "</td>";
		if($row[SELISIH_TOTAL_HA_TOP] != 0) {
			$selisih_total_ha_top = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_HA_TOP] . "</td>";
		}else $selisih_total_ha_top = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_HA_TOP] . "</td>";		
		if($row[SELISIH_DATAR_BLCK] != 0) {
			$selisih_datar_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_DATAR_BLCK] . "</td>";
		}else $selisih_datar_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_DATAR_BLCK] . "</td>";
		if($row[SELISIH_BUKIT_BLCK] != 0) {
			$selisih_bukit_blck = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_BUKIT_BLCK] . "</td>";
		}else $selisih_bukit_blck = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_BUKIT_BLCK] . "</td>";
		if($row[SELISIH_TOTAL_BLCK_TOP] != 0) {
			$selisih_total_blck_top = "<td rowspan='2' style='background:#999; color:#FF0000; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_BLCK_TOP] . "</td>";
		}else $selisih_total_blck_top = "<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>" . $row[SELISIH_TOTAL_BLCK_TOP] . "</td>";
		
		echo"
			<tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>1a.</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Luas Ha</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM0</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM1</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM2</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM3</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TM</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Total</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>HS</td>
				<td rowspan='2'>". $row[TBM0_HS_HA] ."</td>
				<td rowspan='2'>". $row[TBM1_HS_HA] ."</td>
				<td rowspan='2'>". $row[TBM2_HS_HA] ."</td>
				<td rowspan='2'>". $row[TBM3_HS_HA] ."</td>
				<td rowspan='2'>". $row[TM_HS_HA] ."</td>
				<td rowspan='2'>". $row[TOTAL_HS_HA] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>(" . $row[ACTIVITY_CODE] . "-" . $row[DESCRIPTION] . ")</td>
				<td rowspan='2'>". $row[TBM0_RKT_HA] ."</td>
				<td rowspan='2'>". $row[TBM1_RKT_HA] ."</td>
				<td rowspan='2'>". $row[TBM2_RKT_HA] ."</td>
				<td rowspan='2'>". $row[TBM3_RKT_HA] ."</td>
				<td rowspan='2'>". $row[TM_RKT_HA] ."</td>
				<td rowspan='2'>". $row[TOTAL_RKT_HA] ."</td>
			</tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_tbm0_ha
				$selisih_tbm1_ha
				$selisih_tbm2_ha
				$selisih_tbm3_ha
				$selisih_tm_ha
				$selisih_total_ha
			</tr>
			<tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>1b.</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Banyak Block-Status</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM0</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM1</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM2</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TBM3</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>TM</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Total</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>HS</td>
				<td rowspan='2'>". $row[TBM0_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[TBM1_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[TBM2_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[TBM3_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[TM_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[TOTAL_HS_BLCK] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>(" . $row[ACTIVITY_CODE] . "-" . $row[DESCRIPTION] . ")</td>
				<td rowspan='2'>". $row[TBM0_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[TBM1_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[TBM2_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[TBM3_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[TM_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[TOTAL_RKT_BLCK] ."</td>
			</tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_tbm0_blck
				$selisih_tbm1_blck
				$selisih_tbm2_blck
				$selisih_tbm3_blck
				$selisih_tm_blck
				$selisih_blck
			</tr>
			<tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>2a.</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Luas Block-Land Type</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Mineral</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Pasir</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Gambut</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Total</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>HS</td>
				<td rowspan='2'>". $row[MINERAL_HS] ."</td>
				<td rowspan='2'>". $row[PASIR_HS] ."</td>
				<td rowspan='2'>". $row[GAMBUT_HS] ."</td>
				<td rowspan='2'>". $row[TOTAL_HS_LT] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>(" . $row[ACTIVITY_CODE] . "-" . $row[DESCRIPTION] . ")</td>
				<td rowspan='2'>". $row[MINERAL_RKT] ."</td>
				<td rowspan='2'>". $row[PASIR_RKT] ."</td>
				<td rowspan='2'>". $row[GAMBUT_RKT] ."</td>
				<td rowspan='2'>". $row[TOTAL_RKT_LT] ."</td>
			</tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_mineral_ha
				$selisih_pasir_ha
				$selisih_gambut_ha
				$selisih_total_ha_lt
			</tr>
			<tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>2b.</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Banyak Block-Land Type</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Mineral</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Pasir</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Gambut</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Total</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>HS</td>
				<td rowspan='2'>". $row[MINERAL_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[PASIR_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[GAMBUT_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[TOTAL_HS_LT_BLCK] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>(" . $row[ACTIVITY_CODE] . "-" . $row[DESCRIPTION] . ")</td>
				<td rowspan='2'>". $row[MINERAL_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[PASIR_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[GAMBUT_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[TOTAL_RKT_LT_BLCK] ."</td>
			</tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_mineral_blck
				$selisih_pasir_blck
				$selisih_gambut_blck
				$selisih_total_blck_lt
			</tr>
			<tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>3a.</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Luas Block-Topography</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Datar</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Bukit</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Total</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>HS</td>
				<td rowspan='2'>". $row[DATAR_HS] ."</td>
				<td rowspan='2'>". $row[BUKIT_HS] ."</td>
				<td rowspan='2'>". $row[TOTAL_HS_TOP] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>(" . $row[ACTIVITY_CODE] . "-" . $row[DESCRIPTION] . ")</td>
				<td rowspan='2'>". $row[DATAR_RKT] ."</td>
				<td rowspan='2'>". $row[BUKIT_RKT] ."</td>
				<td rowspan='2'>". $row[TOTAL_RKT_TOP] ."</td>
			</tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_datar_ha
				$selisih_bukit_ha
				$selisih_total_ha_top
			</tr>
			<tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>3b.</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; vertical-align:middle;'>Banyak Block-Topography</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Datar</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Bukit</td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Total</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>HS</td>
				<td rowspan='2'>". $row[DATAR_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[BUKIT_HS_BLCK] ."</td>
				<td rowspan='2'>". $row[TOTAL_HS_TOP_BLCK] ."</td>
			</tr><tr></tr>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'></td>
				<td rowspan='2'>(" . $row[ACTIVITY_CODE] . "-" . $row[DESCRIPTION] . ")</td>
				<td rowspan='2'>". $row[DATAR_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[BUKIT_RKT_BLCK] ."</td>
				<td rowspan='2'>". $row[TOTAL_RKT_TOP_BLCK] ."</td>
			</tr><tr></tr>
			<tr>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'></td>
				<td rowspan='2' style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>Selisih</td>
				$selisih_datar_blck
				$selisih_bukit_blck
				$selisih_total_blck_top
			</tr>
			<tr></tr><tr></tr>
			
			";
	}
}

echo"
</table>
</body>
</html>
";
?>