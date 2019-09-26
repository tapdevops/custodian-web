<?PHP 
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 29 Mei 2012																						=
= Update Terakhir	: 29 Mei 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Detail Pengembalian Dokumen Pembebasan Lahan</title>
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
		$query1 = "SELECT  tdrtolad.TDRTOLAD_ReturnCode, u.User_FullName, d.Division_Name, dp.Department_Name,
		    			   p.Position_Name, tdrtolad.TDRTOLAD_ReturnTime
			   	   FROM TD_ReturnOfLandAcquisitionDocument tdrtolad, M_User u, M_Division d, M_Department dp,
				   		M_Position p,M_DivisionDepartmentPosition ddp
			       WHERE tdrtolad.TDRTOLAD_ReturnCode='$id'
				   AND tdrtolad.TDRTOLAD_UserID=u.User_ID
				   AND ddp.DDP_UserID=u.User_ID
				   AND ddp.DDP_DivID=d.Division_ID
				   AND ddp.DDP_DeptID=dp.Department_ID
				   AND ddp.DDP_PosID=p.Position_ID";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);
		$regdate=strtotime($field1[TDRTOLAD_ReturnTime]);
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
		<th colspan=3>Pengembalian Dokumen Pembebasan Lahan</th>";
	if($custodian==1){
$MainContent .="
		<tr>
			<td width='30%'>No Pengembalian</td>
			<td width='67%'>$field1[TDRTOLAD_ReturnCode]</td>
			<td width='3%'><a href='print-return-of-land-acquisition-document.php?id=$field1[TDRTOLAD_ReturnCode]'><img src='./images/icon-print.png'></a>
			</td>
		</tr>";
	}
	else {
$MainContent .="
		<tr>
			<td width='30%'>No Pengembalian</td>
			<td colspan='2'>$field1[TDRTOLAD_ReturnCode]</td>
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
			<th>Tahap GRL</th>
			<th>Periode GRL</th>
			<th>Tanggal Dokumen</th>
			<th>Blok</th>
			<th>Desa</th>
			<th>Pemilik</th>
			<th>Ket Pengembalian</th>
		</tr>
";

		$queryd = "SELECT dla.DLA_Code, c.Company_Name, dla.DLA_ID,tdrtolad.TDRTOLAD_Information, dla.DLA_Phase,
					      dla.DLA_Period, dla.DLA_DocDate, dla.DLA_Block, dla.DLA_Village,dla.DLA_Owner,
					 	  dla.DLA_Information
					FROM TD_ReturnOfLandAcquisitionDocument tdrtolad, M_DocumentLandAcquisition dla, M_Company c
					WHERE tdrtolad.TDRTOLAD_ReturnCode='$id'
					AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
					AND tdrtolad.TDRTOLAD_DocCode=dla.DLA_Code
					AND dla.DLA_CompanyID=c.Company_ID";
		$sqld = mysql_query($queryd);
		while ($arrd = mysql_fetch_array($sqld)) {
			$perdate=strtotime($arrd['DLA_Period']);
			$fperdate=date("j M Y", $perdate);
			$docdate=strtotime($arrd['DLA_DocDate']);
			$fdocdate=date("j M Y", $docdate);
$MainContent .="
		<tr>
			<td align='center'>$arrd[DLA_Code]</td>
			<td align='center'>$arrd[Company_Name]</td>
			<td align='center'>$arrd[DLA_Phase]</td>
			<td align='center'>$fperdate</td>
			<td align='center'>$fdocdate</td>
			<td align='center'>$arrd[DLA_Block]</td>
			<td align='center'>$arrd[DLA_Village]</td>
			<td align='center'>$arrd[DLA_Owner]</td>
			<td align='center'>$arrd[TDRTOLAD_Information]</td>
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
