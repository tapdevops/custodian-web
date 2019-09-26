<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Grup Dokumen</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var txtDocumentGroup_Name = document.getElementById('txtDocumentGroup_Name').value;
	var txtDocumentGroup_Code = document.getElementById('txtDocumentGroup_Code').value;
	var jCharDocCode = txtDocumentGroup_Code.length;

		if (txtDocumentGroup_Name.replace(" ", "") == "") {
			alert("Nama Grup Dokumen Belum Ditentukan!");
			returnValue = false;
		}

		if (txtDocumentGroup_Code.replace(" ", "") == "") {
			alert("Kode Grup Dokumen Belum Ditentukan!");
			returnValue = false;
		}
		else {
			if (jCharDocCode>5){
				alert("Max Karakter Untuk Kode Grup Dokumen adalah 5!");
				returnValue = false;
			}
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
	<form name='add-docgroup' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Grup Dokumen Baru</th>
	<tr>
		<td width='30'>Nama Grup Dokumen</td>
		<td width='70%'>
			<input name='txtDocumentGroup_Name' id='txtDocumentGroup_Name' type='text' />
		</td>
	</tr>
	<tr>
		<td width='30'>Kode Grup Dokumen</td>
		<td width='70%'>
			<input name='txtDocumentGroup_Code' id='txtDocumentGroup_Code' type='text' />
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
	$DocumentGroup_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_DocumentGroup
				WHERE DocumentGroup_ID='$DocumentGroup_ID'
				AND DocumentGroup_Delete_Time is NULL
				ORDER BY DocumentGroup_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='edit-docgroup' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Grup Dokumen</th>
	<tr>
		<td width='30'>ID Grup Dokumen</td>
		<td width='70%'>
			<input name='txtDocumentGroup_ID' type='text' value='$field[DocumentGroup_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Grup Dokumen</td>
		<td>
			<input name='txtDocumentGroup_Name' id='txtDocumentGroup_Name' type='text' value='$field[DocumentGroup_Name]'/>
		</td>
	</tr>
	<tr>
		<td width='30'>Kode Grup Dokumen</td>
		<td width='70%'>
			<input name='txtDocumentGroup_Code' id='txtDocumentGroup_Code' type='text' value='$field[DocumentGroup_Code]'/>
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
	$DocumentGroup_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_DocumentGroup
				WHERE DocumentGroup_ID='$DocumentGroup_ID'
				AND DocumentGroup_Delete_Time is NULL
				ORDER BY DocumentGroup_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-docgroup' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Grup Dokumen Berikut?</th>
	<tr>
		<td width='30'>ID Grup Dokumen</td>
		<td width='70%'>
			<input name='txtDocumentGroup_ID' type='text' value='$field[DocumentGroup_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Grup Dokumen</td>
		<td>
			<input name='txtDocumentGroup_Name' type='text' value='$field[DocumentGroup_Name]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td width='30'>Kode Grup Dokumen</td>
		<td width='70%'>
			<input name='txtDocumentGroup_Code' id='txtDocumentGroup_Code' type='text' value='$field[DocumentGroup_Code]' readonly='true' class='readonly'/>
		</td>
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
			FROM M_DocumentGroup
			WHERE DocumentGroup_Delete_Time is NULL
			ORDER BY DocumentGroup_ID
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Grup Dokumen</th>
		<th>Nama Grup Dokumen</th>
		<th>Kode Grup Dokumen</th>
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
		<th>ID Grup Dokumen</th>
		<th>Nama Grup Dokumen</th>
		<th>Kode Grup Dokumen</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[DocumentGroup_ID]</td>
		<td class='center'>$field[DocumentGroup_Name]</td>
		<td class='center'>$field[DocumentGroup_Code]</td>
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
			FROM M_DocumentGroup
			WHERE DocumentGroup_Delete_Time is NULL";
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
	echo "<meta http-equiv='refresh' content='0; url=document-group.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO M_DocumentGroup
				VALUES (NULL,'$_POST[txtDocumentGroup_Name]','$_POST[txtDocumentGroup_Code]','$mv_UserID',
						sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-group.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE M_DocumentGroup
				SET DocumentGroup_Name='$_POST[txtDocumentGroup_Name]', DocumentGroup_Update_UserID='$mv_UserID',
					DocumentGroup_Update_Time=sysdate(), DocumentGroup_Code='$_POST[txtDocumentGroup_Code]'
				WHERE DocumentGroup_ID='$_POST[txtDocumentGroup_ID]'";

	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-group.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_DocumentGroup
				SET DocumentGroup_Delete_UserID='$mv_UserID', DocumentGroup_Delete_Time=sysdate()
				WHERE DocumentGroup_ID='$_POST[txtDocumentGroup_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-group.php'>";
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