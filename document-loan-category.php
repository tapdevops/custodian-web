<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Kategori Permintaan Dokumen</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var txtLoanCategory_Name = document.getElementById('txtLoanCategory_Name').value;

		if (txtLoanCategory_Name.replace(" ", "") == "") {
			alert("Nama Kategori Permintaan Dokumen Belum Ditentukan!");
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
	<form name='add-loan-category' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Kategori Permintaan Dokumen Baru</th>
	<tr>
		<td width='30'>Nama Kategori Permintaan Dokumen </td>
		<td width='70%'><input name='txtLoanCategory_Name' id='txtLoanCategory_Name' type='text' /></td>
	</tr>
	<tr>
		<td>Deskripsi Kategori Permintaan Dokumen </td>
		<td>
			<textarea name='txtLoanCategory_Description' id='txtLoanCategory_Description' cols='50' rows='2'></textarea>
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

	elseif($act=='edit') {
	$LoanCategory_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_LoanCategory
				WHERE LoanCategory_ID='$LoanCategory_ID'
				AND LoanCategory_Delete_Time is NULL
				ORDER BY LoanCategory_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='edit-loan-category' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Kategori Permintaan Dokumen </th>
	<tr>
		<td width='30'>ID Kategori Permintaan Dokumen </td>
		<td width='70%'>
			<input name='txtLoanCategory_ID'  type='text' value='$field[0]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Kategori Permintaan Dokumen </td>
		<td><input name='txtLoanCategory_Name' id='txtLoanCategory_Name' type='text' value='$field[1]'/></td>
	</tr>
	<tr>
		<td>Deskripsi Kategori Permintaan Dokumen </td>
		<td>
			<textarea name='txtLoanCategory_Description' id='txtLoanCategory_Description' cols='50' rows='2'>$field[2]</textarea>
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
	$LoanCategory_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_LoanCategory
				WHERE LoanCategory_ID='$LoanCategory_ID'
				AND LoanCategory_Delete_Time is NULL
				ORDER BY LoanCategory_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-loan-category' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Kategori Permintaan Dokumen Berikut?</th>
	<tr>
		<td width='30'>ID Kategori Permintaan Dokumen</td>
		<td width='70%'><input name='txtLoanCategory_ID' type='text' value='$field[0]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Nama Kategori Permintaan Dokumen</td>
		<td><input name='txtLoanCategory_Name' type='text' value='$field[1]' readonly='true' class='readonly'/></td>
	</tr>
	<tr>
		<td>Deskripsi Kategori Permintaan Dokumen </td>
		<td>
			<textarea name='txtLoanCategory_Description' id='txtLoanCategory_Description' cols='50' rows='2'>$field[2]</textarea>
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
if(isset($_GET['page'])) {
    $noPage = $_GET['page'];
}
else
	$noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT *
			FROM M_LoanCategory
			WHERE LoanCategory_Delete_Time is NULL
			ORDER BY LoanCategory_ID
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID</th>
		<th>Kategori Permintaan Dokumen</th>
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
	 	<th width='5%'>ID</th>
		<th width='25%'>Kategori Permintaan Dokumen</th>
		<th width='60%'>Deskripsi</th>
		<th width='10%'></th>
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
			FROM M_LoanCategory
			WHERE LoanCategory_Delete_Time is NULL";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1)
	$Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
    	if (($showPage == 1) && ($p != 2))
			$Pager.="...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			$Pager.="...";
        if ($p == $noPage)
			$Pager.="<b><u>$p</b></u> ";
        else
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";
        $showPage = $p;
	}
}
if ($noPage < $jumPage)
	$Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=document-loan-category.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO M_LoanCategory
			VALUES (NULL,'$_POST[txtLoanCategory_Name]','$_POST[txtLoanCategory_Description]','$mv_UserID',
			        sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-loan-category.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE M_LoanCategory
				SET LoanCategory_Name='$_POST[txtLoanCategory_Name]', LoanCategory_Update_UserID='$mv_UserID', LoanCategory_Update_Time=sysdate(),
					LoanCategory_Description='$_POST[txtLoanCategory_Description]'
				WHERE LoanCategory_ID='$_POST[txtLoanCategory_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-loan-category.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_LoanCategory
				SET LoanCategory_Delete_UserID='$mv_UserID', LoanCategory_Delete_Time=sysdate()
				WHERE LoanCategory_ID='$_POST[txtLoanCategory_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-loan-category.php'>";
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
