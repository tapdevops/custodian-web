<?PHP session_start(); ?>
<title>Custodian System | Pencarian</title>
<head>
<?PHP include ("./config/config_db.php"); ?>
<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;							

	var optDocumentGroup = document.getElementById('optDocumentGroup').selectedIndex;
	var txtSearch = document.getElementById('txtSearch').value;
		
		if(optDocumentGroup == 0) {
			alert("Grup Dokumen Belum Dipilih!");
			returnValue = false;
		}
		
		if (txtSearch.replace(" ", "") == "") {	
			alert("Teks Yang Dicari Belum Ditentukan!");
			returnValue = false;
		}

	return returnValue;
}
</script>
</head>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
	
require_once "./include/template.inc";
$page=new Template();

// Menampilkan form pencarian
$MainContent ="
	<div class='home-title'>PENCARIAN</div>
	<form name='search' method='get' action='$PHP_SELF'>
		<center>
			<input name='txtSearch' id='txtSearch' type='text' size='30%'/>
			<select name='optDocumentGroup' id='optDocumentGroup'>
				<option value='0'>--- Pilih Grup Dokumen ---</option>";
			$query = "SELECT *
					  FROM M_DocumentGroup
					  WHERE DocumentGroup_Delete_Time IS NULL";
			$sql = mysql_query($query);
			while ($arr=mysql_fetch_array($sql)) {
$MainContent .="
				<option value='$arr[DocumentGroup_ID]'>$arr[DocumentGroup_Name]</option>";
			}
$MainContent .="
			</select>
			<input name='search' type='submit' value='Cari' class='button' onclick='return validateInput(this);'/>
		</center>
	</form>
";

/* ------- */
/* ACTIONS */
/* ------- */

