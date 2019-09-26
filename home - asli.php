<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 04 Mei 2012																						=
= Update Terakhir	: 29 Mei 2012																						=
= Revisi			:																									=
=		25 Mei 2012 : Persetujuan Untuk Registrasi Dokumen GRL (OK)														=
=		29 Mei 2012 : Persetujuan Permintaan & Pengeluaran Dokumen GRL (OK)												=
=========================================================================================================================
*/
session_start(); 
?>
<title>Custodian System | Beranda</title>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
	
require_once "./include/template.inc";
include ("./include/class.endencrp.php");

$decrp = new custodian_encryp;
$page=new Template();

// Cari apakah user yang login mempunyai hak untuk menyetujui transaksi
$query = "SELECT * 
		  FROM M_Approval 
		  WHERE A_ApproverID='$_SESSION[User_ID]' 
		  AND A_Status='2' 
		  AND A_Delete_Time IS NULL";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

// Jika memiliki hak untuk menyetujui transaksi :
if ($num>0) {
	/* ---------------------------- */
	/* Daftar Persetujuan Transaksi */
	/* ---------------------------- */
	
	$MainContent ="<div class='home-title'>Menunggu Persetujuan Anda</div>";
	
	$query = "SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_RegistrationCode KodeTransaksi, throld.THROLD_RegistrationDate TanggalTransaksi, 
			  				  u.User_FullName User, c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi, '1' IDKategori, 'Registrasi' Kategori,
							  'detail-of-registration-document.php' Link
			  FROM TH_RegistrationOfLegalDocument throld, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs 
			  WHERE throld.THROLD_Delete_Time is NULL 
			  AND throld.THROLD_CompanyID=c.Company_ID 
			  AND throld.THROLD_UserID=u.User_ID 
			  AND a.A_ApproverID='$_SESSION[User_ID]' 
			  AND a.A_Status='2' 
			  AND a.A_TransactionCode=throld.THROLD_RegistrationCode 
			  AND throld.THROLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throld.THRGOLAD_ID ID, throld.THRGOLAD_RegistrationCode KodeTransaksi, throld.THRGOLAD_RegistrationDate TanggalTransaksi, 
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '1' IDKategori, 'Registrasi' Kategori,
							  'detail-of-registration-land-acquisition-document.php' Link
			  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throld.THRGOLAD_Delete_Time is NULL 
			  AND throld.THRGOLAD_CompanyID=c.Company_ID 
			  AND throld.THRGOLAD_UserID=u.User_ID 
			  AND a.A_ApproverID='$_SESSION[User_ID]' 
			  AND a.A_Status='2' 
			  AND a.A_TransactionCode=throld.THRGOLAD_RegistrationCode 
			  AND throld.THRGOLAD_RegStatus=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thlold.THLOLD_ID ID, thlold.THLOLD_LoanCode KodeTransaksi, thlold.THLOLD_LoanDate TanggalTransaksi, 
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '2' IDKategori, 'Permintaan' Kategori,
							  'detail-of-loan-document.php' Link
			  FROM TH_LoanOfLegalDocument thlold, M_User u, M_Company c, M_Approval a,M_DocumentRegistrationStatus drs 
			  WHERE thlold.THLOLD_Delete_Time is NULL 
			  AND thlold.THLOLD_CompanyID=c.Company_ID 
			  AND thlold.THLOLD_UserID=u.User_ID 
			  AND a.A_ApproverID='$_SESSION[User_ID]' 
			  AND a.A_Status='2' 
			  AND a.A_TransactionCode=thlold.THLOLD_LoanCode 
			  AND thlold.THLOLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thlolad.THLOLAD_ID ID, thlolad.THLOLAD_LoanCode KodeTransaksi, thlolad.THLOLAD_LoanDate TanggalTransaksi, 
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '2' IDKategori, 'Permintaan' Kategori,
							  'detail-of-loan-land-acquisition-document.php' Link
			  FROM TH_LoanOfLandAcquisitionDocument thlolad, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE thlolad.THLOLAD_Delete_Time is NULL 
			  AND thlolad.THLOLAD_CompanyID=c.Company_ID 
			  AND thlolad.THLOLAD_UserID=u.User_ID 
			  AND a.A_ApproverID='$_SESSION[User_ID]' 
			  AND a.A_Status='2' 
			  AND a.A_TransactionCode=thlolad.THLOLAD_LoanCode 
			  AND thlolad.THLOLAD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_ReleaseCode KodeTransaksi, throld.THROLD_ReleaseDate TanggalTransaksi, 
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '3' IDKategori, 'Pengeluaran' Kategori,
							  'detail-of-release-document.php' Link
			  FROM TH_ReleaseOfLegalDocument throld, M_User u, M_Company c, M_Approval a, TH_LoanOfLegalDocument thlold,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throld.THROLD_Delete_Time is NULL 
			  AND thlold.THLOLD_LoanCode=throld.THROLD_THLOLD_Code
			  AND thlold.THLOLD_CompanyID=c.Company_ID 
			  AND throld.THROLD_UserID=u.User_ID 
			  AND a.A_ApproverID='$_SESSION[User_ID]' 
			  AND a.A_Status='2' 
			  AND a.A_TransactionCode=throld.THROLD_ReleaseCode
			  AND throld.THROLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thrlolad.THRLOLAD_ID ID, thrlolad.THRLOLAD_ReleaseCode KodeTransaksi, thrlolad.THRLOLAD_ReleaseDate TanggalTransaksi, 
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '3' IDKategori, 'Pengeluaran' Kategori,
							  'detail-of-release-land-acquisition-document.php' Link
			  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_User u, M_Company c, M_Approval a, 
			  	   TH_LoanOfLandAcquisitionDocument thlolad, M_DocumentRegistrationStatus drs
			  WHERE thrlolad.THRLOLAD_Delete_Time is NULL 
			  AND thlolad.THLOLAD_LoanCode=thrlolad.THRLOLAD_THLOLAD_Code
			  AND thlolad.THLOLAD_CompanyID=c.Company_ID 
			  AND thrlolad.THRLOLAD_UserID=u.User_ID 
			  AND a.A_ApproverID='$_SESSION[User_ID]' 
			  AND a.A_Status='2' 
			  AND a.A_TransactionCode=thrlolad.THRLOLAD_ReleaseCode
			  AND thrlolad.THRLOLAD_Status=drs.DRS_Name 			  
			  ORDER BY IDKategori, ID
			  
			  ";
	$sql = mysql_query($query);
	$ext= mysql_num_rows($sql);
	
	// Jika memiliki ada dokumen yang menunggu persetujuan
	if ($ext>0) {	
$MainContent .="
		<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th width='20%'>Kode Transaksi</th>
				<th width='15%'>Tanggal Transaksi</th>
				<th width='15%'>User</th>
				<th width='25%'>Perusahaan</th>
				<th width='10%'>Kategori</th>
				<th width='15%'>Status Transaksi</th>
			</tr>
";

		while ($arr = mysql_fetch_array($sql)){
			$TanggalTransaksi=date("j M Y", strtotime($arr['TanggalTransaksi']));
			$detailLink=($arr['Kategori']=="Registrasi")?"act=".$decrp->encrypt('approve')."&id=".$decrp->encrypt($arr[ID])."":"act=approve&id=$arr[ID]";
$MainContent .="
			<tr>
				<td class='center'>
					<a href='$arr[Link]?$detailLink' class='underline'>$arr[KodeTransaksi]</a>
				</td>
				<td class='center'>$TanggalTransaksi</td>
				<td class='center'>$arr[User]</td>
				<td class='center'>$arr[Perusahaan]</td>
				<td class='center'>$arr[Kategori]</td>
				<td class='center'>$arr[StatusTransaksi]</td>
			</tr>
";
 		}
$MainContent .="
		</table>";
	} // Akhir daftar persetujuan
}

