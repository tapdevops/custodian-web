<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 04 Mei 2012																						=
= Update Terakhir	: 29 Mei 2012																						=
= Revisi			:																									=
= 		24/05/2012	: Penambahan Filter Untuk Pencarian (OK)															=
= 		29/05/2012	: Transaksi Untuk GRL (OK)																			=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Daftar Transaksi</title>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT PEMILIHAN JENIS TRANSAKSI & GRUP DOKUMEN
function validateInput(elem) {
	var optTransactionID = document.getElementById('optTransactionID').selectedIndex;
	var optTHROLD_DocumentGroupID = document.getElementById('optTHROLD_DocumentGroupID').selectedIndex;

	if(optTransactionID == 0) {
		alert("Jenis Transaksi Belum Dipilih!");
		return false;
	}
	if(optTHROLD_DocumentGroupID == 0) {
		alert("Grup Dokumen Belum Dipilih!");
		return false;
	}
	else if(optTHROLD_DocumentGroupID == 3) {
		var phase = document.getElementById('phase').value;

		if (phase.replace(" ", "") != "") {
			if(isNaN(phase)){
				alert ("Tahap Harus Berupa Angka [0-9]!");
				return false;
			}
		}
	}

	return true;
}
// MENAMPILKAN DETAIL FILTER
function showFilterDetail() {
	$.post("jQuery.TransactionListFilter.php", {
		optTHROLD_DocumentGroupID : $('#optTHROLD_DocumentGroupID').val(),
		optTransactionID : $('#optTransactionID').val(),
		optFilterHeader : $('#optFilterHeader').val()
	}, function(response){
		setTimeout("finishAjax('optFilterDetail', '"+escape(response)+"')", 400);
	});
}
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
}
//MENAMPILKAN DAFTAR FASE BILA GRUP DOKUMEN ADALAH GRL
function showFilter(){
	if(document.getElementById('optFilterHeader').value != null){
		document.getElementById('optFilterHeader').innerHTML='';//reset opt(hapus semua pilihan)
	}
	//isi pilihan baru sesuai dengan group dokumen
	if(document.getElementById('optTHROLD_DocumentGroupID').value=="1" || document.getElementById('optTHROLD_DocumentGroupID').value=="2"){ //Arief F - 18102018
		document.getElementById('optPhase').style.display = "none";
		document.getElementById('optFilterHeader').options[0]=new Option('--- Pilih Filter ---', '0');
		document.getElementById('optFilterHeader').options[1]=new Option('Perusahaan', '1');
		document.getElementById('optFilterHeader').options[2]=new Option('Kategori Dokumen', '2');
		document.getElementById('optFilterHeader').options[3]=new Option('Tipe Dokumen', '3');
		document.getElementById('optFilterHeader').options[4]=new Option('Status', '4');
	}else{ //Arief F - 18102018
		if (document.getElementById('optTHROLD_DocumentGroupID').value=="3"){ //Arief F - 18102018
			document.getElementById('optPhase').style.display = "inline";
		}else{ //Arief F - 18102018
			document.getElementById('optPhase').style.display = "none";
		}
		if(document.getElementById('optTransactionID').value=="1" && document.getElementById('optTHROLD_DocumentGroupID').value=="4" ){
			document.getElementById('optFilterHeader').options[0]=new Option('--- Pilih Filter ---', '0');
			document.getElementById('optFilterHeader').options[1]=new Option('Status', '4');
		}else{
			document.getElementById('optFilterHeader').options[0]=new Option('--- Pilih Filter ---', '0');
			document.getElementById('optFilterHeader').options[1]=new Option('Perusahaan', '1');
			document.getElementById('optFilterHeader').options[2]=new Option('Status', '4');
		}
	}
	document.getElementById('optFilterHeader').value = "0";
	document.getElementById('optFilterDetail').value = "0";
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
include ("./include/class.endencrp.php");

$decrp = new custodian_encryp;
$page=new Template();

$ActionContent ="
	<form name='list' method='get' action='transaction-list.php'>
	<table width='100%'>
	<tr>
		<td width='14%'>
			Jenis Transaksi
		</td>
		<td width='1%'>:</td>
		<td width='60%'>
			<select name='optTransactionID' id='optTransactionID' class='filter' onchange='showFilter();'>
				<option value='0'>--- Pilih Jenis Transaksi ---</option>";
			// 	$ActionContent .= "<option value='1' ".($_GET['optTransactionID'] == '1' ? 'selected' : '').">Registrasi</option>
			// 	<option value='2' ".($_GET['optTransactionID'] == '2' ? 'selected' : '').">Permohonan Permintaan</option>
			// 	<option value='3' ".($_GET['optTransactionID'] == '3' ? 'selected' : '').">Pengeluaran</option>
			// 	<option value='4' ".($_GET['optTransactionID'] == '4' ? 'selected' : '').">Pengembalian</option>
			// </select>"; // Arief F - 18102018
			$ActionContent .= "<option value='1'>Registrasi</option>
			<option value='2'>Permohonan Permintaan</option>
			<option value='3'>Pengeluaran</option>
			<option value='4'>Pengembalian</option>
		</select>";
		$ActionContent .= "</td>
		<td width='25%'>
			<input name='listdocument' type='submit' value='Cari' class='button-small' onclick='return validateInput(this);'/><input name='filter' type='submit' value='Filter' class='button-small'/>
		</td>
	</tr>
	<tr>
		<td>
			Grup Dokumen
		</td>
		<td>:</td>
		<td>
			<select name='optTHROLD_DocumentGroupID' id='optTHROLD_DocumentGroupID' class='filter' onchange='showFilter();'>
				<option value='0'>--- Pilih Grup ---</option>"; // Arief F - 17092018

			$query = "SELECT *
					  FROM M_DocumentGroup
					  WHERE DocumentGroup_Delete_Time is NULL";
			$sql = mysql_query($query);

			while ($field = mysql_fetch_array($sql) ){
				//if($_GET['optTHROLD_DocumentGroupID'] == $field['DocumentGroup_ID']){ // Arief F - 17092018
//$ActionContent .="<option value='$field[DocumentGroup_ID]' selected>$field[DocumentGroup_Name]</option>"; // Arief F - 17092018
				//}else{ // Arief F - 17092018
$ActionContent .="
				<option value='$field[DocumentGroup_ID]'>$field[DocumentGroup_Name]</option>";
				//} // Arief F - 17092018
			}
$ActionContent .="
			</select>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>SEARCH</td>
		<td>:</td>
		<td colspan='2'>
			<input name='txtSearch' type='text'/>
		</td>
	</tr>";
	if (isset($_GET[filter])) {
$ActionContent .="
	<tr>
		<td>Filter</td>
		<td>:</td>
		<td colspan=4>
			<select name='optFilterHeader' id='optFilterHeader' class='filter'  onchange='showFilterDetail(this.value);'>
				<option value='0'>--- Pilih Grup Dokumen Terlebih Dahulu ---</option>
			</select>
		</td>
	</tr>
	<tr>
		<td></td><td></td><td>
			<select name='optFilterDetail' id='optFilterDetail' class='filter'>
				<option value='0'>--- Pilih Filter Terlebih Dahulu ---</option>
			</select>
		</td>
	</tr>
	<tr>
		<td></td><td></td><td>
			<div id='optPhase' style='display:none;'>
			Tahap GRL : <input type='text'  name='phase' id='phase' size='5'>
			</div>
		</td>
	</tr>
";
	}
$ActionContent .="
	</table>
	</form>
";

/* ====== */
/* ACTION */
/* ====== */

if(isset($_GET['listdocument'])) {
// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

	if ($_GET['optTHROLD_DocumentGroupID'] == '1' or $_GET['optTHROLD_DocumentGroupID'] == '2'){
		if ($_GET['optTransactionID']==1) {
			$query = "SELECT DISTINCT throld.THROLD_ID AS ID, throld.THROLD_RegistrationCode, throld.THROLD_RegistrationDate,
					  u.User_FullName, c.Company_Name, drs.DRS_Description, dg.DocumentGroup_Name
					  FROM TH_RegistrationOfLegalDocument throld, M_User u, M_Company c, M_DocumentGroup dg,
						   TD_RegistrationOfLegalDocument tdrold, M_DocumentRegistrationStatus drs
					  WHERE throld.THROLD_Delete_Time is NULL
					  AND throld.THROLD_CompanyID=c.Company_ID
					  AND throld.THROLD_UserID=u.User_ID
					  AND throld.THROLD_DocumentGroupID='$_GET[optTHROLD_DocumentGroupID]'
					  AND throld.THROLD_DocumentGroupID=dg.DocumentGroup_ID
					  AND tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
					  AND throld.THROLD_Status=drs.DRS_Name ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							throld.THROLD_RegistrationCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==2) {
			$query = "
				SELECT thlold.THLOLD_ID AS ID, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate,
					   u.User_FullName, c.Company_Name, lc.LoanCategory_Name, dg.DocumentGroup_Name,
					   drs.DRS_Description
				FROM TH_LoanOfLegalDocument thlold
				LEFT JOIN M_User u
				ON thlold.THLOLD_UserID = u.User_ID
				LEFT JOIN M_Company c
				ON thlold.THLOLD_CompanyID = c.Company_ID
				LEFT JOIN M_LoanCategory lc
				ON thlold.THLOLD_LoanCategoryID = lc.LoanCategory_ID
				LEFT JOIN M_DocumentGroup dg
				ON thlold.THLOLD_DocumentGroupID = dg.DocumentGroup_ID
				LEFT JOIN M_DocumentRegistrationStatus drs
				ON thlold.THLOLD_Status = drs.DRS_Name
				LEFT JOIN TD_LoanOfLegalDocument tdlold
				ON tdlold.TDLOLD_THLOLD_ID = thlold.THLOLD_ID
				LEFT JOIN M_DocumentLegal dl
				ON tdlold.TDLOLD_DocCode = dl.DL_DocCode
				WHERE thlold.THLOLD_Delete_Time IS NULL
				AND thlold.THLOLD_DocumentGroupID = '".$_GET[optTHROLD_DocumentGroupID]."'
			";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thlold.THLOLD_LoanCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR tdlold.TDLOLD_DocCode LIKE '%$search%'
						)";
			}
			$groupby ="
				GROUP BY thlold.THLOLD_ID, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate,
					     u.User_FullName, c.Company_Name, lc.LoanCategory_Name, dg.DocumentGroup_Name,
					     drs.DRS_Description
			";
		}
		else if ($_GET['optTransactionID']==3) {
			$query = "	SELECT DISTINCT rel.THROLD_ID AS ID,
										rel.THROLD_ReleaseCode,
										rel.THROLD_ReleaseDate,
										users.User_FullName,
										grupDok.DocumentGroup_Name,
										statusTransaksi.DRS_Description
						FROM TH_ReleaseOfLegalDocument rel
						LEFT JOIN TH_LoanOfLegalDocument loan
							ON loan.THLOLD_LoanCode = rel.THROLD_THLOLD_Code AND loan.THLOLD_DocumentGroupID='$_GET[optTHROLD_DocumentGroupID]'
						LEFT JOIN M_DocumentGroup grupDok
							ON loan.THLOLD_DocumentGroupID = grupDok.DocumentGroup_ID
						LEFT JOIN TD_LoanOfLegalDocument detailLoan
							ON detailLoan.TDLOLD_THLOLD_ID = loan.THLOLD_ID
						LEFT JOIN M_DocumentLegal dokLegal
							ON dokLegal.DL_DocCode = detailLoan.TDLOLD_DocCode
						LEFT JOIN M_DocumentRegistrationStatus statusTransaksi
							ON statusTransaksi.DRS_Name = rel.THROLD_Status
						LEFT JOIN M_User users
							ON users.User_ID = rel.THROLD_UserID
						WHERE rel.THROLD_Delete_Time is NULL ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							rel.THROLD_ReleaseCode LIKE '%$search%'
							OR users.User_FullName LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==4) {
			$query = "SELECT DISTINCT tdrtold.TDRTOLD_ID AS ID, tdrtold.TDRTOLD_ReturnCode, tdrtold.TDRTOLD_ReturnTime,
					  u.User_FullName, dg.DocumentGroup_Name
					  FROM TD_ReturnOfLegalDocument tdrtold, M_User u, M_DocumentGroup dg, M_DocumentLegal dl
					  WHERE tdrtold.TDRTOLD_Delete_Time is NULL
					  AND tdrtold.TDRTOLD_UserID=u.User_ID
					  AND tdrtold.TDRTOLD_DocCode=dl.DL_DocCode
					  AND dl.DL_GroupDocID='$_GET[optTHROLD_DocumentGroupID]'
					  AND dl.DL_GroupDocID=dg.DocumentGroup_ID ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							tdrtold.TDRTOLD_ReturnCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		if ($_GET['optFilterHeader']==1) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throld.THROLD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thlold.THLOLD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND loan.THLOLD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dl.DL_CompanyID='$_GET[optFilterDetail]' ";
			}
		}

		if ($_GET['optFilterHeader']==2) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND tdrold.TDROLD_DocumentCategoryID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND dl.DL_CategoryDocID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND dokLegal.DL_CategoryDocID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dl.DL_CategoryDocID='$_GET[optFilterDetail]' ";
			}
		}

		if ($_GET['optFilterHeader']==3) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND tdrold.TDROLD_DocumentTypeID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND dl.DL_TypeDocID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND dokLegal.DL_TypeDocID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dl.DL_TypeDocID='$_GET[optFilterDetail]' ";
			}
		}
		if ($_GET['optFilterHeader']==4) {

			if ($_GET['optTransactionID']==1) {
				$query .="AND throld.THROLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thlold.THLOLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND rel.THROLD_Status='$_GET[optFilterDetail]' ";
			}
		}

		$orderby ="ORDER BY ID DESC LIMIT $offset, $dataPerPage";
	}

	if ($_GET['optTHROLD_DocumentGroupID']=='3'){
		if ($_GET['optTransactionID']==1) {
			$query = "SELECT DISTINCT throld.THRGOLAD_ID AS ID, throld.THRGOLAD_RegistrationCode,
									  throld.THRGOLAD_RegistrationDate, u.User_FullName, c.Company_Name,
									  drs.DRS_Description, throld.THRGOLAD_Phase
					  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_User u, M_Company c,
						   M_DocumentRegistrationStatus drs
					  WHERE throld.THRGOLAD_Delete_Time is NULL
					  AND throld.THRGOLAD_CompanyID=c.Company_ID
					  AND throld.THRGOLAD_UserID=u.User_ID
					  AND drs.DRS_Name=throld.THRGOLAD_RegStatus ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							throld.THRGOLAD_RegistrationCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==2) {
			$query = "
				SELECT thlolad.THLOLAD_ID AS ID, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate,
					   u.User_FullName, c.Company_Name, lc.LoanCategory_Name,drs.DRS_Description
				FROM TH_LoanOfLandAcquisitionDocument thlolad
				LEFT JOIN M_User u
				  ON thlolad.THLOLAD_UserID=u.User_ID
				LEFT JOIN M_Company c
				  ON thlolad.THLOLAD_CompanyID=c.Company_ID
				LEFT JOIN M_LoanCategory lc
				  ON thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
				LEFT JOIN TD_LoanOfLandAcquisitionDocument tdlolad
				  ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				LEFT JOIN M_DocumentLandAcquisition dla
				  ON tdlolad.TDLOLAD_DocCode=dla.DLA_Code
				LEFT JOIN M_DocumentRegistrationStatus drs
				  ON thlolad.THLOLAD_Status=drs.DRS_Name
				WHERE thlolad.THLOLAD_Delete_Time IS NULL
			";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thlolad.THLOLAD_LoanCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR tdlolad.TDLOLAD_DocCode LIKE '%$search%'
						)";
			}
			$groupby ="
				GROUP BY thlolad.THLOLAD_ID, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate,
						 u.User_FullName, c.Company_Name, lc.LoanCategory_Name,drs.DRS_Description
			";
		}
		else if ($_GET['optTransactionID']==3) {
			$query = "SELECT DISTINCT thrlolad.THRLOLAD_ID AS ID, thrlolad.THRLOLAD_ReleaseCode,
									  thrlolad.THRLOLAD_ReleaseDate, u.User_FullName, drs.DRS_Description
					  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_User u, TH_LoanOfLandAcquisitionDocument thlolad,
						   TD_ReleaseOfLandAcquisitionDocument tdrlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
						   M_DocumentLandAcquisition dla, M_DocumentRegistrationStatus drs
					  WHERE thrlolad.THRLOLAD_Delete_Time is NULL
					  AND thrlolad.THRLOLAD_UserID=u.User_ID
					  AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
					  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
					  AND tdlolad.TDLOLAD_DocCode=dla.DLA_Code
					  AND thrlolad.THRLOLAD_Status=drs.DRS_Name ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thrlolad.THRLOLAD_ReleaseCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==4) {
			$query = "SELECT DISTINCT tdrtolad.TDRTOLAD_ID AS ID, tdrtolad.TDRTOLAD_ReturnCode, tdrtolad.TDRTOLAD_ReturnTime,
									  u.User_FullName
					  FROM TD_ReturnOfLandAcquisitionDocument tdrtolad, M_User u, M_DocumentLandAcquisition dla
					  WHERE tdrtolad.TDRTOLAD_Delete_Time is NULL
					  AND tdrtolad.TDRTOLAD_UserID=u.User_ID
					  AND tdrtolad.TDRTOLAD_DocCode=dla.DLA_Code ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							tdrtolad.TDRTOLAD_ReturnCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		if ($_GET['optFilterHeader']==1) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throld.THRGOLAD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thlolad.THLOLAD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND thlolad.THLOLAD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dla.DLA_CompanyID='$_GET[optFilterDetail]' ";
			}
		}

		if ($_GET['optFilterHeader']==4) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throld.THRGOLAD_RegStatus='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thlolad.THLOLAD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND thrlolad.THRLOLAD_Status='$_GET[optFilterDetail]' ";
			}
		}

		if ($_GET[phase]<>NULL) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throld.THRGOLAD_Phase='$_GET[phase]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND tdlolad.TDLOLAD_Phase='$_GET[phase]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND tdlolad.TDLOLAD_Phase='$_GET[phase]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dla.DLA_Phase='$_GET[phase]' ";
			}
		}

		$orderby ="ORDER BY ID DESC LIMIT $offset, $dataPerPage";
	}

	if ($_GET['optTHROLD_DocumentGroupID'] == '4'){
		if ($_GET['optTransactionID']==1) {
			$query = "SELECT DISTINCT throaod.THROAOD_ID AS ID, throaod.THROAOD_RegistrationCode,
									  throaod.THROAOD_RegistrationDate, u.User_FullName, '-',
									  drs.DRS_Description
					  FROM TH_RegistrationOfAssetOwnershipDocument throaod, M_User u,
						   M_DocumentRegistrationStatus drs
					  WHERE throaod.THROAOD_Delete_Time is NULL
					  AND throaod.THROAOD_UserID=u.User_ID
					  AND drs.DRS_Name=throaod.THROAOD_Status ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							throaod.THROAOD_RegistrationCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==2) {
			$query = "SELECT thloaod.THLOAOD_ID AS ID, thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_LoanDate,
					   u.User_FullName, c.Company_Name, lc.LoanCategory_Name,drs.DRS_Description
				FROM TH_LoanOfAssetOwnershipDocument thloaod
				LEFT JOIN M_User u
				  ON thloaod.THLOAOD_UserID=u.User_ID
				LEFT JOIN M_Company c
				  ON thloaod.THLOAOD_CompanyID=c.Company_ID
				LEFT JOIN M_LoanCategory lc
				  ON thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
				LEFT JOIN TD_LoanOfAssetOwnershipDocument tdloaod
				  ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
				LEFT JOIN M_DocumentRegistrationStatus drs
				  ON thloaod.THLOAOD_Status=drs.DRS_Name
				WHERE thloaod.THLOAOD_Delete_Time IS NULL
			";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thloaod.THLOAOD_LoanCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							-- OR tdloaod.TDLOAOD_DocCode LIKE '%$search%'
						)";
			}
			$groupby ="
				GROUP BY thloaod.THLOAOD_ID, thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_LoanDate,
						 u.User_FullName, c.Company_Name, lc.LoanCategory_Name, drs.DRS_Description
			";
			// echo $query.$groupby;
		}
		else if ($_GET['optTransactionID']==3) {
			$query = "SELECT DISTINCT thrloaod.THROAOD_ID AS ID, thrloaod.THROAOD_ReleaseCode,
									  thrloaod.THROAOD_ReleaseDate, u.User_FullName, drs.DRS_Description
					  FROM TH_ReleaseOfAssetOwnershipDocument thrloaod, M_User u, TH_LoanOfAssetOwnershipDocument thloaod,
						   TD_ReleaseOfAssetOwnershipDocument tdrloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
						   M_DocumentAssetOwnership dao, M_DocumentRegistrationStatus drs
					  WHERE thrloaod.THROAOD_Delete_Time is NULL
					  AND thrloaod.THROAOD_UserID=u.User_ID
					  AND thrloaod.THROAOD_THLOAOD_Code=thloaod.THLOAOD_LoanCode
					  AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
					  AND tdloaod.TDLOAOD_DocCode=dao.DAO_DocCode
					  AND thrloaod.THROAOD_Status=drs.DRS_Name ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thrloaod.THROAOD_ReleaseCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==4) {
			$query = "SELECT DISTINCT tdrtoaod.TDRTOAOD_ID AS ID, tdrtoaod.TDRTOAOD_ReturnCode, tdrtoaod.TDRTOAOD_ReturnTime,
									  u.User_FullName
					  FROM TD_ReturnOfAssetOwnershipDocument tdrtoaod, M_User u, M_DocumentAssetOwnership dao
					  WHERE tdrtoaod.TDRTOAOD_Delete_Time is NULL
					  AND tdrtoaod.TDRTOAOD_UserID=u.User_ID
					  AND tdrtoaod.TDRTOAOD_DocCode=dao.DAO_DocCode ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							tdrtoaod.TDRTOAOD_ReturnCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		if ($_GET['optFilterHeader']==1) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throaod.THROAOD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				if($_GET['optFilterDetail'] == "COP"){
					$query .= "AND thloaod.THLOAOD_CompanyID = '87'";
				}else{
					$query .="AND thloaod.THLOAOD_CompanyID='$_GET[optFilterDetail]' ";
				}
			}
			elseif ($_GET['optTransactionID']==3) {
				if($_GET['optFilterDetail'] == "COP"){
					$query .= "AND dao.DAO_Employee_NIK NOT LIKE '%CO@%'";
				}else{
					$query_comp = "SELECT *
							  FROM M_Company
							  WHERE Company_ID='$_GET[optFilterDetail]'";
					$field_comp = mysql_fetch_array(mysql_query($query_comp));
					$Company_Code=$field_comp['Company_Code'];
					$query .= "AND dao.DAO_Employee_NIK = 'CO@$Company_Code'";
				}
			}
			elseif ($_GET['optTransactionID']==4) {
				if($_GET['optFilterDetail'] == "COP"){
					$query .= "AND dao.DAO_Employee_NIK NOT LIKE '%CO@%'";
				}else{
					$query_comp = "SELECT *
							  FROM M_Company
							  WHERE Company_ID='$_GET[optFilterDetail]'";
					$field_comp = mysql_fetch_array(mysql_query($query_comp));
					$Company_Code=$field_comp['Company_Code'];
					$query .= "AND dao.DAO_Employee_NIK = 'CO@$Company_Code'";
				}
			}
		}

		if ($_GET['optFilterHeader']==4) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throaod.THROAOD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thloaod.THLOAOD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND thrloaod.THROAOD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dao.DAO_Status='$_GET[optFilterDetail]' ";
			}
		}

		$orderby ="ORDER BY ID DESC LIMIT $offset, $dataPerPage";
	}

	if ($_GET['optTHROLD_DocumentGroupID'] == '5'){
		if ($_GET['optTransactionID']==1) {
			$query = "SELECT DISTINCT throold.THROOLD_ID AS ID, throold.THROOLD_RegistrationCode,
									  throold.THROOLD_RegistrationDate, u.User_FullName, c.Company_Name,
									  drs.DRS_Description
					  FROM TH_RegistrationOfOtherLegalDocuments throold, M_User u, M_Company c,
						   M_DocumentRegistrationStatus drs
					  WHERE throold.THROOLD_Delete_Time is NULL
					  AND throold.THROOLD_CompanyID=c.Company_ID
					  AND throold.THROOLD_UserID=u.User_ID
					  AND drs.DRS_Name=throold.THROOLD_Status ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							throold.THROOLD_RegistrationCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==2) {
			$query = "SELECT thloold.THLOOLD_ID AS ID, thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate,
					   u.User_FullName, c.Company_Name, lc.LoanCategory_Name,drs.DRS_Description
				FROM TH_LoanOfOtherLegalDocuments thloold
				LEFT JOIN M_User u
				  ON thloold.THLOOLD_UserID=u.User_ID
				LEFT JOIN M_Company c
				  ON thloold.THLOOLD_CompanyID=c.Company_ID
				LEFT JOIN M_LoanCategory lc
				  ON thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
				LEFT JOIN TD_LoanOfOtherLegalDocuments tdloold
				  ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
				LEFT JOIN M_DocumentRegistrationStatus drs
				  ON thloold.THLOOLD_Status=drs.DRS_Name
				WHERE thloold.THLOOLD_Delete_Time IS NULL
			";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thloold.THLOOLD_LoanCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							-- OR tdloaod.TDLOAOD_DocCode LIKE '%$search%'
						)";
			}
			$groupby ="
				GROUP BY thloold.THLOOLD_ID, thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate,
						 u.User_FullName, c.Company_Name, lc.LoanCategory_Name, drs.DRS_Description
			";
		}
		else if ($_GET['optTransactionID']==3) {
			$query = "SELECT DISTINCT thrloold.THROOLD_ID AS ID, thrloold.THROOLD_ReleaseCode,
									  thrloold.THROOLD_ReleaseDate, u.User_FullName, drs.DRS_Description
					  FROM TH_ReleaseOfOtherLegalDocuments thrloold, M_User u, TH_LoanOfOtherLegalDocuments thloold,
						   TD_ReleaseOfOtherLegalDocuments tdrloold, TD_LoanOfOtherLegalDocuments tdloold,
						   M_DocumentsOtherLegal dol, M_DocumentRegistrationStatus drs
					  WHERE thrloold.THROOLD_Delete_Time is NULL
					  AND thrloold.THROOLD_UserID=u.User_ID
					  AND thrloold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
					  AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
					  AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
					  AND thrloold.THROOLD_Status=drs.DRS_Name ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thrloold.THROOLD_ReleaseCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==4) {
			$query = "SELECT DISTINCT tdrtoold.TDRTOOLD_ID AS ID, tdrtoold.TDRTOOLD_ReturnCode, tdrtoold.TDRTOOLD_ReturnTime,
									  u.User_FullName
					  FROM TD_ReturnOfOtherLegalDocuments tdrtoold, M_User u, M_DocumentsOtherLegal dol
					  WHERE tdrtoold.TDRTOOLD_Delete_Time is NULL
					  AND tdrtoold.TDRTOOLD_UserID=u.User_ID
					  AND tdrtoold.TDRTOOLD_DocCode=dla.DLA_Code ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							tdrtoold.TDRTOOLD_ReturnCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		if ($_GET['optFilterHeader']==1) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throold.THROOLD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thloold.THLOOLD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND dol.DOL_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dol.DOL_CompanyID='$_GET[optFilterDetail]' ";
			}
		}

		if ($_GET['optFilterHeader']==4) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throold.THROOLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thloold.THLOOLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND thrloold.THROOLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND dol.DOL_Status='$_GET[optFilterDetail]' ";
			}
		}

		$orderby ="ORDER BY ID DESC LIMIT $offset, $dataPerPage";
	}

	if ($_GET['optTHROLD_DocumentGroupID'] == '6'){
		if ($_GET['optTransactionID']==1) {
			$query = "SELECT DISTINCT throonld.THROONLD_ID AS ID, throonld.THROONLD_RegistrationCode,
									  throonld.THROONLD_RegistrationDate, u.User_FullName, c.Company_Name,
									  drs.DRS_Description
					  FROM TH_RegistrationOfOtherNonLegalDocuments throonld, M_User u, M_Company c,
						   M_DocumentRegistrationStatus drs
					  WHERE throonld.THROONLD_Delete_Time is NULL
					  AND throonld.THROONLD_CompanyID=c.Company_ID
					  AND throonld.THROONLD_UserID=u.User_ID
					  AND drs.DRS_Name=throonld.THROONLD_Status ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							throonld.THROONLD_RegistrationCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==2) {
			$query = "SELECT thloonld.THLOONLD_ID AS ID, thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_LoanDate,
					   u.User_FullName, c.Company_Name, lc.LoanCategory_Name,drs.DRS_Description
				FROM TH_LoanOfOtherNonLegalDocuments thloonld
				LEFT JOIN M_User u
				  ON thloonld.THLOONLD_UserID=u.User_ID
				LEFT JOIN M_Company c
				  ON thloonld.THLOONLD_CompanyID=c.Company_ID
				LEFT JOIN M_LoanCategory lc
				  ON thloonld.THLOONLD_LoanCategoryID=lc.LoanCategory_ID
				LEFT JOIN TD_LoanOfOtherNonLegalDocuments tdloonld
				  ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
				LEFT JOIN M_DocumentRegistrationStatus drs
				  ON thloonld.THLOONLD_Status=drs.DRS_Name
				WHERE thloonld.THLOONLD_Delete_Time IS NULL
			";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thloonld.THLOONLD_LoanCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							-- OR tdloaod.TDLOAOD_DocCode LIKE '%$search%'
						)";
			}
			$groupby ="
				GROUP BY thloonld.THLOONLD_ID, thloonld.THLOONLD_LoanCode, thloonld.THLOONLD_LoanDate,
						 u.User_FullName, c.Company_Name, lc.LoanCategory_Name, drs.DRS_Description
			";
			// echo $query.$groupby;
		}
		else if ($_GET['optTransactionID']==3) {
			$query = "SELECT DISTINCT thrloonld.THROONLD_ID AS ID, thrloonld.THROONLD_ReleaseCode,
									  thrloonld.THROONLD_ReleaseDate, u.User_FullName, drs.DRS_Description
					  FROM TH_ReleaseOfOtherNonLegalDocuments thrloonld, M_User u, TH_LoanOfOtherNonLegalDocuments thloonld,
						   TD_ReleaseOfOtherNonLegalDocuments tdrloonld, TD_LoanOfOtherNonLegalDocuments tdloonld,
						   M_DocumentsOtherNonLegal donl, M_DocumentRegistrationStatus drs
					  WHERE thrloonld.THROONLD_Delete_Time is NULL
					  AND thrloonld.THROONLD_UserID=u.User_ID
					  AND thrloonld.THROONLD_THLOONLD_Code=thloonld.THLOONLD_LoanCode
					  AND tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
					  AND tdloonld.TDLOONLD_DocCode=donl.DONL_DocCode
					  AND thrloonld.THROONLD_Status=drs.DRS_Name ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							thrloonld.THROONLD_ReleaseCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		else if ($_GET['optTransactionID']==4) {
			$query = "SELECT DISTINCT tdrtoonld.TDRTOONLD_ID AS ID, tdrtoonld.TDRTOONLD_ReturnCode, tdrtoonld.TDRTOONLD_ReturnTime,
									  u.User_FullName
					  FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld, M_User u, M_DocumentsOtherNonLegal donl
					  WHERE tdrtoonld.TDRTOONLD_Delete_Time is NULL
					  AND tdrtoonld.TDRTOONLD_UserID=u.User_ID
					  AND tdrtoonld.TDRTOONLD_DocCode=donl.DONL_DocCode ";
			if ($_GET['txtSearch']) {
				$search=$_GET['txtSearch'];
				$query .="AND (
							tdrtoonld.TDRTOONLD_ReturnCode LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
						)";
			}
		}
		if ($_GET['optFilterHeader']==1) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throonld.THROONLD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thloonld.THLOONLD_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND donl.DONL_CompanyID='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND donl.DONL_CompanyID='$_GET[optFilterDetail]' ";
			}
		}

		if ($_GET['optFilterHeader']==4) {
			if ($_GET['optTransactionID']==1) {
				$query .="AND throonld.THROONLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==2) {
				$query .="AND thloonld.THLOONLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==3) {
				$query .="AND thrloonld.THROONLD_Status='$_GET[optFilterDetail]' ";
			}
			elseif ($_GET['optTransactionID']==4) {
				$query .="AND donl.DONL_Status='$_GET[optFilterDetail]' ";
			}
		}

		// if ($_GET[phase]<>NULL) {
		// 	if ($_GET['optTransactionID']==1) {
		// 		$query .="AND throld.THRGOLAD_Phase='$_GET[phase]' ";
		// 	}
		// 	elseif ($_GET['optTransactionID']==2) {
		// 		$query .="AND tdlolad.TDLOLAD_Phase='$_GET[phase]' ";
		// 	}
		// 	elseif ($_GET['optTransactionID']==3) {
		// 		$query .="AND tdlolad.TDLOLAD_Phase='$_GET[phase]' ";
		// 	}
		// 	elseif ($_GET['optTransactionID']==4) {
		// 		$query .="AND dla.DLA_Phase='$_GET[phase]' ";
		// 	}
		// }

		$orderby ="ORDER BY ID DESC LIMIT $offset, $dataPerPage";
	}

