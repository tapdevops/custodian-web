<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Detail Pengembalian Dokumen</title>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
$act=$_GET["act"];
$id=$_GET["id"];

	if($act=='detail') {
		$id=$_GET['id'];
		$query1 = "SELECT  tdrold.TDRTOLD_ReturnCode, u.User_FullName, d.Division_Name, dp.Department_Name,
		    			   p.Position_Name, tdrold.TDRTOLD_ReturnTime
			   	   FROM TD_ReturnOfLegalDocument tdrold, M_User u, M_Division d, M_Department dp, M_Position p,
				   		M_DivisionDepartmentPosition ddp
			       WHERE tdrold.TDRTOLD_ReturnCode='$id'
				   AND tdrold.TDRTOLD_UserID=u.User_ID
				   AND ddp.DDP_UserID=u.User_ID
				   AND ddp.DDP_DivID=d.Division_ID
				   AND ddp.DDP_DeptID=dp.Department_ID
				   AND ddp.DDP_PosID=p.Position_ID";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);
		$regdate=strtotime($field1[TDRTOLD_ReturnTime]);
		$fregdate=date('j M Y', $regdate);

		// Cek apakah Staff Custodian atau bukan.
		// Staff Custodian memiliki wewenang untuk print pengembalian dokumen.
		$cs_query = "SELECT *
					 FROM M_DivisionDepartmentPosition ddp, M_Department d
					 WHERE ddp.DDP_DeptID=d.Department_ID
					 AND ddp.DDP_UserID='$mv_UserID'
					 AND d.Department_Name LIKE '%Custodian%'";
		$cs_sql = mysql_query($cs_query);
		$custodian = mysql_num_rows($cs_sql);


$MainContent ="
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengembalian Dokumen</th>";
	if($custodian==1){
$MainContent .="
		<tr>
			<td width='30%'>No Pengembalian</td>
			<td width='67%'>$field1[TDRTOLD_ReturnCode]</td>
			<td width='3%'><a href='print-return-of-document.php?id=$field1[TDRTOLD_ReturnCode]'><img src='./images/icon-print.png'></a>
			</td>
		</tr>";
	}
	else {
$MainContent .="
		<tr>
			<td width='30%'>No Pengembalian</td>
			<td colspan='2'>$field1[TDRTOLD_ReturnCode]</td>
		</tr>";
	}
$MainContent .="
		<tr>
			<td>Tanggal Pengembalian</td>
			<td colspan='2'>$fregdate</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td colspan='2'>$field1[User_FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td colspan='2'>$field1[Division_Name]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td colspan='2'>$field1[Department_Name]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td colspan='2'>$field1[Position_Name]</td>
		</tr>
		</table>

		<div class='detail-title'>Daftar Dokumen</div>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th>Kode Dokumen</th>
			<th>Nama Dokumen</th>
			<th>Perusahaan</th>
			<th>Keterangan</th>
		</tr>
";

		$queryd = "SELECT dl.DL_DocCode, dt.DocumentType_Name, c.Company_Name, dg.DocumentGroup_Name,
						  dc.DocumentCategory_Name, dl.DL_NoDoc, dl.DL_ID,tdrold.TDRTOLD_Information,
					 	  di1.DocumentInformation1_Name, di2.DocumentInformation2_Name, dl.DL_Information3
					FROM TD_ReturnOfLegalDocument tdrold, M_DocumentType dt,
					 	 M_DocumentLegal dl, M_Company c, M_DocumentGroup dg, M_DocumentCategory dc,
						 M_DocumentInformation1 di1, M_DocumentInformation2 di2
					WHERE tdrold.TDRTOLD_ReturnCode='$id'
					AND tdrold.TDRTOLD_Delete_Time IS NULL
					AND tdrold.TDRTOLD_DocCode=dl.DL_DocCode
					AND dl.DL_TypeDocID=dt.DocumentType_ID
					AND dl.DL_CompanyID=c.Company_ID
					AND dl.DL_GroupDocID=dg.DocumentGroup_ID
					AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
					AND dl.DL_Information1=di1.DocumentInformation1_ID
					AND dl.DL_Information2=di2.DocumentInformation2_ID";
		$sqld = mysql_query($queryd);
		while ($arrd = mysql_fetch_array($sqld)) {
$MainContent .="
		<tr>
			<td>$arrd[DL_DocCode]</td>
			<td>$arrd[DocumentType_Name] No $arrd[DL_NoDoc]</td>
			<td>$arrd[Company_Name]</td>
			<td>$arrd[TDRTOLD_Information]</td>
		</tr>
";
		}
$MainContent .="
		</table>
";
	}

$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>
</script>
