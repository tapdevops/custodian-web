<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 24 Agustus 2018																					=
= Update Terakhir	: -           																						=
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
<title>Pendaftaran Dokumen</title>
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
	$query="SELECT  throaod.THROAOD_ID,
					throaod.THROAOD_RegistrationCode,
					u.User_FullName,
					d.Division_Name,
					dp.Department_Name,
					p.Position_Name,
					throaod.THROAOD_RegistrationDate,
					dg.DocumentGroup_Name,
					(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=u.User_SPV2) AS Atasan2
			FROM TH_RegistrationOfAssetOwnershipDocument throaod
			LEFT JOIN M_User u
				ON throaod.THROAOD_UserID=u.User_ID
			LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			LEFT JOIN M_DocumentGroup dg
				ON throaod.THROAOD_DocumentGroupID=dg.DocumentGroup_ID
			WHERE throaod.THROAOD_RegistrationCode='$id'
			AND throaod.THROAOD_Delete_Time is NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THROAOD_RegistrationDate']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Pendaftaran Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THROAOD_RegistrationCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THROAOD_RegistrationCode]";?>" /><br />
				<?PHP echo"$arr[THROAOD_RegistrationCode]"; ?>
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
<table cellpadding='0' cellspacing='0'  width='100%' border='1' class='fixed'>
<thead>
    <tr>
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
    	<th rowspan='2'>Keterangan</th>
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
	$queryd="SELECT c.Company_Name,
					dg.DocumentGroup_Name,
					throaod.THROAOD_Information,
					tdroaod.TDROAOD_Employee_NIK,
					tdroaod.TDROAOD_MK_ID,
					tdroaod.TDROAOD_Type, tdroaod.TDROAOD_Jenis,
					tdroaod.TDROAOD_NoPolisi, tdroaod.TDROAOD_NoRangka, tdroaod.TDROAOD_NoMesin,
					tdroaod.TDROAOD_NoBPKB, tdroaod.TDROAOD_STNK_StartDate, tdroaod.TDROAOD_STNK_ExpiredDate,
					tdroaod.TDROAOD_Pajak_StartDate, tdroaod.TDROAOD_Pajak_ExpiredDate,
					tdroaod.TDROAOD_Lokasi_PT, tdroaod.TDROAOD_Region, tdroaod.TDROAOD_Keterangan
			 FROM TH_RegistrationOfAssetOwnershipDocument throaod
			 LEFT JOIN TD_RegistrationOfAssetOwnershipDocument tdroaod
				ON tdroaod.TDROAOD_THROAOD_ID=throaod.THROAOD_ID
			 LEFT JOIN M_Company c
				ON throaod.THROAOD_CompanyID=c.Company_ID
			 LEFT JOIN M_DocumentGroup dg
				ON throaod.THROAOD_DocumentGroupID=dg.DocumentGroup_ID
			 WHERE throaod.THROAOD_RegistrationCode='$id'
			 AND throaod.THROAOD_Delete_Time is NULL";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while ($arrd = mysql_fetch_array($sqld)) {

    $stnk_sdate=(strpos($arrd['TDROAOD_STNK_StartDate'], '0000-00-00') !== false || strpos($arrd['TDROAOD_STNK_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['TDROAOD_STNK_StartDate']));
    $stnk_exdate=(strpos($arrd['TDROAOD_STNK_ExpiredDate'], '0000-00-00') !== false || strpos($arrd['TDROAOD_STNK_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['TDROAOD_STNK_ExpiredDate']));

    $pajak_sdate=(strpos($arrd['TDROAOD_Pajak_StartDate'], '0000-00-00') !== false || strpos($arrd['TDROAOD_Pajak_StartDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['TDROAOD_Pajak_StartDate']));
    $pajak_exdate=(strpos($arrd['TDROAOD_Pajak_ExpiredDate'], '0000-00-00') !== false || strpos($arrd['TDROAOD_Pajak_ExpiredDate'], '1970-01-01') !== false)?"-":date("j M Y", strtotime($arrd['TDROAOD_Pajak_ExpiredDate']));

	if ($jumdata==8) {
		$style="style='page-break-after:always'";
		$jumdata=0;
	}
	else
	{
		$style="";
	}

    if(strpos($arrd['TDROAOD_Employee_NIK'], 'CO@') !== false){
        $get_company_code = explode('CO@', $arrd['TDROAOD_Employee_NIK']);
        $company_code = $get_company_code[1];
        $query7="SELECT Company_Name AS nama_pemilik, Company_Code
            FROM M_Company
            WHERE Company_code='$company_code'";
    }else{
        $query7="SELECT Employee_FullName AS nama_pemilik, Employee_CompanyCode AS Company_Code
            FROM db_master.M_Employee
            WHERE Employee_NIK='$arrd[TDROAOD_Employee_NIK]'";
    }
    $sql7 = mysql_query($query7);
    $nama_pemilik = "-";
    if(mysql_num_rows($sql7) > 0){
        $data7 = mysql_fetch_array($sql7);
        $nama_pemilik = $data7['nama_pemilik']." - ".$data7['Company_Code'];
    }

    $query8="SELECT MK_Name
        FROM db_master.M_MerkKendaraan
        WHERE MK_ID='$arrd[TDROAOD_MK_ID]'
            #Employee_ResignDate IS NULL";
    $sql8 = mysql_query($query8);
    $merk_kendaraan = "-";
    if(mysql_num_rows($sql8) > 0){
        $data8 = mysql_fetch_array($sql8);
        $merk_kendaraan = $data8['MK_Name'];
    }

echo ("
    <tr $style>
    	<td class='center'>
            $nama_pemilik
        </td>
    	<td class='center'>
  			$merk_kendaraan
        </td>
    	<td class='center'>
  			$arrd[TDROAOD_Type]
        </td>
      	<td class='center'>
			$arrd[TDROAOD_Jenis]
        </td>
    	<td class='center'>
			$arrd[TDROAOD_NoPolisi]
        </td>
    	<td class='center'>
  			$arrd[TDROAOD_NoRangka]
        </td>
        <td class='center'>
			$arrd[TDROAOD_NoMesin]
        </td>
    	<td class='center'>
  			$arrd[TDROAOD_NoBPKB]
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
  			$arrd[TDROAOD_Lokasi_PT]
        </td>
    	<td class='center'>
  			$arrd[TDROAOD_Region]
        </td>
    	<td class='center'>
  			$arrd[TDROAOD_Keterangan]
        </td>
    </tr>

");
	$jumdata ++;
	$keterangan=$arrd['THROAOD_Information'];
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='15' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_COOKIE['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
	</tr>
</tfoot>
</table>
<table cellpadding='0' cellspacing='0'  width="100%" class="detail" style="border:none;">
<tr>
	<td><b><u>KETERANGAN TAMBAHAN:</u></b></td>
</tr>
<tr>
	<td><?PHP echo"$keterangan";?></td>
</tr>
</table>
<table cellpadding='0' cellspacing='0'  width='100%' border='1' align='left'>
	<tr>
    	<td width='20%' class='center'>
        	Diserahkan Oleh
        </td>
    	<!--<td width='20%' class='center'>
        	Disetujui Oleh
        </td>-->
    	<td width='20%' class='center'>
        	Diterima Oleh
        </td>
    	<td width='20%' class='center'>
        	Diketahui Oleh
        </td>
    </tr>
    <tr>
    	<td height='60px'>&nbsp;
        </td>
    	<!--<td>&nbsp;
        </td>-->
    	<td>&nbsp;
        </td>
    	<td>&nbsp;
        </td>
    </tr>
    <tr>
    	<td class='center'>
        	<?PHP echo"$arr[User_FullName]"; ?>
        </td>
<?PHP
	$querycm="SELECT u.User_FullName
				FROM M_Approver a, M_User u, M_Role_Approver ra
				WHERE ra.RA_ID='4'
				AND a.Approver_RoleID=ra.RA_ID
				AND a.Approver_UserID=u.User_ID
				ORDER BY a.Approver_ID DESC LIMIT 1";
	$sqlcm = mysql_query($querycm);
	$arrcm=mysql_fetch_array($sqlcm);
?>
    	<?php /*
    	<td class='center'>
        	<?PHP echo"$atasan"; ?>
        </td>
        */ ?>
    	<td class='center'>
        	Staf Custodian
        </td>
    	<td class='center'>
        	<?PHP echo"$arrcm[User_FullName]"; ?>
        </td>
    </tr>
</table>
</div>
</body>
</html>
<?PHP } ?>
