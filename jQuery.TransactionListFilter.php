<?php
	include ("./config/config_db.php");

if($_REQUEST) {
	$optTHROLD_DocumentGroupID = $_REQUEST['optTHROLD_DocumentGroupID'];
	$optFilterHeader = $_REQUEST['optFilterHeader'];
	$optTransactionID = $_REQUEST['optTransactionID'];

	if ($optFilterHeader=="0") {
		?>
		<option value='0'>--- Pilih Grup Dokumen Terlebih Dahulu ---</option>
		<?php
	}

	if ($optFilterHeader=="1") {
		if($optTHROLD_DocumentGroupID == "1" or $optTHROLD_DocumentGroupID == "2"){
	        $query = "SELECT DISTINCT DL_CompanyID as Company_ID, Company_Name FROM M_DocumentLegal
	            LEFT JOIN M_Company ON DL_CompanyID=Company_ID
				WHERE DL_GroupDocID='$optTHROLD_DocumentGroupID'
				ORDER BY Company_Name ASC";
	    }elseif($optTHROLD_DocumentGroupID == "3"){
	        $query = "SELECT DISTINCT DLA_CompanyID as Company_ID, Company_Name FROM M_DocumentLandAcquisition
	            LEFT JOIN M_Company ON DLA_CompanyID=Company_ID
				ORDER BY Company_Name ASC";
	    }elseif($optTHROLD_DocumentGroupID == "4"){
			if($optTransactionID == "2"){
				$query = "SELECT DISTINCT c.Company_ID, c.Company_Name
					FROM TH_LoanOfAssetOwnershipDocument th
					INNER JOIN M_Company c
						ON c.Company_ID = th.THLOAOD_CompanyID
					WHERE
						th.THLOAOD_Delete_TIME IS NULL
					ORDER BY c.Company_Name ASC";
			}else{
				$query = "SELECT DISTINCT c.Company_ID, c.Company_Name
					FROM M_DocumentAssetOwnership dao
					INNER JOIN M_Company c
						ON c.Company_Code = REPLACE(dao.DAO_Employee_NIK, 'CO@', '')
					WHERE dao.DAO_Employee_NIK LIKE '%CO@%'
					AND dao.DAO_Delete_Time is NULL
					AND dao.DAO_Status='1'
					UNION
					SELECT 'COP' Company_ID, 'COP - Car Ownership Program' Company_Name
					FROM M_DocumentAssetOwnership dao
					WHERE dao.DAO_Employee_NIK NOT LIKE '%CO@%'
					AND dao.DAO_Delete_Time is NULL
					AND dao.DAO_Status='1'
					HAVING COUNT(dao.DAO_ID)>0
					ORDER BY Company_Name ASC";
			}
	    }elseif($optTHROLD_DocumentGroupID == "5"){
	        $query = "SELECT DISTINCT DOL_CompanyID as Company_ID, Company_Name FROM M_DocumentsOtherLegal
	            LEFT JOIN M_Company ON DOL_CompanyID=Company_ID
				ORDER BY Company_Name ASC";
	    }elseif($optTHROLD_DocumentGroupID == "6"){
	        $query = "SELECT DISTINCT DONL_CompanyID as Company_ID, Company_Name FROM M_DocumentsOtherNonLegal
	            LEFT JOIN M_Company ON DONL_CompanyID=Company_ID
				ORDER BY Company_Name ASC";
	    }else{
	        $query = "";
	    }
		// $query="SELECT *
		// 		FROM M_Company
		// 		WHERE Company_Delete_Time is NULL
		// 		ORDER BY Company_Name";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Perusahaan ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['Company_ID'];?>"><?php echo $arr['Company_Name'];?></option>
		<?php
		}
	}

	if ($optFilterHeader=="2") {
		$query="SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
				FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
				WHERE dgct.DGCT_Delete_Time is NULL
				AND dgct.DGCT_DocumentGroupID=".$optTHROLD_DocumentGroupID."
				AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
				ORDER BY dc.DocumentCategory_Name";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Kategori Dokumen ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['DocumentCategory_ID'];?>"><?php echo $arr['DocumentCategory_Name'];?></option>
		<?php
		}
	}

	if ($optFilterHeader=="3") {
		$query="SELECT DISTINCT dt.DocumentType_ID, dt.DocumentType_Name
				FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
				WHERE dgct.DGCT_Delete_Time is NULL
				AND dgct.DGCT_DocumentGroupID=".$optTHROLD_DocumentGroupID."
				AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
				ORDER BY dt.DocumentType_Name";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Tipe Dokumen ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['DocumentType_ID'];?>"><?php echo $arr['DocumentType_Name'];?></option>
		<?php
		}
	}

	if ($optFilterHeader=="4") {
		$query="SELECT *
				FROM M_DocumentRegistrationStatus
				WHERE DRS_Delete_Time is NULL";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Status Transaksi ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['DRS_Name'];?>"><?php echo $arr['DRS_Description'];?></option>
		<?php
		}
	}

	if ($optFilterHeader=="5") {
		$query="SELECT *
				FROM M_LoanDetailStatus
				WHERE LDS_Delete_Time is NULL";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Status Dokumen ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['LDS_ID'];?>"><?php echo $arr['LDS_Name'];?></option>
		<?php
		}
	}?>

<?php
}
?>
