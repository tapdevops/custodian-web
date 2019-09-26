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
header("Content-Disposition: attachment; filename=Result_Laporan_Pengeluaran_-_Dokumen_$GrupDokumen.xls");

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
$qcategory = "";
$qarea = "";
$qcompany = "";
$qperiod = "";

$txtStart=date('Y-m-d', strtotime($_POST['txtStart']))." 00:00:00";
$txtEnd=date('Y-m-d', strtotime($_POST['txtEnd']))." 23:59:59";

// echo $_POST['optDocumentGroup'];
if ($_POST['optDocumentGroup'] == '1' || $_POST['optDocumentGroup'] == '2'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thlold.THLOLD_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdlold.TDLOLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
    $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrlold.THROLD_ReleaseDate BETWEEN '$txtStart' AND '$txtEnd'";

    $query = "SELECT DISTINCT thrlold.THROLD_ID, thrlold.THROLD_ReleaseCode, thrlold.THROLD_ReleaseDate,
                              thlold.THLOLD_LoanCategoryID, thlold.THLOLD_LoanDate, drs.DRS_Description,
                              dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
                              dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
              FROM M_DocumentGroup dg, M_Company c, M_DocumentRegistrationStatus drs,
                   TH_ReleaseOfLegalDocument thrlold,
                   TH_LoanOfLegalDocument thlold,
                    M_DocumentCategory dc,
                    TD_LoanOfLegalDocument tdlold
              WHERE thlold.THLOLD_DocumentGroupID='$_POST[optDocumentGroup]'
              $qcompany
              $qarea
              $qcategory
              $qperiod
              AND thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
              AND thlold.THLOLD_ID=tdlold.TDLOLD_THLOLD_ID
              AND thrlold.THROLD_Status=drs.DRS_Name
              AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
              AND thlold.THLOLD_CompanyID=c.Company_ID
              AND tdlold.TDLOLD_DocumentCategoryID=dc.DocumentCategory_ID
              AND thrlold.THROLD_Delete_Time IS NULL
              ORDER BY thrlold.THROLD_ID LIMIT $offset, $dataPerPage";
  }elseif ($_POST['optDocumentGroup']=='3'){
      $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thlolad.THLOLAD_CompanyID='$_POST[optCompany]'";
      $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
      $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrlolad.THRLOLAD_ReleaseDate BETWEEN '$txtStart' AND '$txtEnd'";

      $query = "SELECT DISTINCT thrlolad.THRLOLAD_ID, thrlolad.THRLOLAD_ReleaseCode, thrlolad.THRLOLAD_ReleaseDate,
                                thlolad.THLOLAD_LoanCategoryID, thlolad.THLOLAD_LoanDate, drs.DRS_Description,
                                dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
                FROM M_DocumentGroup dg, M_Company c, M_DocumentRegistrationStatus drs,
                     TH_ReleaseOfLandAcquisitionDocument thrlolad,
                     TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad
                WHERE thrlolad.THRLOLAD_Status=drs.DRS_Name
                $qcompany
                $qarea
                $qperiod
                AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
                AND thlolad.THLOLAD_ID=tdlolad.TDLOLAD_THLOLAD_ID
                AND dg.DocumentGroup_ID='3'
                AND thrlolad.THRLOLAD_Status=drs.DRS_Name
                AND thlolad.THLOLAD_CompanyID=c.Company_ID
                AND thrlolad.THRLOLAD_Delete_Time IS NULL
                ORDER BY thrlolad.THRLOLAD_ID LIMIT $offset, $dataPerPage";
  }elseif ($_POST['optDocumentGroup']=='4'){
      $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thloaod.THLOAOD_CompanyID='$_POST[optCompany]'";
      $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
      $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND throaod.THROAOD_ReleaseDate BETWEEN '$txtStart' AND '$txtEnd'";

      $query = "SELECT DISTINCT throaod.THROAOD_ID, throaod.THROAOD_ReleaseCode, throaod.THROAOD_ReleaseDate,
                                thloaod.THLOAOD_LoanCategoryID, thloaod.THLOAOD_LoanDate, drs.DRS_Description,
                                dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
                FROM M_DocumentGroup dg, M_Company c, M_DocumentRegistrationStatus drs,
                     TH_ReleaseOfAssetOwnershipDocument throaod,
                     TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod
                WHERE throaod.THROAOD_Status=drs.DRS_Name
                $qcompany
                $qarea
                $qperiod
                AND throaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
                AND thloaod.THLOAOD_ID=tdloaod.TDLOAOD_THLOAOD_ID
                AND dg.DocumentGroup_ID='4'
                AND throaod.THROAOD_Status=drs.DRS_Name
                AND thloaod.THLOAOD_CompanyID=c.Company_ID
                AND throaod.THROAOD_Delete_Time IS NULL
                ORDER BY throaod.THROAOD_ID LIMIT $offset, $dataPerPage";
  }elseif ($_POST['optDocumentGroup']=='5'){
      $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thloold.THLOOLD_CompanyID='$_POST[optCompany]'";
      $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
      $qcategory=(!$_POST['optDocumentCategory'])?"":"AND dol.DOL_CategoryDocID='$_POST[optDocumentCategory]'";
      $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrloold.THROOLD_ReleaseDate BETWEEN '$txtStart' AND '$txtEnd'";

      $query = "SELECT DISTINCT thrloold.THROOLD_ID, thrloold.THROOLD_ReleaseCode, thrloold.THROOLD_ReleaseDate,
                                thloold.THLOOLD_LoanCategoryID, thloold.THLOOLD_LoanDate, drs.DRS_Description,
                                dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
                FROM M_DocumentGroup dg, M_Company c, M_DocumentRegistrationStatus drs,
                     TH_ReleaseOfOtherLegalDocuments thrloold,
                     TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold,
                     M_DocumentsOtherLegal dol
                WHERE thrloold.THROOLD_Status=drs.DRS_Name
                $qcompany
                $qarea
                $qperiod
                AND thrloold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
                AND thloold.THLOOLD_ID=tdloold.TDLOOLD_THLOOLD_ID
                AND dg.DocumentGroup_ID='5'
                AND thrloold.THROOLD_Status=drs.DRS_Name
                AND thloold.THLOOLD_CompanyID=c.Company_ID
                AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
                $qcategory
                AND thrloold.THROOLD_Delete_Time IS NULL
                ORDER BY thrloold.THROOLD_ID LIMIT $offset, $dataPerPage";
  }elseif ($_POST['optDocumentGroup']=='6'){
      $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thloonld.THLOONLD_CompanyID='$_POST[optCompany]'";
      $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
      $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrloonld.THROONLD_ReleaseDate BETWEEN '$txtStart' AND '$txtEnd'";

      $query = "SELECT DISTINCT thrloonld.THROONLD_ID, thrloonld.THROONLD_ReleaseCode, thrloonld.THROONLD_ReleaseDate,
                                thloonld.THLOONLD_LoanCategoryID, thloonld.THLOONLD_LoanDate, drs.DRS_Description,
                                dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
                FROM M_DocumentGroup dg, M_Company c, M_DocumentRegistrationStatus drs,
                     TH_ReleaseOfOtherNonLegalDocuments thrloonld, TD_ReleaseOfOtherNonLegalDocuments tdrloonld,
                     TH_LoanOfOtherNonLegalDocuments thloonld, TD_LoanOfOtherNonLegalDocuments tdloonld
                WHERE thrloonld.THROONLD_Status=drs.DRS_Name
                $qcompany
                $qarea
                $qperiod
                AND thrloonld.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
                AND thloonld.THLOONLD_ID=tdloonld.TDLOONLD_THLOONLD_ID
                AND dg.DocumentGroup_ID='6'
                AND thrloonld.THROONLD_Status=drs.DRS_Name
                AND thloonld.THLOONLD_CompanyID=c.Company_ID
                AND thrloonld.THROONLD_Delete_Time IS NULL
                ORDER BY thrloonld.THROONLD_ID LIMIT $offset, $dataPerPage";
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
            $d_query="SELECT dl.DL_DocCode, dt.DocumentType_Name, dl.DL_Instance, dl.DL_NoDoc, dl.DL_ExpDate,
                             lc.LoanCategory_Name, thlold.THLOLD_LoanCode, u.User_FullName,dp.Department_Name,
                             thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime, thrlold.THROLD_ReleaseCode,
                             thlold.THLOLD_LoanDate, a.A_ApprovalDate
                      FROM M_DocumentLegal dl, M_DocumentType dt, TH_ReleaseOfLegalDocument thrlold,
                           TD_ReleaseOfLegalDocument tdrlold, TH_LoanOfLegalDocument thlold,
                           TD_LoanOfLegalDocument tdlold, M_LoanCategory lc, M_Approval a,
                           M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
                      WHERE thrlold.THROLD_ID='$h_arr[THROLD_ID]'
                      AND tdrlold.TDROLD_THROLD_ID=thrlold.THROLD_ID
                      AND tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
                      AND thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
                      AND thlold.THLOLD_ID=tdlold.TDLOLD_THLOLD_ID
                      AND tdlold.TDLOLD_DocCode=dl.DL_DocCode
                      AND thlold.THLOLD_UserID=u.User_ID
                      AND thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID
                      AND ddp.DDP_UserID=u.User_ID
                      AND ddp.DDP_DeptID=dp.Department_ID
                      AND dl.DL_TypeDocID=dt.DocumentType_ID
                      AND a.A_TransactionCode='$h_arr[THROLD_ReleaseCode]'
                      AND a.A_Step=(SELECT MAX(A_Step)
                                    FROM M_Approval
                                    WHERE A_TransactionCode='$h_arr[THROLD_ReleaseCode]')
                      AND thrlold.THROLD_Delete_Time IS NULL ";
            $d_sql=mysql_query($d_query);

            if ($h_arr['THLOLD_LoanCategoryID']=="1"){
                $loandate=date("j M Y", strtotime($h_arr['THLOLD_LoanDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Permintaan</td>
                    <td>$loandate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td>Kategori Dokumen</td>
                    <td>$h_arr[DocumentCategory_Name]</td>
                </tr>
                </table>

                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Instansi Terkait</th>
                    <th>Nomor Dokumen</th>
                    <th>Berlaku Sampai</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Pengeluaran</th>
                    <th>Lead Time</th>
                </tr>";
            }
            else {
                $reldate=date("j M Y", strtotime($h_arr['THROLD_ReleaseDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Pengeluaran</td>
                    <td>$reldate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td>Kategori Dokumen</td>
                    <td>$h_arr[DocumentCategory_Name]</td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Instansi Terkait</th>
                    <th>Nomor Dokumen</th>
                    <th>Berlaku Sampai</th>
                    <th>Kode Pengeluaran</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Persetujuan</th>
                </tr>";
            }

            while ($arr = mysql_fetch_array($d_sql)) {
            if ($h_arr['THLOLD_LoanCategoryID']=="1"){
                    $reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
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
                    <td class='center'>$arr[DL_DocCode]</td>
                    <td class='center'>$arr[DocumentType_Name]</td>
                    <td class='center'>$arr[DL_Instance]</td>
                    <td class='center'>$arr[DL_NoDoc]</td>
                    <td class='center'>$expdate</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOLD_LoanCode]</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$reldate</td>
                    <td class='center'>$leaddate</td>
                </tr>";
            }
            else {
                $loandate=date("j M Y", strtotime($arr['THLOLD_LoanDate']));
                $appdate=date("j M Y", strtotime($arr['A_ApprovalDate']));
                if ($arr['DL_ExpDate']=="0000-00-00 00:00:00")
                    $expdate="-";
                else
                    $expdate=date("j M Y", strtotime($arr['DL_ExpDate']));

                $Output .="
                <tr>
                    <td class='center'>$arr[DL_DocCode]</td>
                    <td class='center'>$arr[DocumentType_Name]</td>
                    <td class='center'>$arr[DL_Instance]</td>
                    <td class='center'>$arr[DL_NoDoc]</td>
                    <td class='center'>$expdate</td>
                    <td class='center'>$arr[THROLD_ReleaseCode]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOLD_LoanCode]</td>
                    <td class='center'>$loandate</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$appdate</td>
                </tr>";
            }
            }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '3'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $d_query="SELECT dla.DLA_Code, dla.DLA_Phase, dla.DLA_Period, dla.DLA_Village, dla.DLA_Block,
                             dla.DLA_Owner, dla.DLA_DocDate, lc.LoanCategory_Name, thlolad.THLOLAD_LoanCode,
                             u.User_FullName,dp.Department_Name,thrlolad.THRLOLAD_ReleaseDate, a.A_ApprovalDate,
                             tdrlolad.TDRLOLAD_LeadTime, thrlolad.THRLOLAD_ReleaseCode, thlolad.THLOLAD_LoanDate
                      FROM M_DocumentLandAcquisition dla,TH_ReleaseOfLandAcquisitionDocument thrlolad,
                           TD_ReleaseOfLandAcquisitionDocument tdrlolad, TH_LoanOfLandAcquisitionDocument thlolad,
                           TD_LoanOfLandAcquisitionDocument tdlolad, M_LoanCategory lc, M_Approval a,
                           M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
                      WHERE thrlolad.THRLOLAD_ID='$h_arr[THRLOLAD_ID]'
                      AND tdrlolad.TDRLOLAD_THRLOLAD_ID=thrlolad.THRLOLAD_ID
                      AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
                      AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
                      AND thlolad.THLOLAD_ID=tdlolad.TDLOLAD_THLOLAD_ID
                      AND tdlolad.TDLOLAD_DocCode=dla.DLA_Code
                      AND thlolad.THLOLAD_UserID=u.User_ID
                      AND thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
                      AND ddp.DDP_UserID=u.User_ID
                      AND ddp.DDP_DeptID=dp.Department_ID
                      AND a.A_TransactionCode='$h_arr[THRLOLAD_ReleaseCode]'
                      AND a.A_Step=(SELECT MAX(A_Step)
                                    FROM M_Approval
                                    WHERE A_TransactionCode='$h_arr[THRLOLAD_ReleaseCode]')
                      AND thrlolad.THRLOLAD_Delete_Time IS NULL ";
            $d_sql=mysql_query($d_query);

            if ($h_arr['THLOLAD_LoanCategoryID']=="1"){
                $loandate=date("j M Y", strtotime($h_arr['THLOLAD_LoanDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Permintaan</td>
                    <td>$loandate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                </table>

                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>Tahap</th>
                    <th>Periode</th>
                    <th>Desa</th>
                    <th>Blok</th>
                    <th>Pemilik</th>
                    <th>Tanggal Dokumen</th>
                    <th>Kode Pengeluaran</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Pengeluaran</th>
                    <th>Lead Time</th>
                </tr>";
            }
            else {
                $reldate=date("j M Y", strtotime($h_arr['THRLOLAD_ReleaseDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Pengeluaran</td>
                    <td>$reldate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>Tahap</th>
                    <th>Periode</th>
                    <th>Desa</th>
                    <th>Blok</th>
                    <th>Pemilik</th>
                    <th>Tanggal Dokumen</th>
                    <th>Kode Pengeluaran</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Persetujuan</th>
                </tr>";
            }

            while ($arr = mysql_fetch_array($d_sql)) {
            if ($h_arr['THLOLAD_LoanCategoryID']=="1"){
                $reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
                $period=date("j M Y", strtotime($arr['DLA_Period']));
                $docdate=date("j M Y", strtotime($arr['DLA_DocDate']));
                if ($arr['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")
                    $leaddate="-";
                else
                    $leaddate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));

                $Output .="
                <tr>
                    <td class='center'>$arr[DLA_Code]</td>
                    <td class='center'>$arr[DLA_Phase]</td>
                    <td class='center'>$period</td>
                    <td class='center'>$arr[DLA_Village]</td>
                    <td class='center'>$arr[DLA_Block]</td>
                    <td class='center'>$arr[DLA_Owner]</td>
                    <td class='center'>$docdate</td>
                    <td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOLAD_LoanCode]</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$reldate</td>
                    <td class='center'>$leaddate</td>
                </tr>";
            }
            else {
                $loandate=date("j M Y", strtotime($arr['THLOLAD_LoanDate']));
                $appdate=date("j M Y", strtotime($arr['A_ApprovalDate']));
                $period=date("j M Y", strtotime($arr['DLA_Period']));
                $docdate=date("j M Y", strtotime($arr['DLA_DocDate']));

                $Output .="
                <tr>
                    <td class='center'>$arr[DLA_Code]</td>
                    <td class='center'>$arr[DLA_Phase]</td>
                    <td class='center'>$period</td>
                    <td class='center'>$arr[DLA_Village]</td>
                    <td class='center'>$arr[DLA_Block]</td>
                    <td class='center'>$arr[DLA_Owner]</td>
                    <td class='center'>$docdate</td>
                    <td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOLAD_LoanCode]</td>
                    <td class='center'>$loandate</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$appdate</td>
                </tr>";
            }
            }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '4'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $d_query="SELECT dao.DAO_DocCode, m_mk.MK_Name merk_kendaraan,
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
                             lc.LoanCategory_Name, thloaod.THLOAOD_LoanCode, u.User_FullName,dp.Department_Name,
                             thrloaod.THROAOD_ReleaseDate, thrloaod.THROAOD_ReleaseCode,
                             thloaod.THLOAOD_LoanDate, a.A_ApprovalDate
                      FROM M_DocumentAssetOwnership dao, TH_ReleaseOfAssetOwnershipDocument thrloaod,
                           TD_ReleaseOfAssetOwnershipDocument tdrloaod, TH_LoanOfAssetOwnershipDocument thloaod,
                           TD_LoanOfAssetOwnershipDocument tdloaod, M_LoanCategory lc, M_Approval a,
                           M_User u, M_Department dp, M_DivisionDepartmentPosition ddp, db_master.M_MerkKendaraan m_mk
                      WHERE thrloaod.THROAOD_ID='$h_arr[THROAOD_ID]'
                      AND tdrloaod.TDROAOD_THROAOD_ID=thrloaod.THROAOD_ID
                      AND tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
                      AND thrloaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
                      AND thloaod.THLOAOD_ID=tdloaod.TDLOAOD_THLOAOD_ID
                      AND tdloaod.TDLOAOD_DocCode=dao.DAO_DocCode
                      AND thloaod.THLOAOD_UserID=u.User_ID
                      AND thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
                      AND ddp.DDP_UserID=u.User_ID
                      AND ddp.DDP_DeptID=dp.Department_ID
                      AND a.A_TransactionCode='$h_arr[THROAOD_ReleaseCode]'
                      AND m_mk.MK_ID=dao.DAO_MK_ID
                      AND a.A_Step=(SELECT MAX(A_Step)
                                    FROM M_Approval
                                    WHERE A_TransactionCode='$h_arr[THROAOD_ReleaseCode]')
                      AND thrloaod.THROAOD_Delete_Time IS NULL ";
            $d_sql=mysql_query($d_query);

            if ($h_arr['THLOAOD_LoanCategoryID']=="1"){
                $loandate=date("j M Y", strtotime($h_arr['THLOAOD_LoanDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Permintaan</td>
                    <td>$loandate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                </tr>
                </table>

                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>Nama Pemilik</th>
                    <th>Merk Kendaraan</th>
                    <th>Nomor Polisi</th>
                    <th>Masa STNK</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Pengeluaran</th>
                </tr>";
            }
            else {
                $reldate=date("j M Y", strtotime($h_arr['THROAOD_ReleaseDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Pengeluaran</td>
                    <td>$reldate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>Nama Pemilik</th>
                    <th>Merk Kendaraan</th>
                    <th>Nomor Polisi</th>
                    <th>Masa STNK</th>
                    <th>Kode Pengeluaran</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Persetujuan</th>
                </tr>";
            }

            while ($arr = mysql_fetch_array($d_sql)) {
            if ($h_arr['THLOAOD_LoanCategoryID']=="1"){
                    $reldate=date("j M Y", strtotime($arr['THROAOD_ReleaseDate']));
                $Output .="
                <tr>
                    <td class='center'>$arr[DAO_DocCode]</td>
                    <td class='center'>$arr[nama_pemilik]</td>
                    <td class='center'>$arr[merk_kendaraan]</td>
                    <td class='center'>$arr[DAO_NoPolisi]</td>
                    <td class='center'>$arr[start_stnk] s/d $arr[expired_stnk]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOAOD_LoanCode]</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$reldate</td>
                </tr>";
            }
            else {
                $loandate=date("j M Y", strtotime($arr['THLOAOD_LoanDate']));
                $appdate=date("j M Y", strtotime($arr['A_ApprovalDate']));

                $Output .="
                <tr>
                    <td class='center'>$arr[DAO_DocCode]</td>
                    <td class='center'>$arr[nama_pemilik]</td>
                    <td class='center'>$arr[merk_kendaraan]</td>
                    <td class='center'>$arr[DAO_NoPolisi]</td>
                    <td class='center'>$arr[start_stnk] s/d $arr[expired_stnk]</td>
                    <td class='center'>$arr[THROAOD_ReleaseCode]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOAOD_LoanCode]</td>
                    <td class='center'>$loandate</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$appdate</td>
                </tr>";
            }
            }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '5'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $d_query="SELECT dol.DOL_DocCode, mdc.DocumentCategory_Name kategori_dokumen,
                             dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
                             dol.DOL_TglTerbit, dol.DOL_TglBerakhir,
                             lc.LoanCategory_Name, thloold.THLOOLD_LoanCode, u.User_FullName,dp.Department_Name,
                             thrloold.THROOLD_ReleaseDate, thrloold.THROOLD_ReleaseCode,
                             thloold.THLOOLD_LoanDate, a.A_ApprovalDate
                      FROM M_DocumentsOtherLegal dol, TH_ReleaseOfOtherLegalDocuments thrloold,
                           TD_ReleaseOfOtherLegalDocuments tdrloold, TH_LoanOfOtherLegalDocuments thloold,
                           TD_LoanOfOtherLegalDocuments tdloold, M_LoanCategory lc, M_Approval a,
                           M_User u, M_Department dp, M_DivisionDepartmentPosition ddp,
                           db_master.M_DocumentCategory mdc
                      WHERE thrloold.THROOLD_ID='$h_arr[THROOLD_ID]'
                      AND tdrloold.TDROOLD_THROOLD_ID=thrloold.THROOLD_ID
                      AND tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
                      AND thrloold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
                      AND thloold.THLOOLD_ID=tdloold.TDLOOLD_THLOOLD_ID
                      AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
                      AND thloold.THLOOLD_UserID=u.User_ID
                      AND thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
                      AND ddp.DDP_UserID=u.User_ID
                      AND ddp.DDP_DeptID=dp.Department_ID
                      AND a.A_TransactionCode='$h_arr[THROOLD_ReleaseCode]'
                      AND mdc.DocumentCategory_ID=dol.DOL_CategoryDocID
                      $qcategory
                      AND a.A_Step=(SELECT MAX(A_Step)
                                    FROM M_Approval
                                    WHERE A_TransactionCode='$h_arr[THROOLD_ReleaseCode]')
                      AND thrloold.THROOLD_Delete_Time IS NULL ";
            $d_sql=mysql_query($d_query);

            if ($h_arr['THLOOLD_LoanCategoryID']=="1"){
                $loandate=date("j M Y", strtotime($h_arr['THLOOLD_LoanDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Permintaan</td>
                    <td>$loandate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
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
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Pengeluaran</th>
                </tr>";
            }
            else {
                $reldate=date("j M Y", strtotime($h_arr['THROOLD_ReleaseDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Pengeluaran</td>
                    <td>$reldate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
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
                    <th>Kode Pengeluaran</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Persetujuan</th>
                </tr>";
            }

            while ($arr = mysql_fetch_array($d_sql)) {
                $tgl_berakhir = date("j M Y", strtotime($arr['DOL_TglBerakhir']));
            if ($h_arr['THLOOLD_LoanCategoryID']=="1"){
                    $reldate=date("j M Y", strtotime($arr['THROOLD_ReleaseDate']));
                $Output .="
                <tr>
                    <td class='center'>$arr[DOL_DocCode]</td>
                    <td class='center'>$arr[kategori_dokumen]</td>
                    <td class='center'>$arr[DOL_NamaDokumen]</td>
                    <td class='center'>$arr[DOL_InstansiTerkait]</td>
                    <td class='center'>$arr[DOL_NoDokumen]</td>
                    <td class='center'>$tgl_berakhir</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOOLD_LoanCode]</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$reldate</td>
                </tr>";
            }
            else {
                $loandate=date("j M Y", strtotime($arr['THLOOLD_LoanDate']));
                $appdate=date("j M Y", strtotime($arr['A_ApprovalDate']));

                $Output .="
                <tr>
                    <td class='center'>$arr[DOL_DocCode]</td>
                    <td class='center'>$arr[kategori_dokumen]</td>
                    <td class='center'>$arr[DOL_NamaDokumen]</td>
                    <td class='center'>$arr[DOL_InstansiTerkait]</td>
                    <td class='center'>$arr[DOL_NoDokumen]</td>
                    <td class='center'>$tgl_berakhir</td>
                    <td class='center'>$arr[THROOLD_ReleaseCode]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOOLD_LoanCode]</td>
                    <td class='center'>$loandate</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$appdate</td>
                </tr>";
            }
            }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '6'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $d_query="SELECT donl.DONL_DocCode,
                             donl.DONL_NamaDokumen, donl.DONL_TahunDokumen, donl.DONL_NoDokumen,
                             md.Department_Name nama_dept,
                             lc.LoanCategory_Name, thloold.THLOONLD_LoanCode, u.User_FullName,dp.Department_Name,
                             thrloold.THROONLD_ReleaseDate, thrloold.THROONLD_ReleaseCode,
                             thloold.THLOONLD_LoanDate, a.A_ApprovalDate
                      FROM M_DocumentsOtherNonLegal donl, TH_ReleaseOfOtherNonLegalDocuments thrloold,
                           TD_ReleaseOfOtherNonLegalDocuments tdrloold, TH_LoanOfOtherNonLegalDocuments thloold,
                           TD_LoanOfOtherNonLegalDocuments tdloold, M_LoanCategory lc, M_Approval a,
                           M_User u, M_Department dp, M_DivisionDepartmentPosition ddp, M_Department md
                      WHERE thrloold.THROONLD_ID='$h_arr[THROONLD_ID]'
                      AND tdrloold.TDROONLD_THROONLD_ID=thrloold.THROONLD_ID
                      AND tdrloold.TDROONLD_TDLOONLD_ID=tdloold.TDLOONLD_ID
                      AND thrloold.THROONLD_THLOONLD_Code=thloold.THLOONLD_LoanCode
                      AND thloold.THLOONLD_ID=tdloold.TDLOONLD_THLOONLD_ID
                      AND tdloold.TDLOONLD_DocCode=donl.DONL_DocCode
                      AND thloold.THLOONLD_UserID=u.User_ID
                      AND thloold.THLOONLD_LoanCategoryID=lc.LoanCategory_ID
                      AND ddp.DDP_UserID=u.User_ID
                      AND ddp.DDP_DeptID=dp.Department_ID
                      AND md.Department_ID=donl.DONL_Dept_Code
                      AND a.A_TransactionCode='$h_arr[THROONLD_ReleaseCode]'
                      $qcategory
                      AND a.A_Step=(SELECT MAX(A_Step)
                                    FROM M_Approval
                                    WHERE A_TransactionCode='$h_arr[THROONLD_ReleaseCode]')
                      AND thrloold.THROONLD_Delete_Time IS NULL ";
            $d_sql=mysql_query($d_query);

            if ($h_arr['THLOONLD_LoanCategoryID']=="1"){
                $loandate=date("j M Y", strtotime($h_arr['THLOONLD_LoanDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Permintaan</td>
                    <td>$loandate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                </tr>
                </table>

                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>No. Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Tahun Dokumen</th>
                    <th>Departemen</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Pengeluaran</th>
                </tr>";
            }
            else {
                $reldate=date("j M Y", strtotime($h_arr['THROONLD_ReleaseDate']));

                $Output .="
                <table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
                <tr>
                    <td>Perusahaan</td>
                    <td>$h_arr[Company_Name]</td>
                    <td>Tanggal Pengeluaran</td>
                    <td>$reldate</td>
                </tr>
                <tr>
                    <td>Grup Dokumen</td>
                    <td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
                    <td>Status Pengeluaran</td>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Kode Dokumen</th>
                    <th>No. Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Tahun Dokumen</th>
                    <th>Departemen</th>
                    <th>Kode Pengeluaran</th>
                    <th>Jenis Permintaan</th>
                    <th>Kode Permintaan</th>
                    <th>Tanggal Permintaan</th>
                    <th>Nama Peminta</th>
                    <th>Departemen</th>
                    <th>Tanggal Persetujuan</th>
                </tr>";
            }

            while ($arr = mysql_fetch_array($d_sql)) {
            if ($h_arr['THLOONLD_LoanCategoryID']=="1"){
                    $reldate=date("j M Y", strtotime($arr['THROONLD_ReleaseDate']));
                $Output .="
                <tr>
                    <td class='center'>$arr[DONL_DocCode]</td>
                    <td class='center'>$arr[DONL_NoDokumen]</td>
                    <td class='center'>$arr[DONL_NamaDokumen]</td>
                    <td class='center'>$arr[DONL_TahunDokumen]</td>
                    <td class='center'>$arr[nama_dept]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOONLD_LoanCode]</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$reldate</td>
                </tr>";
            }
            else {
                $loandate=date("j M Y", strtotime($arr['THLOONLD_LoanDate']));
                $appdate=date("j M Y", strtotime($arr['A_ApprovalDate']));

                $Output .="
                <tr>
                    <td class='center'>$arr[DONL_DocCode]</td>
                    <td class='center'>$arr[DONL_NoDokumen]</td>
                    <td class='center'>$arr[DONL_NamaDokumen]</td>
                    <td class='center'>$arr[DONL_TahunDokumen]</td>
                    <td class='center'>$arr[nama_dept]</td>
                    <td class='center'>$arr[THROONLD_ReleaseCode]</td>
                    <td class='center'>$arr[LoanCategory_Name]</td>
                    <td class='center'>$arr[THLOONLD_LoanCode]</td>
                    <td class='center'>$loandate</td>
                    <td class='center'>$arr[User_FullName]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$appdate</td>
                </tr>";
            }
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
