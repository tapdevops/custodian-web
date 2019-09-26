<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource               																			=
= Dibuat Tanggal	: 7 Sep 2018																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pengembalian Dokumen</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico">
<link href="./css/style-print.css" rel="stylesheet" type="text/css">
<script src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
   	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
   	$(".stripeMe tr:even").addClass("alt");
 	});
</script>
<SCRIPT>
function printPage(){
document.getElementById('PrintButton').style.display = "none"
window.print()
document.getElementById('PrintButton').style.display = "block"
}
</SCRIPT>

</head>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($mv_UserID)) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
?>

<body>
<div id='header'>
	<!--<input type='button' name="PrintButton" id="PrintButton" onclick='printPage()' value='CETAK' class='print-button' />-->
	<div id='header-inside'>
    	<div class="tap">PT Triputra Agro Persada </div>
        <div class="custodian">Custodian Department </div>
        <div class="alamat">Jalan DR.Ide Anak Agung Gde Agung Kav. E.3.2. No 1<br />
        Jakarta - 12950</div>
    </div>
	<div style='border-bottom:#000 solid 3px;'></div>
	<?PHP
	$id=$_GET["id"];
	$query = "SELECT  tdroaod.TDRTOAOD_ReturnCode,
					  u.User_FullName,
					  d.Division_Name,
					  dp.Department_Name,
					  p.Position_Name,
					  tdroaod.TDRTOAOD_ReturnTime,
					  dg.DocumentGroup_Name,
					  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV2) AS Atasan2
			  FROM TD_ReturnOfAssetOwnershipDocument tdroaod
			  LEFT JOIN M_User u
				ON tdroaod.TDRTOAOD_UserID=u.User_ID
			  LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			  LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			  LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			  LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			  LEFT JOIN M_DocumentAssetOwnership dao
				ON tdroaod.TDRTOAOD_DocCode=dao.DAO_DocCode
			  LEFT JOIN M_DocumentGroup dg
				ON dao.DAO_GroupDocID=dg.DocumentGroup_ID
			  WHERE tdroaod.TDRTOAOD_ReturnCode='$id'";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['TDRTOAOD_ReturnTime']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Pengembalian Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pengembalian</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[TDRTOAOD_ReturnCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tgl Pengembalian</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[TDRTOAOD_ReturnCode]";?>" /><br />
				<?PHP echo"$arr[TDRTOAOD_ReturnCode]"; ?>
		   </td>
		</tr>
		<tr>
			<td>
				<b>Nama Penerima</b>
			</td>
			<td>
				<?PHP echo"$arr[User_FullName]"; ?>
			</td>
			<td>
				<b>Jabatan</b>
			</td>
			<td>
				<?PHP echo"$arr[Position_Name]"; ?>
			</td>
		</tr>
		<tr>
			<td>
				<b>Divisi</b>
			</td>
			<td>
				<?PHP echo"$arr[Division_Name]"; ?>
			</td>
			<td>
				<b>Atasan</b>
			</td>
			<td>
				<?PHP echo"$atasan"; ?>
			</td>
		</tr>
		<tr>
			<td>
				<b>Departemen</b>
			</td>
			<td>
				<?PHP echo"$arr[Department_Name]"; ?>
			</td>
			<td>
			</td>
			<td>
			</td>
		</tr>
	</table>
</div>

