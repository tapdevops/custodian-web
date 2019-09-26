<?PHP
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 06 Juni 2012																						=
= Update Terakhir	: 06 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
include ("./config/db_sql.php");


function xlsBOF() {
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
return;
}

// Function penanda akhir file (End Of File) Excel

function xlsEOF() {
echo pack("ss", 0x0A, 0x00);
return;
}

// Function untuk menulis data (angka) ke cell excel

function xlsWriteNumber($Row, $Col, $Value) {
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
echo pack("d", $Value);
return;
}

// Function untuk menulis data (text) ke cell excel

function xlsWriteLabel($Row, $Col, $Value ) {
$L = strlen($Value);
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
echo $Value;
return;
}

function generate_excel($id){ 
$query = "SELECT  thrgolad.THRGOLAD_ID, thrgolad.THRGOLAD_RegistrationCode, u.User_FullName, d.Division_Name, 
				  dp.Department_Name, p.Position_Name, thrgolad.THRGOLAD_RegistrationDate, dg.DocumentGroup_Name,
				  (SELECT u.User_FullName
				   FROM M_Approval a, M_User u
				   WHERE a.A_TransactionCode='$id'
				   AND a.A_Step='1' 
				   AND a.A_ApproverID=u.User_ID) AS Atasan
		  FROM TH_RegistrationOfLandAcquisitionDocument thrgolad, M_User u, M_Division d, M_Department dp, M_Position p,
			   M_DivisionDepartmentPosition ddp, M_DocumentGroup dg
		  WHERE thrgolad.THRGOLAD_RegistrationCode='$id'
		  AND thrgolad.THRGOLAD_Delete_Time is NULL 
		  AND thrgolad.THRGOLAD_UserID=u.User_ID 
		  AND ddp.DDP_UserID=u.User_ID
		  AND ddp.DDP_DivID=d.Division_ID
		  AND ddp.DDP_DeptID=dp.Department_ID
		  AND ddp.DDP_PosID=p.Position_ID
		  AND dg.DocumentGroup_ID='3'";
$sql = mysql_query($query);
$arr = mysql_fetch_array($sql);
$regdate=strtotime($arr['THRGOLAD_RegistrationDate']);
$fregdate=date("j M Y", $regdate);

xlsWriteLabel(1,1,"Kode Pendaftaran");
xlsWriteLabel(1,1,"Kode Pendaftaran");

} 

?>