<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_report_vra_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>REPORT VRA</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>SUB KATEGORI VRA</td>
	<td>KODE VRA</td>
	<td>TIPE VRA</td>
	<td>DESKRIPSI VRA TYPE</td>
	<td>JUMLAH ALAT</td>
	<td>TAHUN ALAT</td>
	<td>UOM</td>
	<td>STANDAR KERJA<BR>QTY/HARI</td>
	<td>STANDAR KERJA<BR>HARI VRA/TAHUN</td>
	<td>STANDAR KERJA<BR>QTY/TAHUN</td>
	<td>STANDAR KERJA<BR>TOTAL QTY/TAHUN</td>
	<td>GAJI+TUNJ OPERATOR<BR>TK</td>
	<td>GAJI+TUNJ OPERATOR<BR>GP/BULAN</td>
	<td>GAJI+TUNJ OPERATOR<BR>TOTAL GP/BULAN</td>
	<td>GAJI+TUNJ OPERATOR<BR>TUNJ/BULAN</td>
	<td>GAJI+TUNJ OPERATOR<BR>TOTAL TUNJ/BULAN</td>
	<td>GAJI+TUNJ OPERATOR<BR>TOTAL GAJI & TUNJ</td>
	<td>GAJI+TUNJ OPERATOR<BR>RP/QTY</td>
	<td>GAJI+TUNJ HELPER<BR>TK</td>
	<td>GAJI+TUNJ HELPER<BR>GP/BULAN</td>
	<td>GAJI+TUNJ HELPER<BR>TOTAL GP/BULAN</td>
	<td>GAJI+TUNJ HELPER<BR>TUNJ/BULAN</td>
	<td>GAJI+TUNJ HELPER<BR>TOTAL TUNJ/BULAN</td>
	<td>GAJI+TUNJ HELPER<BR>TOTAL GAJI & TUNJ</td>
	<td>GAJI+TUNJ HELPER<BR>RP/QTY</td>
	<td>PAJAK QTY/SAT</td>
	<td>PAJAK HARGA</td>
	<td>PAJAK RP/QTY</td>
	<td>RENTAL QTY/SAT</td>
	<td>RENTAL HARGA</td>
	<td>RENTAL RP/QTY</td>
	<td>BAHAN BAKAR<BR>QTY/SAT</td>
	<td>BAHAN BAKAR<BR>HARGA</td>
	<td>BAHAN BAKAR<BR>RP/QTY</td>
	<td>OLI MESIN<BR>QTY/SAT</td>
	<td>OLI MESIN<BR>HARGA</td>
	<td>OLI MESIN<BR>RP/QTY</td>
	<td>OLI TRANSMISI<BR>QTY/SAT</td>
	<td>OLI TRANSMISI<BR>HARGA</td>
	<td>OLI TRANSMISI<BR>RP/QTY</td>
	<td>MINYAK HYDROLIC<BR>QTY/SAT</td>
	<td>MINYAK HYDROLIC<BR>HARGA</td>
	<td>MINYAK HYDROLIC<BR>RP/QTY</td>
	<td>GREASE<BR>QTY/SAT</td>
	<td>GREASE<BR>HARGA</td>
	<td>GREASE<BR>RP/QTY</td>
	<td>FILTER OLI<BR>QTY/SAT</td>
	<td>FILTER OLI<BR>HARGA</td>
	<td>FILTER OLI<BR>RP/QTY</td>
	<td>FILTER HYDROLIC<BR>QTY/SAT</td>
	<td>FILTER HYDROLIC<BR>HARGA</td>
	<td>FILTER HYDROLIC<BR>RP/QTY</td>
	<td>FILTER SOLAR<BR>QTY/SAT</td>
	<td>FILTER SOLAR<BR>HARGA</td>
	<td>FILTER SOLAR<BR>RP/QTY</td>
	<td>FILTER SOLAR<BR>MOISTURE SEPARATOR<BR>QTY/SAT</td>
	<td>FILTER SOLAR<BR>MOISTURE SEPARATOR<BR>HARGA</td>
	<td>FILTER SOLAR<BR>MOISTURE SEPARATOR<BR>RP/QTY</td>
	<td>FILTER UDARA<BR>QTY/SAT</td>
	<td>FILTER UDARA<BR>HARGA</td>
	<td>FILTER UDARA<BR>RP/QTY</td>
	<td>GANTI SPAREPART<BR>QTY/SAT</td>
	<td>GANTI SPAREPART<BR>HARGA</td>
	<td>GANTI SPAREPART<BR>RP/QTY</td>
	<td>GANTI BAN LUAR<BR>QTY/SAT</td>
	<td>GANTI BAN LUAR<BR>HARGA</td>
	<td>GANTI BAN LUAR<BR>RP/QTY</td>
	<td>GANTI BAN DALAM<BR>QTY/SAT</td>
	<td>GANTI BAN DALAM<BR>HARGA</td>
	<td>GANTI BAN DALAM<BR>RP/QTY</td>
	<td>SERVIS WORKSHOP<BR>QTY/SAT</td>
	<td>SERVIS WORKSHOP<BR>HARGA</td>
	<td>SERVIS WORKSHOP<BR>RP/QTY</td>
	<td>OVERHAUL<BR>QTY/SAT</td>
	<td>OVERHAUL<BR>HARGA</td>
	<td>OVERHAUL<BR>RP/QTY</td>
	<td>SERVIS BENGKEL LUAR<BR>QTY/SAT</td>
	<td>SERVIS BENGKEL LUAR<BR>HARGA</td>
	<td>SERVIS BENGKEL LUAR<BR>RP/QTY</td>
	<td>TOTAL BIAYA</td>
	<td>TOTAL RP/QTY</td>
	<td>RP/QTY/TIPE VRA</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['VRA_SUB_CAT_DESCRIPTION']."</td>";
		echo "<td>".$row['VRA_CODE']."</td>";
        echo "<td>".$row['VRA_TYPE']."</td>";
        echo "<td>".$row['DESCRIPTION_VRA_TYPE']."</td>";
		echo "<td>".$row['JUMLAH_ALAT']."</td>";
		echo "<td>".$row['TAHUN_ALAT']."</td>";
		echo "<td>".$row['UOM']."</td>";
		echo "<td>".$row['QTY_DAY']."</td>";
		echo "<td>".$row['DAY_YEAR_VRA']."</td>";
		echo "<td>".$row['QTY_YEAR']."</td>";
		echo "<td>".$row['TOTAL_QTY_TAHUN']."</td>";
		echo "<td>".$row['JUMLAH_OPERATOR']."</td>";
		echo "<td>".$row['GAJI_OPERATOR']."</td>";
		echo "<td>".$row['TOTAL_GAJI_OPERATOR']."</td>";
		echo "<td>".$row['TUNJANGAN_OPERATOR']."</td>";
		echo "<td>".$row['TOTAL_TUNJANGAN_OPERATOR']."</td>";
		echo "<td>".$row['TOTAL_GAJI_TUNJANGAN_OPERATOR']."</td>";
		echo "<td>".$row['RP_QTY_OPERATOR']."</td>";
		echo "<td>".$row['JUMLAH_HELPER']."</td>";
		echo "<td>".$row['GAJI_HELPER']."</td>";
		echo "<td>".$row['TOTAL_GAJI_HELPER']."</td>";
		echo "<td>".$row['TUNJANGAN_HELPER']."</td>";
		echo "<td>".$row['TOTAL_TUNJANGAN_HELPER']."</td>";
		echo "<td>".$row['TOTAL_GAJI_TUNJANGAN_HELPER']."</td>";
		echo "<td>".$row['RP_QTY_HELPER']."</td>";
		echo "<td>".$row['RVRA1_VALUE1']."</td>";
		echo "<td>".$row['RVRA1_VALUE2']."</td>";
		echo "<td>".$row['RVRA1_VALUE3']."</td>";
		echo "<td>".$row['RVRA17_VALUE1']."</td>";
		echo "<td>".$row['RVRA17_VALUE2']."</td>";
		echo "<td>".$row['RVRA17_VALUE3']."</td>";
		echo "<td>".$row['RVRA2_VALUE1']."</td>";
		echo "<td>".$row['RVRA2_VALUE2']."</td>";
		echo "<td>".$row['RVRA2_VALUE3']."</td>";
		echo "<td>".$row['RVRA3_VALUE1']."</td>";
		echo "<td>".$row['RVRA3_VALUE2']."</td>";
		echo "<td>".$row['RVRA3_VALUE3']."</td>";
		echo "<td>".$row['RVRA4_VALUE1']."</td>";
		echo "<td>".$row['RVRA4_VALUE2']."</td>";
		echo "<td>".$row['RVRA4_VALUE3']."</td>";
		echo "<td>".$row['RVRA5_VALUE1']."</td>";
		echo "<td>".$row['RVRA5_VALUE2']."</td>";
		echo "<td>".$row['RVRA5_VALUE3']."</td>";
		echo "<td>".$row['RVRA6_VALUE1']."</td>";
		echo "<td>".$row['RVRA6_VALUE2']."</td>";
		echo "<td>".$row['RVRA6_VALUE3']."</td>";
		echo "<td>".$row['RVRA7_VALUE1']."</td>";
		echo "<td>".$row['RVRA7_VALUE2']."</td>";
		echo "<td>".$row['RVRA7_VALUE3']."</td>";
		echo "<td>".$row['RVRA8_VALUE1']."</td>";
		echo "<td>".$row['RVRA8_VALUE2']."</td>";
		echo "<td>".$row['RVRA8_VALUE3']."</td>";
		echo "<td>".$row['RVRA9_VALUE1']."</td>";
		echo "<td>".$row['RVRA9_VALUE2']."</td>";
		echo "<td>".$row['RVRA9_VALUE3']."</td>";
		echo "<td>".$row['RVRA10_VALUE1']."</td>";
		echo "<td>".$row['RVRA10_VALUE2']."</td>";
		echo "<td>".$row['RVRA10_VALUE3']."</td>";
		echo "<td>".$row['RVRA11_VALUE1']."</td>";
		echo "<td>".$row['RVRA11_VALUE2']."</td>";
		echo "<td>".$row['RVRA11_VALUE3']."</td>";
		echo "<td>".$row['RVRA12_VALUE1']."</td>";
		echo "<td>".$row['RVRA12_VALUE2']."</td>";
		echo "<td>".$row['RVRA12_VALUE3']."</td>";
		echo "<td>".$row['RVRA13_VALUE1']."</td>";
		echo "<td>".$row['RVRA13_VALUE2']."</td>";
		echo "<td>".$row['RVRA13_VALUE3']."</td>";
		echo "<td>".$row['RVRA14_VALUE1']."</td>";
		echo "<td>".$row['RVRA14_VALUE2']."</td>";
		echo "<td>".$row['RVRA14_VALUE3']."</td>";
		echo "<td>".$row['RVRA15_VALUE1']."</td>";
		echo "<td>".$row['RVRA15_VALUE2']."</td>";
		echo "<td>".$row['RVRA15_VALUE3']."</td>";
		echo "<td>".$row['RVRA16_VALUE1']."</td>";
		echo "<td>".$row['RVRA16_VALUE2']."</td>";
		echo "<td>".$row['RVRA16_VALUE3']."</td>";
		echo "<td>".$row['RVRA18_VALUE1']."</td>";
		echo "<td>".$row['RVRA18_VALUE2']."</td>";
		echo "<td>".$row['RVRA18_VALUE3']."</td>";
		echo "<td>".$row['TOTAL_BIAYA']."</td>";
		echo "<td>".$row['TOTAL_RP_QTY']."</td>";
		echo "<td>".$row['RP_QTY_VRA_TYPE']."</td>";
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