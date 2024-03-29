<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=mod_review_estate_cost_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>MODULE REVIEW ESTATE COST</div>
<div style='font-weight:bolder; font-size:12px;'>PERIODE : ".date('Y', strtotime($this->data['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:12px;'>REGION : ".$this->data['REGION_NAME']."</div>
<div style='font-size:10px;'><i>Generate Terakhir : ".$this->last_data['INSERT_TIME']." oleh ".$this->last_data['INSERT_USER']."</i></div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>GROUP 1</td>
	<td rowspan='2'>GROUP 2</td>
	<td rowspan='2'>KODE AKTIVITAS</td>
	<td rowspan='2'>DESKRIPSI</td>
";
if (!empty($this->data['BA_CODE'])) {
	foreach ($this->data['BA_CODE'] as $idx => $row) {
		echo "<td rowspan='2'>NORMA</td>";
		echo "<td colspan='2'>".$row."</td>";
	}
}
echo "</tr>";
echo "<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>";
if (!empty($this->data['BA_CODE'])) {
	foreach ($this->data['BA_CODE'] as $idx => $row) {
		echo "<td>RP/HA</td>";
		echo "<td>RP/KG</td>";
	}
}
echo "</tr>";

//data
$old_group = "";
if (!empty($this->data['BA_CODE'])) {
	foreach ($this->data['BA_CODE'] as $idx1 => $row1) {
		$subtotal['rp_ha'][$row1] = 0;
		$subtotal['rp_kg'][$row1] = 0;
		$total['rp_ha'][$row1] = 0;
		$total['rp_kg'][$row1] = 0;
	}
}

if (!empty($this->data['GROUP_ACTIVITY'])) {
	foreach ($this->data['GROUP_ACTIVITY'] as $idx => $row) {
		//sub total
		if(($old_group) && ($old_group <> $this->data['data'][$row]['GROUP02_DESC'])){
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='4'>SUBTOTAL</td>";
			//echo "<td>&nbsp;</td>";
			foreach ($this->data['BA_CODE'] as $idx1 => $row1) {
				echo "<td> </td>";
				echo "<td>".$subtotal['rp_ha'][$row1]."</td>";
				echo "<td>".$subtotal['rp_kg'][$row1]."</td>";
				
				$subtotal['rp_ha'][$row1] = 0; //reset value
				$subtotal['rp_kg'][$row1] = 0; //reset value
			}
			echo "</tr>";
		}
		
		echo "<tr>";
		echo "<td>".$this->data['data'][$row]['GROUP01_DESC']."</td>";
		echo "<td>".$this->data['data'][$row]['GROUP02_DESC']."</td>";
		echo "<td>".$this->data['data'][$row]['ACTIVITY_CODE']."</td>";
		echo "<td>".$this->data['data'][$row]['ACTIVITY_DESC']."</td>";
		
		if (!empty($this->data['BA_CODE'])) {
			foreach ($this->data['BA_CODE'] as $idx1 => $row1) {
				echo "<td>".$this->data['data'][$row][$row1]['NORMA']."</td>";
				echo "<td>".$this->data['data'][$row][$row1]['rp_ha']."</td>";
				echo "<td>".$this->data['data'][$row][$row1]['rp_kg']."</td>";
				
				$subtotal['rp_ha'][$row1] += $this->data['data'][$row][$row1]['rp_ha'];
				$subtotal['rp_kg'][$row1] += $this->data['data'][$row][$row1]['rp_kg'];
				$total['rp_ha'][$row1] += $this->data['data'][$row][$row1]['rp_ha'];
				$total['rp_kg'][$row1] += $this->data['data'][$row][$row1]['rp_kg'];
			}
		}
		
		echo "</tr>";
		
		$old_group = $this->data['data'][$row]['GROUP02_DESC'];
	}
	//SUBTOTAL
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='4'>SUBTOTAL</td>";
	//echo "<td>&nbsp;</td>";
	foreach ($this->data['BA_CODE'] as $idx1 => $row1) {
		echo "<td> </td>";
		echo "<td>".$subtotal['rp_ha'][$row1]."</td>";
		echo "<td>".$subtotal['rp_kg'][$row1]."</td>";
	}
	echo "</tr>";
	
	//GRANDTOTAL
	echo "<tr style='vertical-align:top; background:#000; color:#FFF; font-weight:bolder;'>";
	echo "<td colspan='4'>GRAND TOTAL</td>";
	//echo "<td>&nbsp;</td>";
	foreach ($this->data['BA_CODE'] as $idx1 => $row1) {
		echo "<td> </td>";
		echo "<td>".$total['rp_ha'][$row1]."</td>";
		echo "<td>".$total['rp_kg'][$row1]."</td>";
	}
	echo "</tr>";
}

echo"
</table>
</body>
</html>
";
?>