<?php
	include ("./config/config_db.php");

if($_REQUEST)
{
	$CompanyAreaID = $_REQUEST['CompanyAreaID'];
    if($CompanyAreaID == "" || $CompanyAreaID == "0" || $CompanyAreaID == "1" || $CompanyAreaID == "3" || $CompanyAreaID == "4" ){
        $query_additional = "";
        if($CompanyAreaID != ""){ $query_additional = "AND Company_ID_Area = '$CompanyAreaID'"; }
        $query = "SELECT Company_Name, Company_ID
					FROM M_Company
					WHERE Company_Delete_Time is NULL
					$query_additional
					ORDER BY Company_Name";
    }else{
        $query = "";
    }

	$sql = mysql_query($query);
	$num=mysql_num_rows($query);
	if ($num=="0"){?>
	<option value="ALL">--- Tidak Ada Daftar Perusahaan ---</option>
<?PHP
	}else{ ?>
		<option value="ALL">--- Pilih Perusahaan ---</option>
		<?php
			if(!empty($_REQUEST['ReportOfReg']) && $CompanyAreaID == ''){
				if($_REQUEST['ReportOfReg'] == 'true'){ ?>
					<option value='88'>National</option>
				<?php }
			}
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['Company_ID'];?>" style="width:500px"><?php echo $arr['Company_Name'];?></option>
		<?php
		}
	}?>

<?php
}
?>
