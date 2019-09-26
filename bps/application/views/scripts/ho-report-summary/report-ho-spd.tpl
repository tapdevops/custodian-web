<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_ho_spd_".date("YmdHis").".xls");

$arr = array();

if ($this->data['count'] > 0) {
	foreach($this->data['rows'] as $key => $item) {
		$arr[$item['GROUP_01']][$key] = $item;
	}
}

$finArray = array();

$td_jan = $td_feb = $td_mar = $td_apr = $td_may = $td_jun = $td_jul = $td_aug = $td_sep = $td_oct = $td_nov = $td_dec = 0;
$td_adj = $td_outlook = 0;
$td_year = 0;

$tq_jan = $tq_feb = $tq_mar = $tq_apr = $tq_may = $tq_jun = $tq_jul = $tq_aug = $tq_sep = $tq_oct = $tq_nov = $tq_dec = 0;
$tq_adj = $tq_outlook = 0;
$tq_year = 0;

foreach ($arr as $key => $val) {
	$dis_jan = $dis_feb = $dis_mar = $dis_apr = $dis_may = $dis_jun = $dis_jul = $dis_aug = $dis_sep = $dis_oct = $dis_nov = $dis_dec = 0;
	$dis_adj = $dis_outlook = 0;
	$dis_total = 0;

	$qty_jan = $qty_feb = $qty_mar = $qty_apr = $qty_may = $qty_jun = $qty_jul = $qty_aug = $qty_sep = $qty_oct = $qty_nov = $qty_dec = 0;
	$qty_adj = $qty_outlook = 0;
	$qty_total = 0;

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

		$qty_jan = $qty_jan + $val1['QTY_JAN'];
		$qty_feb = $qty_feb + $val1['QTY_FEB'];
		$qty_mar = $qty_mar + $val1['QTY_MAR'];
		$qty_apr = $qty_apr + $val1['QTY_APR'];
		$qty_may = $qty_may + $val1['QTY_MAY'];
		$qty_jun = $qty_jun + $val1['QTY_JUN'];
		$qty_jul = $qty_jul + $val1['QTY_JUL'];
		$qty_aug = $qty_aug + $val1['QTY_AUG'];
		$qty_sep = $qty_sep + $val1['QTY_SEP'];
		$qty_oct = $qty_oct + $val1['QTY_OCT'];
		$qty_nov = $qty_nov + $val1['QTY_NOV'];
		$qty_dec = $qty_dec + $val1['QTY_DEC'];
		$qty_total = $qty_total + $val1['QTY_TOTAL'];

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
		'QTY_JAN' => $qty_jan,
		'QTY_FEB' => $qty_feb,
		'QTY_MAR' => $qty_mar,
		'QTY_APR' => $qty_apr,
		'QTY_MAY' => $qty_may,
		'QTY_JUN' => $qty_jun,
		'QTY_JUL' => $qty_jul,
		'QTY_AUG' => $qty_aug,
		'QTY_SEP' => $qty_sep,
		'QTY_OCT' => $qty_oct,
		'QTY_NOV' => $qty_nov,
		'QTY_DEC' => $qty_dec,
		'QTY_TOTAL' => $qty_total
	);
	$finArray[] = $arr[$key]['subtotal'];

	$td_jan = $td_jan + $arr[$key]['subtotal']['DIS_JAN'];
	$td_feb = $td_feb + $arr[$key]['subtotal']['DIS_FEB'];
	$td_mar = $td_mar + $arr[$key]['subtotal']['DIS_MAR'];
	$td_apr = $td_apr + $arr[$key]['subtotal']['DIS_APR'];
	$td_may = $td_may + $arr[$key]['subtotal']['DIS_MAY'];
	$td_jun = $td_jun + $arr[$key]['subtotal']['DIS_JUN'];
	$td_jul = $td_jul + $arr[$key]['subtotal']['DIS_JUL'];
	$td_aug = $td_aug + $arr[$key]['subtotal']['DIS_AUG'];
	$td_sep = $td_sep + $arr[$key]['subtotal']['DIS_SEP'];
	$td_oct = $td_oct + $arr[$key]['subtotal']['DIS_OCT'];
	$td_nov = $td_nov + $arr[$key]['subtotal']['DIS_NOV'];
	$td_dec = $td_dec + $arr[$key]['subtotal']['DIS_DEC'];
	$td_year = $td_year + $arr[$key]['subtotal']['DIS_TOTAL'];

	$tq_jan = $tq_jan + $arr[$key]['subtotal']['QTY_JAN'];
	$tq_feb = $tq_feb + $arr[$key]['subtotal']['QTY_FEB'];
	$tq_mar = $tq_mar + $arr[$key]['subtotal']['QTY_MAR'];
	$tq_apr = $tq_apr + $arr[$key]['subtotal']['QTY_APR'];
	$tq_may = $tq_may + $arr[$key]['subtotal']['QTY_MAY'];
	$tq_jun = $tq_jun + $arr[$key]['subtotal']['QTY_JUN'];
	$tq_jul = $tq_jul + $arr[$key]['subtotal']['QTY_JUL'];
	$tq_aug = $tq_aug + $arr[$key]['subtotal']['QTY_AUG'];
	$tq_sep = $tq_sep + $arr[$key]['subtotal']['QTY_SEP'];
	$tq_oct = $tq_oct + $arr[$key]['subtotal']['QTY_OCT'];
	$tq_nov = $tq_nov + $arr[$key]['subtotal']['QTY_NOV'];
	$tq_dec = $tq_dec + $arr[$key]['subtotal']['QTY_DEC'];
	$tq_year = $tq_year + $arr[$key]['subtotal']['QTY_TOTAL'];

}
$finArray[] = array(
	'TOTAL' => 1,
	'TD_JAN' => $td_jan,
	'TD_FEB' => $td_feb,
	'TD_MAR' => $td_mar,
	'TD_APR' => $td_apr,
	'TD_MAY' => $td_may,
	'TD_JUN' => $td_jun,
	'TD_JUL' => $td_jul,
	'TD_AUG' => $td_aug,
	'TD_SEP' => $td_sep,
	'TD_OCT' => $td_oct,
	'TD_NOV' => $td_nov,
	'TD_DEC' => $td_dec,
	'TD_YEAR' => $td_year,
	'TQ_JAN' => $tq_jan,
	'TQ_FEB' => $tq_feb,
	'TQ_MAR' => $tq_mar,
	'TQ_APR' => $tq_apr,
	'TQ_MAY' => $tq_may,
	'TQ_JUN' => $tq_jun,
	'TQ_JUL' => $tq_jul,
	'TQ_AUG' => $tq_aug,
	'TQ_SEP' => $tq_sep,
	'TQ_OCT' => $tq_oct,
	'TQ_NOV' => $tq_nov,
	'TQ_DEC' => $tq_dec,
	'TQ_YEAR' => $tq_year
);

