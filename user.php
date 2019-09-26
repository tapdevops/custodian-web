<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 4 Mei 2012																						=
= Update Terakhir	: 25 Sep 2012																						=
= Revisi			:																									=
= 		25/05/2012	: Penambahan Reset Password (OK)																	=
= 		25/09/2012	: Perubahan Struktur DB M_User																		=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Daftar Pengguna</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var txtUser_FullName = document.getElementById('txtUser_FullName').value;
	var txtUser_Name = document.getElementById('txtUser_Name').value;
	var txtUser_Email = document.getElementById('txtUser_Email').value;
	var optDDP_DivID = document.getElementById('optDDP_DivID').value;
	var optDDP_DeptID = document.getElementById('optDDP_DeptID').value;
	var optDDP_PosID = document.getElementById('optDDP_PosID').value;
	var optUser_Role = document.getElementById('optUser_Role').value;

		if (txtUser_FullName.replace(" ", "") == "") {
			alert("Nama Karyawan Belum Ditentukan!");
			returnValue = false;
		}
		if (txtUser_Name.replace(" ", "") == "") {
			alert("Username untuk Login Belum Ditentukan!");
			returnValue = false;
		}
		if (txtUser_Email.replace(" ", "") == "") {
			alert("Email Belum Ditentukan!");
			returnValue = false;
		}
		if(optDDP_DivID == "0") {
			alert("Divisi Belum Dipilih!");
			returnValue = false;
		}
		if (optDDP_DeptID == "0") {
			alert("Departement Belum Dipilih!");
			returnValue = false;
		}
		if (optDDP_PosID == "0") {
			alert("Posisi Belum Dipilih!");
			returnValue = false;
		}
		if (optUser_Role == "0") {
			alert("Peran Belum Dipilih!");
			returnValue = false;
		}
	return returnValue;
}

