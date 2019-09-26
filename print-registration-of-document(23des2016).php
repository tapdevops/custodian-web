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
	$query="SELECT  throld.THROLD_ID, 
					throld.THROLD_RegistrationCode, 
					u.User_FullName, 
					d.Division_Name, 
					dp.Department_Name,
					p.Position_Name, 
					throld.THROLD_RegistrationDate, 
					dg.DocumentGroup_Name,
					(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=u.User_SPV2) AS Atasan2
			FROM TH_RegistrationOfLegalDocument throld
			LEFT JOIN M_User u
				ON throld.THROLD_UserID=u.User_ID 
			LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			LEFT JOIN M_DocumentGroup dg
				ON throld.THROLD_DocumentGroupID=dg.DocumentGroup_ID
			WHERE throld.THROLD_RegistrationCode='$id'
			AND throld.THROLD_Delete_Time is NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THROLD_RegistrationDate']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Pendaftaran Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THROLD_RegistrationCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Pendaftaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THROLD_RegistrationCode]";?>" /><br />
				<?PHP echo"$arr[THROLD_RegistrationCode]"; ?>
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
        <th>
        	Perusahaan
        </th>
        <th>
        	Grup Dokumen
        </th>
        <th>
        	Kategori Dokumen
        </th>
        <th>
        	Tipe Dokumen
        </th>
        <th>
        	No Dokumen
        </th>
        <th>
        	Instansi Terkait
        </th>
        <th>
        	Tanggal Berlaku
        </th>
        <th>
        	Tanggal Habis Berlaku
        </th>
        <th>
        	Keterangan 1
        </th>
        <th>
        	Keterangan 2
        </th>
        <th>
        	Keterangan 3
        </th>
   	</tr>
</thead>
<tbody>
<?PHP
	$queryd="SELECT dt.DocumentType_Name, 
					c.Company_Name, 
					dg.DocumentGroup_Name,
					throld.THROLD_Information, 
					dc.DocumentCategory_Name, 
					tdrold.TDROLD_DocumentNo, 
					tdrold.TDROLD_Instance,
					tdrold.TDROLD_DatePublication, 
					tdrold.TDROLD_DateExpired,
					di1.DocumentInformation1_Name, 
					di2.DocumentInformation2_Name, 
					tdrold.TDROLD_DocumentInformation3
			 FROM TH_RegistrationOfLegalDocument throld
			 LEFT JOIN TD_RegistrationOfLegalDocument tdrold
				ON tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
			 LEFT JOIN M_DocumentType dt
				ON tdrold.TDROLD_DocumentTypeID=dt.DocumentType_ID
			 LEFT JOIN M_Company c
				ON throld.THROLD_CompanyID=c.Company_ID
			 LEFT JOIN M_DocumentGroup dg
				ON throld.THROLD_DocumentGroupID=dg.DocumentGroup_ID
			 LEFT JOIN M_DocumentCategory dc
				ON tdrold.TDROLD_DocumentCategoryID=dc.DocumentCategory_ID
			 LEFT JOIN M_DocumentInformation1 di1
				ON tdrold.TDROLD_DocumentInformation1ID=di1.DocumentInformation1_ID
			 LEFT JOIN M_DocumentInformation2 di2
				ON tdrold.TDROLD_DocumentInformation2ID=di2.DocumentInformation2_ID
			 WHERE throld.THROLD_RegistrationCode='$id'
			 AND throld.THROLD_Delete_Time is NULL";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while ($arrd = mysql_fetch_array($sqld)) {
	
	$pubdate=date("d M Y", strtotime($arrd[TDROLD_DatePublication]));
	$expdate=(($arrd[TDROLD_DateExpired]=="0000-00-00 00:00:00")||($arrd[TDROLD_DateExpired]=="1970-01-01 01:00:00"))?"-":date("d M Y", strtotime($arrd[TDROLD_DateExpired]));
	/*
	if ($jumdata==8) {
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
 			$arrd[Company_Name]
       </td>
    	<td class='center'>
  			$arrd[DocumentGroup_Name]
       </td>
    	<td class='center'>
  			$arrd[DocumentCategory_Name]
        </td>
      	<td class='center'>
			$arrd[DocumentType_Name]
        </td>
    	<td class='center'>
			$arrd[TDROLD_DocumentNo]
        </td>
    	<td class='center'>
  			$arrd[TDROLD_Instance]
        </td>
    	<td class='center'>
  			$pubdate
        </td>
    	<td class='center'>
  			$expdate
        </td>
	  	<td class='center'>
  			$arrd[DocumentInformation1_Name]
        </td>
    	<td class='center'>
  			$arrd[DocumentInformation2_Name]
        </td>
    	<td class='center'>
  			$arrd[TDROLD_DocumentInformation3]
        </td>
    </tr>
	
");
	$jumdata ++;
	$keterangan=$arrd[THROLD_Information];
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='11' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_SESSION['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
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
<table cellpadding='0' cellspacing='0'  width='80%' border='1' align='left'>
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
    	<td height='60px'>&nbsp;
        </td>
    	<td>&nbsp;
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
				AND a.Approver_Delete_Time is null"; //edited by NB 21.11.2014 yang sblmnya compare antara roleID di table approver dgn ID di M_ROLE_APPROVER menjadi roleID di M_APPROVER dengan M_ROLE_APPROVER
	$sqlcm = mysql_query($querycm);
	$arrcm=mysql_fetch_array($sqlcm);
?>
    	<td class='center'>
        	<?PHP echo"$atasan"; ?>
        </td>
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