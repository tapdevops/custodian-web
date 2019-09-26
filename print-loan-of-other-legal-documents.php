<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.1																								=
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
<title>Permintaan Dokumen</title>
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
	$query = "SELECT  thloold.THLOOLD_ID,
					  thloold.THLOOLD_LoanCode,
					  u.User_FullName,
					  d.Division_Name,
					  dp.Department_Name,
					  p.Position_Name,
					  thloold.THLOOLD_LoanDate,
					  dg.DocumentGroup_Name,
					  thloold.THLOOLD_Information,
					  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					  (SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=u.User_SPV2) AS Atasan2
			  FROM TH_LoanOfOtherLegalDocuments thloold
			  LEFT JOIN M_User u
				ON thloold.THLOOLD_UserID=u.User_ID
			  LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			  LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			  LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			  LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			  LEFT JOIN M_DocumentGroup dg
				ON dg.DocumentGroup_ID='5'
			  WHERE thloold.THLOOLD_LoanCode='$id'
			  AND thloold.THLOOLD_Delete_Time IS NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THLOOLD_LoanDate']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Permohonan Permintaan Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Permintaan</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THLOOLD_LoanCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Permintaan</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THLOOLD_LoanCode]";?>" /><br />
				<?PHP echo"$arr[THLOOLD_LoanCode]"; ?>
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
<table width='100%' border='1' class='fixed' cellpadding='0' cellspacing='0'>
<thead>
    <tr>
        <th width='10%'>Kode Dokumen</th>
        <th width='10%'>Perusahaan</th>
        <th width='10%'>Kategori Dokumen</th>
        <th width='10%'>Nama Dokumen</th>
        <th width='10%'>Instansi Terkait</th>
        <th width='10%'>No. Dokumen</th>
        <th width='10%'>Tanggal Terbit</th>
        <th width='10%'>Tanggal Berakhir</th>
        <th width='20%'>Ket Permintaan</th>
    </tr>
</thead>
<tbody>
<?PHP
	$queryd="SELECT dol.DOL_DocCode,
    				c.Company_Name,
					dg.DocumentGroup_Name,
					dc.DocumentCategory_Name,
					dol.DOL_ID,
                    dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait,
   				    dol.DOL_NoDokumen, dol.DOL_TglTerbit, dol.DOL_TglBerakhir,
					tdloold.TDLOOLD_Information
			 FROM TH_LoanOfOtherLegalDocuments thloold
			 LEFT JOIN TD_LoanOfOtherLegalDocuments tdloold
				ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
				AND tdloold.TDLOOLD_Delete_Time IS NULL
			 LEFT JOIN M_DocumentsOtherLegal dol
				ON tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
			 LEFT JOIN M_Company c
				ON dol.DOL_CompanyID=c.Company_ID
			 LEFT JOIN M_DocumentGroup dg
				ON dol.DOL_GroupDocID=dg.DocumentGroup_ID
                LEFT JOIN db_master.M_DocumentCategory dc
   				ON dc.DocumentCategory_ID=dol.DOL_CategoryDocId
			 WHERE thloold.THLOOLD_LoanCode='$id'
			 AND thloold.THLOOLD_Delete_Time IS NULL";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while($arrd = mysql_fetch_array($sqld)) {
    $style="";
    $tgl_terbit=date("j M Y", strtotime($arrd['DOL_TglTerbit']));
	$tgl_berakhir=date("j M Y", strtotime($arrd['DOL_TglBerakhir']));
	/*
	if ($jumdata==1) {
		$style="style='page-break-after:always'";
		$jumdata=0;
	}
	else
	{
		$style="";
	}*/

	echo ("
    <tr $style>
    	<td class='center'>
			$arrd[DOL_DocCode]
        </td>
    	<td class='center'>
 			$arrd[Company_Name]
        </td>
        <td class='center'>
			$arrd[DocumentCategory_Name]
        </td>
        <td class='center'>
			$arrd[DOL_NamaDokumen]
        </td>
        <td class='center'>
			$arrd[DOL_InstansiTerkait]
        </td>
    	<td class='center'>
  			$arrd[DOL_NoDokumen]
        </td>
    	<td class='center'>
  			$tgl_terbit
        </td>
        <td class='center'>
  			$tgl_berakhir
        </td>
    	<td class='center'>
  			$arrd[TDLOOLD_Information]
        </td>
    </tr>");
	$jumdata ++;
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='18' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_COOKIE['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
	</tr>
</tfoot>
</table>
<table cellpadding='0' cellspacing='0'  width="100%" class="detail" style="border:none;">
<tr>
	<td><b><u>KETERANGAN TAMBAHAN:</u></b></td>
</tr>
<tr>
	<td><?PHP echo"$arr[THLOOLD_Information]";?></td>
</tr>
</table>
<table width='60%' border='1' align='left' cellpadding='0' cellspacing='0'>
	<tr>
    	<td width='20%' class='center'>
        	Dipinjam Oleh
        </td>
    	<td width='20%' class='center'>
        	Disetujui Oleh
        </td>
    	<td width='20%' class='center'>
        	Diketahui Oleh
        </td>
    </tr>
    <tr>
    	<td height='60px'>&nbsp;
        </td>
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
    	<td class='center'>
        	<?PHP echo"$atasan"; ?>
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
