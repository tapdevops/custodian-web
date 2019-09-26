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
header("Content-Disposition: attachment; filename=Result_Laporan_Pengembalian_-_Dokumen_$GrupDokumen.xls");

include ("./config/config_db.php");
error_reporting(E_ALL);
$PHP_SELF = "http://".$_SERVER['HTTP_HOST'];

$dataPerPage = 20;
if(isset($_POST['page']))
    $noPage = $_POST['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$txtStart=date('Y-m-d', strtotime($_POST['txtStart']))." 00:00:00";
$txtEnd=date('Y-m-d', strtotime($_POST['txtEnd']))." 23:59:59";

$query = "";

if ($_POST['optDocumentGroup'] == '1' || $_POST['optDocumentGroup'] == '2'){
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
              AND ddp.DDP_DeptID=dp.Department_ID
              ORDER BY tdrtold.TDRTOLD_ReturnCode LIMIT $offset, $dataPerPage";
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
              AND ddp.DDP_DeptID=dp.Department_ID
              ORDER BY tdrtolad.TDRTOLAD_ReturnCode LIMIT $offset, $dataPerPage";
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
              AND ddp.DDP_DeptID=dp.Department_ID
              ORDER BY tdrtoaod.TDRTOAOD_ReturnCode LIMIT $offset, $dataPerPage";
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
              AND ddp.DDP_DeptID=dp.Department_ID
              ORDER BY tdrtoold.TDRTOOLD_ReturnCode LIMIT $offset, $dataPerPage";
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
              AND ddp.DDP_DeptID=dp.Department_ID
              ORDER BY tdrtoonld.TDRTOONLD_ReturnCode LIMIT $offset, $dataPerPage";
}

// echo $query;

$Output = "";
$regdate = "";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

// echo $num;

if($num == NULL || $num == 0){
    $Output = "<table>
        <tr>
            <td>Tidak ada data</td>
        </tr>
    </table>";
}else{
    if ($_POST['optDocumentGroup'] == '1' || $_POST['optDocumentGroup'] == '2'){
        while ($h_arr = mysql_fetch_array($sql)) {
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

            $Output .="
            <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
            <tr>
                <td>Kode Permintaan</td>
                <td>$h_arr[THLOLD_LoanCode]</td>
                <td>Tanggal Permintaan</td>
                <td>$loandate</td>
            </tr>
            <tr>
                <td>Nama Peminta</td>
                <td>$h_arr[User_FullName]</td>
                <td>Departemen</td>
                <td>$h_arr[Department_Name]</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td>$h_arr[Company_Name]</td>
                <td></td><td></td><td></td>
            </tr>
            <tr>
                <td>Grup Dokumen</td>
                <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                <td>Kategori Dokumen</td>
                <td>$h_arr[DocumentCategory_Name]</td>
            </tr>
            </table>

            <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
            <tr>
                <th>Tanggal Pengembalian</th>
                <th>Kode Dokumen</th>
                <th>Nama Dokumen</th>
                <th>Instansi Terkait</th>
                <th>Nomor Dokumen</th>
                <th>Berlaku Sampai</th>
                <th>Kode Pengeluaran</th>
                <th>Tanggal Pengeluaran</th>
                <th>Lead Time</th>
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
            $Output .="
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
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '3'){
        while ($h_arr = mysql_fetch_array($sql)) {
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
            $Output .="
            <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
            <tr>
                <td>Kode Permintaan</td>
                <td>$h_arr[THLOLAD_LoanCode]</td>
                <td>Tanggal Permintaan</td>
                <td>$loandate</td>
            </tr>
            <tr>
                <td>Nama Peminta</td>
                <td>$h_arr[User_FullName]</td>
                <td>Departemen</td>
                <td>$h_arr[Department_Name]</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
                <td>Grup Dokumen</td>
                <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
            </tr>
            </table>

            <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
            <tr>
                <th>Tanggal Pengembalian</th>
                <th>Kode Dokumen</th>
                <th>Tahap</th>
                <th>Periode</th>
                <th>Desa</th>
                <th>Blok</th>
                <th>Pemilik</th>
                <th>Kode Pengeluaran</th>
                <th>Tanggal Pengeluaran</th>
                <th>Lead Time</th>
            </tr>";

            while ($arr = mysql_fetch_array($d_sql)) {
                $reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
                $retdate=date("j M Y", strtotime($arr['TDRTOLAD_ReturnTime']));
                $period=date("j M Y", strtotime($arr['DLA_Period']));
                if ($arr['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")
                    $leaddate="-";
                else
                    $leaddate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
            $Output .="
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
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '4'){
        while ($h_arr = mysql_fetch_array($sql)) {
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
            $Output .="
            <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
            <tr>
                <td>Kode Permintaan</td>
                <td>$h_arr[THLOAOD_LoanCode]</td>
                <td>Tanggal Permintaan</td>
                <td>$loandate</td>
            </tr>
            <tr>
                <td>Nama Peminta</td>
                <td>$h_arr[User_FullName]</td>
                <td>Departemen</td>
                <td>$h_arr[Department_Name]</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
                <td>Grup Dokumen</td>
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
            $Output .="
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
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '5'){
        while ($h_arr = mysql_fetch_array($sql)) {
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
            $Output .="
            <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
            <tr>
                <td>Kode Permintaan</td>
                <td>$h_arr[THLOOLD_LoanCode]</td>
                <td>Tanggal Permintaan</td>
                <td>$loandate</td>
            </tr>
            <tr>
                <td>Nama Peminta</td>
                <td>$h_arr[User_FullName]</td>
                <td>Departemen</td>
                <td>$h_arr[Department_Name]</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
                <td>Grup Dokumen</td>
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
            $Output .="
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
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '6'){
        while ($h_arr = mysql_fetch_array($sql)) {
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
            $Output .="
            <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
            <tr>
                <td>Kode Permintaan</td>
                <td>$h_arr[THLOONLD_LoanCode]</td>
                <td>Tanggal Permintaan</td>
                <td>$loandate</td>
            </tr>
            <tr>
                <td>Nama Peminta</td>
                <td>$h_arr[User_FullName]</td>
                <td>Departemen</td>
                <td>$h_arr[Department_Name]</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
                <td>Grup Dokumen</td>
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
            $Output .="
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
            $Output .="
                </table>
            ";
        }
    }
}

// Menampilkan Dokumen
echo $Output;

exit();
?>
