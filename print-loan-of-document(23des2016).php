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
=========================================================================================================================
*/
session_start(); 
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
	$query = "SELECT  thlold.THLOLD_ID, 
					  thlold.THLOLD_LoanCode, 
					  u.User_FullName, 
					  d.Division_Name, 
					  dp.Department_Name,
					  p.Position_Name, 
					  thlold.THLOLD_LoanDate, 
					  dg.DocumentGroup_Name,
					  thlold.THLOLD_Information,
					  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					  (SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=u.User_SPV2) AS Atasan2
			  FROM TH_LoanOfLegalDocument thlold
			  LEFT JOIN M_User u
				ON thlold.THLOLD_UserID=u.User_ID
			  LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			  LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			  LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			  LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			  LEFT JOIN M_DocumentGroup dg
				ON thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
			  WHERE thlold.THLOLD_LoanCode='$id'
			  AND thlold.THLOLD_Delete_Time IS NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THLOLD_LoanDate']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Permohonan Permintaan Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Permintaan</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THLOLD_LoanCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Permintaan</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THLOLD_LoanCode]";?>" /><br />
				<?PHP echo"$arr[THLOLD_LoanCode]"; ?>
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
    	<th width='10%'>
        	Kode Dokumen
        </th>
        <th width='30%'>
        	Nama Dokumen
        </th>
        <th width='10%'>
        	Perusahaan
        </th>
        <th width='10%'>
        	Grup Dokumen
        </th>
        <th width='10%'>
        	Kategori Dokumen
        </th>
        <th width='5%'>
        	Ket 1
        </th>
        <th width='5%'>
        	Ket 2
        </th>
        <th width='10%'>
        	Ket 3
        </th>
        <th width='10%'>
        	Ket Permintaan
        </th>
   	</tr>
</thead>
<tbody>
<?PHP
	$queryd="SELECT dl.DL_DocCode, 
					dt.DocumentType_Name, 
					c.Company_Name, 
					dg.DocumentGroup_Name, 
					dc.DocumentCategory_Name, 
					dl.DL_NoDoc, 
					dl.DL_ID,
					tdlold.TDLOLD_Information, 
					di1.DocumentInformation1_Name, 
					di2.DocumentInformation2_Name, 
					dl.DL_Information3
			 FROM TH_LoanOfLegalDocument thlold
			 LEFT JOIN TD_LoanOfLegalDocument tdlold
				ON tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
				AND tdlold.TDLOLD_Delete_Time IS NULL
			 LEFT JOIN M_DocumentLegal dl
				ON tdlold.TDLOLD_DocCode=dl.DL_DocCode
			 LEFT JOIN M_DocumentType dt
				ON dl.DL_TypeDocID=dt.DocumentType_ID 
			 LEFT JOIN M_Company c
				ON dl.DL_CompanyID=c.Company_ID
			 LEFT JOIN M_DocumentGroup dg
				ON dl.DL_GroupDocID=dg.DocumentGroup_ID
			 LEFT JOIN M_DocumentCategory dc
				ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
			 LEFT JOIN M_DocumentInformation1 di1
				ON dl.DL_Information1=di1.DocumentInformation1_ID
			 LEFT JOIN M_DocumentInformation2 di2
				ON dl.DL_Information2=di2.DocumentInformation2_ID
			 WHERE thlold.THLOLD_LoanCode='$id'
			 AND thlold.THLOLD_Delete_Time IS NULL";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while($arrd = mysql_fetch_array($sqld)) {
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
			$arrd[DL_DocCode]
        </td>
    	<td class='center'>
			$arrd[DocumentType_Name] No $arrd[DL_NoDoc]
        </td>
    	<td class='center'>
 			$arrd[Company_Name]
       </td>
    	<td class='center'>
  			$arrd[DocumentGroup_Name]
       </td>
    	<td class='center'>
  			$arrd[DocumentCategory_Name]
        </td>
    	<td class='center'>
  			$arrd[DocumentInformation1_Name]
        </td>
    	<td class='center'>
  			$arrd[DocumentInformation2_Name]
        </td>
    	<td class='center'>
  			$arrd[DL_Information3]
        </td>
    	<td class='center'>
  			$arrd[TDLOLD_Information]
        </td>
    </tr>");
	$jumdata ++;
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='9' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_SESSION['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
	</tr>
</tfoot>
</table>
<table cellpadding='0' cellspacing='0'  width="100%" class="detail" style="border:none;">
<tr>
	<td><b><u>KETERANGAN TAMBAHAN:</u></b></td>
</tr>  
<tr>
	<td><?PHP echo"$arr[THLOLD_Information]";?></td>
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
				AND a.Approver_ID=ra.RA_ID
				AND a.Approver_UserID=u.User_ID";
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