$allquery=$query.$groupby.$orderby;
$sql = mysql_query($allquery);
$num = mysql_num_rows($sql);

$sqldg = mysql_query($allquery);
$arr = mysql_fetch_array($sqldg);

//echo $allquery;die();

	if ($_GET['optTHROLD_DocumentGroupID'] == '1' or $_GET['optTHROLD_DocumentGroupID'] == '2'){
		// Menampilkan Daftar Transaksi Registrasi Dokumen
		if ($_GET['optTransactionID']==1) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Registrasi Dokumen $arr[DocumentGroup_Name]</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
					<th></th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THROLD_RegistrationDate']);
				$fregdate=date("j M Y", $regdate);
						$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-registration-document.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>
						<a href='detail-of-registration-document.php?act=".$decrp->encrypt('edit')."&id=".$decrp->encrypt($field[0])."'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
					</td>
				</tr>
				";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}


		// Menampilkan Daftar Transaksi Untuk Permintaan Dokumen
		else if ($_GET['optTransactionID']==2) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=7 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Permohonan Permintaan Dokumen$arr[DocumentGroup_Name]</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$loandate=strtotime($field['THLOLD_LoanDate']);
				$floandate=date("j M Y", $loandate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-loan-document.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$floandate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>$field[7]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengeluaran Dokumen
		else if ($_GET['optTransactionID']==3) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>

					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengeluaran Dokumen $arr[DocumentGroup_Name]</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THROLD_ReleaseDate']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-release-document.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[5]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengembalian Dokumen
		else if ($_GET['optTransactionID']==4) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengembalian Dokumen $arr[DocumentGroup_Name]</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['TDRTOLD_ReturnTime']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-return-document.php?act=detail&id=$field[1]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
				</tr>
";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}
	}

	// UNTUK TRANSAKSI DOKUMEN Pembebasan Lahan
	if ($_GET['optTHROLD_DocumentGroupID']=='3'){
		// Menampilkan Daftar Transaksi Registrasi Dokumen Pembebasan Lahan
		if ($_GET['optTransactionID']==1) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Registrasi Dokumen Pembebasan Lahan</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
					<th></th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THRGOLAD_RegistrationDate']);
				$fregdate=date("j M Y", $regdate);
						$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-registration-land-acquisition-document.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>
						<a href='detail-of-registration-land-acquisition-document.php?act=".$decrp->encrypt('edit')."&id=".$decrp->encrypt($field[0])."'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
					</td>
				</tr>
				";
				}
$MainContent .="
				</table>

			";
			}
		}


		// Menampilkan Daftar Transaksi Untuk Permintaan Dokumen
		else if ($_GET['optTransactionID']==2) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=7 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Permohonan Permintaan Dokumen$arr[DocumentGroup_Name]</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$loandate=strtotime($field['THLOLAD_LoanDate']);
				$floandate=date("j M Y", $loandate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-loan-land-acquisition-document.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$floandate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>$field[6]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengeluaran Dokumen
		else if ($_GET['optTransactionID']==3) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>

					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengeluaran Dokumen $arr[DocumentGroup_Name]</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THRLOLAD_ReleaseDate']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-release-land-acquisition-document.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengembalian Dokumen
		else if ($_GET['optTransactionID']==4) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengembalian Dokumen $arr[DocumentGroup_Name]</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['TDRTOLAD_ReturnTime']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-return-land-acquisition-document.php?act=detail&id=$field[1]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
				</tr>
";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}
	}

	// UNTUK TRANSAKSI DOKUMEN Kepemilikan Aset
	if ($_GET['optTHROLD_DocumentGroupID']=='4'){
		// Menampilkan Daftar Transaksi Registrasi Dokumen Kepemilikan Aset
		if ($_GET['optTransactionID']==1) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Registrasi Dokumen Kepemilikan Aset</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
					<th></th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THROAOD_RegistrationDate']);
				$fregdate=date("j M Y", $regdate);
						$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-registration-asset-ownership-document.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>
						<a href='detail-of-registration-asset-ownership-document.php?act=".$decrp->encrypt('edit')."&id=".$decrp->encrypt($field[0])."'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
					</td>
				</tr>
				";
				}
