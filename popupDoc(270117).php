<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pick(symbol,row) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.adddetaildoc.txtTDLOLD_DocumentCode<?= $_GET['row'] ?>.value = symbol;
	window.opener.document.adddetaildoc.docCode.value = window.opener.document.adddetaildoc.docCode.value + "\'" + symbol + "\'" +",";
	window.close();
	}
}
function pickla(symbol,row) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.adddetaildoc.txtTDLOLAD_DocCode<?= $_GET['row'] ?>.value = symbol;
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
$txtTHLOLD_CompanyID=$_GET['cID'];
$txtTHLOLD_DocumentGroupID=$_GET['gID'];

IF ($txtTHLOLD_DocumentGroupID<>"3"){
	$optTDLOLD_DocumentCategoryID=$_GET['catID'];
	if ($_GET[recentCode]) $filter =" AND dl.DL_DocCode NOT IN (".substr($_GET[recentCode],0, -1).")";

	$query="SELECT DISTINCT dt.DocumentType_Name, c.Company_Name, dg.DocumentGroup_Name, dc.DocumentCategory_Name, 
							dl.DL_DocCode, dl.DL_NoDoc, dl.DL_Instance, dl.DL_PubDate, dl.DL_ExpDate, 
							di1.DocumentInformation1_Name, di2.DocumentInformation2_Name, dl.DL_Information3
			FROM M_DocumentLegal dl, M_DocumentType dt, M_Company c, M_DocumentGroup dg, M_DocumentCategory dc, 
				 M_DocumentInformation1 di1, M_DocumentInformation2 di2
			WHERE dl.DL_Status ='1'
			AND dl.DL_CompanyID='$txtTHLOLD_CompanyID'
			AND dl.DL_GroupDocID='$txtTHLOLD_DocumentGroupID'
			AND dl.DL_CategoryDocID='$optTDLOLD_DocumentCategoryID'
			AND dl.DL_Delete_Time IS NULL
			AND dl.DL_CompanyID=c.Company_ID
			AND dl.DL_GroupDocID=dg.DocumentGroup_ID
			AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
			AND dl.DL_TypeDocID=dt.DocumentType_ID
			AND dl.DL_Information1=di1.DocumentInformation1_ID
			AND dl.DL_Information2=di2.DocumentInformation2_ID
			$filter 
			ORDER BY dl.DL_DocCode
			LIMIT 0,10";
	$sql = mysql_query($query);
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
		echo "<div class=title><b>$h_arr[Company_Name] - $h_arr[DocumentGroup_Name]</b><br><i>$h_arr[DocumentCategory_Name]</i></div>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='15%'>Kode Dokumen</th>
			<th width='25%'>Tipe Dokumen</th>
			<th width='10%'>No Dokumen</th>
			<th width='10%'>Instansi Terkait</th>
			<th width='10%'>Tgl Terbit</th>
			<th width='10%'>Habis Berlaku</th>
			<th width='10%'>Ket 1</th>
			<th width='10%'>Ket 2</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT dt.DocumentType_Name,dl.DL_DocCode, dl.DL_NoDoc, dl.DL_Instance, dl.DL_PubDate, dl.DL_ExpDate, 
							   di1.DocumentInformation1_Name, di2.DocumentInformation2_Name, dl.DL_Information3
						FROM M_DocumentLegal dl, M_DocumentType dt, M_DocumentInformation1 di1, M_DocumentInformation2 di2
						WHERE dl.DL_Status ='1'
						AND dl.DL_CompanyID='$txtTHLOLD_CompanyID'
						AND dl.DL_GroupDocID='$txtTHLOLD_DocumentGroupID'
						AND dl.DL_CategoryDocID='$optTDLOLD_DocumentCategoryID'
						AND dl.DL_Delete_Time IS NULL
						AND dl.DL_TypeDocID=dt.DocumentType_ID
						AND dl.DL_Information1=di1.DocumentInformation1_ID
						AND dl.DL_Information2=di2.DocumentInformation2_ID
						$filter
						AND (
							dl.DL_DocCode LIKE '%$search%'
							OR dl.DL_TypeDocID LIKE '%$search%'
							OR dt.DocumentType_Name LIKE '%$search%'
							OR dl.DL_Information1 LIKE '%$search%'
							OR di1.DocumentInformation1_Name LIKE '%$search%'
							OR dl.DL_Information2 LIKE '%$search%'
							OR di2.DocumentInformation2_Name LIKE '%$search%'
							OR dl.DL_Information3 LIKE '%$search%'
							OR dl.DL_Instance LIKE '%$search%'
							OR dl.DL_NoDoc LIKE '%$search%'
							OR dl.DL_PubDate LIKE '%$search%'
							OR dl.DL_ExpDate LIKE '%$search%'
						)						
						ORDER BY dl.DL_DocCode ASC 
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$pubdate=date("d M Y", strtotime($arr[DL_PubDate]));
			if (($arr[DL_ExpDate]=="0000-00-00 00:00:00")||($arr[DL_ExpDate]=="1970-01-01 01:00:00"))
				$expdate="-";
			else
				$expdate=date("d M Y", strtotime($arr[DL_ExpDate]));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['DL_DocCode'] ?>','<?= $_GET['row'] ?>')"><?= $arr['DL_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['DocumentType_Name'] ?></td>
				<td align='center'><?= $arr['DL_NoDoc'] ?></td>
				<td align='center'><?= $arr['DL_Instance'] ?></td>
				<td align='center'><?= $pubdate ?></td>
				<td align='center'><?= $expdate ?></td>
				<td align='center'><?= $arr['DocumentInformation1_Name'] ?></td>
				<td align='center'><?= $arr['DocumentInformation2_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

ELSE{
	$phase=$_GET['pID'];
	if ($_GET[recentCode]) $filter =" AND dla.DLA_Code NOT IN (".substr($_GET[recentCode],0, -1).")";

	$query="SELECT DISTINCT c.Company_Name, dg.DocumentGroup_Name, dla.DLA_Code, dla.DLA_Phase, dla.DLA_Period,
							dla.DLA_DocDate, dla.DLA_Block, dla.DLA_Village, dla.DLA_Owner
			FROM M_DocumentLandAcquisition dla, M_Company c, M_DocumentGroup dg
			WHERE dla.DLA_Status ='1'
			AND dla.DLA_CompanyID='$txtTHLOLD_CompanyID'
			AND dg.DocumentGroup_ID='$txtTHLOLD_DocumentGroupID'
			AND dla.DLA_Phase='$phase'
			AND dla.DLA_Delete_Time IS NULL
			AND dla.DLA_CompanyID=c.Company_ID
			$filter 
			ORDER BY dla.DLA_Code
			LIMIT 0,10";
	$sql = mysql_query($query);
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
		$period=date("d M Y", strtotime($h_arr[DLA_Period]));
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		echo "<div class=title><b>$h_arr[Company_Name] - $h_arr[DocumentGroup_Name]</b><br><i>Tahap $h_arr[DLA_Phase] : $period</i></div>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='15%'>Kode Dokumen</th>
			<th width='10%'>Tanggal Dokumen</th>
			<th width='35%'>Blok</th>
			<th width='20%'>Desa</th>
			<th width='20%'>Pemilik</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT c.Company_Name, dg.DocumentGroup_Name, dla.DLA_Code, dla.DLA_Phase, dla.DLA_Period,
							   dla.DLA_DocDate, dla.DLA_Block, dla.DLA_Village, dla.DLA_Owner
						FROM M_DocumentLandAcquisition dla, M_Company c, M_DocumentGroup dg
						WHERE dla.DLA_Status ='1'
						AND dla.DLA_CompanyID='$txtTHLOLD_CompanyID'
						AND dg.DocumentGroup_ID='$txtTHLOLD_DocumentGroupID'
						AND dla.DLA_Phase='$phase'
						AND dla.DLA_Delete_Time IS NULL
						AND dla.DLA_CompanyID=c.Company_ID
						$filter
						AND (
							dla.DLA_Code LIKE '%$search%'
							OR dla.DLA_DocDate LIKE '%$search%'
							OR dla.DLA_Block LIKE '%$search%' 
							OR dla.DLA_Village LIKE '%$search%' 
							OR dla.DLA_Owner LIKE '%$search%'
						)						
						ORDER BY dla.DLA_Code ASC 
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$docdate=date("d M Y", strtotime($arr[DLA_DocDate]));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pickla('<?= $arr['DLA_Code'] ?>','<?= $_GET['row'] ?>')"><?= $arr['DLA_Code'] ?></a></u></td>
				<td align='center'><?= $docdate ?></td>
				<td align='center'><?= $arr['DLA_Block'] ?></td>
				<td align='center'><?= $arr['DLA_Village'] ?></td>
				<td align='center'><?= $arr['DLA_Owner'] ?></td>
			</tr>
			<?PHP
		}
	}
}
?>		
</TABLE>
</BODY>
</HTML>