// Menampilkan Departemen Sesuai Divisi Yang Dipilih
function showDept() {
 	<?php
 	$query = "SELECT * FROM M_Division";
 	$result = mysql_query($query);
 	while ($data = mysql_fetch_array($result)) {
		$Division_ID = $data['Division_ID'];
   		echo "if (document.getElementById('optDDP_DivID').value == \"".$Division_ID."\") {";
   		$query2 = "SELECT * FROM M_Department WHERE Department_DivID='$Division_ID' order by Department_Name";
   		$result2 = mysql_query($query2);
		$num = mysql_num_rows ($result2);
		if ($num > 0) {
			$opt = "document.getElementById('optDDP_DeptID').innerHTML = \"";
   			$opt .= "<option value='0'>--- Pilih Departemen ---</option>";
			while ($data2 = mysql_fetch_array($result2)) {
       			$opt .= "<option value='".$data2['Department_ID']."'>".$data2['Department_Name']."</option>";
			}
		}
		else {
			$opt = "document.getElementById('optDDP_DeptID').innerHTML = \"";
			$opt .= "<option value=''>--- Tidak Ada Departemen ---</option>";
   		}

   	$opt .= "\"";
   	echo $opt;
   	echo "}\n";
 	}?>
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
	if($act=='add') {
$ActionContent ="
	<form name='add-user' id='user' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Pengguna Baru</th>
	<tr>
		<td>Nama Lengkap Pengguna</td>
		<td><input name='txtUser_FullName' id='txtUser_FullName' type='text' style='width:350px;'/></td>
	</tr>
	<tr>
		<td>Email Pengguna</td>
		<td>
			<input name='txtUser_Email' id='txtUser_Email' type='text' value='' style='width:350px;'/> @tap-agri.com
			<!--<select name='optUser_DomainEmail' id='optUser_DomainEmail' required>
				<option value=''>Pilih Domain Email</option>
				<option value='@tap-agri.com' selected>@tap-agri.com</option>
				<option value='@tap-agri.co.id'>@tap-agri.co.id</option>
			</select>-->
		</td>
	</tr>
	<tr>
		<td>Login</td>
		<td><input name='txtUser_Name' id='txtUser_Name' type='text' style='width:350px;'/></td>
	</tr>
	<tr>
		<td>Kata Sandi Pengguna</td>
		<td>
			<input name='txtUser_Password' id='txtUser_Password' type='password' value='tap123' style='width:350px;'/>
		</td>
	</tr>
	<tr>
		<td>Divisi</td>
		<td>
			<select name='optDDP_DivID' id='optDDP_DivID' onChange='showDept()' style='width:350px;'>
				<option value='0'>--- Pilih Divisi ---</option>";

                 $query1 = "SELECT *
							FROM M_Division
							WHERE Division_Delete_Time is NULL
							AND Division_ID<>0
							ORDER BY Division_Name";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[Division_ID]'>$data[Division_Name]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Departemen</td>
		<td>
			<select name='optDDP_DeptID' id='optDDP_DeptID' style='width:350px;'>
				<option value='0'> - Pilih Divisi Terlebih Dahulu- </option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Level</td>
		<td>
			<select name='optDDP_PosID' id='optDDP_PosID'>
				<option value='0'>--- Pilih Posisi ---</option>";

                 $query1 = "SELECT *
				 			FROM M_Position
							WHERE Position_Delete_Time is NULL
							ORDER BY Position_ID"; //Arief F - 21082018
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[Position_ID]'>$data[Position_Name]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Atasan 1</td>
		<td>
			<select name='optUser_SPV1' id='optUser_SPV1' style='width:350px;'>
				<option value='0'>--- Pilih Atasan ---</option>";

                 $query1 = "SELECT *
				 			FROM M_User
							WHERE User_Delete_Time is NULL
							ORDER BY User_FullName";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[User_ID]'>$data[User_FullName]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Atasan Tambahan</td>
		<td>
			<select name='optUser_SPV2' id='optUser_SPV2' style='width:350px;'>
				<option value='0'>--- Pilih Atasan Tambahan ---</option>";

                 $query1 = "SELECT *
				 			FROM M_User
							WHERE User_Delete_Time is NULL
							ORDER BY User_FullName";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[User_ID]'>$data[User_FullName]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Peran Pengguna Dalam Custodian</td>
		<td>
			<select name='optUser_Role' id='optUser_Role'>
				<option value='0'>--- Pilih Peran ---</option>";

                 $query1 = "SELECT *
				 				FROM M_Role
								WHERE Role_Delete_Time is NULL
								ORDER BY Role_ID DESC";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[Role_ID]'>$data[Role_Name]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<th colspan=3>
		<input name='add' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}

	elseif($act=='edit')	{
	$User_ID=$_GET["id"];

	$query = "	SELECT u.User_ID,u.User_FullName,u.User_Name,u.User_Password,u.User_Email,u.User_Local,
					   division.Division_ID,division.Division_Name,dept.Department_ID,dept.Department_Name,
					   pos.Position_ID,pos.Position_Name,role.Role_ID,role.Role_Name,u.User_SPV1,u.User_SPV2,
					   IFNULL((SELECT spv1.User_FullName FROM M_User spv1 WHERE spv1.User_ID=u.User_SPV1),'[Belum Ada Atasan]') NamaAtasan1,
					   IFNULL((SELECT spv2.User_FullName FROM M_User spv2 WHERE spv2.User_ID=u.User_SPV2),'[Tidak Ada Atasan Tambahan]') NamaAtasan2
				FROM M_User u
				LEFT JOIN M_DivisionDepartmentPosition ddp
					ON ddp.DDP_UserID=u.User_ID
				LEFT JOIN M_Department dept
					ON ddp.DDP_DeptID=dept.Department_ID
				LEFT JOIN M_Division division
					ON ddp.DDP_DivID=division.Division_ID
				LEFT JOIN M_Position pos
					ON ddp.DDP_PosID=pos.Position_ID
				LEFT JOIN M_UserRole ur
					ON ur.MUR_UserID=u.User_ID
				LEFT JOIN M_Role role
					ON ur.MUR_RoleID=role.Role_ID
				WHERE u.User_ID='$User_ID'
				AND u.User_Delete_Time is NULL
				ORDER BY u.User_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	if($field['User_Local']){
		$inputEmail=$field['User_Email'];
		$username_email=explode("@",$inputEmail);
		$email=$username_email[0];

		$ActionContent ="
			<form name='edit-user' method='post' action='$PHP_SELF'>
			<table width='100%' id='mytable' class='stripeMe'>
			<th colspan=3>Ubah Pengguna</th>
			<input name='User_Local' id='User_Local' type='hidden' value='1' readonly='true' class='readonly'/>
			<tr>
				<td width='30'>ID Pengguna</td>
				<td width='70%'>
					<input name='txtUser_ID' id='txtUser_ID' type='text' value='$field[User_ID]' readonly='true' class='readonly'/>
				</td>
			</tr>
			<tr>
				<td>Nama Lengkap Pengguna</td>
				<td><input name='txtUser_FullName' id='txtUser_FullName' type='text' value='$field[User_FullName]' style='width:350px;'/></td>
			</tr>
			<tr>
				<td>Login</td>
				<td><input name='txtUser_Name' id='txtUser_Name' type='text' value='$field[User_Name]' style='width:350px;'></td>
			</tr>
			<tr>
				<td>Kata Sandi Pengguna</td>
				<td>
					<input name='oldPass' type='hidden' value='$field[User_Password]' />
					<input name='txtUser_Password' id='txtUser_Password' type='password' value='$field[User_Password]' style='width:350px;'/>
				</td>
			</tr>
			<tr>
				<td>Email Pengguna</td>
				<td><input name='txtUser_Email' id='txtUser_Email' type='text' value='$email' style='width:350px;'/> @tap-agri.com</td>
			</tr>
			<tr>
				<td>Divisi</td>
				<td>
					<select name='optDDP_DivID' id='optDDP_DivID' onChange='showDept()' style='width:350px;'>";

						 $query1 = "SELECT *
									FROM M_Division
									WHERE Division_Delete_Time is NULL
									AND Division_ID<>0
									ORDER BY Division_Name";
						 $hasil1 = mysql_query($query1);

						 $ActionContent .="
						 <option value='0'>--- Pilih Divisi ---</option>";
						 while ($data = mysql_fetch_array($hasil1)){
							$selected=($data['Division_ID']==$field['Division_ID'])?"selected=selected":"";
							$ActionContent .="
								<option value='$data[Division_ID]' $selected>$data[Division_Name]</option>";
						 }
		$ActionContent .="
					</select>
				</td>
			</tr>
			<tr>
				<td>Departemen</td>
				<td>
					<select name='optDDP_DeptID' id='optDDP_DeptID' style='width:350px;'>
						<option value='$field[Department_ID]'>$field[Department_Name]</option>
						<option value='0'>---------------------------------</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Level</td>
				<td>
					<select name='optDDP_PosID' id='optDDP_PosID'>";

						 $query1 = "SELECT *
									FROM M_Position
									WHERE Position_Delete_Time is NULL
									ORDER BY Position_ID";
						 $hasil1 = mysql_query($query1);
					$ActionContent .="
						 <option value='0'>--- Pilih Posisi ---</option>";
						 while ($data = mysql_fetch_array($hasil1)){
							$selected=($data['Position_ID']==$field['Position_ID'])?"selected=selected":"";
		$ActionContent .="
						<option value='$data[Position_ID]' $selected>$data[Position_Name]</option>";
						 }
		$ActionContent .="
					</select>
				</td>
			</tr>
			<tr>
				<td>Atasan 1</td>
				<td>
					<select name='optUser_SPV1' id='optUser_SPV1' style='width:350px;'>
						<option value='0'>--- Pilih Atasan ---</option>";

						 $query1 = "SELECT *
									FROM M_User
									WHERE User_Delete_Time is NULL
									ORDER BY User_FullName";
						 $hasil1 = mysql_query($query1);

						 while ($data = mysql_fetch_array($hasil1)){
							$selected=($data['User_ID']==$field['User_SPV1'])?"selected=selected":"";
		$ActionContent .="
						<option value='$data[User_ID]' $selected>$data[User_FullName]</option>";
						 }
		$ActionContent .="
					</select>
				</td>
			</tr>
			<tr>
				<td>Atasan Tambahan</td>
				<td>
					<select name='optUser_SPV2' id='optUser_SPV2' style='width:350px;'>
						<option value='0'>--- Pilih Atasan Tambahan ---</option>";

						 $query1 = "SELECT *
									FROM M_User
									WHERE User_Delete_Time is NULL
									ORDER BY User_FullName";
						 $hasil1 = mysql_query($query1);

						 while ($data = mysql_fetch_array($hasil1)){
							$selected=($data['User_ID']==$field['User_SPV2'])?"selected=selected":"";
		$ActionContent .="
						<option value='$data[User_ID]' $selected>$data[User_FullName]</option>";
						 }
		$ActionContent .="
					</select>
				</td>
			</tr>
			<tr>
				<td>Peran Pengguna Dalam Custodian</td>
				<td>
					<select name='optUser_Role' id='optUser_Role'>";

						 $query1 = "SELECT *
									FROM M_Role
									WHERE Role_Delete_Time is NULL
									ORDER BY Role_ID DESC";
						 $hasil1 = mysql_query($query1);
						$ActionContent .="
							<option value='0'>--- Pilih Peran ---</option>";
						 while ($data = mysql_fetch_array($hasil1)){
							$selected=($data['Role_ID']==$field['Role_ID'])?"selected=selected":"";
		$ActionContent .="
						<option value='$data[Role_ID]' $selected>$data[Role_Name]</option>";
						 }
		$ActionContent .="
					</select>
				</td>
			</tr>
			<th colspan=3>
				<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
				<input name='cancel' type='submit' value='Batal' class='button'/>
			</th>
			</table>
			</form>";
	}else{
		$ActionContent ="
		<form name='edit-user' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Detail Pengguna</th>
		<input name='User_Local' id='User_Local' type='hidden' value='0' readonly='true' class='readonly'/>
		<tr>
			<td width='30'>ID Pengguna</td>
			<td width='70%'>
				<input name='txtUser_ID' type='text' value='$field[User_ID]' readonly='true' class='readonly' style='width:350px'/>
			</td>
		</tr>
		<tr>
			<td>Nama Lengkap Pengguna</td>
			<td>
				<input name='txtUser_FullName' type='text' value='$field[User_FullName]' readonly='true' class='readonly' style='width:350px'/>
			</td>
		</tr>
		<tr>
			<td>Email Pengguna</td>
			<td>
				<input name='txtUser_Email' type='text' value='$field[User_Email]' readonly='true' class='readonly' style='width:350px'/>
			</td>
		</tr>
		<tr>
			<td>Login</td>
			<td><input name='txtUser_Name' id='txtUser_Name' type='text' value='$field[User_Name]' readonly='true' class='readonly' style='width:350px'></td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtDDP_DivID' type='text' value='$field[Division_Name]' readonly='true' class='readonly' style='width:350px'/>
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtDDP_DeptID' type='text' value='$field[Department_Name]' readonly='true' class='readonly' style='width:350px'/>
			</td>
		</tr>
		<tr>
			<td>Level</td>
			<td>
				<input name='txtDDP_PosID' type='text' value='$field[Position_Name]' readonly='true' class='readonly' style='width:350px'/>
			</td>
		</tr>
		<tr>
			<td>Atasan 1</td>
			<td>
				<input type='hidden' name='optUser_SPV1' id='optUser_SPV1' value='$field[User_SPV1]'>
				<input name='txtUser_SPV1' type='text' value='$field[NamaAtasan1]' readonly='true' class='readonly' style='width:350px'/>
			</td>
		</tr>
		<tr>
		<td>Atasan Tambahan</td>
			<td>
			<select name='optUser_SPV2' id='optUser_SPV2' style='width:350px;'>
				<option value='0'>--- Pilih Atasan Tambahan ---</option>";

				 $query1 = "SELECT *
							FROM M_User
							WHERE User_Delete_Time is NULL
							ORDER BY User_FullName";
				 $hasil1 = mysql_query($query1);

				 while ($data = mysql_fetch_array($hasil1)){
					$selected=($data['User_ID']==$field['User_SPV2'])?"selected=selected":"";
					$ActionContent .="
					<option value='$data[User_ID]' $selected>$data[User_FullName]</option>";
				 }
		$ActionContent .="
			</select>
			</td>
		</tr>
		<tr>
			<td>Peran Pengguna Dalam Custodian</td>
			<td>
				<select name='optUser_Role' id='optUser_Role'>";

					 $query1 = "SELECT *
								FROM M_Role
								WHERE Role_Delete_Time is NULL
								ORDER BY Role_ID DESC";
					 $hasil1 = mysql_query($query1);
					 $ActionContent .="
					 <option value='0'>--- Pilih Peran ---</option>";
					 while ($data = mysql_fetch_array($hasil1)){
						$selected=($data['Role_ID']==$field['Role_ID'])?"selected=selected":"";
						$ActionContent .="
						<option value='$data[Role_ID]' $selected>$data[Role_Name]</option>";
					 }
		$ActionContent .="
				</select>
			</td>
		</tr>
		<th colspan=3>
			<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
			<input name='cancel' type='submit' value='Batal' class='button'/>
		</th>
		</table>
		</form>";
	}
	}

	elseif($act=='delete')	{
	$User_ID=$_GET["id"];

	$query = "	SELECT u.User_ID,u.User_FullName,u.User_Name,u.User_Password,u.User_Email,u.User_Local,
					   division.Division_ID,division.Division_Name,dept.Department_ID,dept.Department_Name,
					   pos.Position_ID,pos.Position_Name,role.Role_ID,role.Role_Name,u.User_SPV1,u.User_SPV2,
					   IFNULL((SELECT spv1.User_FullName FROM M_User spv1 WHERE spv1.User_ID=u.User_SPV1),'[Belum Ada Atasan]') NamaAtasan1,
					   IFNULL((SELECT spv2.User_FullName FROM M_User spv2 WHERE spv2.User_ID=u.User_SPV2),'[Tidak Ada Atasan Tambahan]') NamaAtasan2
				FROM M_User u
				LEFT JOIN M_DivisionDepartmentPosition ddp
					ON ddp.DDP_UserID=u.User_ID
				LEFT JOIN M_Department dept
					ON ddp.DDP_DeptID=dept.Department_ID
				LEFT JOIN M_Division division
					ON ddp.DDP_DivID=division.Division_ID
				LEFT JOIN M_Position pos
					ON ddp.DDP_PosID=pos.Position_ID
				LEFT JOIN M_UserRole ur
					ON ur.MUR_UserID=u.User_ID
				LEFT JOIN M_Role role
					ON ur.MUR_RoleID=role.Role_ID
				WHERE u.User_ID='$User_ID'
				AND u.User_Delete_Time is NULL
				ORDER BY u.User_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-user' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Pengguna Berikut?</th>
	<tr>
		<td width='30'>ID Pengguna</td>
		<td width='70%'>
			<input name='txtUser_ID' type='text' value='$field[User_ID]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Nama Lengkap Pengguna</td>
		<td>
			<input name='txtUser_FullName' type='text' value='$field[User_FullName]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Email Pengguna</td>
		<td>
			<input name='txtUser_Email' type='text' value='$field[User_Email]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Login</td>
		<td><input name='txtUser_Name' id='txtUser_Name' type='text' value='$field[User_Name]' readonly='true' class='readonly' style='width:350px'></td>
	</tr>
	<tr>
		<td>Divisi</td>
		<td>
			<input name='txtDDP_DivID' type='text' value='$field[Division_Name]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Departemen</td>
		<td>
			<input name='txtDDP_DeptID' type='text' value='$field[Department_Name]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Level</td>
		<td>
			<input name='txtDDP_PosID' type='text' value='$field[Position_Name]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Atasan 1</td>
		<td>
			<input name='txtUser_SPV' type='text' value='$field[NamaAtasan1]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Atasan Tambahan</td>
		<td>
			<input name='txtUser_SPV' type='text' value='$field[NamaAtasan2]' readonly='true' class='readonly' style='width:350px'/>
		</td>
	</tr>
	<tr>
		<td>Peran Pengguna Dalam Custodian</td>
		<td><input name='txtUser_Role' type='text' value='$field[Role_Name]' readonly='true' class='readonly' style='width:350px'/></td>
	</tr>
	<th colspan=3>
		<input name='delete' type='submit' value='Ya' class='button'/>
		<input name='cancel' type='submit' value='Tidak' class='button'/>
	</th>
	</table>
	</form>
";
	}
}

