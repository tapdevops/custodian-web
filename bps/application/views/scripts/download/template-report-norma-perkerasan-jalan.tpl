<?php

//header('Content-type: application/ms-excel');
//header("Content-Disposition: attachment;Filename=template_harga_perkerasan_jalan_".date("YmdHis").".csv");

$file = "template_harga_perkerasan_jalan_".date("YmdHis").".csv";
$fp = fopen($file, "w");
	$judul[] = 'PERIODE BUDGET';
	$judul[] = 'BA CODE';
	$judul[] = 'KODE AKTIVITAS';
	$judul[] = 'DESKRIPSI AKTIVITAS';
	$judul[] = 'REGION CODE';
	$judul[] = 'MIN JARAK (KM)';
	$judul[] = 'MAX JARAK (KM)';
	$judul[] = 'JARAK AVG (KM)';
	$judul[] = 'JARAK PP (KM)';
	$judul[] = 'JARAK RANGE';
	$judul[] = 'JUMLAH MATERIAL';
	$judul[] = 'TRIP MATERIAL';
	$judul[] = 'BIAYA MATERIAL';
	$judul[] = 'DT TRIP';
	$judul[] = 'DT PRICE';
	$judul[] = 'EXCAV HM';
	$judul[] = 'EXCAV PRICE';
	$judul[] = 'COMPACTOR HM';
	$judul[] = 'COMPACTOR PRICE';
	$judul[] = 'GRADER HM';
	$judul[] = 'GRADER PRICE';
	$judul[] = 'INTERNAL PRICE';
	$judul[] = 'EXTERNAL PERCENT';
	$judul[] = 'EXTERNAL BENEFIT';
	$judul[] = 'EXTERNAL PRICE';
	fputcsv($fp, $judul);
	if($this->data['count'] > 0){
		
		foreach($this->data['rows'] as $idx => $row){
			$isi = array();
			$jarak = explode("-", $row['PARAMETER_VALUE']);
			$isi[] = $row['PERIOD_BUDGET'];
			$isi[] = $row['BA_CODE'];
			$isi[] = $row['ACTIVITY_CODE'];
			$isi[] = $row['DESCRIPTION'];
			$isi[] = $this->data['params']['src_region_code'];
			$isi[] = str_replace(" ", "", $jarak[0]);
			$isi[] = str_replace(" ", "", $jarak[1]);
			$isi[] = $row['JARAK_AVG'];
			$isi[] = $row['JARAK_PP'];
			$isi[] = $row['JARAK_RANGE'];
			$isi[] = $row['MATERIAL_QTY'];
			$isi[] = $row['TRIP_MATERIAL'];
			$isi[] = $row['BIAYA_MATERIAL'];
			$isi[] = $row['DT_TRIP'];
			$isi[] = $row['DT_PRICE'];
			$isi[] = $row['EXCAV_HM'];
			$isi[] = $row['EXCAV_PRICE'];
			$isi[] = $row['COMPACTOR_HM'];
			$isi[] = $row['COMPACTOR_PRICE'];
			$isi[] = $row['GRADER_HM'];
			$isi[] = $row['GRADER_PRICE'];
			$isi[] = $row['INTERNAL_PRICE'];
			$isi[] = $row['EXTERNAL_PERCENT'];
			$isi[] = $row['EXTERNAL_BENEFIT'];
			$isi[] = 0;
			fputcsv($fp, $isi);
		}
		
	}
	
fclose($fp);
if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    
}
unlink($file);


// HEADER TABLE
/* echo "
<table border=1>
<tr style='background:#999; color:#FFF; font-weight:bolder; text-align:center; vertical-align:middle;'>
	<td>PERIODE BUDGET</td>
	<td>BA CODE</td>
	<td>KODE AKTIVITAS</td>
	<td>DESKRIPSI AKTIVITAS</td>
	<td>MIN JARAK (KM)</td>
	<td>MAX JARAK (KM)</td>
	<td>JARAK AVG (KM)</td>
	<td>JARAK PP (KM)</td>
	<td>JUMLAH MATERIAL</td>
	<td>TRIP MATERIAL</td>
	<td>BIAYA MATERIAL</td>
	<td>DT TRIP</td>
	<td>DT PRICE</td>
	<td>EXCAV HM</td>
	<td>EXCAV PRICE</td>
	<td>COMPACTOR HM</td>
	<td>COMPACTOR PRICE</td>
	<td>GRADER HM</td>
	<td>GRADER PRICE</td>
	<td>INTERNAL PRICE</td>
	<td>EXTERNAL PERCENT</td>
	<td>EXTERNAL BENEFIT</td>
	<td>EXTERNAL PRICE</td>
</tr>
"; */

//data
/* if($this->data['count'] > 0){
	foreach($this->data['rows'] as $idx => $row){
		$jarak = explode("-", $row['PARAMETER_VALUE']);
		
		echo "<tr style='vertical-align:top;'>";
		echo "<td>".$row['PERIOD_BUDGET']."</td>";
		echo "<td>".$row['BA_CODE']."</td>";
		echo "<td>".$row['ACTIVITY_CODE']."</td>";
		echo "<td>".$row['DESCRIPTION']."</td>";
		echo "<td>".str_replace(" ", "", $jarak[0])."</td>";
		echo "<td>".str_replace(" ", "", $jarak[1])."</td>";
		echo "<td>".$row['JARAK_AVG']."</td>";
		echo "<td>".$row['JARAK_PP']."</td>";
		echo "<td>".$row['MATERIAL_QTY']."</td>";
		echo "<td>".$row['TRIP_MATERIAL']."</td>";
		echo "<td>".$row['BIAYA_MATERIAL']."</td>";
		echo "<td>".$row['DT_TRIP']."</td>";
		echo "<td>".$row['DT_PRICE']."</td>";
		echo "<td>".$row['EXCAV_HM']."</td>";
		echo "<td>".$row['EXCAV_PRICE']."</td>";
		echo "<td>".$row['COMPACTOR_HM']."</td>";
		echo "<td>".$row['COMPACTOR_PRICE']."</td>";
		echo "<td>".$row['GRADER_HM']."</td>";
		echo "<td>".$row['GRADER_PRICE']."</td>";
		echo "<td>".$row['INTERNAL_PRICE']."</td>";
		echo "<td>".$row['EXTERNAL_PERCENT']."</td>";
		echo "<td>".$row['EXTERNAL_BENEFIT']."</td>";
		echo "<td>0</td>";
		echo "</tr>";
	}
}

echo"
</table>";

echo"
</body>
</html>
"; */
?>