<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Konfigurasi GrupKategoriTipe Dokumen</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var optDGCT_DocumentGroupID = document.getElementById('optDGCT_DocumentGroupID').value;
	var optDGCT_DocumentCategoryID = document.getElementById('optDGCT_DocumentCategoryID').value;
	var optDGCT_DocumentTypeID = document.getElementById('optDGCT_DocumentTypeID').value;

		if(optDGCT_DocumentGroupID == 0) {
			alert("Grup Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optDGCT_DocumentCategoryID == 0) {
			alert("Kategori Dokumen Belum Dipilih!");
			returnValue = false;
		}

		if(optDGCT_DocumentTypeID == 0) {
			alert("Tipe Dokumen Belum Dipilih!");
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
	<form name='add-dgct' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah GrupKategoriTipe Dokumen Baru</th>
	<tr>
		<td>Pilih Grup Dokumen</td>
		<td>
			<select name='optDGCT_DocumentGroupID' id='optDGCT_DocumentGroupID'>
				<option value='0'>--- Pilih Grup Dokumen ---</option>";

                 $query1 = "SELECT *
				 				FROM M_DocumentGroup
								WHERE DocumentGroup_Delete_Time is NULL
								ORDER BY DocumentGroup_Name";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1)) {
$ActionContent .="
				<option value='$data[DocumentGroup_ID]'>$data[DocumentGroup_Name]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Pilih Kategori Dokumen</td><td>
			<select name='optDGCT_DocumentCategoryID' id='optDGCT_DocumentCategoryID'>
            	<option value='0'>--- Pilih Kategori Dokumen ---</option>";

				 $query1 = "SELECT *
				 				FROM M_DocumentCategory
								WHERE DocumentCategory_Delete_Time is NULL
								ORDER BY DocumentCategory_Name";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1)) {
$ActionContent .="
				<option value='$data[DocumentCategory_ID]'>$data[DocumentCategory_Name]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Pilih Tipe Dokumen</td><td>
			<select name='optDGCT_DocumentTypeID' id='optDGCT_DocumentTypeID'>
            	<option value='0'>--- Pilih Tipe Dokumen ---</option>";

				 $query1 = "SELECT *
				 				FROM M_DocumentType
								WHERE DocumentType_Delete_Time is NULL
								ORDER BY DocumentType_Name";
                 $hasil1 = mysql_query($query1);

                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[DocumentType_ID]'>$data[DocumentType_Name]</option>";
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
	$DGCT_ID=$_GET["id"];

	$query = "SELECT *
				FROM L_DocumentGroupCategoryType
				WHERE DGCT_ID='$DGCT_ID'
				AND DGCT_Delete_Time is NULL
				ORDER BY DGCT_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='edit-dgct' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah GrupKategoriTipe Dokumen</th>
	<tr>
		<td width='30'>ID GrupKategoriTipe Dokumen</td>
		<td width='70%'>
			<input name='txtDGCT_ID' type='text' value='$field[DGCT_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Pilih Group Dokumen</td>
		<td>
			<select name='optDGCT_DocumentGroupID' id='optDGCT_DocumentGroupID'>";

                 $query1 = "SELECT *
				 				FROM M_DocumentGroup
								WHERE DocumentGroup_Delete_Time is NULL
								ORDER BY DocumentGroup_Name";
                 $hasil1 = mysql_query($query1);

                 $query2 = "SELECT g.DocumentGroup_ID as 'ID', g.DocumentGroup_Name as 'Name'
				 				FROM M_DocumentGroup g, L_DocumentGroupCategoryType dgct
								WHERE dgct.DGCT_ID='$DGCT_ID'
								AND g.DocumentGroup_ID=dgct.DGCT_DocumentGroupID";
                 $hasil2 = mysql_query($query2);
				 $arr = mysql_fetch_array($hasil2);

$ActionContent .="
				<option value='$arr[0]'>$arr[1]</option>
          		<option value='0'>---------------------------------</option>
				 ";
                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[DocumentGroup_ID]'>$data[DocumentGroup_Name]</option>";
                 }
           $ActionContent .="
		   	</select>
		</td>
	</tr>
	<tr>
		<td>Pilih Kategori Dokumen</td>
		<td>
			<select name='optDGCT_DocumentCategoryID' id='optDGCT_DocumentCategoryID'>";

                 $query1 = "SELECT *
				 				FROM M_DocumentCategory
								WHERE DocumentCategory_Delete_Time is NULL
								ORDER BY DocumentCategory_Name";
                 $hasil1 = mysql_query($query1);

                 $query2 = "SELECT g.DocumentCategory_ID as 'ID', g.DocumentCategory_Name as 'Name'
				 				FROM M_DocumentCategory g, L_DocumentGroupCategoryType dgct
								WHERE dgct.DGCT_ID='$DGCT_ID'
								AND g.DocumentCategory_ID=dgct.DGCT_DocumentCategoryID";
                 $hasil2 = mysql_query($query2);
				 $arr = mysql_fetch_array($hasil2);

$ActionContent .="
				<option value='$arr[0]'>$arr[1]</option>
          		<option value='0'>---------------------------------</option>
				 ";
                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[DocumentCategory_ID]'>$data[DocumentCategory_Name]</option>";
                 }
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td>Pilih Tipe Dokumen</td>
		<td>
			<select name='optDGCT_DocumentTypeID'> id='optDGCT_DocumentTypeID'>";

                 $query1 = "SELECT *
				 				FROM M_DocumentType
								WHERE DocumentType_Delete_Time is NULL
								ORDER BY DocumentType_Name";
                 $hasil1 = mysql_query($query1);

                 $query2 = "SELECT g.DocumentType_ID as 'ID', g.DocumentType_Name as 'Name'
				 				FROM M_DocumentType g, L_DocumentGroupCategoryType dgct
								WHERE dgct.DGCT_ID='$DGCT_ID'
								AND g.DocumentType_ID=dgct.DGCT_DocumentTypeID";
                 $hasil2 = mysql_query($query2);
				 $arr = mysql_fetch_array($hasil2);