/* ---------------------------- */
/* Daftar Transaksi Outstanding */
/* ---------------------------- */
$query = "SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_RegistrationCode KodeTransaksi, throld.THROLD_RegistrationDate TanggalTransaksi, 
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, 
						  '1' IDKategori, 'Registrasi' Kategori, 'detail-of-registration-document.php' Link
		  FROM TH_RegistrationOfLegalDocument throld, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throld.THROLD_Delete_Time is NULL 
		  AND throld.THROLD_CompanyID=c.Company_ID 
		  AND throld.THROLD_UserID='$_SESSION[User_ID]'
		  AND throld.THROLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT throld.THRGOLAD_ID ID, throld.THRGOLAD_RegistrationCode KodeTransaksi, throld.THRGOLAD_RegistrationDate TanggalTransaksi,
						  c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi, 
						  '1' IDKategori, 'Registrasi' Kategori,'detail-of-registration-land-acquisition-document.php' Link
		  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throld.THRGOLAD_Delete_Time is NULL 
		  AND throld.THRGOLAD_CompanyID=c.Company_ID 
		  AND throld.THRGOLAD_UserID='$_SESSION[User_ID]'
		  AND throld.THRGOLAD_RegStatus=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thlold.THLOLD_ID ID, thlold.THLOLD_LoanCode KodeTransaksi, thlold.THLOLD_LoanDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, 
						  '2' IDKategori, 'Permintaan' Kategori,'detail-of-loan-document.php' Link
		  FROM TH_LoanOfLegalDocument thlold, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thlold.THLOLD_Delete_Time is NULL 
		  AND thlold.THLOLD_CompanyID=c.Company_ID 
		  AND thlold.THLOLD_UserID='$_SESSION[User_ID]' 
		  AND thlold.THLOLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thlolad.THLOLAD_ID ID, thlolad.THLOLAD_LoanCode KodeTransaksi, thlolad.THLOLAD_LoanDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, 
						  '2' IDKategori, 'Permintaan' Kategori,'detail-of-loan-land-acquisition-document.php' Link
		  FROM TH_LoanOfLandAcquisitionDocument thlolad, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thlolad.THLOLAD_Delete_Time is NULL 
		  AND thlolad.THLOLAD_CompanyID=c.Company_ID 
		  AND thlolad.THLOLAD_UserID='$_SESSION[User_ID]' 
		  AND thlolad.THLOLAD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_ReleaseCode KodeTransaksi, throld.THROLD_ReleaseDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, 
						  '3' IDKategori, 'Pengeluaran' Kategori,'detail-of-release-document.php' Link			  
		  FROM TH_ReleaseOfLegalDocument throld, TH_LoanOfLegalDocument thlold, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throld.THROLD_Delete_Time is NULL 
		  AND thlold.THLOLD_LoanCode=throld.THROLD_THLOLD_Code
		  AND thlold.THLOLD_CompanyID=c.Company_ID  
		  AND throld.THROLD_UserID='$_SESSION[User_ID]'
		  AND throld.THROLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thrlolad.THRLOLAD_ID ID, thrlolad.THRLOLAD_ReleaseCode KodeTransaksi, thrlolad.THRLOLAD_ReleaseDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, 
						  '3' IDKategori, 'Pengeluaran' Kategori,'detail-of-release-land-acquisition-document.php' Link
		  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, TH_LoanOfLandAcquisitionDocument thlolad, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thrlolad.THRLOLAD_Delete_Time is NULL 
		  AND thlolad.THLOLAD_LoanCode=thrlolad.THRLOLAD_THLOLAD_Code
		  AND thlolad.THLOLAD_CompanyID=c.Company_ID
		  AND thrlolad.THRLOLAD_UserID='$_SESSION[User_ID]' 
		  AND thrlolad.THRLOLAD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  ORDER BY IDKategori, ID";
