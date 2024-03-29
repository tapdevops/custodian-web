<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Agustus 2018																					=
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
	$query="SELECT  throold.THROOLD_ID,
					throold.THROOLD_RegistrationCode,
					u.User_FullName,
					d.Division_Name,
					dp.Department_Name,
					p.Position_Name,
					throold.THROOLD_RegistrationDate,
					dg.DocumentGroup_Name,
					(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=u.User_SPV2) AS Atasan2
			FROM TH_RegistrationOfOtherLegalDocuments throold
			LEFT JOIN M_User u
				ON throold.THROOLD_UserID=u.User_ID
			LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			LEFT JOIN M_DocumentGroup dg
				ON throold.THROOLD_DocumentGroupID=dg.DocumentGroup_ID
			WHERE throold.THROOLD_RegistrationCode='$id'
			AND throold.THROOLD_Delete_Time is NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THROOLD_RegistrationDate']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Pendaftaran Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THROOLD_RegistrationCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THROOLD_RegistrationCode]";?>" /><br />
				<?PHP echo"$arr[THROOLD_RegistrationCode]"; ?>
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
<table cellpadding='0' cellspacing='0'  width='100%' border='1' class='fixed2'>
<thead>
    <tr>
        <th>Perusahaan</th>
        <th>Kategori Dokumen</th>
        <th>Nama Dokumen</th>
        <th>Instansi Terkait</th>
        <th>No. Dokumen</th>
        <th>Tanggal Terbit</th>
        <th>Tanggal Berakhir</th>
        <th>Keterangan</th>
    </tr>
</thead>
<tbody>
<?PHP
	$queryd="SELECT DISTINCT CASE WHEN TDROOLD_Core_CompanyID IS NOT NULL
                        THEN TDROOLD_Core_CompanyID
                        ELSE THROOLD_CompanyID
                    END AS core_company_id,
                    CASE WHEN TDROOLD_Core_CompanyID IS NOT NULL
                        THEN (SELECT Company_Name FROM M_Company
                                WHERE Company_ID = TDROOLD_Core_CompanyID
                            )
                        ELSE (SELECT Company_Name FROM M_Company
                                WHERE Company_ID = THROOLD_CompanyID
                            )
                    END AS core_company_name,
					-- dg.DocumentGroup_Name,
					throold.THROOLD_Information,
					tdroold.TDROOLD_KategoriDokumenID,
					tdroold.TDROOLD_NamaDokumen,
					tdroold.TDROOLD_InstansiTerkait, tdroold.TDROOLD_NoDokumen,
					tdroold.TDROOLD_TglTerbit, tdroold.TDROOLD_TglBerakhir,
                    tdroold.TDROOLD_Keterangan
			 FROM TH_RegistrationOfOtherLegalDocuments throold
			 LEFT JOIN TD_RegistrationOfOtherLegalDocuments tdroold
				ON tdroold.TDROOLD_THROOLD_ID=throold.THROOLD_ID
			 -- LEFT JOIN M_Company c
				-- ON throold.THROOLD_CompanyID=c.Company_ID
			 -- LEFT JOIN M_DocumentGroup dg
				-- ON throold.THROOLD_DocumentGroupID=dg.DocumentGroup_ID
			 WHERE throold.THROOLD_RegistrationCode='$id'
			 AND throold.THROOLD_Delete_Time is NULL";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while ($arrd = mysql_fetch_array($sqld)) {

	$tglterbit=date("d M Y", strtotime($arrd['TDROOLD_TglTerbit']));
	$tglberakhir=(($arrd['TDROOLD_TglBerakhir']=="0000-00-00 00:00:00")||($arrd['TDROOLD_TglBerakhir']=="1970-01-01 01:00:00"))?"-":date("d M Y", strtotime($arrd['TDROOLD_TglBerakhir']));

	if ($jumdata==8) {
		$style="style='page-break-after:always'";
		$jumdata=0;
	}
	else
	{
		$style="";
	}

    include ("./config/config_db_master.php");
    $query7="SELECT DocumentCategory_Name
        FROM db_master.M_DocumentCategory
        WHERE DocumentCategory_ID='$arrd[TDROOLD_KategoriDokumenID]'";
    $sql7 = mysql_query($query7);
    $nama_kategoridokumen = "-";
    if(mysql_num_rows($sql7) > 0){
        $data7 = mysql_fetch_array($sql7);
        $nama_kategoridokumen = $data7['DocumentCategory_Name'];
    }
    include ("./config/config_db.php");

echo ("
    <tr $style>
        <td class='center'>
            $arrd[core_company_name]
        </td>
    	<td class='center'>
 			$nama_kategoridokumen
        </td>
    	<td class='center'>
  			$arrd[TDROOLD_NamaDokumen]
       </td>
    	<td class='center'>
  			$arrd[TDROOLD_InstansiTerkait]
        </td>
    	<td class='center'>
			$arrd[TDROOLD_NoDokumen]
        </td>
    	<td class='center'>
  			$tglterbit
        </td>
    	<td class='center'>
  			$tglberakhir
        </td>
        <td class='center'>
            $arrd[TDROOLD_Keterangan]
        </td>
    </tr>

");
	$jumdata ++;
	$keterangan=$arrd['THROOLD_Information'];
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='8' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_COOKIE['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
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
