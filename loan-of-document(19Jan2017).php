<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.3.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 04 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
= 		23/05/2012	: Validasi keterangan dihilangkan. (OK)																=
=					  Kategori dokumen dipindahkan ke bagian detail peminjaman -> Perubahan Struktur DB (OK)			=
=					  Button "Cancel" untuk detail transaksi (OK)														=
= 		31/05/2012	: Persetujuan transaksi via email & email notifikasi. (OK)											=
= 		19/06/2012	: List Of Value utk Dokumen. (OK)																	=
=		19/09/2012	: Perubahan Reminder Email																			=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start(); 
?>
<title>Custodian System | Permintaan Dokumen</title>
<head>
<?PHP 
include ("./config/config_db.php"); 
include ("./include/function.mail.lodoc.php");
?>

<script language="JavaScript" type="text/JavaScript">
// MENAMPILKAN GRUP DOKUMEN DARI PERUSAHAAN YANG DIPILIH
function showGroup() {
 	$.post("jQuery.LoanDocument.php", {
		grup : "non_grl",
		optTHLOLD_CompanyID : $('#optTHLOLD_CompanyID').val()
	}, function(response){
		
		setTimeout("finishAjax('optTHLOLD_DocumentGroupID', '"+escape(response)+"')", 400);
	});
}
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
} 

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;							

	var optTHLOLD_LoanCategoryID = document.getElementById('optTHLOLD_LoanCategoryID').selectedIndex;
	var optTHLOLD_CompanyID = document.getElementById('optTHLOLD_CompanyID').selectedIndex;
	var optTHLOLD_DocumentGroupID = document.getElementById('optTHLOLD_DocumentGroupID').selectedIndex;
	var txtTHLOLD_Information = document.getElementById('txtTHLOLD_Information').value;
		
		if(optTHLOLD_LoanCategoryID == 0) {
			alert("Tipe Permintaan Belum Dipilih!");
			returnValue = false;
		}
		if(optTHLOLD_CompanyID == 0) {
			alert("Perusahaan Belum Dipilih!");
			returnValue = false;
		}
		if(optTHLOLD_DocumentGroupID == 0) {
			alert("Grup Dokumen Belum Dipilih!");
			returnValue = false;
		}
		if (txtTHLOLD_Information.replace(" ", "") == "")  {	
			alert("Alasan Permintaan Belum Diisi!");
			returnValue = false;
		}
	return returnValue;
}

