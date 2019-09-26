<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Pemberi Persetujuan</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optApprover_UserID = document.getElementById('optApprover_UserID').value;
	var optApprover_Step = document.getElementById('optApprover_Step').value;

		if (optApprover_UserID==0) {
			alert("Pemberi Persetujuan Belum Ditentukan!");
			returnValue = false;
		}

		if (optApprover_Step==0) {
			alert("Step Belum Ditentukan!");
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

// Menampilkan list role untuk approver
$query = "SELECT ra.RA_Name, ra.RA_ID
		  FROM M_Role_Approver ra LEFT JOIN M_Approver a
		  ON ra.RA_ID=a.Approver_RoleID
		  WHERE a.Approver_Delete_Time is NULL";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);
$step=3;

if ($num=='0') {
$MainContent ="
	Tambahkan Pengaturan Pemberi Persetujuan Terlebih Dahulu.
";
}
else {
// Menampilkan form untuk pemilihan approver
$MainContent ="
	<form name='add-approver' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pemberi Persetujuan</th>
		<tr>
			<th width='50%'>Peran</th>
			<th width='50%'>User</th>
		</tr>
";
while ($arr = mysql_fetch_array($sql)) {
$MainContent .="
		<tr>
			<td><input name='txtApprover_RoleID[]' type='hidden' value='$arr[RA_ID]'>$arr[RA_Name]</td>
			<td class='center'>
				<select name='optApprover_UserID[]' id='optApprover_UserID'>";

				// Menampilkan user sesuai setting approver yang lama (bila ada)
				$query1 = "SELECT u.User_ID, u.User_FullName
						   FROM M_Approver a, M_User u
						   WHERE a.Approver_Delete_Time is NULL
						   AND a.Approver_UserID=u.User_ID
						   AND a.Approver_RoleID='$arr[RA_ID]'";
				$sql1 = mysql_query($query1);
				$num1 = mysql_num_rows($sql1);
				$arr1=mysql_fetch_array($sql1);

				if ($num1<>'0') {
$MainContent .="
					<option value='$arr1[User_ID]'>$arr1[User_FullName]</option>";
				}
$MainContent .="
					<option value='0'>--- Pilih Pemberi Persetujuan ---</option>";

					 // Menampilkan list user
					 $query1 = "SELECT *
								FROM M_User
								WHERE User_Delete_Time is NULL
								ORDER BY User_FullName";
					 $hasil1 = mysql_query($query1);

					 while ($data = mysql_fetch_array($hasil1)){
$MainContent .="
					<option value='$data[User_ID]'>$data[User_FullName]</option>";
					 }
$MainContent .="
				</select>
			</td>";
/*			<td class='center'>
				<select name='optApprover_Step[]' id='optApprover_Step'>";

				// Menampilkan step sesuai setting yang lama (bila ada)
				$query2 = "SELECT *
						   FROM M_Approver
						   WHERE Approver_Delete_Time is NULL
						   AND Approver_RoleID='$arr[RA_ID]'";
				$sql2 = mysql_query($query2);
				$num2 = mysql_num_rows($sql2);
				$arr2=mysql_fetch_array($sql2);

				if ($num2<>'0') {
$MainContent .="
					<option value='$arr2[Approver_Step]'>$arr2[Approver_Step]</option>";
				}
$MainContent .="
					<option value='0'>--- Tentukan Step ---</option>";

				// Menampilkan list step
				for ($i=0;$i<$num;$i++) {
					$nstep=$step+$i;
$MainContent .="
					<option value='$nstep'>$nstep</option>";
				}
$MainContent .="
				</select>
			</td>*/

$MainContent .= "
		</tr>
";
}
$MainContent .="
		<th colspan=3>
			<input name='addapp' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
			<input name='cancel' type='submit' value='Batal' class='button'/>
		</th>
	</table>
	</form>
";
}

/* ------- */
/* ACTIONS */
/* ------- */

// Apabila menekan button "Cancel"
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";
}

// Apabila menekan button "Simpan"
elseif(isset($_POST[addapp])) {
	$optApprover_UserID=$_POST['optApprover_UserID'];
	$optApprover_Step=$_POST['optApprover_Step'];
	$txtApprover_RoleID=$_POST['txtApprover_RoleID'];
	$jumlah=count($txtApprover_RoleID);

	for ($i=0;$i<$jumlah;$i++) {
		// Menonaktifkan konfigurasi approver lama (bila ada)
		$sql= "UPDATE M_Approver
			   SET Approver_Delete_UserID='$mv_UserID', Approver_Delete_Time=sysdate()
			   WHERE Approver_RoleID='$txtApprover_RoleID[$i]'";
		$mysqli->query($sql);

		// Konfigurasi approver yang baru
		$sql1= "INSERT INTO M_Approver
				VALUES (NULL,'$optApprover_UserID[$i]','$txtApprover_RoleID[$i]','0',
						'$mv_UserID', sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";
		$mysqli->query($sql1);
	}
	echo "<meta http-equiv='refresh' content='0; url=approver.php'>";
}
$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>
