<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_perencanaan_produksi_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>PERENCANAAN PRODUKSI</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>AFD CODE</td>
	<td>BLOCK - DESC</td>
	<td>HA PLANTED</td>
	<td>JARAK KE PKS</td>
	<td>% LANGSIR</td>
	<td>POKOK PRODUKTIF</td>
	<td>SPH PRODUKTIF</td>
	<td>TON AKTUAL</td>
	<td>JANJANG AKTUAL</td>
	<td>BJR AKTUAL</td>
	<td>YPH AKTUAL</td>
	<td>TON TAKSASI</td>
	<td>JANJANG TAKSASI</td>
	<td>BJR TAKSASI</td>
	<td>YPH TAKSASI</td>
	<td>ANTISIPASI TON</td>
	<td>ANTISIPASI JJG</td>
	<td>ANTISIPASI BJR</td>
	<td>ANTISIPASI YPH</td>
	<td>BUDGET TON</td>
	<td>BUDGET YPH</td>
	<td>VAR YPH</td>
	<td>PERIODE BUDGET SMS1 HA</td>
	<td>PERIODE BUDGET SMS1 POKOK</td>
	<td>PERIODE BUDGET SMS1 SPH</td>
	<td>PERIODE BUDGET SMS2 HA</td>
	<td>PERIODE BUDGET SMS2 POKOK</td>
	<td>PERIODE BUDGET SMS2 SPH</td>
	<td>YIELD PROFILE YPH</td>
	<td>YIELD PROFILE TON</td>
	<td>POTENSI YPH</td>
	<td>POTENSI TON</td>
	<td>BUDGET JJG</td>
	<td>BUDGET BJR</td>
	<td>BUDGET TON</td>
	<td>BUDGET YPH</td>
	<td>DISTRIBUSI JAN</td>
	<td>DISTRIBUSI FEB</td>
	<td>DISTRIBUSI MAR</td>
	<td>DISTRIBUSI APR</td>
	<td>DISTRIBUSI MEI</td>
	<td>DISTRIBUSI JUN</td>
	<td>DISTRIBUSI JUL</td>
	<td>DISTRIBUSI AGS</td>
	<td>DISTRIBUSI SEP</td>
	<td>DISTRIBUSI OKT</td>
	<td>DISTRIBUSI NOV</td>
	<td>DISTRIBUSI DES</td>
	<td>DISTRIBUSI SMS1</td>
	<td>DISTRIBUSI SMS2</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['AFD_CODE']."</td>";
		echo "<td>".$row['BLOCK_CODE']." - ".$row['BLOCK_DESC']."</td>";
		echo "<td>".$row['HA_PANEN']."</td>";
		echo "<td>".$row['JARAK_PKS']."</td>";
		echo "<td>".$row['PERSEN_LANGSIR']."</td>";
		echo "<td>".$row['POKOK_PRODUKTIF']."</td>";
		echo "<td>".$row['SPH_PRODUKTIF']."</td>";
		echo "<td>".$row['TON_AKTUAL']."</td>";
		echo "<td>".$row['JANJANG_AKTUAL']."</td>";
		echo "<td>".$row['BJR_AKTUAL']."</td>";
		echo "<td>".$row['YPH_AKTUAL']."</td>";
		echo "<td>".$row['TON_TAKSASI']."</td>";
		echo "<td>".$row['JANJANG_TAKSASI']."</td>";
		echo "<td>".$row['BJR_TAKSASI']."</td>";
		echo "<td>".$row['YPH_TAKSASI']."</td>";
		echo "<td>".$row['TON_ANTISIPASI']."</td>";
		echo "<td>".$row['JANJANG_ANTISIPASI']."</td>";
		echo "<td>".$row['BJR_ANTISIPASI']."</td>";
		echo "<td>".$row['YPH_ANTISIPASI']."</td>";
		echo "<td>".$row['TON_BUDGET_TAHUN_BERJALAN']."</td>";
		echo "<td>".$row['YPH_BUDGET_TAHUN_BERJALAN']."</td>";
		echo "<td>".$row['VAR_YPH']."</td>";
		echo "<td>".$row['HA_SMS1']."</td>";
		echo "<td>".$row['POKOK_SMS1']."</td>";
		echo "<td>".$row['SPH_SMS1']."</td>";
		echo "<td>".$row['HA_SMS2']."</td>";
		echo "<td>".$row['POKOK_SMS2']."</td>";
		echo "<td>".$row['SPH_SMS2']."</td>";
		echo "<td>".$row['YPH_PROFILE']."</td>";
		echo "<td>".$row['TON_PROFILE']."</td>";
		echo "<td>".$row['YPH_PROPORTION']."</td>";
		echo "<td>".$row['TON_PROPORTION']."</td>";
		echo "<td>".$row['JANJANG_BUDGET']."</td>";
		echo "<td>".$row['BJR_BUDGET']."</td>";
		echo "<td>".$row['TON_BUDGET']."</td>";
		echo "<td>".$row['YPH_BUDGET']."</td>";
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
		echo "<td>".$row['SMS1']."</td>";
		echo "<td>".$row['SMS2']."</td>";
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