<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Status Transaksi Dokumen</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var txtDRS_Name = document.getElementById('txtDRS_Name').value;

		if (txtDRS_Name.replace(" ", "") == "") {
			alert("Nama Status Transaksi Dokumen Belum Ditentukan!");
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
	<form name='add-docregstat' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Status Transaksi Dokumen</th>
	<tr>
		<td width='30'>Status Transaksi Dokumen</td>
		<td width='70%'><input name='txtDRS_Name' id='txtDRS_Name' type='text' /></td>
	</tr>
	<tr>
		<td width='30'>Deskripsi</td>
		<td width='70%'><input name='txtDRS_Description' id='txtDRS_Description' type='text' /></td>
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
	$DRS_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_DocumentRegistrationStatus
				WHERE DRS_ID='$DRS_ID'
				AND DRS_Delete_Time is NULL
				ORDER BY DRS_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='edit-docregstat' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Status Transaksi Dokumen</th>
	<tr>
		<td width='30'>ID Status Transaksi Dokumen</td>
		<td width='70%'>
			<input name='txtDRS_ID' type='text' value='$field[0]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Status Transaksi Dokumen</td>
		<td>
			<input name='txtDRS_Name' id='txtDRS_Name' type='text' value='$field[1]'/>
		</td>
	</tr>
	<tr>
		<td width='30'>Deskripsi</td>
		<td width='70%'><input name='txtDRS_Description' id='txtDRS_Description' type='text' value='$field[2]'/></td>
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
	$DRS_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_DocumentRegistrationStatus
				WHERE DRS_ID='$DRS_ID'
				AND DRS_Delete_Time is NULL
				ORDER BY DRS_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-docregstat' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Status Transaksi Dokumen Berikut?</th>
	<tr>
		<td width='30'>ID Status Transaksi Dokumen</td>
		<td width='70%'>
			<input name='txtDRS_ID' type='text' value='$field[0]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Status Transaksi Dokumen</td>
		<td><input name='txtDRS_Name' type='text' value='$field[1]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td width='30'>Deskripsi</td>
		<td width='70%'><input name='txtDRS_Description' id='txtDRS_Description' type='text' value='$field[2]' readonly='true' class='readonly'/></td>
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
			FROM M_DocumentRegistrationStatus
			WHERE DRS_Delete_Time is NULL
			ORDER BY DRS_ID
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Status Transaksi Dokumen</th>
		<th>Status Transaksi Dokumen</th>
		<th>Deskripsi</th>
	</tr>
	<tr>
		<td colspan=3 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num<>NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID</th>
		<th>Status Transaksi</th>
		<th>Deskripsi</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[0]</td>
		<td class='center'>$field[1]</td>
		<td class='center'>$field[2]</td>
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
			FROM M_DocumentRegistrationStatus
			WHERE DRS_Delete_Time is NULL";
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
	echo "<meta http-equiv='refresh' content='0; url=document-transaction-status.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO M_DocumentRegistrationStatus
			VALUES (NULL,'$_POST[txtDRS_Name]','$_POST[txtDRS_Description]','$mv_UserID', sysdate(),
					'$mv_UserID', sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-transaction-status.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE M_DocumentRegistrationStatus
			SET DRS_Name='$_POST[txtDRS_Name]', DRS_Description='$_POST[txtDRS_Description]',
				DRS_Update_UserID='$mv_UserID', DRS_Update_Time=sysdate()
			WHERE DRS_ID='$_POST[txtDRS_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-transaction-status.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_DocumentRegistrationStatus
			SET DRS_Delete_UserID='$mv_UserID', DRS_Delete_Time=sysdate()
			WHERE DRS_ID='$_POST[txtDRS_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-transaction-status.php'>";
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
