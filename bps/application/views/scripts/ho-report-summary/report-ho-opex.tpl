<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_ho_opex_".date("YmdHis").".xls");

$arr = array();

if ($this->data['count'] > 0) {
	foreach($this->data['rows'] as $key => $item) {
		$arr[$item['GROUP_01']][$key] = $item;
	}
}

$finArray = array();

$ttl_jan = $ttl_feb = $ttl_mar = $ttl_apr = $ttl_may = $ttl_jun = $ttl_jul = $ttl_aug = $ttl_sep = $ttl_oct = $ttl_nov = $ttl_dec = 0;
$ttl_adj = $ttl_outlook = 0;
$ttl_year = 0;

foreach ($arr as $key => $val) {
	$dis_jan = $dis_feb = $dis_mar = $dis_apr = $dis_may = $dis_jun = $dis_jul = $dis_aug = $dis_sep = $dis_oct = $dis_nov = $dis_dec = 0;
	$dis_adj = $dis_outlook = 0;
	$dis_total = 0;
	foreach ($val as $key1 => $val1) {
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
		$dis_total = $dis_total + $val1['DIS_TOTAL'];
		$dis_adj = $dis_adj + $val1['ADJ'];
		$dis_outlook = $dis_outlook + $val1['OUTLOOK'];

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
		'ADJ' => $dis_adj,
		'OUTLOOK' => $dis_outlook
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
	$ttl_adj = $ttl_adj + $arr[$key]['subtotal']['ADJ'];
	$ttl_outlook = $ttl_outlook + $arr[$key]['subtotal']['OUTLOOK'];

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
	'TTL_ADJ' => $ttl_adj,
	'TTL_OUTLOOK' => $ttl_outlook
);

//header
echo "
<html>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
	<body>
		<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
		<div style='font-weight:bolder; font-size:20px;'>REPORT OPEX</div>
		<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div>
		<br>

		<table border=1>
			<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
				<td rowspan='2'>CC CODE</td>
				<td rowspan='2'>COST CENTER NAME</td>
				<td rowspan='2'>GROUP 01</td>
				<td rowspan='2'>COA</td>
				<td rowspan='2'>KETERANGAN COA</td>
				<td rowspan='2'>RENCANA KERJA</td>
				<td rowspan='2'>KETERANGAN RENCANA KERJA</td>
				<td rowspan='2'>CORE</td>
				<td rowspan='2'>COMPANY</td>
				<td rowspan='2'>BA</td>
				<td colspan='13'>DISTRIBUSI BIAYA</td>
				<td rowspan='2'>ADJUSTMENT</td>
				<td rowspan='2'>OUTLOOK</td>
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
				<td>22</td>
				<td>23</td>
				<td>24</td>
				<td>25</td>
			</tr>
			";

			if ($this->data['count'] > 0) {
				foreach ($finArray as $k => $row) {
					if (isset($row['SUBTOTAL'])) {
						echo "<tr style='vertical-align:top; background:#CCC;'>";
							echo "<td colspan='10'>SUB TOTAL</td>";
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
							echo "<td>" . (($row['ADJ'] == 0) ? '-' : $row['ADJ']) . "</td>";
							echo "<td>" . (($row['OUTLOOK'] == 0) ? '-' : $row['OUTLOOK']) . "</td>";
						echo "</tr>";
					} else if (isset($row['TOTAL'])) {
						echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
							echo "<td colspan='10'>TOTAL</td>";
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
							echo "<td>" . (($row['TTL_ADJ'] == 0) ? '-' : $row['TTL_ADJ']) . "</td>";
							echo "<td>" . (($row['TTL_OUTLOOK'] == 0) ? '-' : $row['TTL_OUTLOOK']) . "</td>";
						echo "</tr>";
					} else {
						echo "<tr style='vertical-align:top; font-weight:bolder;'>";
							echo "<td>" . $row['CC_CODE'] . "</td>";
							echo "<td>" . $row['CC_NAME'] . "</td>";
							echo "<td>" . $row['GROUP_01'] . "</td>";
							echo "<td>" . $row['COA_CODE'] . "</td>";
							echo "<td>" . $row['COA_DESC'] . "</td>";
							echo "<td>" . $row['RK_NAME'] . "</td>";
							echo "<td>" . $row['RK_DESCRIPTION'] . "</td>";
							echo "<td>" . $row['CORE_CODE'] . "</td>";
							echo "<td>" . $row['COMP_NAME'] . "</td>";
							echo "<td>" . $row['BA_NAME'] . "</td>";
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
							echo "<td>" . (($row['ADJ'] == 0) ? '-' : $row['ADJ']) . "</td>";
							echo "<td>" . (($row['OUTLOOK'] == 0) ? '-' : $row['OUTLOOK']) . "</td>";
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