//header
echo "
<html>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
	<body>
		<div style='font-weight:bolder; font-size:20px;'>RENCANA KERJA TAHUNAN ".date('Y', strtotime($this->data['rows'][0]['PERIOD_BUDGET']))."</div>
		<div style='font-weight:bolder; font-size:20px;'>REPORT SPD</div>
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
				<td rowspan='2'>BUSINESS AREA</td>
				<td colspan='13'>DISTRIBUSI BIAYA</td>
				<td rowspan='2'>SATUAN</td>
				<td colspan='13'>DISTRIBUSI QTY</td>
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
				<td>26</td>
				<td>27</td>
				<td>28</td>
				<td>29</td>
				<td>30</td>
				<td>31</td>
				<td>32</td>
				<td>33</td>
				<td>34</td>
				<td>35</td>
				<td>36</td>
				<td>37</td>
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
							echo "<td></td>";
							echo "<td>" . (($row['QTY_JAN'] == 0) ? '' : $row['QTY_JAN']) . "</td>";
							echo "<td>" . (($row['QTY_FEB'] == 0) ? '' : $row['QTY_FEB']) . "</td>";
							echo "<td>" . (($row['QTY_MAR'] == 0) ? '' : $row['QTY_MAR']) . "</td>";
							echo "<td>" . (($row['QTY_APR'] == 0) ? '' : $row['QTY_APR']) . "</td>";
							echo "<td>" . (($row['QTY_MAY'] == 0) ? '' : $row['QTY_MAY']) . "</td>";
							echo "<td>" . (($row['QTY_JUN'] == 0) ? '' : $row['QTY_JUN']) . "</td>";
							echo "<td>" . (($row['QTY_JUL'] == 0) ? '' : $row['QTY_JUL']) . "</td>";
							echo "<td>" . (($row['QTY_AUG'] == 0) ? '' : $row['QTY_AUG']) . "</td>";
							echo "<td>" . (($row['QTY_SEP'] == 0) ? '' : $row['QTY_SEP']) . "</td>";
							echo "<td>" . (($row['QTY_OCT'] == 0) ? '' : $row['QTY_OCT']) . "</td>";
							echo "<td>" . (($row['QTY_NOV'] == 0) ? '' : $row['QTY_NOV']) . "</td>";
							echo "<td>" . (($row['QTY_DEC'] == 0) ? '' : $row['QTY_DEC']) . "</td>";
							echo "<td>" . (($row['QTY_TOTAL'] == 0) ? '' : $row['QTY_TOTAL']) . "</td>";
						echo "</tr>";
					} else if (isset($row['TOTAL'])) {
						echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
							echo "<td colspan='10'>TOTAL</td>";
							echo "<td>" . (($row['TD_JAN'] == 0) ? '-' : $row['TD_JAN']) . "</td>";
							echo "<td>" . (($row['TD_FEB'] == 0) ? '-' : $row['TD_FEB']) . "</td>";
							echo "<td>" . (($row['TD_MAR'] == 0) ? '-' : $row['TD_MAR']) . "</td>";
							echo "<td>" . (($row['TD_APR'] == 0) ? '-' : $row['TD_APR']) . "</td>";
							echo "<td>" . (($row['TD_MAY'] == 0) ? '-' : $row['TD_MAY']) . "</td>";
							echo "<td>" . (($row['TD_JUN'] == 0) ? '-' : $row['TD_JUN']) . "</td>";
							echo "<td>" . (($row['TD_JUL'] == 0) ? '-' : $row['TD_JUL']) . "</td>";
							echo "<td>" . (($row['TD_AUG'] == 0) ? '-' : $row['TD_AUG']) . "</td>";
							echo "<td>" . (($row['TD_SEP'] == 0) ? '-' : $row['TD_SEP']) . "</td>";
							echo "<td>" . (($row['TD_OCT'] == 0) ? '-' : $row['TD_OCT']) . "</td>";
							echo "<td>" . (($row['TD_NOV'] == 0) ? '-' : $row['TD_NOV']) . "</td>";
							echo "<td>" . (($row['TD_DEC'] == 0) ? '-' : $row['TD_DEC']) . "</td>";
							echo "<td>" . (($row['TD_YEAR'] == 0) ? '-' : $row['TD_YEAR']) . "</td>";
							echo "<td></td>";
							echo "<td>" . (($row['TQ_JAN'] == 0) ? '' : $row['TQ_JAN']) . "</td>";
							echo "<td>" . (($row['TQ_FEB'] == 0) ? '' : $row['TQ_FEB']) . "</td>";
							echo "<td>" . (($row['TQ_MAR'] == 0) ? '' : $row['TQ_MAR']) . "</td>";
							echo "<td>" . (($row['TQ_APR'] == 0) ? '' : $row['TQ_APR']) . "</td>";
							echo "<td>" . (($row['TQ_MAY'] == 0) ? '' : $row['TQ_MAY']) . "</td>";
							echo "<td>" . (($row['TQ_JUN'] == 0) ? '' : $row['TQ_JUN']) . "</td>";
							echo "<td>" . (($row['TQ_JUL'] == 0) ? '' : $row['TQ_JUL']) . "</td>";
							echo "<td>" . (($row['TQ_AUG'] == 0) ? '' : $row['TQ_AUG']) . "</td>";
							echo "<td>" . (($row['TQ_SEP'] == 0) ? '' : $row['TQ_SEP']) . "</td>";
							echo "<td>" . (($row['TQ_OCT'] == 0) ? '' : $row['TQ_OCT']) . "</td>";
							echo "<td>" . (($row['TQ_NOV'] == 0) ? '' : $row['TQ_NOV']) . "</td>";
							echo "<td>" . (($row['TQ_DEC'] == 0) ? '' : $row['TQ_DEC']) . "</td>";
							echo "<td>" . (($row['TQ_YEAR'] == 0) ? '' : $row['TQ_YEAR']) . "</td>";
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
							echo "<td>" . $row['SATUAN'] . "</td>";
							echo "<td>" . $row['QTY_JAN'] . "</td>";
							echo "<td>" . $row['QTY_FEB'] . "</td>";
							echo "<td>" . $row['QTY_MAR'] . "</td>";
							echo "<td>" . $row['QTY_APR'] . "</td>";
							echo "<td>" . $row['QTY_MAY'] . "</td>";
							echo "<td>" . $row['QTY_JUN'] . "</td>";
							echo "<td>" . $row['QTY_JUL'] . "</td>";
							echo "<td>" . $row['QTY_AUG'] . "</td>";
							echo "<td>" . $row['QTY_SEP'] . "</td>";
							echo "<td>" . $row['QTY_OCT'] . "</td>";
							echo "<td>" . $row['QTY_NOV'] . "</td>";
							echo "<td>" . $row['QTY_DEC'] . "</td>";
							echo "<td>" . $row['QTY_TOTAL'] . "</td>";
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