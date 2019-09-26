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

$queryOtherNonLegal = "SELECT tdroonld.TDROONLD_ID,
							CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroonld.TDROONLD_LeadTime)/7)+1) > 3
								THEN '3'
								ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroonld.TDROONLD_LeadTime)/7)+1)
							END	ReminderLevel,
							tdroonld.TDROONLD_Insert_UserID UserID,throonld.THROONLD_ReleaseCode RelCode,mdonl.DONL_DocCode DocCode,
							DATE_FORMAT(tdroonld.TDROONLD_Insert_Time, '%d %M %Y') RelTime,
							COALESCE(mu.User_SPV2,mu.User_SPV1,TDROONLD_Insert_UserID) SupervisorID,
							DATE_FORMAT(mdonl.DONL_TahunDokumen, '%d %M %Y') DONL_TahunDokumen,
							mdonl.DONL_NamaDokumen,mdonl.DONL_NoDokumen,mc.Company_Name,md.Department_Name,
							mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division,
							throonld.THROONLD_ReminderReturn flag, throonld.THROONLD_ReasonOfDocumentReturn alasan
						FROM TD_ReleaseOfOtherNonLegalDocuments tdroonld
						LEFT JOIN TH_ReleaseOfOtherNonLegalDocuments throonld
							ON tdroonld.TDROONLD_THROONLD_ID=throonld.THROONLD_ID
							AND throonld.THROONLD_ApproveNotReturn IS NULL
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
						LEFT JOIN M_DocumentGroup mdg
							ON mdg.DocumentGroup_ID=mdonl.DONL_GroupDocID
						LEFT JOIN M_Company mc
							ON mc.Company_ID=mdonl.DONL_CompanyID
						LEFT JOIN db_master.M_Employee me
							ON mu.User_ID = me.Employee_NIK
						LEFT JOIN db_master.M_Department md
							ON md.Department_Code=mdonl.DONL_Dept_Code
						LEFT JOIN TD_ReturnOfOtherNonLegalDocuments tdrtoonld
							ON tdloonld.TDLOONLD_DocCode=tdrtoonld.TDRTOONLD_DocCode
							AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
						WHERE TDROONLD_Delete_Time IS NULL
							AND tdrtoonld.TDRTOONLD_ID IS NULL
							AND tdroonld.TDROONLD_LeadTime NOT LIKE '%1970-01-01%'
							AND thloonld.THLOONLD_LoanCategoryID=1
							AND TDROONLD_LeadTime<=CURDATE()
						ORDER BY throonld.THROONLD_ReleaseCode ASC";
$sqlOtherNonLegal = mysql_query($queryOtherNonLegal);

$otherNonLegalIDs = "";

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataOtherNonLegal = mysql_fetch_array($sqlOtherNonLegal)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataOtherNonLegal['RelCode']){
		if($tempRel['ReminderLevel'] == 1){
			$otherNonLegalIDs.=$tempRel['TDROONLD_ID'].",";
			mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		}elseif($tempRel['ReminderLevel'] == 2){
			mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
		}elseif($tempRel['ReminderLevel'] == 3){
			mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
			mail_ret_other_non_legal($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
		}
		// mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		// 	mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		// }
		$listDoc = null;
		unset($listDoc);
		$listDoc=[];
	}
	$tempRel=$dataOtherNonLegal;
	$otherNonLegalIDs.=$dataOtherNonLegal['TDROONLD_ID'].",";
	array_push($listDoc,[
		"DocCode"=>$dataOtherNonLegal['DocCode'],
		"TDROONLD_ID"=>$dataOtherNonLegal['TDROONLD_ID'],
		"Company_Name"=>$dataOtherNonLegal['Company_Name'],
		"Department_Name"=>$dataOtherNonLegal['Department_Name'],
		"DONL_NamaDokumen"=>$dataOtherNonLegal['DONL_NamaDokumen'],
		"DONL_NoDokumen"=>$dataOtherNonLegal['DONL_NoDokumen'],
		"DONL_TahunDokumen"=>$dataOtherNonLegal['DONL_TahunDokumen'],
		"RelTime"=>$dataOtherNonLegal['RelTime'],
		"flag"=>$dataOtherNonLegal['flag'],
		"alasan"=>$dataOtherNonLegal['alasan']
	]);
}
if(isset($tempRel['RelCode'])){
	if($tempRel['ReminderLevel'] == 1){
		$otherNonLegalIDs.=$tempRel['TDROONLD_ID'].",";
		mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
	}elseif($tempRel['ReminderLevel'] == 2){
		mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
	}elseif($tempRel['ReminderLevel'] == 3){
		mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		mail_ret_other_non_legal($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
	}
	// mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
	// 	mail_ret_other_non_legal($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	// }
}

$otherNonLegalIDs = rtrim($otherNonLegalIDs,",");

$updateUserID='cust0002';

// $queryUpdateOtherNonLegal = "UPDATE TD_ReleaseOfOtherNonLegalDocuments SET TDROONLD_LeadTime=DATE_ADD(TDROONLD_LeadTime,INTERVAL 7 DAY),
// 								TDROONLD_Update_Time=NOW(),TDROONLD_Update_UserID='$updateUserID'
// 								WHERE TDROONLD_ID IN (".$otherNonLegalIDs.")";
// $sqlUpdateOtherNonLegal = mysql_query($queryUpdateOtherNonLegal);

?>
