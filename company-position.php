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
<title>Custodian System | Daftar Posisi</title>
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
if(isset($_GET['page'])){
    $noPage = $_GET['page'];
}
else
	$noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT *
		  FROM M_Position
		  WHERE Position_Delete_Time is NULL
		  ORDER BY Position_Name
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);


$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th width='30%'>Kode Posisi</th>
		<th width='70%'>Nama Posisi</th>
	</tr>";

if ($num==NULL){
	$MainContent .="
		<tr>
			<td colspan=2 align='center'>Belum Ada Data</td>
		</tr>";
}else{
	while ($field = mysql_fetch_array($sql)){
		$MainContent .="
			<tr>
				<td class='center'>$field[Position_ID]</td>
				<td align='left'>$field[Position_Name]</td>
			</tr>
		";
	}
}
$MainContent .="</table>";

$query1 = "	SELECT *
			FROM M_Position
			WHERE Position_Delete_Time is NULL";
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

$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>