$ActionContent .="
				<option value='$arr[0]'>$arr[1]</option>
          		<option value='0'>---------------------------------</option>
				 ";
                 while ($data = mysql_fetch_array($hasil1))
                 {
$ActionContent .="
				<option value='$data[DocumentType_ID]'>$data[DocumentType_Name]</option>";
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
	</form>
";
	}

	elseif($act=='delete')	{
	$DGCT_ID=$_GET["id"];

	$query = "SELECT *
				FROM L_DocumentGroupCategoryType
				WHERE DGCT_ID='$DGCT_ID'
				AND DGCT_Delete_Time is NULL
				ORDER BY DGCT_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-dgct' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus GrupKategoriTipe Dokumen Berikut?</th>
	<tr>
		<td width='30'>ID GrupKategoriTipe Dokumen</td>
		<td width='70%'>
			<input name='txtDGCT_ID' type='text' value='$field[DGCT_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>";

    $query2 = "SELECT g.DocumentGroup_ID as 'ID', g.DocumentGroup_Name as 'Name'
				FROM M_DocumentGroup g, L_DocumentGroupCategoryType dgct
				WHERE dgct.DGCT_ID='$DGCT_ID'
				AND g.DocumentGroup_ID=dgct.DGCT_DocumentGroupID";
    $hasil2 = mysql_query($query2);
	$arr = mysql_fetch_array($hasil2);

$ActionContent .="
	<tr>
		<td>Grup Dokumen</td>
		<td>
			<input name='txtDGCT_DocumentGroupID' type='text' value='$arr[1]' readonly='true' class='readonly'/>
		</td>
	</tr>";

    $query2 = "SELECT g.DocumentCategory_ID as 'ID', g.DocumentCategory_Name as 'Name'
				FROM M_DocumentCategory g, L_DocumentGroupCategoryType dgct
				WHERE dgct.DGCT_ID='$DGCT_ID'
				AND g.DocumentCategory_ID=dgct.DGCT_DocumentCategoryID";
    $hasil2 = mysql_query($query2);
	$arr = mysql_fetch_array($hasil2);

$ActionContent .="
	<tr>
		<td>Kategori Dokumen</td>
		<td>
			<input name='txtDGCT_DocumentCategoryID' type='text' value='$arr[1]' readonly='true' class='readonly'/>
		</td>
	</tr>	";

    $query2 = "SELECT g.DocumentType_ID as 'ID', g.DocumentType_Name as 'Name'
				FROM M_DocumentType g, L_DocumentGroupCategoryType dgct
				WHERE dgct.DGCT_ID='$DGCT_ID'
				AND g.DocumentType_ID=dgct.DGCT_DocumentTypeID";
    $hasil2 = mysql_query($query2);
	$arr = mysql_fetch_array($hasil2);

$ActionContent .="
	<tr>
		<td>Tipe Dokumen</td>
		<td>
			<input name='txtDGCT_DocumentTypeID' type='text' value='$arr[1]' readonly='true' class='readonly'/>
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
else $noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT *
		  FROM L_DocumentGroupCategoryType dgct, M_DocumentGroup dg, M_DocumentCategory dc, M_DocumentType dt
		  WHERE dgct.DGCT_Delete_Time is NULL
		  AND dgct.DGCT_DocumentGroupID=dg.DocumentGroup_ID
		  AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
		  AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
		  ORDER BY dgct.DGCT_ID
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID</th>
		<th>Grup Dokumen</th>
		<th>Kategori Dokumen</th>
		<th>Tipe Dokumen</th>
	</tr>
	<tr>
		<td colspan=4 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num<>NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID</th>
		<th>Grup Dokumen</th>
		<th>Kategori Dokumen</th>
		<th>Tipe Dokumen</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[DGCT_ID]</td>
		<td class='center'>$field[DocumentGroup_Name]</td>
		<td class='center'>$field[DocumentCategory_Name]</td>
		<td class='center'>$field[DocumentType_Name]</td>
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

$query1= "SELECT *
		  FROM L_DocumentGroupCategoryType dgct, M_DocumentGroup dg, M_DocumentCategory dc, M_DocumentType dt
		  WHERE dgct.DGCT_Delete_Time is NULL
		  AND dgct.DGCT_DocumentGroupID=dg.DocumentGroup_ID
		  AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
		  AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
		  ORDER BY dgct.DGCT_ID";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;
if ($noPage > 1) $Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
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
	echo "<meta http-equiv='refresh' content='0; url=document-groupcategorytype.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO L_DocumentGroupCategoryType
			VALUES (NULL,'$_POST[optDGCT_DocumentGroupID]',
			        '$_POST[optDGCT_DocumentCategoryID]','$_POST[optDGCT_DocumentTypeID]','$mv_UserID',
					sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-groupcategorytype.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE L_DocumentGroupCategoryType
			SET DGCT_DocumentGroupID='$_POST[optDGCT_DocumentGroupID]',
				DGCT_DocumentCategoryID='$_POST[optDGCT_DocumentCategoryID]',
				DGCT_DocumentTypeID='$_POST[optDGCT_DocumentTypeID]',DGCT_Update_UserID='$mv_UserID',
				DGCT_Update_Time=sysdate()
			WHERE DGCT_ID='$_POST[txtDGCT_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-groupcategorytype.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE L_DocumentGroupCategoryType
			SET DGCT_Delete_UserID='$mv_UserID', DGCT_Delete_Time=sysdate()
			WHERE DGCT_ID='$_POST[txtDGCT_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-groupcategorytype.php'>";
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
