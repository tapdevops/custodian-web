<?php

switch($_GET['optTHROLD_DocumentGroupID']){
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
header("Content-Disposition: attachment; filename=Result_Pencarian_-_Dokumen_$GrupDokumen.xls");

include ("./config/config_db.php");
error_reporting(E_ALL);
$PHP_SELF = "http://".$_SERVER['HTTP_HOST'];

// Menampilkan Dokumen

if ($_GET['optTHROLD_DocumentGroupID'] ==  '1' or $_GET['optTHROLD_DocumentGroupID'] == '2'){
    $query = "SELECT dl.DL_ID, dl.DL_DocCode, c.Company_Name, dc.DocumentCategory_Name,
    dt.DocumentType_Name, dl.DL_PubDate, dl.DL_ExpDate, lds.LDS_Name, dg.DocumentGroup_Name,
    dl.DL_NoDoc, dl.DL_Location, u.User_FullName
    FROM
    M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt, M_LoanDetailStatus lds,
    M_DocumentGroup dg, M_DocumentInformation1 di1, M_DocumentInformation2 di2, M_User u
    WHERE
    dl.DL_GroupDocID = '$_GET[optTHROLD_DocumentGroupID]'
    AND dl.DL_GroupDocID = dg.DocumentGroup_ID
    AND dl.DL_CompanyID = c.Company_ID
    AND dl.DL_CategoryDocID = dc.DocumentCategory_ID
    AND dl.DL_TypeDocID = dt.DocumentType_ID
    AND lds.LDS_ID = dl.DL_Status
    AND dl.DL_RegUserID = u.User_ID
    AND dl.DL_Information1 = di1.DocumentInformation1_ID
    AND dl.DL_Information2 = di2.DocumentInformation2_ID
    AND dl.DL_Delete_Time IS NULL ";

    if ($_GET['txtSearch']) {
        $search=$_GET['txtSearch'];
        $query .="AND (
                    dl.DL_DocCode LIKE '%$search%'
                    OR dl.DL_CompanyID LIKE '%$search%'
                    OR c.Company_Name LIKE '%$search%'
                    OR dl.DL_CategoryDocID LIKE '%$search%'
                    OR dc.DocumentCategory_Name LIKE '%$search%'
                    OR dl.DL_TypeDocID LIKE '%$search%'
                    OR dt.DocumentType_Name LIKE '%$search%'
                    OR dl.DL_RegUserID LIKE '%$search%'
                    OR u.User_FullName LIKE '%$search%'
                    OR dl.DL_Information1 LIKE '%$search%'
                    OR di1.DocumentInformation1_Name LIKE '%$search%'
                    OR dl.DL_Information2 LIKE '%$search%'
                    OR di2.DocumentInformation2_Name LIKE '%$search%'
                    OR dl.DL_Information3 LIKE '%$search%'
                    OR dl.DL_Instance LIKE '%$search%'
                    OR dl.DL_RegTime LIKE '%$search%'
                    OR dl.DL_NoDoc LIKE '%$search%'
                    OR dl.DL_PubDate LIKE '%$search%'
                    OR dl.DL_ExpDate LIKE '%$search%'
                    OR lds.LDS_Name LIKE '%$search%'
                )";
    }
    if ($_GET['optDocumentCategory']) {
		$query .="AND dl.DL_CategoryDocID='".$_GET['optDocumentCategory']."' ";
	}
    if ($_GET['optCompanyID']!=-1) {
        $query .="AND dl.DL_CompanyID='".$_GET['optCompanyID']."' ";
    }
    if ($_GET['optDocumentStatusID']!=-1) {
        $query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
    }
    if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
        $query .="AND (dl.DL_PubDate BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y')
        )";
    }
    else if($_GET['txtDateStart']!=""){
        $query .="AND (dl.DL_PubDate > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y'))";
    }
    else if($_GET['txtDateEnd']!=""){
        $query .="AND (dl.DL_PubDate < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
    }
}
elseif ($_GET['optTHROLD_DocumentGroupID']=='3'){
    $query = "SELECT dla.DLA_ID, c.Company_Name, dla.DLA_Phase, dla.DLA_Period, dla.DLA_DocRevision, lds.LDS_Name,
                     dla.DLA_Code, dla.DLA_Location, u.User_FullName
              FROM M_DocumentLandAcquisition dla, M_Company c, M_User u,  M_LoanDetailStatus lds
              WHERE c.Company_ID=dla.DLA_CompanyID
              AND dla.DLA_Delete_Time IS NULL
              AND dla.DLA_Status=lds.LDS_ID
              AND dla.DLA_RegUserID=u.User_ID ";

    if ($_GET['txtSearch']) {
        $search=$_GET['txtSearch'];
        $query .="AND (
                    dla.DLA_Code LIKE '%$search%'
                    OR dla.DLA_CompanyID LIKE '%$search%'
                    OR c.Company_Name LIKE '%$search%'
                    OR dla.DLA_RegUserID LIKE '%$search%'
                    OR u.User_FullName LIKE '%$search%'
                    OR dla.DLA_Information LIKE '%$search%'
                    OR dla.DLA_RegTime LIKE '%$search%'
                    OR dla.DLA_Phase LIKE '%$search%'
                    OR dla.DLA_Period LIKE '%$search%'
                    OR dla.DLA_DocDate LIKE '%$search%'
                    OR dla.DLA_Block LIKE '%$search%'
                    OR dla.DLA_Village LIKE '%$search%'
                    OR dla.DLA_Owner LIKE '%$search%'
                    OR dla.DLA_Information LIKE '%$search%'
                    OR dla.DLA_AreaClass LIKE '%$search%'
                    OR dla.DLA_AreaStatement LIKE '%$search%'
                    OR dla.DLA_AreaPrice LIKE '%$search%'
                    OR dla.DLA_AreaTotalPrice LIKE '%$search%'
                    OR dla.DLA_PlantClass LIKE '%$search%'
                    OR dla.DLA_PlantQuantity LIKE '%$search%'
                    OR dla.DLA_PlantPrice LIKE '%$search%'
                    OR dla.DLA_PlantTotalPrice LIKE '%$search%'
                    OR dla.DLA_GrandTotal LIKE '%$search%'
                    OR lds.LDS_Name LIKE '%$search%'
                )";
    }
    if ($_GET['optCompanyID']!=-1) {
        $query .="AND dla.DLA_CompanyID='".$_GET['optCompanyID']."' ";
    }
    if ($_GET['optDocumentStatusID']!=-1) {
        $query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
    }
    if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
        $query .="AND (dl.DLA_Period BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
    }
    else if($_GET['txtDateStart']!=""){
        $query .="AND (dl.DLA_Period > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y'))";
    }
    else if($_GET['txtDateEnd']!=""){
        $query .="AND (dl.DLA_Period < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
    }
}
elseif ($_GET['optTHROLD_DocumentGroupID']=='4'){
    $query = "SELECT dao.DAO_ID, m_mk.MK_Name,
                    CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
                      THEN
                        (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
                      ELSE
                        (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
                    END nama_pemilik,
                     dao.DAO_Employee_NIK,
                     dao.DAO_NoPolisi,
                     CASE WHEN dao.DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
                         WHEN dao.DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
                         ELSE DATE_FORMAT(dao.DAO_STNK_StartDate, '%d/%m/%Y')
                     END AS start_stnk,
                     CASE WHEN dao.DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
                         WHEN dao.DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
                         ELSE DATE_FORMAT(dao.DAO_STNK_ExpiredDate, '%d/%m/%Y')
                     END AS expired_stnk,
                     CASE WHEN dao.DAO_Pajak_StartDate LIKE '%0000-00-00%' THEN '-'
                         WHEN dao.DAO_Pajak_StartDate LIKE '%1970-01-01%' THEN '-'
                         ELSE DATE_FORMAT(dao.DAO_Pajak_StartDate, '%d/%m/%Y')
                     END AS start_pajak,
                     CASE WHEN dao.DAO_Pajak_ExpiredDate LIKE '%0000-00-00%' THEN '-'
                         WHEN dao.DAO_Pajak_ExpiredDate LIKE '%1970-01-01%' THEN '-'
                         ELSE DATE_FORMAT(dao.DAO_Pajak_ExpiredDate, '%d/%m/%Y')
                     END AS expired_pajak,
                     dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate, dao.DAO_Pajak_StartDate, dao.DAO_Pajak_ExpiredDate,
                     lds.LDS_Name, dao.DAO_DocCode, dao.DAO_Location, u.User_FullName
              FROM M_DocumentAssetOwnership dao, M_User u, M_LoanDetailStatus lds, db_master.M_MerkKendaraan m_mk
              WHERE dao.DAO_Delete_Time IS NULL
              AND dao.DAO_Status=lds.LDS_ID
              AND dao.DAO_RegUserID=u.User_ID
              AND m_mk.MK_ID=dao.DAO_MK_ID
            ";

    if ($_GET['txtSearch']) {
        $search=$_GET['txtSearch'];
        $query .="AND (
                    dao.DAO_DocCode LIKE '%$search%'
                    OR dao.DAO_RegUserID LIKE '%$search%'
                    OR u.User_FullName LIKE '%$search%'
                    OR dao.DAO_RegTime LIKE '%$search%'
                    OR lds.LDS_Name LIKE '%$search%'
                    OR dao.DAO_STNK_StartDate LIKE '%$search%'
                    OR dao.DAO_STNK_ExpiredDate LIKE '%$search%'
                    OR dao.DAO_Pajak_StartDate LIKE '%$search%'
                    OR dao.DAO_Pajak_ExpiredDate LIKE '%$search%'
                    OR (dao.DAO_Employee_NIK LIKE '%$search%' OR e.Employee_FullName LIKE '%$search%' OR c.Company_Name LIKE '%$search%')
                    OR m_mk.MK_Name LIKE '%$search%'
                    OR dao.DAO_NoPolisi LIKE '%$search%'
                    OR dao.DAO_NoBPKB LIKE '%$search%'
                    OR dao.DAO_NoMesin LIKE '%$search%'
                    OR dao.DAO_NoRangka LIKE '%$search%'
                    OR dao.DAO_Type LIKE '%$search%'
                    OR dao.DAO_Jenis LIKE '%$search%'
                    OR dao.DAO_Lokasi_PT LIKE '%$search%'
                    OR dao.DAO_Region LIKE '%$search%'
                    OR dao.DAO_Keterangan LIKE '%$search%'
                )";
    }
    if ($_GET['optCompanyID']!=-1) {
        $query .="AND dao.DAO_CompanyID='".$_GET['optCompanyID']."' ";
    }
    if ($_GET['optDocumentStatusID']!=-1) {
        $query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
    }
    if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
        $query .="AND ((dao.DAO_STNK_ExpiredDate BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y') OR (dao.DAO_Pajak_ExpiredDate BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
    }
    else if($_GET['txtDateStart']!=""){
        $query .="AND (
            dao.DAO_STNK_ExpiredDate > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y')
            OR dao.DAO_Pajak_ExpiredDate > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y')
        )";
    }
    else if($_GET['txtDateEnd']!=""){
        $query .="AND (
            dao.DAO_STNK_ExpiredDate < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y')
            OR dao.DAO_Pajak_ExpiredDate < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y')
        )";
    }
}
elseif ($_GET['optTHROLD_DocumentGroupID']=='5'){
    $query = "SELECT dol.DOL_ID, c.Company_Name, m_dc.DocumentCategory_Name,
                     dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
                     dol.DOL_TglTerbit, dol.DOL_TglBerakhir, lds.LDS_Name, dol.DOL_DocCode,
                     dol.DOL_Location, u.User_FullName
              FROM M_DocumentsOtherLegal dol, M_Company c, M_User u,  M_LoanDetailStatus lds,
                db_master.M_DocumentCategory m_dc
              WHERE c.Company_ID=dol.DOL_CompanyID
              AND dol.DOL_Delete_Time IS NULL
              AND dol.DOL_Status=lds.LDS_ID
              AND dol.DOL_RegUserID=u.User_ID
              AND m_dc.DocumentCategory_ID=DOL_CategoryDocID
             ";

    if ($_GET['txtSearch']) {
        $search=$_GET['txtSearch'];
        $query .="AND (
                    dol.DOL_DocCode LIKE '%$search%'
                    OR dol.DOL_CompanyID LIKE '%$search%'
                    OR c.Company_Name LIKE '%$search%'
                    OR dol.DOL_RegUserID LIKE '%$search%'
                    OR u.User_FullName LIKE '%$search%'
                    OR dol.DOL_RegTime LIKE '%$search%'
                    OR lds.LDS_Name LIKE '%$search%'
                    OR m_dc.DocumentCategory_Name LIKE '%$search%'
                    OR dol.DOL_NamaDokumen LIKE '%$search%'
                    OR dol.DOL_InstansiTerkait LIKE '%$search%'
                    OR dol.DOL_NoDokumen LIKE '%$search%'
                    OR dol.DOL_TglTerbit LIKE '%$search%'
                    OR dol.DOL_TglBerakhir LIKE '%$search%'
                )";
    }
    if ($_GET['optCompanyID']!=-1) {
        $query .="AND dol.DOL_CompanyID='".$_GET['optCompanyID']."' ";
    }
    if ($_GET['optDocumentStatusID']!=-1) {
        $query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
    }
    if($_GET['txtDateStart']!=""&&$_GET['txtDateEnd']!="") {
        $query .="AND (dol.DOL_TglTerbit BETWEEN STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y') AND STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
    }
    else if($_GET['txtDateStart']!=""){
        $query .="AND (dol.DOL_TglTerbit > STR_TO_DATE('".$_GET['txtDateStart']."', '%m/%d/%Y'))";
    }
    else if($_GET['txtDateEnd']!=""){
        $query .="AND (dol.DOL_TglTerbit < STR_TO_DATE('".$_GET['txtDateEnd']."', '%m/%d/%Y'))";
    }
}
elseif ($_GET['optTHROLD_DocumentGroupID']=='6'){
    $query = "SELECT donl.DONL_ID, c.Company_Name, donl.DONL_NoDokumen, donl.DONL_NamaDokumen,
                donl.DONL_TahunDokumen, m_d.Department_Name,
                lds.LDS_Name, donl.DONL_DocCode, donl.DONL_Location, u.User_FullName
              FROM M_DocumentsOtherNonLegal donl, M_Company c, M_User u, M_LoanDetailStatus lds,
                db_master.M_Department m_d
              WHERE c.Company_ID=donl.DONL_CompanyID
              AND donl.DONL_Delete_Time IS NULL
              AND donl.DONL_Status=lds.LDS_ID
              AND donl.DONL_RegUserID=u.User_ID
              AND m_d.Department_Code=donl.DONL_Dept_Code
             ";

    if ($_GET['txtSearch']) {
        $search=$_GET['txtSearch'];
        $query .="AND (
                    donl.DONL_DocCode LIKE '%$search%'
                    OR donl.DONL_CompanyID LIKE '%$search%'
                    OR c.Company_Name LIKE '%$search%'
                    OR donl.DONL_RegUserID LIKE '%$search%'
                    OR u.User_FullName LIKE '%$search%'
                    OR donl.DONL_RegTime LIKE '%$search%'
                    OR lds.LDS_Name LIKE '%$search%'
                    OR donl.DONL_NoDokumen LIKE '%$search%'
                    OR donl.DONL_NamaDokumen LIKE '%$search%'
                    OR donl.DONL_TahunDokumen LIKE '%$search%'
                    OR m_d.Department_Name LIKE '%$search%'
                    OR donl.DONL_Dept_Code LIKE '%$search%'
                ) ";
    }
    if ($_GET['optCompanyID']!=-1) {
        $query .="AND donl.DONL_CompanyID='".$_GET['optCompanyID']."' ";
    }
    if ($_GET['optDocumentStatusID']!=-1) {
        $query .="AND lds.LDS_Name='".$_GET['optDocumentStatusID']."' ";
    }
    else if($_GET['optTahunDokumen']!=-1){
        $query .="AND donl.DONL_TahunDokumen = STR_TO_DATE('".$_GET['optTahunDokumen']."', '%Y')";
    }
}

