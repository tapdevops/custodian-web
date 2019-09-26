<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Penambahan Chest</title>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

if(isset($_COOKIE['berhasil_generate']) && $_COOKIE['berhasil_generate'] == 1){
	$ActionContent = "<script>alert('Berhasil Generate Kode');</script>";
	$_COOKIE['berhasil_generate'] = null;
}

if(isset($_COOKIE['gagal_generate']) && $_COOKIE['gagal_generate'] == 1){
	$ActionContent = "<script>alert('Gagal Generate Kode');</script>";
	$_COOKIE['gagal_generate'] = null;
}

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	if($act=='add') {
$ActionContent ="
	<form name='add-doclocstruc' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Chest Baru</th>
	<tr>
		<td width='30'>Jumlah Cell (Character)</td>
		<td width='70%'><input name='txtDLS_TotalCellChar' type='text' value='3' /></td>
	</tr>
	<tr>
		<td>Jumlah Cell (Number)</td>
		<td><input name='txtDLS_TotalCellNo' type='text' value='6' /></td>
	</tr>
	<tr>
		<td>Jumlah Cabin</td>
		<td><input name='txtDLS_TotalCabin' type='text' value='2' /></td>
	</tr>
	<tr>
		<td>Jumlah Folder</td>
		<td><input name='txtDLS_TotalFolder' type='text' value='75' /></td>
	</tr>
	<th colspan=3>
		<input name='add' type='submit' value='Simpan' class='button' />
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}elseif($act=='edit') { //Arief F - 14082018
	$DLS_ID=$_GET["id"];
	$query = "SELECT *
				FROM M_DocumentLocationStructure
				WHERE DLS_ID='$DLS_ID'
				AND DLS_Delete_Time is NULL
				ORDER BY DLS_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
		<form name='add-doclocstruc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Ubah Chest</th>
		<tr>
			<td width='30'>ID Chest</td>
			<td width='70%'>
				<input name='txtDLS_ID' id='txtDLS_ID' type='text' value='$field[0]' readonly='true' class='readonly'/>
			</td>
		</tr>
		<tr>
			<td width='30'>Jumlah Cell (Character)</td>
			<td width='70%'><input name='txtDLS_TotalCellChar' type='text' value='$field[1]' /></td>
		</tr>
		<tr>
			<td>Jumlah Cell (Number)</td>
			<td><input name='txtDLS_TotalCellNo' type='text' value='$field[2]' /></td>
		</tr>
		<tr>
			<td>Jumlah Cabin</td>
			<td><input name='txtDLS_TotalCabin' type='text' value='$field[3]' /></td>
		</tr>
		<tr>
			<td>Jumlah Folder</td>
			<td><input name='txtDLS_TotalFolder' type='text' value='$field[4]' /></td>
		</tr>
		<th colspan=3>
			<input name='edit' type='submit' value='Simpan' class='button' />
			<input name='cancel' type='submit' value='Batal' class='button'/>
		</th>
		</table>
		</form>
	";
	}

	if($act=='generate') {
	$DLS_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_DocumentLocationStructure
				WHERE DLS_ID='$DLS_ID'
				AND DLS_Delete_Time is NULL
				ORDER BY DLS_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='add-doclocstruc' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Yakin Ingin Generate Kode Untuk ID Chest Berikut Ini?<br>Hasil Generate Ini Tidak Dapat Dibatalkan atau Dihapus.</th>
	<tr>
		<td width='30'>ID Chest</td>
		<td width='70%'>
			<input name='txtDLS_ID' type='text' value='$field[DLS_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<th colspan=3>
		<input name='generate' type='submit' value='Simpan' class='button' />
		<input name='cancel' type='submit' value='Batal' class='button'/>
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
			FROM M_DocumentLocationStructure
			WHERE DLS_Delete_Time is NULL
			ORDER BY DLS_ID
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Chest</th>
		<th>Jumlah Cell (Character)</th>
		<th>Jumlah Cell (Number)</th>
		<th>Jumlah Cabin</th>
		<th>Jumlah Folder</th>
	</tr>
	<tr>
		<td colspan=5 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num<>NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Chest</th>
		<th>Jumlah Cell (Character)</th>
		<th>Jumlah Cell (Number)</th>
		<th>Jumlah Cabin</th>
		<th>Jumlah Folder</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[DLS_ID]</td>
		<td class='center'>$field[DLS_TotalCellChar]</td>
		<td class='center'>$field[DLS_TotalCellNo]</td>
		<td class='center'>$field[DLS_TotalCabin]</td>
		<td class='center'>$field[DLS_TotalFolder]</td>
		<td class='center'>
			<b>
				<a href='$PHP_SELF?act=edit&id=$field[0]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>"; //Arief F - 14082018
	if ($field[DLS_GenerateStatus]==0){
$MainContent .="
			<a href='$PHP_SELF?act=generate&id=$field[0]'><img title='Generate Location' src='./images/icon-generate.png' width='20'></a>"; //Arief F - 14082018
	}
	$MainContent .="
			</b>
		</td>
	</tr>
"; //Arief F - 14082018
 }
}
$MainContent .="
	</table>
