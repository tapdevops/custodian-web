<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Menu</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var txtMenu_Name = document.getElementById('txtMenu_Name').value;
	var txtMenu_Link = document.getElementById('txtMenu_Link').value;

		if (txtMenu_Name.replace(" ", "") == "") {
			alert("Nama Menu Belum Ditentukan!");
			returnValue = false;
		}

		if (txtMenu_Link.replace(" ", "") == "") {
			alert("Link Menu Belum Ditentukan!");
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

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	if($act=='add') {
$ActionContent ="
	<form name='add-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Menu Baru</th>
	<tr>
		<td width='30'>Nama Menu</td>
		<td width='70%'><input name='txtMenu_Name' id='txtMenu_Name' type='text' /></td>
	</tr>
	<tr>
		<td>ID Menu Utama</td>
		<td><input name='txtMenu_ParentID' id='txtMenu_ParentID' type='text' /></td>
	</tr>
	<tr>
		<td>Link Menu</td>
		<td><input name='txtMenu_Link' id='txtMenu_Link' type='text' /></td>
	</tr>
	<tr>
		<td>Atribut 1</td>
		<td><input name='txtMenu_Attribute1' id='txtMenu_Attribute1' type='text' /></td>
	</tr>
	<tr>
		<td>Atribut 2</td>
		<td><input name='txtMenu_Attribute2' id='txtMenu_Attribute2' type='text' /></td>
	</tr>
	<tr>
		<td>Urutan</td>
		<td><input name='txtMenu_Seq' id='txtMenu_Seq' type='text' /></td>
	</tr>
	<th colspan=3>
		<input name='add' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}

	elseif($act=='edit') {
	$Menu_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_Menu
				WHERE Menu_ID='$Menu_ID'
				AND Menu_Delete_Time is NULL
				ORDER BY Menu_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='edit-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Menu</th>
	<tr>
		<td width='30'>ID Menu</td>
		<td width='70%'>
			<input name='txtMenu_ID' id='txtMenu_ID' type='text' value='$field[0]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Menu</td>
		<td>
			<input name='txtMenu_Name' id='txtMenu_Name' type='text' value='$field[1]'/>
		</td>
	</tr>
	<tr>
		<td>ID Menu Induk</td>
		<td><input name='txtMenu_ParentID' id='txtMenu_ParentID' type='text' value='$field[2]'/></td>
	</tr>
	<tr>
		<td>Link</td>
		<td><input name='txtMenu_Link' id='txtMenu_Link' type='text' value='$field[3]'/></td>
	</tr>
	<tr>
		<td>Atribut 1</td>
		<td><input name='txtMenu_Attribute1' id='txtMenu_Attribute1' type='text' value='$field[4]'/></td>
	</tr>
	<tr>
		<td>Atribut 2</td>
		<td><input name='txtMenu_Attribute2' id='txtMenu_Attribute2' type='text' value='$field[5]'/></td>
	</tr>
	<tr>
		<td>Urutan</td>
		<td><input name='txtMenu_Seq' id='txtMenu_Seq' type='text' value='$field[6]'/></td>
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
	$Menu_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_Menu
				WHERE Menu_ID='$Menu_ID'
				AND Menu_Delete_Time is NULL
				ORDER BY Menu_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Menu Berikut?</th>
	<tr>
		<td width='30'>ID Menu</td>
		<td width='70%'><input name='txtMenu_ID' type='text' value='$field[0]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Nama Menu</td>
		<td><input name='txtMenu_Name' type='text' value='$field[1]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>ID Menu Utama</td>
		<td><input name='txtMenu_ParentID' type='text' value='$field[2]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Link</td>
		<td><input name='txtMenu_Link' type='text' value='$field[3]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Atribut 1</td>
		<td><input name='txtMenu_Attribute1' type='text' value='$field[4]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Atribut 2</td>
		<td><input name='txtMenu_Attribute2' type='text' value='$field[5]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Urutan</td>
		<td><input name='txtMenu_Seq' type='text' value='$field[6]' readonly='true' class='readonly'/></td>
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
			FROM M_Menu
			WHERE Menu_Delete_Time is NULL
			ORDER BY Menu_ID
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Menu</th>
		<th>Nama Menu</th>
		<th>Menu Utama</th>
		<th>Link</th>
		<th>Atribut 1</th>
		<th>Atribut 2</th>
		<th>Urutan</th>
	</tr>
	<tr>
		<td colspan=7 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num<>NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Menu</th>
		<th>Nama Menu</th>
		<th>Menu Utama</th>
		<th>Link</th>
		<th>Atribut 1</th>
		<th>Atribut 2</th>
		<th>Urutan</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[0]</td>
		<td>$field[1]</td>
		<td class='center'>$field[2]</td>
		<td>$field[3]</td>
		<td>$field[4]</td>
		<td>$field[5]</td>
		<td class='center'>$field[6]</td>
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
			FROM M_Menu
			WHERE Menu_Delete_Time is NULL";
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
if ($noPage < $jumPage) $Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a>";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=menu.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO M_Menu
			VALUES (NULL,'$_POST[txtMenu_Name]','$_POST[txtMenu_ParentID]','$_POST[txtMenu_Link]',
					'$_POST[txtMenu_Attribute1]','$_POST[txtMenu_Attribute2]','$_POST[txtMenu_Seq]','$mv_UserID',
					sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=menu.php'>";
		echo "<script>alert('Berhasil tambah menu')</script>"; //Arief F - 14082018
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE M_Menu
			SET Menu_Name='$_POST[txtMenu_Name]', Menu_ParentID='$_POST[txtMenu_ParentID]',
				Menu_Link='$_POST[txtMenu_Link]', Menu_Attribute1='$_POST[txtMenu_Attribute1]',
				Menu_Attribute2='$_POST[txtMenu_Attribute2]', Menu_Seq='$_POST[txtMenu_Seq]',
				Menu_Update_UserID='$mv_UserID', Menu_Update_Time=sysdate()
			WHERE Menu_ID='$_POST[txtMenu_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=menu.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_Menu
			SET Menu_Delete_UserID='$mv_UserID', Menu_Delete_Time=sysdate()
			WHERE Menu_ID='$_POST[txtMenu_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=menu.php'>";
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