$queryAll=$query;
$sql = mysql_query($queryAll);
$num = mysql_num_rows($sql);
$sqldg = mysql_query($queryAll);
$arr = mysql_fetch_array($sqldg);

$MainContent = "";

//echo $queryAll;
if ($_GET['optTHROLD_DocumentGroupID'] == '1' or $_GET['optTHROLD_DocumentGroupID'] == '2'){
    if ($num==NULL) {
    $MainContent .="
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>Kategori</th>
            <th>Tipe</th>
            <th>Tanggal Terbit</th>
            <th>Tanggal Habis Masa Berlaku</th>
            <th>Status</th>
            <th>No. Dokumen</th>
            <th>Pendaftar</th>
        </tr>
        <tr>
            <td colspan=7 align='center'>Belum Ada Data</td>
        </tr>
        </table>
    ";
    }
    if ($num<>NULL){
    $MainContent .="
        <form name='list' method='GET' target='_blank' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th colspan='10' align='center'>Daftar Dokumen $arr[DocumentGroup_Name]</th>
        </tr>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>Kategori</th>
            <th>Tipe</th>
            <th>Tanggal Terbit</th>
            <th>Tanggal Habis Masa Berlaku</th>
            <th>Status</th>
            <th>No. Dokumen</th>
            <th>Pendaftar</th>
        </tr>
    ";

        while ($field = mysql_fetch_array($sql)) {
            $fpubdate=(strpos($field['DL_PubDate'], '0000-00-00') !== false || strpos($field['DL_PubDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field['DL_PubDate']));
            $fexpdate=(strpos($field['DL_ExpDate'], '0000-00-00') !== false || strpos($field['DL_ExpDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field['DL_ExpDate']));
    $MainContent .="
        <tr>
            <td class='center'>$field[DL_ID]</td>
            <td class='center'>
                <a href='$PHP_SELF?act=detail& id=$field[DL_DocCode]' class='underline'>$field[DL_DocCode]</a>
            </td>
            <td class='center'>$field[Company_Name]</td>
            <td class='center'>$field[DocumentCategory_Name]</td>
            <td class='center'>$field[DocumentType_Name]</td>
            <td class='center'>$fpubdate</td>
            <td class='center'>$fexpdate</td>
            <td class='center'>$field[LDS_Name]</td>
            <td class='center'>$field[DL_NoDoc]</td>
            <td class='center'>$field[User_FullName]</td>
        </tr>
    ";
        $no=$no+1;
        }
    $MainContent .="
        </table>
        </form>
    ";
    }
}

elseif ($_GET['optTHROLD_DocumentGroupID']=='3'){
    if ($num==NULL) {
    $MainContent .="
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>Tahap</th>
            <th>Periode</th>
            <th>Revisi</th>
            <th>Status</th>
            <th>Pendaftar</th>
        </tr>
        <tr>
            <td colspan=8 align='center'>Belum Ada Data</td>
        </tr>
        </table>
    ";
    }
    if ($num<>NULL){
    $MainContent .="
        <form name='list' method='GET' action='print-land-acquisition-document-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th colspan='8' align='center'>Daftar Dokumen Pembebasan Lahan</th>
        </tr>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>Tahap</th>
            <th>Periode</th>
            <th>Revisi</th>
            <th>Status</th>
            <th>Pendaftar</th>
        </tr>
    ";

        while ($field = mysql_fetch_array($sql)) {
            $regdate=strtotime($field[3]);
            $fregdate=date("j M Y", $regdate);
    $MainContent .="
        <tr>
            <td class='center'>$field[DLA_ID]</td>
            <td class='center'>
                <a href='$PHP_SELF?act=detailLA&id=$field[0]' class='underline'>$field[DLA_Code]</a></td>
            <td class='center'>$field[1]</td>
            <td class='center'>$field[2]</td>
            <td class='center'>$fregdate</td>
            <td class='center'>$field[4]</td>
            <td class='center'>$field[5]</td>
            <td class='center'>$field[User_FullName]</td>
        </tr>
    ";
        $no=$no+1;
        }
    $MainContent .="
        </table>
        </form>
    ";
    }
}

elseif ($_GET['optTHROLD_DocumentGroupID']=='4'){
    if ($num==NULL) {
    $MainContent .="
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th rowspan='2'>ID</th>
            <th rowspan='2'>Kode Dokumen</th>
            <th rowspan='2'>Merk Kendaraan</th>
            <th rowspan='2'>Nama Pemilik</th>
            <th rowspan='2'>No. Polisi</th>
            <th colspan='2'>STNK</th>
            <th colspan='2'>Pajak</th>
            <th rowspan='2'>Status</th>
            <th rowspan='2'>Pendaftar</th>
        </tr>
        <tr>
            <th>Start Date</th>
            <th>Expired Date</th>
            <th>Start Date</th>
            <th>Expired Date</th>
        </tr>
        <tr>
            <td colspan=11 align='center'>Belum Ada Data</td>
        </tr>
        </table>
    ";
    }
    if ($num<>NULL){
    $MainContent .="
        <form name='list' method='GET' action='print-asset-ownership-document-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th colspan='12' align='center'>Daftar Dokumen Kepemilikan Aset</th>
        </tr>
        <tr>
            <th rowspan='2'>ID</th>
            <th rowspan='2'>Kode Dokumen</th>
            <th rowspan='2'>Merk Kendaraan</th>
            <th rowspan='2'>Nama Pemilik</th>
            <th rowspan='2'>No. Polisi</th>
            <th colspan='2'>STNK</th>
            <th colspan='2'>Pajak</th>
            <th rowspan='2'>Status</th>
            <th rowspan='2'>Pendaftar</th>
        </tr>
        <tr>
            <th>Start Date</th>
            <th>Expired Date</th>
            <th>Start Date</th>
            <th>Expired Date</th>
        </tr>
    ";

        while ($field = mysql_fetch_array($sql)) {
    $MainContent .="
        <tr>
            <td class='center'>$field[DAO_ID]</td>
            <td class='center'>
                <a href='$PHP_SELF?act=detailAO&id=$field[DAO_DocCode]' class='underline'>$field[DAO_DocCode]</a></td>
            <td class='center'>$field[MK_Name]</td>
            <td class='center'>$field[nama_pemilik]</td>
            <td class='center'>$field[DAO_NoPolisi]</td>
            <td class='center'>$field[start_stnk]</td>
            <td class='center'>$field[expired_stnk]</td>
            <td class='center'>$field[start_pajak]</td>
            <td class='center'>$field[expired_pajak]</td>
            <td class='center'>$field[LDS_Name]</td>
            <td class='center'>$field[User_FullName]</td>
        </tr>
    ";

        }
    $MainContent .="
        </table>
        </form>
    ";
    }
}

elseif ($_GET['optTHROLD_DocumentGroupID']=='5'){
    if ($num==NULL) {
    $MainContent .="
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>Kategori Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Instansi Terkait</th>
            <th>No. Dokumen</th>
            <th>Tanggal Terbit</th>
            <th>Tanggal Berakhir</th>
            <th>Status</th>
            <th>Pendaftar</th>
        </tr>
        <tr>
            <td colspan=11 align='center'>Belum Ada Data</td>
        </tr>
        </table>
    ";
    }
    if ($num<>NULL){
    $MainContent .="
        <form name='list' method='GET' action='print-other-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th colspan='11' align='center'>Daftar Dokumen Lainnya (Legal)</th>
        </tr>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>Kategori Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Instansi Terkait</th>
            <th>No. Dokumen</th>
            <th>Tanggal Terbit</th>
            <th>Tanggal Berakhir</th>
            <th>Status</th>
            <th>Pendaftar</th>
        </tr>
    ";

        while ($field = mysql_fetch_array($sql)) {

            $tgl_terbit=(strpos($field[6], '0000-00-00') !== false || strpos($field[6], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field[6]));
            $tgl_berakhir=(strpos($field[7], '0000-00-00') !== false || strpos($field[7], '1970-01-01') !== false)?"-":date("j M Y", strtotime($field[7]));
    $MainContent .="
        <tr>
            <td class='center'>$field[DOL_ID]</td>
            <td class='center'>
                <a href='$PHP_SELF?act=detailOL&id=$field[DOL_DocCode]' class='underline'>$field[DOL_DocCode]</a></td>
            <td class='center'>$field[1]</td>
            <td class='center'>$field[2]</td>
            <td class='center'>$field[3]</td>
            <td class='center'>$field[4]</td>
            <td class='center'>$field[5]</td>
            <td class='center'>$tgl_terbit</td>
            <td class='center'>$tgl_berakhir</td>
            <td class='center'>$field[8]</td>
            <td class='center'>$field[User_FullName]</td>
        </tr>
    ";
        $no=$no+1;
        }
    $MainContent .="
        </table>
        </form>
    ";
    }
}

elseif ($_GET['optTHROLD_DocumentGroupID']=='6'){
    if ($num==NULL) {
    $MainContent .="
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>No. Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Tahun Dokumen</th>
            <th>Departemen</th>
            <th>Status</th>
            <th>Pendaftar</th>
        </tr>
        <tr>
            <td colspan=9 align='center'>Belum Ada Data</td>
        </tr>
        </table>
    ";
    }
    if ($num<>NULL){
    $MainContent .="
        <form name='list' method='GET' action='print-other-non-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);' target='_blank'>
        <table width='100%' border='1' class='stripeMe'>
        <tr>
            <th colspan='9' align='center'>Daftar Dokumen Lainnya (Di Luar Legal)</th>
        </tr>
        <tr>
            <th>ID</th>
            <th>Kode Dokumen</th>
            <th>Perusahaan</th>
            <th>No. Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Tahun Dokumen</th>
            <th>Departemen</th>
            <th>Status</th>
            <th>Pendaftar</th>
        </tr>
    ";

        while ($field = mysql_fetch_array($sql)) {
            $regdate=strtotime($field[3]);
            $fregdate=date("j M Y", $regdate);
    $MainContent .="
        <tr>
            <td class='center'>$field[DONL_ID]</td>
            <td class='center'>
                <a href='$PHP_SELF?act=detailONL&id=$field[DONL_DocCode]' class='underline'>$field[DONL_DocCode]</a></td>
            <td class='center'>$field[1]</td>
            <td class='center'>$field[2]</td>
            <td class='center'>$field[3]</td>
            <td class='center'>$field[4]</td>
            <td class='center'>$field[5]</td>
            <td class='center'>$field[6]</td>
            <td class='center'>$field[User_FullName]</td>
        </tr>
    ";
        $no=$no+1;
        }
    $MainContent .="
        </table>
        </form>
    ";
    }
}

echo $MainContent;

exit();
?>