$sql = mysql_query($query);
$ext= mysql_num_rows($sql);
	
// Jika memiliki ada transaksi outstanding
if ($ext>0) {	
$MainContent .="<div class='home-title'>Transaksi Anda Yang Masih Diproses</div>";
$MainContent .="
	<table width='100%' border='1' class='stripeMe'>
		<tr>
			<th width='30%'>Kode Transaksi</th>
			<th width='15%'>Tanggal Transaksi</th>
			<th width='25%'>Perusahaan</th>
			<th width='15%'>Kategori</th>
			<th width='15%'>Status Transaksi</th>
		</tr>
";

	while ($arr = mysql_fetch_array($sql)){
		$TanggalTransaksi=date("j M Y", strtotime($arr['TanggalTransaksi']));
		$detailLink=($arr['Kategori']=="Registrasi")?"id=".$decrp->encrypt($arr[ID])."":"id=$arr[ID]";
$MainContent .="
		<tr>
			<td class='center'>
				<a href='$arr[Link]?$detailLink' class='underline'>$arr[KodeTransaksi]</a>
			</td>
			<td class='center'>$TanggalTransaksi</td>
			<td class='center'>$arr[Perusahaan]</td>
			<td class='center'>$arr[Kategori]</td>
			<td class='center'>$arr[StatusTransaksi]</td>
		</tr>
";
	}
$MainContent .="
	</table>";
} // Akhir daftar outstanding transaksi