$dataPerPage = 20;
if(isset($_GET['page'])){
    $noPage = $_GET['page'];
}
else $noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query =   "SELECT u.User_ID,u.User_FullName,u.User_Name,u.User_Email,d.Division_Name,dp.Department_Name,p.Position_Name, r.Role_Name
			FROM M_User u
			LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			LEFT JOIN M_Division d
				ON d.Division_ID=ddp.DDP_DivID
			LEFT JOIN M_Department dp
				ON dp.Department_ID=ddp.DDP_DeptID
			LEFT JOIN M_Position p
				ON p.Position_ID=ddp.DDP_PosID
			LEFT JOIN M_UserRole ur
				ON ur.MUR_UserID=u.User_ID
			LEFT JOIN M_Role r
				ON r.Role_ID=ur.MUR_RoleID
			WHERE u.User_Delete_Time is NULL
			ORDER BY u.User_ID ";
$limit = "LIMIT $offset, $dataPerPage";
$allquery=$query.$limit;
$sql = mysql_query($allquery);
$num = mysql_num_rows($sql);

$MainContent ="
	<form name='search' method='post' action='user.php'>
		<div style='text-align:left; padding:10px 5px; margin-bottom :5px;'>
			<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/> <input type='submit' class='button' value='CARI' style='width:50px'>
		</div>
	</form>

	<form name='reset-pass' method='post' action='$PHP_SELF'>
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th width='10%'>ID Pengguna</th>
		<th width='20%'>Nama Pengguna</th>
		<th width='25%'>Nama Lengkap Pengguna</th>
		<th width='30%'>Email Pengguna</th>
		<th width='10%'>Peran Pengguna</th>
		<th width='5%'></th>
	</tr>";

