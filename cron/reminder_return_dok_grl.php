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

$queryLandAcquisition = "SELECT tdrlolad.TDRLOLAD_ID,tdrlolad.TDRLOLAD_Insert_UserID UserID,thrlolad.THRLOLAD_ReleaseCode RelCode,mdla.DLA_Code DocCode,
								CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrlolad.TDRLOLAD_LeadTime)/7)+1) > 3
									THEN '3'
									ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrlolad.TDRLOLAD_LeadTime)/7)+1)
								END	ReminderLevel,
								DATE_FORMAT(tdrlolad.TDRLOLAD_Insert_Time, '%d %M %Y') RelTime,
								COALESCE(mu.User_SPV2,mu.User_SPV1,tdrlolad.TDRLOLAD_Insert_UserID) SupervisorID,
								mdla.DLA_AreaStatement,mdla.DLA_PlantTotalPrice,mdla.DLA_GrandTotal,mdla.DLA_Phase,
								mdla.DLA_Period,mdla.DLA_Village,mdla.DLA_Block,mdla.DLA_Owner,mdla.DLA_DocDate,
								mc.Company_Name,mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division,
								thrlolad.THRLOLAD_ReminderReturn flag, thrlolad.THRLOLAD_ReasonOfDocumentReturn alasan
							FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad
							LEFT JOIN TH_ReleaseOfLandAcquisitionDocument thrlolad
								ON thrlolad.THRLOLAD_ID = tdrlolad.TDRLOLAD_THRLOLAD_ID
								AND thrlolad.THRLOLAD_ApproveNotReturn IS NULL
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
							LEFT JOIN TD_ReturnOfLandAcquisitionDocument tdrtolad
								ON tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
								AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
							WHERE TDRLOLAD_Delete_Time IS NULL
								AND tdrtolad.TDRTOLAD_ID IS NULL
								AND tdrlolad.TDRLOLAD_LeadTime NOT LIKE '%1970-01-01%'
								AND thlolad.THLOLAD_LoanCategoryID=1
								AND CURDATE()>=TDRLOLAD_LeadTime
							ORDER BY thrlolad.THRLOLAD_ReleaseCode ASC";
$sqlLandAcquisition = mysql_query($queryLandAcquisition);

$landAcquisitionIDs = "";

$tempRel="";
$listDoc = null;
unset($listDoc);
$listDoc=[];
while ($dataLandAcquisition = mysql_fetch_array($sqlLandAcquisition)) {
	if(isset($tempRel['RelCode'])&&$tempRel['RelCode']!=$dataLandAcquisition['RelCode']){
		if($tempRel['ReminderLevel'] == 1){
			$landAcquisitionIDs.=$tempRel['TDRLOLAD_ID'].",";
			mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		}elseif($tempRel['ReminderLevel'] == 2){
			mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
		}elseif($tempRel['ReminderLevel'] == 3){
			mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
			if($tempRel['SupervisorID'] != $tempRel['UserID']){
				mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
			}
			mail_ret_land_acquisition($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
		}
		// mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
		// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
		// 	mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		// }
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
		"RelTime"=>$dataLandAcquisition['RelTime'],
		"flag"=>$dataLandAcquisition['flag'],
		"alasan"=>$dataLandAcquisition['alasan']
	]);
}
if(isset($tempRel['RelCode'])){
	if($tempRel['ReminderLevel'] == 1){
		$landAcquisitionIDs.=$tempRel['TDRLOLAD_ID'].",";
		mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
	}elseif($tempRel['ReminderLevel'] == 2){
		mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
	}elseif($tempRel['ReminderLevel'] == 3){
		mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel,-1);
		if($tempRel['SupervisorID'] != $tempRel['UserID']){
			mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
		}
		mail_ret_land_acquisition($tempRel['RelCode'],$MD,$listDoc,$tempRel,1,1);
	}
	// mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['UserID'],$listDoc,$tempRel);
	// if($tempRel['ReminderLevel']>1&&$tempRel['SupervisorID']!=$tempRel['UserID']){
	// 	mail_ret_land_acquisition($tempRel['RelCode'],$tempRel['SupervisorID'],$listDoc,$tempRel,1);
	// }
}

$landAcquisitionIDs = rtrim($landAcquisitionIDs,",");

$updateUserID='cust0002';

// $queryUpdateLandAcquisition = "UPDATE TD_ReleaseOfLandAcquisitionDocument SET TDRLOLAD_LeadTime=DATE_ADD(TDRLOLAD_LeadTime,INTERVAL 7 DAY),
// 								TDRLOLAD_Update_Time=NOW(),TDRLOLAD_Update_UserID='$updateUserID'
// 								WHERE TDRLOLAD_ID IN (".$landAcquisitionIDs.")";
// $sqlUpdateLandAcquisition = mysql_query($queryUpdateLandAcquisition);

?>
