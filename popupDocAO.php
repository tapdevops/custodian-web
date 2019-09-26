<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pick(symbol,row) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.adddetaildoc.txtTDLOAOD_DocumentCode<?= $_GET['row'] ?>.value = symbol;
	window.opener.document.adddetaildoc.docCode.value = window.opener.document.adddetaildoc.docCode.value + "\'" + symbol + "\'" +",";
	window.close();
	}
}
// -->
</SCRIPT>
<link href="./css/style.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<?PHP
$batas = 10;
$pg = isset( $_GET['pg'] ) ? $_GET['pg'] : "";

if ( empty( $pg ) ) {
	$posisi = 0;
	$pg = 1;
} else {
	$posisi = ( $pg - 1 ) * $batas;
}

$txtTHLOLD_CompanyID=$_GET['cID'];
$txtTHLOLD_DocumentGroupID=$_GET['gID'];

IF ($txtTHLOLD_DocumentGroupID=="4"){
	if ($_GET['recentCode']) $filter =" AND dao.DAO_DocCode NOT IN (".substr($_GET[recentCode],0, -1).")";
	if($txtTHLOLD_CompanyID == "COP"){
		$addfilter = "dao.DAO_Employee_NIK NOT LIKE '%CO@%'";
	}else{
		$query = "SELECT *
				  FROM M_Company
				  WHERE Company_ID='$txtTHLOLD_CompanyID'";
		$field = mysql_fetch_array(mysql_query($query));
		$Company_Code=$field['Company_Code'];
		$addfilter = "dao.DAO_Employee_NIK = 'CO@$Company_Code'";
	}

	$query="SELECT DISTINCT c.Company_Name,
				dao.DAO_RegTime, dao.DAO_DocCode,
				dao.DAO_Employee_NIK,
				mk.MK_Name, dao.DAO_Type, dao.DAO_Jenis, dao.DAO_NoPolisi, dao.DAO_NoRangka,
				dao.DAO_NoMesin, dao.DAO_NoBPKB, dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate, dao.DAO_Pajak_StartDate,
				dao.DAO_Pajak_ExpiredDate, dao.DAO_Lokasi_PT, dao.DAO_Region, dao.DAO_Keterangan
			FROM M_DocumentAssetOwnership dao
			LEFT JOIN db_master.M_MerkKendaraan mk
				ON dao.DAO_MK_ID=mk.MK_ID
			LEFT JOIN M_Company c
				ON dao.DAO_CompanyID=c.Company_ID
			WHERE
			$addfilter
			AND dao.DAO_Delete_Time IS NULL
			$filter
			ORDER BY dao.DAO_RegTime DESC
		";
	$limit = " LIMIT $posisi, $batas";
	$no = 1+$posisi;
	$lastQuery = $query.$limit;

	$sql = mysql_query($lastQuery);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		// echo $query;
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		if($txtTHLOLD_CompanyID == "COP"){
			$title_company_name = "Car Ownership Program (COP)";
		}else{
			$get_company_code = explode('CO@', $h_arr['DAO_Employee_NIK']);
			$company_code = $get_company_code[1];
			$query7="SELECT Company_Name AS nama_pemilik
				FROM M_Company
				WHERE Company_code='$company_code'";
			$sql7 = mysql_query($query7);
			$data7 = mysql_fetch_array($sql7);
			$title_company_name = $data7['nama_pemilik'];
		}
		echo "<div class=title><b>$title_company_name - Kepemilikan Aset</b></div>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
            <tr>
            	<th rowspan='2'>No.</th>
            	<th rowspan='2'>Nama Pemilik</th>
            	<th rowspan='2'>Merk Kendaraan</th>
            	<th rowspan='2'>Type</th>
            	<th rowspan='2'>Jenis</th>
            	<th rowspan='2'>No. Polisi</th>
            	<th rowspan='2'>No. Rangka</th>
            	<th rowspan='2'>No. Mesin</th>
            	<th rowspan='2'>No. BPKB</th>
            	<th colspan='2'>STNK</th>
            	<th colspan='2'>Pajak Kendaraan</th>
            	<th rowspan='2'>Lokasi (PT)</th>
            	<th rowspan='2'>Region</th>
            	<th rowspan='2'>Keterangan</th>
            </tr>
            <tr>
            	<th>Start Date</th>
            	<th>Expired Date</th>
            	<th>Start Date</th>
            	<th>Expired Date</th>
            </tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT c.Company_Name,
							dao.DAO_RegTime, dao.DAO_DocCode,
							dao.DAO_Employee_NIK,
							mk.MK_Name, dao.DAO_Type, dao.DAO_Jenis, dao.DAO_NoPolisi, dao.DAO_NoRangka,
							dao.DAO_NoMesin, dao.DAO_NoBPKB, dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate, dao.DAO_Pajak_StartDate,
							dao.DAO_Pajak_ExpiredDate, dao.DAO_Lokasi_PT, dao.DAO_Region, dao.DAO_Keterangan
						FROM M_DocumentAssetOwnership dao
						LEFT JOIN db_master.M_MerkKendaraan mk
							ON dao.DAO_MK_ID=mk.MK_ID
						LEFT JOIN M_Company c
							ON dao.DAO_CompanyID=c.Company_ID
						WHERE dao.DAO_Status ='1'
						AND dao.DAO_CompanyID='$txtTHLOLD_CompanyID'
						AND dao.DAO_GroupDocID='$txtTHLOLD_DocumentGroupID'
						AND dao.DAO_Delete_Time IS NULL
						$filter
						AND (
							dao.DAO_DocCode LIKE '%$search%'
							OR dao.DAO_Employee_NIK LIKE '%$search%'
							OR mk.MK_Name LIKE '%$search%'
							OR dao.DAO_Type LIKE '%$search%'
							OR dao.DAO_Jenis LIKE '%$search%'
							OR dao.DAO_NoPolisi LIKE '%$search%'
							OR dao.DAO_NoRangka LIKE '%$search%'
							OR dao.DAO_NoMesin LIKE '%$search%'
							OR dao.DAO_NoBPKB LIKE '%$search%'
							OR dao.DAO_STNK_StartDate LIKE '%$search%'
							OR dao.DAO_STNK_ExpiredDate LIKE '%$search%'
							OR dao.DAO_Pajak_StartDate LIKE '%$search%'
							OR dao.DAO_Pajak_ExpiredDate LIKE '%$search%'
							OR dao.DAO_Lokasi_PT LIKE '%$search%'
							OR dao.DAO_Region LIKE '%$search%'
							OR dao.DAO_Keterangan LIKE '%$search%'
						)
						ORDER BY dao.DAO_RegTime DESC ";
			$limit = " LIMIT $posisi, $batas";
			$sql = mysql_query($query.$limit);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$stnk_sdate=(strpos($arr['DAO_STNK_StartDate'], '0000-00-00') !== false || strpos($arr['DAO_STNK_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_STNK_StartDate']));
			$stnk_exdate=(strpos($arr['DAO_STNK_ExpiredDate'], '0000-00-00') !== false || strpos($arr['DAO_STNK_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_STNK_ExpiredDate']));

			$pajak_sdate=(strpos($arr['DAO_Pajak_StartDate'], '0000-00-00') !== false || strpos($arr['DAO_Pajak_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_Pajak_StartDate']));
			$pajak_exdate=(strpos($arr['DAO_Pajak_ExpiredDate'], '0000-00-00') !== false || strpos($arr['DAO_Pajak_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arr['DAO_Pajak_ExpiredDate']));

			if(strpos($arr['DAO_Employee_NIK'], 'CO@') !== false){
				$get_company_code = explode('CO@', $arr['DAO_Employee_NIK']);
				$company_code = $get_company_code[1];
				$query7="SELECT Company_Name AS nama_pemilik
					FROM M_Company
					WHERE Company_code='$company_code'";
			}else{
				$query7="SELECT Employee_FullName AS nama_pemilik
					FROM db_master.M_Employee
					WHERE Employee_NIK='$arr[DAO_Employee_NIK]'";
			}
			$sql7 = mysql_query($query7);
			$nama_pemilik = "-";
			if(mysql_num_rows($sql7) > 0){
				$data7 = mysql_fetch_array($sql7);
				$nama_pemilik = $data7['nama_pemilik'];
			}
			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['DAO_DocCode'] ?>','<?= $_GET['row'] ?>')"><?= $arr['DAO_DocCode'] ?></a></u></td>
				<td align='center'><?= $nama_pemilik ?></td>
				<td align='center'><?= $arr['MK_Name'] ?></td>
				<td align='center'><?= $arr['DAO_Type'] ?></td>
				<td align='center'><?= $arr['DAO_Jenis'] ?></td>
                <td align='center'><?= $arr['DAO_NoPolisi'] ?></td>
                <td align='center'><?= $arr['DAO_NoRangka'] ?></td>
                <td align='center'><?= $arr['DAO_NoMesin'] ?></td>
                <td align='center'><?= $arr['DAO_NoBPKB'] ?></td>
				<td align='center'><?= $stnk_sdate ?></td>
				<td align='center'><?= $stnk_exdate ?></td>
                <td align='center'><?= $pajak_sdate ?></td>
				<td align='center'><?= $pajak_exdate ?></td>
				<td align='center'><?= $arr['DAO_Lokasi_PT'] ?></td>
                <td align='center'><?= $arr['DAO_Region'] ?></td>
                <td align='center'><?= $arr['DAO_Keterangan'] ?></td>
			</tr>
			<?PHP
		}
	}
}
?>
</TABLE>
<?php
	$jml_data = mysql_num_rows(mysql_query($query));
	$JmlHalaman = ceil($jml_data/$batas);
	if ( $pg > 1 ) {
		$link = $pg-1;
		if (isset($_GET[pID])) {
			$prev = "<a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Previous </a>";
		} else {
			$prev = "<a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Previous </a>";
		}
	} else {
		$prev = "Previous ";
	}

	$nmr = '';
	for ($i = 1; $i<= $JmlHalaman; $i++) {
		if ($i == $pg) {
			$nmr .= $i . " ";
		} else {
			if (isset($_GET[pID])) {
				$nmr .= "<a href='?pg=$i&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:green;font-weight:bold;'>$i</a> ";
			} else {
				$nmr .= "<a href='?pg=$i&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:green;font-weight:bold;'>$i</a> ";
			}
		}
	}

	if ($pg < $JmlHalaman) {
		$link = $pg + 1;
		if (isset($_GET[pID])) {
			$next = " <a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Next</a>";
		} else {
			$next = " <a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Next</a>";
		}
	} else {
		$next = " Next";
	}

	if ($JmlHalaman > 1) echo '<br />Halaman : ' .$prev . $nmr . $next . '<br /><br />';
?>
</BODY>
</HTML>
