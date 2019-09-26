<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_report_checkroll_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>REPORT CHECKROLL DAN PK UMUM</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>JOB CODE</td>
	<td>GRUP CR</td>
	<td>DESKRIPSI</td>
	<td>EMPLOYEE STATUS</td>
	<td>KENAIKAN GP(%)</td>
	<td>GP</td>
	<td>MPP PERIOD BUDGET</td>
	<td>TOTAL GP MPP</td>
	<td>ASTEK</td>
	<td>CATU</td>
	<td>JABATAN</td>
	<td>KEHADIRAN</td>
	<td>LAINNYA</td>
	<td>PPH21</td>
	<td>TOTAL GAJI + TUNJANGAN</td>
	<td>RP/HK/BULAN</td>
	<td>BONUS</td>
	<td>HHR</td>
	<td>OBAT</td>
	<td>THR</td>
	<td>TOTAL TUNJANGAN PK UMUM</td>
	<td>DIS YEAR</td>
	<td>DIS JAN</td>
	<td>DIS FEB</td>
	<td>DIS MAR</td>
	<td>DIS APR</td>
	<td>DIS MAY</td>
	<td>DIS JUN</td>
	<td>DIS JUL</td>
	<td>DIS AUG</td>
	<td>DIS SEP</td>
	<td>DIS OCT</td>
	<td>DIS NOV</td>
	<td>DIS DEC</td>
</tr>
";

