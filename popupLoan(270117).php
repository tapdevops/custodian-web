<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Permintaan Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pick(symbol) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.addRelDoc.txtTHROLD_THLOLD_Code.value = symbol;
	window.close();
	}
}
function pickla(symbol) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.addRelDoc.txtTHRLOLAD_THLOLAD_Code.value = symbol;
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

IF ($grup=="non_grl"){
	$query="SELECT DISTINCT thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate, u.User_FullName,
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
			$query =   "SELECT DISTINCT thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate, u.User_FullName,
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
				<td align='center'><u><a href="javascript:pick('<?= $arr['THLOLD_LoanCode'] ?>')"><?= $arr['THLOLD_LoanCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['User_FullName'] ?></td>
				<td align='center'><?= $arr['LoanCategory_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

ELSE{
	$query="SELECT DISTINCT thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, u.User_FullName,
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
			$query =   "SELECT DISTINCT thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, u.User_FullName,
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
				<td align='center'><u><a href="javascript:pickla('<?= $arr['THLOLAD_LoanCode'] ?>')"><?= $arr['THLOLAD_LoanCode'] ?></a></u></td>
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