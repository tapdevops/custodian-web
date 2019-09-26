<?php
// foreach ($_POST as $key => $value) {
//     echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
// }
// exit();
switch($_POST['optDocumentGroup']){
    case "1" : $GrupDokumen = "Legal"; break;
    case "2" : $GrupDokumen = "Lisensi"; break;
    case "3" : $GrupDokumen = "Pembebasan_Lahan"; break;
    case "4" : $GrupDokumen = "Kepemilikan_Aset"; break;
    case "5" : $GrupDokumen = "Lainnya_(Legal)"; break;
    case "6" : $GrupDokumen = "Lainnya_(Di_Luar_Legal)"; break;
}

// Fungsi header dengan mengirimkan raw data excel
header("Content-type: application/vnd-ms-excel");
// Mendefinisikan nama file ekspor
header("Content-Disposition: attachment; filename=Result_Laporan_Dokumen_Outstanding_-_Dokumen_$GrupDokumen.xls");

include ("./config/config_db.php");
error_reporting(E_ALL);
$PHP_SELF = "http://".$_SERVER['HTTP_HOST'];

$dataPerPage = 20;
if(isset($_POST['page']))
    $noPage = $_POST['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "";

if ($_POST['optDocumentGroup'] == '1' || $_POST['optDocumentGroup'] == '2'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND dl.DL_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qcategory=(!$_POST['optDocumentCategory'])?"":"AND dl.DL_CategoryDocID='$_POST[optDocumentCategory]'";

    $query = "SELECT DISTINCT dl.DL_DocCode, dt.DocumentType_Name, dl.DL_NoDoc, dl.DL_PubDate, thlold.THLOLD_LoanCode,
                     thlold.THLOLD_LoanDate, u.User_FullName, dp.Department_Name, thrlold.THROLD_ReleaseCode,
                     thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime, dl.DL_Instance,
                     datediff(sysdate(), tdrlold.TDROLD_LeadTime) AS keterlambatan,
                     dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
                     dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
              FROM M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt,
                   M_DocumentGroup dg, TH_ReleaseOfLegalDocument thrlold, TH_LoanOfLegalDocument thlold,
                   TD_ReleaseOfLegalDocument tdrlold, TD_LoanOfLegalDocument tdlold, M_User u, M_Department dp,
                   M_DivisionDepartmentPosition ddp
              WHERE dl.DL_GroupDocID='$_POST[optDocumentGroup]'
              $qcompany
              $qarea
              $qcategory
              AND dl.DL_GroupDocID=dg.DocumentGroup_ID
              AND dl.DL_CompanyID=c.Company_ID
              AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
              AND dl.DL_TypeDocID=dt.DocumentType_ID
              AND dl.DL_DocCode=tdlold.TDLOLD_DocCode
              AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
              AND thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
              AND thrlold.THROLD_ID=tdrlold.TDROLD_THROLD_ID
              AND thlold.THLOLD_UserID=u.User_ID
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND dl.DL_Status='4'
              AND tdrlold.TDROLD_ReturnCode NOT IN (SELECT TDRTOLD_ReturnCode
                                                    FROM TD_ReturnOfLegalDocument
                                                    WHERE TDRTOLD_Delete_Time IS NULL)
              AND tdrlold.TDROLD_LeadTime<>'0000-00-00 00:00:00'
              AND dl.DL_Delete_Time IS NULL
              ORDER BY dl.DL_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='3'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND dla.DLA_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";

    $query = "SELECT DISTINCT dla.DLA_Code, dla.DLA_Phase, dla.DLA_Period, dla.DLA_Village, dla.DLA_Block, dla.DLA_Owner,
                     thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, u.User_FullName, dp.Department_Name,
                     thrlolad.THRLOLAD_ReleaseCode,thrlolad.THRLOLAD_ReleaseDate,
                     tdrlolad.TDRLOLAD_LeadTime, datediff(sysdate(), tdrlolad.TDRLOLAD_LeadTime) AS keterlambatan,
                     c.Company_ID,c.Company_Name,dg.DocumentGroup_ID,dg.DocumentGroup_Name
              FROM M_DocumentLandAcquisition dla, M_Company c, M_DocumentGroup dg,
                   TH_ReleaseOfLandAcquisitionDocument thrlolad, TH_LoanOfLandAcquisitionDocument thlolad,
                   TD_ReleaseOfLandAcquisitionDocument tdrlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
                   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              $qcompany
              $qarea
              AND dla.DLA_CompanyID=c.Company_ID
              AND dla.DLA_Code=tdlolad.TDLOLAD_DocCode
              AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
              AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
              AND thrlolad.THRLOLAD_ID=tdrlolad.TDRLOLAD_THRLOLAD_ID
              AND thlolad.THLOLAD_UserID=u.User_ID
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND dla.DLA_Status='4'
              AND tdrlolad.TDRLOLAD_LeadTime<>'0000-00-00 00:00:00'
              AND tdrlolad.TDRLOLAD_ReturnCode NOT IN (SELECT TDRTOLAD_ReturnCode
                                                       FROM TD_ReturnOfLandAcquisitionDocument
                                                       WHERE TDRTOLAD_Delete_Time IS NULL)
              AND dla.DLA_Delete_Time IS NULL
              ORDER BY dla.DLA_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='4'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND dao.DAO_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";

    $query = "SELECT DISTINCT dao.DAO_DocCode,
                    m_mk.MK_Name merk_kendaraan,
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
                    END AS expired_stnk,
                     thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_LoanDate, u.User_FullName, dp.Department_Name,
                     thrloaod.THROAOD_ReleaseCode,thrloaod.THROAOD_ReleaseDate,
                     tdrloaod.TDROAOD_LeadTime, datediff(sysdate(), tdrloaod.TDROAOD_LeadTime) AS keterlambatan,
                     c.Company_ID,c.Company_Name,dg.DocumentGroup_ID,dg.DocumentGroup_Name
              FROM M_DocumentAssetOwnership dao, M_Company c, M_DocumentGroup dg,
                   TH_ReleaseOfAssetOwnershipDocument thrloaod, TH_LoanOfAssetOwnershipDocument thloaod,
                   TD_ReleaseOfAssetOwnershipDocument tdrloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
                   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp, db_master.M_MerkKendaraan m_mk
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              $qcompany
              $qarea
              AND dao.DAO_CompanyID=c.Company_ID
              AND dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
              AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
              AND thrloaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
              AND thrloaod.THROAOD_ID=tdrloaod.TDROAOD_THROAOD_ID
              AND thloaod.THLOAOD_UserID=u.User_ID
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND dao.DAO_Status='4'
              AND tdrloaod.TDROAOD_LeadTime<>'0000-00-00 00:00:00'
              AND tdrloaod.TDROAOD_ReturnCode NOT IN (SELECT TDRTOAOD_ReturnCode
                                                       FROM TD_ReturnOfAssetOwnershipDocument
                                                       WHERE TDRTOAOD_Delete_Time IS NULL)
              AND dao.DAO_Delete_Time IS NULL
              AND m_mk.MK_ID=dao.DAO_MK_ID
              ORDER BY dao.DAO_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='5'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND dol.DOL_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qcategory=(!$_POST['optDocumentCategory'])?"":"AND dol.DOL_CategoryDocID='$_POST[optDocumentCategory]'";

    $query = "SELECT DISTINCT dol.DOL_DocCode,
                    mdc.DocumentCategory_Name kategori_dokumen, dol.DOL_NamaDokumen,
                    dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
                    dol.DOL_TglTerbit, dol.DOL_TglBerakhir,
                     thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate, u.User_FullName, dp.Department_Name,
                     thrloold.THROOLD_ReleaseCode,thrloold.THROOLD_ReleaseDate,
                     tdrloold.TDROOLD_LeadTime, datediff(sysdate(), tdrloold.TDROOLD_LeadTime) AS keterlambatan,
                     c.Company_ID,c.Company_Name,dg.DocumentGroup_ID,dg.DocumentGroup_Name
              FROM M_DocumentsOtherLegal dol, M_Company c, M_DocumentGroup dg,
                   TH_ReleaseOfOtherLegalDocuments thrloold, TH_LoanOfOtherLegalDocuments thloold,
                   TD_ReleaseOfOtherLegalDocuments tdrloold, TD_LoanOfOtherLegalDocuments tdloold,
                   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp, db_master.M_DocumentCategory mdc
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              $qcompany
              $qarea
              $qcategory
              AND dol.DOL_CompanyID=c.Company_ID
              AND dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
              AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
              AND thrloold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
              AND thrloold.THROOLD_ID=tdrloold.TDROOLD_THROOLD_ID
              AND thloold.THLOOLD_UserID=u.User_ID
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND dol.DOL_Status='4'
              AND tdrloold.TDROOLD_LeadTime<>'0000-00-00 00:00:00'
              AND tdrloold.TDROOLD_ReturnCode NOT IN (SELECT TDRTOOLD_ReturnCode
                                                       FROM TD_ReturnOfOtherLegalDocuments
                                                       WHERE TDRTOOLD_Delete_Time IS NULL)
              AND dol.DOL_Delete_Time IS NULL
              AND mdc.DocumentCategory_ID=dol.DOL_CategoryDocID
              ORDER BY dol.DOL_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='6'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND donl.DONL_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";

    $query = "SELECT DISTINCT donl.DONL_DocCode,
                    donl.DONL_NamaDokumen, donl.DONL_TahunDokumen, donl.DONL_NoDokumen,
                    md.Department_Name nama_dept,
                     thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_LoanDate, u.User_FullName, dp.Department_Name,
                     thrloonld.THROONLD_ReleaseCode,thrloonld.THROONLD_ReleaseDate,
                     tdrloonld.TDROONLD_LeadTime, datediff(sysdate(), tdrloonld.TDROONLD_LeadTime) AS keterlambatan,
                     c.Company_ID,c.Company_Name,dg.DocumentGroup_ID,dg.DocumentGroup_Name
              FROM M_DocumentsOtherNonLegal donl, M_Company c, M_DocumentGroup dg,
                   TH_ReleaseOfOtherNonLegalDocuments thrloonld, TH_LoanOfOtherNonLegalDocuments thloonld,
                   TD_ReleaseOfOtherNonLegalDocuments tdrloonld, TD_LoanOfOtherNonLegalDocuments tdloonld,
                   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp, M_Department md
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              $qcompany
              $qarea
              AND donl.DONL_CompanyID=c.Company_ID
              AND donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
              AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
              AND thrloonld.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
              AND thrloonld.THROONLD_ID=tdrloonld.TDROONLD_THROONLD_ID
              AND thloonld.THLOONLD_UserID=u.User_ID
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND md.Department_ID=donl.DONL_Dept_Code
              AND donl.DONL_Status='4'
              AND tdrloonld.TDROONLD_LeadTime<>'0000-00-00 00:00:00'
              AND tdrloonld.TDROONLD_ReturnCode NOT IN (SELECT TDRTOONLD_ReturnCode
                                                       FROM TD_ReturnOfOtherNonLegalDocuments
                                                       WHERE TDRTOOnLD_Delete_Time IS NULL)
              AND donl.DONL_Delete_Time IS NULL
              ORDER BY donl.DONL_ID LIMIT $offset, $dataPerPage";
}

// echo $query;

$Output = "";
$regdate = "";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);
$no = 1;
$sqldg = mysql_query($query);
$arr = mysql_fetch_array($sqldg);
$h_sql= mysql_query($query);
$h_arr = mysql_fetch_array($h_sql);

