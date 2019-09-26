<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=report_vra_utilisasi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>
<div style='font-weight:bolder; font-size:20px;'>REPORT VRA UTILISASI PER REGION</div>
<div style='font-weight:bolder; font-size:12px;'>PERIODE : ".date('Y', strtotime($this->data['PERIOD_BUDGET']))."</div>
<div style='font-weight:bolder; font-size:12px;'>REGION : ".$this->data['REGION_NAME']."</div><br>
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td rowspan='2'>KODE VRA</td>
	<td rowspan='2'>NAMA VRA</td>
	<td rowspan='2'>NORMA</td>
";
if (!empty($this->data['BA_CODE'])) {
	foreach ($this->data['BA_CODE'] as $idx => $row) {
		echo "<td colspan='2'>".$row."</td>";
	}
}
echo "</tr>";
echo "<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>";
if (!empty($this->data['BA_CODE'])) {
	foreach ($this->data['BA_CODE'] as $idx => $row) {
		echo "<td>RP/QTY</td>";
		echo "<td>SELISIH</td>";
	}
}
echo "</tr>";


if (!empty($this->data['VRA_CODE'])) {
	foreach ($this->data['VRA_CODE'] as $idx => $row) {
		echo "<tr>";
		echo "<td>".$this->data['data'][$row]['VRA_CODE']."</td>";
		echo "<td>".$this->data['data'][$row]['TYPE']."</td>";
		echo "<td>".$this->data['data'][$row]['NORMA']."</td>";
		
		if (!empty($this->data['BA_CODE'])) {
			foreach ($this->data['BA_CODE'] as $idx1 => $row1) {
				echo "<td>".$this->data['data'][$row][$row1]['rp_qty']."</td>";
				echo "<td>".$this->data['data'][$row][$row1]['selisih']."</td>";
			}
		}
		
		echo "</tr>";
	}
}


echo"
</table>
</body>
</html>
";
?>