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
include ("./include/function.mail.returnAllDoc.php");
// include ("./include/function.mail.retdoc.php");
// include ("./include/function.mail.retdocao.php");
// include ("./include/function.mail.retdocla.php");
// include ("./include/function.mail.retdocol.php");
// include ("./include/function.mail.retdoconl.php");

$queryAssetOwnership = "SELECT tdroaod.TDROAOD_ID,FLOOR(DATEDIFF(tdroaod.TDROAOD_LeadTime,tdroaod.TDROAOD_Insert_Time)/7) ReminderLevel,
							tdroaod.TDROAOD_Insert_UserID UserID,throaod.THROAOD_ReleaseCode RelCode,mdao.DAO_DocCode DocCode,
							DATE_FORMAT(tdroaod.TDROAOD_Insert_Time, '%d %M %Y') RelTime,
							COALESCE(mu.User_SPV2,mu.User_SPV1,TDROAOD_Insert_UserID) SupervisorID,
							DATE_FORMAT(mdao.DAO_STNK_StartDate, '%d %M %Y') DAO_STNK_StartDate,
							DATE_FORMAT(mdao.DAO_STNK_ExpiredDate, '%d %M %Y') DAO_STNK_ExpiredDate,
							mdao.DAO_NoPolisi,m_e_a.Employee_FullName OwnerName,m_mk.MK_Name VehicleBrand,
							mc.Company_Name,mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division
						FROM TD_ReleaseOfAssetOwnershipDocument tdroaod
						LEFT JOIN TH_ReleaseOfAssetOwnershipDocument throaod ON tdroaod.TDROAOD_THROAOD_ID=throaod.THROAOD_ID
							AND throaod.THROAOD_Delete_Time IS NULL
						LEFT JOIN TD_LoanOfAssetOwnershipDocument tdloaod ON tdroaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
							AND tdloaod.TDLOAOD_Delete_Time IS NULL
						INNER JOIN M_User mu
							ON tdroaod.TDROAOD_Insert_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						INNER JOIN M_DocumentAssetOwnership mdao
							ON mdao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
							AND mdao.DAO_Delete_Time IS NULL
						LEFT JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
							AND thloaod.THLOAOD_Delete_Time IS NULL
						LEFT JOIN TD_ReturnOfAssetOwnershipDocument tdrtoaod ON tdloaod.TDLOAOD_DocCode=tdrtoaod.TDRTOAOD_DocCode
							AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL
						LEFT JOIN M_Company mc
							ON mc.Company_ID=mdao.DAO_CompanyID
						LEFT JOIN M_DocumentGroup mdg
							ON mdg.DocumentGroup_ID=mdao.DAO_GroupDocID
						LEFT JOIN db_master.M_Employee me
							ON mu.User_ID = me.Employee_NIK
						LEFT JOIN db_master.M_MerkKendaraan m_mk
							ON mdao.DAO_MK_ID=m_mk.MK_ID
						LEFT JOIN db_master.M_Employee m_e_a
							ON mdao.DAO_Employee_NIK=m_e_a.Employee_NIK
						WHERE TDROAOD_Delete_Time IS NULL
							AND tdrtoaod.TDRTOAOD_ID IS NULL
							AND thloaod.THLOAOD_LoanCategoryID=1
							AND TDROAOD_LeadTime<=CURDATE()
						ORDER BY throaod.THROAOD_ReleaseCode ASC";
