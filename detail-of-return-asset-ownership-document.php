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
		$query1 = "SELECT  tdrtoaod.TDRTOAOD_ReturnCode, u.User_FullName, d.Division_Name, dp.Department_Name,
		    			   p.Position_Name, tdrtoaod.TDRTOAOD_ReturnTime, u.User_ID
			   	   FROM TD_ReturnOfAssetOwnershipDocument tdrtoaod
				   LEFT JOIN M_User u
						ON tdrtoaod.TDRTOAOD_UserID=u.User_ID
				   LEFT JOIN M_DivisionDepartmentPosition ddp
						ON u.User_ID=ddp.DDP_UserID
						AND ddp.DDP_Delete_Time is NULL
				   LEFT JOIN M_Division d
						ON ddp.DDP_DivID=d.Division_ID
				   LEFT JOIN M_Department dp
						ON ddp.DDP_DeptID=dp.Department_ID
				   LEFT JOIN M_Position p
						ON ddp.DDP_PosID=p.Position_ID
			       WHERE tdrtoaod.TDRTOAOD_ReturnCode='$id'";
		$sql1 = mysql_query($query1);
		$field1 = mysql_fetch_array($sql1);
		$fregdate=date('j M Y', strtotime($field1[TDRTOAOD_ReturnTime]));

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
			<td width='67%'>$field1[TDRTOAOD_ReturnCode]</td>
			<td width='3%'><a href='print-return-of-asset-ownership-document.php?id=$field1[TDRTOAOD_ReturnCode]'><img src='./images/icon-print.png'></a>
			</td>
		</tr>";
	}
	else {
$MainContent .="
		<tr>
			<td width='30%'>No Pengembalian</td>
			<td colspan='2'>$field1[TDRTOAOD_ReturnCode]</td>
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
			<th rowspan='2'>Kode Dokumen</th>
			<th rowspan='2'>Nama Pemilik</th>
			<th rowspan='2'>Merk Kendaraan</th>
			<th rowspan='2'>Type</th>
			<th rowspan='2'>Jenis</th>
			<th rowspan='2'>No. Polisi</th>
			<th rowspan='2'>No. Rangka</th>
			<th rowspan='2'>No. Mesin</th>
			<th rowspan='2'>No. BPKB</th>
			<th colspan='2'>STNK</th>
			<th colspan='2'>Pajak Kendaraan</th>
			<th rowspan='2'>Lokasi (PT)</th>
			<th rowspan='2'>Region</th>
			<th rowspan='2'>Ket. Pengembalian</th>
		</tr>
		<tr>
			<th>Start Date</th>
			<th>Expired Date</th>
			<th>Start Date</th>
			<th>Expired Date</th>
		</tr>
";

		$queryd = "SELECT dao.DAO_DocCode,
						  dao.DAO_ID,tdrtoaod.TDRTOAOD_Information,
						  dao.DAO_Employee_NIK,
                          CASE WHEN dao.DAO_Employee_NIK LIKE 'CO@%'
  						  THEN
  						  	(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(dao.DAO_Employee_NIK, 'CO@', ''))
  						  ELSE
  							(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=dao.DAO_Employee_NIK)
  						  END nama_pemilik,
						  m_mk.MK_Name, dao.DAO_Type, dao.DAO_Jenis,
						  dao.DAO_NoPolisi, dao.DAO_NoRangka, dao.DAO_NoMesin, dao.DAO_NoBPKB,
						  dao.DAO_STNK_StartDate, dao.DAO_STNK_ExpiredDate,
						  dao.DAO_Pajak_StartDate, dao.DAO_Pajak_ExpiredDate,
						  dao.DAO_Lokasi_PT, dao.DAO_Region
					FROM TD_ReturnOfAssetOwnershipDocument tdrtoaod
					INNER JOIN M_DocumentAssetOwnership dao
						ON tdrtoaod.TDRTOAOD_DocCode=dao.DAO_DocCode
					INNER JOIN db_master.M_MerkKendaraan m_mk
						ON dao.DAO_MK_ID=m_mk.MK_ID
					WHERE tdrtoaod.TDRTOAOD_ReturnCode='$id'
					AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL";
		$sqld = mysql_query($queryd);
		while ($arrd = mysql_fetch_array($sqld)) {
            $stnk_sdate=(strpos($arrd['DAO_STNK_StartDate'], '0000-00-00') !== false || strpos($arrd['DAO_STNK_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['DAO_STNK_StartDate']));
			$stnk_exdate=(strpos($arrd['DAO_STNK_ExpiredDate'], '0000-00-00') !== false || strpos($arrd['DAO_STNK_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['DAO_STNK_ExpiredDate']));

			$pajak_sdate=(strpos($arrd['DAO_Pajak_StartDate'], '0000-00-00') !== false || strpos($arrd['DAO_Pajak_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['DAO_Pajak_StartDate']));
			$pajak_exdate=(strpos($arrd['DAO_Pajak_ExpiredDate'], '0000-00-00') !== false || strpos($arrd['DAO_Pajak_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['DAO_Pajak_ExpiredDate']));

$MainContent .="
        <tr>
            <td align='center'>$arrd[DAO_DocCode]</td>
            <td align='center'>$arrd[nama_pemilik]</td>
            <td align='center'>$arrd[MK_Name]</td>
            <td align='center'>$arrd[DAO_Type]</td>
            <td align='center'>$arrd[DAO_Jenis]</td>
            <td align='center'>$arrd[DAO_NoPolisi]</td>
            <td align='center'>$arrd[DAO_NoRangka]</td>
            <td align='center'>$arrd[DAO_NoMesin]</td>
            <td align='center'>$arr[DAO_NoBPKB]</td>
            <td align='center'>$stnk_sdate</td>
            <td align='center'>$stnk_exdate</td>
            <td align='center'>$pajak_sdate</td>
            <td align='center'>$pajak_exdate</td>
            <td align='center'>$arrd[DAO_Lokasi_PT]</td>
            <td align='center'>$arrd[DAO_Region]</td>
            <td align='center'><pre>$arrd[TDRTOAOD_Information]</pre></td>
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
