<?PHP 
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.1																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 29 Mei 2012																						=
= Update Terakhir	: 27 Sep 2012																						=
= Revisi			:																									=
=		26/09/2012	: Perubahan Query (LEFT JOIN) & Penambahan Header-Footer											=
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
<title>Pengeluaran Dokumen</title>
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
	$DocID=$_GET["id"];
	$query="SELECT DISTINCT throld.THROLD_ID,
							throld.THROLD_ReleaseCode,
							throld.THROLD_ReleaseDate,
							u.User_FullName AS Penerima,
							c.Company_Name,
							throld.THROLD_Status,
							throld.THROLD_Information,
							p.Position_Name AS PosPenerima,
							d.Division_Name AS DivPenerima,
							dp.Department_Name AS DeptPenerima,
							dg.DocumentGroup_Name,
							dg.DocumentGroup_ID,
							throld.THROLD_Reason,
							c.Company_ID,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV1 FROM M_User u1 WHERE u1.User_ID=u.User_ID)) AS Atasan,
							(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=throld.THROLD_UserID) AS CustodianStaff,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV1 FROM M_User u1 WHERE u1.User_ID=throld.THROLD_UserID)) AS AtasanCustodian1,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV2 FROM M_User u1 WHERE u1.User_ID=throld.THROLD_UserID)) AS AtasanCustodian2
			FROM TH_ReleaseOfLegalDocument throld
			LEFT JOIN TH_LoanOfLegalDocument thlold
				ON throld.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				AND thlold.THLOLD_Delete_Time IS NULL
			LEFT JOIN M_User u
				ON thlold.THLOLD_UserID=u.User_ID
			LEFT JOIN M_DivisionDepartmentPosition ddp
				ON u.User_ID=ddp.DDP_UserID
				AND ddp.DDP_Delete_Time is NULL
			LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			LEFT JOIN M_Company c
				ON thlold.THLOLD_CompanyID=c.Company_ID
			LEFT JOIN M_Approval dra
				ON dra.A_TransactionCode=throld.THROLD_ReleaseCode
			LEFT JOIN M_DocumentGroup dg
				ON thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
			WHERE throld.THROLD_ReleaseCode='$DocID'
			AND throld.THROLD_Delete_Time is NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
	$atasan=($arr['AtasanCustodian2'])?$arr['AtasanCustodian2']:$arr['AtasanCustodian1'];
	?>
	<div id='title'>Pengeluaran Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pengeluaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THROLD_ReleaseCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Pengeluaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THROLD_ReleaseCode]";?>" />
				<?PHP echo"$arr[THROLD_ReleaseCode]"; ?>
		   </td>
		</tr>
		<tr>
			<td>
				<b>Nama Penerima</b>
			</td>
			<td>
				<?PHP echo"$arr[Penerima]"; ?>
			</td>
			<td>
				<b>Jabatan</b>
			</td>
			<td>
				<?PHP echo"$arr[PosPenerima]"; ?>
			</td>
		</tr>
		<tr>
			<td>
				<b>Divisi</b>
			</td>
			<td>
				<?PHP echo"$arr[DivPenerima]"; ?>
			</td>
			<td>
				<b>Atasan</b>
			</td>
			<td>
				<?PHP echo"$arr[Atasan]"; ?>
			</td>
		</tr>
		<tr>
			<td>
				<b>Departemen</b>
			</td>
			<td>
				<?PHP echo"$arr[DeptPenerima]"; ?>
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
        	Kode Dokumen
        </th>
        <th>
        	Nama Dokumen
        </th>
       <th>
        	Tanggal Publikasi
        </th>
        <th>
        	Kategori Dokumen
        </th>
        <th>
        	Tanggal Permintaan
        </th>
        <th>
        	Ket 1
        </th>
        <th>
        	Ket 2
        </th>
        <th>
        	Ket 3
        </th>
        <th>
        	Jenis Permintaan
        </th>
		<th>
        	Keterangan Pengeluaran
        </th>
        <th>
        	Tanggal Pengembalian
        </th>
   	</tr>