$sqlAssetOwnership = mysql_query($queryAssetOwnership);
$queryLandAcquisition = "SELECT tdrlolad.TDRLOLAD_ID,tdrlolad.TDRLOLAD_Insert_UserID UserID,thrlolad.THRLOLAD_ReleaseCode RelCode,mdla.DLA_Code DocCode,
								FLOOR(DATEDIFF(tdrlolad.TDRLOLAD_LeadTime,tdrlolad.TDRLOLAD_Insert_Time)/7) ReminderLevel,
								DATE_FORMAT(tdrlolad.TDRLOLAD_Insert_Time, '%d %M %Y') RelTime,
								COALESCE(mu.User_SPV2,mu.User_SPV1,tdrlolad.TDRLOLAD_Insert_UserID) SupervisorID,
								mdla.DLA_AreaStatement,mdla.DLA_PlantTotalPrice,mdla.DLA_GrandTotal,mdla.DLA_Phase,
								mdla.DLA_Period,mdla.DLA_Village,mdla.DLA_Block,mdla.DLA_Owner,mdla.DLA_DocDate,
								mc.Company_Name,mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division
							FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad
							LEFT JOIN TH_ReleaseOfLandAcquisitionDocument thrlolad ON thrlolad.THRLOLAD_ID = tdrlolad.TDRLOLAD_THRLOLAD_ID
								AND thrlolad.THRLOLAD_Delete_Time IS NULL
							LEFT JOIN TD_LoanOfLandAcquisitionDocument tdlolad ON tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
								AND tdlolad.TDLOLAD_Delete_Time IS NULL
							INNER JOIN M_User mu
								ON tdrlolad.TDRLOLAD_Insert_UserID=mu.User_ID
								AND mu.User_Delete_Time IS NULL
							INNER JOIN M_DocumentLandAcquisition mdla
								ON mdla.DLA_Code=tdlolad.TDLOLAD_DocCode
								AND mdla.DLA_Delete_Time IS NULL
							LEFT JOIN M_Company mc
								ON mc.Company_ID=mdla.DLA_CompanyID
							LEFT JOIN M_DocumentGroup mdg
								ON mdg.DocumentGroup_ID=3
							LEFT JOIN db_master.M_Employee me
								ON mu.User_ID = me.Employee_NIK
							LEFT JOIN TH_LoanOfLandAcquisitionDocument thlolad ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
								AND thlolad.THLOLAD_Delete_Time IS NULL
							LEFT JOIN TD_ReturnOfLandAcquisitionDocument tdrtolad ON tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
								AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
							WHERE TDRLOLAD_Delete_Time IS NULL
								AND tdrtolad.TDRTOLAD_ID IS NULL
								AND thlolad.THLOLAD_LoanCategoryID=1
								AND CURDATE()>=TDRLOLAD_LeadTime
							ORDER BY thrlolad.THRLOLAD_ReleaseCode ASC";
$sqlLandAcquisition = mysql_query($queryLandAcquisition);
$queryLegal = "SELECT tdrold.TDROLD_ID,tdrold.TDROLD_Insert_UserID UserID,mdl.DL_DocCode DocCode,
					FLOOR(DATEDIFF(tdrold.TDROLD_LeadTime,tdrold.TDROLD_Insert_Time)/7) ReminderLevel,
					throld.THROLD_ReleaseCode RelCode,DATE_FORMAT(tdrold.TDROLD_Insert_Time, '%d %M %Y') RelTime,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDROLD_Insert_UserID) SupervisorID,
					mc.Company_Name,mdc.DocumentCategory_Name,
					mdt.DocumentType_Name,mdl.DL_NoDoc,mdg.DocumentGroup_Name,
					mu.User_FullName,me.Employee_Department,me.Employee_Division
				FROM TH_ReleaseOfLegalDocument throld
				LEFT JOIN TD_ReleaseOfLegalDocument tdrold
					ON tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
				LEFT JOIN TH_LoanOfLegalDocument thlold
					ON thlold.THLOLD_LoanCode=throld.THROLD_THLOLD_Code
				LEFT JOIN TD_LoanOfLegalDocument tdlold
					ON tdrold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				INNER JOIN M_User mu
					ON tdrold.TDROLD_Insert_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				INNER JOIN M_DocumentLegal mdl
					ON mdl.DL_DocCode=tdlold.TDLOLD_DocCode
					AND mdl.DL_Delete_Time IS NULL
				LEFT JOIN M_Company mc
					ON mc.Company_ID=mdl.DL_CompanyID
				LEFT JOIN M_DocumentCategory mdc
					ON mdc.DocumentCategory_ID=mdl.DL_CategoryDocID
				LEFT JOIN M_DocumentType mdt
					ON mdt.DocumentType_ID=mdl.DL_TypeDocID
				LEFT JOIN M_DocumentGroup mdg
					ON mdg.DocumentGroup_ID=mdl.DL_GroupDocID
				LEFT JOIN db_master.M_Employee me
					ON mu.User_ID = me.Employee_NIK
				LEFT JOIN TD_ReturnOfLegalDocument tdrtold
					ON tdlold.TDLOLD_DocCode=tdrtold.TDRTOLD_DocCode
					AND tdrtold.TDRTOLD_Delete_Time IS NULL
				WHERE throld.THROLD_Delete_Time IS NULL
					AND tdrtold.TDRTOLD_ID IS NULL
					AND thlold.THLOLD_LoanCategoryID=1
					AND CURDATE()>=tdrold.TDROLD_LeadTime
				ORDER BY throld.THROLD_ReleaseCode
				LIMIT 0,1";
