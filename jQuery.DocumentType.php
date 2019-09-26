<?php
	include ("./config/config_db.php");

if($_REQUEST)
{
	$CategoryID = $_REQUEST['CategoryID'];
	$GroupID = $_REQUEST['GroupID'];
	$query="SELECT DISTINCT dt.DocumentType_ID,dt.DocumentType_Name
			FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
			WHERE dgct.DGCT_DocumentGroupID=".$GroupID."
			AND dgct.DGCT_DocumentCategoryID=".$CategoryID."
			AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
			AND dgct.DGCT_Delete_Time is NULL
			ORDER BY DocumentType_Name";
	$sql = mysql_query($query);?>
	<option value="0">--- Pilih Tipe Dokumen ---</option>
	<?php
	while ($arr = mysql_fetch_array($sql)) {?>
		<option value="<?php echo $arr['DocumentType_ID'];?>" style="width:500px"><?php echo $arr['DocumentType_Name'];?></option>
	<?php
	}?>
<?php
}
?>
