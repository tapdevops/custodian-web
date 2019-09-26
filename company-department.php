<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Update Terakhir	: 4 Mei 2012																						=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Daftar Departemen</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
</head>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$dataPerPage = 20;
if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

// Menampilkan daftar departemen
$query = "SELECT d.Division_ID, d.Division_Name, dp.Department_ID, dp.Department_Name
		  FROM M_Department dp, M_Division d
		  WHERE dp.Department_Delete_Time is NULL
		  AND d.Division_ID=dp.Department_DivID
		  ORDER BY d.Division_Name, dp.Department_Name
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
		<tr>
			<th width='20%'>Kode Departemen</th>
			<th width='40%'>Nama Departemen</th>
			<th width='40%'>Nama Divisi</th>
		</tr>";

// Menampilkan daftar departemen
if ($num==NULL){
	$MainContent .="
		<tr>
			<td colspan=3 align='center'>Belum Ada Data</td>
		</tr>";
}else{
	while ($field = mysql_fetch_array($sql)){
		$MainContent .="
		<tr>
			<td class='center'>$field[Department_ID]</td>
			<td align='left'>$field[Department_Name]</td>
			<td align='left'>$field[Division_Name]</td>
		</tr>";
	}
}
$MainContent .="</table>";

// Pager
$query1= "SELECT d.Division_ID, d.Division_Name, dp.Department_ID, dp.Department_Name
		  FROM M_Department dp, M_Division d
		  WHERE dp.Department_Delete_Time is NULL
		  AND d.Division_ID=dp.Department_DivID
		  ORDER BY dp.Department_ID ";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;
if ($noPage > 1) $Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";

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

$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>
