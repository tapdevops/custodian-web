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

$queryLegal = "SELECT tdrold.TDROLD_ID,tdrold.TDROLD_Insert_UserID UserID,mdl.DL_DocCode DocCode,
					FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrold.TDROLD_LeadTime)/7)+1) ReminderLevel,
					CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrold.TDROLD_LeadTime)/7)+1) > 3
						THEN '3'
						ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrold.TDROLD_LeadTime)/7)+1)
					END	ReminderLevel,
					throld.THROLD_ReleaseCode RelCode,DATE_FORMAT(tdrold.TDROLD_Insert_Time, '%d %M %Y') RelTime,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDROLD_Insert_UserID) SupervisorID,
					mc.Company_Name,mdc.DocumentCategory_Name,
					mdt.DocumentType_Name,mdl.DL_NoDoc,mdg.DocumentGroup_Name,
					mu.User_FullName,me.Employee_Department,me.Employee_Division,
					throld.THROLD_ReminderReturn flag, throld.THROLD_ReasonOfDocumentReturn alasan,
					CASE WHEN mdc.DocumentCategory_Name = 'Pabrik'
						THEN (SELECT mu.User_ID
							FROM M_Role_Approver mra
							INNER JOIN M_Approver ma
								ON mra.RA_ID=ma.Approver_RoleID
								AND ma.Approver_Delete_Time IS NULL
							INNER JOIN M_User mu
								ON ma.Approver_UserID=mu.User_ID
							WHERE mra.RA_Name LIKE 'MD Upstream'
								AND mra.RA_Delete_Time IS NULL)
						ELSE (SELECT mu.User_ID
							FROM M_Role_Approver mra
							INNER JOIN M_Approver ma
								ON mra.RA_ID=ma.Approver_RoleID
								AND ma.Approver_Delete_Time IS NULL
							INNER JOIN M_User mu
								ON ma.Approver_UserID=mu.User_ID
							WHERE mra.RA_Name LIKE 'MD Downstream'
								AND mra.RA_Delete_Time IS NULL)
					END MD_UserID
				FROM TD_ReleaseOfLegalDocument tdrold
				LEFT JOIN TH_ReleaseOfLegalDocument throld
					ON tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
					AND throld.THROLD_ApproveNotReturn IS NULL
					AND throld.THROLD_Delete_Time IS NULL
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
					AND tdrold.TDROLD_LeadTime NOT LIKE '%1970-01-01%'
					AND thlold.THLOLD_LoanCategoryID=1
					AND CURDATE()>=tdrold.TDROLD_LeadTime
				ORDER BY throld.THROLD_ReleaseCode";
$sqlLegal = mysql_query($queryLegal);

$legalIDs = "";

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataLegal = mysql_fetch_array($sqlLegal)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataLegal['RelCode']){
		if($tempRel['ReminderLevel'] == 1){
			$legalIDs.=$tempRel['TDROLD_ID'].",";
			mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		}elseif($tempRel['ReminderLevel'] == 2){
			mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if(($tempRel['SupervisorID'] != 0) && ($tempRel['SupervisorID'] != $tempRel['UserID'])){
				mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
		}elseif($tempRel['ReminderLevel'] == 3){
			mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if(($tempRel['SupervisorID'] != 0) && ($tempRel['SupervisorID'] != $tempRel['UserID'])){
				mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
			mail_ret_legal($tempRel['RelCode'],$tempRel['MD_UserID'],$listDoc,$tempRel,1,1);
		}
		// if($tempRel['ReminderLevel']>2){
		// 	mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
		// }
		// else{
		// 	$legalIDs.=$dataLegal['TDROLD_ID'].",";
		// 	mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		// }
		// if((int)$tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		// 	mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		// }
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
		"RelTime"=>$dataLegal['RelTime'],
		"flag"=>$dataLegal['flag'],
		"alasan"=>$dataLegal['alasan']
	]);
}
if(isset($tempRel['RelCode'])){
	if($tempRel['ReminderLevel'] == 1){
		$legalIDs.=$tempRel['TDROLD_ID'].",";
		mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
	}elseif($tempRel['ReminderLevel'] == 2){
		mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if(($tempRel['SupervisorID'] != 0) && ($tempRel['SupervisorID'] != $tempRel['UserID'])){
			mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
	}elseif($tempRel['ReminderLevel'] == 3){
		mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if(($tempRel['SupervisorID'] != 0) && ($tempRel['SupervisorID'] != $tempRel['UserID'])){
			mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		mail_ret_legal($tempRel['RelCode'],$tempRel['MD_UserID'],$listDoc,$tempRel,1,1);
	}
	// if($tempRel['ReminderLevel']>2){
	// 	mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
	// }
	// else{
	// 	$legalIDs.=$dataLegal['TDROLD_ID'].",";
	// 	mail_ret_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	// }
	// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
	// 	mail_ret_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	// }
}

$legalIDs = rtrim($legalIDs,",");

$updateUserID='cust0002';

// $queryUpdateLegal = "UPDATE TD_ReleaseOfLegalDocument SET TDROLD_LeadTime=DATE_ADD(TDROLD_LeadTime,INTERVAL 7 DAY),
// 								TDROLD_Update_Time=NOW(),TDROLD_Update_UserID='$updateUserID'
// 								WHERE TDROLD_ID IN (".$legalIDs.")";
// $sqlUpdateLegal = mysql_query($queryUpdateLegal);

?>