$sqlLegal = mysql_query($queryLegal);
$queryOtherLegal = "SELECT tdroold.TDROOLD_ID,FLOOR(DATEDIFF(tdroold.TDROOLD_LeadTime,tdroold.TDROOLD_Insert_Time)/7) ReminderLevel,
						tdroold.TDROOLD_Insert_UserID UserID,throold.THROOLD_ReleaseCode RelCode,mdol.DOL_DocCode DocCode,
						DATE_FORMAT(tdroold.TDROOLD_Insert_Time, '%d %M %Y') RelTime,
						COALESCE(mu.User_SPV2,mu.User_SPV1,TDROOLD_Insert_UserID) SupervisorID,
						mdol.DOL_NamaDokumen,mdol.DOL_InstansiTerkait,mdol.DOL_NoDokumen,mdc.DocumentCategory_Name,
						DATE_FORMAT(mdol.DOL_TglTerbit, '%d %M %Y') DOL_TglTerbit,
						DATE_FORMAT(mdol.DOL_TglBerakhir, '%d %M %Y') DOL_TglBerakhir,
						mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division
					FROM TD_ReleaseOfOtherLegalDocuments tdroold
					LEFT JOIN TH_ReleaseOfOtherLegalDocuments throold ON tdroold.TDROOLD_THROOLD_ID=throold.THROOLD_ID
						AND throold.THROOLD_Delete_Time IS NULL
					LEFT JOIN TD_LoanOfOtherLegalDocuments tdloold ON tdroold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
						AND tdloold.TDLOOLD_Delete_Time IS NULL
					INNER JOIN M_User mu
						ON tdroold.TDROOLD_Insert_UserID=mu.User_ID
						AND mu.User_Delete_Time IS NULL
					INNER JOIN M_DocumentsOtherLegal mdol
						ON mdol.DOL_DocCode=tdloold.TDLOOLD_DocCode
						AND mdol.DOL_Delete_Time IS NULL
					LEFT JOIN TH_LoanOfOtherLegalDocuments thloold
						ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
						AND thloold.THLOOLD_Delete_Time IS NULL
					LEFT JOIN TD_ReturnOfOtherLegalDocuments tdrtoold
						ON tdloold.TDLOOLD_DocCode=tdrtoold.TDRTOOLD_DocCode
						AND tdrtoold.TDRTOOLD_Delete_Time IS NULL
					LEFT JOIN M_DocumentCategory mdc
						ON mdc.DocumentCategory_ID=mdol.DOL_CategoryDocID
					LEFT JOIN M_DocumentGroup mdg
						ON mdg.DocumentGroup_ID=mdol.DOL_GroupDocID
					LEFT JOIN db_master.M_Employee me
						ON mu.User_ID = me.Employee_NIK
					WHERE TDROOLD_Delete_Time IS NULL
						AND tdrtoold.TDRTOOLD_ID IS NULL
						AND thloold.THLOOLD_LoanCategoryID=1
						AND TDROOLD_LeadTime<=CURDATE()
					ORDER BY throold.THROOLD_ReleaseCode ASC";