//SEARCH
if($_POST['txtSearch']<>'') {
	$search=$_POST['txtSearch'];
	$query="SELECT u.User_ID,u.User_FullName,u.User_Name,u.User_Email,d.Division_Name,dp.Department_Name,p.Position_Name, r.Role_Name
			FROM M_User u
			LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			LEFT JOIN M_Division d
				ON d.Division_ID=ddp.DDP_DivID
			LEFT JOIN M_Department dp
				ON dp.Department_ID=ddp.DDP_DeptID
			LEFT JOIN M_Position p
				ON p.Position_ID=ddp.DDP_PosID
			LEFT JOIN M_UserRole ur
				ON ur.MUR_UserID=u.User_ID
			LEFT JOIN M_Role r
				ON r.Role_ID=ur.MUR_RoleID
			WHERE u.User_Delete_Time is NULL
			AND (
				u.User_ID LIKE '%$search%'
				OR u.User_FullName LIKE '%$search%'
				OR u.User_Name LIKE '%$search%'
				OR u.User_Email LIKE '%$search%'
				OR d.Division_Name LIKE '%$search%'
				OR dp.Department_Name LIKE '%$search%'
				OR p.Position_Name LIKE '%$search%'
				OR r.Role_Name LIKE '%$search%'
			)
			ORDER BY u.User_ID ";
	$limit="LIMIT $offset, $dataPerPage";
	$allquery=$query.$limit;
	$sql = mysql_query($allquery);
	$numSearch=mysql_num_rows($sql);
}

