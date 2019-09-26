<?php
// ================================== JANGAN DIUBAH ==================================
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment;Filename=data_norma_distribusi_vra_non_infra_".date("YmdHis").".xls");

//header
echo "
<html>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">
<body>";
// ================================== JANGAN DIUBAH ==================================

// TITLE DOKUMEN
echo "
<div style='font-weight:bolder; font-size:20px;'>DISTRIBUSI VRA - NON INFRA</div><br>
";

// HEADER TABLE
echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>KODE VRA</td>
	<td>TIPE VRA</td>
	<td>UOM</td>";
foreach($this->data['tabs'] as $idx => $row){
	echo "<td>".$row['LOCATION_CODE']."</td>";
}
echo "
	<td>BIBITAN</td>
	<td>BASE CAMP</td>
	<td>UMUM</td>
	<td>LAINNYA</td>
	<td>TOTAL</td>
	<td>TOTAL COST</td>
</tr>
";


//data
if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['DESCRIPTION']."</td>";
		echo "<td>".$row['VRA_CODE']."</td>";
		echo "<td>".$row['VRA_SUB_CAT_DESCRIPTION']."</td>";
		echo "<td>".$row['UOM']."</td>";
		
		//afd loop heres
		$bibitan=0;$basecamp=0;$umum=0;$lain=0;$other=0;
		$totalHm=0;$totalHmPrice=0;
		foreach($this->data['rowsAfd'] as $idx => $rowAfds){
			foreach($rowAfds as $idx => $rowAfd){
				if($rowAfd['TRX_CODE']==$row['TRX_CODE']){
					if($rowAfd['LOCATION_CODE']=='BIBITAN'){
						$bibitan=$rowAfd['HM_KM'];
					}elseif($rowAfd['LOCATION_CODE']=='BASECAMP'){
						$basecamp=$rowAfd['HM_KM'];
					}elseif($rowAfd['LOCATION_CODE']=='UMUM'){
						$umum=$rowAfd['HM_KM'];
					}elseif($rowAfd['LOCATION_CODE']=='LAIN'){
						$lain=$rowAfd['HM_KM'];
					}else{
						// echo "<td>";
						$other=$rowAfd['HM_KM'];
						foreach($this->data['tabs'] as $tdx) {
							if($rowAfd['LOCATION_CODE'] == $tdx['LOCATION_CODE']) {
							  echo "<td>".$other."</td>";
							}
						}
					}
					$totalHm 		= $totalHm+$rowAfd['HM_KM'];
					$totalHmPrice	= $totalHmPrice+$rowAfd['PRICE_HM_KM'];
				}
			}
		}
		
		echo "<td>".$bibitan."</td>";
		echo "<td>".$basecamp."</td>";
		echo "<td>".$umum."</td>";
		echo "<td>".$lain."</td>";
		echo "<td>".$totalHm."</td>";
		echo "<td>".$totalHmPrice."</td>";		
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
