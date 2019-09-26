<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 29 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Pengembalian Dokumen Pembebasan Lahan</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.retdocla.php");
?>

<script language="JavaScript" type="text/JavaScript">
function showList(n) {
	var docGrup="3"; //Grup Dokumen Ganti Rugi Lahan
	var txtKe = n;
	sList = window.open("popupRelease.php?gID="+docGrup+"&txtKe="+txtKe+"", "Daftar_Pengeluaran_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
}
// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var jrow = document.getElementById('countRow').value;

	for (i = 1; i <= jrow; i++){
		var txtTDRTOLAD_DocCode = document.getElementById('txtTDRTOLAD_DocCode' + i).value;
		var checkDocCode = 0;
		txtTDRTOLAD_DocCode=txtTDRTOLAD_DocCode.replace("\n","");

		if (txtTDRTOLAD_DocCode.replace(" ", "") == "")  {
			alert("Kode Dokumen pada baris ke-" + i + " Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
				$query = "SELECT *
				  		  FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad,
						       TD_LoanOfLandAcquisitionDocument tdlolad, M_DocumentLandAcquisition dla
				  		  WHERE tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				  		  AND dla.DLA_Code=tdlolad.TDLOLAD_DocCode
						  AND dla.DLA_Status='4'
			   			  AND tdrlolad.TDRLOLAD_ReturnCode='0'";
				$result = mysql_query($query);
				while ($data = mysql_fetch_array($result)) {
					$TDLOLAD_DocCode = $data['TDLOLAD_DocCode'];
					$a = "if (txtTDRTOLAD_DocCode == '$TDLOLAD_DocCode') {";
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
		<th colspan=3>Pengembalian Dokumen Pembebasan Lahan</th>";

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
				<textarea name='txtTDRTOLAD_DocCode1' id='txtTDRTOLAD_DocCode1' cols='20' rows='1' readonly='readonly' onClick='javascript:showList(1);'></textarea>
			</td>
			<td>
				<textarea name='txtTDRTOLAD_Information1' id='txtTDRTOLAD_Information1' cols='20' rows='1'></textarea>
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
        		  	 FROM TD_ReturnOfLandAcquisitionDocument tdrtolad, M_Approval dra
        			 WHERE tdrtolad.TDRTOLAD_Delete_Time is NULL
        			 AND dra.A_ApproverID='$mv_UserID'
        			 AND dra.A_Status='2'
        			 AND dra.A_TransactionCode=tdrtolad.TDRTOLAD_ReturnCode
        			 AND tdrtolad.TDRTOLAD_ReturnCode='$id'";
        $approver=mysql_num_rows(mysql_query($cApp_query));
        $appQuery=(($do=='approve')&&($approver=="1"))?"AND m_app.A_ApproverID='$mv_UserID'":"AND m_app.A_Status='2'";

		$query1 = "SELECT  tdrtolad.TDRTOLAD_ReturnCode, u.User_FullName, d.Division_Name, dp.Department_Name,
		    			   p.Position_Name, tdrtolad.TDRTOLAD_ReturnTime, u.User_ID,
						   tdrtolad.TDRTOLAD_Status, drs.DRS_Description, tdrtolad.TDRTOLAD_Reason,
                           (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=m_app.A_ApproverID) waitingApproval
			   	   FROM TD_ReturnOfLandAcquisitionDocument tdrtolad
				   LEFT JOIN M_User u
						ON tdrtolad.TDRTOLAD_UserID=u.User_ID
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
      					ON tdrtolad.TDRTOLAD_ReturnCode=m_app.A_TransactionCode
                     	$appQuery
                   LEFT JOIN M_DocumentRegistrationStatus drs
			        	ON tdrtolad.TDRTOLAD_Status=drs.DRS_Name
			       WHERE tdrtolad.TDRTOLAD_ReturnCode='$id'";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);
		$fregdate=date('j M Y', strtotime($field1[TDRTOLAD_ReturnTime]));


		$ActionContent ="
		<form name='app-doc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>";
		if(($do=='approve')&&($approver=="1"))
        	$ActionContent .="<th colspan=3>Persetujuan Pengembalian Dokumen Pembebasan Lahan</th>";
        else
            $ActionContent .="<th colspan=3>Pengembalian Dokumen Pembebasan Lahan</th>";
		$ActionContent .="<tr>
			<td width='30%'>No Pengembalian</td>
			<td width='67%'>
				<input type='hidden' name='txtTDRTOLAD_ReturnCode' id='txtTDRTOLAD_ReturnCode' value='$field1[TDRTOLAD_ReturnCode]'>
				$field1[TDRTOLAD_ReturnCode]
			</td>
			<td width='3%'><a href='print-return-of-land-acquisition-document.php?id=$field1[TDRTOLAD_ReturnCode]' target='_blank'><img src='./images/icon-print.png'></a>
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
        			<select name='optTDRTOLAD_Status' id='optTDRTOLAD_Status'>
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
        			<textarea name='txtTDRTOLAD_Reason' id='txtTDRTOLAD_Reason' cols='50' rows='2'>$arr[TDRTOLAD_Reason]</textarea>
        			<br>*Wajib Diisi Apabila Dokumen Ditolak.
        		</td>
        	</tr>";
        }else {
        	$ActionContent .="<tr>
        		<td>Status Dokumen</td>";

        	if($field1['TDRTOLAD_Status']=="waiting") {
        		$ActionContent .="
        		      <td colspan='2'>Menunggu Persetujuan $field1[waitingApproval]</td>
                </tr>";
        	}else if($field1['TDRTOLAD_Status']=="accept") {
        		$ActionContent .="
        			<td colspan='2'>Disetujui</td>
        		</tr>";
        	}else if($field1['TDRTOLAD_Status']=="reject") {
        		$ActionContent .="
        			<td colspan='2'>Ditolak</td>
        		</tr>
        		<tr>
        			<td>Alasan</td>
        			<td colspan='2'>$field1[TDRTOLAD_Reason]</td>
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
			<th>Tahap GRL</th>
			<th>Periode GRL</th>
			<th>Tanggal Dokumen</th>
			<th>Blok</th>
			<th>Desa</th>
			<th>Pemilik</th>
			<th>Ket. Pengembalian</th>
		</tr>";

		$queryd = "SELECT dla.DLA_Code, c.Company_Name, dla.DLA_ID,tdrtolad.TDRTOLAD_Information, dla.DLA_Phase,
					      dla.DLA_Period, dla.DLA_DocDate, dla.DLA_Block, dla.DLA_Village,dla.DLA_Owner,
					 	  dla.DLA_Information
					FROM TD_ReturnOfLandAcquisitionDocument tdrtolad, M_DocumentLandAcquisition dla, M_Company c
					WHERE tdrtolad.TDRTOLAD_ReturnCode='$id'
					AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
					AND tdrtolad.TDRTOLAD_DocCode=dla.DLA_Code
					AND dla.DLA_CompanyID=c.Company_ID";
		$sqld = mysql_query($queryd);
		while ($arrd = mysql_fetch_array($sqld)) {
			$fperdate=date("j M Y", strtotime($arrd['DLA_Period']));
			$fdocdate=date("j M Y", strtotime($arrd['DLA_DocDate']));
			$ActionContent .="
			<tr>
				<td align='center'>$arrd[DLA_Code]</td>
				<td align='center'>$arrd[Company_Name]</td>
				<td align='center'>$arrd[DLA_Phase]</td>
				<td align='center'>$fperdate</td>
				<td align='center'>$fdocdate</td>
				<td align='center'>$arrd[DLA_Block]</td>
				<td align='center'>$arrd[DLA_Village]</td>
				<td align='center'>$arrd[DLA_Owner]</td>
				<td align='center'><pre>$arrd[TDRTOLAD_Information]</pre></td>
			</tr>";
		}
		$ActionContent .="
		</table>";
	}

	//Kirim Ulang Email Persetujuan
	if($act=='resend'){
		mail_return_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=return-of-land-acquisition-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT DISTINCT tdrtolad.TDRTOLAD_ID, tdrtolad.TDRTOLAD_ReturnCode, tdrtolad.TDRTOLAD_ReturnTime, u.User_FullName,
            drs.DRS_Description, tdrtolad.TDRTOLAD_Status
		  FROM TD_ReturnOfLandAcquisitionDocument tdrtolad
		  LEFT JOIN M_User u
            ON tdrtolad.TDRTOLAD_UserID=u.User_ID
          LEFT JOIN M_DocumentRegistrationStatus drs
            ON tdrtolad.TDRTOLAD_Status=drs.DRS_Name
		  WHERE tdrtolad.TDRTOLAD_Delete_Time is NULL
		  	AND u.User_ID='$mv_UserID'
		  ORDER BY tdrtolad.TDRTOLAD_ID DESC
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
		$fregdate=date("j M Y", strtotime($field['TDRTOLAD_ReturnTime']));
		$resend=($field['TDRTOLAD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

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

$query1 ="SELECT DISTINCT tdrtolad.TDRTOLAD_ID, tdrtolad.TDRTOLAD_ReturnCode, tdrtolad.TDRTOLAD_ReturnTime, u.User_FullName,
            drs.DRS_Description, tdrtolad.TDRTOLAD_Status
		  FROM TD_ReturnOfLandAcquisitionDocument tdrtolad
		  LEFT JOIN M_User u
            ON tdrtolad.TDRTOLAD_UserID=u.User_ID
          LEFT JOIN M_DocumentRegistrationStatus drs
            ON tdrtolad.TDRTOLAD_Status=drs.DRS_Name
		  WHERE tdrtolad.TDRTOLAD_Delete_Time is NULL
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
	echo "<meta http-equiv='refresh' content='0; url=return-of-land-acquisition-document.php'>";
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
	$query = "SELECT c.Company_Code
			  FROM M_DocumentLandAcquisition dla, M_Company c
			  WHERE dla.DLA_Code='$_POST[txtTDRTOLAD_DocCode1]'
			  AND dla.DLA_CompanyID=c.Company_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code='GRL';

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
			$txtTDRTOLAD_DocCode=str_replace("", "\n",$_POST["txtTDRTOLAD_DocCode".$i]);
			$txtTDRTOLAD_Information=str_replace("<br>", "\n",$_POST["txtTDRTOLAD_Information".$i]);

			$sql1= "INSERT INTO TD_ReturnOfLandAcquisitionDocument
					VALUES (NULL,'$CT_Code','$txtTDRTOLAD_DocCode','$txtTDRTOLAD_Information',
							'waiting', NULL, sysdate(),
							'$mv_UserID','$mv_UserID', sysdate(),NULL,NULL)";
			$mysqli->query($sql1);

			$sql2="UPDATE TD_ReleaseOfLandAcquisitionDocument tdrlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
						  M_DocumentLandAcquisition dla, M_DocumentLandAcquisitionAttribute dlaa
				   SET tdrlolad.TDRLOLAD_ReturnCode='$CT_Code',
				   	   tdrlolad.TDRLOLAD_Update_UserID='$mv_UserID',
					   tdrlolad.TDRLOLAD_Update_Time=sysdate(),
					   dla.DLA_Status='1',
				   	   dla.DLA_Update_UserID='$mv_UserID',
					   dla.DLA_Update_Time=sysdate(),
					   dlaa.DLAA_Status ='1',
					   dlaa.DLAA_Update_Time=sysdate(),
					   dlaa.DLAA_Update_UserID='$mv_UserID'
				   WHERE tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				   AND tdrlolad.TDRLOLAD_ReturnCode='0'
				   AND tdlolad.TDLOLAD_DocCode='$txtTDRTOLAD_DocCode'
				   AND dla.DLA_Code=tdlolad.TDLOLAD_DocCode";
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
	echo "<meta http-equiv='refresh' content='0; url=return-of-land-acquisition-document.php'>";
}

if(isset($_POST['approval'])){
    $A_TransactionCode=$_POST['txtTDRTOLAD_ReturnCode'];
	$A_ApproverID=$mv_UserID;
	$A_Status=$_POST['optTDRTOLAD_Status'];
	$TDRTOLAD_Reason=str_replace("<br>", "\n",$_POST['txtTDRTOLAD_Reason']);

	// MENCARI TAHAP APPROVAL USER TERSEBUT
	$query = "SELECT *
				FROM M_Approval
				WHERE A_TransactionCode='$A_TransactionCode'
				AND A_ApproverID='$A_ApproverID'";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);
	$step=$arr['A_Step'];
	$AppDate=$arr['A_ApprovalDate'];

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
                $query = "UPDATE TD_ReturnOfLandAcquisitionDocument
                            SET TDRTOLAD_Status='accept', TDRTOLAD_Update_UserID='$A_ApproverID',
                                TDRTOLAD_Update_Time=sysdate()
                            WHERE TDRTOLAD_ReturnCode='$A_TransactionCode'
                            AND TDRTOLAD_Delete_Time IS NULL";
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
                                WHERE Company_ID='$h_arr[DLA_CompanyID]'";
                    $sql = mysql_query($query);
                    $field = mysql_fetch_array($sql);
                    $Company_Code=$field['Company_Code'];

                    // Cari Kode Dokumen Grup
                    $query = "SELECT *
                                FROM M_DocumentGroup
                                WHERE DocumentGroup_ID ='$h_arr[DLA_GroupDocID]'";
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
                              FROM TD_ReturnOfLandAcquisitionDocument tdrtolad,
                                   M_DocumentLandAcquisition dla
                              WHERE tdrtolad.TDRTOLAD_ReturnCode='$h_arr[TDRTOLAD_ReturnCode]'
                              AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
                              AND tdrtolad.TDRTOLAD_DocCode=dla.DLA_Code";
                    $d_sql=mysql_query($d_query);
                    while($d_arr=mysql_fetch_array($d_sql)){
                        $newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
                        // Kode Pengeluaran Dokumen
                        $CT_Code="$newnum/DRETN/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

                        $docStatus = 1; //Dokumen Tersedia kembali pada Custodian
                        $query1="UPDATE M_DocumentLandAcquisition
                                 SET DLA_Status='$docStatus', DLA_Update_UserID='$A_ApproverID',
                                     DLA_Update_Time=sysdate()
                                 WHERE DLA_Code='$d_arr[DLA_Code]'";
                        // $query2="INSERT INTO M_CodeTransaction
                        // 	   	 VALUES (NULL,'$CT_Code','$nnum','DRETN','$Company_Code','$DocumentGroup_Code',
                        // 				 '$rmonth','$regyear','$A_ApproverID',sysdate(),
                        // 				 '$A_ApproverID',sysdate(),NULL,NULL)";

                        $mysqli->query($query1);
                        // $mysqli->query($query2);
                        $nnum=$nnum+1;
                    }
                    mail_notif_return_doc($A_TransactionCode, $h_arr['TDRTOLAD_UserID'], 3 );
                    mail_notif_return_doc($A_TransactionCode, "cust0002", 3 );

					echo "<meta http-equiv='refresh' content='0; url=home.php'>";
                }
            }
        }
        // PROSES BILA "TOLAK"
    	if ($A_Status=='4') {
    		$query = "UPDATE TD_ReturnOfLandAcquisitionDocument
    					SET TDRTOLAD_Status='reject', TDRTOLAD_Reason='$TDRTOLAD_Reason',
    						TDRTOLAD_Update_Time=sysdate(), TDRTOLAD_Update_UserID='$A_ApproverID'
    					WHERE TDRTOLAD_ReturnCode='$A_TransactionCode'";

    		$query1 = "UPDATE M_Approval
    					SET A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
    						A_Status='$A_Status'
    					WHERE A_TransactionCode='$A_TransactionCode'
    					AND A_Step>'$step'";
    		if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1))) {
    			$txtDLA_ID=$_POST['txtDLA_ID'];
    			$jumlah=count($txtDLA_ID);

    			for ($i=0;$i<$jumlah;$i++) {
    				$query = "UPDATE M_DocumentLandAcquisition
    						  SET DLA_Status='4', DLA_Update_UserID='$A_ApproverID', DLA_Update_Time=sysdate()
    						  WHERE DLA_ID='$txtDLA_ID[$i]'";
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
	el.name = 'txtTDRTOLAD_DocCode' + iteration;
	el.id = 'txtTDRTOLAD_DocCode' + iteration;
	el.setAttribute("readonly","readonly"); //Arief F - 26092018
	el.setAttribute("onClick", "javascript:showList("+iteration+");"); //Arief F - 26092018
	el.size = '80';
	cellOneSel.appendChild(el);

	// INFORMASI PENGEMBALIAN
	var cellTwoSel = row.insertCell(1);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDRTOLAD_Information' + iteration;
	el.id = 'txtTDRTOLAD_Information' + iteration;
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
