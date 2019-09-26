<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_rkt_panen_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>RKT PANEN</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>PRESENTASE OER</td>
	<td>YIELD</td>
	<td>AFD CODE</td>
	<td>BLOK - DESC</td>
	<td>BULAN TANAM</td>
	<td>TAHUN TANAM</td>
	<td>HA</td>
	<td>POKOK</td>
	<td>SPH</td>
	<td>TOPOGRAFI</td>
	<td>TIPE TANAH</td>
	<td>TON</td>
	<td>JANJANG</td>
	<td>BJR AFD</td>
	<td>JARAK PKS</td>
	<td>SUMBER BIAYA UNIT</td>
	<td>PERSEN LANGSIR</td>
	<td>BIAYA PEMANEN<BR>HK</td>
	<td>BIAYA PEMANEN<BR>RP/BASIS</td>
	<td>BIAYA PEMANEN<BR>RP/PREMI JANJANG</td>
	<td>PREMI INSENTIF</td>
	<td>BIAYA PEMANEN<BR>RP/PREMI BRD</td>
	<td>BIAYA PEMANEN<BR>RP/TOTAL</td>
	<td>BIAYA PEMANEN<BR>RP/KG</td>
	<td>BIAYA SPV<BR>RP/BASIS</td>
	<td>BIAYA SPV<BR>RP/PREMI</td>
	<td>BIAYA SPV<BR>RP/TOTAL</td>
	<td>BIAYA SPV<BR>RP/KG</td>
	<td>BIAYA ALAT PANEN<BR>RP/KG</td>
	<td>BIAYA ALAT PANEN<BR>RP/TOTAL</td>
	<td>TUKANG MUAT<BR>BASIS</td>
	<td>TUKANG MUAT<BR>PREMI</td>
	<td>TUKANG MUAT<BR>TOTAL</td>
	<td>TUKANG MUAT<BR>RP/KG</td>
	<td>SUPIR<BR>PREMI</td>
	<td>SUPIR<BR>RP/KG</td>
	<td>ANGKUT TBS<BR>RP/KG/KM</td>
	<td>ANGKUT TBS<BR>RP/ANGKUT</td>
	<td>ANGKUT TBS<BR>RP/KG</td>
	<td>KRANI BUAH<BR>BASIS</td>
	<td>KRANI BUAH<BR>PREMI</td>
	<td>KRANI BUAH<BR>TOTAL</td>
	<td>KRANI BUAH<BR>RP/KG</td>
	<td>LANGSIR<BR>TON</td>
	<td>LANGSIR<BR>RP</td>
	<td>LANGSIR<BR>TUKANG MUAT</td>
	<td>LANGSIR<BR>RP/KG</td>
	<td>COST JAN</td>
	<td>COST FEB</td>
	<td>COST MAR</td>
	<td>COST APR</td>
	<td>COST MAY</td>
	<td>COST JUN</td>
	<td>COST JUL</td>
	<td>COST AUG</td>
	<td>COST SEP</td>
	<td>COST OCT</td>
	<td>COST NOV</td>
	<td>COST DEC</td>
	<td>COST SETAHUN</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGETHS']."</td>";
		echo "<td>".$row['BA_CODEHS']."</td>";
		echo "<td>".$row['OER_BA']."</td>";
		echo "<td>".number_format($row['YIELD'],4,".",",")."</td>";
		echo "<td>".$row['AFD_CODEHS']."</td>";
		echo "<td>".$row['BLOCK_CODEHS'] . " - " . $row['BLOCK_DESCHS'] ."</td>";
		echo "<td>" . $row['BULAN_TANAM'] . "</td>";
		echo "<td>" . $row['TAHUN_TANAM'] . "</td>";
		echo "<td>" . $row['HA_PLANTED'] . "</td>";
		echo "<td>" . $row['POKOK_TANAM'] . "</td>";
		echo "<td>" . $row['SPH'] . "</td>";
		echo "<td>" . $row['TOPOGRAPHY'] . "</td>";
		echo "<td>" . $row['LAND_TYPE'] . "</td>";
		echo "<td>".$row['TON']."</td>";
		echo "<td>".$row['JANJANG']."</td>";
		echo "<td>".$row['BJR_AFD']."</td>";
		echo "<td>".$row['JARAK_PKS']."</td>";
		echo "<td>".$row['SUMBER_BIAYA']."</td>";
		echo "<td>".$row['PERSEN_LANGSIR']."</td>";
		echo "<td>".$row['BIAYA_PEMANEN_HK']."</td>";
		echo "<td>".$row['BIAYA_PEMANEN_RP_BASIS']."</td>";
		echo "<td>".$row['BIAYA_PEMANEN_RP_PREMI_JANJANG']."</td>";
		echo "<td>".$row['INCENTIVE']."</td>";
		echo "<td>".$row['BIAYA_PEMANEN_RP_PREMI_BRD']."</td>";
		echo "<td>".$row['BIAYA_PEMANEN_RP_TOTAL']."</td>";
		echo "<td>".$row['BIAYA_PEMANEN_RP_KG']."</td>";
		echo "<td>".$row['BIAYA_SPV_RP_BASIS']."</td>";
		echo "<td>".$row['BIAYA_SPV_RP_PREMI']."</td>";
		echo "<td>".$row['BIAYA_SPV_RP_TOTAL']."</td>";
		echo "<td>".$row['BIAYA_SPV_RP_KG']."</td>";
		echo "<td>".$row['BIAYA_ALAT_PANEN_RP_KG']."</td>";
		echo "<td>".$row['BIAYA_ALAT_PANEN_RP_TOTAL']."</td>";
		echo "<td>".$row['TUKANG_MUAT_BASIS']."</td>";
		echo "<td>".$row['TUKANG_MUAT_PREMI']."</td>";
		echo "<td>".$row['TUKANG_MUAT_TOTAL']."</td>";
		echo "<td>".$row['TUKANG_MUAT_RP_KG']."</td>";
		echo "<td>".$row['SUPIR_PREMI']."</td>";
		echo "<td>".$row['SUPIR_RP_KG']."</td>";
		echo "<td>".$row['ANGKUT_TBS_RP_KG_KM']."</td>";
		echo "<td>".$row['ANGKUT_TBS_RP_ANGKUT']."</td>";
		echo "<td>".$row['ANGKUT_TBS_RP_KG']."</td>";
		echo "<td>".$row['KRANI_BUAH_BASIS']."</td>";
		echo "<td>".$row['KRANI_BUAH_PREMI']."</td>";
		echo "<td>".$row['KRANI_BUAH_TOTAL']."</td>";
		echo "<td>".$row['KRANI_BUAH_RP_KG']."</td>";
		echo "<td>".$row['LANGSIR_TON']."</td>";
		echo "<td>".$row['LANGSIR_RP']."</td>";
		echo "<td>".$row['LANGSIR_TUKANG_MUAT']."</td>";
		echo "<td>".$row['LANGSIR_RP_KG']."</td>";
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
		echo "<td>".$row['COST_SETAHUN']."</td>";
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