// Cek apakah Administrator atau bukan. 
		// Administrator memiliki hak untuk upload softcopy & edit dokumen.
		$query = "SELECT *
				  FROM M_UserRole
				  WHERE MUR_RoleID='1'
				  AND MUR_UserID='$_SESSION[User_ID]'
				  AND MUR_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$admin = mysql_num_rows($sql);

if ($admin=="1") {
	/* ------------------------------------ */
	/* Daftar User Yang Menunggu Verifikasi */
	/* ------------------------------------ */
	$query="SELECT u.User_ID,u.User_NIK,u.User_FullName,u.User_Name,u.User_Email,d.Division_Name,dp.Department_Name,p.Position_Name, r.Role_Name
			FROM M_User u
			LEFT JOIN M_DivisionDepartmentPosition ddp ON ddp.DDP_UserID=u.User_ID
			LEFT JOIN M_Division d ON d.Division_ID=ddp.DDP_DivID
			LEFT JOIN M_Department dp ON dp.Department_ID=ddp.DDP_DeptID
			LEFT JOIN M_Position p ON p.Position_ID=ddp.DDP_PosID
			LEFT JOIN M_UserRole ur ON ur.MUR_UserID=u.User_ID
			LEFT JOIN M_Role r ON r.Role_ID=ur.MUR_RoleID
			WHERE u.User_Delete_Time is NULL
			AND u.User_NIK IS NULL
			AND u.User_FullName IS NOT NULL
			AND u.User_Name IS NOT NULL
			AND u.User_Email IS NOT NULL
			AND d.Division_Name IS NULL
			AND dp.Department_Name IS NULL
			AND p.Position_Name IS NULL
			AND r.Role_Name IS NULL
			ORDER BY u.User_ID ";
	$sql = mysql_query($query);
	$ext= mysql_num_rows($sql);
		
	// Jika memiliki ada user yang harus diverifikasi
	if ($ext>0) {	
	$MainContent .="<div class='home-title'>User Yang Harus Anda Autorisasi</div>";
	$MainContent .="
		<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th width='10%'>User ID</th>
				<th width='20%'>User Name</th>
				<th width='30%'>Nama Lengkap</th>
				<th width='30%'>Email</th>
				<th width='10%'></th>
			</tr>
	";

		while ($arr = mysql_fetch_array($sql)){
	$MainContent .="
			<tr>
				<td class='center'>$arr[User_ID]</td>
				<td class='center'>$arr[User_Name]</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[User_Email]</td>
				<td class='center'>
					<b>
					<a href='user.php?act=edit&id=$arr[User_ID]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
					<a href='user.php?act=delete&id=$arr[User_ID]'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a>
					</b>
				</td>
			</tr>
	";
		}
	$MainContent .="
		</table>";
	} // Akhir daftar verifikasi user
}

$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>