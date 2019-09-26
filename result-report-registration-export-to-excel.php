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
header("Content-Disposition: attachment; filename=Result_Laporan_Pendaftaran_-_Dokumen_$GrupDokumen.xls");

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

// echo $_POST['optDocumentGroup'];
if ($_POST['optDocumentGroup'] == '1' || $_POST['optDocumentGroup'] == '2'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thrgold.THROLD_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdrgold.TDROLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
    $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrgold.THROLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

    $query = "SELECT DISTINCT thrgold.THROLD_ID, thrgold.THROLD_RegistrationCode, thrgold.THROLD_RegistrationDate,
                              u.User_FullName, dp.Department_Name, drs.DRS_Description,
                              dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
                              dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
              FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc,
                   TH_RegistrationOfLegalDocument thrgold, TD_RegistrationOfLegalDocument tdrgold,
                   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp, M_DocumentRegistrationStatus drs
              WHERE thrgold.THROLD_DocumentGroupID='$_POST[optDocumentGroup]'
              AND tdrgold.TDROLD_THROLD_ID=thrgold.THROLD_ID
              $qcompany
              $qarea
              $qcategory
              $qperiod
              AND thrgold.THROLD_DocumentGroupID=dg.DocumentGroup_ID
              AND thrgold.THROLD_CompanyID=c.Company_ID
              AND thrgold.THROLD_Status=drs.DRS_Name
              AND tdrgold.TDROLD_DocumentCategoryID=dc.DocumentCategory_ID
              AND thrgold.THROLD_UserID=u.User_ID
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND thrgold.THROLD_Delete_Time IS NULL
              ORDER BY thrgold.THROLD_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='3'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND thrgolad.THRGOLAD_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrgolad.THRGOLAD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

    $query = "SELECT DISTINCT thrgolad.THRGOLAD_ID, thrgolad.THRGOLAD_RegistrationCode, drs.DRS_Description,
                              thrgolad.THRGOLAD_RegistrationDate, u.User_FullName, dp.Department_Name,
                              dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
              FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfLandAcquisitionDocument thrgolad,
                   TD_RegistrationOfLandAcquisitionDocument tdrgolad,M_DocumentRegistrationStatus drs,
                   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
              $qcompany
              $qarea
              $qperiod
              AND thrgolad.THRGOLAD_CompanyID=c.Company_ID
              AND thrgolad.THRGOLAD_UserID=u.User_ID
              AND thrgolad.THRGOLAD_RegStatus=drs.DRS_Name
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND thrgolad.THRGOLAD_Delete_Time IS NULL
              ORDER BY thrgolad.THRGOLAD_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='4'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND th.THROAOD_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND th.THROAOD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

    $query = "SELECT DISTINCT th.THROAOD_ID, th.THROAOD_RegistrationCode, drs.DRS_Description,
                              th.THROAOD_RegistrationDate, u.User_FullName, dp.Department_Name,
                              dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
              FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfAssetOwnershipDocument th,
                   TD_RegistrationOfAssetOwnershipDocument td, M_DocumentRegistrationStatus drs,
                   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              AND td.TDROAOD_THROAOD_ID=th.THROAOD_ID
              $qcompany
              $qarea
              $qperiod
              AND th.THROAOD_CompanyID=c.Company_ID
              AND th.THROAOD_UserID=u.User_ID
              AND th.THROAOD_Status=drs.DRS_Name
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND th.THROAOD_Delete_Time IS NULL
              ORDER BY th.THROAOD_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='5'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND th.THROOLD_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qcategory=(!$_POST['optDocumentCategory'])?"":"AND td.TDROOLD_KategoriDokumenID='$_POST[optDocumentCategory]'";
    $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND th.THROOLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

    $query = "SELECT DISTINCT th.THROOLD_ID, th.THROOLD_RegistrationCode, drs.DRS_Description,
                              th.THROOLD_RegistrationDate, u.User_FullName, dp.Department_Name,
                              dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
              FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfOtherLegalDocuments th,
                   TD_RegistrationOfOtherLegalDocuments td, M_DocumentRegistrationStatus drs,
                   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              AND td.TDROOLD_THROOLD_ID=th.THROOLD_ID
              $qcompany
              $qarea
              $qcategory
              $qperiod
              AND th.THROOLD_CompanyID=c.Company_ID
              AND th.THROOLD_UserID=u.User_ID
              AND th.THROOLD_Status=drs.DRS_Name
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND th.THROOLD_Delete_Time IS NULL
              ORDER BY th.THROOLD_ID LIMIT $offset, $dataPerPage";
}elseif ($_POST['optDocumentGroup']=='6'){
    $qcompany=($_POST['optCompany'] == "ALL")?"":"AND th.THROONLD_CompanyID='$_POST[optCompany]'";
    $qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
    $qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND th.THROONLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

    $query = "SELECT DISTINCT th.THROONLD_ID, th.THROONLD_RegistrationCode, drs.DRS_Description,
                              th.THROONLD_RegistrationDate, u.User_FullName, dp.Department_Name,
                              dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
              FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfOtherNonLegalDocuments th,
                   TD_RegistrationOfOtherNonLegalDocuments td, M_DocumentRegistrationStatus drs,
                   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
              WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
              AND td.TDROONLD_THROONLD_ID=th.THROONLD_ID
              $qcompany
              $qarea
              $qperiod
              AND th.THROONLD_CompanyID=c.Company_ID
              AND th.THROONLD_UserID=u.User_ID
              AND th.THROONLD_Status=drs.DRS_Name
              AND ddp.DDP_UserID=u.User_ID
              AND ddp.DDP_DeptID=dp.Department_ID
              AND th.THROONLD_Delete_Time IS NULL
              ORDER BY th.THROONLD_ID LIMIT $offset, $dataPerPage";
}