$sqlOtherLegal = mysql_query($queryOtherLegal);
$queryOtherNonLegal = "SELECT tdroonld.TDROONLD_ID,FLOOR(DATEDIFF(tdroonld.TDROONLD_LeadTime,tdroonld.TDROONLD_Insert_Time)/7) ReminderLevel,
							tdroonld.TDROONLD_Insert_UserID UserID,throonld.THROONLD_ReleaseCode RelCode,mdonl.DONL_DocCode DocCode,
							DATE_FORMAT(tdroonld.TDROONLD_Insert_Time, '%d %M %Y') RelTime,
							COALESCE(mu.User_SPV2,mu.User_SPV1,TDROONLD_Insert_UserID) SupervisorID,
							DATE_FORMAT(mdonl.DONL_TahunDokumen, '%d %M %Y') DONL_TahunDokumen,
							mdonl.DONL_NamaDokumen,mdonl.DONL_NoDokumen,mc.Company_Name,md.Department_Name,
							mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division
						FROM TD_ReleaseOfOtherNonLegalDocuments tdroonld
						LEFT JOIN TH_ReleaseOfOtherNonLegalDocuments throonld ON tdroonld.TDROONLD_THROONLD_ID=throonld.THROONLD_ID
							AND throonld.THROONLD_Delete_Time IS NULL
						LEFT JOIN TD_LoanOfOtherNonLegalDocuments tdloonld ON tdroonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
							AND tdloonld.TDLOONLD_Delete_Time IS NULL
						INNER JOIN M_User mu ON tdroonld.TDROONLD_Insert_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						INNER JOIN M_DocumentsOtherNonLegal mdonl
							ON mdonl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
							AND mdonl.DONL_Delete_Time IS NULL
						LEFT JOIN TH_LoanOfOtherNonLegalDocuments thloonld
							ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
							AND thloonld.THLOONLD_Delete_Time IS NULL
						LEFT JOIN TD_ReturnOfOtherNonLegalDocuments tdrtoonld
							ON tdloonld.TDLOONLD_DocCode=tdrtoonld.TDRTOONLD_DocCode
							AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
						LEFT JOIN M_DocumentGroup mdg
							ON mdg.DocumentGroup_ID=mdonl.DONL_GroupDocID
						LEFT JOIN M_Company mc
							ON mc.Company_ID=mdonl.DONL_CompanyID
						LEFT JOIN db_master.M_Employee me
							ON mu.User_ID = me.Employee_NIK
						LEFT JOIN db_master.M_Department md
							ON md.Department_Code=mdonl.DONL_Dept_Code
						WHERE TDROONLD_Delete_Time IS NULL
							AND tdrtoonld.TDRTOONLD_ID IS NULL
							AND thloonld.THLOONLD_LoanCategoryID=1
							AND TDROONLD_LeadTime<=CURDATE()
						ORDER BY throonld.THROONLD_ReleaseCode ASC";
$sqlOtherNonLegal = mysql_query($queryOtherNonLegal);

$assetOwnershipIDs = "";
$landAcquisitionIDs = "";
$legalIDs = "";
$otherLegalIDs = "";
$otherNonLegalIDs = "";

