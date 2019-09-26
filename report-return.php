<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 06 Juni 2012																						=
= Update Terakhir	: 06 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Laporan Pengembalian Dokumen</title>
<?PHP include ("./config/config_db.php"); ?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
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
		CompanyAreaID: document.getElementById('optArea').value
	}, function(response){
		setTimeout("finishAjax('optCompany', '"+escape(response)+"')", 400);
	});
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
			<input name='export_to_excel' formaction='result-report-return-export-to-excel.php' type='submit' value='&nbsp;Export to Excel&nbsp;' class='button-small-blue' onclick='return validateInput(this);' />
		</td>
	</tr>
	<tr>
		<td>Grup</td>
		<td>:</td>
		<td colspan='2'>
			<select name='optDocumentGroup' id='optDocumentGroup' onchange='showCategory()'>
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
			<input type='text' size='10' name='txtStart' $val_txtStart id='txtStart' onclick=\"javascript:NewCssCal('txtStart', 'MMddyyyy');\"/> s/d <input type='text' size='10' name='txtEnd' $val_txtEnd id='txtEnd' onclick=\"javascript:NewCssCal('txtEnd', 'MMddyyyy');\"/>
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
		$qcompany=($optCompany == "ALL")?"":"AND thlold.THLOLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND tdlold.TDLOLD_DocumentCategoryID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtold.TDRTOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT tdrtold.TDRTOLD_ReturnCode, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
								  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc,
			  		   TH_ReleaseOfLegalDocument thrlold, TD_ReleaseOfLegalDocument tdrlold,
			  		   TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold,
			  		   TD_ReturnOfLegalDocument tdrtold, M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				  AND tdrtold.TDRTOLD_Delete_Time IS NULL
				  AND tdrlold.TDROLD_ReturnCode=tdrtold.TDRTOLD_ReturnCode
				  AND tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				  AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
				  AND tdlold.TDLOLD_DocCode=tdrtold.TDRTOLD_DocCode
				  AND thlold.THLOLD_DocumentGroupID='$optDocumentGroup'
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thlold.THLOLD_CompanyID=c.Company_ID
				  AND tdlold.TDLOLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thlold.THLOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  ORDER BY tdrtold.TDRTOLD_ReturnCode LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='3'){
		$qcompany=($optCompany == "ALL")?"":"AND thlolad.THLOLAD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtolad.TDRTOLAD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT tdrtolad.TDRTOLAD_ReturnCode, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfLandAcquisitionDocument thrlolad, TD_ReleaseOfLandAcquisitionDocument tdrlolad,
			  		   TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
			  		   TD_ReturnOfLandAcquisitionDocument tdrtolad, M_DivisionDepartmentPosition ddp
				  WHERE thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				  AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
				  AND tdrlolad.TDRLOLAD_ReturnCode=tdrtolad.TDRTOLAD_ReturnCode
				  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				  AND tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
				  $qcompany
				  $qarea
				  $qperiod
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  AND thlolad.THLOLAD_CompanyID=c.Company_ID
				  AND thlolad.THLOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  ORDER BY tdrtolad.TDRTOLAD_ReturnCode LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='4'){
		$qcompany=($optCompany == "ALL")?"":"AND thloaod.THLOAOD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtoaod.TDRTOAOD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT tdrtoaod.TDRTOAOD_ReturnCode, thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfAssetOwnershipDocument thrloaod, TD_ReleaseOfAssetOwnershipDocument tdrloaod,
			  		   TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
			  		   TD_ReturnOfAssetOwnershipDocument tdrtoaod, M_DivisionDepartmentPosition ddp
				  WHERE thrloaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
				  AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL
				  AND tdrloaod.TDROAOD_ReturnCode=tdrtoaod.TDRTOAOD_ReturnCode
				  AND tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
				  AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
				  AND tdloaod.TDLOAOD_DocCode=tdrtoaod.TDRTOAOD_DocCode
				  $qcompany
				  $qarea
				  $qperiod
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  AND thloaod.THLOAOD_CompanyID=c.Company_ID
				  AND thloaod.THLOAOD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  ORDER BY tdrtoaod.TDRTOAOD_ReturnCode LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='5'){
		$qcompany=($optCompany == "ALL")?"":"AND thloold.THLOOLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND dol.DOL_CategoryDocID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtoold.TDRTOOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT tdrtoold.TDRTOOLD_ReturnCode, thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfOtherLegalDocuments thrloold, TD_ReleaseOfOtherLegalDocuments tdrloold,
			  		   TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold,
			  		   TD_ReturnOfOtherLegalDocuments tdrtoold, M_DivisionDepartmentPosition ddp,
					   M_DocumentsOtherLegal dol
				  WHERE thrloold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
				  AND tdrtoold.TDRTOOLD_Delete_Time IS NULL
				  AND tdrloold.TDROOLD_ReturnCode=tdrtoold.TDRTOOLD_ReturnCode
				  AND tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
				  AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
				  AND tdloold.TDLOOLD_DocCode=tdrtoold.TDRTOOLD_DocCode
				  AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  AND thloold.THLOOLD_CompanyID=c.Company_ID
				  AND thloold.THLOOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  ORDER BY tdrtoold.TDRTOOLD_ReturnCode LIMIT $offset, $dataPerPage";
	}elseif ($optDocumentGroup=='6'){
		$qcompany=($optCompany == "ALL")?"":"AND thloonld.THLOONLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtoonld.TDRTOONLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		$query = "SELECT DISTINCT tdrtoonld.TDRTOONLD_ReturnCode, thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfOtherNonLegalDocuments thrloonld, TD_ReleaseOfOtherNonLegalDocuments tdrloonld,
			  		   TH_LoanOfOtherNonLegalDocuments thloonld, TD_LoanOfOtherNonLegalDocuments tdloonld,
			  		   TD_ReturnOfOtherNonLegalDocuments tdrtoonld, M_DivisionDepartmentPosition ddp
				  WHERE thrloonld.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
				  AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
				  AND tdrloonld.TDROONLD_ReturnCode=tdrtoonld.TDRTOONLD_ReturnCode
				  AND tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
				  AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
				  AND tdloonld.TDLOONLD_DocCode=tdrtoonld.TDRTOONLD_DocCode
				  $qcompany
				  $qarea
				  $qperiod
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  AND thloonld.THLOONLD_CompanyID=c.Company_ID
				  AND thloonld.THLOONLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  ORDER BY tdrtoonld.TDRTOONLD_ReturnCode LIMIT $offset, $dataPerPage";
	}

	// echo $query;