//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		if (($jobcode != "") && (( $jobcode != $row['JOB_CODE'] ) || ( $bacode != $row['BA_CODE'] ))) {
			$rp_hk = ($total_mpp) ? ($total_rp_hk/$total_mpp) : 0;
			echo "<tr style='vertical-align:top; background:#CCC;'>";
			echo "<td colspan='7'>SUB TOTAL</td>";
			echo "<td>".$GP_INFLASI."</td>";
			echo "<td>".$MPP_PERIOD_BUDGET."</td>";
			echo "<td>".$TOTAL_GP_MPP."</td>";
			echo "<td>".$ASTEK."</td>";
			echo "<td>".$CATU."</td>";
			echo "<td>".$JABATAN."</td>";
			echo "<td>".$KEHADIRAN."</td>";
			echo "<td>".$LAINNYA."</td>";
			echo "<td>".$PPH_21."</td>";
			echo "<td>".$TOTAL_GAJI_TUNJANGAN."</td>";
			echo "<td>".$rp_hk."</td>";
			echo "<td>".$BONUS."</td>";
			echo "<td>".$HHR."</td>";
			echo "<td>".$OBAT."</td>";
			echo "<td>".$THR."</td>";
			echo "<td>".$TOTAL_TUNJANGAN_PK_UMUM."</td>";
			echo "<td>".$YEAR."</td>";
			echo "<td>".$DIS_JAN."</td>";
			echo "<td>".$DIS_FEB."</td>";
			echo "<td>".$DIS_MAR."</td>";
			echo "<td>".$DIS_APR."</td>";
			echo "<td>".$DIS_MAY."</td>";
			echo "<td>".$DIS_JUN."</td>";
			echo "<td>".$DIS_JUL."</td>";
			echo "<td>".$DIS_AUG."</td>";
			echo "<td>".$DIS_SEP."</td>";
			echo "<td>".$DIS_OCT."</td>";
			echo "<td>".$DIS_NOV."</td>";
			echo "<td>".$DIS_DEC."</td>";
			echo "</tr>";
						
		
			//reset data
			$GP_INFLASI = 0;
			$MPP_PERIOD_BUDGET = 0;
			$TOTAL_GP_MPP = 0;
			$PPH_21 = 0;
			$ASTEK = 0;
			$JABATAN = 0;
			$KEHADIRAN = 0;
			$LAINNYA = 0;
			$CATU = 0;
			$TOTAL_GAJI_TUNJANGAN = 0;
			$RP_HK_PERBULAN = 0;
			$OBAT = 0;
			$THR = 0;
			$HHR = 0;
			$BONUS = 0;
			$TOTAL_TUNJANGAN_PK_UMUM = 0;
			$YEAR = 0;
			$DIS_JAN = 0;
			$DIS_FEB = 0;
			$DIS_MAR = 0;
			$DIS_APR = 0;
			$DIS_MAY = 0;
			$DIS_JUN = 0;
			$DIS_JUL = 0;
			$DIS_AUG = 0;
			$DIS_SEP = 0;
			$DIS_OCT = 0;
			$DIS_NOV = 0;
			$DIS_DEC = 0;
			$countData = 0;
			$total_rp_hk = 0;
			$total_mpp = 0;
			$showSubTotal = 1;
		}else{
			$showSubTotal = 0;
		}
		
		//total per BA
		if (($bacode != "") && ( $bacode != $row['BA_CODE'] )) {
			$rp_hk = ($grand_total_mpp) ? ($grand_total_rp_hk/$grand_total_mpp) : 0;
			echo "<tr style='vertical-align:top; background:red;'>";
			echo "<td colspan='7'>GRAND TOTAL</td>";
			echo "<td>".$GRANDTOTAL_GP_INFLASI."</td>";
			echo "<td>".$GRANDTOTAL_MPP_PERIOD_BUDGET."</td>";
			echo "<td>".$GRANDTOTAL_TOTAL_GP_MPP."</td>";
			echo "<td>".$GRANDTOTAL_ASTEK."</td>";
			echo "<td>".$GRANDTOTAL_CATU."</td>";
			echo "<td>".$GRANDTOTAL_JABATAN."</td>";
			echo "<td>".$GRANDTOTAL_KEHADIRAN."</td>";
			echo "<td>".$GRANDTOTAL_LAINNYA."</td>";
			echo "<td>".$GRANDTOTAL_PPH_21."</td>";
			echo "<td>".$GRANDTOTAL_TOTAL_GAJI_TUNJANGAN."</td>";
			echo "<td>".$rp_hk."</td>";
			echo "<td>".$GRANDTOTAL_BONUS."</td>";
			echo "<td>".$GRANDTOTAL_HHR."</td>";
			echo "<td>".$GRANDTOTAL_OBAT."</td>";
			echo "<td>".$GRANDTOTAL_THR."</td>";
			echo "<td>".$GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM."</td>";
			echo "<td>".$GRANDTOTAL_YEAR."</td>";
			echo "<td>".$GRANDTOTAL_DIS_JAN."</td>";
			echo "<td>".$GRANDTOTAL_DIS_FEB."</td>";
			echo "<td>".$GRANDTOTAL_DIS_MAR."</td>";
			echo "<td>".$GRANDTOTAL_DIS_APR."</td>";
			echo "<td>".$GRANDTOTAL_DIS_MAY."</td>";
			echo "<td>".$GRANDTOTAL_DIS_JUN."</td>";
			echo "<td>".$GRANDTOTAL_DIS_JUL."</td>";
			echo "<td>".$GRANDTOTAL_DIS_AUG."</td>";
			echo "<td>".$GRANDTOTAL_DIS_SEP."</td>";
			echo "<td>".$GRANDTOTAL_DIS_OCT."</td>";
			echo "<td>".$GRANDTOTAL_DIS_NOV."</td>";
			echo "<td>".$GRANDTOTAL_DIS_DEC."</td>";
			echo "</tr>";
						
		
			//reset data
			$GRANDTOTAL_GP_INFLASI = 0;
			$GRANDTOTAL_MPP_PERIOD_BUDGET = 0;
			$GRANDTOTAL_TOTAL_GP_MPP = 0;
			$GRANDTOTAL_PPH_21 = 0;
			$GRANDTOTAL_ASTEK = 0;
			$GRANDTOTAL_JABATAN = 0;
			$GRANDTOTAL_KEHADIRAN = 0;
			$GRANDTOTAL_LAINNYA = 0;
			$GRANDTOTAL_CATU = 0;
			$GRANDTOTAL_TOTAL_GAJI_TUNJANGAN = 0;
			$GRANDTOTAL_RP_HK_PERBULAN = 0;
			$GRANDTOTAL_OBAT = 0;
			$GRANDTOTAL_THR = 0;
			$GRANDTOTAL_HHR = 0;
			$GRANDTOTAL_BONUS = 0;
			$GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM = 0;
			$GRANDTOTAL_YEAR = 0;
			$GRANDTOTAL_DIS_JAN = 0;
			$GRANDTOTAL_DIS_FEB = 0;
			$GRANDTOTAL_DIS_MAR = 0;
			$GRANDTOTAL_DIS_APR = 0;
			$GRANDTOTAL_DIS_MAY = 0;
			$GRANDTOTAL_DIS_JUN = 0;
			$GRANDTOTAL_DIS_JUL = 0;
			$GRANDTOTAL_DIS_AUG = 0;
			$GRANDTOTAL_DIS_SEP = 0;
			$GRANDTOTAL_DIS_OCT = 0;
			$GRANDTOTAL_DIS_NOV = 0;
			$GRANDTOTAL_DIS_DEC = 0;
			$GRANDTOTAL_countData = 0;
			$GRANDTOTAL_total_rp_hk = 0;
			$GRANDTOTAL_total_mpp = 0;
			$showSubTotal = 1;
		}else{
			$showSubTotal = 0;
		}
		
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['JOB_CODE']."</td>";
		echo "<td>".$row['GROUP_CHECKROLL_DESC']."</td>";
		echo "<td>".$row['JOB_DESCRIPTION']."</td>";
		echo "<td>".$row['EMPLOYEE_STATUS']."</td>";
		echo "<td>".$row['PERCENT_INCREASE']."</td>";
		echo "<td>".$row['GP_INFLASI']."</td>";
		echo "<td>".$row['MPP_PERIOD_BUDGET']."</td>";
		echo "<td>".$row['TOTAL_GP_MPP']."</td>";
		echo "<td>".$row['ASTEK']."</td>";
		echo "<td>".$row['CATU']."</td>";
		echo "<td>".$row['JABATAN']."</td>";
		echo "<td>".$row['KEHADIRAN']."</td>";
		echo "<td>".$row['LAINNYA']."</td>";
		echo "<td>".$row['PPH_21']."</td>";
		echo "<td>".$row['TOTAL_GAJI_TUNJANGAN']."</td>";
		echo "<td>".$row['RP_HK_PERBULAN']."</td>";
		echo "<td>".$row['BONUS']."</td>";
		echo "<td>".$row['HHR']."</td>";
		echo "<td>".$row['OBAT']."</td>";
		echo "<td>".$row['THR']."</td>";
		echo "<td>".$row['TOTAL_TUNJANGAN_PK_UMUM']."</td>";
		echo "<td>".$row['DIS_YEAR']."</td>";
		echo "<td>".$row['DIS_JAN']."</td>";
		echo "<td>".$row['DIS_FEB']."</td>";
		echo "<td>".$row['DIS_MAR']."</td>";
		echo "<td>".$row['DIS_APR']."</td>";
		echo "<td>".$row['DIS_MAY']."</td>";
		echo "<td>".$row['DIS_JUN']."</td>";
		echo "<td>".$row['DIS_JUL']."</td>";
		echo "<td>".$row['DIS_AUG']."</td>";
		echo "<td>".$row['DIS_SEP']."</td>";
		echo "<td>".$row['DIS_OCT']."</td>";
		echo "<td>".$row['DIS_NOV']."</td>";
		echo "<td>".$row['DIS_DEC']."</td>";
		echo "</tr>";
		
		//subtotal
		$GP_INFLASI += $row['GP_INFLASI'];
		$MPP_PERIOD_BUDGET += $row['MPP_PERIOD_BUDGET'];
		$TOTAL_GP_MPP += $row['TOTAL_GP_MPP'];
		$PPH_21 += $row['PPH_21'];
		$ASTEK += $row['ASTEK'];
		$JABATAN += $row['JABATAN'];
		$KEHADIRAN += $row['KEHADIRAN'];
		$LAINNYA += $row['LAINNYA'];
		$CATU += $row['CATU'];
		$TOTAL_GAJI_TUNJANGAN += $row['TOTAL_GAJI_TUNJANGAN'];
		$RP_HK_PERBULAN += $row['RP_HK_PERBULAN'];
		$OBAT += $row['OBAT'];
		$THR += $row['THR'];
		$HHR += $row['HHR'];
		$BONUS += $row['BONUS'];
		$TOTAL_TUNJANGAN_PK_UMUM += $row['TOTAL_TUNJANGAN_PK_UMUM'];
		$YEAR += $row['DIS_YEAR'];
		$DIS_JAN += $row['DIS_JAN'];
		$DIS_FEB += $row['DIS_FEB'];
		$DIS_MAR += $row['DIS_MAR'];
		$DIS_APR += $row['DIS_APR'];
		$DIS_MAY += $row['DIS_MAY'];
		$DIS_JUN += $row['DIS_JUN'];
		$DIS_JUL += $row['DIS_JUL'];
		$DIS_AUG += $row['DIS_AUG'];
		$DIS_SEP += $row['DIS_SEP'];
		$DIS_OCT += $row['DIS_OCT'];
		$DIS_NOV += $row['DIS_NOV'];
		$DIS_DEC += $row['DIS_DEC'];
		$countData += 1;
		
		$total_rp_hk += ( $row['RP_HK_PERBULAN'] * $row['MPP_PERIOD_BUDGET'] );
		$total_mpp += $row['MPP_PERIOD_BUDGET'];
		
		//grand total
		$GRANDTOTAL_GP_INFLASI += $row['GP_INFLASI'];
		$GRANDTOTAL_MPP_PERIOD_BUDGET += $row['MPP_PERIOD_BUDGET'];
		$GRANDTOTAL_TOTAL_GP_MPP += $row['TOTAL_GP_MPP'];
		$GRANDTOTAL_PPH_21 += $row['PPH_21'];
		$GRANDTOTAL_ASTEK += $row['ASTEK'];
		$GRANDTOTAL_JABATAN += $row['JABATAN'];
		$GRANDTOTAL_KEHADIRAN += $row['KEHADIRAN'];
		$GRANDTOTAL_LAINNYA += $row['LAINNYA'];
		$GRANDTOTAL_CATU += $row['CATU'];
		$GRANDTOTAL_TOTAL_GAJI_TUNJANGAN += $row['TOTAL_GAJI_TUNJANGAN'];
		$GRANDTOTAL_RP_HK_PERBULAN += $row['RP_HK_PERBULAN'];
		$GRANDTOTAL_OBAT += $row['OBAT'];
		$GRANDTOTAL_THR += $row['THR'];
		$GRANDTOTAL_HHR += $row['HHR'];
		$GRANDTOTAL_BONUS += $row['BONUS'];
		$GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM += $row['TOTAL_TUNJANGAN_PK_UMUM'];
		$GRANDTOTAL_YEAR += $row['DIS_YEAR'];
		$GRANDTOTAL_DIS_JAN += $row['DIS_JAN'];
		$GRANDTOTAL_DIS_FEB += $row['DIS_FEB'];
		$GRANDTOTAL_DIS_MAR += $row['DIS_MAR'];
		$GRANDTOTAL_DIS_APR += $row['DIS_APR'];
		$GRANDTOTAL_DIS_MAY += $row['DIS_MAY'];
		$GRANDTOTAL_DIS_JUN += $row['DIS_JUN'];
		$GRANDTOTAL_DIS_JUL += $row['DIS_JUL'];
		$GRANDTOTAL_DIS_AUG += $row['DIS_AUG'];
		$GRANDTOTAL_DIS_SEP += $row['DIS_SEP'];
		$GRANDTOTAL_DIS_OCT += $row['DIS_OCT'];
		$GRANDTOTAL_DIS_NOV += $row['DIS_NOV'];
		$GRANDTOTAL_DIS_DEC += $row['DIS_DEC'];
		$GRANDTOTAL_countData += 1;
		
		$grand_total_rp_hk += ( $row['RP_HK_PERBULAN'] * $row['MPP_PERIOD_BUDGET'] );
		$grand_total_mpp += $row['MPP_PERIOD_BUDGET'];
		
		$jobcode = $row['JOB_CODE'];
		$jobcode_grup = $row['GROUP_CHECKROLL_DESC'];
		$jobcode_desc = $row['JOB_DESCRIPTION'];
		$bacode = $row['BA_CODE'];
	}
	
	$rp_hk = ($total_mpp) ? ($total_rp_hk/$total_mpp) : 0;
	echo "<tr style='vertical-align:top; background:#CCC;'>";
	echo "<td colspan='7'>SUB TOTAL</td>";
	echo "<td>".$GP_INFLASI."</td>";
	echo "<td>".$MPP_PERIOD_BUDGET."</td>";
	echo "<td>".$TOTAL_GP_MPP."</td>";
	echo "<td>".$ASTEK."</td>";
	echo "<td>".$CATU."</td>";
	echo "<td>".$JABATAN."</td>";
	echo "<td>".$KEHADIRAN."</td>";
	echo "<td>".$LAINNYA."</td>";
	echo "<td>".$PPH_21."</td>";
	echo "<td>".$TOTAL_GAJI_TUNJANGAN."</td>";
	echo "<td>".$rp_hk."</td>";
	echo "<td>".$BONUS."</td>";
	echo "<td>".$HHR."</td>";
	echo "<td>".$OBAT."</td>";
	echo "<td>".$THR."</td>";
	echo "<td>".$TOTAL_TUNJANGAN_PK_UMUM."</td>";
	echo "<td>".$YEAR."</td>";
	echo "<td>".$DIS_JAN."</td>";
	echo "<td>".$DIS_FEB."</td>";
	echo "<td>".$DIS_MAR."</td>";
	echo "<td>".$DIS_APR."</td>";
	echo "<td>".$DIS_MAY."</td>";
	echo "<td>".$DIS_JUN."</td>";
	echo "<td>".$DIS_JUL."</td>";
	echo "<td>".$DIS_AUG."</td>";
	echo "<td>".$DIS_SEP."</td>";
	echo "<td>".$DIS_OCT."</td>";
	echo "<td>".$DIS_NOV."</td>";
	echo "<td>".$DIS_DEC."</td>";
	echo "</tr>";	
	
	//grand total
	$rp_hk = ($grand_total_mpp) ? ($grand_total_rp_hk/$grand_total_mpp) : 0;
	echo "<tr style='vertical-align:top; background:red;'>";
	echo "<td colspan='7'>GRAND TOTAL</td>";
	echo "<td>".$GRANDTOTAL_GP_INFLASI."</td>";
	echo "<td>".$GRANDTOTAL_MPP_PERIOD_BUDGET."</td>";
	echo "<td>".$GRANDTOTAL_TOTAL_GP_MPP."</td>";
	echo "<td>".$GRANDTOTAL_ASTEK."</td>";
	echo "<td>".$GRANDTOTAL_CATU."</td>";
	echo "<td>".$GRANDTOTAL_JABATAN."</td>";
	echo "<td>".$GRANDTOTAL_KEHADIRAN."</td>";
	echo "<td>".$GRANDTOTAL_LAINNYA."</td>";
	echo "<td>".$GRANDTOTAL_PPH_21."</td>";
	echo "<td>".$GRANDTOTAL_TOTAL_GAJI_TUNJANGAN."</td>";
	echo "<td>".$rp_hk."</td>";
	echo "<td>".$GRANDTOTAL_BONUS."</td>";
	echo "<td>".$GRANDTOTAL_HHR."</td>";
	echo "<td>".$GRANDTOTAL_OBAT."</td>";
	echo "<td>".$GRANDTOTAL_THR."</td>";
	echo "<td>".$GRANDTOTAL_TOTAL_TUNJANGAN_PK_UMUM."</td>";
	echo "<td>".$GRANDTOTAL_YEAR."</td>";
	echo "<td>".$GRANDTOTAL_DIS_JAN."</td>";
	echo "<td>".$GRANDTOTAL_DIS_FEB."</td>";
	echo "<td>".$GRANDTOTAL_DIS_MAR."</td>";
	echo "<td>".$GRANDTOTAL_DIS_APR."</td>";
	echo "<td>".$GRANDTOTAL_DIS_MAY."</td>";
	echo "<td>".$GRANDTOTAL_DIS_JUN."</td>";
	echo "<td>".$GRANDTOTAL_DIS_JUL."</td>";
	echo "<td>".$GRANDTOTAL_DIS_AUG."</td>";
	echo "<td>".$GRANDTOTAL_DIS_SEP."</td>";
	echo "<td>".$GRANDTOTAL_DIS_OCT."</td>";
	echo "<td>".$GRANDTOTAL_DIS_NOV."</td>";
	echo "<td>".$GRANDTOTAL_DIS_DEC."</td>";
	echo "</tr>";
}

echo"
</table>";

echo"
</body>
</html>
";
?>