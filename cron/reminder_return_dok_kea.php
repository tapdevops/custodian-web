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
include ("./include/function.mail.returnAllDoc.php");

$query_get_MD = "SELECT mu.User_ID, mu.User_Name
	FROM M_Role_Approver mra
	INNER JOIN M_Approver ma
		ON mra.RA_ID=ma.Approver_RoleID
		AND ma.Approver_Delete_Time IS NULL
	INNER JOIN M_User mu
		ON ma.Approver_UserID=mu.User_ID
	WHERE mra.RA_Name LIKE 'MD Downstream'
		AND mra.RA_Delete_Time IS NULL";
$sql_get_MD = mysql_query($query_get_MD);
$d_MD = mysql_fetch_array($sql_get_MD);
$MD = $d_MD['User_ID'];

$queryAssetOwnership = "SELECT tdroaod.TDROAOD_ID,
							CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroaod.TDROAOD_LeadTime)/7)+1) > 3
								THEN '3'
								ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroaod.TDROAOD_LeadTime)/7)+1)
							END	ReminderLevel,
							tdroaod.TDROAOD_Insert_UserID UserID, throaod.THROAOD_ReleaseCode RelCode,
							mdao.DAO_DocCode DocCode,
							DATE_FORMAT(tdroaod.TDROAOD_Insert_Time, '%d %M %Y') RelTime,
							COALESCE(mu.User_SPV2, mu.User_SPV1, TDROAOD_Insert_UserID) SupervisorID,
							DATE_FORMAT(mdao.DAO_STNK_StartDate, '%d %M %Y') DAO_STNK_StartDate,
							DATE_FORMAT(mdao.DAO_STNK_ExpiredDate, '%d %M %Y') DAO_STNK_ExpiredDate,
							mdao.DAO_NoPolisi,
							CASE WHEN mdao.DAO_Employee_NIK LIKE 'CO@%'
		  					  THEN
		  						(SELECT mc_on.Company_Name FROM M_Company mc_on WHERE mc_on.Company_code = REPLACE(mdao.DAO_Employee_NIK, 'CO@', ''))
		  					  ELSE
		  						(SELECT me_on.Employee_FullName FROM db_master.M_Employee me_on WHERE me_on.Employee_NIK=mdao.DAO_Employee_NIK)
		  				  	END OwnerName,
							m_mk.MK_Name VehicleBrand,
							mdg.DocumentGroup_Name,
							mu.User_FullName,
							me.Employee_Department,
							me.Employee_Division,
							throaod.THROAOD_ReminderReturn flag, throaod.THROAOD_ReasonOfDocumentReturn alasan
						FROM TD_ReleaseOfAssetOwnershipDocument tdroaod
						LEFT JOIN TH_ReleaseOfAssetOwnershipDocument throaod
							ON tdroaod.TDROAOD_THROAOD_ID=throaod.THROAOD_ID
							AND throaod.THROAOD_ApproveNotReturn IS NULL
							AND throaod.THROAOD_Delete_Time IS NULL
						LEFT JOIN TD_LoanOfAssetOwnershipDocument tdloaod ON tdroaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
							AND tdloaod.TDLOAOD_Delete_Time IS NULL
						INNER JOIN M_User mu
							ON tdloaod.TDLOAOD_Insert_UserID=mu.User_ID
							AND mu.User_Delete_Time IS NULL
						INNER JOIN M_DocumentAssetOwnership mdao
							ON mdao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
							AND mdao.DAO_Delete_Time IS NULL
						LEFT JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
							AND thloaod.THLOAOD_Delete_Time IS NULL
						LEFT JOIN M_DocumentGroup mdg
							ON mdg.DocumentGroup_ID=mdao.DAO_GroupDocID
						LEFT JOIN db_master.M_Employee me
							ON mu.User_ID = me.Employee_NIK
						LEFT JOIN db_master.M_MerkKendaraan m_mk
							ON mdao.DAO_MK_ID=m_mk.MK_ID
						LEFT JOIN TD_ReturnOfAssetOwnershipDocument tdrtoaod
							ON tdloaod.TDLOAOD_DocCode=tdrtoaod.TDRTOAOD_DocCode
							AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL
						WHERE tdroaod.TDROAOD_Delete_Time IS NULL
		                    AND tdrtoaod.TDRTOAOD_ID IS NULL
							AND tdroaod.TDROAOD_LeadTime NOT LIKE '%1970-01-01%'
							AND thloaod.THLOAOD_LoanCategoryID=1
							AND tdroaod.TDROAOD_LeadTime<=CURDATE()
						ORDER BY throaod.THROAOD_ReleaseCode ASC";
