<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pick(symbol,row) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.adddetaildoc.txtTDLOOLD_DocumentCode<?= $_GET['row'] ?>.value = symbol;
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

IF ($txtTHLOLD_DocumentGroupID=="5"){
	if ($_GET['recentCode']) $filter =" AND dol.DOL_DocCode NOT IN (".substr($_GET['recentCode'],0, -1).")";

	$query="SELECT DISTINCT c.Company_Name, dg.DocumentGroup_Name,
				dol.DOL_RegTime, dol.DOL_DocCode,
				dc.DocumentCategory_Name, dol.DOL_NamaDokumen,
                dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
				dol.DOL_TglTerbit, dol.DOL_TglBerakhir
			FROM M_DocumentsOtherLegal dol
			LEFT JOIN db_master.M_DocumentCategory dc
				ON dc.DocumentCategory_ID=dol.DOL_CategoryDocId
			LEFT JOIN M_Company c
				ON dol.DOL_CompanyID=c.Company_ID
			LEFT JOIN M_DocumentGroup dg
				ON dol.DOL_GroupDocID=dg.DocumentGroup_ID
			WHERE dol.DOL_CompanyID='$txtTHLOLD_CompanyID'
			AND dol.DOL_GroupDocID='$txtTHLOLD_DocumentGroupID'
			AND dol.DOL_Status = '1'
			AND dol.DOL_Delete_Time IS NULL
			$filter
			ORDER BY dol.DOL_RegTime DESC";
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
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		echo "<div class=title><b>$h_arr[Company_Name] - Dokumen Lainnya (Legal)</b></div>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
            <tr>
                <th>No.</th>
                <th>Kategori Dokumen</th>
                <th>Nama Dokumen</th>
                <th>Instansi Terkait</th>
                <th>No. Dokumen</th>
                <th>Tanggal Terbit</th>
                <th>Tanggal Berakhir</th>
            </tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT dt.DocumentType_Name, dl.DOL_RegTime, dl.DOL_DocCode, dl.DOL_NoDoc, dl.DOL_Instance, dl.DOL_PubDate, dl.DOL_ExpDate,
							   di1.DocumentInformation1_Name, di2.DocumentInformation2_Name, dl.DOL_Information3
						FROM M_DocumentsOtherLegal dl, M_DocumentType dt, M_DocumentInformation1 di1, M_DocumentInformation2 di2
						WHERE dl.DOL_Status ='1'
						AND dl.DOL_CompanyID='$txtTHLOLD_CompanyID'
						AND dl.DOL_GroupDocID='$txtTHLOLD_DocumentGroupID'
						AND dl.DOL_CategoryDocID='$optTDLOLD_DocumentCategoryID'
						AND dl.DOL_Delete_Time IS NULL
						AND dol.DOL_Status = '1'
						AND dl.DOL_TypeDocID=dt.DocumentType_ID
						AND dl.DOL_Information1=di1.DocumentInformation1_ID
						AND dl.DOL_Information2=di2.DocumentInformation2_ID
						$filter
						AND (
							dl.DOL_DocCode LIKE '%$search%'
							OR dl.DOL_TypeDocID LIKE '%$search%'
							OR dt.DocumentType_Name LIKE '%$search%'
							OR dl.DOL_Information1 LIKE '%$search%'
							OR di1.DocumentInformation1_Name LIKE '%$search%'
							OR dl.DOL_Information2 LIKE '%$search%'
							OR di2.DocumentInformation2_Name LIKE '%$search%'
							OR dl.DOL_Information3 LIKE '%$search%'
							OR dl.DOL_Instance LIKE '%$search%'
							OR dl.DOL_NoDoc LIKE '%$search%'
							OR dl.DOL_PubDate LIKE '%$search%'
							OR dl.DOL_ExpDate LIKE '%$search%'
						)
						ORDER BY dl.DOL_RegTime DESC ";
			$limit = " LIMIT $posisi, $batas";
			$sql = mysql_query($query.$limit);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$tgl_terbit=date("d M Y", strtotime($arr['DOL_TglTerbit']));
			if (($arr['DOL_TglBerakhir']=="0000-00-00 00:00:00")||($arr['DOL_TglBerakhir']=="1970-01-01 01:00:00"))
				$tgl_berakhir="-";
			else
				$tgl_berakhir=date("d M Y", strtotime($arr['DOL_TglBerakhir']));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['DOL_DocCode'] ?>','<?= $_GET['row'] ?>')"><?= $arr['DOL_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['DocumentCategory_Name'] ?></td>
				<td align='center'><?= $arr['DOL_NamaDokumen'] ?></td>
				<td align='center'><?= $arr['DOL_InstansiTerkait'] ?></td>
                <td align='center'><?= $arr['DOL_NoDokumen'] ?></td>
				<td align='center'><?= $tgl_terbit ?></td>
				<td align='center'><?= $tgl_berakhir ?></td>
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
