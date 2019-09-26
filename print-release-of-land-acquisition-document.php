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
	$query="SELECT DISTINCT thrlolad.THRLOLAD_ID,
							thrlolad.THRLOLAD_ReleaseCode,
							thrlolad.THRLOLAD_ReleaseDate,
							u.User_FullName as Penerima,
							c.Company_Name,
							thrlolad.THRLOLAD_Status,
							thrlolad.THRLOLAD_Information,
							p.Position_Name AS PosPenerima,
							d.Division_Name AS DivPenerima,
							dp.Department_Name AS DeptPenerima,
							dg.DocumentGroup_Name,
							dg.DocumentGroup_ID,
							thrlolad.THRLOLAD_Reason,
							c.Company_ID,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV1 FROM M_User u1 WHERE u1.User_ID=u.User_ID)) AS Atasan,
							(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=thrlolad.THRLOLAD_UserID) AS CustodianStaff,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV1 FROM M_User u1 WHERE u1.User_ID=thrlolad.THRLOLAD_UserID)) AS AtasanCustodian1,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV2 FROM M_User u1 WHERE u1.User_ID=thrlolad.THRLOLAD_UserID)) AS AtasanCustodian2
			FROM TH_ReleaseOfLandAcquisitionDocument thrlolad
			LEFT JOIN TH_LoanOfLandAcquisitionDocument thlolad
				ON thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				AND thlolad.THLOLAD_Delete_Time IS NULL
			LEFT JOIN M_User u
				ON thlolad.THLOLAD_UserID=u.User_ID
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
				ON thlolad.THLOLAD_CompanyID=c.Company_ID
			LEFT JOIN M_Approval dra
				ON dra.A_TransactionCode=thrlolad.THRLOLAD_ReleaseCode
			LEFT JOIN M_DocumentGroup dg
				ON dg.DocumentGroup_ID='3'
			WHERE thrlolad.THRLOLAD_ReleaseCode='$DocID'
			AND thrlolad.THRLOLAD_Delete_Time is NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
	$atasan=($arr['AtasanCustodian2'])?$arr['AtasanCustodian2']:$arr['AtasanCustodian1'];
	?>
	<div id='title'>Pengeluaran Dokumen <?PHP echo"$arr[DocumentGroup_Name]"; ?></div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pengeluaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THRLOLAD_ReleaseCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Pengeluaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THRLOLAD_ReleaseCode]";?>" />
				<?PHP echo"$arr[THRLOLAD_ReleaseCode]"; ?>
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
    	<th width='10%'>
        	Kode Dokumen
        </th>
        <th width='10%'>
        	Perusahaan
        </th>
        <th width='5%'>
        	Tahap
        </th>
        <th width='10%'>
        	Periode
        </th>
        <th width='10%'>
        	Tanggal Dokumen
        </th>
        <th width='5%'>
        	Blok
        </th>
        <th width='5%'>
        	Desa
        </th>
        <th width='10%'>
        	Pemilik
        </th>
        <th width='10%'>
        	Jenis Permintaan
        </th>
        <th width='10%'>
        	Tanggal Permintaan
        </th>
        <th width='10%'>
        	Keterangan Pengeluaran
        </th>
        <th width='10%'>
        	Tanggal Pengembalian
        </th>
   	</tr>
<?PHP
	$queryd = "SELECT tdrlolad.TDRLOLAD_ID, tdlolad.TDLOLAD_ID, tdlolad.TDLOLAD_Code, thlolad.THLOLAD_LoanDate,
					  tdlolad.TDLOLAD_DocCode,c.Company_Name, dg.DocumentGroup_Name, dla.DLA_Phase, dla.DLA_Period,
					  dla.DLA_DocDate, dla.DLA_RegTime, dla.DLA_Block, dla.DLA_Village, dla.DLA_Owner,
					  lc.LoanCategory_Name, tdrlolad.TDRLOLAD_LeadTime, tdrlolad.TDRLOLAD_Information
			   FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
					 M_DocumentLandAcquisition dla, M_Company c, M_DocumentGroup dg,
					 TH_LoanOfLandAcquisitionDocument thlolad, M_LoanCategory lc,
					 TH_ReleaseOfLandAcquisitionDocument thrlolad
			   WHERE thrlolad.THRLOLAD_ReleaseCode='$DocID'
			   AND tdrlolad.TDRLOLAD_THRLOLAD_ID=thrlolad.THRLOLAD_ID
			   AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
			   AND tdlolad.TDLOLAD_DocCode=dla.DLA_Code
			   AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
			   AND dla.DLA_CompanyID=c.Company_ID
			   AND dg.DocumentGroup_ID='3'
			   AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
			   AND thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID";
	$sqld = mysql_query($queryd);
	$jumdata=0;

	while ($arrd = mysql_fetch_array($sqld)) {
		$fperdate=date("j M Y", strtotime($arrd['DLA_Period']));
		$fdocdate=date("j M Y", strtotime($arrd['DLA_DocDate']));
		$fregdate=date('j M Y', strtotime($arrd[THLOLAD_LoanDate]));
		$fretdate=(($arrd['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")||($arrd['TDRLOLAD_LeadTime']=="1970-01-01 01:00:00"))?"-":date('j M Y', strtotime($arrd[TDRLOLAD_LeadTime]));
		$publik = date('d/m/Y H:i:s', strtotime($arrd['DLA_RegTime']));
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
				$arrd[TDLOLAD_DocCode]
			</td>
			<td class='center'>
				$publik
			</td>
			<td class='center'>
				$arrd[DLA_Phase]
			</td>
			<td class='center'>
				$fperdate
			</td>
			<td class='center'>
				$fdocdate
			</td>
			<td class='center'>
				$arrd[DLA_Block]
			</td>
			<td class='center'>
				$arrd[DLA_Village]
			</td>
			<td class='center'>
				$arrd[DLA_Owner]
			</td>
			<td class='center'>
				$arrd[LoanCategory_Name]
			</td>
			<td class='center'>
				$fregdate
			</td>
			<td class='center'>
				$arrd[TDRLOLAD_Information]
			</td>
			<td class='center'>
				$fretdate
			</td>
		</tr>");
		$jumdata ++;
	}
?>
</tbody>
<tfoot>
	<tr>
		<td colspan='12' style='font-size:8px;font-weight:bolder;' align='right'><i>PRINTED BY CUSTODIAN SYSTEM VER <?PHP echo $_COOKIE['version']?> ON <?PHP echo date("d/m/Y H:i:s")?></i></td>
	</tr>
</tfoot>
</table>
<table cellpadding='0' cellspacing='0'  width="100%" class="detail" style="border:none;">
<tr>
	<td><b><u>KETERANGAN TAMBAHAN:</u></b></td>
</tr>
<tr>
	<td><?PHP echo"$arr[THRLOLAD_Information]";?></td>
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
