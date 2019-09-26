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
include ("./include/function.mail.expdoc.php");

$queryLegal="SELECT mdl.DL_RegUserID User_ID,mdl.DL_DocCode DocCode,mdl.DL_NoDoc,
				DATE_FORMAT(mdl.DL_ExpDate,'%d %M %Y') ExpTime,
				mc.Company_Name,mdc.DocumentCategory_Name,mdt.DocumentType_Name,
				'Legal/Lisensi' AS GrupDok
			FROM M_DocumentLegal mdl
			LEFT JOIN M_DocumentCategory mdc
				ON mdl.DL_CategoryDocID=mdc.DocumentCategory_ID
			LEFT JOIN M_DocumentType mdt
				ON mdt.DocumentType_ID=mdl.DL_TypeDocID
			LEFT JOIN M_Company mc
				ON mc.Company_ID=mdl.DL_CompanyID
			WHERE mdl.DL_ExpDate IS NOT NULL
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
		"DocCode"=>$dataLegal['DocCode'],
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

?>