$sql = mysql_query($query);
$num = mysql_num_rows($sql);
$no = 1;

	if ($optDocumentGroup == '1' || $optDocumentGroup == '2'){
		$qcompany=($optCompany == "ALL")?"":"AND thlold.THLOLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND tdlold.TDLOLD_DocumentCategoryID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtold.TDRTOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>Tanggal Pengembalian</th>
				<th>Kode Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
				<th>Nomor Dokumen</th>
				<th>Berlaku Sampai</th>
				<th>Tanggal Pengeluaran</th>
				<th>Lead Time</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-return.php' target='_blank'>
					<input type='hidden' name='txtStart' value='$tStart'>
					<input type='hidden' name='txtEnd' value='$tEnd'>
					<input type='hidden' name='optCompany' value='$optCompany'>
					<input type='hidden' name='optArea' value='$optArea'>
					<input type='hidden' name='optDocumentCategory' value='$optDocumentCategory'>";
			while ($h_arr = mysql_fetch_array($sql)) {
				$loandate=date("j M Y", strtotime($h_arr['THLOLD_LoanDate']));

				$d_query="SELECT tdrtold.TDRTOLD_ReturnTime, tdrtold.TDRTOLD_DocCode, dt.DocumentType_Name,
								 dl.DL_Instance, dl.DL_NoDoc, dl.DL_ExpDate,thrlold.THROLD_ReleaseCode,
								 thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime
								 FROM TD_ReturnOfLegalDocument tdrtold
								 LEFT JOIN M_DocumentLegal dl
								 	ON tdrtold.TDRTOLD_DocCode=dl.DL_DocCode
								 LEFT JOIN M_DocumentType dt
								 	ON dl.DL_TypeDocID=dt.DocumentType_ID
								 LEFT JOIN TD_ReleaseOfLegalDocument tdrlold
								 	ON tdrlold.TDROLD_ReturnCode= tdrtold.TDRTOLD_ReturnCode
								 LEFT JOIN TH_ReleaseOfLegalDocument thrlold
								 	ON tdrlold.TDROLD_TDLOLD_ID=thrlold.THROLD_ID
								 WHERE tdrtold.TDRTOLD_ReturnCode='$h_arr[TDRTOLD_ReturnCode]'
								 AND tdrtold.TDRTOLD_Delete_Time IS NULL";
				$d_sql=mysql_query($d_query);

				$MainContent .="
				<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
				<tr>
					<td width='19%'>Kode Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$h_arr[THLOLD_LoanCode]</td>
					<td width='19%'>Tanggal Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$loandate</td>
				</tr>
				<tr>
					<td>Nama Peminta</td>
					<td>:</td>
					<td>$h_arr[User_FullName]</td>
					<td>Departemen</td>
					<td>:</td>
					<td>$h_arr[Department_Name]</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td>$h_arr[Company_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
					<td>Kategori Dokumen</td>
					<td>:</td>
					<td>$h_arr[DocumentCategory_Name]</td>
				</tr>
				</table>

				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th>Tanggal Pengembalian</th>
					<th>Kode Dokumen</th>
					<th>Nama Dokumen</th>
					<th>Instansi Terkait</th>
					<th>Nomor Dokumen</th>
					<th>Berlaku Sampai</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Lead Time</th>
				</tr>";

				while ($arr = mysql_fetch_array($d_sql)) {
					$reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
					$retdate=date("j M Y", strtotime($arr['TDRTOLD_ReturnTime']));
					if ($arr['DL_ExpDate']=="0000-00-00 00:00:00")
						$expdate="-";
					else
						$expdate=date("j M Y", strtotime($arr['DL_ExpDate']));
					if ($arr['TDROLD_LeadTime']=="0000-00-00 00:00:00")
						$leaddate="-";
					else
						$leaddate=date("j M Y", strtotime($arr['TDROLD_LeadTime']));
				$MainContent .="
				<tr>
					<td class='center'>$retdate</td>
					<td class='center'>$arr[TDRTOLD_DocCode]</td>
					<td class='center'>$arr[DocumentType_Name]</td>
					<td class='center'>$arr[DL_Instance]</td>
					<td class='center'>$arr[DL_NoDoc]</td>
					<td class='center'>$expdate</td>
					<td class='center'>$arr[THROLD_ReleaseCode]</td>
					<td class='center'>$reldate</td>
					<td class='center'>$leaddate</td>
				</tr>";
				}
				$MainContent .="
					</table>
				";
			}
		}
		$query1= "SELECT DISTINCT tdrtold.TDRTOLD_ReturnCode, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
								  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc,
			  		   TH_ReleaseOfLegalDocument thrlold, TD_ReleaseOfLegalDocument tdrlold,
			  		   TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold,
			  		   TD_ReturnOfLegalDocument tdrtold, M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				  AND tdrtold.TDRTOLD_Delete_Time IS NULL
				  AND tdrlold.TDROLD_ReturnCode=tdrtold.TDRTOLD_ReturnCode
				  AND tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				  AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
				  AND tdlold.TDLOLD_DocCode=tdrtold.TDRTOLD_DocCode
				  AND thlold.THLOLD_DocumentGroupID='$optDocumentGroup'
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thlold.THLOLD_CompanyID=c.Company_ID
				  AND tdlold.TDLOLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thlold.THLOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID ";
	}elseif ($optDocumentGroup=='3'){
		$qcompany=($optCompany == "ALL")?"":"AND thlolad.THLOLAD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtolad.TDRTOLAD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>Tanggal Pengembalian</th>
				<th>Kode Dokumen</th>
				<th>Tahap</th>
				<th>Periode</th>
				<th>Desa</th>
				<th>Blok</th>
				<th>Pemilik</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Lead Time</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-return.php' target='_blank'>
					<input type='hidden' name='txtStart' value='$tStart'>
					<input type='hidden' name='txtEnd' value='$tEnd'>
					<input type='hidden' name='optCompany' value='$optCompany'>
					<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
				$loandate=date("j M Y", strtotime($h_arr['THLOLAD_LoanDate']));

				$d_query="SELECT tdrtolad.TDRTOLAD_ReturnTime, tdrtolad.TDRTOLAD_DocCode, dla.DLA_Phase,
								 dla.DLA_Period, dla.DLA_Village, dla.DLA_Block,
							 	 dla.DLA_Owner, thrlolad.THRLOLAD_ReleaseCode,
								 thrlolad.THRLOLAD_ReleaseDate, tdrlolad.TDRLOLAD_LeadTime
						  FROM TD_ReturnOfLandAcquisitionDocument tdrtolad
						  LEFT JOIN M_DocumentLandAcquisition dla
						  	ON tdrtolad.TDRTOLAD_DocCode=dla.DLA_Code
						  LEFT JOIN TD_ReleaseOfLandAcquisitionDocument tdrlolad
						  	ON tdrlolad.TDRLOLAD_ReturnCode= tdrtolad.TDRTOLAD_ReturnCode
						  LEFT JOIN TH_ReleaseOfLandAcquisitionDocument thrlolad
							ON tdrlolad.TDRLOLAD_TDLOLAD_ID=thrlolad.THRLOLAD_ID
						  WHERE tdrtolad.TDRTOLAD_ReturnCode='$h_arr[TDRTOLAD_ReturnCode]'
						  	AND tdrtolad.TDRTOLAD_Delete_Time IS NULL";
				$d_sql=mysql_query($d_query);
				$MainContent .="
				<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
				<tr>
					<td width='19%'>Kode Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$h_arr[THLOLAD_LoanCode]</td>
					<td width='19%'>Tanggal Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$loandate</td>
				</tr>
				<tr>
					<td>Nama Peminta</td>
					<td>:</td>
					<td>$h_arr[User_FullName]</td>
					<td>Departemen</td>
					<td>:</td>
					<td>$h_arr[Department_Name]</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
				</tr>
				</table>

				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th>Tanggal Pengembalian</th>
					<th>Kode Dokumen</th>
					<th>Tahap</th>
					<th>Periode</th>
					<th>Desa</th>
					<th>Blok</th>
					<th>Pemilik</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Lead Time</th>
				</tr>";

				while ($arr = mysql_fetch_array($d_sql)) {
					$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
					$retdate=date("j M Y", strtotime($arr['TDRTOLAD_ReturnTime']));
					$period=date("j M Y", strtotime($arr['DLA_Period']));
					if ($arr['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")
						$leaddate="-";
					else
						$leaddate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
				$MainContent .="
				<tr>
					<td class='center'>$retdate</td>
					<td class='center'>$arr[TDRTOLAD_DocCode]</td>
					<td class='center'>$arr[DLA_Phase]</td>
					<td class='center'>$period</td>
					<td class='center'>$arr[DLA_Village]</td>
					<td class='center'>$arr[DLA_Block]</td>
					<td class='center'>$arr[DLA_Owner]</td>
					<td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
					<td class='center'>$reldate</td>
					<td class='center'>$leaddate</td>
				</tr>";
				}
				$MainContent .="
					</table>
				";
			}

		}
		$query1= "SELECT DISTINCT tdrtolad.TDRTOLAD_ReturnCode, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfLandAcquisitionDocument thrlolad, TD_ReleaseOfLandAcquisitionDocument tdrlolad,
			  		   TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
			  		   TD_ReturnOfLandAcquisitionDocument tdrtolad, M_DivisionDepartmentPosition ddp
				  WHERE thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				  AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
				  AND tdrlolad.TDRLOLAD_ReturnCode=tdrtolad.TDRTOLAD_ReturnCode
				  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				  AND tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  $qcompany
				  $qarea
				  $qperiod
				  AND thlolad.THLOLAD_CompanyID=c.Company_ID
				  AND thlolad.THLOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID ";
   }elseif ($optDocumentGroup=='4'){
		$qcompany=($optCompany == "ALL")?"":"AND thloaod.THLOAOD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtoaod.TDRTOAOD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>Tanggal Pengembalian</th>
				<th>Kode Dokumen</th>
				<th>Nama Pemilik</th>
				<th>Merk Kendaraan</th>
				<th>Nomor Polisi</th>
				<th>Masa STNK</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Lead Time</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-return.php' target='_blank'>
					<input type='hidden' name='txtStart' value='$tStart'>
					<input type='hidden' name='txtEnd' value='$tEnd'>
					<input type='hidden' name='optCompany' value='$optCompany'>
					<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
				$loandate=date("j M Y", strtotime($h_arr['THLOAOD_LoanDate']));

				$d_query="SELECT tdrtoaod.TDRTOAOD_ReturnTime, tdrtoaod.TDRTOAOD_DocCode, m_mk.MK_Name merk_kendaraan,
								 CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
								  THEN
									(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
								  ELSE
									(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
								 END nama_pemilik,
								 dao.DAO_NoPolisi, CASE WHEN dao.DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
		 							WHEN dao.DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
		 							ELSE DATE_FORMAT(dao.DAO_STNK_StartDate, '%d/%m/%Y')
		 						 END AS start_stnk,
		 						 CASE WHEN dao.DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
		 							WHEN dao.DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
		 							ELSE DATE_FORMAT(dao.DAO_STNK_ExpiredDate, '%d/%m/%Y')
		 						 END AS expired_stnk, thrloaod.THROAOD_ReleaseCode,
								 thrloaod.THROAOD_ReleaseDate, tdrloaod.TDROAOD_LeadTime
						  FROM TD_ReturnOfAssetOwnershipDocument tdrtoaod
						  LEFT JOIN M_DocumentAssetOwnership dao
						  	ON tdrtoaod.TDRTOAOD_DocCode=dao.DAO_DocCode
						  LEFT JOIN TD_ReleaseOfAssetOwnershipDocument tdrloaod
						  	ON tdrloaod.TDROAOD_ReturnCode= tdrtoaod.TDRTOAOD_ReturnCode
						  LEFT JOIN TH_ReleaseOfAssetOwnershipDocument thrloaod
						  	ON tdrloaod.TDROAOD_TDLOAOD_ID=thrloaod.THROAOD_ID
						  LEFT JOIN db_master.M_MerkKendaraan m_mk
						  	ON m_mk.MK_ID=dao.DAO_MK_ID
						  WHERE tdrtoaod.TDRTOAOD_ReturnCode='$h_arr[TDRTOAOD_ReturnCode]'
						  AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL";
				$d_sql=mysql_query($d_query);
				$MainContent .="
				<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
				<tr>
					<td width='19%'>Kode Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$h_arr[THLOAOD_LoanCode]</td>
					<td width='19%'>Tanggal Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$loandate</td>
				</tr>
				<tr>
					<td>Nama Peminta</td>
					<td>:</td>
					<td>$h_arr[User_FullName]</td>
					<td>Departemen</td>
					<td>:</td>
					<td>$h_arr[Department_Name]</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
				</tr>
				</table>

				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th>Tanggal Pengembalian</th>
					<th>Kode Dokumen</th>
					<th>Nama Pemilik</th>
					<th>Merk Kendaraan</th>
					<th>Nomor Polisi</th>
					<th>Masa STNK</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Lead Time</th>
				</tr>";

				while ($arr = mysql_fetch_array($d_sql)) {
					$reldate=date("j M Y", strtotime($arr['THROAOD_ReleaseDate']));
					$retdate=date("j M Y", strtotime($arr['TDRTOAOD_ReturnTime']));
					if ($arr['TDROAOD_LeadTime']=="0000-00-00 00:00:00")
						$leaddate="-";
					else
						$leaddate=date("j M Y", strtotime($arr['TDROAOD_LeadTime']));
				$MainContent .="
				<tr>
					<td class='center'>$retdate</td>
					<td class='center'>$arr[TDRTOAOD_DocCode]</td>
					<td class='center'>$arr[nama_pemilik]</td>
					<td class='center'>$arr[merk_kendaraan]</td>
					<td class='center'>$arr[DAO_NoPolisi]</td>
					<td class='center'>$arr[start_stnk] s/d $arr[expired_stnk]</td>
					<td class='center'>$arr[THROAOD_ReleaseCode]</td>
					<td class='center'>$reldate</td>
					<td class='center'>$leaddate</td>
				</tr>";
				}
				$MainContent .="
					</table>
				";
			}
		}

		$query1 = "SELECT DISTINCT tdrtoaod.TDRTOAOD_ReturnCode, thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfAssetOwnershipDocument thrloaod, TD_ReleaseOfAssetOwnershipDocument tdrloaod,
			  		   TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
			  		   TD_ReturnOfAssetOwnershipDocument tdrtoaod, M_DivisionDepartmentPosition ddp
				  WHERE thrloaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
				  AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL
				  AND tdrloaod.TDROAOD_ReturnCode=tdrtoaod.TDRTOAOD_ReturnCode
				  AND tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
				  AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
				  AND tdloaod.TDLOAOD_DocCode=tdrtoaod.TDRTOAOD_DocCode
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  $qcompany
				  $qarea
				  $qperiod
				  AND thloaod.THLOAOD_CompanyID=c.Company_ID
				  AND thloaod.THLOAOD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID";
	}elseif ($optDocumentGroup=='5'){
		$qcompany=($optCompany == "ALL")?"":"AND thloold.THLOOLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qcategory=(!$optDocumentCategory)?"":"AND dol.DOL_CategoryDocID='$optDocumentCategory'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtoold.TDRTOOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>Tanggal Pengembalian</th>
				<th>Kode Dokumen</th>
				<th>Kategori Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
				<th>Nomor Dokumen</th>
				<th>Berlaku Sampai</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Lead Time</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-return.php' target='_blank'>
					<input type='hidden' name='txtStart' value='$tStart'>
					<input type='hidden' name='txtEnd' value='$tEnd'>
					<input type='hidden' name='optCompany' value='$optCompany'>
					<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
				$loandate=date("j M Y", strtotime($h_arr['THLOOLD_LoanDate']));

				$d_query="SELECT tdrtoold.TDRTOOLD_ReturnTime, tdrtoold.TDRTOOLD_DocCode,
								 mdc.DocumentCategory_Name kategori_dokumen, dol.DOL_NamaDokumen,
								 dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
								 dol.DOL_TglTerbit, dol.DOL_TglBerakhir, thrloold.THROOLD_ReleaseCode,
								 thrloold.THROOLD_ReleaseDate, tdrloold.TDROOLD_LeadTime
						  FROM TD_ReturnOfOtherLegalDocuments tdrtoold
						  LEFT JOIN M_DocumentsOtherLegal dol
						  	ON tdrtoold.TDRTOOLD_DocCode=dol.DOL_DocCode
							$qcategory
						  LEFT JOIN TD_ReleaseOfOtherLegalDocuments tdrloold
						  	ON tdrloold.TDROOLD_ReturnCode= tdrtoold.TDRTOOLD_ReturnCode
						  LEFT JOIN TH_ReleaseOfOtherLegalDocuments thrloold
						  	ON tdrloold.TDROOLD_TDLOOLD_ID=thrloold.THROOLD_ID
						  LEFT JOIN db_master.M_DocumentCategory mdc
						  	ON mdc.DocumentCategory_ID=dol.DOL_CategoryDocID
						  WHERE tdrtoold.TDRTOOLD_ReturnCode='$h_arr[TDRTOOLD_ReturnCode]'
						  AND tdrtoold.TDRTOOLD_Delete_Time IS NULL";
				$d_sql=mysql_query($d_query);
				$MainContent .="
				<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
				<tr>
					<td width='19%'>Kode Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$h_arr[THLOOLD_LoanCode]</td>
					<td width='19%'>Tanggal Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$loandate</td>
				</tr>
				<tr>
					<td>Nama Peminta</td>
					<td>:</td>
					<td>$h_arr[User_FullName]</td>
					<td>Departemen</td>
					<td>:</td>
					<td>$h_arr[Department_Name]</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
				</tr>
				</table>

				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th>Tanggal Pengembalian</th>
					<th>Kode Dokumen</th>
					<th>Kategori Dokumen</th>
					<th>Nama Dokumen</th>
					<th>Instansi Terkait</th>
					<th>Nomor Dokumen</th>
					<th>Berlaku Sampai</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Lead Time</th>
				</tr>";

				while ($arr = mysql_fetch_array($d_sql)) {
					$tgl_berakhir = date("j M Y", strtotime($arr['DOL_TglBerakhir']));
					$reldate=date("j M Y", strtotime($arr['THROOLD_ReleaseDate']));
					$retdate=date("j M Y", strtotime($arr['TDRTOOLD_ReturnTime']));
					if ($arr['TDROOLD_LeadTime']=="0000-00-00 00:00:00")
						$leaddate="-";
					else
						$leaddate=date("j M Y", strtotime($arr['TDROOLD_LeadTime']));
				$MainContent .="
				<tr>
					<td class='center'>$retdate</td>
					<td class='center'>$arr[TDRTOOLD_DocCode]</td>
					<td class='center'>$arr[kategori_dokumen]</td>
					<td class='center'>$arr[DOL_NamaDokumen]</td>
					<td class='center'>$arr[DOL_InstansiTerkait]</td>
					<td class='center'>$arr[DOL_NoDokumen]</td>
					<td class='center'>$tgl_berakhir</td>
					<td class='center'>$arr[THROOLD_ReleaseCode]</td>
					<td class='center'>$reldate</td>
					<td class='center'>$leaddate</td>
				</tr>";
				}
				$MainContent .="
					</table>
				";
			}
		}

		$query1 = "SELECT DISTINCT tdrtoold.TDRTOOLD_ReturnCode, thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfOtherLegalDocuments thrloold, TD_ReleaseOfOtherLegalDocuments tdrloold,
			  		   TH_LoanOfOtherLegalDocuments thloold, TD_LoanOfOtherLegalDocuments tdloold,
			  		   TD_ReturnOfOtherLegalDocuments tdrtoold, M_DivisionDepartmentPosition ddp,
					   M_DocumentsOtherLegal dol
				  WHERE thrloold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
				  AND tdrtoold.TDRTOOLD_Delete_Time IS NULL
				  AND tdrloold.TDROOLD_ReturnCode=tdrtoold.TDRTOOLD_ReturnCode
				  AND tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
				  AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
				  AND tdloold.TDLOOLD_DocCode=tdrtoold.TDRTOOLD_DocCode
				  AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  AND thloold.THLOOLD_CompanyID=c.Company_ID
				  AND thloold.THLOOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID";
	}elseif ($optDocumentGroup=='6'){
		$qcompany=($optCompany == "ALL")?"":"AND thloonld.THLOONLD_CompanyID='$optCompany'";
		$qarea=(!$optArea)?"":"AND c.Company_ID_Area='$optArea'";
		$qperiod=((!$tStart)&&(!$tEnd))?"":"AND tdrtoonld.TDRTOONLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>Tanggal Pengembalian</th>
				<th>Kode Dokumen</th>
				<th>No. Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Tahun Dokumen</th>
				<th>Departemen</th>
				<th>Kode Pengeluaran</th>
				<th>Tanggal Pengeluaran</th>
				<th>Lead Time</th>
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-return.php' target='_blank'>
					<input type='hidden' name='txtStart' value='$tStart'>
					<input type='hidden' name='txtEnd' value='$tEnd'>
					<input type='hidden' name='optCompany' value='$optCompany'>
					<input type='hidden' name='optArea' value='$optArea'>";
			while ($h_arr = mysql_fetch_array($sql)) {
				$loandate=date("j M Y", strtotime($h_arr['THLOONLD_LoanDate']));

				$d_query="SELECT tdrtoonld.TDRTOONLD_ReturnTime, tdrtoonld.TDRTOONLD_DocCode,
								 donl.DONL_NamaDokumen, donl.DONL_TahunDokumen, donl.DONL_NoDokumen,
								 md.Department_Name nama_dept, thrloonld.THROONLD_ReleaseCode,
								 thrloonld.THROONLD_ReleaseDate, tdrloonld.TDROONLD_LeadTime
						  FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld
						  LEFT JOIN M_DocumentsOtherNonLegal donl
						  	ON tdrtoonld.TDRTOONLD_DocCode=donl.DONL_DocCode
						  LEFT JOIN TD_ReleaseOfOtherNonLegalDocuments tdrloonld
						  	ON tdrloonld.TDROONLD_ReturnCode= tdrtoonld.TDRTOONLD_ReturnCode
						  LEFT JOIN TH_ReleaseOfOtherNonLegalDocuments thrloonld
						  	ON tdrloonld.TDROONLD_TDLOONLD_ID=thrloonld.THROONLD_ID
						  LEFT JOIN M_Department md
						    ON md.Department_ID=donl.DONL_Dept_Code
						  WHERE tdrtoonld.TDRTOONLD_ReturnCode='$h_arr[TDRTOONLD_ReturnCode]'
						  AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL";
				$d_sql=mysql_query($d_query);
				$MainContent .="
				<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
				<tr>
					<td width='19%'>Kode Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$h_arr[THLOONLD_LoanCode]</td>
					<td width='19%'>Tanggal Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$loandate</td>
				</tr>
				<tr>
					<td>Nama Peminta</td>
					<td>:</td>
					<td>$h_arr[User_FullName]</td>
					<td>Departemen</td>
					<td>:</td>
					<td>$h_arr[Department_Name]</td>
				</tr>
				<tr>
					<td>Perusahaan</td>
					<td>:</td>
					<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
				</tr>
				</table>

				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th>Tanggal Pengembalian</th>
					<th>Kode Dokumen</th>
					<th>No. Dokumen</th>
					<th>Nama Dokumen</th>
					<th>Tahun Dokumen</th>
					<th>Departemen</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Lead Time</th>
				</tr>";

				while ($arr = mysql_fetch_array($d_sql)) {
					$reldate=date("j M Y", strtotime($arr['THROONLD_ReleaseDate']));
					$retdate=date("j M Y", strtotime($arr['TDRTOONLD_ReturnTime']));
					if ($arr['TDROONLD_LeadTime']=="0000-00-00 00:00:00")
						$leaddate="-";
					else
						$leaddate=date("j M Y", strtotime($arr['TDROONLD_LeadTime']));
				$MainContent .="
				<tr>
					<td class='center'>$retdate</td>
					<td class='center'>$arr[TDRTOONLD_DocCode]</td>
					<td class='center'>$arr[DONL_NoDokumen]</td>
					<td class='center'>$arr[DONL_NamaDokumen]</td>
					<td class='center'>$arr[DONL_TahunDokumen]</td>
					<td class='center'>$arr[nama_dept]</td>
					<td class='center'>$arr[THROONLD_ReleaseCode]</td>
					<td class='center'>$reldate</td>
					<td class='center'>$leaddate</td>
				</tr>";
				}
				$MainContent .="
					</table>
				";
			}
		}

		$query1 = "SELECT DISTINCT tdrtoonld.TDRTOONLD_ReturnCode, thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_LoanDate,
								  u.User_FullName, dp.Department_Name,
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfOtherNonLegalDocuments thrloonld, TD_ReleaseOfOtherNonLegalDocuments tdrloonld,
			  		   TH_LoanOfOtherNonLegalDocuments thloonld, TD_LoanOfOtherNonLegalDocuments tdloonld,
			  		   TD_ReturnOfOtherNonLegalDocuments tdrtoonld, M_DivisionDepartmentPosition ddp
				  WHERE thrloonld.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
				  AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
				  AND tdrloonld.TDROONLD_ReturnCode=tdrtoonld.TDRTOONLD_ReturnCode
				  AND tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
				  AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
				  AND tdloonld.TDLOONLD_DocCode=tdrtoonld.TDRTOONLD_DocCode
				  AND dg.DocumentGroup_ID='$optDocumentGroup'
				  $qcompany
				  $qarea
				  $qperiod
				  AND thloonld.THLOONLD_CompanyID=c.Company_ID
				  AND thloonld.THLOONLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID";
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