</thead>
<tbody>
<?PHP
	$queryd = "SELECT tdrold.TDROLD_ID, tdlold.TDLOLD_ID, tdlold.TDLOLD_Code, tdlold.TDLOLD_DocCode,dt.DocumentType_Name,
			  		 dt.DocumentType_ID, c.Company_Name, dg.DocumentGroup_Name, dc.DocumentCategory_Name,
				     dl.DL_NoDoc, dl.DL_PubDate, dl.DL_ID,tdrold.TDROLD_Information, thlold.THLOLD_LoanDate,
					 di1.DocumentInformation1_Name, di2.DocumentInformation2_Name, dl.DL_Information3,
					 lc.LoanCategory_Name, tdrold.TDROLD_LeadTime,tdrold.TDROLD_Information
				FROM TD_ReleaseOfLegalDocument tdrold, M_DocumentType dt, TD_LoanOfLegalDocument tdlold,
					 M_DocumentLegal dl, M_Company c, M_DocumentGroup dg, M_DocumentCategory dc,
					 TH_LoanOfLegalDocument thlold, M_DocumentInformation1 di1, M_DocumentInformation2 di2,
					 M_LoanCategory lc,TH_ReleaseOfLegalDocument throld
				WHERE throld.THROLD_ReleaseCode='$DocID'
				AND tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
				AND tdrold.TDROLD_Delete_Time IS NULL
				AND tdlold.TDLOLD_DocCode=dl.DL_DocCode
				AND tdrold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				AND dl.DL_TypeDocID=dt.DocumentType_ID
				AND dl.DL_CompanyID=c.Company_ID
				AND dl.DL_GroupDocID=dg.DocumentGroup_ID
				AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
				AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
				AND dl.DL_Information1=di1.DocumentInformation1_ID
				AND dl.DL_Information2=di2.DocumentInformation2_ID
				AND thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while ($arrd = mysql_fetch_array($sqld)) {
		$fregdate=date('j M Y', strtotime($arrd[THLOLD_LoanDate]));
		$fretdate=(($arrd['TDROLD_LeadTime']=="0000-00-00 00:00:00")||($arrd['TDROLD_LeadTime']=="1970-01-01 01:00:00"))?"-":date('j M Y', strtotime($arrd[TDROLD_LeadTime]));
		$publik = date('j M Y', strtotime($arrd['DL_PubDate']));

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
			<td align='center'>
				$arrd[TDLOLD_DocCode]
			</td>
			<td align='center'>
				$arrd[DocumentType_Name] No $arrd[DL_NoDoc]
			</td>
			<td align='center'>
				$publik
			</td>
			<td align='center'>
				$arrd[DocumentCategory_Name]
			</td>
			<td align='center'>
				$fregdate
			</td>
			<td align='center'>
				$arrd[DocumentInformation1_Name]
			</td>
			<td align='center'>
				$arrd[DocumentInformation2_Name]
			</td>
			<td align='center'>
				$arrd[DL_Information3]
			</td>
			<td align='center'>
				$arrd[LoanCategory_Name]
			</td>
			<td align='center'>
				$arrd[TDROLD_Information]
			</td>
			<td align='center'>
				$fretdate
			 </td>
		</tr>");
		$jumdata ++;
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='11' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_COOKIE['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
	</tr>
</tfoot>
</table>
<table cellpadding='0' cellspacing='0'  width="100%" class="detail" style="border:none;">
<tr>
	<td><b><u>KETERANGAN TAMBAHAN:</u></b></td>
</tr>
<tr>
	<td><?PHP echo"$arr[THROLD_Information]";?></td>
</tr>
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
    	<td height='60px'>&nbsp;
        </td>
    	<td>&nbsp;
        </td>
    	<td>&nbsp;
        </td>
    </tr>
    <tr>
    	<td class='center'>
        	<?PHP echo"$arr[CustodianStaff]"; ?>
        </td>
    	<td class='center'>
        	<?PHP echo"$atasan"; ?>
        </td>
    	<td class='center'>
        	<?PHP echo"$arr[Penerima]"; ?>
        </td>
    </tr>
</table>
</div>
</body>
</html>
<?PHP } ?>
