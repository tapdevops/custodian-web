<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_kebutuhan_aktivitas_dev_cost_afd".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>REPORT KEBUTUHAN AKTIVITAS ".date('Y', strtotime($this->data['PERIOD']))."</div>
<div style='font-weight:bolder; font-size:20px;'>DEVELOPMENT COST</div>
<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>BA CODE</td>
	<td rowspan='2'>BA NAME</td>
	<td rowspan='2'>AFD CODE</td>
	";

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
if($this->data['count'] > 0){
	$BA_CODE = "";
	$AFD_CODE = "";
	$ESTATE_NAME = "";
	$TIPE_TRANSAKSI = "";
	$DESCRIPTION = "";
	$ACTIVITY_CODE = "";
	$ACTIVITY_DESC = "";
	
	foreach($this->data['rows'] as $idx => $row){
		/*if ($cekgroup1 == $row['BA_CODE'].$row['AFD_CODE'].$row['ESTATE_NAME'].$row['TIPE_TRANSAKSI'].$row['GROUP02_DESC']){
			$BA_CODE = "";
			$AFD_CODE = "";
			$ESTATE_NAME = "";
			$TIPE_TRANSAKSI = "";
			$DESCRIPTION = "";
		}else{
			$BA_CODE = $row['BA_CODE'];
			$AFD_CODE = $row['AFD_CODE'];
			$ESTATE_NAME = $row['ESTATE_NAME'];
			$TIPE_TRANSAKSI = $row['TIPE_TRANSAKSI'];
			$DESCRIPTION = $row['GROUP02_DESC'];
		}
		
		if ($cekgroup2 == $row['ACTIVITY_CODE'].$row['ACTIVITY_DESC']){
			$ACTIVITY_CODE = "";
			$ACTIVITY_DESC = "";
		}else{
			$ACTIVITY_CODE = $row['ACTIVITY_CODE'];
			$ACTIVITY_DESC = $row['ACTIVITY_DESC'];
		}*/

		//current group combination
		$curGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$curGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}

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
		echo "<td>".$row['SUB_COST_ELEMENT_DESC']."</td>";
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
		
		/*$cekgroup1 = $row['BA_CODE'].$row['AFD_CODE'].$row['ESTATE_NAME'].$row['TIPE_TRANSAKSI'].$row['GROUP02_DESC']; 
		$cekgroup2 = $row['ACTIVITY_CODE'].$row['ACTIVITY_DESC']; */
		$lastBa = $row['BA_CODE'];
		$lastAfd = $row['AFD_CODE'];
		$lastTipeTransaksi = $row['GROUP01'];
		$lastActivity = $row['ACTIVITY_CODE'];
		$lastGroup = "";
		for ($i = 1 ; $i <= $this->data['max_group'] ; $i++){
			$lastGroup .= $row['GROUP'.str_pad($i,2,'0',STR_PAD_LEFT)];
		}
	}
}

echo"
</table>
</body>
</html>
";
?>