$sqlAssetOwnership = mysql_query($queryAssetOwnership);

$assetOwnershipIDs = "";

$tempRel="";
$listDoc=[];
while ($dataAssetOwnership = mysql_fetch_array($sqlAssetOwnership)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataAssetOwnership['RelCode']){
		if($tempRel['ReminderLevel'] == 1){
			$assetOwnershipIDs.=$tempRel['TDROAOD_ID'].",";
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		}elseif($tempRel['ReminderLevel'] == 2){
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
		}elseif($tempRel['ReminderLevel'] == 3){
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
			mail_ret_asset_ownership($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
		}

		// if($tempRel['ReminderLevel']>2){
		// 	mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
		// }
		// else{
		// 	$assetOwnershipIDs.=$tempRel['TDROAOD_ID'].",";
		// 	mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		// }
		// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		// 	mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		// }
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataAssetOwnership;
	array_push($listDoc,[
		"DocCode"=>$dataAssetOwnership['DocCode'],
		"TDROAOD_ID"=>$dataAssetOwnership['TDROAOD_ID'],
		// "Company_Name"=>$dataAssetOwnership['Company_Name'],
		"DAO_NoPolisi"=>$dataAssetOwnership['DAO_NoPolisi'],
		"OwnerName"=>$dataAssetOwnership['OwnerName'],
		"VehicleBrand"=>$dataAssetOwnership['VehicleBrand'],
		"DAO_STNK_StartDate"=>$dataAssetOwnership['DAO_STNK_StartDate'],
		"DAO_STNK_ExpiredDate"=>$dataAssetOwnership['DAO_STNK_ExpiredDate'],
		"RelTime"=>$dataAssetOwnership['RelTime'],
		"flag"=>$dataAssetOwnership['flag'],
		"alasan"=>$dataAssetOwnership['alasan']
	]);
}
if(isset($tempRel['RelCode'])){
	if($tempRel['ReminderLevel'] == 1){
		$assetOwnershipIDs.=$tempRel['TDROAOD_ID'].",";
		mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
	}elseif($tempRel['ReminderLevel'] == 2){
		mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
	}elseif($tempRel['ReminderLevel'] == 3){
		mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		mail_ret_asset_ownership($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
	}
	// if($tempRel['ReminderLevel']>2){
	// 	mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
	// }
	// else{
	// 	$assetOwnershipIDs.=$tempRel['TDROAOD_ID'].",";
	// 	mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	// }
	// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
	// 	mail_ret_asset_ownership($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	// }
}

$assetOwnershipIDs = rtrim($assetOwnershipIDs,",");

// $updateUserID='cust0002';

// $queryUpdateAssetOwnership = "UPDATE TD_ReleaseOfAssetOwnershipDocument SET TDROAOD_LeadTime=DATE_ADD(TDROAOD_LeadTime,INTERVAL 7 DAY),
// 								TDROAOD_Update_Time=NOW(),TDROAOD_Update_UserID='$updateUserID'
// 								WHERE TDROAOD_ID IN (".$assetOwnershipIDs.")";
// $sqlUpdateAssetOwnership = mysql_query($queryUpdateAssetOwnership);

?>
