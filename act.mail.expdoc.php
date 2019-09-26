<!--
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 14 Sep 2018																						=
= Update Terakhir	: -																									=
= Revisi			:																									=
========================================================================================================================
-->
<link href="./css/mobile.css" rel="stylesheet" type="text/css">
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.expdoc.php");
$decrp = new custodian_encryp;

if( !empty($_GET['act']) && !empty($_GET['uadm']) && !empty($_GET['gd']) ) {
    $act=$decrp->decrypt($_GET['act']);
    $uadm = $decrp->decrypt($_GET['uadm']);

    if($act == "accept" || $act == "noneed"){
        if($act == "accept"){
            $status = "1";
            $pesan = "Dokumen telah dipilih untuk Diperbaharui.<br>Silahkan hubungi tim custodian untuk memastikan";
            mail_exp_legal_update('cust0002', $uadm);
        }elseif($act == "noneed"){
            $status = "2";
            $pesan = "Dokumen telah dipilih untuk Tidak Perlu Diperbaharui.";
        }else{
            $status = null;
        }
        $query = "UPDATE M_DocumentLegal mdl
                    LEFT JOIN M_DocumentCategory mdc
                        ON mdl.DL_CategoryDocID=mdc.DocumentCategory_ID
                    LEFT JOIN M_DocumentType mdt
                        ON mdt.DocumentType_ID=mdl.DL_TypeDocID
                    LEFT JOIN M_Company mc
                        ON mc.Company_ID=mdl.DL_CompanyID
                    SET mdl.DL_StatusReminderExpired = '$status'
                    WHERE mdl.DL_ExpDate IS NOT NULL
                        AND mdl.DL_RegUserID='$uadm'
                        AND mdl.DL_Delete_Time IS NULL
                        AND mdl.DL_StatusReminderExpired IS NULL
                        AND (
                            (UPPER(TRIM(DocumentCategory_Name))='HGU' AND DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH)=CURDATE()
                                AND
                                (
                                    (DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH)=CURDATE()) OR
                                    (DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
                                    (DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
                                    (DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
                                    (DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
                                    (DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
                                    (DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 6 DAY)=CURDATE())
                                )
                            )
                            OR
                            (
                                (DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH)=CURDATE()) OR
                                (DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
                                (DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
                                (DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
                                (DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
                                (DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
                                (DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 6 DAY)=CURDATE())
                            )
                        )";
            mysql_query($query);
        echo "
        <table border='0' align='center' cellpadding='0' cellspacing='0'>
        <tbody>
        <tr>
        <td class='header'>Tersimpan</td>
        </tr>
        <tr>
        <td>
        $pesan<br>
        Terima kasih.<br><br>
        Hormat Kami,<br />Departemen Custodian<br />
        PT Triputra Agro Persada
        </td>
        </tr>
        <tr>
        <td class='footer'>Powered By Custodian System </td>
        </tr>
        </tbody>
        </table>";
    }
}
