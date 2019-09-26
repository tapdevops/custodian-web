<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 29 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
=		26/09/2012	: Perubahan Query (LEFT JOIN) & Penambahan Header-Footer											=
=		10/10/2012  : Perubahan baris 344 yang semula ($pers=$not_ext[$a]/$jRow*100;) jadi 								=
					  ($pers=$not_ext[$a]/$jumdata*100;																	=
=========================================================================================================================
*/
session_start(); 
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
if(!isset($_SESSION['User_ID'])) {
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
	$query="SELECT  thrgolad.THRGOLAD_ID,
					thrgolad.THRGOLAD_RegistrationCode,
					thrgolad.THRGOLAD_RegistrationDate,
					dg.DocumentGroup_Name,
					u.User_FullName,
					d.Division_Name,
					dp.Department_Name,
					p.Position_Name,
					(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=u.User_SPV2) AS Atasan2
			FROM TH_RegistrationOfLandAcquisitionDocument thrgolad
			LEFT JOIN M_DocumentGroup dg
				ON dg.DocumentGroup_ID='3'
			LEFT JOIN M_User u
				ON thrgolad.THRGOLAD_UserID=u.User_ID 
			LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID 
			LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID 
			LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID 
			LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID 
			WHERE thrgolad.THRGOLAD_RegistrationCode='$id' 
			AND thrgolad.THRGOLAD_Delete_Time is NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THRGOLAD_RegistrationDate']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Pendaftaran Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THRGOLAD_RegistrationCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THRGOLAD_RegistrationCode]";?>" /><br />
				<?PHP echo"$arr[THRGOLAD_RegistrationCode]"; ?>
		   </td>
		</tr>
		<tr>
			<td>
				<b>Nama Pendaftar</b>
			</td>
			<td>&nbsp;</td>
			<td>
				<b>Jabatan</b>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				<b>Divisi</b>
			</td>
			<td>&nbsp;</td>
			<td>
				<b>Atasan</b>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>
				<b>Departemen</b>
			</td>
			<td>&nbsp;</td>
			<td>
			</td>
			<td>
			</td>
		</tr>
	</table>
</div>

<div id='content'>
<?PHP
	$queryd = "	SELECT DISTINCT Company_Name,
								THRGOLAD_Phase,
								THRGOLAD_Period,
								TDRGOLAD_DocDate,
								TDRGOLAD_Block,
								TDRGOLAD_Village,
								TDRGOLAD_Owner, 
							    TDRGOLAD_Information,
								TDRGOLAD_AreaClass,
								TDRGOLAD_AreaPrice,
								TDRGOLAD_AreaStatement,
								TDRGOLAD_AreaTotalPrice, 
							    TDRGOLAD_PlantClass,
								TDRGOLAD_PlantQuantity,
								TDRGOLAD_PlantPrice,
								TDRGOLAD_Revision,
								TDRGOLAD_PlantTotalPrice,
							    TDRGOLAD_GrandTotal, 
								THRGOLAD_Information,
								TDRGOLAD_ID
				FROM TH_RegistrationOfLandAcquisitionDocument
				LEFT JOIN TD_RegistrationOfLandAcquisitionDocument
					ON TDRGOLAD_THRGOLAD_ID=THRGOLAD_ID
					AND TDRGOLAD_Delete_Time IS NULL
				LEFT JOIN M_Company
					ON THRGOLAD_CompanyID=Company_ID
				WHERE THRGOLAD_RegistrationCode='$id'
				AND THRGOLAD_Delete_Time is NULL";
	$sqlhd = mysql_query($queryd);
	$sqld = mysql_query($queryd);
	$objhd=mysql_fetch_object($sqlhd);
	$fperdate=date("j M Y", strtotime($objhd->THRGOLAD_Period));
	
?>
<table cellpadding='0' cellspacing='0'  width="100%" style="border:none;">
    <tr>
        <td width='13%' align="left" valign="top">Kebun</td>
        <td width='22%' align="left" valign="top"><?PHP echo"$objhd->Company_Name"; ?></td>
        <td rowspan="4" align="left" valign="top" width="56%">
        <u>Hal yang perlu diperhatikan sebelum pemeriksaan dokumen pembebasan lahan :</u>
        <ol>
        <li>Status lahan secara legalitas (peta batas ijin lokasi/HGU, status kawasan menurut TGHK & RTRWP</li>
        <li>Dokumen Soil Survey Valid (Penentuan Area Berpasir/Rawa Tergenang/Gambut Galam</li>
        </ol>
        </td>
        <td rowspan="4" align="left" valign="top">
        <b>
        v : ada<br />
        x : tidak ada<br />
        ≠ : tidak perlu<br />
        λ : kondisional
        </b>
		</td>
    </tr>
    <tr>
    	<td align="left" valign="top">Tahap</td>
        <td align="left" valign="top"><?PHP echo"$objhd->THRGOLAD_Phase"; ?></td>
  	</tr>
    <tr>
    	<td align="left" valign="top">Periode Ganti Rugi</td>
        <td align="left" valign="top"><?PHP echo"$fperdate"; ?></td>
  	</tr>
    <tr>
    	<td align="left" valign="top">Tanggal Pemeriksaan</td>
        <td align="left" valign="top"><?PHP echo"$fregdate"; ?></td>
    </tr>
</table>
<table width='100%' border='1' class='detail' cellpadding="0" cellspacing="0" >
<thead>
    <tr>
        <th rowspan='2'>Tanggal</th>
        <th rowspan='2'>Blok</th>
        <th rowspan='2'>Desa</th>
        <th rowspan='2'>Nama Pemilik</th>
        <th rowspan='2'>Kelas</th>
        <th colspan='3'>Lahan</th>
       	<th rowspan='2'>Kelas</th>
        <th colspan='3'>Tanam Tumbuh</th>
       	<th rowspan='2'>Total</th>
		<th rowspan='2'>Keterangan</th>
	<?PHP
	$t_query="SELECT * 
				FROM M_LandAcquisitionAttribute 
				WHERE LAA_Delete_Time is NULL 
				ORDER BY LAA_ID ";
	$t_sql=mysql_query($t_query);
	$counts=mysql_num_rows($t_sql);
	?>
	<th colspan='<?PHP echo"$counts";?>'>Kelengkapan Dokumen</th>
    </tr>
	<tr>
		<th>Ha</th>
		<th>Rp/Ha</th>
		<th>Nilai (Rp)</th>
		<th>Qty</th>
		<th>Rp/Pkk</th>
		<th>Nilai (Rp)</th>
	<?PHP while ($t_arr = mysql_fetch_array($t_sql)){ ?>
		<th align="center" width="10px" valign="top"><?PHP echo"$t_arr[LAA_Acronym]"; ?></th>
	<?PHP } ?>
	</tr>
</thead>
<tbody>
<?	
	$tHa=0; $tSumHa=0; $tQty=0; $tSumQty=0; $tSum=0;

	while ($arrd = mysql_fetch_array($sqld)) {
		$fdocdate=date("j M Y", strtotime($arrd['TDRGOLAD_DocDate']));
		
		/*
		if ($jumdata==10) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else
		{
			$style="";
		}*/
		
		$TDRGOLAD_AreaStatement=number_format($arrd[TDRGOLAD_AreaStatement],2,',','.');
		$TDRGOLAD_AreaPrice=number_format($arrd[TDRGOLAD_AreaPrice],0,',','.');
		$TDRGOLAD_AreaTotalPrice=number_format($arrd[TDRGOLAD_AreaTotalPrice],0,',','.');
		$TDRGOLAD_PlantQuantity=number_format($arrd[TDRGOLAD_PlantQuantity],2,',','.');
		$TDRGOLAD_PlantPrice=number_format($arrd[TDRGOLAD_PlantPrice],0,',','.');
		$TDRGOLAD_PlantTotalPrice=number_format($arrd[TDRGOLAD_PlantTotalPrice],0,',','.');
		$TDRGOLAD_GrandTotal=number_format($arrd[TDRGOLAD_GrandTotal],0,',','.');
		
		// TOTAL
		$tHa=$tHa+$arrd[TDRGOLAD_AreaStatement]; 
		$tSumHa=$tSumHa+$arrd[TDRGOLAD_AreaTotalPrice]; 
		$tQty=$tQty+$arrd[TDRGOLAD_PlantQuantity]; 
		$tSumQty=$tSumQty+$arrd[TDRGOLAD_PlantTotalPrice]; 
		$tSum=$tSum+$arrd[TDRGOLAD_GrandTotal];
		
		echo "
		<tr $style>
			<td align='center'>$fdocdate</td>
			<td align='center'>$arrd[TDRGOLAD_Block]</td>
			<td align='center'>$arrd[TDRGOLAD_Village]</td>
			<td align='center'>$arrd[TDRGOLAD_Owner]</td>
			<td align='center'>$arrd[TDRGOLAD_AreaClass]</td>
			<td align='right'>$TDRGOLAD_AreaStatement</td>
			<td align='right'>$TDRGOLAD_AreaPrice</td>
			<td align='right'>$TDRGOLAD_AreaTotalPrice</td>
			<td align='center'>$arrd[TDRGOLAD_PlantClass]</td>
			<td align='right'>$TDRGOLAD_PlantQuantity</td>
			<td align='right'>$TDRGOLAD_PlantPrice</td>
			<td align='right'>$TDRGOLAD_PlantTotalPrice</td>
			<td align='right'>$TDRGOLAD_GrandTotal</td>
			<td align='right'>$arrd[TDRGOLAD_Information]</td>";
		$at_query="	SELECT LAAS_Symbol,LAAS_ID
					FROM TD_RegistrationOfLandAcquisitionDocumentDetail
					LEFT JOIN M_LandAcquisitionAttributeStatus
						ON TDRGOLADD_AttributeStatusID=LAAS_ID
					WHERE TDRGOLADD_TDRGOLAD_ID='$arrd[TDRGOLAD_ID]' 
					AND TDRGOLADD_Delete_Time IS NULL";
		$at_sql = mysql_query($at_query);
		
		$kelengkapan=1;
		while (($at_arr = mysql_fetch_array($at_sql))&&($kelengkapan<'15')) {
			if ($at_arr[LAAS_ID]=='1')
				$ext[$kelengkapan]=intval($ext[$kelengkapan])+1;
			if ($at_arr[LAAS_ID]=='2')
				$not_ext[$kelengkapan]=intval($not_ext[$kelengkapan])+1;
				
			echo"<td align='center'>$at_arr[LAAS_Symbol]</td>";
			$kelengkapan++;
		}

		echo "</tr>";
		$jumdata++;
	}
	//FORMAT TOTAL
	$tHa=number_format($tHa,2,',','.');
	$tSumHa=number_format($tSumHa,0,',','.');
	$tQty=number_format($tQty,2,',','.');
	$tSumQty=number_format($tSumQty,0,',','.');
	$tSum=number_format($tSum,0,',','.');

?>
   <tr>
   		<th colspan='5'>TOTAL</th>
        <th align="right"><?PHP echo"$tHa"; ?></th>
        <th>&nbsp;</th>
        <th align="right"><?PHP echo"$tSumHa"; ?></th>
        <th>&nbsp;</th>
        <th align="right"><?PHP echo"$tQty"; ?></th>
        <th>&nbsp;</th>
        <th align="right"><?PHP echo"$tSumQty"; ?></th>
        <th align="right"><?PHP echo"$tSum"; ?></th>
		<th>&nbsp;</th>
        <th colspan="<?PHP echo"$counts";?>">&nbsp;</th>
   </tr>
   <tr>
   		<td colspan="14" align="right">Persentase Data Tidak Lengkap</td>
<?PHP
	
		for ($a=1;$a<$kelengkapan;$a++){
		$pers=$not_ext[$a]/$jumdata*100;
		$pers=number_format($pers,0,',','.');
		
		echo "<td>$pers%</td>";
		}
?>
   </tr>
   <tr>
   		<td colspan="14" align="right">Total Dokumen Yang Tidak Dilengkapi</td>
<?PHP
		for ($a=1;$a<$kelengkapan;$a++){
			if ($not_ext[$a]==NULL)
				echo"<td>0</td>";
			else 
				echo"<td>$not_ext[$a]</td>";
		}
?>
   </tr>
   <tr>
   		<td colspan="14" align="right">Total Kelengkapan Dokumen</td>
<?PHP
		for ($a=1;$a<$kelengkapan;$a++){
		echo"<td>$ext[$a]</td>";
		}
?>
   </tr>
</tbody>
<tfoot>
	<tr>
		<td colspan='100' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_SESSION['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
	</tr>
</tfoot>
</table>
<table cellpadding='0' cellspacing='0'  width="100%" class="detail" style="border:none;">
<tr>
	<td><b><u>KETERANGAN TAMBAHAN:</u></b></td>
</tr>  
<tr>
	<td><?PHP echo"<pre>$objhd->THRGOLAD_Information</pre>";?></td>
</tr>  
</table>
<table cellpadding='0' cellspacing='0'  width='80%' border='1' align='left' cellpadding="0" cellspacing="0">
	<tr>
    	<td width='20%' class='center'>
        	Diserahkan Oleh
        </td>
    	<td width='20%' class='center'>
        	Disetujui Oleh
        </td>
    	<td width='20%' class='center'>
        	Diterima Oleh
        </td>
    	<td width='20%' class='center'>
        	Diketahui Oleh
        </td>
    </tr>
    <tr>
    	<td height='60px'>&nbsp;</td>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
    </tr>
    <tr>
    	<td class='center'>&nbsp;</td>
<?PHP
	$querycm="SELECT u.User_FullName
				FROM M_Approver a, M_User u, M_Role_Approver ra
				WHERE ra.RA_ID='4'
				AND a.Approver_ID=ra.RA_ID
				AND a.Approver_UserID=u.User_ID";
	$sqlcm = mysql_query($querycm);
	$arrcm=mysql_fetch_array($sqlcm);
?>
    	<td class='center'>&nbsp;</td>
    	<td class='center'>Staf Custodian</td>
    	<td class='center'><?PHP echo"$arrcm[User_FullName]"; ?></td>
    </tr>
</table>
</div>
</body>
</html>
<?PHP } ?>