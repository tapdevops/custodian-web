<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Pengaturan Pemberi Persetujuan</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;

	var txtRA_Name = document.getElementById('txtRA_Name').value;

		if (txtRA_Name.replace(" ", "") == "") {
			alert("Nama Role Belum Ditentukan!");
			returnValue = false;
		}

	return returnValue;
}
</script>
</head>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
?>

<script type='text/javascript'>
	$(document).ready(function() {
	    var max_fields      = 10;
	    var wrapper         = $(".input_field");
	    var add_button      = $("#add_field");

	    $(add_button).click(function (e) {
	    	e.preventDefault();
	        var nextDoc = "<tr>\
	        	<td class='center'>\
	        		<select name='proses[]'>\
	        			<option value=''>-- Pilih --</option>\
	        			<option value='1'>Registrasi</option>\
	        			<option value='2'>Peminjaman</option>\
						<option value='3'>Pengeluaran</option>\
						<option value='4'>Pengembalian</option>\
	        		</select>\
	        	</td>\
				<td class='center'>\
					<select name='dokumen[]'>\
						<option value=''>-- Pilih --</option>\
						<option value='1'>Legal - Asli</option>\
						<option value='2'>Legal - Fotokopi</option>\
						<option value='3'>Lisensi - Asli</option>\
						<option value='4'>Lisensi - Fotokopi</option>\
						<option value='7'>Legal & Lisensi - Semua</option>\
						<option value='8'>Legal & Lisensi - Asli & Softcopy</option>\
						<option value='9'>Legal & Lisensi - Hardcopy</option>\
						<option value='23'>Legal & Lisensi - Softcopy</option>\
						<option value='5'>Pembebasan Lahan - Asli</option>\
						<option value='6'>Pembebasan Lahan - Fotokopi</option>\
						<option value='10'>Pembebasan Lahan - Semua</option>\
						<option value='11'>Pembebasan Lahan - Asli & Softcopy</option>\
						<option value='12'>Pembebasan Lahan - Hardcopy</option>\
						<option value='24'>Pembebasan Lahan - Softcopy</option>\
						<option value='13'>Kepemilikan Aset - Semua</option>\
						<option value='14'>Kepemilikan Aset - Asli</option>\
						<option value='15'>Kepemilikan Aset - Hardcopy</option>\
						<option value='25'>Kepemilikan Aset - Softcopy</option>\
						<option value='16'>Lainnya (Legal) - Semua</option>\
						<option value='17'>Lainnya (Legal) - Asli</option>\
						<option value='18'>Lainnya (Legal) - Hardcopy</option>\
						<option value='26'>Lainnya (Legal) - Softcopy</option>\
						<option value='19'>Lainnya (Di Luar Legal) - Semua</option>\
						<option value='20'>Lainnya (Di Luar Legal) - Asli</option>\
						<option value='21'>Lainnya (Di Luar Legal) - Hardcopy</option>\
						<option value='22'>Semua Dokumen</option>\
						<option value='27'>Semua Dokumen - Asli</option>\
						<option value='28'>Semua Dokumen - Hardcopy</option>\
						<option value='29'>Semua Dokumen - Softcopy</option>\
					</select>\
				</td>\
				<td class='center'>\
					<select name='step[]'>\
						<option value=''>-- Pilih --</option>\
						<option value='1'>1</option>\
						<option value='2'>2</option>\
						<option value='3'>3</option>\
						<option value='4'>4</option>\
						<option value='5'>5</option>\
						<option value='6'>6</option>\
						<option value='7'>7</option>\
					</select>\
				</td>\
				<td class='center'>\
					<select name='status[]'>\
						<option value=''>-- Pilih --</option>\
						<option value='1'>Approve</option>\
						<option value='2'>Notifikasi</option>\
					</select>\
				</td>\
				<td class='center'>\
					<a href='#' class='remove_dok'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a>\
				</td>\
			</tr>";

			$('#tblData tbody').append(nextDoc);
	    });

	    $('#tblData tbody').on('click', '.remove_dok', function(e) {
	    	var par = $(this).parent().parent();
	    	par.remove();
	    });
	});
</script>

<?php

$arrayProses = array('1' => 'Registrasi', '2' => 'Peminjaman', '3' => 'Pengeluaran', '4' => 'Pengembalian'); //Arief F - 11102018

