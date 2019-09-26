<?php
	include ("./config/config_db.php");

if($_REQUEST)
{
	$GroupDocID = $_REQUEST['GroupDocID'];
    if($GroupDocID == "1" or $GroupDocID == "2"){
        $query = "SELECT DISTINCT DL_CompanyID as Company_ID, Company_Name FROM M_DocumentLegal
            LEFT JOIN M_Company ON DL_CompanyID=Company_ID
			WHERE DL_GroupDocID='$GroupDocID'";
    }elseif($GroupDocID == "3"){
        $query = "SELECT DISTINCT DLA_CompanyID as Company_ID, Company_Name FROM M_DocumentLandAcquisition
            LEFT JOIN M_Company ON DLA_CompanyID=Company_ID";
    }elseif($GroupDocID == "4"){
        $query = "SELECT DISTINCT DAO_CompanyID as Company_ID, Company_Name FROM M_DocumentAssetOwnership
            LEFT JOIN M_Company ON DAO_CompanyID=Company_ID";
    }elseif($GroupDocID == "5"){
        $query = "SELECT DISTINCT DOL_CompanyID as Company_ID, Company_Name FROM M_DocumentsOtherLegal
            LEFT JOIN M_Company ON DOL_CompanyID=Company_ID";
    }elseif($GroupDocID == "6"){
        $query = "SELECT DISTINCT DONL_CompanyID as Company_ID, Company_Name FROM M_DocumentsOtherNonLegal
            LEFT JOIN M_Company ON DONL_CompanyID=Company_ID";
    }else{
        $query = "";
    }

	$sql = mysql_query($query);
	$num=mysql_num_rows($query);
	if ($num=="0"){?>
	<option value="-1">--- Tidak Ada Daftar Perusahaan ---</option>
<?PHP
	}
	else{?>
	<option value="-1">--- Pilih Perusahaan ---</option>
	<?php
	while ($arr = mysql_fetch_array($sql)) {?>
		<option value="<?php echo $arr['Company_ID'];?>" style="width:500px"><?php echo $arr['Company_Name'];?></option>
	<?php
	}
	}?>

<?php
}
?>