$MainContent .="
				</table>

			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Permintaan Dokumen
		else if ($_GET['optTransactionID']==2) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=7 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-asset-ownership-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Permohonan Permintaan Dokumen Kepemilikan Aset</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$loandate=strtotime($field['THLOAOD_LoanDate']);
				$floandate=date("j M Y", $loandate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-loan-asset-ownership-document.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$floandate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>$field[6]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengeluaran Dokumen
		else if ($_GET['optTransactionID']==3) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>

					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-asset-ownership-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengeluaran Dokumen Kepemilikan Aset</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THRLOAOD_ReleaseDate']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-release-asset-ownership-document.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengembalian Dokumen
		else if ($_GET['optTransactionID']==4) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-asset-ownership-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengembalian Dokumen Kepemilikan Aset</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['TDRTOAOD_ReturnTime']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-return-asset-ownership-document.php?act=detail&id=$field[1]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
				</tr>
";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}
	}

	// UNTUK TRANSAKSI DOKUMEN Lainnya (Legal)
	if ($_GET['optTHROLD_DocumentGroupID']=='5'){
		// Menampilkan Daftar Transaksi Registrasi Dokumen Lainnya (Legal)
		if ($_GET['optTransactionID']==1) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Registrasi Dokumen Lainnya (Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
					<th></th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THROOLD_RegistrationDate']);
				$fregdate=date("j M Y", $regdate);
						$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-registration-other-legal-documents.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>
						<a href='detail-of-registration-other-legal-documents.php?act=".$decrp->encrypt('edit')."&id=".$decrp->encrypt($field[0])."'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
					</td>
				</tr>
				";
				}
$MainContent .="
				</table>

			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Permintaan Dokumen
		else if ($_GET['optTransactionID']==2) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=7 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-other-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Permohonan Permintaan Dokumen Lainnya (Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$loandate=strtotime($field['THLOOLD_LoanDate']);
				$floandate=date("j M Y", $loandate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-loan-other-non-legal-documents.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$floandate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>$field[6]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengeluaran Dokumen
		else if ($_GET['optTransactionID']==3) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>

					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-other-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengeluaran Dokumen Lainnya (Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THROOLD_ReleaseDate']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-release-other-legal-documents.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengembalian Dokumen
		else if ($_GET['optTransactionID']==4) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-other-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengembalian Dokumen Lainnya (Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['TDRTOOLD_ReturnTime']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-return-other-legal-documents.php?act=detail&id=$field[1]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
				</tr>
";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}
	}

	// UNTUK TRANSAKSI DOKUMEN Lainnya (Di Luar Legal)
	if ($_GET['optTHROLD_DocumentGroupID']=='6'){
		// Menampilkan Daftar Transaksi Registrasi Dokumen Lainnya (Di Luar Legal)
		if ($_GET['optTransactionID']==1) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Registrasi Dokumen Lainnya (Di Luar Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pendaftaran</th>
					<th>Tanggal Pendaftaran</th>
					<th>Nama Pendaftar</th>
					<th>Nama Perusahaan</th>
					<th>Status</th>
					<th></th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THROONLD_RegistrationDate']);
				$fregdate=date("j M Y", $regdate);
						$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-registration-other-non-legal-documents.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>
						<a href='detail-of-registration-other-non-legal-documents.php?act=".$decrp->encrypt('edit')."&id=".$decrp->encrypt($field[0])."'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
					</td>
				</tr>
				";
				}
$MainContent .="
				</table>

			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Permintaan Dokumen
		else if ($_GET['optTransactionID']==2) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=7 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-document-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Permohonan Permintaan Dokumen Lainnya (Di Luar Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Permintaan</th>
					<th>Tanggal Permintaan</th>
					<th>Nama Peminjam</th>
					<th>Nama Perusahaan</th>
					<th>Kategori Permintaan</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$loandate=strtotime($field['THLOONLD_LoanDate']);
				$floandate=date("j M Y", $loandate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-loan-other-non-legal-documents.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$floandate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
					<td class='center'>$field[5]</td>
					<td class='center'>$field[6]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengeluaran Dokumen
		else if ($_GET['optTransactionID']==3) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>

					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-other-non-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengeluaran Dokumen Lainnya (Di Luar Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengeluaran</th>
					<th>Tanggal Pengeluaran</th>
					<th>Dikeluarkan Oleh</th>
					<th>Status</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['THRLOONLD_ReleaseDate']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-release-other-non-legal-documents.php?id=$field[0]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
					<td class='center'>$field[4]</td>
				</tr>";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}

		// Menampilkan Daftar Transaksi Untuk Pengembalian Dokumen
		else if ($_GET['optTransactionID']==4) {
			if ($num==NULL) {
$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
				<tr>
					<td colspan=6 align='center'>Belum Ada Data</td>
				</tr>
				</table>
";
			}

			if ($num<>NULL){
$MainContent .="
				<form name='list' method='post' action='print-other-non-legal-documents-barcode.php' onsubmit='return validateBarcodePrint(this);'>
				<table width='100%' border='1' class='stripeMe'>
				<tr>
					<th colspan=7 align='center'>Daftar Transaksi Pengembalian Dokumen Lainnya (Di Luar Legal)</th>
				</tr>
				<tr>
					<th>ID</th>
					<th>Kode Pengembalian</th>
					<th>Tanggal Pengembalian</th>
					<th>Nama Penerima Dokumen</th>
				</tr>
";

				while ($field = mysql_fetch_array($sql)) {
				$regdate=strtotime($field['TDRTOONLD_ReturnTime']);
				$fregdate=date("j M Y", $regdate);

$MainContent .="
				<tr>
					<td class='center'>$field[0]</td>
					<td class='center'>
						<a href='detail-of-return-other-non-legal-documents.php?act=detail&id=$field[1]' class='underline'>$field[1]</a>
					</td>
					<td class='center'>$fregdate</td>
					<td class='center'>$field[3]</td>
				</tr>
";
				}
$MainContent .="
				</table>
				</form>
			";
			}
		}
	}

		$sql1 = mysql_query($query.$groupby);
		$num1 = mysql_num_rows($sql1);

		$getLink=$_SERVER["REQUEST_URI"];
		$arr = explode("&page=", $getLink);
		$link = $arr[0];

		$jumData = $num1;
		$jumPage = ceil($jumData/$dataPerPage);

		$prev=$noPage-1;
		$next=$noPage+1;

		if ($noPage > 1)
			$Pager.="<a href='$link&page=$prev'>&lt;&lt; Prev</a> ";
		for($p=1; $p<=$jumPage; $p++) {
			if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
				if (($showPage == 1) && ($p != 2))
					$Pager.="...";
				if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
					$Pager.="...";
				if ($p == $noPage)
					$Pager.="<b><u>$p</b></u> ";
				else
					$Pager.="<a href='$link&page=$p'>$p</a> ";

				$showPage = $p;
			}
		}

		if ($noPage < $jumPage)
			$Pager .= "<a href='$link&page=$next'>Next &gt;&gt;</a> ";
	}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>
