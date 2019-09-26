<?PHP session_start(); ?>
<title>Custodian System | Pengaturan Pemberi Persetujuan</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;							

	var txtRA_Name = document.getElementById('txtRA_Name').value;
		
		if (txtRA_Name.replace(" ", "") == "") {
			alert("Nama Role Belum Ditentukan!");
			returnValue = false;
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
if(isset($_GET["act"]))
{
	if($act=='add') {
$ActionContent ="
	<form name='add-role' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Peran Baru</th>
	<tr>
		<td width='30'>Nama Peran</td>
		<td width='70%'><input name='txtRA_Name' id='txtRA_Name' type='text' /></td>
	</tr>
	<th colspan=3>
		<input name='add' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/></th>
	</table>
	</form>
";
	}
	
	elseif($act=='edit')	{
	$RA_ID=$_GET["id"];
	
	$query = "SELECT * 
				FROM M_Role_Approver 
				WHERE RA_ID='$RA_ID' 
				AND RA_Delete_Time is NULL ORDER BY RA_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	
$ActionContent ="
	<form name='edit-role' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Peran</th>
	<tr>
		<td width='30'>ID Peran</td>
		<td width='70%'>
			<input name='txtRA_ID' type='text' value='$field[RA_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Peran</td>
		<td><input name='txtRA_Name' id='txtRA_Name' type='text' value='$field[RA_Name]'/></td>
	</tr>
	<th colspan=3>
		<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}

	elseif($act=='delete')	{
	$RA_ID=$_GET["id"];
	
	$query = "SELECT * 
				FROM M_Role_Approver 
				WHERE RA_ID='$RA_ID' 
				AND RA_Delete_Time is NULL 
				ORDER BY RA_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	
$ActionContent ="
	<form name='delete-role' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Peran Berikut?</th>
	<tr>
		<td width='30'>ID Peran</td>
		<td width='70%'>
			<input name='txtRA_ID' type='text' value='$field[RA_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Peran</td>
		<td><input name='txtRA_Name' type='text' value='$field[RA_Name]' readonly='true' class='readonly'/></td>
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

$query = "SELECT * 
			FROM M_Role_Approver 
			WHERE RA_Delete_Time is NULL 
			ORDER BY RA_ID 
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Peran</th>
		<th>Nama Peran</th>
	</tr>
	<tr>
		<td colspan=2 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num<>NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Peran</th>
		<th>Nama Peran</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[RA_ID]</td>
		<td class='center'>$field[RA_Name]</td>
		<td class='center'>
			<b><a href='$PHP_SELF?act=edit&id=$field[0]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
			<a href='$PHP_SELF?act=delete&id=$field[0]'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a></b>
		</td>
	</tr>
";
 }
}
$MainContent .="
	</table>
";

$query1 = "SELECT * 
			FROM M_Role_Approver 
			WHERE RA_Delete_Time is NULL";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1) $Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++){
         if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
            if (($showPage == 1) && ($p != 2))  $Pager.="..."; 
            if (($showPage != ($jumPage - 1)) && ($p == $jumPage))  $Pager.="...";
            if ($p == $noPage) $Pager.="<b><u>$p</b></u> ";
            else $Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";
            $showPage = $p;          
         }
}
if ($noPage < $jumPage) $Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO M_Role_Approver 
				VALUES (NULL,'$_POST[txtRA_Name]','$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', 
						sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE M_Role_Approver 
				SET RA_Name='$_POST[txtRA_Name]', RA_Update_UserID='$_SESSION[User_ID]', RA_Update_Time=sysdate() 
				WHERE RA_ID='$_POST[txtRA_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_Role_Approver 
				SET RA_Delete_UserID='$_SESSION[User_ID]', RA_Delete_Time=sysdate() 
				WHERE RA_ID='$_POST[txtRA_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
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