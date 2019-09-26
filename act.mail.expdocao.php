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
include ("./include/function.mail.expdocao.php");
$decrp = new custodian_encryp;

if( !empty($_GET['act']) && !empty($_GET['uadm']) && !empty($_GET['gd']) ) {
    $act=$decrp->decrypt($_GET['act']);
    $uadm = $decrp->decrypt($_GET['uadm']);

    if($act == "accept" || $act == "noneed"){
        if($act == "accept"){
            $status = "1";
            $pesan = "Dokumen telah dipilih untuk Diperbaharui.<br>Silahkan hubungi tim custodian untuk memastikan";
            mail_exp_asset_ownership_update('cust0002', $uadm);
        }elseif($act == "noneed"){
            $status = "2";
            $pesan = "Dokumen telah dipilih untuk Tidak Perlu Diperbaharui.";
        }else{
            $status = null;
        }
        $query = "UPDATE M_DocumentAssetOwnership mdao
				INNER JOIN db_master.M_MerkKendaraan m_mk
					ON mdao.DAO_MK_ID=m_mk.MK_ID
				INNER JOIN M_Company co
					ON co.Company_ID = mdao.DAO_CompanyID
				INNER JOIN M_Role_Approver mra
					ON (co.Company_Area!='-' AND mra.RA_Name = CASE WHEN co.Company_Area = 'KALTIM'
						THEN CASE
							WHEN co.Company_Code IN ('EBL', 'NPN', 'DLJ')
								THEN 'ADMIN - KALTIM 1'
							WHEN co.Company_Code IN ('PTA', 'KAM', 'SAWA', 'KSD', 'HPM', 'MSL')
								THEN 'ADMIN - KALTIM 2'
							ELSE 'ADMIN - KALTIM 3'
							END
						ELSE
							CONCAT('ADMIN - ', co.Company_Area)
						END)
					AND mra.RA_Delete_Time IS NULL
				INNER JOIN M_Approver ma
					ON mra.RA_ID=ma.Approver_RoleID
                    AND ma.Approver_UserID = '$uadm'
					AND ma.Approver_Delete_Time IS NULL
                SET mdao.DAO_StatusReminderExpired = '$status'
				WHERE mdao.DAO_Delete_Time IS NULL
					AND mdao.DAO_StatusReminderExpired IS NULL
					AND (
						(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 6 DAY)=CURDATE())
					)
					OR
					(
						(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
						(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 6 DAY)=CURDATE())
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
