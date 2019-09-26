<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=mod_review_dev_cost_afd_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>MODULE REVIEW DEVELOPMENT COST</div>
<div style='font-weight:bolder; font-size:12px;'>PERIODE : ".date('Y', strtotime($this->data['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:12px;'>REGION : ".$this->data['REGION_NAME']."</div>
<div style='font-weight:bolder; font-size:12px;'>COMPANY : ".$this->data['COMPANY_NAME']."</div>
<div style='font-weight:bolder; font-size:12px;'>BUSINESS AREA : ".$this->data['ESTATE_NAME']."</div>
<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>GROUP 1</td>
	<td rowspan='2'>GROUP 2</td>
	<td rowspan='2'>KODE AKTIVITAS</td>
	<td rowspan='2'>DESKRIPSI</td>
	<td rowspan='2'>NORMA</td>
";
if (!empty($this->data['AFD_CODE'])) {
	foreach ($this->data['AFD_CODE'] as $idx => $row) {
		echo "<td>".$row."</td>";
	}
}
echo "</tr>";
echo "<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>";
if (!empty($this->data['AFD_CODE'])) {
	foreach ($this->data['AFD_CODE'] as $idx => $row) {
		echo "<td>RP/HA</td>";
	}
}
echo "</tr>";

//data
$old_group = "";
if (!empty($this->data['AFD_CODE'])) {
	foreach ($this->data['AFD_CODE'] as $idx1 => $row1) {
		$subtotal[$row1] = 0;
		$total[$row1] = 0;
	}
}

if (!empty($this->data['GROUP_ACTIVITY'])) {
	foreach ($this->data['GROUP_ACTIVITY'] as $idx => $row) {
		//sub total
		if(($old_group) && ($old_group <> $this->data['data'][$row]['GROUP01_DESC'])){
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='4'>SUBTOTAL</td>";
			echo "<td>&nbsp;</td>";
			foreach ($this->data['AFD_CODE'] as $idx1 => $row1) {
				echo "<td>".$subtotal[$row1]."</td>";
				
				$subtotal[$row1] = 0; //reset value
			}
			echo "</tr>";
		}
		
		echo "<tr>";
		echo "<td>".$this->data['data'][$row]['GROUP01_DESC']."</td>";
		echo "<td>".$this->data['data'][$row]['GROUP02_DESC']."</td>";
		echo "<td>".$this->data['data'][$row]['ACTIVITY_CODE']."</td>";
		echo "<td>".$this->data['data'][$row]['ACTIVITY_DESC']."</td>";
		echo "<td>".$this->data['data'][$row]['NORMA']."</td>";
		
		if (!empty($this->data['AFD_CODE'])) {
			foreach ($this->data['AFD_CODE'] as $idx1 => $row1) {
				//echo "<td>".$this->data['data'][$row][$row1]['norma']."</td>";
				echo "<td>".$this->data['data'][$row][$row1]['rp_ha']."</td>";
				
				$subtotal[$row1] += $this->data['data'][$row][$row1]['rp_ha'];
				$total[$row1] += $this->data['data'][$row][$row1]['rp_ha'];
			}
		}
		
		echo "</tr>";
		
		$old_group = $this->data['data'][$row]['GROUP01_DESC'];
	}
	//SUBTOTAL
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='4'>SUBTOTAL</td>";
	echo "<td>&nbsp;</td>";
	foreach ($this->data['AFD_CODE'] as $idx1 => $row1) {
		echo "<td>".$subtotal[$row1]."</td>";
	}
	echo "</tr>";
	
	//GRANDTOTAL
	echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
	echo "<td colspan='4'>GRAND TOTAL</td>";
	echo "<td>&nbsp;</td>";
	foreach ($this->data['AFD_CODE'] as $idx1 => $row1) {
		echo "<td>".$total[$row1]."</td>";
	}
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>