// echo $num;

if($num == NULL || $num == 0){
    $Output = "<table>
        <tr>
            <td>Tidak ada data</td>
        </tr>
    </table>";
}else{
    if ($_POST['optDocumentGroup'] == '1' || $_POST['optDocumentGroup'] == '2'){
        $Output .="
			<form name='list' method='post' action='print-report-outstanding-document.php' target='_blank'>
			<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
			<input type='hidden' name='optArea' value='$_POST[optArea]'>
			<input type='hidden' name='optDocumentCategory' value='$_POST[optDocumentCategory]'>

			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td>Perusahaan</td>
				<td>:</td>
				<td>$h_arr[Company_Name]</td>
				<td></td><td></td>
			</tr>
			<tr>
				<td width='19%'>Grup Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>
					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
				</td>
				<td width='19%'>Kategori Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>$h_arr[DocumentCategory_Name]</td>
			</tr>
			</table>
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
			<tr>
				<th>Kode Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
				<th>Nomor Dokumen</th>
				<th>Berlaku</th>
				<th>Kode Permintaan</th>
				<th>Tanggal Permintaan</th>
				<th>Nama Peminta</th>
				<th>Departemen</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Tanggal Jatuh Tempo</th>
				<th>Lead Time (hari)</th>
			</tr>
		";

			while ($arr = mysql_fetch_array($sql)) {
			$berlaku=date("j M Y", strtotime($arr['DL_PubDate']));
			$reqdate=date("j M Y", strtotime($arr['THLOLD_LoanDate']));
			$reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
			$duedate=date("j M Y", strtotime($arr['TDROLD_LeadTime']));
		$Output .="
			<tr>
				<td class='center'>$arr[DL_DocCode]</td>
				<td class='center'>$arr[DocumentType_Name]</td>
				<td class='center'>$arr[DL_Instance]</td>
				<td class='center'>$arr[DL_NoDoc]</td>
				<td class='center'>$berlaku</td>
				<td class='center'>$arr[THLOLD_LoanCode]</td>
				<td class='center'>$reqdate</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$arr[THROLD_ReleaseCode]</td>
				<td class='center'>$reldate</td>
				<td class='center'>$duedate</td>
				<td class='center'>$arr[keterlambatan]</td>
			</tr>
		";
			}
		$Output .="
			</table>
		";
    }elseif($_POST['optDocumentGroup'] == '3'){
		$Output .="
			<form name='list' method='post' action='print-report-outstanding-document.php' target='_blank'>
			<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
			<input type='hidden' name='optArea' value='$_POST[optArea]'>

			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td width='19%'>Perusahaan</td>
				<td width='1%'>:</td>
				<td width='30%'>$h_arr[Company_Name]</td>
				<td width='19%'>Grup Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>
					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
				</td>
			</tr>
			</table>
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
			<tr>
				<th>Kode Dokumen</th>
				<th>Tahap GRL</th>
				<th>Periode GRL</th>
				<th>Desa</th>
				<th>Blok</th>
				<th>Pemilik</th>
				<th>Kode Permintaan</th>
				<th>Tanggal Permintaan</th>
				<th>Nama Peminta</th>
				<th>Departemen</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Tanggal Jatuh Tempo</th>
				<th>Lead Time (hari)</th>
			</tr>
		";

			while ($arr = mysql_fetch_array($sql)) {
			$periode=date("j M Y", strtotime($arr['DLA_Period']));
			$reqdate=date("j M Y", strtotime($arr['THLOLAD_LoanDate']));
			$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
			$duedate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
		$Output .="
			<tr>
				<td class='center'>$arr[DLA_Code]</td>
				<td class='center'>$arr[DLA_Phase]</td>
				<td class='center'>$periode</td>
				<td class='center'>$arr[DLA_Village]</td>
				<td class='center'>$arr[DLA_Block]</td>
				<td class='center'>$arr[DLA_Owner]</td>
				<td class='center'>$arr[THLOLAD_LoanCode]</td>
				<td class='center'>$reqdate</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
				<td class='center'>$reldate</td>
				<td class='center'>$duedate</td>
				<td class='center'>$arr[keterlambatan]</td>
			</tr>";
			}
		$Output .="
			</table>
		";
    }elseif($_POST['optDocumentGroup'] == '4'){
		$Output .="
			<form name='list' method='post' action='print-report-outstanding-document.php' target='_blank'>
			<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
			<input type='hidden' name='optArea' value='$_POST[optArea]'>

			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td width='19%'>Perusahaan</td>
				<td width='1%'>:</td>
				<td width='30%'>$h_arr[Company_Name]</td>
				<td width='19%'>Grup Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>
					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
				</td>
			</tr>
			</table>
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
			<tr>
				<th>Kode Dokumen</th>
				<th>Nama Pemilik</th>
				<th>Merk Kendaraan</th>
				<th>Nomor Polisi</th>
				<th>Masa STNK</th>
				<th>Kode Permintaan</th>
				<th>Tanggal Permintaan</th>
				<th>Nama Peminta</th>
				<th>Departemen</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Tanggal Jatuh Tempo</th>
				<th>Lead Time (hari)</th>
			</tr>
		";

			while ($arr = mysql_fetch_array($sql)) {
			$reqdate=date("j M Y", strtotime($arr['THLOAOD_LoanDate']));
			$reldate=date("j M Y", strtotime($arr['THROAOD_ReleaseDate']));
			$duedate=date("j M Y", strtotime($arr['TDROAOD_LeadTime']));
		$Output .="
			<tr>
				<td class='center'>$arr[DAO_DocCode]</td>
				<td class='center'>$arr[nama_pemilik]</td>
				<td class='center'>$arr[merk_kendaraan]</td>
				<td class='center'>$arr[DAO_NoPolisi]</td>
				<td class='center'>$arr[start_stnk] s/d $arr[expired_stnk]</td>
				<td class='center'>$arr[THLOAOD_LoanCode]</td>
				<td class='center'>$reqdate</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$arr[THROAOD_ReleaseCode]</td>
				<td class='center'>$reldate</td>
				<td class='center'>$duedate</td>
				<td class='center'>$arr[keterlambatan]</td>
			</tr>";
			}
		$Output .="
			</table>
		";
    }elseif($_POST['optDocumentGroup'] == '5'){
        $Output .="
			<form name='list' method='post' action='print-report-outstanding-document.php' target='_blank'>
			<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
			<input type='hidden' name='optArea' value='$_POST[optArea]'>

			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td width='19%'>Perusahaan</td>
				<td width='1%'>:</td>
				<td width='30%'>$h_arr[Company_Name]</td>
				<td width='19%'>Grup Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>
					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
				</td>
			</tr>
			</table>
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
			<tr>
				<th>Kode Dokumen</th>
				<th>Kategori Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
				<th>Nomor Dokumen</th>
				<th>Berlaku Sampai</th>
				<th>Kode Permintaan</th>
				<th>Tanggal Permintaan</th>
				<th>Nama Peminta</th>
				<th>Departemen</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Tanggal Jatuh Tempo</th>
				<th>Lead Time (hari)</th>
			</tr>
		";

			while ($arr = mysql_fetch_array($sql)) {
			$tgl_berakhir = date("j M Y", strtotime($arr['DOL_TglBerakhir']));
			$reqdate=date("j M Y", strtotime($arr['THLOOLD_LoanDate']));
			$reldate=date("j M Y", strtotime($arr['THROOLD_ReleaseDate']));
			$duedate=date("j M Y", strtotime($arr['TDROOLD_LeadTime']));
		$Output .="
			<tr>
				<td class='center'>$arr[DOL_DocCode]</td>
				<td class='center'>$arr[kategori_dokumen]</td>
				<td class='center'>$arr[DOL_NamaDokumen]</td>
				<td class='center'>$arr[DOL_InstansiTerkait]</td>
				<td class='center'>$arr[DOL_NoDokumen]</td>
				<td class='center'>$tgl_berakhir</td>
				<td class='center'>$arr[THLOOLD_LoanCode]</td>
				<td class='center'>$reqdate</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$arr[THROOLD_ReleaseCode]</td>
				<td class='center'>$reldate</td>
				<td class='center'>$duedate</td>
				<td class='center'>$arr[keterlambatan]</td>
			</tr>";
			}
		$Output .="
			</table>
		";
    }elseif($_POST['optDocumentGroup'] == '6'){
        $Output .="
  			<form name='list' method='post' action='print-report-outstanding-document.php' target='_blank'>
  			<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
  			<input type='hidden' name='optArea' value='$_POST[optArea]'>

  			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
  			<tr>
  				<td width='19%'>Perusahaan</td>
  				<td width='1%'>:</td>
  				<td width='30%'>$h_arr[Company_Name]</td>
  				<td width='19%'>Grup Dokumen</td>
  				<td width='1%'>:</td>
  				<td width='30%'>
  					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
  				</td>
  			</tr>
  			</table>
  			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
  			<tr>
  				<th>Kode Dokumen</th>
				<th>No. Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Tahun Dokumen</th>
				<th>Departemen pada Dokumen</th>
  				<th>Kode Permintaan</th>
  				<th>Tanggal Permintaan</th>
  				<th>Nama Peminta</th>
  				<th>Departemen</th>
  				<th>Kode Pengeluaran</th>
  				<th>Tanggal Pengeluaran</th>
  				<th>Tanggal Jatuh Tempo</th>
  				<th>Lead Time (hari)</th>
  			</tr>
  		";

  			while ($arr = mysql_fetch_array($sql)) {
  			$reqdate=date("j M Y", strtotime($arr['THLOONLD_LoanDate']));
  			$reldate=date("j M Y", strtotime($arr['THROONLD_ReleaseDate']));
  			$duedate=date("j M Y", strtotime($arr['TDROONLD_LeadTime']));
  		$Output .="
  			<tr>
  				<td class='center'>$arr[DONL_DocCode]</td>
				<td class='center'>$arr[DONL_NoDokumen]</td>
				<td class='center'>$arr[DONL_NamaDokumen]</td>
				<td class='center'>$arr[DONL_TahunDokumen]</td>
				<td class='center'>$arr[nama_dept]</td>
  				<td class='center'>$arr[THLOONLD_LoanCode]</td>
  				<td class='center'>$reqdate</td>
  				<td class='center'>$arr[User_FullName]</td>
  				<td class='center'>$arr[Department_Name]</td>
  				<td class='center'>$arr[THROONLD_ReleaseCode]</td>
  				<td class='center'>$reldate</td>
  				<td class='center'>$duedate</td>
  				<td class='center'>$arr[keterlambatan]</td>
  			</tr>";
  			}
  		$Output .="
  			</table>
  		";
    }
}

// Menampilkan Dokumen
echo $Output;

exit();
?>