$arrayDoc = array(
	'1' => 'Legal - Asli',
	'2' => 'Legal - Fotokopi',
	'3' => 'Lisensi - Asli',
	'4' => 'Lisensi - Fotokopi',
	'7' => 'Legal & Lisensi - Semua', //Arief F - 13092018
	'8' => 'Legal & Lisensi - Asli & Softcopy', //Arief F - 13092018
	'9' => 'Legal & Lisensi - Hardcopy', //Arief F - 13092018
	'23' => 'Legal & Lisensi - Softcopy', //Arief F - 24092018
	'5' => 'Pembebasan Lahan - Asli',
	'6' => 'Pembebasan Lahan - Fotokopi',
	'10' => 'Pembebasan Lahan - Semua', //Arief F - 13092018
	'11' => 'Pembebasan Lahan - Asli & Softcopy', //Arief F - 13092018
	'12' => 'Pembebasan Lahan - Hardcopy', //Arief F - 13092018
	'24' => 'Pembebasan Lahan - Softcopy', //Arief F - 13092018
	'13' => 'Kepemilikan Aset - Semua', //Arief F - 13092018
	'14' => 'Kepemilikan Aset - Asli', //Arief F - 13092018
	'15' => 'Kepemilikan Aset - Hardcopy', //Arief F - 13092018
	'25' => 'Kepemilikan Aset - Softcopy', //Arief F - 13092018
	'16' => 'Lainnya (Legal) - Semua', //Arief F - 13092018
	'17' => 'Lainnya (Legal) - Asli', //Arief F - 13092018
	'18' => 'Lainnya (Legal) - Hardcopy', //Arief F - 13092018
	'26' => 'Lainnya (Legal) - Softcopy', //Arief F - 13092018
	'19' => 'Lainnya (Di Luar Legal) - Semua', //Arief F - 13092018
	'20' => 'Lainnya (Di Luar Legal) - Asli', //Arief F - 13092018
	'21' => 'Lainnya (Di Luar Legal) - Hardcopy', //Arief F - 13092018,
	'22' => 'Semua Dokumen',//Arief F - 11102018
	'27' => 'Semua Dokumen - Asli',//Arief F - 11102018
	'28' => 'Semua Dokumen - Hardcopy',//Arief F - 11102018
	'29' => 'Semua Dokumen - Softcopy',//Arief F - 11102018
);

$arrayStep = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
$arrayAprv = array('1' => 'Approve', '2' => 'Notifikasi');

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	if($act=='add') {
$ActionContent ="
	<form name='add-role' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Peran Baru</th>
	<tr>
		<td width='30'>Nama Peran</td>
		<td width='70%'><input name='txtRA_Name' id='txtRA_Name' type='text' /></td>
	</tr>
	</table>
	<table width='100%' border='1' id='tblData' class='stripeMe'>
		<thead>
		<tr>
			<th>Proses</th>
			<th>Dokumen</th>
			<th>Step ke</th>
			<th>Approval</th>
			<th></th>
		</tr>
		</thead>
		<tbody>";

	$ActionContent .= '<tr>';
	$ActionContent .= "<td class='center'><select name='proses[]'><option value=''>-- Pilih --</option>";
	foreach ($arrayProses as $kProses => $vProses) {
		$ActionContent .= "<option value='$kProses'>$vProses</option>";
	}
	$ActionContent .= '</select></td>';

	$ActionContent .= "<td class='center'><select name='dokumen[]'><option value=''>-- Pilih --</option>";
	foreach ($arrayDoc as $kDoc => $vDoc) {
		$ActionContent .= "<option value='$kDoc'>$vDoc</option>";
	}
	$ActionContent .= '</select></td>';

	$ActionContent .= "<td class='center'><select name='step[]'><option value=''>-- Pilih --</option>";
	foreach ($arrayStep as $kStep => $vStep) {
		$ActionContent .= "<option value='$kStep'>$vStep</option>";
	}
	$ActionContent .= '</select></td>';

	$ActionContent .= "<td class='center'><select name='status[]'><option value=''>-- Pilih --</option>";
	foreach ($arrayAprv as $kAprv => $vAprv) {
		$ActionContent .= "<option value='$kAprv'>$vAprv</option>";
	}
	$ActionContent .= '</select></td>';

	$ActionContent .= ($i == 0) ? "<td class='center'><a href='#' id='add_field'><img title='Tambah' src='./images/icon_addrow.png' width='20'></a></td>" : "<td class='center'><a href='#' class='remove_dok'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a></td>";

	$ActionContent .= '</tr>';

$ActionContent .= "
	</tbody>
	</table>
	<table width='100%' border='1'>
	<th colspan=4>
		<input name='add' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</tr>
	</table>
	</form>
";
	}

	elseif($act=='edit')	{
	$RA_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_Role_Approver ra
				LEFT JOIN M_Role_ApproverDocStepStatus rads
				ON ra.RA_ID = rads.RADS_RA_ID
				WHERE ra.RA_ID='$RA_ID'
				AND ra.RA_Delete_Time is NULL ORDER BY ra.RA_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);
	$sql1 = mysql_query($query);

