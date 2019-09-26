<?php
	include ("./config/config_db.php");

if($_REQUEST)
{
	$GroupDocID = $_REQUEST['GroupDocID'];
    $CompanyID = $_REQUEST['CompanyID'];
    $where = "";
    if(!empty($CompanyID)){
        $where = "WHERE DONL_CompanyID='$CompanyID'";
    }
    $query = "SELECT DISTINCT DONL_TahunDokumen FROM M_DocumentsOtherNonLegal ".$where;

	$sql = mysql_query($query);
	$num=mysql_num_rows($query);
	if ($num=="0"){?>
	<option value="-1">--- Tidak Ada Tahun Dokumen ---</option>
<?PHP
	}
	else{?>
	<option value="-1">--- Pilih Tahun Dokumen ---</option>
	<?php
	while ($arr = mysql_fetch_array($sql)) {?>
		<option value="<?php echo $arr['DONL_TahunDokumen'];?>" style="width:500px"><?php echo $arr['DONL_TahunDokumen'];?></option>
	<?php
	}
	}?>

<?php
}
?>
