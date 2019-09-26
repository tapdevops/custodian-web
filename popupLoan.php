<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Permintaan Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pick(symbol) {
	var result = symbol.split('||');

	if (window.opener && !window.opener.closed) {
    window.opener.document.addRelDoc.txtTHROLD_THLOLD_Code.value = result[0];
    window.opener.document.addRelDoc.txtTHROLD_Information.value = result[1];
	window.close();
	}
}
function pickla(symbol) {
	var result = symbol.split('||');
	if (window.opener && !window.opener.closed) {
    window.opener.document.addRelDoc.txtTHRLOLAD_THLOLAD_Code.value = result[0];
    window.opener.document.addRelDoc.txtTHRLOLAD_Information.value = result[1];
	window.close();
	}
}
function pickao(symbol) {
	var result = symbol.split('||');
	if (window.opener && !window.opener.closed) {
    window.opener.document.addRelDoc.txtTHROAOD_THLOAOD_Code.value = result[0];
    window.opener.document.addRelDoc.txtTHROAOD_Information.value = result[1];
	window.close();
	}
}
function pickol(symbol) {
	var result = symbol.split('||');
	if (window.opener && !window.opener.closed) {
    window.opener.document.addRelDoc.txtTHROOLD_THLOOLD_Code.value = result[0];
    window.opener.document.addRelDoc.txtTHROOLD_Information.value = result[1];
	window.close();
	}
}
function pickonl(symbol) {
	var result = symbol.split('||');
	if (window.opener && !window.opener.closed) {
    window.opener.document.addRelDoc.txtTHROONLD_THLOONLD_Code.value = result[0];
    window.opener.document.addRelDoc.txtTHROONLD_Information.value = result[1];
	window.close();
	}
}
// -->
</SCRIPT>
<link href="./css/style.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<?PHP
$grup=$_GET['gID'];