$ActionContent ="
	<form name='edit-role' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Peran</th>
	<tr>
		<td width='30'>ID Peran</td>
		<td width='70%'>
			<input name='txtRA_ID' type='text' value='$field[RA_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Peran</td>
		<td><input name='txtRA_Name' id='txtRA_Name' type='text' value='$field[RA_Name]'/></td>
	</tr>
	</table>
	<table width='100%' border='1' id='tblData' class='stripeMe'>
		<thead>
		<tr>
			<th>Proses</th>
			<th>Dokumen</th>
			<th>Step ke</th>
			<th>Approval</th>
			<th></th>
		</tr>
		</thead>
		<tbody>";

		$i = 0;
		while ($row = mysql_fetch_array($sql1)) {
			$ActionContent .= '<tr>';

			$ActionContent .= "<td class='center'><select name='proses[]'><option value=''>-- Pilih --</option>";
			foreach ($arrayProses as $kProses => $vProses) {
				$selected = ($row[RADS_ProsesID] == $kProses) ? ' selected="selected"' : '';
				$ActionContent .= "<option value='$kProses'$selected>$vProses</option>";
			}
			$ActionContent .= '</select></td>';

			$ActionContent .= "<td class='center'><select name='dokumen[]'><option value=''>-- Pilih --</option>";
			foreach ($arrayDoc as $kDoc => $vDoc) {
				$selected = ($row[RADS_DocID] == $kDoc) ? ' selected="selected"' : '';
				$ActionContent .= "<option value='$kDoc'$selected>$vDoc</option>";
			}
			$ActionContent .= '</select></td>';

			$ActionContent .= "<td class='center'><select name='step[]'><option value=''>-- Pilih --</option>";
			foreach ($arrayStep as $kStep => $vStep) {
				$selected = ($row[RADS_StepID] == $kStep) ? ' selected="selected"' : '';
				$ActionContent .= "<option value='$kStep'$selected>$vStep</option>";
			}
			$ActionContent .= '</select></td>';

			$ActionContent .= "<td class='center'><select name='status[]'><option value=''>-- Pilih --</option>";
			foreach ($arrayAprv as $kAprv => $vAprv) {
				$selected = ($row[RADS_StatusID] == $kAprv) ? ' selected="selected"' : '';
				$ActionContent .= "<option value='$kAprv'$selected>$vAprv</option>";
			}
			$ActionContent .= '</select></td>';

			$ActionContent .= ($i == 0) ? "<td class='center'><a href='#' id='add_field'><img title='Tambah' src='./images/icon_addrow.png' width='20'></a></td>" : "<td class='center'><a href='#' class='remove_dok'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a></td>";

			$ActionContent .= '</tr>';

			$i++;
		}

$ActionContent .= "
	</tbody>
	</table>
	<table width='100%' border='1'>
	<th colspan=4>
		<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
	</form>
";
	}

	elseif($act=='delete')	{
	$RA_ID=$_GET["id"];

	$query = "SELECT *
				FROM M_Role_Approver
				WHERE RA_ID='$RA_ID'
				AND RA_Delete_Time is NULL
				ORDER BY RA_ID ";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='delete-role' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Peran Berikut?</th>
	<tr>
		<td width='30'>ID Peran</td>
		<td width='70%'>
			<input name='txtRA_ID' type='text' value='$field[RA_ID]' readonly='true' class='readonly'/>
		</td>
	</tr>
	<tr>
		<td>Nama Peran</td>
		<td><input name='txtRA_Name' type='text' value='$field[RA_Name]' readonly='true' class='readonly'/></td>
	</tr>
	<th colspan=3>
		<input name='delete' type='submit' value='Ya' class='button'/>
		<input name='cancel' type='submit' value='Tidak' class='button'/>
	</th>
	</table>
	</form>
";
	}
}

$dataPerPage = 20;
if(isset($_GET['page'])){
    $noPage = $_GET['page'];
}
else $noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT *
			FROM M_Role_Approver
			WHERE RA_Delete_Time is NULL
			ORDER BY RA_ID
			LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

