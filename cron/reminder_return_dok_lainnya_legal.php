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

$queryOtherLegal = "SELECT tdroold.TDROOLD_ID,
						CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroold.TDROOLD_LeadTime)/7)+1) > 3
							THEN '3'
							ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroold.TDROOLD_LeadTime)/7)+1)
						END	ReminderLevel,
						tdroold.TDROOLD_Insert_UserID UserID,throold.THROOLD_ReleaseCode RelCode,mdol.DOL_DocCode DocCode,
						DATE_FORMAT(tdroold.TDROOLD_Insert_Time, '%d %M %Y') RelTime,
						COALESCE(mu.User_SPV2,mu.User_SPV1,TDROOLD_Insert_UserID) SupervisorID,
						mdol.DOL_NamaDokumen,mdol.DOL_InstansiTerkait,mdol.DOL_NoDokumen,mdc.DocumentCategory_Name,
						DATE_FORMAT(mdol.DOL_TglTerbit, '%d %M %Y') DOL_TglTerbit,
						DATE_FORMAT(mdol.DOL_TglBerakhir, '%d %M %Y') DOL_TglBerakhir,
						mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division,
						throold.THROOLD_ReminderReturn flag, throold.THROOLD_ReasonOfDocumentReturn alasan
					FROM TD_ReleaseOfOtherLegalDocuments tdroold
					LEFT JOIN TH_ReleaseOfOtherLegalDocuments throold
						ON tdroold.TDROOLD_THROOLD_ID=throold.THROOLD_ID
						AND throold.THROOLD_ApproveNotReturn IS NULL
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
					LEFT JOIN M_DocumentCategory mdc
						ON mdc.DocumentCategory_ID=mdol.DOL_CategoryDocID
					LEFT JOIN M_DocumentGroup mdg
						ON mdg.DocumentGroup_ID=mdol.DOL_GroupDocID
					LEFT JOIN db_master.M_Employee me
						ON mu.User_ID = me.Employee_NIK
					LEFT JOIN TD_ReturnOfOtherLegalDocuments tdrtoold
						ON tdloold.TDLOOLD_DocCode=tdrtoold.TDRTOOLD_DocCode
						AND tdrtoold.TDRTOOLD_Delete_Time IS NULL
					WHERE TDROOLD_Delete_Time IS NULL
						AND tdrtoold.TDRTOOLD_ID IS NULL
						AND tdroold.TDROOLD_LeadTime NOT LIKE '%1970-01-01%'
						AND thloold.THLOOLD_LoanCategoryID=1
						AND TDROOLD_LeadTime<=CURDATE()
					ORDER BY throold.THROOLD_ReleaseCode ASC";
$sqlOtherLegal = mysql_query($queryOtherLegal);

$otherLegalIDs = "";

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataOtherLegal = mysql_fetch_array($sqlOtherLegal)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataOtherLegal['RelCode']){
		if($tempRel['ReminderLevel'] == 1){
			$otherLegalIDs.=$tempRel['TDROOLD_ID'].",";
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		}elseif($tempRel['ReminderLevel'] == 2){
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
		}elseif($tempRel['ReminderLevel'] == 3){
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
			mail_ret_other_legal($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
		}
		// if($tempRel['ReminderLevel']>2){
		// 	mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1,1);
		// }
		// else{
		// 	$otherLegalIDs.=$dataOtherLegal['TDROOLD_ID'].",";
		// 	mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		// }
		// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		// 	mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		// }
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
		"RelTime"=>$dataOtherLegal['RelTime'],
		"flag"=>$dataOtherLegal['flag'],
		"alasan"=>$dataOtherLegal['alasan']
	]);
}
if(isset($tempRel['RelCode'])){
	if($tempRel['ReminderLevel'] == 1){
		$otherLegalIDs.=$tempRel['TDROOLD_ID'].",";
		mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
	}elseif($tempRel['ReminderLevel'] == 2){
		mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
	}elseif($tempRel['ReminderLevel'] == 3){
		mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		mail_ret_other_legal($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
	}
	// mail_ret_other_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
	// 	mail_ret_other_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	// }
}

$otherLegalIDs = rtrim($otherLegalIDs,",");

$updateUserID='cust0002';

// $queryUpdateOtherLegal = "UPDATE TD_ReleaseOfOtherLegalDocuments SET TDROOLD_LeadTime=DATE_ADD(TDROOLD_LeadTime,INTERVAL 7 DAY),
// 								TDROOLD_Update_Time=NOW(),TDROOLD_Update_UserID='$updateUserID'
// 								WHERE TDROOLD_ID IN (".$otherLegalIDs.")";
// $sqlUpdateOtherLegal = mysql_query($queryUpdateOtherLegal);

?>
