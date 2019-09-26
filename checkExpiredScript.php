<!--
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Sep 2018																						=
= Update Terakhir	: 																									=
= Revisi			:																									=
=========================================================================================================================
-->

<?PHP
if(!isset($_SERVER['HTTP_HOST'])){
	$_SERVER['HTTP_HOST']='localhost/custodian';
}
include ("./config/config_db.php");
include ("./include/function.mail.expdoc.php");
include ("./include/function.mail.expdocao.php");
//include ("./include/function.mail.expdocla.php");
//include ("./include/function.mail.expdocol.php");
//include ("./include/function.mail.expdoconl.php");

$queryAssetOwnership="SELECT mdao.DAO_DocCode DocCode,mdao.DAO_NoPolisi,ma.Approver_UserID User_ID,
							mc.Company_Name,me.Employee_FullName,m_mk.MK_Name,
							DATE_FORMAT(mdao.DAO_STNK_ExpiredDate,'%d %M %Y') STNKExpTime,
							DATE_FORMAT(mdao.DAO_Pajak_ExpiredDate,'%d %M %Y') PajakExpTime
						FROM M_DocumentAssetOwnership mdao
						LEFT JOIN db_master.M_MerkKendaraan m_mk
							ON mdao.DAO_MK_ID=m_mk.MK_ID
						LEFT JOIN db_master.M_Employee me
							ON mdao.DAO_Employee_NIK=me.Employee_NIK
						INNER JOIN db_master.M_Company mc 
							ON me.Employee_CompanyCode=mc.Company_Name
							AND mc.Company_InactiveTime IS NULL
						INNER JOIN M_Role_Approver mra 
							ON (mc.Company_Region!='-' AND mra.RA_Name LIKE CONCAT('ADMIN - %',mc.Company_Region))
							AND mra.RA_Delete_Time IS NULL
						INNER JOIN M_Approver ma 
							ON mra.RA_ID=ma.Approver_RoleID
							AND ma.Approver_Delete_Time IS NULL
						WHERE mdao.DAO_Delete_Time IS NULL
							AND (
								(DATE_SUB(mdao.DAO_STNK_ExpiredDate,INTERVAL 1 MONTH)=CURDATE())
								OR 
								(DATE_SUB(mdao.DAO_Pajak_ExpiredDate,INTERVAL 1 MONTH)=CURDATE())
							)
						ORDER BY ma.Approver_UserID ASC";
$sqlAssetOwnership = mysql_query($queryAssetOwnership);

$queryLegal="SELECT mdl.DL_RegUserID User_ID,mdl.DL_DocCode DocCode,mdl.DL_NoDoc,
				DATE_FORMAT(mdl.DL_ExpDate,'%d %M %Y') ExpTime,
				mc.Company_Name,mdc.DocumentCategory_Name,mdt.DocumentType_Name
			FROM M_DocumentLegal mdl
			LEFT JOIN M_DocumentCategory mdc 
				ON mdl.DL_CategoryDocID=mdc.DocumentCategory_ID
			LEFT JOIN M_DocumentType mdt
				ON mdt.DocumentType_ID=mdl.DL_TypeDocID
			LEFT JOIN M_Company mc
				ON mc.Company_ID=mdl.DL_CompanyID
			WHERE mdl.DL_ExpDate IS NOT NULL 
				AND mdl.DL_Delete_Time IS NULL
				AND (
					(UPPER(TRIM(DocumentCategory_Name))='HGU' AND DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH)=CURDATE())
					OR
					(DATE_SUB(mdl.DL_ExpDate,INTERVAL 1 MONTH)=CURDATE())
				)
			ORDER BY mdl.DL_RegUserID ASC";
$sqlLegal = mysql_query($queryLegal);

$tempRel="";
$listDoc=[];
while ($dataLegal = mysql_fetch_array($sqlLegal)) {
	if(isset($tempRel['User_ID'])&&$tempRel['User_ID']!=$dataLegal['User_ID']){
		mail_exp_legal($tempRel['DocCode'],$tempRel['User_ID'],$listDoc);
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataLegal;
	array_push($listDoc,[
		"Company_Name"=>$dataLegal['Company_Name'],
		"DocumentCategory_Name"=>$dataLegal['DocumentCategory_Name'],
		"DocumentType_Name"=>$dataLegal['DocumentType_Name'],
		"DL_NoDoc"=>$dataLegal['DL_NoDoc'],
		"ExpTime"=>$dataLegal['ExpTime']
	]);
}
if(isset($tempRel['User_ID'])){
	mail_exp_legal($tempRel['DocCode'],$tempRel['User_ID'],$listDoc);
}

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataAssetOwnership = mysql_fetch_array($sqlAssetOwnership)) {
	if(isset($tempRel['User_ID'])&&$tempRel['User_ID']!=$dataAssetOwnership['User_ID']){
		mail_exp_asset_ownership($tempRel['DocCode'],$tempRel['User_ID'],$listDoc);
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataAssetOwnership;
	array_push($listDoc,[
		"Company_Name"=>$dataAssetOwnership['Company_Name'],
		"DAO_NoPolisi"=>$dataAssetOwnership['DAO_NoPolisi'],
		"Employee_FullName"=>$dataAssetOwnership['Employee_FullName'],
		"MK_Name"=>$dataAssetOwnership['MK_Name'],
		"STNKExpTime"=>$dataAssetOwnership['STNKExpTime'],
		"PajakExpTime"=>$dataAssetOwnership['PajakExpTime'],
	]);
}
if(isset($tempRel['User_ID'])){
	mail_exp_asset_ownership($tempRel['DocCode'],$tempRel['User_ID'],$listDoc);
}
?>