if ($grup=="1"){ //Arief F - 14092018
	$query="SELECT DISTINCT thlold.THLOLD_LoanCode, thlold.THLOLD_Information, thlold.THLOLD_LoanDate, u.User_FullName,
							c.Company_Name, lc.LoanCategory_Name
			FROM TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold,
				 M_User u, M_Company c, M_LoanCategory lc
			WHERE thlold.THLOLD_Delete_Time is NULL
			AND thlold.THLOLD_CompanyID=c.Company_ID
			AND thlold.THLOLD_UserID=u.User_ID
			AND thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID
			AND thlold.THLOLD_Status='accept'
			AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
			AND tdlold.TDLOLD_Response='0'
			ORDER BY thlold.THLOLD_LoanCode
			LIMIT 0,10";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Permintaan Dokumen Yang Tersedia</div>
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
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>Kode Permintaan</th>
			<th width='25%'>Tanggal Permintaan</th>
			<th width='25%'>Peminta</th>
			<th width='25%'>Kategori Permintaan</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thlold.THLOLD_LoanCode, thlold.THLOLD_Information, thlold.THLOLD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thlold.THLOLD_Delete_Time is NULL
						AND thlold.THLOLD_CompanyID=c.Company_ID
						AND thlold.THLOLD_UserID=u.User_ID
						AND thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID
						AND thlold.THLOLD_Status='accept'
						AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
						AND tdlold.TDLOLD_Response='0'
						AND (
							thlold.THLOLD_LoanCode LIKE '%$search%'
							OR thlold.THLOLD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thlold.THLOLD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$loandate=date("d M Y", strtotime($arr[THLOLD_LoanDate]));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['THLOLD_LoanCode'] ?>||<?= $arr['THLOLD_Information'] ?>')"><?= $arr['THLOLD_LoanCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['User_FullName'] ?></td>
				<td align='center'><?= $arr['LoanCategory_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==3){ //Arief F - 14092018
	$query="SELECT DISTINCT thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_Information, thlolad.THLOLAD_LoanDate, u.User_FullName,
							c.Company_Name, lc.LoanCategory_Name
			FROM TH_LoanOfLandAcquisitionDocument thlolad,TD_LoanOfLandAcquisitionDocument tdlolad,
				 M_User u, M_Company c, M_LoanCategory lc
			WHERE thlolad.THLOLAD_Delete_Time is NULL
			AND thlolad.THLOLAD_CompanyID=c.Company_ID
			AND thlolad.THLOLAD_UserID=u.User_ID
			AND thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
			AND thlolad.THLOLAD_Status='accept'
			AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
			AND tdlolad.TDLOLAD_Response='0'
			ORDER BY thlolad.THLOLAD_LoanCode
			LIMIT 0,10";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Permintaan Dokumen Yang Tersedia</div>
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
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>Kode Permintaan</th>
			<th width='25%'>Tanggal Permintaan</th>
			<th width='25%'>Peminta</th>
			<th width='25%'>Kategori Permintaan</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_Information, thlolad.THLOLAD_LoanDate, u.User_FullName,
							   c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfLandAcquisitionDocument thlolad,TD_LoanOfLandAcquisitionDocument tdlolad,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thlolad.THLOLAD_Delete_Time is NULL
							  AND thlolad.THLOLAD_CompanyID=c.Company_ID
							  AND thlolad.THLOLAD_UserID=u.User_ID
							  AND thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
							  AND thlolad.THLOLAD_Status='accept'
							  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
							  AND tdlolad.TDLOLAD_Response='0'
						AND (
							thlolad.THLOLAD_LoanCode LIKE '%$search%'
							OR thlolad.THLOLAD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thlolad.THLOLAD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$loandate=date("d M Y", strtotime($arr[THLOLAD_LoanDate]));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pickla('<?= $arr['THLOLAD_LoanCode'] ?>||<?= $arr['THLOLAD_Information'] ?>')"><?= $arr['THLOLAD_LoanCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['User_FullName'] ?></td>
				<td align='center'><?= $arr['LoanCategory_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==4){ //Arief F - 14092018
	$query="SELECT DISTINCT thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_Information, thloaod.THLOAOD_LoanDate, u.User_FullName,
							c.Company_Name, lc.LoanCategory_Name
			FROM TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
				 M_User u, M_Company c, M_LoanCategory lc
			WHERE thloaod.THLOAOD_Delete_Time is NULL
			AND thloaod.THLOAOD_CompanyID=c.Company_ID
			AND thloaod.THLOAOD_UserID=u.User_ID
			AND thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
			AND thloaod.THLOAOD_Status='accept'
			AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
			AND tdloaod.TDLOAOD_Response='0'
			ORDER BY thloaod.THLOAOD_LoanCode
			LIMIT 0,10";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Permintaan Dokumen Yang Tersedia</div>
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
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>Kode Permintaan</th>
			<th width='25%'>Tanggal Permintaan</th>
			<th width='25%'>Peminta</th>
			<th width='25%'>Kategori Permintaan</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_Information, thloaod.THLOAOD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thloaod.THLOAOD_Delete_Time is NULL
						AND thloaod.THLOAOD_CompanyID=c.Company_ID
						AND thloaod.THLOAOD_UserID=u.User_ID
						AND thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
						AND thloaod.THLOAOD_Status='accept'
						AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
						AND tdloaod.TDLOAOD_Response='0'
						AND (
							thloaod.THLOAOD_LoanCode LIKE '%$search%'
							OR thloaod.THLOAOD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thloaod.THLOAOD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$loandate=date("d M Y", strtotime($arr['THLOAOD_LoanDate']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickao('<?= $arr['THLOAOD_LoanCode'] ?>||<?= $arr['THLOAOD_Information'] ?>')"><?= $arr['THLOAOD_LoanCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['User_FullName'] ?></td>
				<td align='center'><?= $arr['LoanCategory_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==5){ //Arief F - 14092018
	$query="SELECT DISTINCT thloold.THLOOLD_LoanCode, thloold.THLOOLD_Information, thloold.THLOOLD_LoanDate, u.User_FullName,
							c.Company_Name, lc.LoanCategory_Name
			FROM TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold,
				 M_User u, M_Company c, M_LoanCategory lc
			WHERE thloold.THLOOLD_Delete_Time is NULL
			AND thloold.THLOOLD_CompanyID=c.Company_ID
			AND thloold.THLOOLD_UserID=u.User_ID
			AND thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
			AND thloold.THLOOLD_Status='accept'
			AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
			AND tdloold.TDLOOLD_Response='0'
			ORDER BY thloold.THLOOLD_LoanCode
			LIMIT 0,10";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Permintaan Dokumen Yang Tersedia</div>
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
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>Kode Permintaan</th>
			<th width='25%'>Tanggal Permintaan</th>
			<th width='25%'>Peminta</th>
			<th width='25%'>Kategori Permintaan</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thloold.THLOOLD_LoanCode, thloold.THLOOLD_Information, thloold.THLOOLD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thloold.THLOOLD_Delete_Time is NULL
						AND thloold.THLOOLD_CompanyID=c.Company_ID
						AND thloold.THLOOLD_UserID=u.User_ID
						AND thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
						AND thloold.THLOOLD_Status='accept'
						AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
						AND tdloold.TDLOOLD_Response='0'
						AND (
							thloold.THLOOLD_LoanCode LIKE '%$search%'
							OR thloold.THLOOLD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thloold.THLOOLD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$loandate=date("d M Y", strtotime($arr['THLOOLD_LoanDate']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickol('<?= $arr['THLOOLD_LoanCode'] ?>||<?= $arr['THLOOLD_Information'] ?>')"><?= $arr['THLOOLD_LoanCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['User_FullName'] ?></td>
				<td align='center'><?= $arr['LoanCategory_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==6){ //Arief F - 14092018
	$query="SELECT DISTINCT thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_Information, thloonld.THLOONLD_LoanDate, u.User_FullName,
							c.Company_Name, lc.LoanCategory_Name
			FROM TH_LoanOfOtherNonLegalDocuments thloonld, TD_LoanOfOtherNonLegalDocuments tdloonld,
				 M_User u, M_Company c, M_LoanCategory lc
			WHERE thloonld.THLOONLD_Delete_Time is NULL
			AND thloonld.THLOONLD_CompanyID=c.Company_ID
			AND thloonld.THLOONLD_UserID=u.User_ID
			AND thloonld.THLOONLD_LoanCategoryID=lc.LoanCategory_ID
			AND thloonld.THLOONLD_Status='accept'
			AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
			AND tdloonld.TDLOONLD_Response='0'
			ORDER BY thloonld.THLOONLD_LoanCode
			LIMIT 0,10";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Permintaan Dokumen Yang Tersedia</div>
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
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>Kode Permintaan</th>
			<th width='25%'>Tanggal Permintaan</th>
			<th width='25%'>Peminta</th>
			<th width='25%'>Kategori Permintaan</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_Information,thloonld.THLOONLD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfOtherNonLegalDocuments thloonld, TD_LoanOfOtherNonLegalDocuments tdloonld,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thloonld.THLOONLD_Delete_Time is NULL
						AND thloonld.THLOONLD_CompanyID=c.Company_ID
						AND thloonld.THLOONLD_UserID=u.User_ID
						AND thloonld.THLOONLD_LoanCategoryID=lc.LoanCategory_ID
						AND thloonld.THLOONLD_Status='accept'
						AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
						AND tdloonld.TDLOONLD_Response='0'
						AND (
							thloonld.THLOONLD_LoanCode LIKE '%$search%'
							OR thloonld.THLOONLD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thloonld.THLOONLD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$loandate=date("d M Y", strtotime($arr['THLOONLD_LoanDate']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickonl('<?= $arr['THLOONLD_LoanCode'] ?>||<?= $arr['THLOONLD_Information'] ?>')"><?= $arr['THLOONLD_LoanCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['User_FullName'] ?></td>
				<td align='center'><?= $arr['LoanCategory_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}
?>
</TABLE>
</BODY>
</HTML>