if(!$num){
	$MainContent .="
		<tr>
			<td colspan=7 align='center'>Belum Ada Data</td>
		</tr>";
}else if(($_POST['txtSearch']<>'')&&(!$numSearch)){
	$MainContent .="
		<tr>
			<td colspan=7 align='center'>Data Tidak Ditemukan</td>
		</tr>";
}else{
	$no=1;
	while ($field = mysql_fetch_array($sql)){
		$MainContent .="
		<tr>
			<td class='center'><input name='txtUser_ID' type='hidden' value='$field[User_ID]'/>$field[User_ID]</td>
			<td class='center'>$field[User_Name]</td>
			<td class='center'><input id='txtUser_FullName' type='hidden' value='$field[User_FullName]'/>$field[User_FullName]</td>
			<td class='center'>$field[User_Email]</td>
			<td class='center'>$field[Role_Name]</td>
			<td class='center'>
				<b>
				<a href='$PHP_SELF?act=edit&id=$field[0]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
				<a href='$PHP_SELF?act=delete&id=$field[0]'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a>
				</b>
			</td>
		</tr>";
		$no=$no+1;
	}
}
$MainContent .="
	<input name='jRow' id='jRow' type='hidden' value='$no'/>
	</table>
	</form>";

$sql1 = mysql_query($query);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;
if ($noPage > 1) $Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++){
         if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)){
            if (($showPage == 1) && ($p != 2))  $Pager.="...";
            if (($showPage != ($jumPage - 1)) && ($p == $jumPage))  $Pager.="...";
            if ($p == $noPage) $Pager.="<b><u>$p</b></u> ";
            else $Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";
            $showPage = $p;
         }
}
if ($noPage < $jumPage) $Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a>";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=user.php'>";
}