";

$query1 = "SELECT *
			FROM M_DocumentLocationStructure
			WHERE DLS_Delete_Time is NULL";
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
	echo "<meta http-equiv='refresh' content='0; url=document-location-structure.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO M_DocumentLocationStructure
			VALUES (NULL,'$_POST[txtDLS_TotalCellChar]','$_POST[txtDLS_TotalCellNo]',
					'$_POST[txtDLS_TotalCabin]','$_POST[txtDLS_TotalFolder]','NULL','$mv_UserID',
					sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=document-location-structure.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) { //Arief F - 14082018
	$sql= "UPDATE M_DocumentLocationStructure
			SET DLS_TotalCellChar='$_POST[txtDLS_TotalCellChar]', DLS_TotalCellNo='$_POST[txtDLS_TotalCellNo]',
				DLS_TotalCabin='$_POST[txtDLS_TotalCabin]', DLS_TotalFolder='$_POST[txtDLS_TotalFolder]',
				DLS_GenerateStatus='0', DLS_Update_UserID='$mv_UserID', DLS_Update_Time=sysdate()
			WHERE DLS_ID='$_POST[txtDLS_ID]'";  //Arief F - 14082018
	if($mysqli->query($sql)) {  //Arief F - 14082018
		echo "<meta http-equiv='refresh' content='0; url=document-location-structure.php'>";  //Arief F - 14082018
	}  //Arief F - 14082018
	else {  //Arief F - 14082018
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";  //Arief F - 14082018
	} //Arief F - 14082018
}  //Arief F - 14082018

