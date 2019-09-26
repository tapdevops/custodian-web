<?PHP session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<title>Custodian System</title>
<head>
<?PHP include ("./config/config_db.php"); 
require_once('include/ldap.class.php'); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;							

	var txtUserName = document.getElementById('txtUserName').value;
	var txtPassword = document.getElementById('txtPassword').value;
		
		if (txtUserName.replace(" ", "") == "") {	
			alert("Username Harus Diisi!");
			returnValue = false;
		}

		if (txtPassword.replace(" ", "") == "") {	
			alert("Password Harus Diisi!");
			returnValue = false;
		}

	return returnValue;
}
</script>
</head>
<?PHP
require_once "./include/template-full.inc";
include_once "./include/global_session.php";
$page=new Template();

if (!$_SESSION['User_ID']) {
	$MainContent ="
	<div id='login-area' align=center><div id='login-area-inside'>
		<form name='login' method='post' action='$PHP_SELF' onsubmit='return validateInput(this);'>
			<table class='no-border' width='400'>
			<tr class='no-border'>
				<td class='no-border' width='250'>
					<div class='login'>Nama Pengguna</div>
					<input name='txtUserName' id='txtUserName' type='text' style='width:80%' /><br><br>
					<div class='login'>Kata Sandi</div>
					<input name='txtPassword' id='txtPassword' type='password' style='width:80%'  /><br>
				</td>
				<td class='no-border' width='100'>
					<input name='login' type='submit' value='' class='button-login'/>
				</td>
			</tr>
			</table>
		</form>
	</div></div>
	";
	if($_GET["act"]=='error'){
		$Warning ="
		<div class='warning'>Anda Harus Login Terlebih Dahulu.</div>
		";
	}
}

else {
	echo "<meta http-equiv='refresh' content='0; url=home.php'>";	
}


if(isset($_POST['login'])) {
	
	$User_Name = $_POST['txtUserName'];
	$User_Password = $_POST['txtPassword'];
	
	//cek ldap login
	$ldap = new ldapClass();
	$authResult = $ldap->ldapAuth($User_Name,$User_Password);
	
	for ($x=0; $x<$authResult["count"]; $x++) {
		$samLdap=$authResult[$x]['samaccountname'][0];
		$giveNameLdap=$authResult[$x]['givenname'][0];
		$emailLdap=$authResult[$x]['mail'][0];
		$namaLdap=$authResult[$x]['cn'][0];
	}  
	  
	//koneksi ldap sukses
	if ($giveNameLdap) {
		//get id user for session
		$query="SELECT * 
				FROM M_User 
				WHERE User_Name='$User_Name'
				AND User_Delete_Time is NULL";
	}else{
	//if ldap failed, get user from local
		$query="SELECT * 
				FROM M_User 
				WHERE User_Name='$User_Name' 
				AND User_Password=md5('$User_Password') 
				AND User_Delete_Time is NULL";
	}
	$sql = mysql_query($query);
	$num = mysql_num_rows($sql);
	
	if($num==1) {
	//cek user role
		$field = mysql_fetch_array($sql);
		$User_ID=$field['User_ID'];
		
		$query="SELECT m.Menu_Link
				FROM M_RoleMenu rm, M_UserRole ur, M_Menu m
				WHERE rm.RM_RoleID=ur.MUR_RoleID
				AND m.Menu_ID=rm.RM_MenuID
				AND ur.MUR_UserID='$User_ID'
				AND rm.RM_Delete_Time IS NULL
				AND ur.MUR_Delete_Time IS NULL
				AND m.Menu_Delete_Time IS NULL";
		$sql=mysql_query($query);
		$statusUser=mysql_num_rows($sql);
		if ($statusUser){
			while ($arr=mysql_fetch_array($sql)) {
				$accessright[]= $arr['Menu_Link'];
			}
		
			session_register("User_ID");
			session_register("Access_Page");
			$_SESSION['User_ID'] = $User_ID;
			$_SESSION['Access_Page']=$accessright;
			$ip=$_SERVER['REMOTE_ADDR'];
			
			//Catat Action ke Logs
			$logs_query="INSERT INTO Logs VALUES ('$User_ID',sysdate(),'$ip','login','login')";
			$mysqli->query($logs_query);
						
			if (!$_SESSION['Referer']) {
				echo "<meta http-equiv='refresh' content='0; url=home.php'>";
			}
			else {
				echo "<meta http-equiv='refresh' content='0; url=$_SESSION[Referer]'>";
			}
		}
		else {
			$Warning ="
			<div class='warning'>Harap Hubungin Tim Custodian, Untuk Autorisasi User Anda.</div>
			";
		}
	}
	
	else if ($giveNameLdap){
	//if ldap success but user id on local table is null
	//insert local user base on ldap
		$Warning ="
			<div class='warning'>Email Anda Belum Terdaftar Dalam Sistem HRIS. Mohon Hubungi Tim HR Untuk Menambahkan Email Anda ke Sistem HRIS.</div>";
	}
	
	else{
	$Warning ="
	<div class='warning'>User Name atau Password Salah.</div>
	";
	}
}

$page->Content($MainContent);
$page->Warnings($Warning);
$page->Show();
?>