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
	$query = "SELECT  tdroold.TDRTOOLD_ReturnCode,
					  u.User_FullName,
					  d.Division_Name,
					  dp.Department_Name,
					  p.Position_Name,
					  tdroold.TDRTOOLD_ReturnTime,
					  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV1) AS Atasan1,
					  (SELECT u1.User_FullName FROM M_User u1 WHERE u1.User_ID=u.User_SPV2) AS Atasan2
			  FROM TD_ReturnOfOtherLegalDocuments tdroold
			  LEFT JOIN M_User u
				ON tdroold.TDRTOOLD_UserID=u.User_ID
			  LEFT JOIN M_DivisionDepartmentPosition ddp
				ON ddp.DDP_UserID=u.User_ID
			  LEFT JOIN M_Division d
				ON ddp.DDP_DivID=d.Division_ID
			  LEFT JOIN M_Department dp
				ON ddp.DDP_DeptID=dp.Department_ID
			  LEFT JOIN M_Position p
				ON ddp.DDP_PosID=p.Position_ID
			  LEFT JOIN M_DocumentsOtherLegal dol
				ON tdroold.TDRTOOLD_DocCode=dol.DOL_DocCode
			  WHERE tdroold.TDRTOOLD_ReturnCode='$id'";
	$sql = mysql_query($query);
	$arr = mysql_fetch_array($sql);

	$fregdate=date("j M Y", strtotime($arr['TDRTOOLD_ReturnTime']));
	$atasan=($arr['Atasan2'])?$arr['Atasan2']:$arr['Atasan1'];
	?>
	<div id='title'>Pengembalian Dokumen Lainnya (Legal)</div>
	<table width='100%'>
		<tr>
			<td width='15%'>
				<b>No Pengembalian</b>
			</td>
			<td width='25%'>
				<?PHP echo"$arr[TDRTOOLD_ReturnCode]"; ?>
			</td>
			<td width='15%'>
				<b>Tgl Pengembalian</b>
			</td>
			<td width='25%'>
				<?PHP echo"$fregdate"; ?>
			</td>
			<td rowspan='4' align='center'>
				<img src="<?PHP echo "barcode.php?text=$arr[TDRTOOLD_ReturnCode]";?>" /><br />
				<?PHP echo"$arr[TDRTOOLD_ReturnCode]"; ?>
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
        <th>Kode Dokumen</th>
        <th>Perusahaan</th>
        <th>Kategori Dokumen</th>
        <th>Nama Dokumen</th>
        <th>Instansi Terkait</th>
        <th>No. Dokumen</th>
        <th>Tgl. Terbit</th>
        <th>Tgl. Berakhir</th>
        <th>Ket. Pengembalian</th>
    </tr>
</thead>
<tbody>
<?PHP
	$queryd="SELECT dol.DOL_DocCode, c.Company_Name, dg.DocumentGroup_Name,
                    dc.DocumentCategory_Name,
                    dol.DOL_NamaDokumen, dol.DOL_InstansiTerkait, dol.DOL_NoDokumen,
                    dol.DOL_TglTerbit, dol.DOL_TglBerakhir,
                    tdroold.TDRTOOLD_Information
			 FROM TD_ReturnOfOtherLegalDocuments tdroold,
			 	  M_DocumentsOtherLegal dol, M_Company c, M_DocumentGroup dg,
                  db_master.M_DocumentCategory dc
			 WHERE tdroold.TDRTOOLD_ReturnCode='$id'
    			 AND tdroold.TDRTOOLD_Delete_Time IS NULL
    			 AND tdroold.TDRTOOLD_DocCode=dol.DOL_DocCode
    			 AND dol.DOL_CompanyID=c.Company_ID
    			 AND dol.DOL_GroupDocID=dg.DocumentGroup_ID
                 AND dol.DOL_CategoryDocID=dc.DocumentCategory_ID";
	$sqld = mysql_query($queryd);
	$jumdata=0;
	while ($arrd = mysql_fetch_array($sqld)) {
        $tgl_terbit=date("j M Y", strtotime($arrd['DOL_TglTerbit']));
        $tgl_berakhir=date("j M Y", strtotime($arrd['DOL_TglBerakhir']));

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
				$arrd[DOL_DocCode]
			</td>
			<td class='center'>
				$arrd[Company_Name]
		    </td>
            <td class='center'>$arrd[DocumentCategory_Name]</td>
            <td class='center'>$arrd[DOL_NamaDokumen]</td>
            <td class='center'>$arrd[DOL_InstansiTerkait]</td>
            <td class='center'>$arrd[DOL_NoDokumen]</td>
            <td class='center'>$tgl_terbit</td>
            <td class='center'>$tgl_berakhir</td>
			<td class='center'>
				$arrd[TDRTOOLD_Information]
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
				FROM TD_ReturnOfOtherLegalDocuments tdrtroold, M_User u, TD_LoanOfOtherLegalDocuments tdloold,
					 TH_LoanOfOtherLegalDocuments thloold
				WHERE tdloold.TDLOOLD_DocCode=tdrtroold.TDRTOOLD_DocCode
				AND tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
				AND thloold.THLOOLD_UserID=u.User_ID
				AND tdrtroold.TDRTOOLD_ReturnCode='$id'
				ORDER BY tdloold.TDLOOLD_Insert_Time DESC";
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
