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

	var txtMK_Name = document.getElementById('txtMK_Name').value;

		if (txtMK_Name.replace(" ", "") == "") {
			alert("Nama Tipe Kendaraan Belum Ditentukan!");
			returnValue = false;
		}

	return returnValue;
}
</script>
</head>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage)) ){
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
	<th colspan=3>Tambah Kendaraan Baru</th>
	<tr>
		<td width='30'>Nama Tipe Kendaraan</td>
		<td width='70%'><input name='txtMK_Name' id='txtMK_Name' type='text' /></td>
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
	$MK_ID=$_GET["id"];

	$query = "SELECT *
				FROM db_master.M_MerkKendaraan
				WHERE MK_ID='$MK_ID'
				AND MK_DeleteTime is NULL
				ORDER BY MK_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='edit-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Menu</th>
	<tr>
		<td width='30'>ID Tipe Kendaraan</td>
		<td width='70%'>
			<input name='txtMK_ID' id='txtMK_ID' type='text' value='$field[0]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Tipe Kendaraan</td>
		<td>
			<input name='txtMK_Name' id='txtMK_Name' type='text' value='$field[1]'/>
		</td>
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
	$MK_ID=$_GET["id"];

	$query = "SELECT *
				FROM db_master.M_MerkKendaraan
				WHERE MK_ID='$MK_ID'
				AND MK_DeleteTime is NULL
				ORDER BY MK_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Menu Berikut?</th>
	<tr>
		<td width='30'>ID Tipe Kendaraan</td>
		<td width='70%'><input name='txtMK_ID' type='text' value='$field[0]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Nama Tipe Kendaraan</td>
		<td><input name='txtMK_Name' type='text' value='$field[1]' readonly='true' class='readonly'/></td>
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
			FROM db_master.M_MerkKendaraan
			WHERE MK_DeleteTime is NULL
			ORDER BY MK_ID
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Tipe Kendaraan</th>
		<th>Nama Tipe Kendaraan</th>
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
		<th>ID Tipe Kendaraan</th>
		<th>Nama Tipe Kendaraan</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[0]</td>
		<td>$field[1]</td>
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
			FROM db_master.M_MerkKendaraan
			WHERE MK_DeleteTime is NULL";
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
	echo "<meta http-equiv='refresh' content='0; url=kendaraan.php'>";
}

elseif(isset($_POST['add'])) {
	$sql= "INSERT INTO db_master.M_MerkKendaraan
			VALUES (NULL,'$_POST[txtMK_Name]','1','$mv_UserID',
					sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=kendaraan.php'>";
		echo "<script>alert('Berhasil tambah tipe kendaraan')</script>"; //Arief F - 14082018
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE db_master.M_MerkKendaraan
			SET MK_Name='$_POST[txtMK_Name]',
				MK_UpdateUser='$mv_UserID', MK_UpdateTime=sysdate()
			WHERE MK_ID='$_POST[txtMK_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=kendaraan.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE db_master.M_MerkKendaraan
			SET  MK_Status='0', MK_DeleteUser='$mv_UserID', MK_DeleteTime=sysdate()
			WHERE MK_ID='$_POST[txtMK_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=kendaraan.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penghapusan Data Gagal.</div>";
	}
}

include ("./config/config_db.php");


$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
