<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 06 Juni 2012																						=
= Update Terakhir	: 06 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Pengembalian Dokumen</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico">
<link href="./css/style-print.css" rel="stylesheet" type="text/css">
<script src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
   	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
   	$(".stripeMe tr:even").addClass("alt");
 	});
</script>
<SCRIPT>
function printPage(){
document.getElementById('PrintButton').style.display = "none"
window.print()
document.getElementById('PrintButton').style.display = "block"
}
</SCRIPT>

</head>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
?>

<body>
<div id='header'>
<input type='button' name="PrintButton" id="PrintButton" onclick='printPage()' value='CETAK' class='print-button' />
	<div id='header-inside'>
    	<div class="tap">PT Triputra Agro Persada </div>
        <div class="custodian">Custodian Department </div>
        <div class="alamat">Jalan DR.Ide Anak Agung Gde Agung Kav. E.3.2. No 1<br />
        Jakarta - 12950</div>
    </div>
</div>
<div id='content'>
<?PHP
$query = "";
$txtStart=date('Y-m-d', strtotime($_POST['txtStart']))." 00:00:00";
$txtEnd=date('Y-m-d', strtotime($_POST['txtEnd']))." 23:59:59";

$start=date('j M Y', strtotime($_POST['txtStart']));
$end=date('j M Y', strtotime($_POST['txtEnd']));
$periode=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"Periode $start s/d $end";