if ($num==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Peran</th>
		<th>Nama Peran</th>
	</tr>
	<tr>
		<td colspan=2 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num<>NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>ID Peran</th>
		<th>Nama Peran</th>
		<th></th>
	</tr>
";

while ($field = mysql_fetch_array($sql)){
$MainContent .="
	<tr>
		<td class='center'>$field[RA_ID]</td>
		<td class='center'>$field[RA_Name]</td>
		<td class='center'>
			<b><a href='$PHP_SELF?act=edit&id=$field[0]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
			<a href='$PHP_SELF?act=delete&id=$field[0]'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a></b>
		</td>
	</tr>
";
 }
}
$MainContent .="
	</table>
";

$query1 = "SELECT *
			FROM M_Role_Approver
			WHERE RA_Delete_Time is NULL";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1) $Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++){
         if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
            if (($showPage == 1) && ($p != 2))  $Pager.="...";
            if (($showPage != ($jumPage - 1)) && ($p == $jumPage))  $Pager.="...";
            if ($p == $noPage) $Pager.="<b><u>$p</b></u> ";
            else $Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";
            $showPage = $p;
         }
}
if ($noPage < $jumPage) $Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
}

elseif(isset($_POST[add])) {
	$sql= "INSERT INTO M_Role_Approver
				VALUES (NULL,'$_POST[txtRA_Name]','$mv_UserID', sysdate(),'$mv_UserID',
						sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		$last_id = $mysqli->insert_id;

		if (isset($_POST['dokumen']) && isset($_POST['step']) && isset($_POST['status']) && !empty($_POST['dokumen']) && !empty($_POST['step']) && !empty($_POST['status'])) {
			$sql1 = "INSERT INTO M_Role_ApproverDocStepStatus VALUES ";
			foreach ($_POST['dokumen'] as $k=>$v) {
				$sql1 .= "(NULL, '$last_id', '{$_POST['proses'][$k]}', '$v', '{$_POST['step'][$k]}', '{$_POST['status'][$k]}', '$mv_UserID', sysdate(), '$mv_UserID', sysdate(), NULL, NULL), ";

			}

			$sql1 = substr($sql1, 0, -2);
			if ($mysqli->query($sql1)) {
				echo "<meta http-equiv='refresh' content=0'; url=role-approver.php'>";
			}
		}

		echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[edit])) {
	//echo '<pre>'; print_r ($_POST); echo '</pre>'; die;
	$sql= "UPDATE M_Role_Approver
				SET RA_Name='$_POST[txtRA_Name]', RA_Update_UserID='$mv_UserID', RA_Update_Time=sysdate()
				WHERE RA_ID='$_POST[txtRA_ID]'";
	if($mysqli->query($sql)) {
		$q1 = mysql_fetch_array(mysql_query("SELECT COUNT(*) as jumlah FROM M_Role_ApproverDocStepStatus WHERE RADS_RA_ID = '{$_POST[txtRA_ID]}'"));

		if ($q1['jumlah'] > 0) {
			$q2 = mysql_query("DELETE FROM M_Role_ApproverDocStepStatus WHERE RADS_RA_ID = '{$_POST[txtRA_ID]}'");
		}
		$sql1 = "INSERT INTO M_Role_ApproverDocStepStatus VALUES ";
		foreach ($_POST['dokumen'] as $k=>$v) {
			$sql1 .= "(NULL, '$_POST[txtRA_ID]', '{$_POST['proses'][$k]}', '$v', '{$_POST['step'][$k]}', '{$_POST['status'][$k]}', '$mv_UserID', sysdate(), '$mv_UserID', sysdate(), NULL, NULL), ";
		}

		$sql1 = substr($sql1, 0, -2);
		if ($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content=0'; url=role-approver.php'>";
		}
		echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_Role_Approver
				SET RA_Delete_UserID='$mv_UserID', RA_Delete_Time=sysdate()
				WHERE RA_ID='$_POST[txtRA_ID]'";
	if($mysqli->query($sql)) {
		$sql1 = "UPDATE M_Role_ApproverDocStepStatus SET RADS_Delete_UserID='$mv_UserID', RADS_Delete_Time=sysdate() WHERE RADS_RA_ID='$_POST[txtRA_ID]'";
		if ($mysqli->query($sql)) {
			echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
		}
		echo "<meta http-equiv='refresh' content='0; url=role-approver.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penghapusan Data Gagal.</div>";
	}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
