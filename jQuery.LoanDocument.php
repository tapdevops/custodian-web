<?php
	include ("./config/config_db.php");

if($_REQUEST)
{
	$grup = $_REQUEST['grup'];
	$optTHLOLD_CompanyID = $_REQUEST['optTHLOLD_CompanyID'];
	if ($grup=="non_grl"){
	
		$query="SELECT DISTINCT dg.DocumentGroup_ID, dg.DocumentGroup_Name
				FROM M_DocumentLegal dl, M_DocumentGroup dg
				WHERE dl.DL_Delete_Time IS NULL
				AND dl.DL_CompanyID = '$optTHLOLD_CompanyID'
				AND dl.DL_GroupDocID = dg.DocumentGroup_ID
				AND dl.DL_Status='1'
				ORDER BY dg.DocumentGroup_Name";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Kategori Dokumen ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['DocumentGroup_ID'];?>"><?php echo $arr['DocumentGroup_Name'];?></option>
		<?php
		}
	}?>
	
<?php	
}
?>
