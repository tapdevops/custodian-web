<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_rkt_pupuk_distribusi_biaya_sisip_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>RKT DISTRIBUSI BIAYA PUPUK - SISIP</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>COMPANY NAME</td>
	<td>AFD CODE</td>
	<td>BLOCK CODE</td>
	<td>LAND TYPE</td>
	<td>TOPOGRAPHY</td>
	<td>BULAN TANAM</td>
	<td>TAHUN TANAM</td>
	<td>MATURITY STAGE SMS1</td>
	<td>MATURITY STAGE SMS2</td>
	<td>HA PLANTED</td>
	<td>POKOK TANAM</td>
	<td>SPH</td>
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
	<td>SETAHUN</td>
	<td>COST TRANSPORT/KG</td>
	<td>COST TOOLS/KG</td>
	<td>COST LABOUR/KG</td>
	<td>JENIS PUPUK JAN</td>
	<td>JENIS PUPUK FEB</td>
	<td>JENIS PUPUK MAR</td>
	<td>JENIS PUPUK APR</td>
	<td>JENIS PUPUK MAY</td>
	<td>JENIS PUPUK JUN</td>
	<td>JENIS PUPUK JUL</td>
	<td>JENIS PUPUK AUG</td>
	<td>JENIS PUPUK SEP</td>
	<td>JENIS PUPUK OCT</td>
	<td>JENIS PUPUK NOV</td>
	<td>JENIS PUPUK DEC</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['COMPANY_NAME']."</td>";
		echo "<td>".$row['AFD_CODE']."</td>";
		echo "<td>".$row['BLOCK_CODE']."</td>";
        echo "<td>".$row['LAND_TYPE']."</td>";
        echo "<td>".$row['TOPOGRAPHY']."</td>";
		echo "<td>".$row['TAHUN_TANAM_M']."</td>";
		echo "<td>".$row['TAHUN_TANAM_Y']."</td>";
		echo "<td>".$row['MATURITY_STAGE_SMS1']."</td>";
		echo "<td>".$row['MATURITY_STAGE_SMS2']."</td>";
		echo "<td>".$row['HA_PLANTED']."</td>";
		echo "<td>".$row['POKOK_TANAM']."</td>";
		echo "<td>".$row['SPH']."</td>";
		echo "<td>".$row['JAN']."</td>";
		echo "<td>".$row['FEB']."</td>";
		echo "<td>".$row['MAR']."</td>";
		echo "<td>".$row['APR']."</td>";
		echo "<td>".$row['MAY']."</td>";
		echo "<td>".$row['JUN']."</td>";
		echo "<td>".$row['JUL']."</td>";
		echo "<td>".$row['AUG']."</td>";
		echo "<td>".$row['SEP']."</td>";
		echo "<td>".$row['OCT']."</td>";
		echo "<td>".$row['NOV']."</td>";
		echo "<td>".$row['DEC']."</td>";
		echo "<td>".$row['SETAHUN']."</td>";
		echo "<td>".$row['COST_TRANSPORT_KG']."</td>";
		echo "<td>".$row['COST_TOOLS_KG']."</td>";
		echo "<td>".$row['COST_LABOUR_POKOK']."</td>";		
		echo "<td>".$row['PUPUK_JAN']."</td>";
		echo "<td>".$row['PUPUK_FEB']."</td>";
		echo "<td>".$row['PUPUK_MAR']."</td>";
		echo "<td>".$row['PUPUK_APR']."</td>";
		echo "<td>".$row['PUPUK_MAY']."</td>";
		echo "<td>".$row['PUPUK_JUN']."</td>";
		echo "<td>".$row['PUPUK_JUL']."</td>";
		echo "<td>".$row['PUPUK_AUG']."</td>";
		echo "<td>".$row['PUPUK_SEP']."</td>";
		echo "<td>".$row['PUPUK_OCT']."</td>";
		echo "<td>".$row['PUPUK_NOV']."</td>";
		echo "<td>".$row['PUPUK_DEC']."</td>";
		echo "</tr>";
	}
}

echo"
</table>";

echo"
</body>
</html>
";
?>