if ($_POST['optDocumentGroup'] == "1" || $_POST['optDocumentGroup'] == "2") {
	$qcompany=($_POST['optCompany'] == "ALL")?"":"AND thlold.THLOLD_CompanyID='$_POST[optCompany]'";
	$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
	$qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdlold.TDLOLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
	$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtold.TDRTOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

	$query = "SELECT DISTINCT tdrtold.TDRTOLD_ReturnCode, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
								  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc,
			  		   TH_ReleaseOfLegalDocument thrlold, TD_ReleaseOfLegalDocument tdrlold,
			  		   TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold,
			  		   TD_ReturnOfLegalDocument tdrtold, M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				  AND tdrtold.TDRTOLD_Delete_Time IS NULL
				  AND tdrlold.TDROLD_ReturnCode=tdrtold.TDRTOLD_ReturnCode
				  AND tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				  AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
				  AND tdlold.TDLOLD_DocCode=tdrtold.TDRTOLD_DocCode
				  AND thlold.THLOLD_DocumentGroupID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thlold.THLOLD_CompanyID=c.Company_ID
				  AND tdlold.TDLOLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thlold.THLOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID";
}elseif ($_POST['optDocumentGroup']=='3'){
	$qcompany=($_POST['optCompany'] == "ALL")?"":"AND thlolad.THLOLAD_CompanyID='$_POST[optCompany]'";
	$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
	$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtolad.TDRTOLAD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

	$query = "SELECT DISTINCT tdrtolad.TDRTOLAD_ReturnCode, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfLandAcquisitionDocument thrlolad, TD_ReleaseOfLandAcquisitionDocument tdrlolad,
			  		   TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
			  		   TD_ReturnOfLandAcquisitionDocument tdrtolad, M_DivisionDepartmentPosition ddp
				  WHERE thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				  AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
				  AND tdrlolad.TDRLOLAD_ReturnCode=tdrtolad.TDRTOLAD_ReturnCode
				  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				  AND tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
				  $qcompany
				  $qarea
				  $qperiod
				  AND dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
				  AND thlolad.THLOLAD_CompanyID=c.Company_ID
				  AND thlolad.THLOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID";
  }elseif ($_POST['optDocumentGroup']=='4'){
      $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thloaod.THLOAOD_CompanyID='$_POST[optCompany]'";
      $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
      $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtoaod.TDRTOAOD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

      $query = "SELECT DISTINCT tdrtoaod.TDRTOAOD_ReturnCode, thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_LoanDate,
                                u.User_FullName, dp.Department_Name,
                                dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
                FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
                     TH_ReleaseOfAssetOwnershipDocument thrloaod, TD_ReleaseOfAssetOwnershipDocument tdrloaod,
                     TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
                     TD_ReturnOfAssetOwnershipDocument tdrtoaod, M_DivisionDepartmentPosition ddp
                WHERE thrloaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
                AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL
                AND tdrloaod.TDROAOD_ReturnCode=tdrtoaod.TDRTOAOD_ReturnCode
                AND tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
                AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
                AND tdloaod.TDLOAOD_DocCode=tdrtoaod.TDRTOAOD_DocCode
                $qcompany
                $qarea
                $qperiod
                AND dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
                AND thloaod.THLOAOD_CompanyID=c.Company_ID
                AND thloaod.THLOAOD_UserID=u.User_ID
                AND ddp.DDP_UserID=u.User_ID
                AND ddp.DDP_DeptID=dp.Department_ID";
  }elseif ($_POST['optDocumentGroup']=='5'){
      $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thloold.THLOOLD_CompanyID='$_POST[optCompany]'";
      $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
      $qcategory=(!$_POST['optDocumentCategory'])?"":"AND dol.DOL_CategoryDocID='$_POST[optDocumentCategory]'";
      $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtoold.TDRTOOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

      $query = "SELECT DISTINCT tdrtoold.TDRTOOLD_ReturnCode, thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate,
                                u.User_FullName, dp.Department_Name,
                                dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
                FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
                     TH_ReleaseOfOtherLegalDocuments thrloold, TD_ReleaseOfOtherLegalDocuments tdrloold,
                     TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold,
                     TD_ReturnOfOtherLegalDocuments tdrtoold, M_DivisionDepartmentPosition ddp,
                     M_DocumentsOtherLegal dol
                WHERE thrloold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
                AND tdrtoold.TDRTOOLD_Delete_Time IS NULL
                AND tdrloold.TDROOLD_ReturnCode=tdrtoold.TDRTOOLD_ReturnCode
                AND tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
                AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
                AND tdloold.TDLOOLD_DocCode=tdrtoold.TDRTOOLD_DocCode
                AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
                $qcompany
                $qarea
                $qcategory
                $qperiod
                AND dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
                AND thloold.THLOOLD_CompanyID=c.Company_ID
                AND thloold.THLOOLD_UserID=u.User_ID
                AND ddp.DDP_UserID=u.User_ID
                AND ddp.DDP_DeptID=dp.Department_ID";
  }elseif ($_POST['optDocumentGroup']=='6'){
      $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thloonld.THLOONLD_CompanyID='$_POST[optCompany]'";
      $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
      $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtoonld.TDRTOONLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

      $query = "SELECT DISTINCT tdrtoonld.TDRTOONLD_ReturnCode, thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_LoanDate,
                                u.User_FullName, dp.Department_Name,
                                dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
                FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
                     TH_ReleaseOfOtherNonLegalDocuments thrloonld, TD_ReleaseOfOtherNonLegalDocuments tdrloonld,
                     TH_LoanOfOtherNonLegalDocuments thloonld, TD_LoanOfOtherNonLegalDocuments tdloonld,
                     TD_ReturnOfOtherNonLegalDocuments tdrtoonld, M_DivisionDepartmentPosition ddp
                WHERE thrloonld.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
                AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
                AND tdrloonld.TDROONLD_ReturnCode=tdrtoonld.TDRTOONLD_ReturnCode
                AND tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
                AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
                AND tdloonld.TDLOONLD_DocCode=tdrtoonld.TDRTOONLD_DocCode
                $qcompany
                $qarea
                $qperiod
                AND dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
                AND thloonld.THLOONLD_CompanyID=c.Company_ID
                AND thloonld.THLOONLD_UserID=u.User_ID
                AND ddp.DDP_UserID=u.User_ID
                AND ddp.DDP_DeptID=dp.Department_ID";
  }

$sql = mysql_query($query);
$h_sql= mysql_query($query);
$h_arr = mysql_fetch_array($h_sql);
$jumdata=0;


echo"
<div id='title'>Laporan Pengembalian Dokumen</div>
<div class='h2'>$periode</div>
<div class='h5'>Tanggal Cetak : ".date('j M Y')."</div>";

