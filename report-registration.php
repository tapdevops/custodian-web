<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 05 Juni 2012																						=
= Update Terakhir	: 05 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Laporan Pendaftaran Dokumen</title>
<?PHP include ("./config/config_db.php"); ?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
// JQUERY UNTUK MENAMPILKAN TIPE DOKUMEN DARI KATEGORI DOKUMEN YANG DIPILIH
function showCategory(){
	$.post("jQuery.DocumentCategory.php", {
		GroupID: $('#optDocumentGroup').val()
	}, function(response){
		setTimeout("finishAjax('optDocumentCategory', '"+escape(response)+"')", 400);
	});
}
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
}

function showCompany(){
	$.post("jQuery.CompanyNameReport.php", {
		CompanyAreaID: document.getElementById('optArea').value,
		ReportOfReg: 'true'
	}, function(response){
		setTimeout("finishAjax('optCompany', '"+escape(response)+"')", 400);
	});
}

// VALIDASI INPUT UNTUK MENAMPILKAN LIST DOKUMEN
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optDocumentGroup = document.getElementById('optDocumentGroup').selectedIndex;

		if(optDocumentGroup == 0) {
			alert("Grup Dokumen Belum Dipilih!");
			returnValue = false;
		}


	return returnValue;
}

// VALIDASI INPUT UNTUK PRINT
function validatePrint(elem) {
	var returnValue;
	returnValue = true;

	var user1 = document.getElementById('user1').value;
	var user2 = document.getElementById('user2').value;
	var user3 = document.getElementById('user3').value;
	var user4 = document.getElementById('user4').value;

		if(user1.replace(" ", "") == "") {
			alert("Nama Pembuat Laporan Belum Ditentukan!");
			returnValue = false;
		}

		if(user2.replace(" ", "") == "") {
			alert("Nama Pemeriksa Laporan Belum Ditentukan!");
			returnValue = false;
		}

		if(user3.replace(" ", "") == "") {
			alert("Nama Pemberi Persetujuan 1 Belum Ditentukan!");
			returnValue = false;
		}

		if(user4.replace(" ", "") == "") {
			alert("Nama Pemberi Persetujuan 2 Belum Ditentukan!");
			returnValue = false;
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

if(isset($_GET['page']))
	$noPage = $_GET['page'];
else
	$noPage = 1;

if(!empty($_POST['optArea'])){
	$optArea = $_POST['optArea'];
}else{
	$optArea = $_GET['optArea'];
}

if(!empty($_POST['optCompany'])){
	$optCompany = $_POST['optCompany'];
}else{
	$optCompany = $_GET['optCompany'];
}

if(!empty($_POST['optDocumentGroup'])){
	$optDocumentGroup = $_POST['optDocumentGroup'];
}else{
	$optDocumentGroup = $_GET['optDocumentGroup'];
}

if(!empty($_POST['optDocumentCategory'])){
	$optDocumentCategory = $_POST['optDocumentCategory'];
}else{
	$optDocumentCategory = $_GET['optDocumentCategory'];
}

if(!empty($_POST['txtStart'])){
	$tStart = $_POST['txtStart'];
}else{
	$tStart = $_GET['txtStart'];
}

if(!empty($_POST['txtEnd'])){
	$tEnd = $_POST['txtEnd'];
}else{
	$tEnd = $_GET['txtEnd'];
}

if(!empty($_POST['listdocument'])){
	$listdocument = $_POST['listdocument'];
}else{
	$listdocument = $_GET['listdocument'];
}

$ActionContent ="
	<form name='list' method='post' action='$PHP_SELF'>
	<input type='hidden' name='page' value='$noPage' />
	<table width='100%'>
	<tr>
		<td width='9%'>Area</td>
		<td width='1%'>:</td>
		<td width='65%'>
			<select name='optArea' id='optArea' onchange='return showCompany()'>
			<option value=''>--- Semua Area ---</option>";
			$query="SELECT DISTINCT Company_ID_Area, Company_Area
					FROM M_Company
					WHERE Company_Delete_Time is NULL
					AND Company_Area != ''
					ORDER BY Company_Area";
			$result=mysql_query($query);

			while ($object = mysql_fetch_object($result) ){
				$selected_area = "";
				if(!empty($optArea)){
					if($optArea == $object->Company_ID_Area){
						$selected_area = "selected";
					}
				}
				$ActionContent .="<option value='".$object->Company_ID_Area."' $selected_area>".$object->Company_Area."</option>";
			}
$ActionContent.="
			</select>
		</td>
		<td width='25%' align='right'>
			<input name='listdocument' type='submit' value='Cari' class='button-small' onclick='return validateInput(this);'/>
		</td>
	</tr>
	<tr>
		<td>PT</td>
		<td>:</td>
		<td>
			<select name='optCompany' id='optCompany'>
				<option value='ALL'>--- Semua Perusahaan ---</option>
				<option value='88'>National</option>
";
			$c_query_additional = "";
			if(!empty($optArea)){
				$c_query_additional = "AND Company_ID_Area='$optArea'";
			}
			$c_query="SELECT Company_ID, UPPER(Company_Name) AS Company_Name
					  FROM M_Company
					  WHERE Company_Delete_Time is NULL
					  $c_query_additional
					  ORDER BY Company_Name";
			$c_sql=mysql_query($c_query);

			while ($c_arr = mysql_fetch_array($c_sql) ){
				$selected_company = "";
				if(!empty($optCompany)){
					if($optCompany == $c_arr['Company_ID']){
						$selected_company = "selected";
					}
				}
$ActionContent .="
				<option value='$c_arr[Company_ID]' $selected_company>$c_arr[Company_Name]</option>";
			}
$ActionContent .="
			</select>
		</td>

		<td>
			<input name='export_to_excel' formaction='result-report-registration-export-to-excel.php' type='submit' value='&nbsp;Export to Excel&nbsp;' class='button-small-blue' onclick='return validateInput(this);' />
		</td>
	</tr>
	<tr>
		<td>Grup</td>
		<td>:</td>
		<td colspan='2'>
			<select name='optDocumentGroup' id='optDocumentGroup' onchange='javascript:showCategory();'>
				<option value='0'>--- Pilih Grup Dokumen ---</option>";
			$g_query="SELECT DocumentGroup_ID, DocumentGroup_Name
					  FROM M_DocumentGroup
					  WHERE DocumentGroup_Delete_Time is NULL";
			$g_sql = mysql_query($g_query);

			while ($g_arr=mysql_fetch_array($g_sql) ){
				$selected_doc_group = "";
				if(!empty($optDocumentGroup)){
					if($optDocumentGroup == $g_arr['DocumentGroup_ID']){
						$selected_doc_group = "selected";
					}
				}
$ActionContent .="
				<option value='$g_arr[DocumentGroup_ID]' $selected_doc_group>$g_arr[DocumentGroup_Name]</option>";
			}
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Kategori</td>
		<td>:</td>
		<td colspan='2'>
			<select name='optDocumentCategory' id='optDocumentCategory'>";
			if(!empty($optDocumentGroup)){
				$GroupID = $optDocumentGroup;
				if($GroupID == '1' || $GroupID == '2' || $GroupID == '5'){
					if($GroupID == '5'){
						$query="SELECT DocumentCategory_ID, DocumentCategory_Name
							FROM db_master.M_DocumentCategory
							WHERE DocumentCategory_Delete_Time IS NULL";
					}else{
						$query="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name
							FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
							WHERE dgct.DGCT_DocumentGroupID='$GroupID'
							AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
							AND dgct.DGCT_Delete_Time is NULL";
					}
					$sql = mysql_query($query);
					$num=mysql_num_rows($query);
					if ($num=="0"){
						$ActionContent .="
						<option value='0'>--- Tidak Ada ---</option>";
					}else{
						$ActionContent .="
						<option value='0'>--- Pilih Kategori Dokumen ---</option>";
						while ($arr = mysql_fetch_array($sql)) {
							$selected_category = "";
							if(!empty($optDocumentCategory)){
								if($optDocumentCategory == $arr['DocumentCategory_ID']){
									$selected_category = "selected";
								}
							}
							$ActionContent .="
							<option value='".$arr['DocumentCategory_ID']."' style='width:500px' $selected_category>".$arr['DocumentCategory_Name']."</option>";
						}
					}
				}else{
					$ActionContent .="
					<option value='0'>--- Tidak Ada ---</option>";
				}
			}else{
				$ActionContent .="
				<option value='0'>--- Semua Kategori Dokumen ---</option>";
			}
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Periode</td>
		<td>:</td>";
		$val_txtStart = "";
		if(!empty($tStart)){
			$val_txtStart = "value='".$tStart."'";
		}
		$val_txtEnd = "";
		if(!empty($tEnd)){
			$val_txtEnd = "value='".$tEnd."'";
		}
		$ActionContent .="<td colspan='2'>
			<input type='text' size='10' name='txtStart' id='txtStart' $val_txtStart onclick=\"javascript:NewCssCal('txtStart', 'MMddyyyy');\"/>&nbsp;&nbsp;s/d <input type='text' size='10' name='txtEnd' id='txtEnd' $val_txtEnd onclick=\"javascript:NewCssCal('txtEnd', 'MMddyyyy');\"/>
		</td>
	</tr>
	</table>
	</form>
";

/* ====== */
/* ACTION */
/* ====== */

	if(isset($listdocument)) {


// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$txtStart=date('Y-m-d', strtotime($tStart))." 00:00:00";
$txtEnd=date('Y-m-d', strtotime($tEnd))." 23:59:59";

	if ($optDocumentGroup == '1' || $optDocumentGroup == '2'){
		$qcompany=($optCompany == "ALL")?"":"AND thrgold.THROLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND tdrgold.TDROLD_DocumentCategoryID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND thrgold.THROLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT thrgold.THROLD_ID, thrgold.THROLD_RegistrationCode, thrgold.THROLD_RegistrationDate,
						 		  u.User_FullName, dp.Department_Name, drs.DRS_Description,
						 		  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
						 		  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc,
			  		   TH_RegistrationOfLegalDocument thrgold, TD_RegistrationOfLegalDocument tdrgold,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp, M_DocumentRegistrationStatus drs
				  WHERE thrgold.THROLD_DocumentGroupID='$optDocumentGroup'
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thrgold.THROLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thrgold.THROLD_CompanyID=c.Company_ID
				  AND thrgold.THROLD_Status=drs.DRS_Name
				  AND tdrgold.TDROLD_THROLD_ID=thrgold.THROLD_ID
				  AND tdrgold.TDROLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thrgold.THROLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND thrgold.THROLD_Delete_Time IS NULL
				  ORDER BY thrgold.THROLD_ID LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='3'){
		$qcompany=($optCompany == "ALL")?"":"AND thrgolad.THRGOLAD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND thrgolad.THRGOLAD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT thrgolad.THRGOLAD_ID, thrgolad.THRGOLAD_RegistrationCode, drs.DRS_Description,
								  thrgolad.THRGOLAD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfLandAcquisitionDocument thrgolad,
				  	   TD_RegistrationOfLandAcquisitionDocument tdrgolad,M_DocumentRegistrationStatus drs,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  $qcompany
				  $qarea
				  $qperiod
				  AND thrgolad.THRGOLAD_CompanyID=c.Company_ID
				  AND thrgolad.THRGOLAD_UserID=u.User_ID
				  AND thrgolad.THRGOLAD_RegStatus=drs.DRS_Name
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
				  AND thrgolad.THRGOLAD_Delete_Time IS NULL
				  ORDER BY thrgolad.THRGOLAD_ID LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='4'){
		$qcompany=($optCompany == "ALL")?"":"AND th.THROAOD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND th.THROAOD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT th.THROAOD_ID, th.THROAOD_RegistrationCode, drs.DRS_Description,
								  th.THROAOD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfAssetOwnershipDocument th,
				  	   TD_RegistrationOfAssetOwnershipDocument td, M_DocumentRegistrationStatus drs,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  AND td.TDROAOD_THROAOD_ID=th.THROAOD_ID
				  $qcompany
				  $qarea
				  $qperiod
				  AND th.THROAOD_CompanyID=c.Company_ID
				  AND th.THROAOD_UserID=u.User_ID
				  AND th.THROAOD_Status=drs.DRS_Name
				  AND td.TDROAOD_THROAOD_ID=th.THROAOD_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND th.THROAOD_Delete_Time IS NULL
				  ORDER BY th.THROAOD_ID LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='5'){
		$qcompany=($optCompany == "ALL")?"":"AND th.THROOLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND td.TDROOLD_KategoriDokumenID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND th.THROOLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT th.THROOLD_ID, th.THROOLD_RegistrationCode, drs.DRS_Description,
								  th.THROOLD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfOtherLegalDocuments th,
				  	   TD_RegistrationOfOtherLegalDocuments td, M_DocumentRegistrationStatus drs,
					   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  AND td.TDROOLD_THROOLD_ID=th.THROOLD_ID
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND th.THROOLD_CompanyID=c.Company_ID
				  AND th.THROOLD_UserID=u.User_ID
				  AND th.THROOLD_Status=drs.DRS_Name
				  AND td.TDROOLD_THROOLD_ID=th.THROOLD_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND th.THROOLD_Delete_Time IS NULL
				  ORDER BY th.THROOLD_ID LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='6'){
		$qcompany=($optCompany == "ALL")?"":"AND th.THROONLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND th.THROONLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT th.THROONLD_ID, th.THROONLD_RegistrationCode, drs.DRS_Description,
								  th.THROONLD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfOtherNonLegalDocuments th,
				  	   TD_RegistrationOfOtherNonLegalDocuments td, M_DocumentRegistrationStatus drs,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  AND td.TDROONLD_THROONLD_ID=th.THROONLD_ID
				  $qcompany
				  $qarea
				  $qperiod
				  AND th.THROONLD_CompanyID=c.Company_ID
				  AND th.THROONLD_UserID=u.User_ID
				  AND th.THROONLD_Status=drs.DRS_Name
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND th.THROONLD_Delete_Time IS NULL
				  ORDER BY th.THROONLD_ID LIMIT $offset, $dataPerPage";
	}
$sql = mysql_query($query);
$num = mysql_num_rows($sql);
$no = 1;

// echo $query;

	if ($optDocumentGroup == '1' || $optDocumentGroup == '2'){
		$qcompany=($optCompany == "ALL")?"":"AND thrgold.THROLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND tdrgold.TDROLD_DocumentCategoryID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND thrgold.THROLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
			$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>Nama Perusahaan</th>
					<th>Nama Dokumen</th>
					<th>Instansi Terkait</th>
					<th>Nomor Dokumen</th>
					<th>Tanggal Terbit</th>
					<th>Berlaku Sampai</th>
					<th>Keterangan 1</th>
					<th>Keterangan 2</th>
					<th>Keterangan 3</th>
				</tr>
				<tr>
					<td colspan='20' align='center'>Belum Ada Data</td>
				</tr>
				</table>
			";
		}
		if ($num<>NULL){
			$MainContent .="
				<form name='list' method='post' action='print-report-registration.php' target='_blank'>
						<input type='hidden' name='txtStart' value='$tStart'>
						<input type='hidden' name='txtEnd' value='$tEnd'>
						<input type='hidden' name='optCompany' value='$optCompany'>
						<input type='hidden' name='optArea' value='$optArea'>
						<input type='hidden' name='optDocumentCategory' value='$optDocumentCategory'>";
			while ($h_arr = mysql_fetch_array($sql)) {
			$regdate=date("j M Y", strtotime($h_arr['THROLD_RegistrationDate']));

			$MainContent .="
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='19%'>No. Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='25%'>
						$h_arr[THROLD_RegistrationCode]
					</td>
					<td width='19%'>Tanggal Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='30%'>
						$regdate
					</td>
				</tr>
				<tr>
					<td>Nama Pendaftar</td>
					<td>:</td>
					<td>
						$h_arr[User_FullName]
					</td>
					<td>Departemen</td>
					<td>:</td>
					<td>
						$h_arr[Department_Name]
					</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td>$h_arr[Company_Name]</td>
					<td>Status Pendaftaran</td>
					<td>:</td>
					<td>$h_arr[DRS_Description]</td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td>
						<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
					</td>
					<td>Kategori Dokumen</td>
					<td>:</td>
					<td>$h_arr[DocumentCategory_Name]</td>
				</tr>
				</table>
				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0' style='margin-bottom:5px;'>
				<tr>
					<th>Nama Perusahaan</th>
					<th>Nama Dokumen</th>
					<th>Instansi Terkait</th>
					<th>Nomor Dokumen</th>
					<th>Tanggal Terbit</th>
					<th>Berlaku Sampai</th>
					<th>Keterangan 1</th>
					<th>Keterangan 2</th>
					<th>Keterangan 3</th>
				</tr>
			";
				$d_query="SELECT dt.DocumentType_Name, tdrgold.TDROLD_Instance, tdrgold.TDROLD_DocumentNo,
								 tdrgold.TDROLD_DatePublication,tdrgold.TDROLD_DateExpired,
								 di1.DocumentInformation1_Name, di2.DocumentInformation2_Name,
					 			CASE WHEN tdrgold.TDROLD_Core_CompanyID IS NOT NULL
					 				THEN (SELECT c.Company_Name FROM M_Company c
					 						WHERE c.Company_ID = tdrgold.TDROLD_Core_CompanyID
					 					)
					 				ELSE (SELECT c.Company_Name FROM M_Company c
					 						WHERE c.Company_ID = thrgold.THROLD_CompanyID
					 					)
					 			END AS company_name
					  	  FROM TH_RegistrationOfLegalDocument thrgold, M_DocumentType dt,
						   	   TD_RegistrationOfLegalDocument tdrgold, M_DocumentInformation1 di1,
						   	   M_DocumentInformation2 di2
					 	  WHERE thrgold.THROLD_ID='$h_arr[THROLD_ID]'
						  AND tdrgold.TDROLD_THROLD_ID=thrgold.THROLD_ID
						  AND tdrgold.TDROLD_DocumentTypeID=dt.DocumentType_ID
						  AND tdrgold.TDROLD_DocumentInformation1ID=di1.DocumentInformation1_ID
						  AND tdrgold.TDROLD_DocumentInformation2ID=di2.DocumentInformation2_ID
						  AND tdrgold.TDROLD_Delete_Time IS NULL ";
				$d_sql=mysql_query($d_query);

				while ($arr = mysql_fetch_array($d_sql)) {
				$berlaku=date("j M Y", strtotime($arr['TDROLD_DatePublication']));
				if ($arr['TDROLD_DateExpired']=="0000-00-00 00:00:00")
					$expdate="-";
				else
					$expdate=date("j M Y", strtotime($arr['TDROLD_DateExpired']));
			$MainContent .="
				<tr>
					<td class='center'>$arr[company_name]</td>
					<td class='center'>$arr[DocumentType_Name]</td>
					<td class='center'>$arr[TDROLD_Instance]</td>
					<td class='center'>$arr[TDROLD_DocumentNo]</td>
					<td class='center'>$berlaku</td>
					<td class='center'>$expdate</td>
					<td class='center'>$arr[DocumentInformation1_Name]</td>
					<td class='center'>$arr[DocumentInformation2_Name]</td>
					<td class='center'>$arr[TDROLD_DocumentInformation3]</td>
				</tr>
			";
				}
			$MainContent .="
				</table>
			";
			}
		}
		$query1= "SELECT DISTINCT thrgold.THROLD_ID, thrgold.THROLD_RegistrationCode, thrgold.THROLD_RegistrationDate,
						 u.User_FullName, dp.Department_Name,
						 dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
						 dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc,
			  		   TH_RegistrationOfLegalDocument thrgold, TD_RegistrationOfLegalDocument tdrgold,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE thrgold.THROLD_DocumentGroupID='$optDocumentGroup'
				  AND tdrgold.TDROLD_THROLD_ID=thrgold.THROLD_ID
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thrgold.THROLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thrgold.THROLD_CompanyID=c.Company_ID
				  AND tdrgold.TDROLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thrgold.THROLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND thrgold.THROLD_Delete_Time IS NULL ";
	}

	elseif ($optDocumentGroup=='3'){
		$qcompany=($optCompany == "ALL")?"":"AND thrgolad.THRGOLAD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND thrgolad.THRGOLAD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
			<tr>
				<th>Nama Perusahaan</th>
				<th>Tahap GRL</th>
				<th>Periode GRL</th>
				<th>Desa</th>
				<th>Blok</th>
				<th>Pemilik</th>
				<th>Keterangan</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
			$MainContent .="
				<form name='list' method='post' action='print-report-registration.php' target='_blank'>
						<input type='hidden' name='txtStart' value='$tStart'>
						<input type='hidden' name='txtEnd' value='$tEnd'>
						<input type='hidden' name='optCompany' value='$optCompany'>
						<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
			$regdate=date("j M Y", strtotime($h_arr['THRGOLAD_RegistrationDate']));

			$MainContent .="
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='19%'>No. Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='25%'>
						$h_arr[THRGOLAD_RegistrationCode]
					</td>
					<td width='19%'>Tanggal Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='30%'>
						$regdate
					</td>
				</tr>
				<tr>
					<td>Nama Pendaftar</td>
					<td>:</td>
					<td>
						$h_arr[User_FullName]
					</td>
					<td>Departemen</td>
					<td>:</td>
					<td>
						$h_arr[Department_Name]
					</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td>$h_arr[Company_Name]</td>
					<td>Status Pendaftaran</td>
					<td>:</td>
					<td>$h_arr[DRS_Description]</td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td>$h_arr[DocumentGroup_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				</table>
				<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
				<tr>
					<th>Nama Perusahaan</th>
					<th>Tahap GRL</th>
					<th>Periode GRL</th>
					<th>Desa</th>
					<th>Blok</th>
					<th>Pemilik</th>
					<th>Keterangan</th>
				</tr>
			";
				$d_query="SELECT DISTINCT thrgolad.THRGOLAD_Phase, thrgolad.THRGOLAD_Period,tdrgolad.TDRGOLAD_Village,tdrgolad.TDRGOLAD_Block,
										  tdrgolad.TDRGOLAD_Owner,tdrgolad.TDRGOLAD_Information,
		 					 			CASE WHEN tdrgolad.TDRGOLAD_Core_CompanyID IS NOT NULL
		 					 				THEN (SELECT c.Company_Name FROM M_Company c
		 					 						WHERE c.Company_ID = tdrgolad.TDRGOLAD_Core_CompanyID
		 					 					)
		 					 				ELSE (SELECT c.Company_Name FROM M_Company c
		 					 						WHERE c.Company_ID = thrgolad.THRGOLAD_CompanyID
		 					 					)
		 					 			END AS company_name,
										CASE WHEN tdrgolad.TDRGOLAD_Core_CompanyID IS NOT NULL
											THEN tdrgolad.TDRGOLAD_Core_Phase
											ELSE thrgolad.THRGOLAD_Phase
										END AS tahap
					  	  FROM TH_RegistrationOfLandAcquisitionDocument thrgolad, TD_RegistrationOfLandAcquisitionDocument tdrgolad
					 	  WHERE thrgolad.THRGOLAD_ID='$h_arr[THRGOLAD_ID]'
						  AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
						  AND tdrgolad.TDRGOLAD_Delete_Time IS NULL ";
				$d_sql=mysql_query($d_query);

				while ($arr = mysql_fetch_array($d_sql)) {
				$periode=date("j M Y", strtotime($arr['THRGOLAD_Period']));
				if($periode == "1 Jan 1970"){ $periode = "-"; }
			$MainContent .="
				<tr>
					<td class='center'>$arr[company_name]</td>
					<td class='center'>$arr[tahap]</td>
					<td class='center'>$periode</td>
					<td class='center'>$arr[TDRGOLAD_Village]</td>
					<td class='center'>$arr[TDRGOLAD_Block]</td>
					<td class='center'>$arr[TDRGOLAD_Owner]</td>
					<td class='center'>$arr[TDRGOLAD_Information]</td>
				</tr>";
				}
			$MainContent .="
				</table>
			";
			}
		}
		$query1= "SELECT DISTINCT thrgolad.THRGOLAD_ID, thrgolad.THRGOLAD_RegistrationCode,
								  thrgolad.THRGOLAD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfLandAcquisitionDocument thrgolad,
				  	   TD_RegistrationOfLandAcquisitionDocument tdrgolad,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
				  $qcompany
				  $qarea
				  $qperiod
				  AND thrgolad.THRGOLAD_CompanyID=c.Company_ID
				  AND thrgolad.THRGOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND thrgolad.THRGOLAD_Delete_Time IS NULL ";
	}elseif ($optDocumentGroup=='4'){
		$qcompany=($optCompany == "ALL")?"":"AND th.THROAOD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND th.THROAOD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
			<tr>
				<th>Nama Pemilik</th>
				<th>Merk Kendaraan</th>
				<th>No. Polisi</th>
				<th>Lokasi</th>
				<th>STNK</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
			$MainContent .="
				<form name='list' method='post' action='print-report-registration.php' target='_blank'>
						<input type='hidden' name='txtStart' value='$tStart'>
						<input type='hidden' name='txtEnd' value='$tEnd'>
						<input type='hidden' name='optCompany' value='$optCompany'>
						<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
			$regdate=date("j M Y", strtotime($h_arr['THROAOD_RegistrationDate']));

			$MainContent .="
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='19%'>No. Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='25%'>
						$h_arr[THROAOD_RegistrationCode]
					</td>
					<td width='19%'>Tanggal Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='30%'>
						$regdate
					</td>
				</tr>
				<tr>
					<td>Nama Pendaftar</td>
					<td>:</td>
					<td>
						$h_arr[User_FullName]
					</td>
					<td>Departemen</td>
					<td>:</td>
					<td>
						$h_arr[Department_Name]
					</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td>$h_arr[Company_Name]</td>
					<td>Status Pendaftaran</td>
					<td>:</td>
					<td>$h_arr[DRS_Description]</td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td>$h_arr[DocumentGroup_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				</table>
				<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
				<tr>
					<th>Nama Pemilik</th>
					<th>Merk Kendaraan</th>
					<th>No. Polisi</th>
					<th>Lokasi</th>
					<th>STNK</th>
				</tr>
			";
				$d_query="SELECT DISTINCT m_mk.MK_Name merk_kendaraan,
							CASE WHEN td.TDROAOD_Employee_NIK LIKE 'CO@%'
							  THEN
								(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(td.TDROAOD_Employee_NIK, 'CO@', ''))
							  ELSE
								(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=td.TDROAOD_Employee_NIK)
							END nama_pemilik,
							td.TDROAOD_NoPolisi, td.TDROAOD_Lokasi_PT,
							CASE WHEN td.TDROAOD_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
								WHEN td.TDROAOD_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(td.TDROAOD_STNK_StartDate, '%d/%m/%Y')
							END AS start_stnk,
							CASE WHEN td.TDROAOD_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
								WHEN td.TDROAOD_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(td.TDROAOD_STNK_ExpiredDate, '%d/%m/%Y')
							END AS expired_stnk
					  	  FROM TH_RegistrationOfAssetOwnershipDocument th
						  LEFT JOIN TD_RegistrationOfAssetOwnershipDocument td
						  	ON td.TDROAOD_THROAOD_ID=th.THROAOD_ID
						  LEFT JOIN db_master.M_MerkKendaraan m_mk
	  						ON m_mk.MK_ID=td.TDROAOD_MK_ID
					 	  WHERE th.THROAOD_ID='$h_arr[THROAOD_ID]'
						  AND td.TDROAOD_Delete_Time IS NULL ";
				$d_sql=mysql_query($d_query);

				while ($arr = mysql_fetch_array($d_sql)) {
			$MainContent .="
				<tr>
					<td class='center'>$arr[nama_pemilik]</td>
					<td class='center'>$arr[merk_kendaraan]</td>
					<td class='center'>$arr[TDROAOD_NoPolisi]</td>
					<td class='center'>$arr[TDROAOD_Lokasi_PT]</td>
					<td class='center'>".$arr['start_stnk']." s/d
					".$arr['expired_stnk']."</td>
				</tr>";
				}
			$MainContent .="
				</table>
			";
			}
		}

		$query1 = "SELECT DISTINCT th.THROAOD_ID, th.THROAOD_RegistrationCode,
								  th.THROAOD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfAssetOwnershipDocument th,
				  	   TD_RegistrationOfAssetOwnershipDocument td,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  AND td.TDROAOD_THROAOD_ID=th.THROAOD_ID
				  $qcompany
				  $qarea
				  $qperiod
				  AND th.THROAOD_CompanyID=c.Company_ID
				  AND th.THROAOD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND th.THROAOD_Delete_Time IS NULL";

	}elseif ($optDocumentGroup=='5'){
		$qcompany=($optCompany == "ALL")?"":"AND th.THROOLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND td.TDROOLD_KategoriDokumenID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND th.THROOLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
			<tr>
				<th>Nama Perusahaan</th>
				<th>Kategori Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
				<th>Nomor Dokumen</th>
				<th>Tanggal Terbit</th>
				<th>Tanggal Berakhir</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
			$MainContent .="
				<form name='list' method='post' action='print-report-registration.php' target='_blank'>
						<input type='hidden' name='txtStart' value='$tStart'>
						<input type='hidden' name='txtEnd' value='$tEnd'>
						<input type='hidden' name='optCompany' value='$optCompany'>
						<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
			$regdate=date("j M Y", strtotime($h_arr['THROOLD_RegistrationDate']));

			$MainContent .="
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='19%'>No. Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='25%'>
						$h_arr[THROOLD_RegistrationCode]
					</td>
					<td width='19%'>Tanggal Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='30%'>
						$regdate
					</td>
				</tr>
				<tr>
					<td>Nama Pendaftar</td>
					<td>:</td>
					<td>
						$h_arr[User_FullName]
					</td>
					<td>Departemen</td>
					<td>:</td>
					<td>
						$h_arr[Department_Name]
					</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td>$h_arr[Company_Name]</td>
					<td>Status Pendaftaran</td>
					<td>:</td>
					<td>$h_arr[DRS_Description]</td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td>$h_arr[DocumentGroup_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				</table>
				<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
				<tr>
					<th>Nama Perusahaan</th>
					<th>Kategori Dokumen</th>
					<th>Nama Dokumen</th>
					<th>Instansi Terkait</th>
					<th>Nomor Dokumen</th>
					<th>Tanggal Terbit</th>
					<th>Tanggal Berakhir</th>
				</tr>
			";
				$d_query="SELECT DISTINCT CASE WHEN td.TDROOLD_Core_CompanyID IS NOT NULL
									THEN (SELECT c.Company_Name FROM M_Company c
											WHERE c.Company_ID = td.TDROOLD_Core_CompanyID
										)
									ELSE (SELECT c.Company_Name FROM M_Company c
											WHERE c.Company_ID = th.THROOLD_CompanyID
										)
								END AS company_name, td.TDROOLD_NamaDokumen,
							td.TDROOLD_InstansiTerkait,
							td.TDROOLD_NoDokumen,
							CASE WHEN td.TDROOLD_TglTerbit LIKE '%0000-00-00%' THEN '-'
								WHEN td.TDROOLD_TglTerbit LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(td.TDROOLD_TglTerbit, '%d/%m/%Y')
							END AS tgl_terbit,
							CASE WHEN td.TDROOLD_TglBerakhir LIKE '%0000-00-00%' THEN '-'
								WHEN td.TDROOLD_TglBerakhir LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(td.TDROOLD_TglBerakhir, '%d/%m/%Y')
							END AS tgl_berakhir,
							td.TDROOLD_Keterangan, DocumentCategory_Name
					  	  FROM TH_RegistrationOfOtherLegalDocuments th
						  LEFT JOIN TD_RegistrationOfOtherLegalDocuments td
						  	ON td.TDROOLD_THROOLD_ID=th.THROOLD_ID
						  LEFT JOIN db_master.M_DocumentCategory dc
						  	ON td.TDROOLD_KategoriDokumenID=dc.DocumentCategory_ID
					 	  WHERE th.THROOLD_ID='$h_arr[THROOLD_ID]'
						  $qcategory
						  AND td.TDROOLD_Delete_Time IS NULL ";
				$d_sql=mysql_query($d_query);

				while ($arr = mysql_fetch_array($d_sql)) {
			$MainContent .="
				<tr>
					<td class='center'>$arr[company_name]</td>
					<td class='center'>$arr[DocumentCategory_Name]</td>
					<td class='center'>$arr[TDROOLD_NamaDokumen]</td>
					<td class='center'>$arr[TDROOLD_InstansiTerkait]</td>
					<td class='center'>$arr[TDROOLD_NoDokumen]</td>
					<td class='center'>$arr[tgl_terbit]</td>
					<td class='center'>$arr[tgl_berakhir]</td>
				</tr>";
				}
			$MainContent .="
				</table>
			";
			}
		}

		$query1 = "SELECT DISTINCT th.THROOLD_ID, th.THROOLD_RegistrationCode,
								  th.THROOLD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfOtherLegalDocuments th,
				  	   TD_RegistrationOfOtherLegalDocuments td,
					   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  AND td.TDROOLD_THROOLD_ID=th.THROOLD_ID
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND th.THROOLD_CompanyID=c.Company_ID
				  AND th.THROOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND th.THROOLD_Delete_Time IS NULL";
	}elseif ($optDocumentGroup=='6'){
		$qcompany=($optCompany == "ALL")?"":"AND th.THROONLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND th.THROONLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
			<tr>
				<th>Nama Perusahaan</th>
				<th>No. Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Tahun Dokumen</th>
				<th>Departemen</th>
				<th>Keterangan</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
			$MainContent .="
				<form name='list' method='post' action='print-report-registration.php' target='_blank'>
						<input type='hidden' name='txtStart' value='$tStart'>
						<input type='hidden' name='txtEnd' value='$tEnd'>
						<input type='hidden' name='optCompany' value='$optCompany'>
						<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
			$regdate=date("j M Y", strtotime($h_arr['THROONLD_RegistrationDate']));

			$MainContent .="
				<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='19%'>No. Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='25%'>
						$h_arr[THROONLD_RegistrationCode]
					</td>
					<td width='19%'>Tanggal Pendaftaran</td>
					<td width='1%'>:</td>
					<td width='30%'>
						$regdate
					</td>
				</tr>
				<tr>
					<td>Nama Pendaftar</td>
					<td>:</td>
					<td>
						$h_arr[User_FullName]
					</td>
					<td>Departemen</td>
					<td>:</td>
					<td>
						$h_arr[Department_Name]
					</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td>$h_arr[Company_Name]</td>
					<td>Status Pendaftaran</td>
					<td>:</td>
					<td>$h_arr[DRS_Description]</td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td>$h_arr[DocumentGroup_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				</table>
				<table width='100%' border='1' class='stripeMe' cellpadding='0' cellspacing='0'>
				<tr>
					<th>Nama Perusahaan</th>
					<th>No. Dokumen</th>
					<th>Nama Dokumen</th>
					<th>Tahun Dokumen</th>
					<th width='25%'>Departemen</th>
					<th>Keterangan</th>
				</tr>
			";
				$d_query="SELECT DISTINCT CASE WHEN td.TDROONLD_Core_CompanyID IS NOT NULL
								THEN (SELECT c.Company_Name FROM M_Company c
										WHERE c.Company_ID = td.TDROONLD_Core_CompanyID
									)
								ELSE (SELECT c.Company_Name FROM M_Company c
										WHERE c.Company_ID = th.THROONLD_CompanyID
									)
							END AS company_name, td.TDROONLD_NamaDokumen,
							td.TDROONLD_TahunDokumen,
							td.TDROONLD_NoDokumen, td.TDROONLD_TahunDokumen,
							td.TDROONLD_Keterangan, md.Department_Name
					  	  FROM TH_RegistrationOfOtherNonLegalDocuments th
						  LEFT JOIN TD_RegistrationOfOtherNonLegalDocuments td
						  	ON td.TDROONLD_THROONLD_ID=th.THROONLD_ID
						  LEFT JOIN M_Department md
  	  						ON md.Department_ID=td.TDROONLD_Dept_Code
					 	  WHERE th.THROONLD_ID='$h_arr[THROONLD_ID]'
						  AND td.TDROONLD_Delete_Time IS NULL ";
				$d_sql=mysql_query($d_query);

				while ($arr = mysql_fetch_array($d_sql)) {
			$MainContent .="
				<tr>
					<td class='center'>$arr[company_name]</td>
					<td class='center'>$arr[TDROONLD_NoDokumen]</td>
					<td class='center'>$arr[TDROONLD_NamaDokumen]</td>
					<td class='center'>$arr[TDROONLD_TahunDokumen]</td>
					<td class='center'>$arr[Department_Name]</td>
					<td class='center'>$arr[TDROONLD_Keterangan]</td>
				</tr>";
				}
			$MainContent .="
				</table>
			";
			}
		}

		$query1 = "SELECT DISTINCT th.THROONLD_ID, th.THROONLD_RegistrationCode,
								  th.THROONLD_RegistrationDate, u.User_FullName, dp.Department_Name,
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfOtherNonLegalDocuments th,
				  	   TD_RegistrationOfOtherNonLegalDocuments td,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$optDocumentGroup'
				  AND td.TDROONLD_THROONLD_ID=th.THROONLD_ID
				  $qcompany
				  $qarea
				  $qperiod
				  AND th.THROONLD_CompanyID=c.Company_ID
				  AND th.THROONLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND th.THROONLD_Delete_Time IS NULL";
	}
	if ($num<>NULL){
		if(isset($_GET['page']))
		    $noPage = $_GET['page'];
		else
			$noPage = 1;
	$MainContent .="
				<input type='hidden' name='page' value='$noPage' />
				<input type='hidden' name='optArea' value='$optArea' />
				<input type='hidden' name='optCompany' value='$optCompany' />
				<input type='hidden' name='optDocumentGroup' value='$optDocumentGroup' />
				<input type='hidden' name='optDocumentCategory' value='$optDocumentCategory' />
				<input type='hidden' name='txtStart' value='$tStart' />
				<input type='hidden' name='txtEnd' value='$tEnd' />
				<table width='100%' border='1' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='24%'>Nama Pembuat Laporan</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user1' id='user1'></td>
				</tr>
				<tr>
					<td width='24%'>Nama Pemeriksa Laporan</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user2' id='user2'></td>
				</tr>
				<tr>
					<td width='24%'>Nama Pemberi Persetujuan 1</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user3' id='user3'></td>
				</tr>
				<tr>
					<td width='24%'>Nama Pemberi Persetujuan 2</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user4' id='user4'></td>
				</tr>
			</table>
			<center><input name='print' type='submit' value='Cetak Laporan' class='button' onclick='return validatePrint(this);'/></center>
			</form>";
	}

$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

$extra_link = "";

if(!empty($optArea)){
	$extra_link .= "&optArea=$optArea";
}

if(!empty($optCompany)){
	$extra_link .= "&optCompany=$optCompany";
}

if(!empty($optDocumentGroup)){
	$extra_link .= "&optDocumentGroup=$optDocumentGroup";
}

if(!empty($optDocumentCategory)){
	$extra_link .= "&optDocumentCategory=$optDocumentCategory";
}

if(!empty($tStart)){
	$extra_link .= "&txtStart=$tStart";
}

if(!empty($tEnd)){
	$extra_link .= "&txtEnd=$tEnd";
}

if(!empty($listdocument)){
	$extra_link .= "&listdocument=$listdocument";
}

if ($noPage > 1)
	$Pager.="<a href=$PHP_SELF?page=$prev$extra_link>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
    	if (($showPage == 1) && ($p != 2))
			$Pager.="...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			$Pager.="...";
        if ($p == $noPage)
			$Pager.="<b><u>$p</b></u> ";
        else
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p$extra_link>$p</a> ";

		$showPage = $p;
	}
}

if ($noPage < $jumPage)
	$Pager .= "<a href=$PHP_SELF?page=$next$extra_link>Next &gt;&gt;</a> ";
	}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>
