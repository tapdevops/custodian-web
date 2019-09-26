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
		$query1 = "SELECT  tdrtoonld.TDRTOONLD_ReturnCode, u.User_FullName, d.Division_Name, dp.Department_Name,
		    			   p.Position_Name, tdrtoonld.TDRTOONLD_ReturnTime, u.User_ID
			   	   FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld
				   LEFT JOIN M_User u
						ON tdrtoonld.TDRTOONLD_UserID=u.User_ID
				   LEFT JOIN M_DivisionDepartmentPosition ddp
						ON u.User_ID=ddp.DDP_UserID
						AND ddp.DDP_Delete_Time is NULL
				   LEFT JOIN M_Division d
						ON ddp.DDP_DivID=d.Division_ID
				   LEFT JOIN M_Department dp
						ON ddp.DDP_DeptID=dp.Department_ID
				   LEFT JOIN M_Position p
						ON ddp.DDP_PosID=p.Position_ID
			       WHERE tdrtoonld.TDRTOONLD_ReturnCode='$id'";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);
		$regdate=strtotime($field1[TDRTOONLD_ReturnTime]);
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
			<td width='67%'>$field1[TDRTOONLD_ReturnCode]</td>
			<td width='3%'><a href='print-return-of-document.php?id=$field1[TDRTOONLD_ReturnCode]'><img src='./images/icon-print.png'></a>
			</td>
		</tr>";
	}
	else {
$MainContent .="
		<tr>
			<td width='30%'>No Pengembalian</td>
			<td colspan='2'>$field1[TDRTOONLD_ReturnCode]</td>
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
			<th>Perusahaan</th>
			<th>No. Dokumen</th>
			<th>Nama Dokumen</th>
			<th>Tahun Dokumen</th>
			<th>Departemen</th>
			<th>Ket. Pengembalian</th>
		</tr>
";

		$queryd = "SELECT donl.DONL_DocCode, c.Company_Name,
						  donl.DONL_ID, tdronld.TDRTOONLD_Information,  m_d.Department_Name,
						  donl.DONL_NoDokumen, donl.DONL_NamaDokumen, donl.DONL_TahunDokumen
					FROM TD_ReturnOfOtherNonLegalDocuments tdronld,
					 	 M_DocumentsOtherNonLegal donl, M_Company c,
						 db_master.M_Department m_d
					WHERE tdronld.TDRTOONLD_ReturnCode='$id'
					AND tdronld.TDRTOONLD_Delete_Time IS NULL
					AND tdronld.TDRTOONLD_DocCode=donl.DONL_DocCode
					AND donl.DONL_CompanyID=c.Company_ID
					AND donl.DONL_Dept_Code=m_d.Department_Code";
		$sqld = mysql_query($queryd);
		while ($arrd = mysql_fetch_array($sqld)) {
$MainContent .="
            <tr>
                <td align='center'>$arrd[DONL_DocCode]</td>
                <td class='center'>$arrd[Company_Name]</td>
                <td class='center'>$arrd[DONL_NoDokumen]</td>
                <td class='center'>$arrd[DONL_NamaDokumen]</td>
                <td class='center'>$arrd[DONL_TahunDokumen]</td>
                <td class='center'>$arrd[Department_Name]</td>
                <td align='center'><pre>$arrd[TDRTOONLD_Information]</pre></td>
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