elseif(isset($_POST[add])) {
	$inputEmail=$_POST["txtUser_Email"];
	$username_email=explode("@",$inputEmail);
	$username_email=$username_email[0];
	$email=$username_email."@tap-agri.com";

	//GENERATE USER ID
	$query="select generateUserID() as UserID";
	$sql=mysql_query($query);
	$obj=mysql_fetch_object($sql);
	$User_ID=$obj->UserID;

	$sql= "	INSERT INTO M_User (User_ID,User_Name,User_FullName,User_Email,User_Password,User_Insert_UserID,User_Insert_Time,
			User_Update_UserID,User_Update_Time,User_Delete_UserID,User_Delete_Time,User_SPV1,User_Local,User_SPV2)
			VALUES ('$User_ID','$_POST[txtUser_Name]','$_POST[txtUser_FullName]','$email',md5('$_POST[txtUser_Password]'),
			'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL,'$_POST[optUser_SPV1]','1','$_POST[optUser_SPV2]')";

	if(mysql_query($sql)){
		$sql1="INSERT INTO M_UserRole (MUR_ID,MUR_UserID,MUR_RoleID,MUR_Insert_UserID,
				MUR_Insert_Time,MUR_Update_UserID,MUR_Update_Time,MUR_Delete_UserID,MUR_Delete_Time)
				VALUES (NULL, '$User_ID','$_POST[optUser_Role]','$mv_UserID', sysdate(),'$mv_UserID',
						sysdate(),NULL,NULL)";

		$sql3="INSERT INTO M_DivisionDepartmentPosition (DDP_ID,DDP_UserID,DDP_DivID,DDP_DeptID,DDP_PosID,
			   DDP_Insert_UserID,DDP_Insert_Time,DDP_Update_UserID,DDP_Update_Time,DDP_Delete_UserID,DDP_Delete_Time)
				VALUES (NULL, '$User_ID','$_POST[optDDP_DivID]','$_POST[optDDP_DeptID]',
						'$_POST[optDDP_PosID]','$mv_UserID', sysdate(),'$mv_UserID',
						sysdate(),NULL,NULL)";

		if((mysql_query($sql1)) && (mysql_query($sql3)) ){
				echo "<meta http-equiv='refresh' content='0; url=user.php'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$password=($_POST['oldPass']<>$_POST['txtUser_Password'])?"md5('$_POST[txtUser_Password]')":"'$_POST[txtUser_Password]'";
	if($_POST['User_Local']){
		$query="SELECT * FROM M_DivisionDepartmentPosition WHERE DDP_UserID='$_POST[txtUser_ID]'";
		$sql=mysql_query($query);
		$cRow=mysql_num_rows($sql);
		if($cRow){
			$sql2="UPDATE M_DivisionDepartmentPosition
				   SET DDP_DeptID='$_POST[optDDP_DeptID]', DDP_DivID='$_POST[optDDP_DivID]',
					   DDP_PosID='$_POST[optDDP_PosID]',DDP_Update_UserID='$mv_UserID', DDP_Update_Time=sysdate()
				   WHERE DDP_UserID='$_POST[txtUser_ID]'";
		}else{
			$sql2="INSERT INTO M_DivisionDepartmentPosition (DDP_ID,DDP_UserID,DDP_DivID,DDP_DeptID,DDP_PosID,
				   DDP_Insert_UserID,DDP_Insert_Time,DDP_Update_UserID,DDP_Update_Time,DDP_Delete_UserID,DDP_Delete_Time)
				   VALUES (NULL,'$_POST[txtUser_ID]','$_POST[optDDP_DivID]','$_POST[optDDP_DeptID]','$_POST[optDDP_PosID]',
				   '$mv_UserID',sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";
		}
		$hasil=mysql_query($sql2);
	}

		$sql= "UPDATE M_User
			   SET User_Name='$_POST[txtUser_Name]',User_FullName='$_POST[txtUser_FullName]', User_Email='$_POST[txtUser_Email]',
				   User_Password=$password, User_Update_UserID='$mv_UserID', User_Update_Time=sysdate(),
				   User_SPV1='$_POST[optUser_SPV1]',User_SPV2='$_POST[optUser_SPV2]'
			   WHERE User_ID='$_POST[txtUser_ID]'";
		$hasil=mysql_query($sql);

		$query="SELECT * FROM M_UserRole WHERE MUR_UserID='$_POST[txtUser_ID]'";
		$sql=mysql_query($query);
		$cRow=mysql_num_rows($sql);
		if($cRow){
			$sql1="UPDATE M_UserRole
				   SET MUR_RoleID='$_POST[optUser_Role]', MUR_Update_UserID='$mv_UserID', MUR_Update_Time=sysdate()
				   WHERE MUR_UserID='$_POST[txtUser_ID]'";
		}else{
			$sql1="	INSERT INTO M_UserRole (MUR_ID,MUR_UserID,MUR_RoleID,MUR_Insert_UserID,
					MUR_Insert_Time,MUR_Update_UserID,MUR_Update_Time,MUR_Delete_UserID,MUR_Delete_Time)
					VALUES (NULL,'$_POST[txtUser_ID]','$_POST[optUser_Role]','$mv_UserID',sysdate(),
					'$mv_UserID',sysdate(),NULL,NULL)";
		}
		$hasil=mysql_query($sql1);

		if($hasil){
			echo "<meta http-equiv='refresh' content='0; url=user.php'>";
		}
		else {
			$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
		}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_User
			SET User_Delete_UserID='$mv_UserID', User_Delete_Time=sysdate()
			WHERE User_ID='$_POST[txtUser_ID]'";

	$sql1="UPDATE M_UserRole
			SET MUR_Delete_UserID='$mv_UserID', MUR_Delete_Time=sysdate()
			WHERE MUR_UserID='$_POST[txtUser_ID]'";

	$sql2="UPDATE M_DivisionDepartmentPosition
			SET DDP_Delete_UserID='$mv_UserID', DDP_Delete_Time=sysdate()
			WHERE DDP_UserID='$_POST[txtUser_ID]'";

	if((mysql_query($sql)) && (mysql_query($sql1)) && (mysql_query($sql2)) ){
		echo "<meta http-equiv='refresh' content='0; url=user.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penghapusan Data Gagal.</div>";
	}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