$Output = "";
$regdate = "";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if($num == NULL || $num == 0){
    $Output .= "<table>
        <tr>
            <td>Tidak ada data</td>
        </tr>
    </table>";
}else{
    if ($_POST['optDocumentGroup'] == '1' || $_POST['optDocumentGroup'] == '2'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $regdate=date("j M Y", strtotime($h_arr['THROLD_RegistrationDate']));
            $Output .= "<table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <th>No. Pendaftaran</th>
                    <td>
                        $h_arr[THROLD_RegistrationCode]
                    </td>
                    <th>Tanggal Pendaftaran</th>
                    <td>
                        $regdate
                    </td>
                </tr>
                <tr>
                    <th>Nama Pendaftar</th>
                    <td>
                        $h_arr[User_FullName]
                    </td>
                    <th>Departemen</th>
                    <td>
                        $h_arr[Department_Name]
                    </td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>$h_arr[Company_Name]</td>
                    <th>Status Pendaftaran</th>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <th>Grup Dokumen</th>
                    <td>
                        <input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
                    </td>
                    <th>Kategori Dokumen</th>
                    <td>$h_arr[DocumentCategory_Name]</td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
                <tr>
                    <th>Nama Perusahaan</th>
                    <th>Nama Dokumen</th>
                    <th>Instansi Terkait</th>
                    <th>Nomor Dokumen</th>
                    <th>Tanggal Terbit</th>
                    <th>Berlaku Sampai</th>
                    <th>Keterangan 1</th>
                    <th>Keterangan 2</th>
                    <th>Keterangan 3</th>
                </tr>
            ";
            $d_query="SELECT dt.DocumentType_Name, tdrgold.TDROLD_Instance, tdrgold.TDROLD_DocumentNo,
                             tdrgold.TDROLD_DatePublication,tdrgold.TDROLD_DateExpired,
                             di1.DocumentInformation1_Name, di2.DocumentInformation2_Name,
                             tdrgold.TDROLD_DocumentInformation3,
                            CASE WHEN tdrgold.TDROLD_Core_CompanyID IS NOT NULL
                                THEN (SELECT c.Company_Name FROM M_Company c
                                        WHERE c.Company_ID = tdrgold.TDROLD_Core_CompanyID
                                    )
                                ELSE (SELECT c.Company_Name FROM M_Company c
                                        WHERE c.Company_ID = thrgold.THROLD_CompanyID
                                    )
                            END AS company_name
                      FROM M_DocumentType dt, TH_RegistrationOfLegalDocument thrgold,
                           TD_RegistrationOfLegalDocument tdrgold, M_DocumentInformation1 di1,
                           M_DocumentInformation2 di2
                      WHERE thrgold.THROLD_ID='$h_arr[THROLD_ID]'
                      AND tdrgold.TDROLD_THROLD_ID=thrgold.THROLD_ID
                      AND tdrgold.TDROLD_DocumentTypeID=dt.DocumentType_ID
                      AND tdrgold.TDROLD_DocumentInformation1ID=di1.DocumentInformation1_ID
                      AND tdrgold.TDROLD_DocumentInformation2ID=di2.DocumentInformation2_ID
                      AND tdrgold.TDROLD_Delete_Time IS NULL ";
            $d_sql=mysql_query($d_query);

            while ($arr = mysql_fetch_array($d_sql)) {
                $berlaku=date("j M Y", strtotime($arr['TDROLD_DatePublication']));
                if ($arr['TDROLD_DateExpired']=="0000-00-00 00:00:00")
                    $expdate="-";
                else
                    $expdate=date("j M Y", strtotime($arr['TDROLD_DateExpired']));
            $Output .="
                <tr>
                    <td class='center'>$arr[company_name]</td>
                    <td class='center'>$arr[DocumentType_Name]</td>
                    <td class='center'>$arr[TDROLD_Instance]</td>
                    <td class='center'>$arr[TDROLD_DocumentNo]</td>
                    <td class='center'>$berlaku</td>
                    <td class='center'>$expdate</td>
                    <td class='center'>$arr[DocumentInformation1_Name]</td>
                    <td class='center'>$arr[DocumentInformation2_Name]</td>
                    <td class='center'>$arr[TDROLD_DocumentInformation3]</td>
                </tr>
            ";
                }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '3'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $regdate=date("j M Y", strtotime($h_arr['THRGOLAD_RegistrationDate']));
            $Output .= "<table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <th>No. Pendaftaran</th>
                    <td>
                        $h_arr[THRGOLAD_RegistrationCode]
                    </td>
                    <th>Tanggal Pendaftaran</th>
                    <td>
                        $regdate
                    </td>
                </tr>
                <tr>
                    <th>Nama Pendaftar</th>
                    <td>
                        $h_arr[User_FullName]
                    </td>
                    <th>Departemen</th>
                    <td>
                        $h_arr[Department_Name]
                    </td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>$h_arr[Company_Name]</td>
                    <th>Status Pendaftaran</th>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <th>Grup Dokumen</th>
                    <td>$h_arr[DocumentGroup_Name]</td>
                    <td></td><td></td><td></td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0' style='margin-bottom:10px;'>
                <tr>
                    <th>Nama Perusahaan</th>
                    <th>Tahap GRL</th>
                    <th>Periode GRL</th>
                    <th>Desa</th>
                    <th>Blok</th>
                    <th>Pemilik</th>
                    <th>Keterangan</th>
                </tr>
            ";
                $d_query="SELECT DISTINCT CASE WHEN tdrgolad.TDRGOLAD_Core_CompanyID IS NOT NULL
                                    THEN (SELECT c.Company_Name FROM M_Company c
                                            WHERE c.Company_ID = tdrgolad.TDRGOLAD_Core_CompanyID
                                        )
                                    ELSE (SELECT c.Company_Name FROM M_Company c
                                            WHERE c.Company_ID = thrgolad.THRGOLAD_CompanyID
                                        )
                                END AS company_name,
                                CASE WHEN tdrgolad.TDRGOLAD_Core_CompanyID IS NOT NULL
                                    THEN tdrgolad.TDRGOLAD_Core_Phase
                                    ELSE thrgolad.THRGOLAD_Phase
                                END AS tahap, thrgolad.THRGOLAD_Period,tdrgolad.TDRGOLAD_Village,tdrgolad.TDRGOLAD_Block,
                                          tdrgolad.TDRGOLAD_Owner,tdrgolad.TDRGOLAD_Information
                          FROM TH_RegistrationOfLandAcquisitionDocument thrgolad, TD_RegistrationOfLandAcquisitionDocument tdrgolad
                          WHERE thrgolad.THRGOLAD_ID='$h_arr[THRGOLAD_ID]'
                          AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
                          AND tdrgolad.TDRGOLAD_Delete_Time IS NULL ";
                $d_sql=mysql_query($d_query);

                while ($arr = mysql_fetch_array($d_sql)) {
                $periode=date("j M Y", strtotime($arr['THRGOLAD_Period']));
                if($periode == "1 Jan 1970"){ $periode = "-"; }
            $Output .="
                <tr>
                    <td class='center'>$arr[company_name]</td>
    				<td class='center'>$arr[tahap]</td>
                    <td class='center'>$periode</td>
                    <td class='center'>$arr[TDRGOLAD_Village]</td>
                    <td class='center'>$arr[TDRGOLAD_Block]</td>
                    <td class='center'>$arr[TDRGOLAD_Owner]</td>
                    <td class='center'>$arr[TDRGOLAD_Information]</td>
                </tr>";
                }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '4'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $regdate=date("j M Y", strtotime($h_arr['THROAOD_RegistrationDate']));
            $Output .= "<table width='100%' cellpadding='0' cellspacing='0' style='border-top:1px solid #555;'>
                <tr>
                    <th>No. Pendaftaran</th>
                    <td>
                        $h_arr[THROAOD_RegistrationCode]
                    </td>
                    <th>Tanggal Pendaftaran</th>
                    <td>
                        $regdate
                    </td>
                </tr>
                <tr>
                    <th>Nama Pendaftar</th>
                    <td>
                        $h_arr[User_FullName]
                    </td>
                    <th>Departemen</th>
                    <td>
                        $h_arr[Department_Name]
                    </td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>$h_arr[Company_Name]</td>
                    <th>Status Pendaftaran</th>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <th>Grup Dokumen</th>
                    <td>$h_arr[DocumentGroup_Name]</td>
                    <td></td><td></td><td></td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0' style='margin-bottom:10px;'>
                <tr>
                    <th>Nama Pemilik</th>
                    <th>Merk Kendaraan</th>
                    <th>No. Polisi</th>
                    <th>Lokasi</th>
                    <th>STNK</th>
                </tr>
            ";
                $d_query="SELECT DISTINCT m_mk.MK_Name merk_kendaraan,
                            CASE WHEN td.TDROAOD_Employee_NIK LIKE 'CO@%'
                              THEN
                                (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(td.TDROAOD_Employee_NIK, 'CO@', ''))
                              ELSE
                                (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=td.TDROAOD_Employee_NIK)
                            END nama_pemilik,
                            td.TDROAOD_NoPolisi, td.TDROAOD_Lokasi_PT,
                            CASE WHEN td.TDROAOD_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
                                WHEN td.TDROAOD_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
                                ELSE DATE_FORMAT(td.TDROAOD_STNK_StartDate, '%d/%m/%Y')
                            END AS start_stnk,
                            CASE WHEN td.TDROAOD_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
                                WHEN td.TDROAOD_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
                                ELSE DATE_FORMAT(td.TDROAOD_STNK_ExpiredDate, '%d/%m/%Y')
                            END AS expired_stnk
                          FROM TH_RegistrationOfAssetOwnershipDocument th
                          LEFT JOIN TD_RegistrationOfAssetOwnershipDocument td
                            ON td.TDROAOD_THROAOD_ID=th.THROAOD_ID
                          LEFT JOIN db_master.M_MerkKendaraan m_mk
                            ON m_mk.MK_ID=td.TDROAOD_MK_ID
                          WHERE th.THROAOD_ID='$h_arr[THROAOD_ID]'
                          AND td.TDROAOD_Delete_Time IS NULL ";
                $d_sql=mysql_query($d_query);

                while ($arr = mysql_fetch_array($d_sql)) {
            $Output .="
                <tr>
                    <td class='center'>$arr[nama_pemilik]</td>
                    <td class='center'>$arr[merk_kendaraan]</td>
                    <td class='center'>$arr[TDROAOD_NoPolisi]</td>
                    <td class='center'>$arr[TDROAOD_Lokasi_PT]</td>
                    <td class='center'>".$arr['start_stnk']." s/d
                    ".$arr['expired_stnk']."</td>
                </tr>";
                }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '5'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $regdate=date("j M Y", strtotime($h_arr['THROOLD_RegistrationDate']));
            $Output .="
                <table width='100%' cellpadding='0' cellspacing='0' style='border-top:1px solid #555;'>
                <tr>
                    <th>No. Pendaftaran</th>
                    <td>
                        $h_arr[THROOLD_RegistrationCode]
                    </td>
                    <th>Tanggal Pendaftaran</th>
                    <td>
                        $regdate
                    </td>
                </tr>
                <tr>
                    <th>Nama Pendaftar</th>
                    <td>
                        $h_arr[User_FullName]
                    </td>
                    <th>Departemen</th>
                    <td>
                        $h_arr[Department_Name]
                    </td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>$h_arr[Company_Name]</td>
                    <th>Status Pendaftaran</th>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <th>Grup Dokumen</th>
                    <td>$h_arr[DocumentGroup_Name]</td>
                    <td></td><td></td><td></td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0' style='margin-bottom:10px;'>
                <tr>
                    <th>Nama Perusahaan</th>
                    <th>Kategori Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Instansi Terkait</th>
                    <th>Nomor Dokumen</th>
                    <th>Tanggal Terbit</th>
                    <th>Tanggal Berakhir</th>
                </tr>
            ";
                $d_query="SELECT DISTINCT CASE WHEN td.TDROOLD_Core_CompanyID IS NOT NULL
									THEN (SELECT c.Company_Name FROM M_Company c
											WHERE c.Company_ID = td.TDROOLD_Core_CompanyID
										)
									ELSE (SELECT c.Company_Name FROM M_Company c
											WHERE c.Company_ID = th.THROOLD_CompanyID
										)
								END AS company_name, td.TDROOLD_NamaDokumen,
                            td.TDROOLD_InstansiTerkait,
                            td.TDROOLD_NoDokumen,
                            CASE WHEN td.TDROOLD_TglTerbit LIKE '%0000-00-00%' THEN '-'
                                WHEN td.TDROOLD_TglTerbit LIKE '%1970-01-01%' THEN '-'
                                ELSE DATE_FORMAT(td.TDROOLD_TglTerbit, '%d/%m/%Y')
                            END AS tgl_terbit,
                            CASE WHEN td.TDROOLD_TglBerakhir LIKE '%0000-00-00%' THEN '-'
                                WHEN td.TDROOLD_TglBerakhir LIKE '%1970-01-01%' THEN '-'
                                ELSE DATE_FORMAT(td.TDROOLD_TglBerakhir, '%d/%m/%Y')
                            END AS tgl_berakhir,
                            td.TDROOLD_Keterangan, DocumentCategory_Name
                          FROM TH_RegistrationOfOtherLegalDocuments th
                          LEFT JOIN TD_RegistrationOfOtherLegalDocuments td
                            ON td.TDROOLD_THROOLD_ID=th.THROOLD_ID
                          LEFT JOIN db_master.M_DocumentCategory dc
                            ON td.TDROOLD_KategoriDokumenID=dc.DocumentCategory_ID
                          WHERE th.THROOLD_ID='$h_arr[THROOLD_ID]'
                          $qcategory
                          AND td.TDROOLD_Delete_Time IS NULL ";
                $d_sql=mysql_query($d_query);

                while ($arr = mysql_fetch_array($d_sql)) {
            $Output .="
                <tr>
                    <td class='center'>$arr[company_name]</td>
                    <td class='center'>$arr[DocumentCategory_Name]</td>
                    <td class='center'>$arr[TDROOLD_NamaDokumen]</td>
                    <td class='center'>$arr[TDROOLD_InstansiTerkait]</td>
                    <td class='center'>$arr[TDROOLD_NoDokumen]</td>
                    <td class='center'>$arr[tgl_terbit]</td>
                    <td class='center'>$arr[tgl_berakhir]</td>
                </tr>";
                }
            $Output .="
                </table>
            ";
        }
    }elseif ($_POST['optDocumentGroup'] == '6'){
        while ($h_arr = mysql_fetch_array($sql)) {
            $regdate=date("j M Y", strtotime($h_arr['THROONLD_RegistrationDate']));
            $Output .="
                <table width='100%' cellpadding='0' cellspacing='0' style='border-top:1px solid #555;'>
                <tr>
                    <th>No. Pendaftaran</th>
                    <td>
                        $h_arr[THROONLD_RegistrationCode]
                    </td>
                    <th>Tanggal Pendaftaran</th>
                    <td>
                        $regdate
                    </td>
                </tr>
                <tr>
                    <th>Nama Pendaftar</th>
                    <td>
                        $h_arr[User_FullName]
                    </td>
                    <th>Departemen</th>
                    <td>
                        $h_arr[Department_Name]
                    </td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>$h_arr[Company_Name]</td>
                    <th>Status Pendaftaran</th>
                    <td>$h_arr[DRS_Description]</td>
                </tr>
                <tr>
                    <th>Grup Dokumen</th>
                    <td>$h_arr[DocumentGroup_Name]</td>
                    <td></td><td></td><td></td>
                </tr>
                </table>
                <table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0' style='margin-bottom:10px;'>
                <tr>
                    <th>Nama Perusahaan</th>
                    <th>No. Dokumen</th>
                    <th>Nama Dokumen</th>
                    <th>Tahun Dokumen</th>
                    <th>Departemen</th>
                    <th>Keterangan</th>
                </tr>
            ";
            $d_query="SELECT DISTINCT CASE WHEN td.TDROONLD_Core_CompanyID IS NOT NULL
                            THEN (SELECT c.Company_Name FROM M_Company c
                                    WHERE c.Company_ID = td.TDROONLD_Core_CompanyID
                                )
                            ELSE (SELECT c.Company_Name FROM M_Company c
                                    WHERE c.Company_ID = th.THROONLD_CompanyID
                                )
                        END AS company_name, td.TDROONLD_NamaDokumen,
                        td.TDROONLD_TahunDokumen,
                        td.TDROONLD_NoDokumen, td.TDROONLD_TahunDokumen,
                        td.TDROONLD_Keterangan, md.Department_Name
                      FROM TH_RegistrationOfOtherNonLegalDocuments th
                      LEFT JOIN TD_RegistrationOfOtherNonLegalDocuments td
                        ON td.TDROONLD_THROONLD_ID=th.THROONLD_ID
                      LEFT JOIN M_Department md
                        ON md.Department_ID=td.TDROONLD_Dept_Code
                      WHERE th.THROONLD_ID='$h_arr[THROONLD_ID]'
                      AND td.TDROONLD_Delete_Time IS NULL ";
                $d_sql=mysql_query($d_query);

                while ($arr = mysql_fetch_array($d_sql)) {
            $Output .="
                <tr>
                    <td class='center'>$arr[company_name]</td>
                    <td class='center'>$arr[TDROONLD_NoDokumen]</td>
                    <td class='center'>$arr[TDROONLD_NamaDokumen]</td>
                    <td class='center'>$arr[TDROONLD_TahunDokumen]</td>
                    <td class='center'>$arr[Department_Name]</td>
                    <td class='center'>$arr[TDROONLD_Keterangan]</td>
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