$tempRel="";
$listDoc=[];
while ($dataAssetOwnership = mysql_fetch_array($sqlAssetOwnership)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataAssetOwnership['RelCode']){
		if($tempRel['ReminderLevel']>2){
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
		}
		else{
			$assetOwnershipIDs.=$tempRel['TDROAOD_ID'].",";
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		}
		if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataAssetOwnership;
	array_push($listDoc,[
		"DocCode"=>$dataAssetOwnership['DocCode'],
		"TDROAOD_ID"=>$dataAssetOwnership['TDROAOD_ID'],
		"Company_Name"=>$dataAssetOwnership['Company_Name'],
		"DAO_NoPolisi"=>$dataAssetOwnership['DAO_NoPolisi'],
		"OwnerName"=>$dataAssetOwnership['OwnerName'],
		"VehicleBrand"=>$dataAssetOwnership['VehicleBrand'],
		"DAO_STNK_StartDate"=>$dataAssetOwnership['DAO_STNK_StartDate'],
		"DAO_STNK_ExpiredDate"=>$dataAssetOwnership['DAO_STNK_ExpiredDate'],
		"RelTime"=>$dataAssetOwnership['RelTime']
	]);
}
if(isset($tempRel['RelCode'])){
	if($tempRel['ReminderLevel']>2){
		mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
	}
	else{
		$assetOwnershipIDs.=$tempRel['TDROAOD_ID'].",";
		mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	}
	if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	}
}

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataLandAcquisition = mysql_fetch_array($sqlLandAcquisition)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataLandAcquisition['RelCode']){
		mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
			mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataLandAcquisition;
	$landAcquisitionIDs.=$dataLandAcquisition['TDRLOLAD_ID'].",";
	array_push($listDoc,[
		"DocCode"=>$dataLandAcquisition['DocCode'],
		"TDRLOLAD_ID"=>$dataLandAcquisition['TDRLOLAD_ID'],
		"Company_Name"=>$dataLandAcquisition['Company_Name'],
		"DLA_Phase"=>$dataLandAcquisition['DLA_Phase'],
		"DLA_Period"=>$dataLandAcquisition['DLA_Period'],
		"DLA_AreaStatement"=>$dataLandAcquisition['DLA_AreaStatement'],
		"DLA_PlantTotalPrice"=>$dataLandAcquisition['DLA_PlantTotalPrice'],
		"DLA_GrandTotal"=>$dataLandAcquisition['DLA_GrandTotal'],
		"DLA_Village"=>$dataLandAcquisition['DLA_Village'],
		"DLA_Block"=>$dataLandAcquisition['DLA_Block'],
		"DLA_Owner"=>$dataLandAcquisition['DLA_Owner'],
		"RelTime"=>$dataLandAcquisition['RelTime']
	]);
}
if(isset($tempRel['RelCode'])){
	mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	}
}

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataLegal = mysql_fetch_array($sqlLegal)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataLegal['RelCode']){
		if($tempRel['ReminderLevel']>2){
			mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
		}
		else{
			$legalIDs.=$dataLegal['TDROLD_ID'].",";
			mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		}
		if((int)$tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
			mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataLegal;
	array_push($listDoc,[
		"DocCode"=>$dataLegal['DocCode'],
		"TDROLD_ID"=>$dataLegal['TDROLD_ID'],
		"Company_Name"=>$dataLegal['Company_Name'],
		"DocumentCategory_Name"=>$dataLegal['DocumentCategory_Name'],
		"DocumentType_Name"=>$dataLegal['DocumentType_Name'],
		"DL_NoDoc"=>$dataLegal['DL_NoDoc'],
		"RelTime"=>$dataLegal['RelTime']
	]);
}
if(isset($tempRel['RelCode'])){
	if($tempRel['ReminderLevel']>2){
		mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
	}
	else{
		$legalIDs.=$dataLegal['TDROLD_ID'].",";
		mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	}
	if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	}
}

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataOtherLegal = mysql_fetch_array($sqlOtherLegal)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataOtherLegal['RelCode']){
		if($tempRel['ReminderLevel']>2){
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
		}
		else{
			$otherLegalIDs.=$dataOtherLegal['TDROOLD_ID'].",";
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		}
		if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataOtherLegal;
	array_push($listDoc,[
		"DocCode"=>$dataOtherLegal['DocCode'],
		"TDROOLD_ID"=>$dataOtherLegal['TDROOLD_ID'],
		// "Company_Name"=>$dataOtherLegal['Company_Name'],
		"DocumentCategory_Name"=>$dataOtherLegal['DocumentCategory_Name'],
		"DOL_NamaDokumen"=>$dataOtherLegal['DOL_NamaDokumen'],
		"DOL_InstansiTerkait"=>$dataOtherLegal['DOL_InstansiTerkait'],
		"DOL_NoDokumen"=>$dataOtherLegal['DOL_NoDokumen'],
		"DOL_TglTerbit"=>$dataOtherLegal['DOL_TglTerbit'],
		"DOL_TglBerakhir"=>$dataOtherLegal['DOL_TglBerakhir'],
		"RelTime"=>$dataOtherLegal['RelTime']
	]);
}
if(isset($tempRel['RelCode'])){
	mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	}
}

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataOtherNonLegal = mysql_fetch_array($sqlOtherNonLegal)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataOtherNonLegal['RelCode']){
		mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataOtherLegal;
	$otherNonLegalIDs.=$dataOtherLegal['TDROOLD_ID'].",";
	array_push($listDoc,[
		"DocCode"=>$dataOtherLegal['DocCode'],
		"TDROOLD_ID"=>$dataOtherLegal['TDROOLD_ID'],
		"Company_Name"=>$dataOtherLegal['Company_Name'],
		"Department_Name"=>$dataOtherLegal['Department_Name'],
		"DONL_NamaDokumen"=>$dataOtherLegal['DONL_NamaDokumen'],
		"DONL_NoDokumen"=>$dataOtherLegal['DONL_NoDokumen'],
		"DONL_TahunDokumen"=>$dataOtherLegal['DONL_TahunDokumen'],
		"RelTime"=>$dataOtherLegal['RelTime']
	]);
}
if(isset($tempRel['RelCode'])){
	mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	}
}