if ($_POST['optDocumentGroup'] == "1" || $_POST['optDocumentGroup'] == "2") {
	$jumdata=0;
	while ($h_arr = mysql_fetch_array($sql)) {
		if ($jumdata==2) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else
		{
			$style="";
		}

		$loandate=date("j M Y", strtotime($h_arr['THLOLD_LoanDate']));
		$d_query="SELECT tdrtold.TDRTOLD_ReturnTime, tdrtold.TDRTOLD_DocCode, dt.DocumentType_Name,
						 dl.DL_Instance, dl.DL_NoDoc, dl.DL_ExpDate,thrlold.THROLD_ReleaseCode,
						 thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime
				  FROM TD_ReturnOfLegalDocument tdrtold
				  LEFT JOIN M_DocumentLegal dl
				  ON tdrtold.TDRTOLD_DocCode=dl.DL_DocCode
				  LEFT JOIN M_DocumentType dt
				  ON dl.DL_TypeDocID=dt.DocumentType_ID
				  LEFT JOIN TD_ReleaseOfLegalDocument tdrlold
				  ON tdrlold.TDROLD_ReturnCode= tdrtold.TDRTOLD_ReturnCode
				  LEFT JOIN TH_ReleaseOfLegalDocument thrlold
				  ON tdrlold.TDROLD_TDLOLD_ID=thrlold.THROLD_ID
				  WHERE tdrtold.TDRTOLD_ReturnCode='$h_arr[TDRTOLD_ReturnCode]'
				  AND tdrtold.TDRTOLD_Delete_Time IS NULL";
		$d_sql=mysql_query($d_query);

		echo"
		<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
		<tr>
			<td width='19%'>Kode Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$h_arr[THLOLD_LoanCode]</td>
			<td width='19%'>Tanggal Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$loandate</td>
		</tr>
		<tr>
			<td>Nama Peminta</td>
			<td>:</td>
			<td>$h_arr[User_FullName]</td>
			<td>Departemen</td>
			<td>:</td>
			<td>$h_arr[Department_Name]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>:</td>
			<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
			<td></td><td></td><td></td>
		</tr>
		<tr>
			<td>Grup Dokumen</td>
			<td>:</td>
			<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
			<td>Kategori Dokumen</td>
			<td>:</td>
			<td><input type='hidden' name='optDocumentCategory' value=$h_arr[DocumentCategory_ID]>$h_arr[DocumentCategory_Name]</td>
		</tr>
		</table>

		<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
		<tr>
			<th width='200'>Tanggal Pengembalian</th>
			<th width='200'>Kode Dokumen</th>
			<th width='200'>Nama Dokumen</th>
			<th width='200'>Instansi Terkait</th>
			<th width='200'>Nomor Dokumen</th>
			<th width='200'>Berlaku Sampai</th>
			<th width='200'>Kode Pengeluaran</th>
			<th width='200'>Tanggal Pengeluaran</th>
			<th width='200'>Lead Time</th>
		</tr>";

		while ($arr = mysql_fetch_array($d_sql)) {
			$reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
			$retdate=date("j M Y", strtotime($arr['TDRTOLD_ReturnTime']));
			if ($arr['DL_ExpDate']=="0000-00-00 00:00:00")
				$expdate="-";
			else
				$expdate=date("j M Y", strtotime($arr['DL_ExpDate']));
			if ($arr['TDROLD_LeadTime']=="0000-00-00 00:00:00")
				$leaddate="-";
			else
				$leaddate=date("j M Y", strtotime($arr['TDROLD_LeadTime']));
		echo"
		<tr>
			<td class='center'>$retdate</td>
			<td class='center'>$arr[TDRTOLD_DocCode]</td>
			<td class='center'>$arr[DocumentType_Name]</td>
			<td class='center'>$arr[DL_Instance]</td>
			<td class='center'>$arr[DL_NoDoc]</td>
			<td class='center'>$expdate</td>
			<td class='center'>$arr[THROLD_ReleaseCode]</td>
			<td class='center'>$reldate</td>
			<td class='center'>$leaddate</td>
		</tr>";
		}
		echo"</table>";
		$jumdata++;
	}
}elseif ($_POST['optDocumentGroup']=='3'){
	$jumdata=0;
	while ($h_arr = mysql_fetch_array($sql)) {
		if ($jumdata==2) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else
		{
			$style="";
		}

		$loandate=date("j M Y", strtotime($h_arr['THLOLAD_LoanDate']));

		$d_query="SELECT tdrtolad.TDRTOLAD_ReturnTime, tdrtolad.TDRTOLAD_DocCode, dla.DLA_Phase,
						 dla.DLA_Period, dla.DLA_Village, dla.DLA_Block,
					 	 dla.DLA_Owner, thrlolad.THRLOLAD_ReleaseCode,
						 thrlolad.THRLOLAD_ReleaseDate, tdrlolad.TDRLOLAD_LeadTime
				  FROM TD_ReturnOfLandAcquisitionDocument tdrtolad
				  LEFT JOIN M_DocumentLandAcquisition dla
				  ON tdrtolad.TDRTOLAD_DocCode=dla.DLA_Code
				  LEFT JOIN TD_ReleaseOfLandAcquisitionDocument tdrlolad
				  ON tdrlolad.TDRLOLAD_ReturnCode= tdrtolad.TDRTOLAD_ReturnCode
				  LEFT JOIN TH_ReleaseOfLandAcquisitionDocument thrlolad
				  ON tdrlolad.TDRLOLAD_TDLOLAD_ID=thrlolad.THRLOLAD_ID
				  WHERE tdrtolad.TDRTOLAD_ReturnCode='$h_arr[TDRTOLAD_ReturnCode]'
				  AND tdrtolad.TDRTOLAD_Delete_Time IS NULL";
		$d_sql=mysql_query($d_query);

		echo"
		<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
		<tr>
			<td width='19%'>Kode Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$h_arr[THLOLAD_LoanCode]</td>
			<td width='19%'>Tanggal Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$loandate</td>
		</tr>
		<tr>
			<td>Nama Peminta</td>
			<td>:</td>
			<td>$h_arr[User_FullName]</td>
			<td>Departemen</td>
		<td>:</td>
			<td>$h_arr[Department_Name]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>:</td>
			<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
			<td>Grup Dokumen</td>
			<td>:</td>
			<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
		</tr>
		</table>

		<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
		<tr>
			<th width='200'>Tanggal Pengembalian</th>
			<th width='200'>Kode Dokumen</th>
			<th width='50'>Tahap</th>
			<th width='200'>Periode</th>
			<th width='200'>Desa</th>
			<th width='200'>Blok</th>
			<th width='200'>Pemilik</th>
			<th width='200'>Kode Pengeluaran</th>
			<th width='200'>Tanggal Pengeluaran</th>
			<th width='200'>Lead Time</th>
		</tr>";

		while ($arr = mysql_fetch_array($d_sql)) {
			$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
			$retdate=date("j M Y", strtotime($arr['TDRTOLAD_ReturnTime']));
			$period=date("j M Y", strtotime($arr['DLA_Period']));
			if ($arr['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")
				$leaddate="-";
			else
				$leaddate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
		echo"
		<tr>
			<td class='center'>$retdate</td>
			<td class='center'>$arr[TDRTOLAD_DocCode]</td>
			<td class='center'>$arr[DLA_Phase]</td>
			<td class='center'>$period</td>
			<td class='center'>$arr[DLA_Village]</td>
			<td class='center'>$arr[DLA_Block]</td>
			<td class='center'>$arr[DLA_Owner]</td>
			<td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
			<td class='center'>$reldate</td>
			<td class='center'>$leaddate</td>
		</tr>";
		}
		echo"</table>";
		$jumdata++;
	}
}elseif ($_POST['optDocumentGroup']=='4'){
	$jumdata=0;
	while ($h_arr = mysql_fetch_array($sql)) {
		if ($jumdata==2) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else
		{
			$style="";
		}

        $loandate=date("j M Y", strtotime($h_arr['THLOAOD_LoanDate']));

        $d_query="SELECT tdrtoaod.TDRTOAOD_ReturnTime, tdrtoaod.TDRTOAOD_DocCode, m_mk.MK_Name merk_kendaraan,
                         CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
                          THEN
                            (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
                          ELSE
                            (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
                         END nama_pemilik,
                         dao.DAO_NoPolisi, CASE WHEN dao.DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
                            WHEN dao.DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
                            ELSE DATE_FORMAT(dao.DAO_STNK_StartDate, '%d/%m/%Y')
                         END AS start_stnk,
                         CASE WHEN dao.DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
                            WHEN dao.DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
                            ELSE DATE_FORMAT(dao.DAO_STNK_ExpiredDate, '%d/%m/%Y')
                         END AS expired_stnk, thrloaod.THROAOD_ReleaseCode,
                         thrloaod.THROAOD_ReleaseDate, tdrloaod.TDROAOD_LeadTime
                  FROM TD_ReturnOfAssetOwnershipDocument tdrtoaod
                  LEFT JOIN M_DocumentAssetOwnership dao
                    ON tdrtoaod.TDRTOAOD_DocCode=dao.DAO_DocCode
                  LEFT JOIN TD_ReleaseOfAssetOwnershipDocument tdrloaod
                    ON tdrloaod.TDROAOD_ReturnCode= tdrtoaod.TDRTOAOD_ReturnCode
                  LEFT JOIN TH_ReleaseOfAssetOwnershipDocument thrloaod
                    ON tdrloaod.TDROAOD_TDLOAOD_ID=thrloaod.THROAOD_ID
                  LEFT JOIN db_master.M_MerkKendaraan m_mk
                    ON m_mk.MK_ID=dao.DAO_MK_ID
                  WHERE tdrtoaod.TDRTOAOD_ReturnCode='$h_arr[TDRTOAOD_ReturnCode]'
                  AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL";
		$d_sql=mysql_query($d_query);

		echo"
        <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
        <tr>
            <td width='19%'>Kode Permintaan</td>
            <td width='1%'>:</td>
            <td width='30%'>$h_arr[THLOAOD_LoanCode]</td>
            <td width='19%'>Tanggal Permintaan</td>
            <td width='1%'>:</td>
            <td width='30%'>$loandate</td>
        </tr>
        <tr>
            <td>Nama Peminta</td>
            <td>:</td>
            <td>$h_arr[User_FullName]</td>
            <td>Departemen</td>
            <td>:</td>
            <td>$h_arr[Department_Name]</td>
        </tr>
        <tr>
            <td>Perusahaan</td>
            <td>:</td>
            <td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
            <td>Grup Dokumen</td>
            <td>:</td>
            <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
        </tr>
        </table>

        <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
        <tr>
            <th>Tanggal Pengembalian</th>
            <th>Kode Dokumen</th>
            <th>Nama Pemilik</th>
            <th>Merk Kendaraan</th>
            <th>Nomor Polisi</th>
            <th>Masa STNK</th>
            <th>Kode Pengeluaran</th>
            <th>Tanggal Pengeluaran</th>
            <th>Lead Time</th>
        </tr>";

        while ($arr = mysql_fetch_array($d_sql)) {
            $reldate=date("j M Y", strtotime($arr['THROAOD_ReleaseDate']));
            $retdate=date("j M Y", strtotime($arr['TDRTOAOD_ReturnTime']));
            if ($arr['TDROAOD_LeadTime']=="0000-00-00 00:00:00")
                $leaddate="-";
            else
                $leaddate=date("j M Y", strtotime($arr['TDROAOD_LeadTime']));
        echo "
        <tr>
            <td class='center'>$retdate</td>
            <td class='center'>$arr[TDRTOAOD_DocCode]</td>
            <td class='center'>$arr[nama_pemilik]</td>
            <td class='center'>$arr[merk_kendaraan]</td>
            <td class='center'>$arr[DAO_NoPolisi]</td>
            <td class='center'>$arr[start_stnk] s/d $arr[expired_stnk]</td>
            <td class='center'>$arr[THROAOD_ReleaseCode]</td>
            <td class='center'>$reldate</td>
            <td class='center'>$leaddate</td>
        </tr>";
		}
		echo"</table>";
		$jumdata++;
	}
}elseif ($_POST['optDocumentGroup']=='5'){
    $jumdata=0;
    while ($h_arr = mysql_fetch_array($sql)) {
        if ($jumdata==2) {
            $style="style='page-break-after:always'";
            $jumdata=0;
        }
        else
        {
            $style="";
        }

        $loandate=date("j M Y", strtotime($h_arr['THLOOLD_LoanDate']));

        $d_query="SELECT tdrtoold.TDRTOOLD_ReturnTime, tdrtoold.TDRTOOLD_DocCode,
                         mdc.DocumentCategory_Name kategori_dokumen, dol.DOL_NamaDokumen,
                         dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
                         dol.DOL_TglTerbit, dol.DOL_TglBerakhir, thrloold.THROOLD_ReleaseCode,
                         thrloold.THROOLD_ReleaseDate, tdrloold.TDROOLD_LeadTime
                  FROM TD_ReturnOfOtherLegalDocuments tdrtoold
                  LEFT JOIN M_DocumentsOtherLegal dol
                    ON tdrtoold.TDRTOOLD_DocCode=dol.DOL_DocCode
                    $qcategory
                  LEFT JOIN TD_ReleaseOfOtherLegalDocuments tdrloold
                    ON tdrloold.TDROOLD_ReturnCode= tdrtoold.TDRTOOLD_ReturnCode
                  LEFT JOIN TH_ReleaseOfOtherLegalDocuments thrloold
                    ON tdrloold.TDROOLD_TDLOOLD_ID=thrloold.THROOLD_ID
                  LEFT JOIN db_master.M_DocumentCategory mdc
                    ON mdc.DocumentCategory_ID=dol.DOL_CategoryDocID
                  WHERE tdrtoold.TDRTOOLD_ReturnCode='$h_arr[TDRTOOLD_ReturnCode]'
                  AND tdrtoold.TDRTOOLD_Delete_Time IS NULL";
        $d_sql=mysql_query($d_query);
        echo"
        <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
        <tr>
            <td width='19%'>Kode Permintaan</td>
            <td width='1%'>:</td>
            <td width='30%'>$h_arr[THLOOLD_LoanCode]</td>
            <td width='19%'>Tanggal Permintaan</td>
            <td width='1%'>:</td>
            <td width='30%'>$loandate</td>
        </tr>
        <tr>
            <td>Nama Peminta</td>
            <td>:</td>
            <td>$h_arr[User_FullName]</td>
            <td>Departemen</td>
            <td>:</td>
            <td>$h_arr[Department_Name]</td>
        </tr>
        <tr>
            <td>Perusahaan</td>
            <td>:</td>
            <td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
            <td>Grup Dokumen</td>
            <td>:</td>
            <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
        </tr>
        </table>

        <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
        <tr>
            <th>Tanggal Pengembalian</th>
            <th>Kode Dokumen</th>
            <th>Kategori Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Instansi Terkait</th>
            <th>Nomor Dokumen</th>
            <th>Berlaku Sampai</th>
            <th>Kode Pengeluaran</th>
            <th>Tanggal Pengeluaran</th>
            <th>Lead Time</th>
        </tr>";

        while ($arr = mysql_fetch_array($d_sql)) {
            $tgl_berakhir = date("j M Y", strtotime($arr['DOL_TglBerakhir']));
            $reldate=date("j M Y", strtotime($arr['THROOLD_ReleaseDate']));
            $retdate=date("j M Y", strtotime($arr['TDRTOOLD_ReturnTime']));
            if ($arr['TDROOLD_LeadTime']=="0000-00-00 00:00:00")
                $leaddate="-";
            else
                $leaddate=date("j M Y", strtotime($arr['TDROOLD_LeadTime']));
        echo"
        <tr>
            <td class='center'>$retdate</td>
            <td class='center'>$arr[TDRTOOLD_DocCode]</td>
            <td class='center'>$arr[kategori_dokumen]</td>
            <td class='center'>$arr[DOL_NamaDokumen]</td>
            <td class='center'>$arr[DOL_InstansiTerkait]</td>
            <td class='center'>$arr[DOL_NoDokumen]</td>
            <td class='center'>$tgl_berakhir</td>
            <td class='center'>$arr[THROOLD_ReleaseCode]</td>
            <td class='center'>$reldate</td>
            <td class='center'>$leaddate</td>
        </tr>";
        }
        echo"</table>";
        $jumdata++;
    }
}elseif ($_POST['optDocumentGroup']=='6'){
    $jumdata=0;
    while ($h_arr = mysql_fetch_array($sql)) {
        if ($jumdata==2) {
            $style="style='page-break-after:always'";
            $jumdata=0;
        }
        else
        {
            $style="";
        }

        $loandate=date("j M Y", strtotime($h_arr['THLOONLD_LoanDate']));

        $d_query="SELECT tdrtoonld.TDRTOONLD_ReturnTime, tdrtoonld.TDRTOONLD_DocCode,
                         donl.DONL_NamaDokumen, donl.DONL_TahunDokumen, donl.DONL_NoDokumen,
                         md.Department_Name nama_dept, thrloonld.THROONLD_ReleaseCode,
                         thrloonld.THROONLD_ReleaseDate, tdrloonld.TDROONLD_LeadTime
                  FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld
                  LEFT JOIN M_DocumentsOtherNonLegal donl
                    ON tdrtoonld.TDRTOONLD_DocCode=donl.DONL_DocCode
                  LEFT JOIN TD_ReleaseOfOtherNonLegalDocuments tdrloonld
                    ON tdrloonld.TDROONLD_ReturnCode= tdrtoonld.TDRTOONLD_ReturnCode
                  LEFT JOIN TH_ReleaseOfOtherNonLegalDocuments thrloonld
                    ON tdrloonld.TDROONLD_TDLOONLD_ID=thrloonld.THROONLD_ID
                  LEFT JOIN M_Department md
                    ON md.Department_ID=donl.DONL_Dept_Code
                  WHERE tdrtoonld.TDRTOONLD_ReturnCode='$h_arr[TDRTOONLD_ReturnCode]'
                  AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL";
        $d_sql=mysql_query($d_query);
        echo"
        <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
        <tr>
            <td width='19%'>Kode Permintaan</td>
            <td width='1%'>:</td>
            <td width='30%'>$h_arr[THLOONLD_LoanCode]</td>
            <td width='19%'>Tanggal Permintaan</td>
            <td width='1%'>:</td>
            <td width='30%'>$loandate</td>
        </tr>
        <tr>
            <td>Nama Peminta</td>
            <td>:</td>
            <td>$h_arr[User_FullName]</td>
            <td>Departemen</td>
            <td>:</td>
            <td>$h_arr[Department_Name]</td>
        </tr>
        <tr>
            <td>Perusahaan</td>
            <td>:</td>
            <td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
            <td>Grup Dokumen</td>
            <td>:</td>
            <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
        </tr>
        </table>

        <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
        <tr>
            <th>Tanggal Pengembalian</th>
            <th>Kode Dokumen</th>
            <th>No. Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Tahun Dokumen</th>
            <th>Departemen</th>
            <th>Kode Pengeluaran</th>
            <th>Tanggal Pengeluaran</th>
            <th>Lead Time</th>
        </tr>";

        while ($arr = mysql_fetch_array($d_sql)) {
            $reldate=date("j M Y", strtotime($arr['THROONLD_ReleaseDate']));
            $retdate=date("j M Y", strtotime($arr['TDRTOONLD_ReturnTime']));
            if ($arr['TDROONLD_LeadTime']=="0000-00-00 00:00:00")
                $leaddate="-";
            else
                $leaddate=date("j M Y", strtotime($arr['TDROONLD_LeadTime']));
        echo"
        <tr>
            <td class='center'>$retdate</td>
            <td class='center'>$arr[TDRTOONLD_DocCode]</td>
            <td class='center'>$arr[DONL_NoDokumen]</td>
            <td class='center'>$arr[DONL_NamaDokumen]</td>
            <td class='center'>$arr[DONL_TahunDokumen]</td>
            <td class='center'>$arr[nama_dept]</td>
            <td class='center'>$arr[THROONLD_ReleaseCode]</td>
            <td class='center'>$reldate</td>
            <td class='center'>$leaddate</td>
        </tr>";
        }
        echo"</table>";
        $jumdata++;
    }
}
?>

<table width='40%' border='1' cellpadding='0' cellspacing='0' align='right'>
	<tr>
    	<td width='10%' class='center'>
        	Dibuat
        </td>
    	<td width='10%' class='center'>
        	Diperiksa
        </td>
    	<td width='20%' class='center' colspan='2'>
        	Disetujui
        </td>
    </tr>
    <tr>
    	<td height='60px'>&nbsp;
</td>
        <td>&nbsp;
</td><td>&nbsp;
</td><td>&nbsp;
</td>
    </tr>
	<tr>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user1]"; ?>
        </td>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user2]"; ?>
        </td>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user3]"; ?>
        </td>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user4]"; ?>
        </td>
    </tr>
</table>
</div>
</body>
</html>
<?PHP } ?>
