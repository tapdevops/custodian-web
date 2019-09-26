<!--
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Sep 2018																						=
= Update Terakhir	: 																									=
= Revisi			:																									=
=========================================================================================================================
-->

<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.expdocao.php");

$queryAssetOwnership="SELECT mdao.DAO_DocCode DocCode,
							mdao.DAO_NoPolisi,
							ma.Approver_UserID User_ID,
							m_mk.MK_Name,
							CASE WHEN mdao.DAO_Employee_NIK LIKE 'CO@%'
    						  THEN
    						  	(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(mdao.DAO_Employee_NIK, 'CO@', ''))
    						  ELSE
    							(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=mdao.DAO_Employee_NIK)
    						END nama_pemilik,
							-- DATE_FORMAT(mdao.DAO_STNK_ExpiredDate,'%d %M %Y') STNKExpTime,
							CASE WHEN mdao.DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
								WHEN mdao.DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(mdao.DAO_STNK_ExpiredDate, '%d/%m/%Y')
							END AS STNKExpTime,
							-- DATE_FORMAT(mdao.DAO_Pajak_ExpiredDate,'%d %M %Y') PajakExpTime
							CASE WHEN mdao.DAO_Pajak_ExpiredDate LIKE '%0000-00-00%' THEN '-'
								WHEN mdao.DAO_Pajak_ExpiredDate LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(mdao.DAO_Pajak_ExpiredDate, '%d/%m/%Y')
							END AS PajakExpTime,
							'Kepemilikan Aset' AS GrupDok
						FROM M_DocumentAssetOwnership mdao
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
							AND ma.Approver_Delete_Time IS NULL
						WHERE mdao.DAO_Delete_Time IS NULL
							AND mdao.DAO_StatusReminderExpired IS NULL
							AND (
								(
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
								)
							)
						ORDER BY ma.Approver_UserID ASC";
$sqlAssetOwnership = mysql_query($queryAssetOwnership);

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataAssetOwnership = mysql_fetch_array($sqlAssetOwnership)) {
	if(isset($tempRel['User_ID']) && $tempRel['User_ID'] != $dataAssetOwnership['User_ID']){
		mail_exp_asset_ownership($tempRel['GrupDok'],$tempRel['User_ID'],$listDoc);
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataAssetOwnership;
	array_push($listDoc,[
		// "Company_Name"=>$dataAssetOwnership['Company_Name'],
		"DocCode"=>$dataAssetOwnership['DocCode'],
		"DAO_NoPolisi"=>$dataAssetOwnership['DAO_NoPolisi'],
		"nama_pemilik"=>$dataAssetOwnership['nama_pemilik'],
		"MK_Name"=>$dataAssetOwnership['MK_Name'],
		"STNKExpTime"=>$dataAssetOwnership['STNKExpTime'],
		"PajakExpTime"=>$dataAssetOwnership['PajakExpTime'],
	]);
}
if(isset($tempRel['User_ID'])){
	mail_exp_asset_ownership($tempRel['GrupDok'],$tempRel['User_ID'],$listDoc);
}
?>