//LoV UTK DAFTAR DOKUMEN
function showList(row) {
	var txtTHLOLD_CompanyID = document.getElementById('txtTHLOLD_CompanyID').value;
	var txtTHLOLD_DocumentGroupID = document.getElementById('txtTHLOLD_DocumentGroupID').value;
	var optTDLOLD_DocumentCategoryID = document.getElementById('optTDLOLD_DocumentCategoryID' + row).value;
	var docCode = document.getElementById('docCode').value;
	//var endocCode = base64.encode(docCode);
	//alert (endocCode);
	if (optTDLOLD_DocumentCategoryID=="0")
		alert ("Pilih Kategori Dokumen Pada Baris ke-"+row+" Terlebih Dahulu");
	else
		sList = window.open("popupDoc.php?row="+row+"&cID="+txtTHLOLD_CompanyID+"&gID="+txtTHLOLD_DocumentGroupID+"&catID="+optTDLOLD_DocumentCategoryID+"&recentCode="+docCode+"", "Daftar_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
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
	var txtTHLOLD_CompanyID = document.getElementById('txtTHLOLD_CompanyID').value;
	var txtTHLOLD_DocumentGroupID = document.getElementById('txtTHLOLD_DocumentGroupID').value;
	var txtTHLOLD_Information = document.getElementById('txtTHLOLD_Information').value;
		
		if (txtTHLOLD_Information.replace(" ", "") == "")  {	
			alert("Alasan Permintaan Belum Diisi!");
			returnValue = false;
		}
		
	for (i = 1; i <= jrow; i++){
		checkDocCode = 0;
		var txtTDLOLD_DocumentCode = document.getElementById('txtTDLOLD_DocumentCode' + i).value;
		var optTDLOLD_DocumentCategoryID = document.getElementById('optTDLOLD_DocumentCategoryID' + i).value;
		
		if (optTDLOLD_DocumentCategoryID == "0")  {	
			alert("Kategori Dokumen pada baris ke-" + i + " Belum Dipilih!");
			returnValue = false;
		}
		if (txtTDLOLD_DocumentCode.replace(" ", "") == "")  {	
			alert("Kode Dokumen pada baris ke-" + i + " Belum Terisi!");
			returnValue = false;
		}
		else {
			<?php
 			$query = "SELECT * 
					  FROM M_DocumentLegal
					  WHERE DL_Status ='1'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) { 
				$DocumentCode = $data['DL_DocCode'];
				$DL_CompanyID = $data['DL_CompanyID'];
				$DL_GroupDocID = $data['DL_GroupDocID'];
				$DL_CategoryDocID = $data['DL_CategoryDocID'];
				
				$a = "if ( (txtTDLOLD_DocumentCode == '$DocumentCode') 
							&& (txtTHLOLD_CompanyID == '$DL_CompanyID') 
							&& (txtTHLOLD_DocumentGroupID == '$DL_GroupDocID') 
							&& (optTDLOLD_DocumentCategoryID == '$DL_CategoryDocID') 
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
//die;
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
			<th colspan=3>Permintaan Dokumen</th>
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
				<input name='txtTHLOLD_UserID' type='hidden' value='$_SESSION[User_ID]'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtTHLOLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtTHLOLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input name='txtTHLOLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";
		
		if($field['User_SPV1']||$field['User_SPV2']){
			$query="SELECT DISTINCT c.Company_ID, c.Company_Name 
					FROM M_DocumentLegal dl
					INNER JOIN M_Company c
						ON dl.DL_CompanyID = c.Company_ID
					WHERE dl.DL_Delete_Time is NULL
					AND dl.DL_Status='1'
					ORDER BY c.Company_Name ASC";
			$sql = mysql_query($query);
			$number=mysql_num_rows($sql);
					
			if ($number>0) {
				$ActionContent .="	
				<tr>
					<td>Kategori Permintaan</td>
					<td> 
						<select name='optTHLOLD_LoanCategoryID' id='optTHLOLD_LoanCategoryID'>
							<option value='0'>--- Pilih Kategori Permintaan ---</option>";

						$query1= "SELECT * 
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
						<select name='optTHLOLD_CompanyID' id='optTHLOLD_CompanyID' onChange='showGroup()' style='width:350px'>
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
					<td>Grup Dokumen</td>
					<td> 
						<select name='optTHLOLD_DocumentGroupID' id='optTHLOLD_DocumentGroupID'>
							<option value='0'> - Pilih Perusahaan Terlebih Dahulu- </option>
						</select>
					</td>
				</tr>
				<tr>
					<td valign='top'>Alasan Permintaan</td>
					<td><textarea name='txtTHLOLD_Information' id='txtTHLOLD_Information' cols='50' rows='2'></textarea></td>
				</tr>
				<tr>
					<th colspan=3>
						<input name='addheader' type='submit' value='Simpan' class='button' onclick='return validateInputHeader(this);'/>
						<input name='cancel' type='submit' value='Batal' class='button' />
					</th>
				</tr>";
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
		$ActionContent .="				
		</table>
		</form>";
	}
	
	//Menambah Detail Permintaan Dokumen
	elseif($act=='adddetail')	{ 
		$id=$_GET["id"];
		
		$query = "SELECT thlold.THLOLD_ID, 
						 thlold.THLOLD_LoanCode, 
						 thlold.THLOLD_LoanDate, 
						 u.User_FullName,
						 d.Division_Name,
						 de.Department_Name,
						 p.Position_Name,
						 c.Company_Name, c.Company_ID, c.Company_Area,
						 lc.LoanCategory_Name,
						 lc.LoanCategory_ID,
						 dg.DocumentGroup_Name, dg.DocumentGroup_ID,
						 thlold.THLOLD_Information
				  FROM TH_LoanOfLegalDocument thlold
				  LEFT JOIN M_User u
					ON thlold.THLOLD_UserID=u.User_ID
					AND thlold.THLOLD_UserID='$_SESSION[User_ID]'
				  LEFT JOIN M_Company c
					ON thlold.THLOLD_CompanyID=c.Company_ID 
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON ddp.DDP_UserID=u.User_ID
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN  M_Department de
					ON ddp.DDP_DeptID=de.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_DocumentGroup dg
					ON thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
				  LEFT JOIN M_LoanCategory lc
					ON thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID
				  WHERE thlold.THLOLD_Delete_Time is NULL 
				  AND thlold.THLOLD_LoanCode='$id'";
		$field = mysql_fetch_array(mysql_query($query));
		
		$DL_CompanyID=$field['Company_ID'];
		$DocumentGroup_ID=$field['DocumentGroup_ID'];
		$floandate=date("j M Y", strtotime($field['THLOLD_LoanDate']));

		$ActionContent ="
		<form name='adddetaildoc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Permintaan Dokumen</th>
		</tr>
		<tr>
			<td width='30'>No Permintaan</td>
			<td width='70%'>
				<input name='txtTDLOLD_THLOLD_ID' type='hidden' value='$field[THLOLD_ID]'/>
				<input type='hidden' name='txtTDLOLD_THLOLD_LoanCode' value='$field[THLOLD_LoanCode]'/>
				$field[THLOLD_LoanCode]
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
				<input id='txtTHLOLD_CompanyID' type='hidden' value='$DL_CompanyID'/>
				<input id='txtCompArea' name='txtCompArea' type='hidden' value='$field[Company_Area]'/>
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
			<td>Grup Dokumen</td>
			<td>
				<input name='txtGroupID' id='txtTHLOLD_DocumentGroupID' type='hidden' value='$DocumentGroup_ID'/>
				$field[DocumentGroup_Name]
			</td>
		</tr>
		<tr>
			<td valign='top'>Alasan Permintaan</td>
			<td>
				<textarea name='txtTHLOLD_Information' id='txtTHLOLD_Information' cols='50' rows='2'>$field[THLOLD_Information]</textarea>
			</td>
		</tr>
		</table>
		<input name='docCode' id='docCode' type='hidden' value=''/>
		<div style='space'>&nbsp;</div>
		
		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th>Kategori Dokumen</th>
			<th>Kode Dokumen</th>
			<th>Keterangan Permintaan</th>
		</tr>
		<tr>
			<td>
				<select name='optTDLOLD_DocumentCategoryID1' id='optTDLOLD_DocumentCategoryID1'>
					<option value='0'>--- Pilih Kategori Dokumen ---</option>";
				$sc_query="SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
						   FROM M_DocumentLegal dl
						   LEFT JOIN M_DocumentCategory dc
								ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
						   WHERE dl.DL_Delete_Time IS NULL
						   AND dl.DL_CompanyID='$DL_CompanyID'
						   AND dl.DL_GroupDocID='$DocumentGroup_ID'				   
						   AND dl.DL_Status='1'
						   ORDER BY dc.DocumentCategory_Name";
				$sc_sql = mysql_query($sc_query);
				while ($sc_arr=mysql_fetch_array($sc_sql)){
					$ActionContent .="
					<option value='$sc_arr[DocumentCategory_ID]'>$sc_arr[DocumentCategory_Name]</option>";
				}

		$ActionContent .="
				</select>
			</td>
			<td>
			<input type='text' name='txtTDLOLD_DocumentCode1' id='txtTDLOLD_DocumentCode1' value='' readonly='readonly' onClick='javascript:showList(1);'>

			</td>
			<td>
				<textarea name='txtTDLOLD_Information1' id='txtTDLOLD_Information1' cols='20' rows='1'></textarea>
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

			$result = array();

			for($sApp=1;$sApp<10;$sApp++) {
				//ATASAN LANGSUNG
				$query="SELECT User_SPV1,User_SPV2
						FROM M_User
						WHERE User_ID='$user'";
						//echo $query; echo '<br />';
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
				$canApprove=mysql_num_rows(mysql_query($query));
				
				if($canApprove){
					$user = $atasan;
					array_push($result, $user);
					$ats = $user;
					break;
				}else{
					$user = $atasan;
					$sApp=3;
				}						
			}

			$obj1 = mysql_fetch_object(mysql_query("SELECT User_SPV1, User_SPV2 FROM M_User WHERE User_ID = '$ats'"));
			array_push($result, $obj1->User_SPV1);
			
			/*$query="SELECT a.Approver_UserID
					FROM M_Approver a
					LEFT JOIN M_Role_Approver ra
						ON ra.RA_ID=a.Approver_RoleID
					WHERE a.Approver_Delete_Time is NULL
					ORDER BY ra.RA_ID";*/

			if ($field['DocumentGroup_ID'] == '1' && $field['LoanCategory_ID'] != '3') { $jenis = '1'; $proses = '2'; }
			else if ($field['DocumentGroup_ID'] == '1' && $field['LoanCategory_ID'] == '3') { $jenis = '2'; $proses = '2'; }
			else if ($field['DocumentGroup_ID'] == '2' && $field['LoanCategory_ID'] != '3') { $jenis = '3'; $proses = '2'; }
			else if ($field['DocumentGroup_ID'] == '2' && $field['LoanCategory_ID'] == '3') { $jenis = '4'; $proses = '2'; }
			else;

			$query = "
				SELECT ma.Approver_UserID, rads.RADS_StepID
				FROM M_Role_ApproverDocStepStatus rads
				LEFT JOIN M_Role_Approver ra
					ON rads.RADS_RA_ID = ra.RA_ID
				LEFT JOIN M_Approver ma
					ON ra.RA_ID = ma.Approver_RoleID
				WHERE rads.RADS_DocID = '{$jenis}'
					AND rads.RADS_ProsesID = '{$proses}'
					AND ma.Approver_Delete_Time IS NULL
					AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$field['Company_Area']}')
					ORDER BY rads.RADS_StepID
			";
			$sql=mysql_query($query);

			$output = array();
			while($obj=mysql_fetch_object($sql)){
				$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				//$ActionContent .="
				//<input type='text' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}
			//print_r ($output);
			// AKHIR PROSES APPROVAL
			
			$i = 0;
			$newArray = array();
			foreach ($output as $k => $v) {
				if ($v == '0') { $newArray[$k] = $result[$i]; $i++; } else { $newArray[$k] = $v; }
			}

			$key = array_search('', $newArray);
			if (false !== $key) unset($newArray[$key]);

			foreach ($newArray as $key => $value) {
				$ActionContent .= "<input type='hidden' name='txtA_ApproverID[$key]' value='$value' readonly='true' class='readonly' />";
			}
			
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
		echo "<meta http-equiv='refresh' content='0; url=loan-of-document.php'>";
	}
}
	
// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page'])) 
    $noPage = $_GET['page'];
else 
	$noPage = 1;
	
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT thlold.THLOLD_ID, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate, u.User_FullName,
 		  		 c.Company_Name, lc.LoanCategory_Name,drs.DRS_Description, thlold.THLOLD_Status
		  FROM TH_LoanOfLegalDocument thlold, M_User u, M_Company c, M_LoanCategory lc, 
		  	   M_DocumentRegistrationStatus drs
		  WHERE thlold.THLOLD_Delete_Time is NULL 
		  AND thlold.THLOLD_CompanyID=c.Company_ID 
		  AND thlold.THLOLD_UserID=u.User_ID 
		  AND u.User_ID='$_SESSION[User_ID]' 
		  AND thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID
		  AND thlold.THLOLD_Status=drs.DRS_Name
		  ORDER BY thlold.THLOLD_ID  DESC
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
		$loandate=strtotime($field['THLOLD_LoanDate']);
		$floandate=date("j M Y", $loandate);
		$resend=($field['THLOLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-loan-document.php?id=$field[0]' class='underline'>$field[1]</a>
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

$query1 = "SELECT thlold.THLOLD_ID, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate, u.User_FullName,
 		  		  c.Company_Name, lc.LoanCategory_Name
		   FROM TH_LoanOfLegalDocument thlold, M_User u, M_Company c, M_LoanCategory lc
		   WHERE thlold.THLOLD_Delete_Time is NULL 
		   AND thlold.THLOLD_CompanyID=c.Company_ID 
		   AND thlold.THLOLD_UserID=u.User_ID 
		   AND u.User_ID='$_SESSION[User_ID]' 
		   AND thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID";
$num1 = mysql_num_rows(mysql_query($query1));

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
	echo "<meta http-equiv='refresh' content='0; url=loan-of-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_LoanOfLegalDocument thlold
			   SET ct.CT_Delete_UserID='$_SESSION[User_ID]',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$_SESSION[User_ID]',ct.CT_Update_Time=sysdate(),
			       thlold.THLOLD_Delete_UserID='$_SESSION[User_ID]',thlold.THLOLD_Delete_Time=sysdate(),
			       thlold.THLOLD_Update_UserID='$_SESSION[User_ID]',thlold.THLOLD_Update_Time=sysdate()
			   WHERE thlold.THLOLD_ID='$_POST[txtTDLOLD_THLOLD_ID]'
			   AND thlold.THLOLD_LoanCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=loan-of-document.php'>";
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
			  WHERE Company_ID='$_POST[optTHLOLD_CompanyID]'";
	$field = mysql_fetch_array(mysql_query($query));
	$Company_Code=$field['Company_Code'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup 
			  WHERE DocumentGroup_ID ='$_POST[optTHLOLD_DocumentGroupID]'";
	$field = mysql_fetch_array(mysql_query($query));
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Permintaan Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo) 
			  FROM M_CodeTransaction 
			  WHERE CT_Year='$regyear' 
			  AND CT_Action='REQ'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$field = mysql_fetch_array(mysql_query($query));
	
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
		//Insert Header Dokumen 
		$info = str_replace("<br>", "\n", $_POST['txtTHLOLD_Information']);
		$sql1= "INSERT INTO TH_LoanOfLegalDocument 
				VALUES (NULL,'$CT_Code',sysdate(),'$_SESSION[User_ID]','$_POST[optTHLOLD_LoanCategoryID]',
				        '$_POST[optTHLOLD_CompanyID]','$_POST[optTHLOLD_DocumentGroupID]',
						'$info', '0', NULL,
						'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=loan-of-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$count=$_POST[countRow];
	$txtTHLOLD_Information=str_replace("<br>", "\n",$_POST[txtTHLOLD_Information]);
	$A_TransactionCode = $_POST['txtTDLOLD_THLOLD_LoanCode'];
	$A_ApproverID=$_SESSION['User_ID'];
	
	for ($i=1 ; $i<=$count ; $i++) {
		$txtTDLOLD_DocumentCode=$_POST["txtTDLOLD_DocumentCode".$i];
		$txtTDLOLD_Information=str_replace("<br>", "\n",$_POST["txtTDLOLD_Information".$i]);
		$optTDLOLD_DocumentCategoryID=$_POST["optTDLOLD_DocumentCategoryID".$i];

		$sql1= "INSERT INTO TD_LoanOfLegalDocument 
			VALUES (NULL,NULL,'$_POST[txtTDLOLD_THLOLD_ID]', '$optTDLOLD_DocumentCategoryID', 
			'$txtTDLOLD_DocumentCode','$txtTDLOLD_Information', '0','$A_ApproverID', sysdate(),'$A_ApproverID', sysdate(),NULL,NULL)";
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
		
		$sql5= "UPDATE M_DocumentLegal 
			    SET DL_Status ='$docStatus',DL_Update_UserID='$A_ApproverID',DL_Update_Time=sysdate()
			    WHERE DL_DocCode='$txtTDLOLD_DocumentCode'";
		$mysqli->query($sql5);	
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];

	foreach ($txtA_ApproverID as $k1=>$v1) {
		$l = $k1 + 1;
		$select = "
			SELECT RADS_StepID FROM M_Role_ApproverDocStepStatus
			WHERE RADS_ProsesID = '2' AND RADS_DocID = '2'
			AND (RADS_StepID = '$k1' OR RADS_StepID = '$l') AND RADS_StatusID = '2'
		";
		$sql3 = mysql_fetch_array(mysql_query($select));
		//unset($txtA_ApproverID[$sql3['RADS_StepID']]);
		if ($txtA_ApproverID[$k1] == $txtA_ApproverID[$l]) {
			unset($txtA_ApproverID[$sql3['RADS_StepID']]);
		}
	}

	$step = 0;
	foreach ($txtA_ApproverID as $k=>$v) {
		if ($txtA_ApproverID[$k]<>NULL) {
			if ($txtA_ApproverID[$k]<>$_SESSION['User_ID']) {
				$appbefquery = "SELECT * 
						FROM M_Approval
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_ApproverID='$txtA_ApproverID[$k]'
						AND A_Step='$l'";
				$numappbef = mysql_num_rows(mysql_query($appbefquery));

				if ($numappbef == '0') {
					$step=$step+1;
					$sql2 = "INSERT INTO M_Approval
						VALUES (NULL, '$A_TransactionCode', '$txtA_ApproverID[$k]', '$k',
						'1', NULL, '$A_ApproverID', sysdate(), '$A_ApproverID', sysdate(), NULL, NULL)";
					$mysqli->query($sql2);
					$sa_query = "SELECT *
							FROM M_Approval
							WHERE A_TransactionCode='$A_TransactionCode'
							AND A_ApproverID='$txtA_ApproverID[$k]'
							AND A_Step = '$k'
							AND A_Delete_Time IS NULL";
					$sa_arr = mysql_fetch_array(mysql_query($sa_query));
					$ARC_AID = $sa_arr['A_ID'];
					$str = rand(1,100);
					$RandomCode = crypt('T4pagri'.$str);
					$iSQL="INSERT INTO L_ApprovalRandomCode VALUES ('$ARC_AID', '$RandomCode')";
					$mysqli->query($iSQL);
				}
			}
		}
	}
	/*$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$_SESSION['User_ID']){
				$appbefquery="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$_POST[txtTDLOLD_THLOLD_LoanCode]'
							  AND A_ApproverID='$txtA_ApproverID[$i]'";
				$appbefsql = mysql_query($appbefquery);
				$numappbef=mysql_num_rows($appbefsql);
				
				if ($numappbef=='0') {
					$step=$step+1;
					$sql2= "INSERT INTO M_Approval 
							VALUES (NULL,'$_POST[txtTDLOLD_THLOLD_LoanCode]', '$txtA_ApproverID[$i]', '$step', 
									'1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
					$mysqli->query($sql2);
					$sa_query="SELECT *
							   FROM M_Approval
							   WHERE A_TransactionCode='$_POST[txtTDLOLD_THLOLD_LoanCode]'
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
	}*/
	
	// MENCARI JUMLAH APPROVAL
	$query = "SELECT MAX(A_Step) AS jStep
		FROM M_Approval
		WHERE A_TransactionCode='$A_TransactionCode'";
	$arr = mysql_fetch_array(mysql_query($query));
	$jStep=$arr['jStep'];

	if ($_POST['txtGroupID'] == '1' && $_POST['optTHLOLAD_LoanCategoryID'] != '3') { $jenis = '1'; } 
	else if ($_POST['txtGroupID'] == '1' && $_POST['optTHLOLAD_LoanCategoryID'] == '3') { $jenis = '2'; }
	else if ($_POST['txtGroupID'] == '2' && $_POST['optTHLOLAD_LoanCategoryID'] != '3') { $jenis = '3'; }
	else if ($_POST['txtGroupID'] == '2' && $_POST['optTHLOLAD_LoanCategoryID'] == '3') { $jenis = '4'; }
	else;

	for ($i=1; $i<=$jStep; $i++) {
		$query ="
			SELECT rads.RADS_StatusID, ma.A_ApproverID 
			FROM M_Approval ma
			JOIN M_Role_ApproverDocStepStatus rads
				ON ma.A_Step = rads.RADS_StepID
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			WHERE ma.A_Step = '{$i}' 
				AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$_POST['txtCompArea']}')
				AND ma.A_TransactionCode = '{$A_TransactionCode}'
				AND rads.RADS_DocID = '{$jenis}'
				AND rads.RADS_ProsesID = '2'
		";
		$result = mysql_fetch_array(mysql_query($query));

		if ($result['RADS_StatusID'] == '1') {
			$query = "UPDATE M_Approval
				SET A_Status = '2', A_Update_UserID = '$A_ApproverID', A_Update_Time = sysdate() 
				WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_loan_doc($A_TransactionCode);
			}
			break;
		} else if ($result['RADS_StatusID'] == '2') {
			$query = "UPDATE M_Approval 
				SET A_Status = '3', A_Update_UserID = '$A_ApproverID', A_ApprovalDate = sysdate(), A_Update_Time = sysdate() 
				WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_notif_loan_doc($A_TransactionCode, $result['A_ApproverID'], 3);
			}
		}
	}

	/*$sql3= "UPDATE M_Approval 
			SET A_Status='2', A_Update_UserID='$_SESSION[User_ID]',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDLOLD_THLOLD_LoanCode]' 
			AND A_Step='1'";*/
			
	$sql4= "UPDATE TH_LoanOfLegalDocument 
		SET THLOLD_Status='waiting', THLOLD_Information='$txtTHLOLD_Information',
		THLOLD_Update_UserID='$A_ApproverID',THLOLD_Update_Time=sysdate()
		WHERE THLOLD_LoanCode='$A_TransactionCode'
		AND THLOLD_Delete_Time IS NULL";
	$mysqli->query($sql4);

	/*if($mysqli->query($sql4)) {
		// Kirim Email ke Approver 1
		mail_loan_doc($_POST['txtTDLOLD_THLOLD_LoanCode']);
	}*/
	echo "<meta http-equiv='refresh' content='0; url=loan-of-document.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
function klik(ff){ 
showList(ff); 
}

// TAMBAH BARIS
function addRowToTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	document.getElementById('countRow').value = (document.getElementById('countRow').value*1) + 1;
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);
						  
	// KATEGORI DOKUMEN
	var cell0 = row.insertCell(0);
	var sel = document.createElement('select');
	sel.name = 'optTDLOLD_DocumentCategoryID' + iteration;
	sel.id = 'optTDLOLD_DocumentCategoryID' + iteration;
	sel.options[0] = new Option('--- Pilih Kategori Dokumen ---', '0');
	<?PHP
		$query5 = "SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
				   FROM M_DocumentLegal dl, M_DocumentCategory dc
				   WHERE dl.DL_Delete_Time IS NULL
				   AND dl.DL_CompanyID='$DL_CompanyID'
				   AND dl.DL_GroupDocID='$DocumentGroup_ID'
				   AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
				   AND dl.DL_Status='1'
				   ORDER BY dc.DocumentCategory_Name";
 		$sql5 = mysql_query($query5);
		$i = 1;
		
		while ($field5=mysql_fetch_array($sql5)) {
			$s_tmp = "sel.options[$i] = new Option('$field5[1]','$field5[0]');";
			echo $s_tmp;
			$i++;
		}
	?>
	cell0.appendChild(sel);

	// KODE DOKUMEN
	var cell1 = row.insertCell(1);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDLOLD_DocumentCode' + iteration;
	el.id = 'txtTDLOLD_DocumentCode' + iteration;
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
	el.name = 'txtTDLOLD_Information' + iteration;
	el.id = 'txtTDLOLD_Information' + iteration;
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