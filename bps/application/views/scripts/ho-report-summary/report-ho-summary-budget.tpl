<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_ho_summary_budget_".date("YmdHis").".xls");

$arr = array();

if ($this->data['count'] > 0) {
	foreach($this->data['rows'] as $key => $item) {
		$arr[$item['GROUP01']][$key] = $item;
	}
}
$finArray = array();

$ttl_jan = $ttl_feb = $ttl_mar = $ttl_apr = $ttl_may = $ttl_jun = $ttl_jul = $ttl_aug = $ttl_sep = $ttl_oct = $ttl_nov = $ttl_dec = 0;
$ttl_year = $ttl_out = $ttl_sel = 0;

foreach ($arr as $key => $val) {
	$dis_jan = $dis_feb = $dis_mar = $dis_apr = $dis_may = $dis_jun = $dis_jul = $dis_aug = $dis_sep = $dis_oct = $dis_nov = $dis_dec = 0;
	$dis_total = $out_total = $sel_total = 0;

	foreach ($val as $key1 => $val1) {
		//$dis_year = $val1['DIS_JAN'] + $val1['DIS_FEB'] + $val1['DIS_MAR'] + $val1['DIS_APR'] + $val1['DIS_MAY'] + $val1['DIS_JUN'] + //$val1['DIS_JUL'] + $val1['DIS_AUG'] + $val1['DIS_SEP'] + $val1['DIS_OCT'] + $val1['DIS_NOV'] + $val1['DIS_DEC'];
		//$dis_total = 0;

		if ($val1['GROUP01'] == 'OUTLOOK') {
			$val1['OUTLOOK'] = $val1['DIS_JAN'] + $val1['DIS_FEB'] + $val1['DIS_MAR'] + $val1['DIS_APR'] + $val1['DIS_MAY'] + $val1['DIS_JUN'] + $val1['DIS_JUL'] + $val1['DIS_AUG'] + $val1['DIS_SEP'] + $val1['DIS_OCT'] + $val1['DIS_NOV'] + $val1['DIS_DEC'];
			$dis_year = 0;
		} else {
			$val1['OUTLOOK'] = 0;
			$dis_year = $val1['DIS_JAN'] + $val1['DIS_FEB'] + $val1['DIS_MAR'] + $val1['DIS_APR'] + $val1['DIS_MAY'] + $val1['DIS_JUN'] + $val1['DIS_JUL'] + $val1['DIS_AUG'] + $val1['DIS_SEP'] + $val1['DIS_OCT'] + $val1['DIS_NOV'] + $val1['DIS_DEC'];
		}

		$val1['DIS_YEAR'] = $dis_year;
		$val1['SELISIH'] = $val1['OUTLOOK'] - $dis_year;

		$persen = $val1['SELISIH'] / $val1['DIS_YEAR'] * 100;
		//$val1['PERSEN'] = bcdiv($persen, 1, 2);
		$val1['PERSEN'] = number_format(floor($persen*100)/100, 2);

		$dis_jan = $dis_jan + $val1['DIS_JAN'];
		$dis_feb = $dis_feb + $val1['DIS_FEB'];
		$dis_mar = $dis_mar + $val1['DIS_MAR'];
		$dis_apr = $dis_apr + $val1['DIS_APR'];
		$dis_may = $dis_may + $val1['DIS_MAY'];
		$dis_jun = $dis_jun + $val1['DIS_JUN'];
		$dis_jul = $dis_jul + $val1['DIS_JUL'];
		$dis_aug = $dis_aug + $val1['DIS_AUG'];
		$dis_sep = $dis_sep + $val1['DIS_SEP'];
		$dis_oct = $dis_oct + $val1['DIS_OCT'];
		$dis_nov = $dis_nov + $val1['DIS_NOV'];
		$dis_dec = $dis_dec + $val1['DIS_DEC'];
		$dis_total = $dis_total + (
			$val1['DIS_JAN'] + $val1['DIS_FEB'] + $val1['DIS_MAR'] + $val1['DIS_APR'] + $val1['DIS_MAY'] + $val1['DIS_JUN'] + 
			$val1['DIS_JUL'] + $val1['DIS_AUG'] + $val1['DIS_SEP'] + $val1['DIS_OCT'] + $val1['DIS_NOV'] + $val1['DIS_DEC']
		);
		$out_total = $out_total + $val1['OUTLOOK'];
		$sel_total = $sel_total + $val1['SELISIH'];

		$finArray[] = $val1;
	}

	$arr[$key]['subtotal'] = array(
		'SUBTOTAL' => 1,
		'DIS_JAN' => $dis_jan,
		'DIS_FEB' => $dis_feb,
		'DIS_MAR' => $dis_mar,
		'DIS_APR' => $dis_apr,
		'DIS_MAY' => $dis_may,
		'DIS_JUN' => $dis_jun,
		'DIS_JUL' => $dis_jul,
		'DIS_AUG' => $dis_aug,
		'DIS_SEP' => $dis_sep,
		'DIS_OCT' => $dis_oct,
		'DIS_NOV' => $dis_nov,
		'DIS_DEC' => $dis_dec,
		'DIS_TOTAL' => $dis_total,
		'OUTLOOK_TOTAL' => $out_total,
		'SELISIH' => $sel_total
	);
	$finArray[] = $arr[$key]['subtotal'];

	$ttl_jan = $ttl_jan + $arr[$key]['subtotal']['DIS_JAN'];
	$ttl_feb = $ttl_feb + $arr[$key]['subtotal']['DIS_FEB'];
	$ttl_mar = $ttl_mar + $arr[$key]['subtotal']['DIS_MAR'];
	$ttl_apr = $ttl_apr + $arr[$key]['subtotal']['DIS_APR'];
	$ttl_may = $ttl_may + $arr[$key]['subtotal']['DIS_MAY'];
	$ttl_jun = $ttl_jun + $arr[$key]['subtotal']['DIS_JUN'];
	$ttl_jul = $ttl_jul + $arr[$key]['subtotal']['DIS_JUL'];
	$ttl_aug = $ttl_aug + $arr[$key]['subtotal']['DIS_AUG'];
	$ttl_sep = $ttl_sep + $arr[$key]['subtotal']['DIS_SEP'];
	$ttl_oct = $ttl_oct + $arr[$key]['subtotal']['DIS_OCT'];
	$ttl_nov = $ttl_nov + $arr[$key]['subtotal']['DIS_NOV'];
	$ttl_dec = $ttl_dec + $arr[$key]['subtotal']['DIS_DEC'];
	$ttl_year = $ttl_year + $arr[$key]['subtotal']['DIS_TOTAL'];
	$ttl_out = $ttl_out + $arr[$key]['subtotal']['OUTLOOK_TOTAL'];
	$ttl_sel = $ttl_sel + $arr[$key]['subtotal']['SELISIH'];

}
$finArray[] = array(
	'TOTAL' => 1,
	'TTL_JAN' => $ttl_jan,
	'TTL_FEB' => $ttl_feb,
	'TTL_MAR' => $ttl_mar,
	'TTL_APR' => $ttl_apr,
	'TTL_MAY' => $ttl_may,
	'TTL_JUN' => $ttl_jun,
	'TTL_JUL' => $ttl_jul,
	'TTL_AUG' => $ttl_aug,
	'TTL_SEP' => $ttl_sep,
	'TTL_OCT' => $ttl_oct,
	'TTL_NOV' => $ttl_nov,
	'TTL_DEC' => $ttl_dec,
	'TTL_YEAR' => $ttl_year,
	'TTL_OUTLOOK' => $ttl_out,
	'TTL_SELISIH' => $ttl_sel
);
//header
echo "
<html>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
	<body>
		<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
		<div style='font-weight:bolder; font-size:20px;'>REPORT SUMMARY BUDGET HO</div>
		<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div>
		<br>

		<table border=1>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'>CC CODE</td>
				<td rowspan='2'>COST CENTER NAME</td>
				<td rowspan='2'>GROUP 01</td>
				<td rowspan='2'>COA</td>
				<td rowspan='2'>KETERANGAN COA</td>
				<td colspan='13'>DISTRIBUSI BIAYA</td>
				<td rowspan='2'>OUTLOOK</td>
				<td rowspan='2'>VARIANCE OUTLOOK vs BUDGET</td>
				<td rowspan='2'>VARIANCE %</td>
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
				<td>YEAR</td>
			</tr>
			<tr style='background:#000; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td>1</td>
				<td>2</td>
				<td>3</td>
				<td>4</td>
				<td>5</td>
				<td>6</td>
				<td>7</td>
				<td>8</td>
				<td>9</td>
				<td>10</td>
				<td>11</td>
				<td>12</td>
				<td>13</td>
				<td>14</td>
				<td>15</td>
				<td>16</td>
				<td>17</td>
				<td>18</td>
				<td>19</td>
				<td>20</td>
				<td>21</td>
			</tr>
			";

			if ($this->data['count'] > 0) {
				foreach ($finArray as $k => $row) {
					if (isset($row['SUBTOTAL'])) {
						echo "<tr style='vertical-align:top; background:#CCC;'>";
							echo "<td colspan='5'>SUB TOTAL</td>";
							echo "<td>" . (($row['DIS_JAN'] == 0) ? '-' : $row['DIS_JAN']) . "</td>";
							echo "<td>" . (($row['DIS_FEB'] == 0) ? '-' : $row['DIS_FEB']) . "</td>";
							echo "<td>" . (($row['DIS_MAR'] == 0) ? '-' : $row['DIS_MAR']) . "</td>";
							echo "<td>" . (($row['DIS_APR'] == 0) ? '-' : $row['DIS_APR']) . "</td>";
							echo "<td>" . (($row['DIS_MAY'] == 0) ? '-' : $row['DIS_MAY']) . "</td>";
							echo "<td>" . (($row['DIS_JUN'] == 0) ? '-' : $row['DIS_JUN']) . "</td>";
							echo "<td>" . (($row['DIS_JUL'] == 0) ? '-' : $row['DIS_JUL']) . "</td>";
							echo "<td>" . (($row['DIS_AUG'] == 0) ? '-' : $row['DIS_AUG']) . "</td>";
							echo "<td>" . (($row['DIS_SEP'] == 0) ? '-' : $row['DIS_SEP']) . "</td>";
							echo "<td>" . (($row['DIS_OCT'] == 0) ? '-' : $row['DIS_OCT']) . "</td>";
							echo "<td>" . (($row['DIS_NOV'] == 0) ? '-' : $row['DIS_NOV']) . "</td>";
							echo "<td>" . (($row['DIS_DEC'] == 0) ? '-' : $row['DIS_DEC']) . "</td>";
							echo "<td>" . (($row['DIS_TOTAL'] == 0) ? '-' : $row['DIS_TOTAL']) . "</td>";
							echo "<td>" . (($row['OUTLOOK_TOTAL'] == 0) ? '-' : $row['OUTLOOK_TOTAL']) . "</td>";
							echo "<td>" . (($row['SELISIH'] == 0) ? '-' : $row['SELISIH']) . "</td>";
							echo "<td></td>";
						echo "</tr>";
					} else if (isset($row['TOTAL'])) {
						echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
							echo "<td colspan='5'>TOTAL</td>";
							echo "<td>" . (($row['TTL_JAN'] == 0) ? '-' : $row['TTL_JAN']) . "</td>";
							echo "<td>" . (($row['TTL_FEB'] == 0) ? '-' : $row['TTL_FEB']) . "</td>";
							echo "<td>" . (($row['TTL_MAR'] == 0) ? '-' : $row['TTL_MAR']) . "</td>";
							echo "<td>" . (($row['TTL_APR'] == 0) ? '-' : $row['TTL_APR']) . "</td>";
							echo "<td>" . (($row['TTL_MAY'] == 0) ? '-' : $row['TTL_MAY']) . "</td>";
							echo "<td>" . (($row['TTL_JUN'] == 0) ? '-' : $row['TTL_JUN']) . "</td>";
							echo "<td>" . (($row['TTL_JUL'] == 0) ? '-' : $row['TTL_JUL']) . "</td>";
							echo "<td>" . (($row['TTL_AUG'] == 0) ? '-' : $row['TTL_AUG']) . "</td>";
							echo "<td>" . (($row['TTL_SEP'] == 0) ? '-' : $row['TTL_SEP']) . "</td>";
							echo "<td>" . (($row['TTL_OCT'] == 0) ? '-' : $row['TTL_OCT']) . "</td>";
							echo "<td>" . (($row['TTL_NOV'] == 0) ? '-' : $row['TTL_NOV']) . "</td>";
							echo "<td>" . (($row['TTL_DEC'] == 0) ? '-' : $row['TTL_DEC']) . "</td>";
							echo "<td>" . (($row['TTL_YEAR'] == 0) ? '-' : $row['TTL_YEAR']) . "</td>";
							echo "<td>" . (($row['TTL_OUTLOOK'] == 0) ? '-' : $row['TTL_OUTLOOK']) . "</td>";
							echo "<td>" . (($row['TTL_SELISIH'] == 0) ? '-' : $row['TTL_SELISIH']) . "</td>";
							echo "<td></td>";
						echo "</tr>";
					} else {
						echo "<tr style='vertical-align:top; font-weight:bolder;'>";
							echo "<td>" . $row['CC_CODE'] . "</td>";
							echo "<td>" . $row['CC_NAME'] . "</td>";
							echo "<td>" . $row['GROUP01'] . "</td>";
							echo "<td>" . $row['COA_CODE'] . "</td>";
							echo "<td>" . $row['COA_DESC'] . "</td>";
							echo "<td>" . (($row['DIS_JAN'] == 0) ? '-' : $row['DIS_JAN']) . "</td>";
							echo "<td>" . (($row['DIS_FEB'] == 0) ? '-' : $row['DIS_FEB']) . "</td>";
							echo "<td>" . (($row['DIS_MAR'] == 0) ? '-' : $row['DIS_MAR']) . "</td>";
							echo "<td>" . (($row['DIS_APR'] == 0) ? '-' : $row['DIS_APR']) . "</td>";
							echo "<td>" . (($row['DIS_MAY'] == 0) ? '-' : $row['DIS_MAY']) . "</td>";
							echo "<td>" . (($row['DIS_JUN'] == 0) ? '-' : $row['DIS_JUN']) . "</td>";
							echo "<td>" . (($row['DIS_JUL'] == 0) ? '-' : $row['DIS_JUL']) . "</td>";
							echo "<td>" . (($row['DIS_AUG'] == 0) ? '-' : $row['DIS_AUG']) . "</td>";
							echo "<td>" . (($row['DIS_SEP'] == 0) ? '-' : $row['DIS_SEP']) . "</td>";
							echo "<td>" . (($row['DIS_OCT'] == 0) ? '-' : $row['DIS_OCT']) . "</td>";
							echo "<td>" . (($row['DIS_NOV'] == 0) ? '-' : $row['DIS_NOV']) . "</td>";
							echo "<td>" . (($row['DIS_DEC'] == 0) ? '-' : $row['DIS_DEC']) . "</td>";
							echo "<td>" . (($row['DIS_YEAR'] == 0) ? '-' : $row['DIS_YEAR']) . "</td>";
							echo "<td>" . (($row['OUTLOOK'] == 0) ? '-' : $row['OUTLOOK']) . "</td>";
							echo "<td>" . (($row['SELISIH'] == 0) ? '-' : $row['SELISIH']) . "</td>";
							echo "<td>" . (($row['PERSEN'] == 0) ? '-' : $row['PERSEN']) . "</td>";
						echo "</tr>";
					}
				}
			}

echo"
</table>
</body>
</html>
";
?>