<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Pengeluaran Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
function pick(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOLD_DocCode'+n).value = result;
	window.close();
	}
}
function pickla(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOLAD_DocCode'+n).value = result;
	window.close();
	}
}
function pickao(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOAOD_DocCode'+n).value = result;
	window.close();
	}
}
function pickol(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOOLD_DocCode'+n).value = result;
	window.close();
	}
}
function pickonl(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOONLD_DocCode'+n).value = result;
	window.close();
	}
}
</SCRIPT>
<link href="./css/style.css" rel="stylesheet" type="text/css">
<style>
	.pageNumber{
		margin-left:3%;
		margin-right:3%;
		float:left;
	}
	.pagerContainer{
		width:25%;
		margin:auto;
	}
</style>
</HEAD>
<BODY>
<?PHP
session_start();
$PHP_SELF=$_SERVER['PHP_SELF'];
$grup=$_GET['gID'];
$txtKe=$_GET['txtKe'];
$dataPerPage = 20;
$currPage = isset($_GET['page'])?$_GET['page']:0;
$search= isset($_GET['txtSearch'])?$_GET['txtSearch']:"";
if($currPage>0){
	$currPage--;
}
$maxDataNum=0;

if($grup==1){
	$query="SELECT tdlold.TDLOLD_DocCode,dc.DocumentCategory_Name,
				dt.DocumentType_Name,dl.DL_Instance,DATE_FORMAT(dl.DL_PubDate, '%d %M %Y') DL_PubDate,
				(SELECT COUNT(tdlold.TDLOLD_DocCode) Total
					FROM TD_ReleaseOfLegalDocument tdrlold
					INNER JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
					  AND tdlold.TDLOLD_Delete_Time IS NULL
					INNER JOIN TH_LoanOfLegalDocument thlold ON tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
					  AND thlold.THLOLD_UserID='".$_COOKIE['User_ID']."'
					  AND thlold.THLOLD_Delete_Time IS NULL
					INNER JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode
					  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
					LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
					  AND dc.DocumentCategory_Delete_Time IS NULL
					LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
					  AND dt.DocumentType_Delete_Time IS NULL
					WHERE tdrlold.TDROLD_ReturnCode='0'
						AND tdrlold.TDROLD_Delete_Time IS NULL) Total
			FROM TD_ReleaseOfLegalDocument tdrlold
			INNER JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
			  AND tdlold.TDLOLD_Delete_Time IS NULL
			INNER JOIN TH_LoanOfLegalDocument thlold ON tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
			  AND thlold.THLOLD_UserID='".$_COOKIE['User_ID']."'
			  AND thlold.THLOLD_Delete_Time IS NULL
			INNER JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode
			  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
			LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
			  AND dc.DocumentCategory_Delete_Time IS NULL
			LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
			  AND dt.DocumentType_Delete_Time IS NULL
			WHERE tdrlold.TDROLD_ReturnCode='0'
				AND tdrlold.TDROLD_Delete_Time IS NULL
			ORDER BY tdlold.TDLOLD_DocCode ASC
			LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='get' action='$PHP_SELF'>
				<input type='hidden' name='gID' value='$grup'/>
				<input type='hidden' name='txtKe' value='$txtKe'/>
				<input type='hidden' name='page' value='0'/>
				<div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
					<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%' value='$search'/>
				</div>
			</form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<!--th>No. Pengeluaran</th-->
			<th width='20%'>Kode Dokumen</th>
			<th width='20%'>Kategori Dokumen</th>
			<th width='20%'>Tipe Dokumen</th>
			<th width='20%'>Instansi Terkait</th>
			<th width='20%'>Tanggal Terbit</th>
		<tr>
		<?PHP
		if(isset($_GET['txtSearch'])) {
			$query =   "SELECT tdlold.TDLOLD_DocCode,dc.DocumentCategory_Name,
							dt.DocumentType_Name,dl.DL_Instance,DATE_FORMAT(dl.DL_PubDate, '%d %M %Y') DL_PubDate,
							(SELECT COUNT(tdlold.TDLOLD_DocCode) Total
								FROM TD_ReleaseOfLegalDocument tdrlold
								INNER JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
								  AND tdlold.TDLOLD_Delete_Time IS NULL
								INNER JOIN TH_LoanOfLegalDocument thlold ON tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
								  AND thlold.THLOLD_UserID='".$_COOKIE['User_ID']."'
								  AND thlold.THLOLD_Delete_Time IS NULL
								INNER JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode
								  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
								LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
								  AND dc.DocumentCategory_Delete_Time IS NULL
								LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
								  AND dt.DocumentType_Delete_Time IS NULL
								WHERE tdrlold.TDROLD_ReturnCode='0'
									AND tdrlold.TDROLD_Delete_Time IS NULL
									AND (
										tdlold.TDLOLD_DocCode LIKE '%$search%'
										OR dc.DocumentCategory_Name LIKE '%$search%'
										OR dt.DocumentType_Name LIKE '%$search%'
										OR dl.DL_Instance LIKE '%$search%'
										OR DL_PubDate LIKE '%$search%'
										OR MONTH(DL_PubDate) LIKE '%$search%'
									)
							) Total
						FROM TD_ReleaseOfLegalDocument tdrlold
						INNER JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
						  AND tdlold.TDLOLD_Delete_Time IS NULL
						INNER JOIN TH_LoanOfLegalDocument thlold ON tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
						  AND thlold.THLOLD_UserID='".$_COOKIE['User_ID']."'
						  AND thlold.THLOLD_Delete_Time IS NULL
						INNER JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode
						  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
						LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
						  AND dc.DocumentCategory_Delete_Time IS NULL
						LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
						  AND dt.DocumentType_Delete_Time IS NULL
						WHERE tdrlold.TDROLD_ReturnCode='0'
							AND tdrlold.TDROLD_Delete_Time IS NULL
							AND (
								tdlold.TDLOLD_DocCode LIKE '%$search%'
								OR dc.DocumentCategory_Name LIKE '%$search%'
								OR dt.DocumentType_Name LIKE '%$search%'
								OR dl.DL_Instance LIKE '%$search%'
								OR DL_PubDate LIKE '%$search%'
								OR MONTH(DL_PubDate) LIKE '%$search%'
							)
						ORDER BY tdlold.TDLOLD_DocCode ASC
						LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$maxDataNum=$arr['Total'];
			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['TDLOLD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOLD_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['DocumentCategory_Name'] ?></td>
				<td align='center'><?= $arr['DocumentType_Name'] ?></td>
				<td align='center'><?= $arr['DL_Instance'] ?></td>
				<td align='center'><?= $arr['DL_PubDate'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==3){
	$query="SELECT tdlolad.TDLOLAD_DocCode,dla.DLA_Phase,DATE_FORMAT(dla.DLA_Period, '%d %M %Y') DLA_Period,
				DATE_FORMAT(dla.DLA_DocDate, '%d %M %Y') DLA_DocDate,dla.DLA_Block,dla.DLA_Village,dla.DLA_Owner,
				(SELECT COUNT(tdlolad.TDLOLAD_DocCode)
					FROM TD_LoanOfLandAcquisitionDocument tdlolad
					INNER JOIN TH_LoanOfLandAcquisitionDocument thlolad ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
						AND thlolad.THLOLAD_UserID='".$_COOKIE['User_ID']."'
						AND thlolad.THLOLAD_Delete_Time IS NULL
					INNER JOIN TD_ReleaseOfLandAcquisitionDocument tdrlolad ON tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
						AND tdrlolad.TDRLOLAD_ReturnCode='0' AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
					INNER JOIN M_DocumentLandAcquisition dla ON dla.DLA_Code=tdlolad.TDLOLAD_DocCode
						AND dla.DLA_Status='4' AND dla.DLA_Delete_Time IS NULL
					WHERE tdlolad.TDLOLAD_Delete_Time IS NULL
				) Total
			FROM TD_LoanOfLandAcquisitionDocument tdlolad
			INNER JOIN TH_LoanOfLandAcquisitionDocument thlolad ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				AND thlolad.THLOLAD_UserID='".$_COOKIE['User_ID']."'
				AND thlolad.THLOLAD_Delete_Time IS NULL
			INNER JOIN TD_ReleaseOfLandAcquisitionDocument tdrlolad ON tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				AND tdrlolad.TDRLOLAD_ReturnCode='0' AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
			INNER JOIN M_DocumentLandAcquisition dla ON dla.DLA_Code=tdlolad.TDLOLAD_DocCode
				AND dla.DLA_Status='4' AND dla.DLA_Delete_Time IS NULL
			WHERE tdlolad.TDLOLAD_Delete_Time IS NULL
			ORDER BY tdlolad.TDLOLAD_DocCode ASC
			LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='get' action='$PHP_SELF'>
				<input type='hidden' name='gID' value='$grup'/>
				<input type='hidden' name='txtKe' value='$txtKe'/>
				<input type='hidden' name='page' value='0'/>
				<div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
					<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%' value='$search'/>
				</div>
			</form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<!--th width='25%'>No. Pengeluaran</th-->
			<th width='14%'>Kode Dokumen</th>
			<th width='14%'>Tahap GRL</th>
			<th width='14%'>Periode GRL</th>
			<th width='14%'>Tanggal Dokumen</th>
			<th width='14%'>Blok</th>
			<th width='14%'>Desa</th>
			<th width='14%'>Pemilik</th>
		<tr>
		<?PHP
		if(isset($_GET['txtSearch'])) {
			$query =   "SELECT tdlolad.TDLOLAD_DocCode,dla.DLA_Phase,DATE_FORMAT(dla.DLA_Period, '%d %M %Y') DLA_Period,
							DATE_FORMAT(dla.DLA_DocDate, '%d %M %Y') DLA_DocDate,dla.DLA_Block,dla.DLA_Village,dla.DLA_Owner,
							(SELECT COUNT(tdlolad.TDLOLAD_DocCode)
								FROM TD_LoanOfLandAcquisitionDocument tdlolad
								INNER JOIN TH_LoanOfLandAcquisitionDocument thlolad ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
									AND thlolad.THLOLAD_UserID='".$_COOKIE['User_ID']."'
									AND thlolad.THLOLAD_Delete_Time IS NULL
								INNER JOIN TD_ReleaseOfLandAcquisitionDocument tdrlolad ON tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
									AND tdrlolad.TDRLOLAD_ReturnCode='0' AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
								INNER JOIN M_DocumentLandAcquisition dla ON dla.DLA_Code=tdlolad.TDLOLAD_DocCode
									AND dla.DLA_Status='4' AND dla.DLA_Delete_Time IS NULL
								WHERE tdlolad.TDLOLAD_Delete_Time IS NULL
									AND (
										tdlolad.TDLOLAD_DocCode LIKE '%$search%'
										OR dla.DLA_Phase LIKE '%$search%'
										OR MONTH(dla.DLA_Period) LIKE '%$search%'
										OR dla.DLA_DocDate LIKE '%$search%'
										OR MONTH(dla.DLA_DocDate) LIKE '%$search%'
										OR dla.DLA_Block LIKE '%$search%'
										OR dla.DLA_Village LIKE '%$search%'
										OR dla.DLA_Owner LIKE '%$search%'
									)
							) Total
						FROM TD_LoanOfLandAcquisitionDocument tdlolad
						INNER JOIN TH_LoanOfLandAcquisitionDocument thlolad ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
							AND thlolad.THLOLAD_UserID='".$_COOKIE['User_ID']."'
							AND thlolad.THLOLAD_Delete_Time IS NULL
						INNER JOIN TD_ReleaseOfLandAcquisitionDocument tdrlolad ON tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
							AND tdrlolad.TDRLOLAD_ReturnCode='0' AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
						INNER JOIN M_DocumentLandAcquisition dla ON dla.DLA_Code=tdlolad.TDLOLAD_DocCode
							AND dla.DLA_Status='4' AND dla.DLA_Delete_Time IS NULL
						WHERE tdlolad.TDLOLAD_Delete_Time IS NULL
							AND (
								tdlolad.TDLOLAD_DocCode LIKE '%$search%'
								OR dla.DLA_Phase LIKE '%$search%'
								OR MONTH(dla.DLA_Period) LIKE '%$search%'
								OR dla.DLA_DocDate LIKE '%$search%'
								OR MONTH(dla.DLA_DocDate) LIKE '%$search%'
								OR dla.DLA_Block LIKE '%$search%'
								OR dla.DLA_Village LIKE '%$search%'
								OR dla.DLA_Owner LIKE '%$search%'
							)
						ORDER BY tdlolad.TDLOLAD_DocCode ASC
						LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			//$tgl_terbit=date("d M Y", strtotime($arr['DLA_RegTime']));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pickla('<?= $arr['TDLOLAD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOLAD_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['DLA_Phase'] ?></td>
				<td align='center'><?= $arr['DLA_Period'] ?></td>
				<td align='center'><?= $arr['DLA_DocDate'] ?></td>
				<td align='center'><?= $arr['DLA_Block'] ?></td>
				<td align='center'><?= $arr['DLA_Village'] ?></td>
				<td align='center'><?= $arr['DLA_Owner'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==4){
	$query="SELECT tdloaod.TDLOAOD_DocCode, dao.DAO_Employee_NIK,
				  CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
					  THEN
					   (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
					  ELSE
						  (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
				  END nama_pemilik,
				  m_mk.MK_Name,
			  dao.DAO_Type, dao.DAO_Jenis, dao.DAO_NoPolisi, dao.DAO_NoRangka, dao.DAO_NoMesin,
			  (SELECT COUNT(tdloaod.TDLOAOD_DocCode) Total
				FROM TD_LoanOfAssetOwnershipDocument tdloaod
				INNER JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
					AND thloaod.THLOAOD_UserID='".$_COOKIE['User_ID']."'
					AND thloaod.THLOAOD_Delete_Time IS NULL
				INNER JOIN TD_ReleaseOfAssetOwnershipDocument tdrloaod ON tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
					AND tdrloaod.TDROAOD_ReturnCode='0'
					AND tdrloaod.TDROAOD_Delete_Time IS NULL
				INNER JOIN M_DocumentAssetOwnership dao ON dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
					AND dao.DAO_Status='4'
					AND dao.DAO_Delete_Time IS NULL
				LEFT JOIN db_master.M_MerkKendaraan m_mk ON m_mk.MK_ID=dao.DAO_MK_ID
				  AND m_mk.MK_DeleteTime IS NULL
				WHERE tdloaod.TDLOAOD_Delete_Time IS NULL
			  ) Total
			FROM TD_LoanOfAssetOwnershipDocument tdloaod
			INNER JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
				AND thloaod.THLOAOD_UserID='".$_COOKIE['User_ID']."'
				AND thloaod.THLOAOD_Delete_Time IS NULL
			INNER JOIN TD_ReleaseOfAssetOwnershipDocument tdrloaod ON tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
				AND tdrloaod.TDROAOD_ReturnCode='0'
				AND tdrloaod.TDROAOD_Delete_Time IS NULL
			INNER JOIN M_DocumentAssetOwnership dao ON dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
				AND dao.DAO_Status='4'
				AND dao.DAO_Delete_Time IS NULL
			LEFT JOIN db_master.M_MerkKendaraan m_mk ON m_mk.MK_ID=dao.DAO_MK_ID
				AND m_mk.MK_DeleteTime IS NULL
			WHERE tdloaod.TDLOAOD_Delete_Time IS NULL
			ORDER BY tdloaod.TDLOAOD_DocCode ASC
			LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='get' action='$PHP_SELF'>
				<input type='hidden' name='gID' value='$grup'/>
				<input type='hidden' name='txtKe' value='$txtKe'/>
				<input type='hidden' name='page' value='0'/>
				<div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
					<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%' value='$search'/>
				</div>
			</form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<!--th width='25%'>No. Pengeluaran</th-->
			<th width='12.5%'>Kode Dokumen</th>
			<th width='12.5%'>Nama Pemilik</th>
			<th width='12.5%'>Merk Kendaraan</th>
			<th width='12.5%'>Type</th>
			<th width='12.5%'>Jenis</th>
			<th width='12.5%'>No. Polisi</th>
			<th width='12.5%'>No. Rangka</th>
			<th width='12.5%'>No. Mesin</th>
		<tr>
		<?PHP
		if(!empty($_GET['txtSearch'])) {
			$search=$_GET['txtSearch'];
			$query =   "SELECT tdloaod.TDLOAOD_DocCode, dao.DAO_Employee_NIK,
						  CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
							  THEN
							   	  (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
							  ELSE
								  (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
						  END nama_pemilik,
						  m_mk.MK_Name,
						  dao.DAO_Type, dao.DAO_Jenis, dao.DAO_NoPolisi, dao.DAO_NoRangka, dao.DAO_NoMesin,
						  e.Employee_FullName, c.Company_Name,
						(SELECT COUNT(tdloaod.TDLOAOD_DocCode) Total
							FROM TD_LoanOfAssetOwnershipDocument tdloaod
							INNER JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
								AND thloaod.THLOAOD_UserID='".$_COOKIE['User_ID']."'
								AND thloaod.THLOAOD_Delete_Time IS NULL
							INNER JOIN TD_ReleaseOfAssetOwnershipDocument tdrloaod ON tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
								AND tdrloaod.TDROAOD_ReturnCode='0'
								AND tdrloaod.TDROAOD_Delete_Time IS NULL
							INNER JOIN M_DocumentAssetOwnership dao ON dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
								AND dao.DAO_Status='4'
								AND dao.DAO_Delete_Time IS NULL
							LEFT JOIN db_master.M_MerkKendaraan m_mk ON m_mk.MK_ID=dao.DAO_MK_ID
							  AND m_mk.MK_DeleteTime IS NULL
							LEFT JOIN db_master.M_Employee e ON e.Employee_NIK=dao.DAO_Employee_NIK
							LEFT JOIN M_Company c ON c.Company_Code = REPLACE(dao.DAO_Employee_NIK, 'CO@', '')
							WHERE tdloaod.TDLOAOD_Delete_Time IS NULL
							  AND (
									tdloaod.TDLOAOD_DocCode LIKE '%$search%'
									OR (dao.DAO_Employee_NIK LIKE '%$search%' OR e.Employee_FullName LIKE '%$search%' OR c.Company_Name LIKE '%$search%')
									OR m_mk.MK_Name LIKE '%$search%'
									OR dao.DAO_Type LIKE '%$search%'
									OR dao.DAO_Jenis LIKE '%$search%'
									OR dao.DAO_NoPolisi LIKE '%$search%'
									OR dao.DAO_NoRangka LIKE '%$search%'
									OR dao.DAO_NoMesin LIKE '%$search%'
								)
						) Total
						FROM TD_LoanOfAssetOwnershipDocument tdloaod
						INNER JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
							AND thloaod.THLOAOD_UserID='".$_COOKIE['User_ID']."'
							AND thloaod.THLOAOD_Delete_Time IS NULL
						INNER JOIN TD_ReleaseOfAssetOwnershipDocument tdrloaod ON tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
							AND tdrloaod.TDROAOD_ReturnCode='0'
							AND tdrloaod.TDROAOD_Delete_Time IS NULL
						INNER JOIN M_DocumentAssetOwnership dao ON dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
							AND dao.DAO_Status='4'
							AND dao.DAO_Delete_Time IS NULL
						LEFT JOIN db_master.M_MerkKendaraan m_mk ON m_mk.MK_ID=dao.DAO_MK_ID
							AND m_mk.MK_DeleteTime IS NULL
						LEFT JOIN db_master.M_Employee e ON e.Employee_NIK=dao.DAO_Employee_NIK
						LEFT JOIN M_Company c ON c.Company_Code = REPLACE(dao.DAO_Employee_NIK, 'CO@', '')
						WHERE tdloaod.TDLOAOD_Delete_Time IS NULL
						AND (
							tdloaod.TDLOAOD_DocCode LIKE '%$search%'
							OR (dao.DAO_Employee_NIK LIKE '%$search%' OR e.Employee_FullName LIKE '%$search%' OR c.Company_Name LIKE '%$search%')
							OR m_mk.MK_Name LIKE '%$search%'
							OR dao.DAO_Type LIKE '%$search%'
							OR dao.DAO_Jenis LIKE '%$search%'
							OR dao.DAO_NoPolisi LIKE '%$search%'
							OR dao.DAO_NoRangka LIKE '%$search%'
							OR dao.DAO_NoMesin LIKE '%$search%'
						)
						ORDER BY tdloaod.TDLOAOD_DocCode ASC
						LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}
		while ($arr=mysql_fetch_array($sql)){
			?>
			<tr>
				<td align='center'><u><a href="javascript:pickao('<?= $arr['TDLOAOD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOAOD_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['nama_pemilik'] ?></td>
				<td align='center'><?= $arr['MK_Name'] ?></td>
				<td align='center'><?= $arr['DAO_Type'] ?></td>
				<td align='center'><?= $arr['DAO_Jenis'] ?></td>
				<td align='center'><?= $arr['DAO_NoPolisi'] ?></td>
				<td align='center'><?= $arr['DAO_NoRangka'] ?></td>
				<td align='center'><?= $arr['DAO_NoMesin'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==5){
	$query="SELECT tdloold.TDLOOLD_DocCode, dc.DocumentCategory_Name,
			  dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait,dol.DOL_NoDokumen,DATE_FORMAT(dol.DOL_TglTerbit, '%d %M %Y') DOL_TglTerbit,
			  (SELECT COUNT(tdloold.TDLOOLD_ID) Total
				FROM  TD_LoanOfOtherLegalDocuments tdloold
				INNER JOIN TH_LoanOfOtherLegalDocuments thloold ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
					AND thloold.THLOOLD_UserID='".$_COOKIE['User_ID']."'
					AND thloold.THLOOLD_Delete_Time IS NULL
				INNER JOIN TD_ReleaseOfOtherLegalDocuments tdrloold ON tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
				  AND tdrloold.TDROOLD_ReturnCode='0'
				  AND tdrloold.TDROOLD_Delete_Time IS NULL
				INNER JOIN M_DocumentsOtherLegal dol ON dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
				  AND dol.DOL_Status='4' AND dol.DOL_Delete_Time IS NULL
				LEFT JOIN db_master.M_DocumentCategory dc ON dol.DOL_CategoryDocID=dc.DocumentCategory_ID
				  AND dc.DocumentCategory_Delete_Time IS NULL
				WHERE tdloold.TDLOOLD_Delete_Time IS NULL) Total
			FROM  TD_LoanOfOtherLegalDocuments tdloold
			INNER JOIN TH_LoanOfOtherLegalDocuments thloold ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
				AND thloold.THLOOLD_UserID='".$_COOKIE['User_ID']."'
				AND thloold.THLOOLD_Delete_Time IS NULL
			INNER JOIN TD_ReleaseOfOtherLegalDocuments tdrloold ON tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
			  AND tdrloold.TDROOLD_ReturnCode='0'
			  AND tdrloold.TDROOLD_Delete_Time IS NULL
			INNER JOIN M_DocumentsOtherLegal dol ON dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
			  AND dol.DOL_Status='4' AND dol.DOL_Delete_Time IS NULL
			LEFT JOIN db_master.M_DocumentCategory dc ON dol.DOL_CategoryDocID=dc.DocumentCategory_ID
			  AND dc.DocumentCategory_Delete_Time IS NULL
			WHERE tdloold.TDLOOLD_Delete_Time IS NULL
			ORDER BY tdloold.TDLOOLD_DocCode ASC
			LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='get' action='$PHP_SELF'>
				<input type='hidden' name='gID' value='$grup'/>
				<input type='hidden' name='txtKe' value='$txtKe'/>
				<input type='hidden' name='page' value='0'/>
				<div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
					<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%' value='$search'/>
				</div>
			</form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<!-- th width='25%'>No. Pengeluaran</th -->
			<th width='16%'>Kode Dokumen</th>
			<th width='16%'>Kategori Dokumen</th>
			<th width='16%'>Nama Dokumen</th>
			<th width='16%'>Instansi Terkait</th>
			<th width='16%'>No. Dokumen</th>
			<th width='16%'>Tanggal Terbit</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT tdloold.TDLOOLD_DocCode, dc.DocumentCategory_Name,
							dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait,dol.DOL_NoDokumen,DATE_FORMAT(dol.DOL_TglTerbit, '%d %M %Y') DOL_TglTerbit,
							(SELECT COUNT(tdloold.TDLOOLD_ID) Total
								FROM  TD_LoanOfOtherLegalDocuments tdloold
								INNER JOIN TH_LoanOfOtherLegalDocuments thloold ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
									AND thloold.THLOOLD_UserID='".$_COOKIE['User_ID']."'
									AND thloold.THLOOLD_Delete_Time IS NULL
								INNER JOIN TD_ReleaseOfOtherLegalDocuments tdrloold ON tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
								  AND tdrloold.TDROOLD_ReturnCode='0'
								  AND tdrloold.TDROOLD_Delete_Time IS NULL
								INNER JOIN M_DocumentsOtherLegal dol ON dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
								  AND dol.DOL_Status='4' AND dol.DOL_Delete_Time IS NULL
								LEFT JOIN db_master.M_DocumentCategory dc ON dol.DOL_CategoryDocID=dc.DocumentCategory_ID
								  AND dc.DocumentCategory_Delete_Time IS NULL
								WHERE tdloold.TDLOOLD_Delete_Time IS NULL
								AND (
									tdloold.TDLOOLD_DocCode LIKE '%$search%'
									OR dc.DocumentCategory_Name LIKE '%$search%'
									OR dol.DOL_NamaDokumen LIKE '%$search%'
									OR dol.DOL_InstansiTerkait LIKE '%$search%'
									OR dol.DOL_NoDokumen LIKE '%$search%'
									OR dol.DOL_TglTerbit LIKE '%$search%'
									OR MONTH(dol.DOL_TglTerbit) LIKE '%$search%'
								)
							) Total
						FROM  TD_LoanOfOtherLegalDocuments tdloold
						INNER JOIN TH_LoanOfOtherLegalDocuments thloold ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
							AND thloold.THLOOLD_UserID='".$_COOKIE['User_ID']."'
							AND thloold.THLOOLD_Delete_Time IS NULL
						INNER JOIN TD_ReleaseOfOtherLegalDocuments tdrloold ON tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
						  AND tdrloold.TDROOLD_ReturnCode='0'
						  AND tdrloold.TDROOLD_Delete_Time IS NULL
						INNER JOIN M_DocumentsOtherLegal dol ON dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
						  AND dol.DOL_Status='4' AND dol.DOL_Delete_Time IS NULL
						LEFT JOIN db_master.M_DocumentCategory dc ON dol.DOL_CategoryDocID=dc.DocumentCategory_ID
						  AND dc.DocumentCategory_Delete_Time IS NULL
						WHERE tdloold.TDLOOLD_Delete_Time IS NULL
						AND (
							tdloold.TDLOOLD_DocCode LIKE '%$search%'
							OR dc.DocumentCategory_Name LIKE '%$search%'
							OR dol.DOL_NamaDokumen LIKE '%$search%'
							OR dol.DOL_InstansiTerkait LIKE '%$search%'
							OR dol.DOL_NoDokumen LIKE '%$search%'
							OR dol.DOL_TglTerbit LIKE '%$search%'
							OR MONTH(dol.DOL_TglTerbit) LIKE '%$search%'
						)
						ORDER BY tdloold.TDLOOLD_DocCode ASC
						LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			//$tgl_terbit=date("d M Y", strtotime($arr['DOL_RegTime']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickol('<?= $arr['TDLOOLD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOOLD_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['DocumentCategory_Name'] ?></td>
				<td align='center'><?= $arr['DOL_NamaDokumen'] ?></td>
				<td align='center'><?= $arr['DOL_InstansiTerkait'] ?></td>
				<td align='center'><?= $arr['DOL_NoDokumen'] ?></td>
				<td align='center'><?= $arr['DOL_TglTerbit'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==6){
	$query="SELECT tdloonld.TDLOONLD_DocCode,donl.DONL_NoDokumen,donl.DONL_NamaDokumen,donl.DONL_TahunDokumen,m_d.Department_Name,
			  (SELECT COUNT(tdloonld.TDLOONLD_ID) Total
				FROM TD_LoanOfOtherNonLegalDocuments tdloonld
				INNER JOIN TH_LoanOfOtherNonLegalDocuments thloonld ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
					AND thloonld.THLOONLD_UserID='".$_COOKIE['User_ID']."'
					AND thloonld.THLOONLD_Delete_Time IS NULL
				INNER JOIN M_DocumentsOtherNonLegal donl ON donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
				  AND donl.DONL_Status='4'
				  AND donl.DONL_Delete_Time IS NULL
				INNER JOIN M_Company mc ON donl.DONL_PT_ID=mc.Company_ID
				  AND mc.Company_Delete_Time IS NULL
				INNER JOIN TD_ReleaseOfOtherNonLegalDocuments tdrloonld ON tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
				  AND tdrloonld.TDROONLD_ReturnCode='0'
				  AND tdrloonld.TDROONLD_Delete_Time IS NULL
				LEFT JOIN db_master.M_Department m_d ON donl.DONL_Dept_Code=m_d.Department_Code
				WHERE tdloonld.TDLOONLD_Delete_Time IS NULL) Total
			FROM TD_LoanOfOtherNonLegalDocuments tdloonld
			INNER JOIN TH_LoanOfOtherNonLegalDocuments thloonld ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
				AND thloonld.THLOONLD_UserID='".$_COOKIE['User_ID']."'
				AND thloonld.THLOONLD_Delete_Time IS NULL
			INNER JOIN M_DocumentsOtherNonLegal donl ON donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
			  AND donl.DONL_Status='4'
			  AND donl.DONL_Delete_Time IS NULL
			INNER JOIN TD_ReleaseOfOtherNonLegalDocuments tdrloonld ON tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
			  AND tdrloonld.TDROONLD_ReturnCode='0'
			  AND tdrloonld.TDROONLD_Delete_Time IS NULL
			LEFT JOIN db_master.M_Department m_d ON donl.DONL_Dept_Code=m_d.Department_Code
			WHERE tdloonld.TDLOONLD_Delete_Time IS NULL
			ORDER BY tdloonld.TDLOONLD_DocCode ASC
			LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='get' action='$PHP_SELF'>
				<input type='hidden' name='gID' value='$grup'/>
				<input type='hidden' name='txtKe' value='$txtKe'/>
				<input type='hidden' name='page' value='0'/>
				<div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
					<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%' value='$search'/>
				</div>
			</form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<!--th width='25%'>No. Pengeluaran</th-->
			<th width='16%'>Kode Dokumen</th>
			<th width='16%'>No. Dokumen</th>
			<th width='16%'>Nama Dokumen</th>
			<th width='16%'>Tahun Dokumen</th>
			<th width='16%'>Departemen</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT tdloonld.TDLOONLD_DocCode,donl.DONL_NoDokumen,donl.DONL_NamaDokumen,donl.DONL_TahunDokumen,m_d.Department_Name,
							(SELECT COUNT(tdloonld.TDLOONLD_ID) Total
								FROM TD_LoanOfOtherNonLegalDocuments tdloonld
								INNER JOIN TH_LoanOfOtherNonLegalDocuments thloonld ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
									AND thloonld.THLOONLD_UserID='".$_COOKIE['User_ID']."'
									AND thloonld.THLOONLD_Delete_Time IS NULL
								INNER JOIN M_DocumentsOtherNonLegal donl ON donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
								  AND donl.DONL_Status='4'
								  AND donl.DONL_Delete_Time IS NULL
								INNER JOIN TD_ReleaseOfOtherNonLegalDocuments tdrloonld ON tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
								  AND tdrloonld.TDROONLD_ReturnCode='0'
								  AND tdrloonld.TDROONLD_Delete_Time IS NULL
								LEFT JOIN db_master.M_Department m_d ON donl.DONL_Dept_Code=m_d.Department_Code
								WHERE tdloonld.TDLOONLD_Delete_Time IS NULL
								AND (
									tdloonld.TDLOONLD_DocCode LIKE '%$search%'
									OR donl.DONL_NoDokumen LIKE '%$search%'
									OR donl.DONL_NamaDokumen LIKE '%$search%'
									OR donl.DONL_TahunDokumen LIKE '%$search%'
									OR donl.DONL_DocCode LIKE '%$search%'
									OR m_d.Department_Name LIKE '%$search%'
								)
							) Total
						FROM TD_LoanOfOtherNonLegalDocuments tdloonld
						INNER JOIN TH_LoanOfOtherNonLegalDocuments thloonld ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
							AND thloonld.THLOONLD_UserID='".$_COOKIE['User_ID']."'
							AND thloonld.THLOONLD_Delete_Time IS NULL
						INNER JOIN M_DocumentsOtherNonLegal donl ON donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
						  AND donl.DONL_Status='4'
						  AND donl.DONL_Delete_Time IS NULL
						INNER JOIN TD_ReleaseOfOtherNonLegalDocuments tdrloonld ON tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
						  AND tdrloonld.TDROONLD_ReturnCode='0'
						  AND tdrloonld.TDROONLD_Delete_Time IS NULL
						LEFT JOIN db_master.M_Department m_d ON donl.DONL_Dept_Code=m_d.Department_Code
						WHERE tdloonld.TDLOONLD_Delete_Time IS NULL
						AND (
							tdloonld.TDLOONLD_DocCode LIKE '%$search%'
							OR donl.DONL_NoDokumen LIKE '%$search%'
							OR donl.DONL_NamaDokumen LIKE '%$search%'
							OR donl.DONL_TahunDokumen LIKE '%$search%'
							OR donl.DONL_DocCode LIKE '%$search%'
							OR m_d.Department_Name LIKE '%$search%'
						)
						ORDER BY tdloonld.TDLOONLD_DocCode ASC
						LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			//$tgl_terbit=date("d M Y", strtotime($arr['DONL_RegTime']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickonl('<?= $arr['TDLOONLD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOONLD_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['DONL_NoDokumen'] ?></td>
				<td align='center'><?= $arr['DONL_NamaDokumen'] ?></td>
				<td align='center'><?= $arr['DONL_TahunDokumen'] ?></td>
				<td align='center'><?= $arr['Department_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

?>
</TABLE>
<?php
	echo "<div id='pagerContainer' class='pagerContainer'>";
	if($maxDataNum>0){
		if($maxDataNum<$dataPerPage){
			echo "<span>1</span>";
		}
		else{
			$pageNum = ceil($maxDataNum/$dataPerPage);
			if($currPage>0){
				echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=0&txtSearch=$search'>".(1)."</a>";
			}
			if($currPage>3){
				echo "<span class='pageNumber'>...</span>";
			}
			if($currPage>1){
				if($currPage>2){
					echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1-2)."&txtSearch=$search'>".($currPage+1-2)."</a>";
				}
					echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1-1)."&txtSearch=$search'>".($currPage+1-1)."</a>";
			}
			echo "<form class='pageNumber' method='get' style='width:10%'>
						<input type='hidden' name='gID' value='$grup'/>
						<input type='hidden' name='txtKe' value='$txtKe'/>
						<input type='hidden' name='txtSearch' value='$search'/>
						<input type='text' style='width:100%;text-align:center;' name='page' value='".($currPage+1)."'/>
					</form>";

			if($currPage<$pageNum-2){
				echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1+1)."&txtSearch=$search'>".($currPage+1+1)."</a>";
				if($currPage<$pageNum-3){
					echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1+2)."&txtSearch=$search'>".($currPage+1+2)."</a>";
				}
			}
			if($currPage<$pageNum-4){
				echo "<span class='pageNumber'>...</span>";
			}
			if($currPage<$pageNum-1){
				echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($pageNum)."&txtSearch=$search'>".$pageNum."</a>";
			}
		}
	}
	echo "<div style='clear:both'></div>";
?>
</BODY>
</HTML>
