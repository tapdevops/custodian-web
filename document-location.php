<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Lokasi Dokumen Yang Tersedia</title>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$dataPerPage = 20;
if(isset($_GET['page'])) {
    $noPage = $_GET['page'];
}
else $noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT *
			FROM L_DocumentLocation
			WHERE DL_Status<>'0'
			AND DL_Delete_Time is NULL
			ORDER BY DL_Code
			LIMIT $offset, $dataPerPage"; //Arief F 21082018
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Lokasi Dokumen</th>
		<th>Kode Lokasi Dokumen</th>
		<th>Nama Lokasi Dokumen</th>
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
		<th>ID Lokasi Dokumen</th>
		<th>Kode Lokasi Dokumen</th>
		<th>Nama Lokasi Dokumen</th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[DL_ID]</td>
		<td class='center'>$field[DL_Code]</td>
		<td class='center'>$field[DL_Name]</td>
	</tr>
";
 }
}
$MainContent .="
	</table>
";

$query1 = "SELECT *
			FROM L_DocumentLocation
			WHERE DL_Status<>'0'
			AND DL_Delete_Time is NULL
			ORDER BY DL_ID";
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
