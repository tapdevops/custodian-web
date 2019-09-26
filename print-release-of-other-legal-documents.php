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
	$query="SELECT DISTINCT throold.THROOLD_ID,
							throold.THROOLD_ReleaseCode,
							throold.THROOLD_ReleaseDate,
							u.User_FullName AS Penerima,
							c.Company_Name,
							throold.THROOLD_Status,
							throold.THROOLD_Information,
							p.Position_Name AS PosPenerima,
							d.Division_Name AS DivPenerima,
							dp.Department_Name AS DeptPenerima,
							throold.THROOLD_Reason,
							c.Company_ID,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV1 FROM M_User u1 WHERE u1.User_ID=u.User_ID)) AS Atasan,
							(SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=throold.THROOLD_UserID) AS CustodianStaff,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV1 FROM M_User u1 WHERE u1.User_ID=throold.THROOLD_UserID)) AS AtasanCustodian1,
							(SELECT u2.User_FullName FROM M_User u2 WHERE u2.User_ID=(SELECT u1.User_SPV2 FROM M_User u1 WHERE u1.User_ID=throold.THROOLD_UserID)) AS AtasanCustodian2
			FROM TH_ReleaseOfOtherLegalDocuments throold
			LEFT JOIN TH_LoanOfOtherLegalDocuments thloold
				ON throold.THROOLD_THLOOLD_Code=thloold.THLOOLD_LoanCode
				AND thloold.THLOOLD_Delete_Time IS NULL
			LEFT JOIN M_User u
				ON thloold.THLOOLD_UserID=u.User_ID
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
				ON thloold.THLOOLD_CompanyID=c.Company_ID
			LEFT JOIN M_Approval dra
				ON dra.A_TransactionCode=throold.THROOLD_ReleaseCode
			WHERE throold.THROOLD_ReleaseCode='$DocID'
			AND throold.THROOLD_Delete_Time is NULL";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['THROOLD_ReleaseDate']));
	$atasan=($arr['AtasanCustodian2'])?$arr['AtasanCustodian2']:$arr['AtasanCustodian1'];
	?>
	<div id='title'>Pengeluaran Dokumen Lainnya (Legal)</div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pengeluaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[THROOLD_ReleaseCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tanggal Pengeluaran</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[THROOLD_ReleaseCode]";?>" />
				<?PHP echo"$arr[THROOLD_ReleaseCode]"; ?>
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
        	Kategori Dokumen
        </th>
        <th>
        	Nama Dokumen
        </th>
        <th>
        	Instansi Terkait
        </th>
        <th>
        	Nomor Dokumen
        </th>
        <th>
        	Tanggal Terbit
        </th>
        <th>
        	Tanggal Berakhir
        </th>
        <th>
        	Tanggal Permintaan
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
	$queryd = "SELECT tdroold.TDROOLD_ID, tdloold.TDLOOLD_ID, tdloold.TDLOOLD_Code, tdloold.TDLOOLD_DocCode,
			  		 c.Company_Name, dol.DOL_ID,
                     dol.DOL_CategoryDocID, dc.DocumentCategory_Name kategori_dokumen,
                     dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
                     dol.DOL_TglTerbit, dol.DOL_TglBerakhir,
                     tdroold.TDROOLD_Information, thloold.THLOOLD_LoanDate,
					 lc.LoanCategory_Name, tdroold.TDROOLD_LeadTime,tdroold.TDROOLD_Information
				FROM TD_ReleaseOfOtherLegalDocuments tdroold, TD_LoanOfOtherLegalDocuments tdloold,
					 M_DocumentsOtherLegal dol, M_Company c,
					 TH_LoanOfOtherLegalDocuments thloold,
					 M_LoanCategory lc,TH_ReleaseOfOtherLegalDocuments throold,
                     db_master.M_DocumentCategory dc
				WHERE throold.THROOLD_ReleaseCode='$DocID'
				AND tdroold.TDROOLD_THROOLD_ID=throold.THROOLD_ID
				AND tdroold.TDROOLD_Delete_Time IS NULL
				AND tdloold.TDLOOLD_DocCode=dol.DOL_DocCode
				AND tdroold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
				AND dol.DOL_CompanyID=c.Company_ID
				AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
				AND thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
                AND dol.DOL_CategoryDocID=dc.DocumentCategory_ID";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while ($arrd = mysql_fetch_array($sqld)) {
        $style="";
        $fregdate=date('j M Y', strtotime($arrd['THLOOLD_LoanDate']));
        $fretdate=(($arrd['TDROOLD_LeadTime']=="0000-00-00 00:00:00")||($arrd['TDROOLD_LeadTime']=="1970-01-01 01:00:00"))?"-":date('j M Y', strtotime($arrd['TDROOLD_LeadTime']));
        $tgl_terbit=date("j M Y", strtotime($arrd['DOL_TglTerbit']));
        $tgl_berakhir=date("j M Y", strtotime($arrd['DOL_TglBerakhir']));

	echo ("
		<tr $style>
            <td align='center'>
                $arrd[TDLOOLD_DocCode]
            </td>
            <td align='center'>
                $arrd[kategori_dokumen]
            </td>
            <td align='center'>
                $arrd[DOL_NamaDokumen]
            </td>
            <td align='center'>
                $arrd[DOL_InstansiTerkait]
            </td>
            <td align='center'>
                $arrd[DOL_NoDokumen]
            </td>
            <td align='center'>
                $tgl_terbit
            </td>
            <td align='center'>
                $tgl_berakhir
            </td>
            <td align='center'>
                $fregdate
            </td>
            <td align='center'>
                $arrd[LoanCategory_Name]
            </td>
            <td align='center'>
                $arrd[TDROOLD_Information]
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
	<td><?PHP echo"$arr[THROOLD_Information]";?></td>
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