<div id='content'>
<table cellpadding='0' cellspacing='0'  width='100%' border='1' id='mytable' class='stripeMe'>
<thead>
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
</thead>
<tbody>
<?PHP
	$queryd="SELECT dao.DAO_DocCode, dg.DocumentGroup_Name,
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
       				dao.DAO_Lokasi_PT, dao.DAO_Region,
                    tdroaod.TDRTOAOD_Information
			 FROM TD_ReturnOfAssetOwnershipDocument tdroaod,
			 	  M_DocumentAssetOwnership dao, M_DocumentGroup dg,
                  db_master.M_MerkKendaraan m_mk
			 WHERE tdroaod.TDRTOAOD_ReturnCode='$id'
    			 AND tdroaod.TDRTOAOD_Delete_Time IS NULL
    			 AND tdroaod.TDRTOAOD_DocCode=dao.DAO_DocCode
    			 AND dao.DAO_GroupDocID=dg.DocumentGroup_ID
                 AND m_mk.MK_ID=dao.DAO_MK_ID";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while ($arrd = mysql_fetch_array($sqld)) {
        $stnk_sdate=date("j M Y", strtotime($arrd['DAO_STNK_StartDate']));
    	$stnk_exdate=date("j M Y", strtotime($arrd['DAO_STNK_ExpiredDate']));
        $pajak_sdate=date("j M Y", strtotime($arrd['DAO_Pajak_StartDate']));
    	$pajak_exdate=date("j M Y", strtotime($arrd['DAO_Pajak_ExpiredDate']));

		if ($jumdata==8) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else
		{
			$style="";
		}

		echo ("
		<tr $style>
			<td class='center'>
				$arrd[DAO_DocCode]
			</td>
           <td class='center'>
   			$arrd[nama_pemilik]
           </td>
           <td class='center'>
   			$arrd[MK_Name]
           </td>
           <td class='center'>
   			$arrd[DAO_Type]
           </td>
       	<td class='center'>
   			$arrd[DAO_Jenis]
           </td>
       	<td class='center'>
     			$arrd[DAO_NoPolisi]
           </td>
           <td class='center'>
     			$arrd[DAO_NoRangka]
           </td>
           <td class='center'>
     			$arrd[DAO_NoMesin]
           </td>
           <td class='center'>
     			$arrd[DAO_NoBPKB]
           </td>
       	   <td class='center'>
     			$stnk_sdate
           </td>
           <td class='center'>
     			$stnk_exdate
           </td>
           <td class='center'>
     			$pajak_sdate
           </td>
           <td class='center'>
     			$pajak_exdate
           </td>
       	   <td class='center'>
     			$arrd[DAO_Lokasi_PT]
           </td>
       	   <td class='center'>
     			$arrd[DAO_Region]
           </td>
			<td class='center'>
				$arrd[TDRTOAOD_Information]
			</td>
		</tr>");
		$jumdata ++;
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='17' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_COOKIE['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
	</tr>
</tfoot>
</table>
<table cellpadding='0' cellspacing='0'  width='60%' border='1' align='left'>
	<tr>
    	<td width='20%' class='center'>
        	Diserahkan Oleh
        </td>
    	<td width='20%' class='center'>
        	Diketahui Oleh
        </td>
    	<td width='20%' class='center'>
        	Diterima Oleh
        </td>
    </tr>
    <tr>
    	<td height='60px'>
        </td>
    	<td>
        </td>
    	<td>
        </td>
    </tr>
    <tr>
<?PHP
	$queryr="SELECT u.User_FullName
				FROM TD_ReturnOfAssetOwnershipDocument tdrtroaod, M_User u, TD_LoanOfAssetOwnershipDocument tdloaod,
					 TH_LoanOfAssetOwnershipDocument thloaod
				WHERE tdloaod.TDLOAOD_DocCode=tdrtroaod.TDRTOAOD_DocCode
				AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
				AND thloaod.THLOAOD_UserID=u.User_ID
				AND tdrtroaod.TDRTOAOD_ReturnCode='$id'
				ORDER BY tdloaod.TDLOAOD_Insert_Time DESC";
	$sqlr = mysql_query($queryr);
	$arrr=mysql_fetch_array($sqlr);
?>
    	<td class='center'>
        	<?PHP echo"$arrr[User_FullName]"; ?>
        </td>
    	<td class='center'>
        	<?PHP echo"$atasan"; ?>
        </td>
    	<td class='center'>
        	<?PHP echo"$arr[User_FullName]"; ?>
        </td>
    </tr>
</table>
</div>
</body>
</html>
<?PHP } ?>
