<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Pengaturan Format Lokasi</title>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$query = "SELECT DISTINCT DL_Chest, DL_CellChar, DL_CellNo, DL_Cabin, DL_CompanyID, DL_DocGroupID
		  FROM L_DocumentLocation
		  WHERE DL_Delete_Time is NULL
		  ORDER BY DL_ID ";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>Chest</th>
		<th>Char</th>
		<th>Cell</th>
		<th>Cabin</th>
		<th>Perusahaan</th>
		<th>Grup Dokumen</th>
	</tr>
	<tr>
		<td colspan=10 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num<>NULL){
$MainContent ="
	<form name=maploc action='$PHP_SELF' method='post'>
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>Chest</th>
		<th>Char</th>
		<th>Cell</th>
		<th>Cabin</th>
		<th>Perusahaan</th>
		<th>Grup Dokumen</th>
	</tr>
";

	while ($field = mysql_fetch_array($sql)){
		if ($field[DL_CompanyID]<>NULL)
			$style="style='background:#FF9;'";
		else
			$style="";
		if ($field[DL_DocGroupID]<>NULL)
			$style1="style='background:#CCC;'";
		else
			$style1="";
	$MainContent .="
	<tr>
		<td class='center'><input type=hidden name='DL_Chest[]' value='$field[DL_Chest]'>$field[DL_Chest]</td>
		<td class='center'><input type=hidden name='DL_CellChar[]' value='$field[DL_CellChar]'>$field[DL_CellChar]</td>
		<td class='center'><input type=hidden name='DL_CellNo[]' value='$field[DL_CellNo]'>$field[DL_CellNo]</td>
		<td class='center'><input type=hidden name='DL_Cabin[]' value='$field[DL_Cabin]'>$field[DL_Cabin]</td>
		<td class='center'>
			<select name='optCompany[]' $style>
				<option value=''>--- Pilih Perusahaan ---</option>
	";
	$co_query="SELECT Company_ID, UPPER(Company_Name) AS Company_Name
			   FROM M_Company
			   WHERE Company_Delete_Time IS NULL
			   ORDER BY Company_Name";
	$co_sql=mysql_query($co_query);
	while ($co_arr=mysql_fetch_array($co_sql)){
		if ($field[DL_CompanyID]==$co_arr[Company_ID]){
	$MainContent .="<option value='$co_arr[Company_ID]' selected='selected'>$co_arr[Company_Name]</option>";
		}
		else{
	$MainContent .="<option value='$co_arr[Company_ID]'>$co_arr[Company_Name]</option>";
		}

	}
	$MainContent .="
			</select>
		</td>
		<td class='center'>
			<select name='docGroup[]' $style1>
				<option value=''>--- Pilih Grup Dokumen ---</option>";
	if ($field[DL_DocGroupID]=='non_grl')
	$MainContent .="<option value='non_grl' selected='selected'>Legal/Lisensi</option>";
	else
	$MainContent .="<option value='non_grl'>Legal/Lisensi</option>";
	if ($field[DL_DocGroupID]=='grl')
	$MainContent .="<option value='grl' selected='selected'>Pembebasan Lahan</option>";
	else
	$MainContent .="<option value='grl'>Pembebasan Lahan</option>";
	if ($field[DL_DocGroupID]=='kea')
	$MainContent .="<option value='kea' selected='selected'>Kepemilikan Aset</option>";
	else
	$MainContent .="<option value='kea'>Kepemilikan Aset</option>";
	if ($field[DL_DocGroupID]=='dll')
	$MainContent .="<option value='dll' selected='selected'>Dokumen Lainnya (Legal)</option>";
	else
	$MainContent .="<option value='dll'>Dokumen Lainnya (Legal)</option>";
	if ($field[DL_DocGroupID]=='dlnl')
	$MainContent .="<option value='dlnl' selected='selected'>Dokumen Lainnya (Di Luar Legal)</option>";
	else
	$MainContent .="<option value='dlnl'>Dokumen Lainnya (Di Luar Legal)</option>";
	$MainContent .="
			</select>
		</td>
	</tr>
	";
	}
}
$MainContent .="
	<tr>
		<th colspan=10>
			<input name='save' type='submit' value='Simpan' class='button' />
		</th>
	</tr>
	</table>
	</form>
";

if(isset($_POST[save])) {
	$DL_Chest=$_POST['DL_Chest'];
	$DL_CellChar=$_POST['DL_CellChar'];
	$DL_CellNo=$_POST['DL_CellNo'];
	$DL_Cabin=$_POST['DL_Cabin'];
	$Company_ID=$_POST['optCompany'];
	$DocGroupID=$_POST['docGroup'];
	$jRow=count($Company_ID);

	for ($i=0 ; $i<=$jRow ;$i++) {
		$sql="UPDATE L_DocumentLocation
			  SET DL_CompanyID='$Company_ID[$i]', DL_DocGroupID='$DocGroupID[$i]',
				  DL_Update_UserID='$mv_UserID', DL_Update_Time=sysdate()
			  WHERE DL_Chest='$DL_Chest[$i]'
			  AND DL_CellChar='$DL_CellChar[$i]'
			  AND DL_CellNo='$DL_CellNo[$i]'
			  AND DL_Cabin='$DL_Cabin[$i]'";
		$mysqli->query($sql);
	}
	echo "<meta http-equiv='refresh' content='0; url=mapping-location-company.php'>";
}

$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>
