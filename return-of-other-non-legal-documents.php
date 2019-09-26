<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource               																			=
= Dibuat Tanggal	: 26 September 2018																					=
= Update Terakhir	: -           																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Pengembalian Dokumen Lainnya (Di Luar Legal)</title>
<head>
<?PHP include ("./config/config_db.php");
include ("./include/function.mail.retdoconl.php"); ?>

<script language="JavaScript" type="text/JavaScript">
function showList(n) {
	var docGrup="6"; //Grup Dokumen Lainnya (Legal)
	var txtKe = n;
	sList = window.open("popupRelease.php?gID="+docGrup+"&txtKe="+txtKe+"", "Daftar_Pengeluaran_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
}
// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var jrow = document.getElementById('countRow').value;

	for (i = 1; i <= jrow; i++){
		var txtTDRTOONLD_DocCode = document.getElementById('txtTDRTOONLD_DocCode' + i).value;
		var checkDocCode = 0;
		txtTDRTOONLD_DocCode=txtTDRTOONLD_DocCode.replace("\n","");

		if (txtTDRTOONLD_DocCode.replace(" ", "") == "")  {
			alert("Kode Dokumen pada baris ke-" + i + " Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 				$query = "SELECT *
				  		  FROM TD_ReleaseOfOtherNonLegalDocuments tdrloonld, TD_LoanOfOtherNonLegalDocuments tdloonld, M_DocumentsOtherNonLegal donl
				  		  WHERE tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
				  		  AND donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
						  AND donl.DONL_Status='4'
			   			  AND tdrloonld.TDROONLD_ReturnCode='0'";
 				$result = mysql_query($query);
				while ($data = mysql_fetch_array($result)) {
					$TDLOONLD_DocCode = $data['TDLOONLD_DocCode'];
					$a = "if (txtTDRTOONLD_DocCode == '$TDLOONLD_DocCode') {";
					$a .= "checkDocCode = 1; ";
					$a .= "}";
				echo $a;
		 		}
			?>
			if (checkDocCode == 0) {
				alert("Kode Dokumen Yang Dikembalikan pada baris ke-" + i + " SALAH!");
				returnValue = false;
			}
		}
	}
	return returnValue;
}
</script>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	//Menambah Header / Dokumen Baru
	if($act=='add') {
		$ActionContent ="
		<form name='add-detaildoc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengembalian Dokumen Lainnya (Di Luar Legal)</th>";

		$query1="SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						p.Position_Name as PosName,u.User_SPV1,u.User_SPV2
				 FROM M_User u
				 LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				 LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				 LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				 LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				 WHERE u.User_ID='$mv_UserID'";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);

		$jenis = "22"; //Semua Dokumen

		$queryApprover = "
			SELECT ma.Approver_UserID, rads.RADS_StepID, rads.RADS_RA_ID, ra.RA_Name
			FROM M_Role_ApproverDocStepStatus rads
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			LEFT JOIN M_Approver ma
				ON ra.RA_ID = ma.Approver_RoleID
			WHERE rads.RADS_DocID = '$jenis'
				AND rads.RADS_ProsesID = '4'
				AND ma.Approver_Delete_Time IS NULL
				ORDER BY rads.RADS_StepID
		";
		$sqlApprover=mysql_query($queryApprover);
		while($d = mysql_fetch_array($sqlApprover)){
			$approvers[] = $d['Approver_UserID'];  //Approval Untuk ke Custodian
		}

		$ActionContent .="
		<tr>
			<td width='30%'>Nama</td>
			<td>$field1[FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field1[DivName]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field1[DeptName]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field1[PosName]</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>

		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th>Kode Dokumen</th>
			<th>Keterangan Pengembalian</th>
		</tr>
		<tr>
			<td>
				<textarea name='txtTDRTOONLD_DocCode1' id='txtTDRTOONLD_DocCode1' cols='20' rows='1' readonly='readonly' onClick='javascript:showList(1);'></textarea>
			</td>
			<td>
				<textarea name='txtTDRTOONLD_Information1' id='txtTDRTOONLD_Information1' cols='20' rows='1'></textarea>
			</td>
		</tr>
		</table>

		<table width='100%'>
			<tr>
				<td>";
				foreach($approvers as $approver){
					$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$approver' readonly='true' class='readonly'/>";
				}
				$ActionContent .="</td>
			</tr>
			<tr>
				<th  class='bg-white'>
					<input onclick='addRowToTable();' type='button' class='addrow'/>
					<input onclick='removeRowFromTable();' type='button' class='deleterow'/>
					<input type='hidden' value='1' id='countRow' name='countRow' />
				</th>
			</tr>
		</table>

		<table width='100%'>
		<th>
			<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/>
			<input name='cancel' type='submit' value='Batal' class='button'/>
		</th>
		</table>

		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}

	if($act=='detail') {
		$id=$_GET['id'];
		$do=$_GET["do"];

		// Cek apakah user berikut memiliki hak untuk approval
		$cApp_query="SELECT DISTINCT dra.A_ApproverID
					 FROM TD_ReturnOfOtherNonLegalDocuments tdrtoold, M_Approval dra
					 WHERE tdrtoold.TDRTOONLD_Delete_Time is NULL
					 AND dra.A_ApproverID='$mv_UserID'
					 AND dra.A_Status='2'
					 AND dra.A_TransactionCode=tdrtoold.TDRTOONLD_ReturnCode
					 AND tdrtoold.TDRTOONLD_ReturnCode='$id'";
					 // echo $cApp_query;
		$approver=mysql_num_rows(mysql_query($cApp_query));
		$appQuery=(($do=='approve')&&($approver=="1"))?"AND m_app.A_ApproverID='$mv_UserID'":"AND m_app.A_Status='2'";

		$query1 = "SELECT  tdrtoonld.TDRTOONLD_ReturnCode, u.User_FullName, d.Division_Name, dp.Department_Name,
		    			   p.Position_Name, tdrtoonld.TDRTOONLD_ReturnTime, u.User_ID,
						   tdrtoonld.TDRTOONLD_Status, drs.DRS_Description, tdrtoonld.TDRTOONLD_Reason,
                           (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=m_app.A_ApproverID) waitingApproval
			   	   FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld
				   LEFT JOIN M_User u
						ON tdrtoonld.TDRTOONLD_UserID=u.User_ID
				   LEFT JOIN M_DivisionDepartmentPosition ddp
						ON u.User_ID=ddp.DDP_UserID
						AND ddp.DDP_Delete_Time is NULL
				   LEFT JOIN M_Division d
						ON ddp.DDP_DivID=d.Division_ID
				   LEFT JOIN M_Department dp
						ON ddp.DDP_DeptID=dp.Department_ID
				   LEFT JOIN M_Position p
						ON ddp.DDP_PosID=p.Position_ID
				   LEFT JOIN M_Approval m_app
          				ON tdrtoonld.TDRTOONLD_ReturnCode=m_app.A_TransactionCode
                        $appQuery
                   LEFT JOIN M_DocumentRegistrationStatus drs
    			        ON tdrtoonld.TDRTOONLD_Status=drs.DRS_Name
			       WHERE tdrtoonld.TDRTOONLD_ReturnCode='$id'";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);
		$fregdate=date('j M Y', strtotime($field1[TDRTOONLD_ReturnTime]));

		$ActionContent ="
		<form name='app-doc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>";
		if(($do=='approve')&&($approver=="1"))
        	$ActionContent .="<th colspan=3>Persetujuan Pengembalian Dokumen Lainnya (Di Luar Legal)</th>";
        else
            $ActionContent .="<th colspan=3>Pengembalian Dokumen Lainnya (Di Luar Legal)</th>";
		$ActionContent .="<tr>
			<td width='30%'>No Pengembalian</td>
			<td width='67%'>
				<input type='hidden' name='txtTDRTOONLD_ReturnCode' id='txtTDRTOONLD_ReturnCode' value='$field1[TDRTOONLD_ReturnCode]'>
				$field1[TDRTOONLD_ReturnCode]
			</td>
			<td width='3%'><a href='print-return-of-other-non-legal-documents.php?id=$field1[TDRTOONLD_ReturnCode]' target='_blank'><img src='./images/icon-print.png'></a>
			</td>
		</tr>
		<tr>
			<td>Tanggal Pengembalian</td>
			<td colspan='2'>$fregdate</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td colspan='2'>
				<input type='hidden' name='txtUser_ID' id='txtUser_ID' value='$field1[User_ID]'>
				$field1[User_FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td colspan='2'>$field1[Division_Name]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td colspan='2'>$field1[Department_Name]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td colspan='2'>$field1[Position_Name]</td>
		</tr>";

		if(($do=='approve')&&($approver=="1")) {
			$ActionContent .="
			<tr>
				<td>Persetujuan</td>
				<td colspan='2'>
					<select name='optTDRTOONLD_Status' id='optTDRTOONLD_Status'>
						<option value='0'>--- Menunggu Persetujuan ---</option>";
							$q_drs="SELECT *
										FROM M_DocumentRegistrationStatus
										WHERE (DRS_Name <> '' AND DRS_Name <> 'waiting')
										AND DRS_Delete_Time is NULL";
							$s_drs = mysql_query($q_drs);
							while ($f_drs=mysql_fetch_array($s_drs)) {
								if ($f_drs['DRS_ID']==3)
									$ActionContent .="<option value='$f_drs[DRS_ID]'>Setuju</option>";
								else if ($f_drs['DRS_ID']==4)
									$ActionContent .="<option value='$f_drs[DRS_ID]'>Tolak</option>";
							}
			$ActionContent .="
					</select>
				</td>
			</tr>
			<tr>
				<td>Keterangan Persetujuan</td>
				<td colspan='2'>
					<textarea name='txtTDRTOONLD_Reason' id='txtTDRTOONLD_Reason' cols='50' rows='2'>$arr[TDRTOONLD_Reason]</textarea>
					<br>*Wajib Diisi Apabila Dokumen Ditolak.
				</td>
			</tr>";
		}else {
			$ActionContent .="<tr>
				<td>Status Dokumen</td>";

			if($field1['TDRTOONLD_Status']=="waiting") {
				$ActionContent .="
					  <td colspan='2'>Menunggu Persetujuan $field1[waitingApproval]</td>
				</tr>";
			}else if($field1['TDRTOONLD_Status']=="accept") {
				$ActionContent .="
					<td colspan='2'>Disetujui</td>
				</tr>";
			}else if($field1['TDRTOONLD_Status']=="reject") {
				$ActionContent .="
					<td colspan='2'>Ditolak</td>
				</tr>
				<tr>
					<td>Alasan</td>
					<td colspan='2'>$field1[TDRTOONLD_Reason]</td>
				</tr>";
			}else {
				$ActionContent .="
					  <td colspan='2'>Draft</td>
				</tr>";
			}
		}
		if(($do=='approve')&&($approver=="1")) {
			$ActionContent .="
			<th colspan=11>
				<input name='approval' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
				<input name='cancel' type='submit' value='Batal' class='button'/>
			</th>";
		}
		$ActionContent .="
		</table>
		</form>

		<div class='detail-title'>Daftar Dokumen</div>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th>Kode Dokumen</th>
			<th>Perusahaan</th>
			<th>No. Dokumen</th>
			<th>Nama Dokumen</th>
			<th>Tahun Dokumen</th>
			<th>Departemen</th>
			<th>Ket. Pengembalian</th>
		</tr>";

		$queryd = "SELECT donl.DONL_DocCode, c.Company_Name,
						  donl.DONL_ID, tdronld.TDRTOONLD_Information,  m_d.Department_Name,
						  donl.DONL_NoDokumen, donl.DONL_NamaDokumen, donl.DONL_TahunDokumen
					FROM TD_ReturnOfOtherNonLegalDocuments tdronld,
					 	 M_DocumentsOtherNonLegal donl, M_Company c,
						 db_master.M_Department m_d
					WHERE tdronld.TDRTOONLD_ReturnCode='$id'
					AND tdronld.TDRTOONLD_Delete_Time IS NULL
					AND tdronld.TDRTOONLD_DocCode=donl.DONL_DocCode
					AND donl.DONL_CompanyID=c.Company_ID
					AND donl.DONL_Dept_Code=m_d.Department_Code";
		$sqld = mysql_query($queryd);
		while ($arrd = mysql_fetch_array($sqld)) {
			$ActionContent .="
			<tr>
				<td align='center'>$arrd[DONL_DocCode]</td>
				<td class='center'>$arrd[Company_Name]</td>
				<td class='center'>$arrd[DONL_NoDokumen]</td>
				<td class='center'>$arrd[DONL_NamaDokumen]</td>
				<td class='center'>$arrd[DONL_TahunDokumen]</td>
				<td class='center'>$arrd[Department_Name]</td>
				<td align='center'><pre>$arrd[TDRTOONLD_Information]</pre></td>
			</tr>";
		}
		$ActionContent .="
		</table>";
	}

	//Kirim Ulang Email Persetujuan
	if($act=='resend'){
		mail_return_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=return-of-other-non-legal-documents.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT DISTINCT tdrtoonld.TDRTOONLD_ID, tdrtoonld.TDRTOONLD_ReturnCode, tdrtoonld.TDRTOONLD_ReturnTime, u.User_FullName,
            drs.DRS_Description, tdrtoonld.TDRTOONLD_Status
		  FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld
		  LEFT JOIN M_User u
            ON tdrtoonld.TDRTOONLD_UserID=u.User_ID
          LEFT JOIN M_DocumentRegistrationStatus drs
            ON tdrtoonld.TDRTOONLD_Status=drs.DRS_Name
		  WHERE tdrtoonld.TDRTOONLD_Delete_Time is NULL
		  	AND u.User_ID='$mv_UserID'
		  GROUP BY tdrtoonld.TDRTOONLD_ReturnCode
		  ORDER BY tdrtoonld.TDRTOONLD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th>Kode Pengembalian</th>
	<th>Tanggal Pengembalian</th>
	<th>Nama Penerima Dokumen</th>
	<th>Status</th>
	<th></th>
</tr>";
if ($num==NULL) {
	$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$fregdate=date("j M Y", strtotime($field['TDRTOONLD_ReturnTime']));
		$resend=($field['TDRTOONLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='$PHP_SELF?act=detail&id=$field[1]' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="
	</table>
";

$query1 ="SELECT DISTINCT tdrtoonld.TDRTOONLD_ID, tdrtoonld.TDRTOONLD_ReturnCode, tdrtoonld.TDRTOONLD_ReturnTime, u.User_FullName,
            drs.DRS_Description, tdrtoonld.TDRTOONLD_Status
		  FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld
		  LEFT JOIN M_User u
            ON tdrtoonld.TDRTOONLD_UserID=u.User_ID
          LEFT JOIN M_DocumentRegistrationStatus drs
            ON tdrtoonld.TDRTOONLD_Status=drs.DRS_Name
		  WHERE tdrtoonld.TDRTOONLD_Delete_Time is NULL
		  	AND u.User_ID='$mv_UserID'";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1)
	$Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
    	if (($showPage == 1) && ($p != 2))
			$Pager.="...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			$Pager.="...";
        if ($p == $noPage)
			$Pager.="<b><u>$p</b></u> ";
        else
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";

		$showPage = $p;
	}
}

if ($noPage < $jumPage)
	$Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";

/* ACTIONS */
if(isset($_POST['cancel'])) {
	echo "<meta http-equiv='refresh' content='0; url=return-of-other-non-legal-documents.php'>";
}

elseif(isset($_POST['adddetail'])) {
	$regyear=date("Y");
	$rmonth=date("n");

	// Mengubah Bulan ke Romawi
	switch ($rmonth)	{
		case 1: $regmonth="I"; break;
		case 2: $regmonth="II"; break;
		case 3: $regmonth="III"; break;
		case 4: $regmonth="IV"; break;
		case 5: $regmonth="V"; break;
		case 6: $regmonth="VI"; break;
		case 7: $regmonth="VII"; break;
		case 8: $regmonth="VIII"; break;
		case 9: $regmonth="IX"; break;
		case 10: $regmonth="X"; break;
		case 11: $regmonth="XI"; break;
		case 12: $regmonth="XII"; break;
	}

	// Cari Kode Perusahaan $ Kode Grup Dokumen
	$query = "SELECT c.Company_Code, dg.DocumentGroup_Code
			  FROM M_DocumentsOtherNonLegal donl, M_Company c, M_DocumentGroup dg
			  WHERE donl.DONL_DocCode='$_POST[txtTDRTOONLD_DocCode1]'
			  AND donl.DONL_CompanyID=c.Company_ID
			  AND donl.DONL_GroupDocID=dg.DocumentGroup_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Registrasi Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='RETN'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Registrasi Dokumen
	$CT_Code="$newnum/RETN/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode registrasi dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','RETN','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$count=$_POST['countRow'];

		//Insert Detail
		for ($i=1 ; $i<=$count ; $i++) {
			$txtTDRTOONLD_DocCode=str_replace("", "\n",$_POST["txtTDRTOONLD_DocCode".$i]);
			$txtTDRTOONLD_Information=str_replace("<br>", "\n",$_POST["txtTDRTOONLD_Information".$i]);

			$sql1= "INSERT INTO TD_ReturnOfOtherNonLegalDocuments
					VALUES (NULL,'$CT_Code','$txtTDRTOONLD_DocCode','$txtTDRTOONLD_Information',
							'waiting', NULL, sysdate(),
							'$mv_UserID','$mv_UserID', sysdate(),NULL,NULL)";
			$mysqli->query($sql1);

			$sql2="UPDATE TD_ReleaseOfOtherNonLegalDocuments tdrloonld, TD_LoanOfOtherNonLegalDocuments tdloonld, M_DocumentsOtherNonLegal donl
				   SET tdrloonld.TDROONLD_ReturnCode='$CT_Code',
				   	   tdrloonld.TDROONLD_Update_UserID='$mv_UserID',
					   tdrloonld.TDROONLD_Update_Time=sysdate(),
					   donl.DONL_Status='1',
				   	   donl.DONL_Update_UserID='$mv_UserID',
					   donl.DONL_Update_Time=sysdate()
				   WHERE tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
				   AND tdrloonld.TDROONLD_ReturnCode='0'
				   AND tdloonld.TDLOONLD_DocCode='$txtTDRTOONLD_DocCode'
				   AND donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode";
			$mysqli->query($sql2);
		}

		$txtA_ApproverID=$_POST['txtA_ApproverID'];
		$jumlah=count($txtA_ApproverID);

		for($i=0;$i<$jumlah;$i++){
			$step=$i+1;
			$sql2= "INSERT INTO M_Approval
					VALUES (NULL,'$CT_Code', '$txtA_ApproverID[$i]', '$step',
					        '1',NULL,'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
			$mysqli->query($sql2);
			$sa_query="SELECT *
					   FROM M_Approval
					   WHERE A_TransactionCode='$CT_Code'
					   AND A_ApproverID='$txtA_ApproverID[$i]'
					   AND A_Delete_Time IS NULL";
			$sa_sql=mysql_query($sa_query);
			$sa_arr=mysql_fetch_array($sa_sql);
			$ARC_AID=$sa_arr['A_ID'];
			$str=rand(1,100);
			$RandomCode=crypt('T4pagri'.$str);
			$iSQL="INSERT INTO L_ApprovalRandomCode
				   VALUES ('$ARC_AID','$RandomCode')";
			$mysqli->query($iSQL);
		}
		$sql3 = "UPDATE M_Approval
            SET A_Status='2'
            WHERE A_TransactionCode='$CT_Code' AND A_ApproverID='$txtA_ApproverID[0]' AND A_Step='1'";
        $sfe_sql=mysql_query($sql3);
        if($sfe_sql){
		    mail_return_doc($CT_Code);
        }
	}
	echo "<meta http-equiv='refresh' content='0; url=return-of-other-non-legal-documents.php'>";
}

if(isset($_POST['approval'])){
    $A_TransactionCode=$_POST['txtTDRTOONLD_ReturnCode'];
	$A_ApproverID=$mv_UserID;
	$A_Status=$_POST['optTDRTOONLD_Status'];
	$TDRTOONLD_Reason=str_replace("<br>", "\n",$_POST['txtTDRTOONLD_Reason']);

	// MENCARI TAHAP APPROVAL USER TERSEBUT
	$query = "SELECT *
				FROM M_Approval
				WHERE A_TransactionCode='$A_TransactionCode'
				AND A_ApproverID='$A_ApproverID'";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);
	$step=$arr['A_Step'];
	$AppDate=$arr['A_ApprovalDate'];

	$h_query="SELECT *
			  FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld, M_DocumentsOtherNonLegal donl
			  WHERE tdrtoonld.TDRTOONLD_ReturnCode='$A_TransactionCode'
			  AND tdrtoonld.TDRTOONLD_DocCode=donl.DONL_DocCode
			  AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
			  AND donl.DONL_Delete_Time IS NULL";
	$h_sql=mysql_query($h_query);
	$h_arr=mysql_fetch_array($h_sql);

	if ($AppDate==NULL) {
        // MENCARI JUMLAH APPROVAL
        $query = "SELECT MAX(A_Step) AS jStep
                    FROM M_Approval
                    WHERE A_TransactionCode='$A_TransactionCode'";
        $sql = mysql_query($query);
        $arr = mysql_fetch_array($sql);
        $jStep=$arr['jStep'];

        // UPDATE APPROVAL
		$query = "UPDATE M_Approval
					SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
						A_Update_Time=sysdate()
					WHERE A_TransactionCode='$A_TransactionCode'
					AND A_Step='$step'";
        $sql = mysql_query($query);

        // PROSES BILA "SETUJU"
        if ($A_Status=='3') {
            // CEK APAKAH MERUPAKAN APPROVAL FINAL
            if ($step <> $jStep) {
                $nStep=$step+1;
                $query = "UPDATE M_Approval
                            SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
                            WHERE A_TransactionCode='$A_TransactionCode'
                            AND A_Step='$nStep'";
                if ($sql = mysql_query($query)) {
                    mail_return_doc($A_TransactionCode);
                    echo "<meta http-equiv='refresh' content='0; url=home.php'>";
    			}
            }else{
                $query = "UPDATE TD_ReturnOfOtherNonLegalDocuments
                            SET TDRTOONLD_Status='accept', TDRTOONLD_Update_UserID='$A_ApproverID',
                                TDRTOONLD_Update_Time=sysdate()
                            WHERE TDRTOONLD_ReturnCode='$A_TransactionCode'
                            AND TDRTOONLD_Delete_Time IS NULL";
                if ($sql = mysql_query($query)) {
                    // ACTION UNTUK GENERATE NO DOKUMEN
                    $regyear=date("Y");
                    $rmonth=date("n");

                    // Mengubah Bulan ke Romawi
                    switch ($rmonth)	{
                        case 1: $regmonth="I"; break;
                        case 2: $regmonth="II"; break;
                        case 3: $regmonth="III"; break;
                        case 4: $regmonth="IV"; break;
                        case 5: $regmonth="V"; break;
                        case 6: $regmonth="VI"; break;
                        case 7: $regmonth="VII"; break;
                        case 8: $regmonth="VIII"; break;
                        case 9: $regmonth="IX"; break;
                        case 10: $regmonth="X"; break;
                        case 11: $regmonth="XI"; break;
                        case 12: $regmonth="XII"; break;
                    }

                    // Cari Kode Perusahaan
                    $query = "SELECT *
                                FROM M_Company
                                WHERE Company_ID='$h_arr[DONL_CompanyID]'";
                    $sql = mysql_query($query);
                    $field = mysql_fetch_array($sql);
                    $Company_Code=$field['Company_Code'];

                    // Cari Kode Dokumen Grup
                    $query = "SELECT *
                                FROM M_DocumentGroup
                                WHERE DocumentGroup_ID ='$h_arr[DONL_GroupDocID]'";
                    $sql = mysql_query($query);
                    $field = mysql_fetch_array($sql);
                    $DocumentGroup_Code=$field['DocumentGroup_Code'];

                    // Cari No Dokumen Terakhir
                    $query = "SELECT MAX(CD_SeqNo)
                                FROM M_CodeDocument
                                WHERE CD_Year='$regyear'
                                -- AND CT_Action='DOUT'
                                AND CD_GroupDocCode='$DocumentGroup_Code'
                                AND CD_CompanyCode='$Company_Code'
                                AND CD_Delete_Time is NULL";
                    $sql = mysql_query($query);
                    $field = mysql_fetch_array($sql);

                    if($field[0]==NULL)
                        $maxnum=0;
                    else
                        $maxnum=$field[0];
                    $nnum=$maxnum+1;

                    $d_query="SELECT *
                              FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld,
                                   M_DocumentsOtherNonLegal donl
                              WHERE tdrtoonld.TDRTOONLD_ReturnCode='$h_arr[TDRTOONLD_ReturnCode]'
                              AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
                              AND tdrtoonld.TDRTOONLD_DocCode=donl.DONL_DocCode";
                    $d_sql=mysql_query($d_query);
                    while($d_arr=mysql_fetch_array($d_sql)){
                        $newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
                        // Kode Pengeluaran Dokumen
                        $CT_Code="$newnum/DRETN/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

                        $docStatus = 1; //Dokumen Tersedia kembali pada Custodian
                        $query1="UPDATE M_DocumentsOtherNonLegal
                                 SET DONL_Status='$docStatus', DONL_Update_UserID='$A_ApproverID',
                                     DONL_Update_Time=sysdate()
                                 WHERE DONL_DocCode='$d_arr[DONL_DocCode]'";
                        // $query2="INSERT INTO M_CodeTransaction
                        // 	   	 VALUES (NULL,'$CT_Code','$nnum','DRETN','$Company_Code','$DocumentGroup_Code',
                        // 				 '$rmonth','$regyear','$A_ApproverID',sysdate(),
                        // 				 '$A_ApproverID',sysdate(),NULL,NULL)";

                        $mysqli->query($query1);
                        // $mysqli->query($query2);
                        $nnum=$nnum+1;
                    }
                    mail_notif_return_doc($A_TransactionCode, $h_arr['TDRTOONLD_UserID'], 3 );
                    mail_notif_return_doc($A_TransactionCode, "cust0002", 3 );

					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
                }
            }
        }
        // PROSES BILA "TOLAK"
    	if ($A_Status=='4') {
    		$query = "UPDATE TD_ReturnOfOtherNonLegalDocuments
    					SET TDRTOONLD_Status='reject', TDRTOONLD_Reason='$TDRTOONLD_Reason',
    						TDRTOONLD_Update_Time=sysdate(), TDRTOONLD_Update_UserID='$A_ApproverID'
    					WHERE TDRTOONLD_ReturnCode='$A_TransactionCode'";

    		$query1 = "UPDATE M_Approval
    					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
    						A_Status='$A_Status'
    					WHERE A_TransactionCode='$A_TransactionCode'
    					AND A_Step>'$step'";
    		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
    			$txtDONL_ID=$_POST['txtDONL_ID'];
    			$jumlah=count($txtDONL_ID);

    			for ($i=0;$i<$jumlah;$i++) {
    				$query = "UPDATE M_DocumentsOtherNonLegal
    						  SET DONL_Status='4', DONL_Update_UserID='$A_ApproverID', DONL_Update_Time=sysdate()
    						  WHERE DONL_ID='$txtDONL_ID[$i]'";
    				$mysqli->query($query);
    			}
    			// mail_notif_release_doc($A_TransactionCode, $_POST['txtTHLOLD_UserID'], 4 );
    			mail_notif_return_doc($A_TransactionCode, $_POST['txtUser_ID'], 4 );
    			echo "<meta http-equiv='refresh' content='0; url=home.php'>";
    		}
    	}
    }
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
// TAMBAH BARIS
function addRowToTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	document.getElementById('countRow').value = (document.getElementById('countRow').value*1) + 1;
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);

	// KODE DOKUMEN
	var cellOneSel = row.insertCell(0);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDRTOONLD_DocCode' + iteration;
	el.id = 'txtTDRTOONLD_DocCode' + iteration;
	el.setAttribute("readonly","readonly");
	el.setAttribute("onClick", "javascript:showList("+iteration+");");
	el.size = '80';
	cellOneSel.appendChild(el);

	// KETERANGAN PENGEMBALIAN DOKUMEN
	var cellTwoSel = row.insertCell(1);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDRTOONLD_Information' + iteration;
	el.id = 'txtTDRTOONLD_Information' + iteration;
	el.size = '80';
	cellTwoSel.appendChild(el);
}

// HAPUS BARIS
function removeRowFromTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	if(document.getElementById('countRow').value > 1)
		document.getElementById('countRow').value -= 1;
	if (lastRow > 2)
		tbl.deleteRow(lastRow - 1);
}
</script>
