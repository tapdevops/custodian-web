<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 29 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
=		19/09/2012	: Perubahan Reminder Email																			=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start(); 
?>
<title>Custodian System | Permintaan Dokumen Pembebasan Lahan</title>
<head>
<?PHP 
include ("./config/config_db.php"); 
include ("./include/function.mail.lodocla.php");
?>

<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;							

	var optTHLOLAD_LoanCategoryID = document.getElementById('optTHLOLAD_LoanCategoryID').selectedIndex;
	var optTHLOLAD_CompanyID = document.getElementById('optTHLOLAD_CompanyID').selectedIndex;
	var txtTHLOLAD_Information = document.getElementById('txtTHLOLAD_Information').value;
		
		if(optTHLOLAD_LoanCategoryID == 0) {
			alert("Tipe Permintaan Belum Dipilih!");
			returnValue = false;
		}
		if(optTHLOLAD_CompanyID == 0) {
			alert("Perusahaan Belum Dipilih!");
			returnValue = false;
		}
		if (txtTHLOLAD_Information.replace(" ", "") == "")  {	
			alert("Alasan Permintaan Belum Diisi!");
			returnValue = false;
		}
	return returnValue;
}

//LoV UTK DAFTAR DOKUMEN
function showList(row) {
	var txtTHLOLD_CompanyID = document.getElementById('txtTHLOLAD_CompanyID').value;
	var txtTHLOLD_DocumentGroupID = "3";
	var optTDLOLAD_Phase = document.getElementById('optTDLOLAD_Phase' + row).value;
	var docCode = document.getElementById('docCode').value;
	//var endocCode = base64.encode(docCode);
	//alert (endocCode);
	if (optTDLOLAD_Phase=="0")
		alert ("Pilih Tahap GRL Pada Baris ke-"+row+" Terlebih Dahulu");
	else
		sList = window.open("popupDoc.php?row="+row+"&cID="+txtTHLOLD_CompanyID+"&gID="+txtTHLOLD_DocumentGroupID+"&pID="+optTDLOLAD_Phase+"&recentCode="+docCode+"", "Daftar_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
}
function remLink() {
  if (window.sList && window.sList.open && !window.sList.closed)
	window.sList.opener = null;
}

// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var returnValue;
	var checkDocCode;
	returnValue = true;							
	var jrow = document.getElementById('countRow').value;
	var txtTHLOLAD_CompanyID = document.getElementById('txtTHLOLAD_CompanyID').value;
	var txtTHLOLAD_Information = document.getElementById('txtTHLOLAD_Information').value;
	
		if (txtTHLOLAD_Information.replace(" ", "") == "")  {	
			alert("Alasan Permintaan Belum Diisi!");
			returnValue = false;
		}
	
	for (i = 1; i <= jrow; i++){
		checkDocCode = 0;
		var txtTDLOLAD_DocCode = document.getElementById('txtTDLOLAD_DocCode' + i).value;
		var optTDLOLAD_Phase = document.getElementById('optTDLOLAD_Phase' + i).value;

		if (optTDLOLAD_Phase == "0")  {	
			alert("Tahap GRL pada baris ke-" + i + " Belum Dipilih!");
			returnValue = false;
		}
		if (txtTDLOLAD_DocCode.replace(" ", "") == "")  {	
			alert("Kode Dokumen pada baris ke-" + i + " Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 			$query = "SELECT * 
					  FROM M_DocumentLandAcquisition
					  WHERE DLA_Status ='1'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) { 
				$DLA_Code = $data['DLA_Code'];
				$DLA_CompanyID = $data['DLA_CompanyID'];
				$DLA_Phase = $data['DLA_Phase'];
				
				$a = "if ((txtTDLOLAD_DocCode == '$DLA_Code') 
						   && (txtTHLOLAD_CompanyID == '$DLA_CompanyID') 
						   && (optTDLOLAD_Phase == '$DLA_Phase') 
						   ){";
				$a .= "checkDocCode = 1; ";
				$a .= "}";
			echo $a;
		 	}
			?> 
			if (checkDocCode == 0) {
				alert("Kode Dokumen pada baris ke-" + i + " Salah Atau Dokumen Tidak Tersedia!");
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
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
	
require_once "./include/template.inc";
$page=new Template();

$act=$_GET["act"];
if(isset($_GET["act"])) {
	
	//Menambah Header Permintaan Dokumen
	if($act=='add') {
		$ActionContent ="
		<form name='add-doc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Permintaan Dokumen Pembebasan Lahan</th>
		</tr>";
	
		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID, 
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
				  WHERE u.User_ID='$_SESSION[User_ID]'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);
		
		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHLOLAD_UserID' type='hidden' value='$_SESSION[User_ID]'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtTHLOLAD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtTHLOLAD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input name='txtTHLOLAD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";
		
			$query = "SELECT DISTINCT c.Company_ID, c.Company_Name 
					  FROM M_DocumentLandAcquisition dla
					  INNER JOIN M_Company c
						ON dla.DLA_CompanyID = c.Company_ID
					  WHERE dla.DLA_Delete_Time is NULL					  
					  AND dla.DLA_Status='1'
					  ORDER BY c.Company_Name";
			$sql = mysql_query($query);
			$number=mysql_num_rows($sql);
		
		if($field['User_SPV1']||$field['User_SPV2']){
			if ($number>0) {
				$ActionContent .="	
				<tr>
					<td>Kategori Permintaan</td>
					<td> 
						<select name='optTHLOLAD_LoanCategoryID' id='optTHLOLAD_LoanCategoryID'>
							<option value='0'>--- Pilih Kategori Permintaan ---</option>";

						$query1="SELECT * 
								 FROM M_LoanCategory
								 WHERE LoanCategory_Delete_Time is NULL";
						$sql1 = mysql_query($query1);
						
						while ($field1 = mysql_fetch_array($sql1) ){
							$ActionContent .="
							<option value='$field1[LoanCategory_ID]'>$field1[LoanCategory_Name]</option>";
						}
				$ActionContent .="	
						</select>
					</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td> 
						<select name='optTHLOLAD_CompanyID' id='optTHLOLAD_CompanyID'>
							<option value='0'>--- Pilih Perusahan ---</option>";			
						while ($field = mysql_fetch_array($sql) ){
							$ActionContent .="
							<option value='$field[Company_ID]'>$field[Company_Name]</option>";
						}
				$ActionContent .="	
						</select>
					</td>
				</tr>
				<tr>
					<td valign='top'>Alasan Permintaan</td>
					<td><textarea name='txtTHLOLAD_Information' id='txtTHLOLAD_Information' cols='50' rows='2'></textarea></td>
				</tr>
				<tr>
				<th colspan=3>
					<input name='addheader' type='submit' value='Simpan' class='button' onclick='return validateInputHeader(this);'/>
					<input name='cancel' type='submit' value='Batal' class='button' />
				</th></tr>";
			}else {
				if(!$_POST['cancel']){
					echo "<script>alert('Tidak Ada Dokumen Yang Dapat Melakukan Transaksi Ini.');</script>";
				}
				$ActionContent .="				
				<tr>
					<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
						Tidak Ada Dokumen Yang Dapat Melakukan Transaksi Ini.
					</td>
				</tr>
				<tr>
					<th colspan=3>
						<input name='cancel' type='submit' value='OK' class='button'/>
					</th>
				</tr>";
			}
		}else{
			if(!$_POST['cancel']){
				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.');</script>";
			}
			
			$ActionContent .="	
			<tr>
				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
					Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.<br>
					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
				</td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='cancel' type='submit' value='OK' class='button'/>
				</th>
			</tr>";
		}
		$ActionContent .="</table></form>";
	}
	
	//Menambah Detail Permintaan Dokumen
	elseif($act=='adddetail')	{
		$id=$_GET["id"];
		
		$query = "SELECT thlolad.THLOLAD_ID, 
						 thlolad.THLOLAD_LoanCode, 
						 thlolad.THLOLAD_LoanDate, 
						 u.User_FullName,
						 d.Division_Name,
						 de.Department_Name,
						 p.Position_Name,
						 c.Company_Name, c.Company_ID,
						 lc.LoanCategory_Name,
						 lc.LoanCategory_ID,
						 thlolad.THLOLAD_Information
				  FROM TH_LoanOfLandAcquisitionDocument thlolad
				  LEFT JOIN M_User u
					ON thlolad.THLOLAD_UserID=u.User_ID
					AND thlolad.THLOLAD_UserID='$_SESSION[User_ID]'
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON ddp.DDP_UserID=u.User_ID
				  LEFT JOIN M_Company c
					ON thlolad.THLOLAD_CompanyID=c.Company_ID 
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department de
					ON ddp.DDP_DeptID=de.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_LoanCategory lc
					ON thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
				  WHERE thlolad.THLOLAD_Delete_Time is NULL 
				  AND thlolad.THLOLAD_LoanCode='$id'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);
		$DLA_CompanyID=$field['Company_ID'];

		$ActionContent ="
		<form name='adddetaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Permintaan Dokumen Pembebasan Lahan</th>
		</tr>";
		
		$floandate=date("j M Y", strtotime($field['THLOLAD_LoanDate']));
		
		$ActionContent .="
		<tr>
			<td width='30'>No Permintaan</td>
			<td width='70%'>
				<input name='txtTDLOLAD_THLOLAD_ID' type='hidden' value='$field[THLOLAD_ID]'/>
				<input type='hidden' name='txtTDLOLAD_THLOLAD_LoanCode' value='$field[THLOLAD_LoanCode]' readonly='true' class='readonly' style='width:80%;'/>
				$field[THLOLAD_LoanCode]
			</td>
		</tr>
		<tr>
			<td>Tanggal Permintaan</td>
			<td>$floandate</td>
		</tr>
		<tr>
			<td>Nama Peminjam</td>
			<td>$field[User_FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field[Division_Name]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field[Department_Name]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field[Position_Name]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>
				<input id='txtTHLOLAD_CompanyID' type='hidden' value='$DLA_CompanyID'/>
				$field[Company_Name]
			</td>
		</tr>
		<tr>
			<td>Jenis Permintaan</td>
			<td>
				<input name='optTHLOLAD_LoanCategoryID' type='hidden' value='$field[LoanCategory_ID]'/>
				$field[LoanCategory_Name]
			</td>
		</tr>
		<tr>
			<td valign='top'>Alasan Permintaan</td>
			<td>
				<textarea name='txtTHLOLAD_Information' id='txtTHLOLAD_Information' cols='50' rows='2'>$field[THLOLAD_Information]</textarea>
			</td>
		</tr>
		</table>
		<input name='docCode' id='docCode' type='hidden' value=''/>
		<div style='space'>&nbsp;</div>
		
		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th>Tahap GRL</th>
			<th>Kode Dokumen</th>
			<th>Keterangan Permintaan</th>
		</tr>
		<tr>
			<td>
				<select name='optTDLOLAD_Phase1' id='optTDLOLAD_Phase1'>
					<option value='0'>--- Pilih Tahap GRL ---</option>";
		$sc_query="SELECT DISTINCT DLA_Phase
				   FROM M_DocumentLandAcquisition
				   WHERE DLA_Delete_Time IS NULL
				   AND DLA_CompanyID='$DLA_CompanyID'
				   AND DLA_Status='1'
				   ORDER BY DLA_Phase";
		$sc_sql = mysql_query($sc_query);
		while ($sc_arr=mysql_fetch_array($sc_sql)){
			$ActionContent .="
			<option value='$sc_arr[DLA_Phase]'>Tahap $sc_arr[DLA_Phase]</option>";
		}

		$ActionContent .="
				</select>
			</td>
			<td>
				<input type='text' name='txtTDLOLAD_DocCode1' id='txtTDLOLAD_DocCode1' value='' readonly='readonly' onClick='javascript:showList(1);'/>
			</td>
			<td>
				<textarea name='txtTDLOLAD_Information1' id='txtTDLOLAD_Information1' cols='20' rows='1'></textarea>
			</td>
		</tr>
		</table>
		
		<table width='100%'>
		<th  class='bg-white'>
			<input onclick='addRowToTable();' type='button' class='addrow'/>
			<input onclick='removeRowFromTable();' type='button' class='deleterow'/>
			<input type='hidden' value='1' id='countRow' name='countRow' />
		</th>
		</table>
		
		<table width='100%'>
		<tr>
			<td>";

			/* PROSES APPROVAL */
			$user=$_SESSION['User_ID'];
			
			for($sApp=1;$sApp<3;$sApp++) {
				//ATASAN LANGSUNG
				$query="SELECT User_SPV1,User_SPV2
						FROM M_User
						WHERE User_ID='$user'";
				$sql=mysql_query($query);
				$obj=mysql_fetch_object($sql);
				$atasan1=$obj->User_SPV1;
				$atasan2=$obj->User_SPV2;
				
				if($atasan2){
					$sApp=3;
					$atasan=$atasan2;
				}else{
					$atasan=$atasan1;
				}
				
				$query="SELECT Employee_NIK
						FROM db_master.M_Employee
						WHERE Employee_NIK='".$atasan."'
						AND Employee_Position NOT LIKE '%SECTION%'
						AND Employee_Position NOT LIKE '%SUB DEP%'";
				$sql=mysql_query($query);
				$canApprove=mysql_num_rows($sql);
				
				if($canApprove){
					$ActionContent .="<input type='hidden' name='txtA_ApproverID[]' value='$atasan' readonly='true' class='readonly'/>";
				}else{
					$sApp=0;
				}						
				
				$user=$atasan1;
			}
			
			$query="SELECT a.Approver_UserID
					FROM M_Approver a
					LEFT JOIN M_Role_Approver ra
						ON ra.RA_ID=a.Approver_RoleID
					WHERE a.Approver_Delete_Time is NULL
					ORDER BY ra.RA_ID";
			$sql=mysql_query($query);
			
			while($obj=mysql_fetch_object($sql)){
				$ActionContent .="
				<input type='hidden' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}
			// AKHIR PROSES APPROVAL
			
		$ActionContent .="
			</td>
		</tr>
		<th><input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/><input name='canceldetail' type='submit' value='Batal' class='button'/></th>
		</table>
		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}
	//Kirim Ulang Email Persetujuan
	elseif($act=='resend'){
		mail_loan_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=loan-of-land-acquisition-document.php'>";
	}
}
	
// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page'])) 
    $noPage = $_GET['page'];
else 
	$noPage = 1;
	
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT thlolad.THLOLAD_ID, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, u.User_FullName,
 		  		 c.Company_Name, lc.LoanCategory_Name, drs.DRS_Description,thlolad.THLOLAD_Status	
		  FROM TH_LoanOfLandAcquisitionDocument thlolad, M_User u, M_Company c, M_LoanCategory lc,
		  	   M_DocumentRegistrationStatus drs
		  WHERE thlolad.THLOLAD_Delete_Time is NULL 
		  AND thlolad.THLOLAD_CompanyID=c.Company_ID 
		  AND thlolad.THLOLAD_UserID=u.User_ID 
		  AND u.User_ID='$_SESSION[User_ID]' 
		  AND thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
		  AND thlolad.THLOLAD_Status=drs.DRS_Name
		  ORDER BY thlolad.THLOLAD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='25%'>Kode Permintaan</th>
	<th width='10%'>Tanggal Permintaan</th>
	<th width='20%'>Nama Peminjam</th>
	<th width='20%'>Nama Perusahaan</th>
	<th width='10%'>Kategori Permintaan</th>	
	<th width='10%'>Status</th>	
	<th width='5%'></th>	
</tr>";
if ($num==NULL) {
$MainContent .="
	<tr>
		<td colspan=7 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$floandate=date("j M Y", strtotime($field['THLOLAD_LoanDate']));
		$resend=($field['THLOLAD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-loan-land-acquisition-document.php?id=$field[0]' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$floandate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$field[5]</td>
			<td class='center'>$field[6]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT thlolad.THLOLAD_ID, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, u.User_FullName,
 		  		  c.Company_Name, lc.LoanCategory_Name
		   FROM TH_LoanOfLandAcquisitionDocument thlolad, M_User u, M_Company c, M_LoanCategory lc
		   WHERE thlolad.THLOLAD_Delete_Time is NULL 
		   AND thlolad.THLOLAD_CompanyID=c.Company_ID 
		   AND thlolad.THLOLAD_UserID=u.User_ID 
		   AND u.User_ID='$_SESSION[User_ID]' 
		   AND thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID";
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
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=loan-of-land-acquisition-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_LoanOfLandAcquisitionDocument thlolad
			   SET ct.CT_Delete_UserID='$_SESSION[User_ID]',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$_SESSION[User_ID]',ct.CT_Update_Time=sysdate(),
			       thlolad.THLOLAD_Delete_UserID='$_SESSION[User_ID]',thlolad.THLOLAD_Delete_Time=sysdate(),
			       thlolad.THLOLAD_Update_UserID='$_SESSION[User_ID]',thlolad.THLOLAD_Update_Time=sysdate()
			   WHERE thlolad.THLOLAD_ID='$_POST[txtTDLOLAD_THLOLAD_ID]'
			   AND thlolad.THLOLAD_LoanCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=loan-of-land-acquisition-document.php'>";
	}
}

elseif(isset($_POST[addheader])) {
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
			  WHERE Company_ID='$_POST[optTHLOLAD_CompanyID]'";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$Company_Code=$field['Company_Code'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup 
			  WHERE DocumentGroup_ID ='3'";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Permintaan Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo) 
			  FROM M_CodeTransaction 
			  WHERE CT_Year='$regyear' 
			  AND CT_Action='REQ'
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
	
	// Kode Permintaan Dokumen	
	$CT_Code="$newnum/REQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";
	
	// Insert kode Permintaan dokumen baru
	$sql= "INSERT INTO M_CodeTransaction 
		   VALUES (NULL,'$CT_Code','$nnum','REQ','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$_SESSION[User_ID]',sysdate(),'$_SESSION[User_ID]',sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		$info=str_replace("<br>", "\n",$_POST['txtTHLOLAD_Information']);
		//Insert Header Dokumen 
		$sql1= "INSERT INTO TH_LoanOfLandAcquisitionDocument 
				VALUES (NULL,'$CT_Code',sysdate(),'$_SESSION[User_ID]','$_POST[optTHLOLAD_LoanCategoryID]',
				        '$_POST[optTHLOLAD_CompanyID]','$info', '0', NULL,
						'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=loan-of-land-acquisition-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$count=$_POST[countRow];
	$txtTHLOLAD_Information=str_replace("<br>", "\n",$_POST[txtTHLOLAD_Information]);

	for ($i=1 ; $i<=$count ; $i++) {
		$txtTDLOLAD_DocCode=$_POST["txtTDLOLAD_DocCode".$i];
		$txtTDLOLAD_Information=str_replace("<br>", "\n",$_POST["txtTDLOLAD_Information".$i]);
		$optTDLOLAD_Phase=$_POST["optTDLOLAD_Phase".$i];

		$sql1= "INSERT INTO TD_LoanOfLandAcquisitionDocument 
				VALUES (NULL,NULL,'$_POST[txtTDLOLAD_THLOLAD_ID]', '$optTDLOLAD_Phase', 
						'$txtTDLOLAD_DocCode','$txtTDLOLAD_Information', '0','$_SESSION[User_ID]', 
						sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		$mysqli->query($sql1);
	
		switch ($_POST[optTHLOLAD_LoanCategoryID]) {
			case "1":
				$docStatus="2";
				break;
			case "2":
				$docStatus="2";
				break;
			case "3":
				$docStatus="1";
				break;
		}
		$sql5= "UPDATE M_DocumentLandAcquisition dla, M_DocumentLandAcquisitionAttribute dlaa
				SET dla.DLA_Status ='$docStatus',dla.DLA_Update_Time=sysdate(),
				    dla.DLA_Update_UserID='$_SESSION[User_ID]',
					dlaa.DLAA_Status ='2',dlaa.DLAA_Update_Time=sysdate(),
					dlaa.DLAA_Update_UserID='$_SESSION[User_ID]'
				WHERE dla.DLA_Code='$txtTDLOLAD_DocCode'
				AND dlaa.DLAA_DLA_ID=dla.DLA_ID";
		$mysqli->query($sql5);	
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$_SESSION['User_ID']){
				$appbefquery="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$_POST[txtTDLOLAD_THLOLAD_LoanCode]'
							  AND A_ApproverID='$txtA_ApproverID[$i]'";
				$appbefsql = mysql_query($appbefquery);
				$numappbef=mysql_num_rows($appbefsql);
				
				if ($numappbef=='0') {
					$step=$step+1;
					$sql2= "INSERT INTO M_Approval 
							VALUES (NULL,'$_POST[txtTDLOLAD_THLOLAD_LoanCode]', '$txtA_ApproverID[$i]', '$step', 
									'1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
					$mysqli->query($sql2);
					$sa_query="SELECT *
							   FROM M_Approval
							   WHERE A_TransactionCode='$_POST[txtTDLOLAD_THLOLAD_LoanCode]'
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
			}
		}
	}
	
	$sql3= "UPDATE M_Approval 
			SET A_Status='2', A_Update_UserID='$_SESSION[User_ID]',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDLOLAD_THLOLAD_LoanCode]' 
			AND A_Step='1'";
			
	$sql4= "UPDATE TH_LoanOfLandAcquisitionDocument 
			SET THLOLAD_Status='waiting', THLOLAD_Information='$txtTHLOLAD_Information',
			    THLOLAD_Update_UserID='$_SESSION[User_ID]',THLOLAD_Update_Time=sysdate()
			WHERE THLOLAD_LoanCode='$_POST[txtTDLOLAD_THLOLAD_LoanCode]'
			AND THLOLAD_Delete_Time IS NULL";

	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		mail_loan_doc($_POST['txtTDLOLAD_THLOLAD_LoanCode']);
		echo "<meta http-equiv='refresh' content='0; url=loan-of-land-acquisition-document.php'>";
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
						  
	// TAHAP GRL
	var cell0 = row.insertCell(0);
	var sel = document.createElement('select');
	sel.name = 'optTDLOLAD_Phase' + iteration;
	sel.id = 'optTDLOLAD_Phase' + iteration;
	sel.options[0] = new Option('--- Pilih Tahap GRL ---', '0');
	<?PHP
		$query5="SELECT DISTINCT DLA_Phase
				   FROM M_DocumentLandAcquisition
				   WHERE DLA_Delete_Time IS NULL
				   AND DLA_CompanyID='$DLA_CompanyID'
				   AND DLA_Status='1'
				   ORDER BY DLA_Phase";
 		$sql5 = mysql_query($query5);
		$i = 1;
		
		while ($field5=mysql_fetch_array($sql5)) {
			$s_tmp = "sel.options[$i] = new Option('Tahap $field5[0]','$field5[0]');";
			echo $s_tmp;
			$i++;
		}
	?>
	cell0.appendChild(sel);

	// KODE DOKUMEN
	var cell1 = row.insertCell(1);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDLOLAD_DocCode' + iteration;
	el.id = 'txtTDLOLAD_DocCode' + iteration;
	el.value= '';
	//el.setAttribute("onclick","showList("+iteration+")");
	el.onclick=function(){ showList(""+iteration+"");  };
	el.readOnly = true;
	cell1.appendChild(el);
	
	// INFORMASI PERMINTAAN
	var cell2 = row.insertCell(2);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDLOLAD_Information' + iteration;
	el.id = 'txtTDLOLAD_Information' + iteration;
	cell2.appendChild(el);
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