if(isset($_GET[search])) {
	$getLink=$_SERVER["REQUEST_URI"];
	$arr = explode("&page=", $getLink);
	$link = $arr[0];
	
	$search=$_GET['txtSearch'];
	$docGroup=$_GET['optDocumentGroup'];
	
	$dataPerPage = 20;
	if(isset($_GET['page'])) {
    	$noPage = $_GET['page'];
	} 
	else 
		$noPage = 1;
	$offset = ($noPage - 1) * $dataPerPage;
	
	// Melakukan pencarian untuk dokumen legal / license / other
	if ($docGroup<>'3') {
		$query =   "SELECT dl.DL_DocCode,  
						 u.User_FullName, 
						 dl.DL_RegTime, 
						 c.Company_Name,  
						 dc.DocumentCategory_Name,  
						 dt.DocumentType_Name,  
						 dl.DL_NoDoc, 
						 dl.DL_PubDate, 
						 dl.DL_ExpDate, 
						 di1.DocumentInformation1_Name, 
						 di2.DocumentInformation2_Name, 
						 dl.DL_Information3, 
						 dl.DL_Instance, 
						 dl.DL_Location, 
						 lds.LDS_Name
		  			FROM M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt, 
						 M_LoanDetailStatus lds, M_DocumentInformation1 di1, M_DocumentInformation2 di2, M_User u
					WHERE dl.DL_CompanyID=c.Company_ID
					AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
					AND dl.DL_TypeDocID=dt.DocumentType_ID
					AND dl.DL_Status=lds.LDS_ID
					AND dl.DL_RegUserID=u.User_ID
					AND dl.DL_Information1=di1.DocumentInformation1_ID
					AND dl.DL_Information2=di2.DocumentInformation2_ID
					AND dl.DL_GroupDocID='$docGroup'
					AND (
						dl.DL_DocCode LIKE '%$search%'
						OR dl.DL_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR dl.DL_CategoryDocID LIKE '%$search%'
						OR dc.DocumentCategory_Name LIKE '%$search%'
						OR dl.DL_TypeDocID LIKE '%$search%'
						OR dt.DocumentType_Name LIKE '%$search%'
						OR dl.DL_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dl.DL_Information1 LIKE '%$search%'
						OR di1.DocumentInformation1_Name LIKE '%$search%'
						OR dl.DL_Information2 LIKE '%$search%'
						OR di2.DocumentInformation2_Name LIKE '%$search%'
						OR dl.DL_Information3 LIKE '%$search%'
						OR dl.DL_Instance LIKE '%$search%'
						OR dl.DL_RegTime LIKE '%$search%'
						OR dl.DL_NoDoc LIKE '%$search%'
						OR dl.DL_PubDate LIKE '%$search%'
						OR dl.DL_ExpDate LIKE '%$search%'
					)						
					ORDER BY dl.DL_CompanyID, dl.DL_DocCode ASC "; 
		$limit="LIMIT $offset, $dataPerPage";
	}	
	elseif ($docGroup=='3') {
		$query =   "SELECT dla.DLA_Code,  
						 u.User_FullName, 
						 dla.DLA_RegTime, 
						 c.Company_Name,  
						 dla.DLA_Location, 
						 dla.DLA_Phase, 
						 dla.DLA_Period, 
						 dla.DLA_DocDate,
						 dla.DLA_Block, 
						 dla.DLA_Village, 
						 dla.DLA_Owner, 
						 dla.DLA_Information,
						 lds.LDS_Name
		  			FROM M_DocumentLandAcquisition dla, M_Company c, M_User u, M_LoanDetailStatus lds
					WHERE dla.DLA_CompanyID=c.Company_ID
					AND dla.DLA_Status=lds.LDS_ID
					AND dla.DLA_RegUserID=u.User_ID
					AND (
						dla.DLA_Code LIKE '%$search%'
						OR dla.DLA_CompanyID LIKE '%$search%'
						OR c.Company_Name LIKE '%$search%'
						OR dla.DLA_RegUserID LIKE '%$search%'
						OR u.User_FullName LIKE '%$search%'
						OR dla.DLA_Information LIKE '%$search%'
						OR dla.DLA_RegTime LIKE '%$search%'
						OR dla.DLA_Phase LIKE '%$search%'
						OR dla.DLA_Period LIKE '%$search%'
						OR dla.DLA_DocDate LIKE '%$search%'
						OR dla.DLA_Block LIKE '%$search%' 
						OR dla.DLA_Village LIKE '%$search%' 
						OR dla.DLA_Owner LIKE '%$search%'
						OR dla.DLA_Information LIKE '%$search%'
						OR dla.DLA_AreaClass LIKE '%$search%'
						OR dla.DLA_AreaStatement LIKE '%$search%'
						OR dla.DLA_AreaPrice LIKE '%$search%'
						OR dla.DLA_AreaTotalPrice LIKE '%$search%'
						OR dla.DLA_PlantClass LIKE '%$search%'	
						OR dla.DLA_PlantQuantity LIKE '%$search%'
						OR dla.DLA_PlantPrice LIKE '%$search%'
						OR dla.DLA_PlantTotalPrice LIKE '%$search%'	
						OR dla.DLA_GrandTotal LIKE '%$search%'
					)						
					ORDER BY dla.DLA_CompanyID,dla.DLA_Code ASC ";
		$limit="LIMIT $offset, $dataPerPage";
	}	
	$allquery=$query.$limit;
	$sql = mysql_query($allquery);
	$num = mysql_num_rows($sql);
	
	// Menampilkan hasil pencarian
	if ($num==NULL){
$MainContent .="
	<center><b>Hasil Pencarian Tidak Ditemukan.</b></center>
";
	}
	else{
	if ($docGroup<>'3') {
		$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
					<tr>
						<th width='15%'>Kode Dokumen</th>
						<th width='20%'>Perusahaan</th>
						<th width='20%'>Tipe Dokumen</th>
						<th width='15%'>Nomor Dokumen</th>
						<th width='20%'>Instansi Terkait</th>
						<th width='10%'>Status</th>
					</tr>
		";
		
				while ($arr = mysql_fetch_array($sql)){
		$MainContent .="
					<tr>
						<td class='center'><a href='document-list.php?act=detail&id=$arr[DL_DocCode]' class='underline'>$arr[DL_DocCode]</a></td>
						<td class='center'>$arr[Company_Name]</td>
						<td class='center'>$arr[DocumentType_Name]</td>
						<td class='center'>$arr[DL_NoDoc]</td>
						<td class='center'>$arr[DL_Instance]</td>
						<td class='center'>$arr[LDS_Name]</td>
					</tr>
		";
				}
		$MainContent .="
			</table>
		";
	}
	elseif ($docGroup=='3') {
		$MainContent .="
				<table width='100%' border='1' class='stripeMe'>
					<tr>
						<th width='15%'>Kode Dokumen</th>
						<th width='20%'>Perusahaan</th>
						<th width='5%'>Tahap</th>
						<th width='15%'>Periode Ganti Rugi</th>
						<th width='15%'>Desa</th>
						<th width='20%'>Pemilik</th>
						<th width='10%'>Status</th>
					</tr>
		";
		
				while ($arr = mysql_fetch_array($sql)){
		$MainContent .="
					<tr>
						<td class='center'><a href='document-list.php?act=detailLA&id=$arr[DLA_Code]' class='underline'>$arr[DLA_Code]</a></td>
						<td class='center'>$arr[Company_Name]</td>
						<td class='center'>$arr[DLA_Phase]</td>
						<td class='center'>".date('d-m-Y', strtotime($arr[DLA_Period]))."</td>
						<td class='center'>$arr[DLA_Village]</td>
						<td class='center'>$arr[DLA_Owner]</td>
						<td class='center'>$arr[LDS_Name]</td>
					</tr>
		";
				}
		$MainContent .="
			</table>
		";
	}	
	}
}
$sql1 = mysql_query($query);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1) 
	$Pager.="<a href='$link&page=$prev'>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {   
    	if (($showPage == 1) && ($p != 2))  
			$Pager.="..."; 
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))  
			$Pager.="...";
        if ($p == $noPage) 
			$Pager.="<b><u>$p</b></u> ";
        else 
			$Pager.="<a href='$link&page=$p'>$p</a> ";
        
		$showPage = $p;          
	}
}

if ($noPage < $jumPage) 
	$Pager .= "<a href='$link&page=$next'>Next &gt;&gt;</a> ";

$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>