$assetOwnershipIDs = rtrim($assetOwnershipIDs,",");
$landAcquisitionIDs = rtrim($landAcquisitionIDs,",");
$legalIDs = rtrim($legalIDs,",");
$otherLegalIDs = rtrim($otherLegalIDs,",");
$otherNonLegalIDs = rtrim($otherNonLegalIDs,",");

$updateUserID='cust0002';
$queryUpdateAssetOwnership = "UPDATE TD_ReleaseOfAssetOwnershipDocument SET TDROAOD_LeadTime=DATE_ADD(TDROAOD_LeadTime,INTERVAL 7 DAY),
								TDROAOD_Update_Time=NOW(),TDROAOD_Update_UserID='$updateUserID'
								WHERE TDROAOD_ID IN (".$assetOwnershipIDs.")";
$sqlUpdateAssetOwnership = mysql_query($queryUpdateAssetOwnership);
$queryUpdateLandAcquisition = "UPDATE TD_ReleaseOfLandAcquisitionDocument SET TDRLOLAD_LeadTime=DATE_ADD(TDRLOLAD_LeadTime,INTERVAL 7 DAY),
								TDRLOLAD_Update_Time=NOW(),TDRLOLAD_Update_UserID='$updateUserID'
								WHERE TDRLOLAD_ID IN (".$landAcquisitionIDs.")";
$sqlUpdateLandAcquisition = mysql_query($queryUpdateLandAcquisition);
$queryUpdateLegal = "UPDATE TD_ReleaseOfLegalDocument SET TDROLD_LeadTime=DATE_ADD(TDROLD_LeadTime,INTERVAL 7 DAY),
								TDROLD_Update_Time=NOW(),TDROLD_Update_UserID='$updateUserID'
								WHERE TDROLD_ID IN (".$legalIDs.")";
$sqlUpdateLegal = mysql_query($queryUpdateLegal);
$queryUpdateOtherLegal = "UPDATE TD_ReleaseOfOtherLegalDocuments SET TDROOLD_LeadTime=DATE_ADD(TDROOLD_LeadTime,INTERVAL 7 DAY),
								TDROOLD_Update_Time=NOW(),TDROOLD_Update_UserID='$updateUserID'
								WHERE TDROOLD_ID IN (".$otherLegalIDs.")";
$sqlUpdateOtherLegal = mysql_query($queryUpdateOtherLegal);
$queryUpdateOtherNonLegal = "UPDATE TD_ReleaseOfOtherNonLegalDocuments SET TDROONLD_LeadTime=DATE_ADD(TDROONLD_LeadTime,INTERVAL 7 DAY),
								TDROONLD_Update_Time=NOW(),TDROONLD_Update_UserID='$updateUserID'
								WHERE TDROONLD_ID IN (".$otherNonLegalIDs.")";
$sqlUpdateOtherNonLegal = mysql_query($queryUpdateOtherNonLegal);
?>