elseif(isset($_POST[generate])) {
	$gchest=$_POST["txtDLS_ID"];

	// $query = "SELECT MAX(DL_Chest)
	// 			FROM L_DocumentLocation
	// 			WHERE DL_Delete_Time is NULL
	// 			ORDER BY DL_ID ";
	// $sql = mysql_query($query);
	// $field = mysql_fetch_array($sql);
	//
	// if($field[0]==NULL)
	// 	$lchest=0;
	// else
	// 	$lchest=$field[0];
	// $new_lchest=$lchest+1;

	// if ($gchest==$new_lchest){
		$query = "SELECT *
					FROM M_DocumentLocationStructure
					WHERE DLS_ID='$gchest'
					AND DLS_Delete_Time is NULL
					ORDER BY DLS_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	$numCellChar=$field[DLS_TotalCellChar];
	$numCellNo=$field[DLS_TotalCellNo];
	$numCabin=$field[DLS_TotalCabin];
	$numFolder=$field[DLS_TotalFolder];

	$count_process = 0; //Arief F - 21082018

	for($ncc=1; $ncc<=$numCellChar; $ncc++){
		for ($ncn=1; $ncn<=$numCellNo; $ncn++){
			for ($nc=1; $nc<=$numCabin ;$nc++){
				for ($nf=1; $nf<=$numFolder ;$nf++){

					$new_gchest=str_pad($gchest, 2, "0", STR_PAD_LEFT);
					$new_chr=chr($ncc+64);
					$new_ncn=str_pad($ncn, 2, "0", STR_PAD_LEFT);
					$new_nc=str_pad($nc, 2, "0", STR_PAD_LEFT);
					$new_nf=str_pad($nf, 3, "0", STR_PAD_LEFT);

					$code="$new_gchest"."$new_chr"."$new_ncn"."$new_nc"."$new_nf"."F";
					$name="Chest $new_gchest Cell $new_chr$new_ncn Cabin $new_nc Folder $new_nf";

					// Cek DL_Code ada atau tidak di table L_DocumentLocation
					$cek = "SELECT DL_ID FROM L_DocumentLocation WHERE DL_Code='$code'"; //Arief F - 21082018
					$sql_cek = mysql_query($cek); //Arief F - 21082018
					$num = mysql_num_rows($sql_cek); //Arief F - 21082018
					if($num == 0){ //Arief F - 21082018
						$CompanyID = NULL; //Arief F - 21082018
		                $DocGroupID = NULL; //Arief F - 21082018
		                $get_other_component = "SELECT DL_Chest, DL_CellChar, DL_CellNo, DL_Cabin, DL_CompanyID, DL_DocGroupID
		            		  FROM L_DocumentLocation
		            		  WHERE DL_DocGroupID is NOT NULL AND DL_Delete_Time is NULL AND
		            			DL_Chest='$new_gchest' AND DL_CellChar='$new_chr' AND DL_CellNo='$new_ncn' AND DL_Cabin='$new_nc'
		            		  ORDER BY DL_ID LIMIT 0,1"; //Arief F - 21082018
		                $run_sql = mysql_query($get_other_component); //Arief F - 21082018
		                $cek_available_component = mysql_num_rows($run_sql); //Arief F - 21082018
		                if($cek_available_component > 0){ //Arief F - 21082018
							// echo "ada<br>";
		                    $d_cac = mysql_fetch_array($run_sql); //Arief F - 21082018
		                    $CompanyID = $d_cac['DL_CompanyID']; //Arief F - 21082018
		                    $DocGroupID = $d_cac['DL_DocGroupID']; //Arief F - 21082018

							$sql= "INSERT INTO L_DocumentLocation
						 			VALUES (NULL, '$code', '$name', '$new_gchest', '$new_chr', '$new_ncn', '$new_nc',
						 					'$new_nf', '$CompanyID', '$DocGroupID', '1', '$mv_UserID', sysdate(), '$mv_UserID',
											sysdate(), NULL, NULL)"; //Arief F - 21082018
		                }else{
							$sql= "INSERT INTO L_DocumentLocation
						 			VALUES (NULL, '$code', '$name', '$new_gchest', '$new_chr', '$new_ncn', '$new_nc',
						 					'$new_nf', NULL, NULL, '1', '$mv_UserID', sysdate(), '$mv_UserID',
											sysdate(), NULL, NULL)";
						}
						if($mysqli->query($sql)) {
							$count_process++; //Arief F - 21082018
						}
					} //Arief F - 21082018
					//End of Cek DL_Code ada atau tidak di table L_DocumentLocation
				}
			}
		}
	}

	if($count_process > 0){ //Arief F - 21082018
		$sql1= "UPDATE M_DocumentLocationStructure
					SET DLS_GenerateStatus='1', DLS_Update_UserID='$mv_UserID', DLS_Update_Time=sysdate()
					WHERE DLS_ID='$gchest'";
		$mysqli->query($sql1);
		$_COOKIE['berhasil_generate'] = 1; //Arief F - 21082018
		echo "<meta http-equiv='refresh' content='0; url=document-location-structure.php'>";
	}else{ //Arief F - 29082018
		$_COOKIE['gagal_generate'] = 1; //Arief F - 21082018
		// echo "<meta http-equiv='refresh' content='0; url=document-location-structure.php'>";
	} //Arief F - 21082018
	// }
	// else
	// 	$ActionContent .="<div class='warning'>Generate ID Chest Harus Urut.</div>";
}
$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
