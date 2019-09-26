<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.3.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 21 Agustus 2018																					=
= Update Terakhir	: -																						            =
= Revisi			:																									=
=========================================================================================================================
*/
session_start();
include ("./include/mother-variable.php");
?>
<title>Custodian System | Permintaan Dokumen Lainnya (Legal)</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.lodocol.php");
?>

<script language="JavaScript" type="text/JavaScript">

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;

	var optTHLOOLD_DocumentType = document.getElementById('optTHLOOLD_DocumentType').selectedIndex;
	var optTHLOOLD_DocumentWithWatermarkOrNot = document.getElementById('optTHLOOLD_DocumentWithWatermarkOrNot').selectedIndex;
	var optTHLOOLD_LoanCategoryID = document.getElementById('optTHLOOLD_LoanCategoryID').selectedIndex;
	var txtTHLOOLD_SoftcopyReceiver = document.getElementById('txtTHLOOLD_SoftcopyReceiver').value;
	var optTHLOOLD_CompanyID = document.getElementById('optTHLOOLD_CompanyID').selectedIndex;
	var txtTHLOOLD_Information = document.getElementById('txtTHLOOLD_Information').value;

		if(optTHLOOLD_DocumentType == 1 || optTHLOOLD_DocumentType == 2){
			if(optTHLOOLD_LoanCategoryID == 0) {
				alert("Kategori Permintaan Belum Dipilih!");
				returnValue = false;
			}
		}else if(optTHLOOLD_DocumentType == 3){
			if (txtTHLOOLD_SoftcopyReceiver.replace(" ", "") == "")  {
				alert("Email Penerima Dokumen Belum Diisi!");
				returnValue = false;
			}
		}else{
			alert("Tipe Dokumen Belum Dipilih!");
			returnValue = false;
		}
		if(optTHLOOLD_DocumentType == 2 || optTHLOOLD_DocumentType == 3){
            if(optTHLOOLD_DocumentWithWatermarkOrNot == 0) {
                if( optTHLOOLD_DocumentType == 2 ){ var cap_or_watermark = "Watermark";}
                if( optTHLOOLD_DocumentType == 3 ){ var cap_or_watermark = "Cap";}
    			alert("Dokumen dengan "+cap_or_watermark+" Belum Dipilih!");
    			returnValue = false;
    		}
        }
		if(optTHLOOLD_CompanyID == 0) {
			alert("Perusahaan Belum Dipilih!");
			returnValue = false;
		}
		if (txtTHLOOLD_Information.replace(" ", "") == "")  {
			alert("Alasan Permintaan Belum Diisi!");
			returnValue = false;
		}
	return returnValue;
}

function chg_DocWatermarkOrNot(x){
    if(x.value == "ORIGINAL"){
        $('#doc-with-watermark-or-not-val').html("");
        $('#optTHLOOLD_DocumentWithWatermarkOrNot').css('display', 'none');
        $('#optTHLOOLD_DocumentWithWatermarkOrNot').val("0");
    }else if(x.value == "HARDCOPY"){
        document.getElementById('doc-with-watermark-or-not-val').innerHTML = "Dokumen dengan Watermark";
            $('#optTHLOOLD_DocumentWithWatermarkOrNot').css('display', 'block');
    }else if(x.value == "SOFTCOPY"){
        document.getElementById('doc-with-watermark-or-not-val').innerHTML = "Dokumen dengan Cap";
            $('#optTHLOOLD_DocumentWithWatermarkOrNot').css('display', 'block');
    }else{
        document.getElementById('doc-with-watermark-or-not-val').innerHTML = "Dokumen dengan Cap/Watermark";
        $('#optTHLOOLD_DocumentWithWatermarkOrNot').css('display', 'block');
        $('#optTHLOOLD_DocumentWithWatermarkOrNot').val("0");
    }
}

//LoV UTK DAFTAR DOKUMEN
function showList(row) {
	var txtTHLOOLD_CompanyID = document.getElementById('txtTHLOOLD_CompanyID').value;
	var txtTHLOOLD_DocumentGroupID = document.getElementById('txtTHLOOLD_DocumentGroupID').value;
	var docCode = document.getElementById('docCode').value;
	//var endocCode = base64.encode(docCode);
	//alert (endocCode);
	sList = window.open("popupDocOL.php?row="+row+"&cID="+txtTHLOOLD_CompanyID+"&gID="+txtTHLOOLD_DocumentGroupID+"&recentCode="+docCode+"", "Daftar_Dokumen", "width=1000,height=500,scrollbars=yes,resizable=yes");
}
function remLink() {
  if (window.sList && window.sList.open && !window.sList.closed)
	window.sList.opener = null;
}

// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var returnValue;
	var checkDocCode;
	returnValue = true;
	var jrow = document.getElementById('countRow').value;
	var txtTHLOOLD_CompanyID = document.getElementById('txtTHLOOLD_CompanyID').value;
	var txtTHLOOLD_DocumentGroupID = document.getElementById('txtTHLOOLD_DocumentGroupID').value;
	var txtTHLOOLD_Information = document.getElementById('txtTHLOOLD_Information').value;

		if (txtTHLOOLD_Information.replace(" ", "") == "")  {
			alert("Alasan Permintaan Belum Diisi!");
			returnValue = false;
		}

	for (i = 1; i <= jrow; i++){
		checkDocCode = 0;
		var txtTDLOOLD_DocumentCode = document.getElementById('txtTDLOOLD_DocumentCode' + i).value;

		if (txtTDLOOLD_DocumentCode.replace(" ", "") == "")  {
			alert("Kode Dokumen pada baris ke-" + i + " Belum Terisi!");
			returnValue = false;
		}
		/*else {
			<?php
 			$query = "SELECT *
					  FROM M_DocumentsOtherLegal
					  WHERE DOL_Status ='1'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) {
				$DocumentCode = $data['DOL_DocCode'];
				$DOL_CompanyID = $data['DOL_CompanyID'];
				$DOL_GroupDocID = $data['DOL_GroupDocID'];

				$a = "if ( (txtTDLOOLD_DocumentCode == '$DocumentCode')
							&& (txtTHLOOLD_CompanyID == '$DOL_CompanyID')
							&& (txtTHLOOLD_DocumentGroupID == '$DOL_GroupDocID')
						  ){";
				$a .= "checkDocCode = 1; ";
				$a .= "}";
			echo $a;
		 	}
			?>
			if (checkDocCode == 0) {
				alert("Kode Dokumen pada baris ke-" + i + " Salah Atau Dokumen Tidak Tersedia!");
				returnValue = false;
			}
		}*/
	}
	return returnValue;
}
</script>
</head>

<?PHP
//die;
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$act=$_GET["act"];
if(isset($_GET["act"])) {

	//Menambah Header Permintaan Dokumen
	if($act=='add') {
		$ActionContent ="
		<form name='add-doc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Permintaan Dokumen Lainnya (Legal)</th>
		</tr>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2, grup.DocumentGroup_Name,grup.DocumentGroup_ID,
						 e.Employee_GradeCode, e.Employee_Grade
				  FROM M_User u
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_DocumentGroup grup
  					ON grup.DocumentGroup_ID='5'
				  LEFT JOIN db_master.M_Employee AS e
                  	ON u.User_ID = e.Employee_NIK
                  	AND e.Employee_GradeCode IN ('0000000005','06','0000000003','05','04','0000000004')
				  WHERE u.User_ID='$mv_UserID'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHLOOLD_UserID' type='hidden' value='$mv_UserID'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtTHLOOLD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtTHLOOLD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input name='txtTHLOOLD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>
        <tr>
            <td>Grup Dokumen</td>
            <td>
                <input name='txtTHLOOLD_DocumentGroupID' type='hidden' value='$field[DocumentGroup_ID]'/>
                $field[DocumentGroup_Name]
            </td>
        </tr>";

		if(!empty($field['User_SPV1']) || !empty($field['User_SPV2'])){
			if( !empty($field['Employee_GradeCode']) && !empty($field['Employee_Grade']) ){
				$query="SELECT DISTINCT c.Company_ID, UPPER(c.Company_Name) AS Company_Name
						FROM M_DocumentsOtherLegal dol
						INNER JOIN M_Company c
							ON dol.DOL_CompanyID = c.Company_ID
							AND c.Company_Delete_Time is NULL
						WHERE dol.DOL_Delete_Time is NULL
						AND dol.DOL_Status='1'
						ORDER BY c.Company_Name ASC";
				$sql = mysql_query($query);
				$number=mysql_num_rows($sql);

				if ($number>0) {
	                $ActionContent .="
	                <tr>
	                    <td>Tipe Dokumen</td>
	                    <td>
	                        <select name='optTHLOOLD_DocumentType' id='optTHLOOLD_DocumentType' onchange='chg_DocWatermarkOrNot(this)'>
	                            <option value=''>--- Pilih Tipe Dokumen ---</option>
	                            <option value='ORIGINAL'>Asli</option>
	                            <option value='HARDCOPY'>Hardcopy</option>
	                            <!--<option value='SOFTCOPY'>Softcopy</option>-->
	                        </select>
	                    </td>
	                </tr>";

	                $ActionContent .="
	                <tr>
	                    <td id='doc-with-watermark-or-not-val'>Dokumen dengan Cap/Watermark</td>
	                    <td>
		                    <select name='optTHLOOLD_DocumentWithWatermarkOrNot' id='optTHLOOLD_DocumentWithWatermarkOrNot'>
		                        <option value=''>--- Pilih Keterangan ---</option>
		                        <option value='1'>Iya</option>
		                        <option value='2'>Tidak</option>
		                    </select>
	                    </td>
	                </tr>";

					$ActionContent .="
					<tr>
						<td id='td-chg'>Kategori Permintaan</td>
						<td>
							<select name='optTHLOOLD_LoanCategoryID' id='optTHLOOLD_LoanCategoryID'>
								<option value='0'>--- Pilih Kategori Permintaan ---</option>";

							$query1= "SELECT *
									  FROM M_LoanCategory
									  WHERE LoanCategory_Delete_Time is NULL";
							$sql1 = mysql_query($query1);

							while ($field1 = mysql_fetch_array($sql1) ){
								$ActionContent .="
								<option value='$field1[LoanCategory_ID]'>$field1[LoanCategory_Name]</option>";
							}
					$ActionContent .="
							</select>
							<input id='txtTHLOOLD_SoftcopyReceiver' name='txtTHLOOLD_SoftcopyReceiver' type='text' />
						</td>
					</tr>
					<tr>
						<td>Perusahaan</td>
						<td>
							<select name='optTHLOOLD_CompanyID' id='optTHLOOLD_CompanyID' style='width:350px'>
								<option value='0'>--- Pilih Perusahan ---</option>";
							while ($field = mysql_fetch_array($sql) ){
								$ActionContent .="
								<option value='$field[Company_ID]'>$field[Company_Name]</option>";
							}
					$ActionContent .="
							</select>
						</td>
					</tr>
					<tr>
						<td valign='top'>Alasan Permintaan</td>
						<td><textarea name='txtTHLOOLD_Information' id='txtTHLOOLD_Information' cols='50' rows='2'></textarea></td>
					</tr>
					<tr>
						<th colspan=3>
							<input name='addheader' type='submit' value='Simpan' class='button' onclick='return validateInputHeader(this);'/>
							<input name='cancel' type='submit' value='Batal' class='button' />
						</th>
					</tr>";
				}else {
					if(!$_POST['cancel']){
						echo "<script>alert('Tidak Ada Dokumen Yang Dapat Melakukan Transaksi Ini.');</script>";
					}
					$ActionContent .="
					<tr>
						<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
							Tidak Ada Dokumen Yang Dapat Melakukan Transaksi Ini.
						</td>
					</tr>
					<tr>
						<th colspan=3>
							<input name='cancel' type='submit' value='OK' class='button'/>
						</th>
					</tr>";
				}
			}else{ //Else cek jabatan minimal Dept. Head
    			if(!$_POST['cancel']){
    				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini. Minimal jabatan Department Head.);</script>";
    			}

    			$ActionContent .="
    			<tr>
    				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
    					Anda Tidak Dapat Melakukan Transaksi Ini. Minimal jabatan Department Head.<br>
    					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
    				</td>
    			</tr>
    			<tr>
    				<th colspan=3>
    					<input name='cancel' type='submit' value='OK' class='button'/>
    				</th>
    			</tr>";
    		}
		}else{
			if(!$_POST['cancel']){
				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.');</script>";
			}

			$ActionContent .="
			<tr>
				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
					Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.<br>
					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
				</td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='cancel' type='submit' value='OK' class='button'/>
				</th>
			</tr>";
		}
		$ActionContent .="
		</table>
		</form>";
	}

	//Menambah Detail Permintaan Dokumen
	elseif($act=='adddetail')	{
		$id=$_GET["id"];

		$query = "SELECT thloold.THLOOLD_ID,
						 thloold.THLOOLD_LoanCode,
						 thloold.THLOOLD_LoanDate,
						 u.User_FullName,
						 d.Division_Name,
						 de.Department_Name,
						 p.Position_Name,
						 c.Company_Name, c.Company_ID, c.Company_Area,
						 thloold.THLOOLD_DocumentType,
						 thloold.THLOOLD_DocumentWithWatermarkOrNot,
						 thloold.THLOOLD_SoftcopyReceiver,
						 lc.LoanCategory_Name,
						 lc.LoanCategory_ID,
						 dg.DocumentGroup_Name, dg.DocumentGroup_ID,
						 thloold.THLOOLD_Information
				  FROM TH_LoanOfOtherLegalDocuments thloold
				  LEFT JOIN M_User u
					ON thloold.THLOOLD_UserID=u.User_ID
					AND thloold.THLOOLD_UserID='$mv_UserID'
				  LEFT JOIN M_Company c
					ON thloold.THLOOLD_CompanyID=c.Company_ID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON ddp.DDP_UserID=u.User_ID
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN  M_Department de
					ON ddp.DDP_DeptID=de.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_DocumentGroup dg
					ON dg.DocumentGroup_ID='5'
				  LEFT JOIN M_LoanCategory lc
					ON thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
				  WHERE thloold.THLOOLD_Delete_Time is NULL
				  AND thloold.THLOOLD_LoanCode='$id'";
		$field = mysql_fetch_array(mysql_query($query));

		$DOL_CompanyID=$field['Company_ID'];
		$DocumentGroup_ID=$field['DocumentGroup_ID'];
		$floandate=date("j M Y", strtotime($field['THLOOLD_LoanDate']));

		$ActionContent ="
		<form name='adddetaildoc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Permintaan Dokumen Lainnya (Legal)</th>
		</tr>
		<tr>
			<td width='30'>No Permintaan</td>
			<td width='70%'>
				<input name='txtTDLOOLD_THLOOLD_ID' type='hidden' value='$field[THLOOLD_ID]'/>
				<input type='hidden' name='txtTDLOOLD_THLOOLD_LoanCode' value='$field[THLOOLD_LoanCode]'/>
				$field[THLOOLD_LoanCode]
			</td>
		</tr>
		<tr>
			<td>Tanggal Permintaan</td>
			<td>$floandate</td>
		</tr>
		<tr>
			<td>Nama Peminjam</td>
			<td>$field[User_FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field[Division_Name]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field[Department_Name]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field[Position_Name]</td>
		</tr>
		<tr>
			<td>Grup Dokumen</td>
			<td>
				<input name='txtGroupID' id='txtTHLOOLD_DocumentGroupID' type='hidden' value='$DocumentGroup_ID'/>
				$field[DocumentGroup_Name]
			</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>
				<input id='txtTHLOOLD_CompanyID' type='hidden' value='$DOL_CompanyID'/>
				<input id='txtCompArea' name='txtCompArea' type='hidden' value='$field[Company_Area]'/>
				$field[Company_Name]
			</td>
		</tr>
		<tr>
			<td>Tipe Dokumen</td>
			<td colspan='2'><input type='hidden' name='optTHLOOLD_DocumentType' value='$field[THLOOLD_DocumentType]'>";
			if( $field['THLOOLD_DocumentType'] == "ORIGINAL" ){
				$ActionContent .="Asli";
			}elseif( $field['THLOOLD_DocumentType'] == "HARDCOPY" ){
				$ActionContent .="Hardcopy";
			}elseif( $field['THLOOLD_DocumentType'] == "SOFTCOPY" ){
				$ActionContent .="Softcopy";
			}else{
				if( $field['LoanCategory_ID'] < 3) $ActionContent .= "Asli";
				elseif( $field['LoanCategory_ID'] == 3 ) $ActionContent .= "Hardcopy";
				elseif( $field['LoanCategory_ID'] == 4) $ActionContent .= "Softcopy";
				else $ActionContent .= "";
			}
			$ActionContent .="</td>
		</tr>
		";
		if( $field['THLOOLD_DocumentType'] != "ORIGINAL" ){
			if( $field['THLOOLD_DocumentType'] == "HARDCOPY" ){
				$cap_or_watermark = "Watermark";
			}elseif( $field['THLOOLD_DocumentType'] == "SOFTCOPY" ){
				$cap_or_watermark = "Cap";
			}
		$ActionContent .="<tr>
			<td>Dokumen dengan ".$cap_or_watermark."</td>
			<td colspan='2'><input type='hidden' name='optTHLOOLD_DocumentWithWatermarkOrNot' value='$field[THLOOLD_DocumentWithWatermarkOrNot]'>";
				if( $field['THLOOLD_DocumentWithWatermarkOrNot'] == "1" ){
					$ActionContent .="Iya";
				}elseif( $field['THLOOLD_DocumentWithWatermarkOrNot'] == "2" ){
					$ActionContent .="Tidak";
				}else{
					$ActionContent .= "-";
				}
			$ActionContent .="</td>
		</tr>";
		}
		$ActionContent .="<tr>
			";
		if( $field['LoanCategory_ID'] != 4 ){
			$ActionContent .="<td>Kategori Permintaan</td>
			<td>
				<input name='optTHLOOLD_LoanCategoryID' type='hidden' value='$field[LoanCategory_ID]'/>
				$field[LoanCategory_Name]
			</td>";
		}else{
			$ActionContent .="<td>Email Penerima Dokumen</td>
			<td>
				<input name='txtTHLOOLD_SoftcopyReceiver' type='hidden' value='$field[THLOOLD_SoftcopyReceiver]'/>
				$field[THLOOLD_SoftcopyReceiver]
			</td>";
		}
		$ActionContent .="
		</tr>
		<tr>
			<td valign='top'>Alasan Permintaan</td>
			<td>
				<textarea name='txtTHLOOLD_Information' id='txtTHLOOLD_Information' cols='50' rows='2'>$field[THLOOLD_Information]</textarea>
			</td>
		</tr>
		</table>
		<input name='docCode' id='docCode' type='hidden' value=''/>
		<div style='space'>&nbsp;</div>

		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th>Kode Dokumen</th>
			<th>Keterangan Permintaan</th>
		</tr>
		<tr>
			<td>
			<input type='text' name='txtTDLOOLD_DocumentCode1' id='txtTDLOOLD_DocumentCode1' value='' readonly='readonly' onClick='javascript:showList(1);'>

			</td>
			<td>
				<textarea name='txtTDLOOLD_Information1' id='txtTDLOOLD_Information1' cols='20' rows='1'></textarea>
			</td>
		</tr>
		</table>

		<table width='100%'>
		<th  class='bg-white'>
			<input onclick='addRowToTable();' type='button' class='addrow'/>
			<input onclick='removeRowFromTable();' type='button' class='deleterow'/>
			<input type='hidden' value='1' id='countRow' name='countRow' />
		</th>
		</table>

		<table width='100%'>
		<tr>
			<td>";

			/* PROSES APPROVAL */
			$user=$mv_UserID;

			$result = array();

			for($sApp=1;$sApp<10;$sApp++) {
				//Cek Jabatan Pengaju
				$query="SELECT Employee_Grade
					FROM db_master.M_Employee
					WHERE Employee_NIK='".$user."'
					 AND Employee_GradeCode
					 	IN ('0000000005', '06', '0000000003', '05', '04', '0000000004')";
				$sql=mysql_query($query);
				$obj=mysql_fetch_object($sql);
				$jabatan=$obj->Employee_Grade;
				// echo $jabatan;
				$atasan = "";
				if( $jabatan != null ){
					if( $jabatan != "DIVISION HEAD" ){
			            //ATASAN LANGSUNG
			            $query="SELECT User_SPV1,User_SPV2
			                    FROM M_User
			                    WHERE User_ID='$user'";
			            $sql=mysql_query($query);
			            $obj=mysql_fetch_object($sql);
			            $atasan1=$obj->User_SPV1;
			            $atasan2=$obj->User_SPV2;

			            if($atasan2){
			                $atasan=$atasan2;
			            }else{
			                $atasan=$atasan1;
			            }

						$query="SELECT Employee_NIK
								FROM db_master.M_Employee
								WHERE Employee_NIK='".$atasan."'
								AND Employee_Position NOT LIKE '%SECTION%'
								AND Employee_Position NOT LIKE '%SUB DEP%'";
						$canApprove=mysql_num_rows(mysql_query($query));

						if($canApprove){
							$user = $atasan;
							array_push($result, $user);
							$ats = $user;
							break;
						}else{
							$user = $atasan;
							$sApp=3;
						}

						$obj1 = mysql_fetch_object(mysql_query("SELECT User_SPV1, User_SPV2 FROM M_User WHERE User_ID = '$ats'"));
						array_push($result, $obj1->User_SPV1);
			            // $approvers[] = $atasan; //Approval Step ke 1
			        }
					//Jika pengaju adalah Divison Head tidak dibutuhkan approval dari atasan langsung
				}
			}

			if ($field['THLOOLD_DocumentType'] == "ORIGINAL") { $jenis = '17'; $proses = '2'; }
			else if ($field['THLOOLD_DocumentType'] == "HARDCOPY") { $jenis = '18'; $proses = '2'; }
			else if ($field['THLOOLD_DocumentType'] == "SOFTCOPY") { $jenis = '26'; $proses = '2'; }
			else;

			$query = "
				SELECT ma.Approver_UserID, rads.RADS_StepID
				FROM M_Role_ApproverDocStepStatus rads
				LEFT JOIN M_Role_Approver ra
					ON rads.RADS_RA_ID = ra.RA_ID
				LEFT JOIN M_Approver ma
					ON ra.RA_ID = ma.Approver_RoleID
				WHERE rads.RADS_DocID = '{$jenis}'
					AND rads.RADS_ProsesID = '{$proses}'
					AND ma.Approver_Delete_Time IS NULL
					AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$field['Company_Area']}')
					ORDER BY rads.RADS_StepID
			";
			// echo $query;
			$sql=mysql_query($query);

			$output = array();
			while($obj=mysql_fetch_object($sql)){
				$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				//$ActionContent .="
				//<input type='text' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}
			// print_r ($output);
			// AKHIR PROSES APPROVAL

			$i = 0;
			$newArray = array();
			foreach ($output as $k => $v) {
				if ($v == '0') { $newArray[$k] = $result[$i]; $i++; } else { $newArray[$k] = $v; }
			}

			$key = array_search('', $newArray);
			if (false !== $key) unset($newArray[$key]);

			foreach ($newArray as $key => $value) {
				$ActionContent .= "<input type='hidden' name='txtA_ApproverID[$key]' value='$value' readonly='true' class='readonly' />";
			}

		$ActionContent .="
			</td>
		</tr>
		<th><input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/><input name='canceldetail' type='submit' value='Batal' class='button'/></th>
		</table>
		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}
	//Kirim Ulang Email Persetujuan
	elseif($act=='resend'){
		mail_loan_doc($_GET['code'],'1');
		// echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		// echo "<meta http-equiv='refresh' content='0; url=loan-of-other-legal-documents.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT thloold.THLOOLD_ID, thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate, u.User_FullName,
 		  		 c.Company_Name, lc.LoanCategory_Name,drs.DRS_Description, thloold.THLOOLD_Status
		  FROM TH_LoanOfOtherLegalDocuments thloold, M_User u, M_Company c, M_LoanCategory lc,
		  	   M_DocumentRegistrationStatus drs
		  WHERE thloold.THLOOLD_Delete_Time is NULL
		  AND thloold.THLOOLD_CompanyID=c.Company_ID
		  AND thloold.THLOOLD_UserID=u.User_ID
		  AND u.User_ID='$mv_UserID'
		  AND lc.loanCategory_ID != '4' #sementara yg softcopy di hide
		  AND thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID
		  AND thloold.THLOOLD_Status=drs.DRS_Name
		  ORDER BY thloold.THLOOLD_ID  DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='25%'>Kode Permintaan</th>
	<th width='10%'>Tanggal Permintaan</th>
	<th width='20%'>Nama Peminjam</th>
	<th width='20%'>Nama Perusahaan</th>
	<th width='10%'>Kategori Permintaan</th>
	<th width='10%'>Status</th>
	<th width='5%'></th>
</tr>";
if ($num==NULL) {
$MainContent .="
	<tr>
		<td colspan=7 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$loandate=strtotime($field['THLOOLD_LoanDate']);
		$floandate=date("j M Y", $loandate);
		$resend=($field['THLOOLD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";
		if($field[6] == "Draft"){
			$link = "loan-of-other-legal-documents.php?act=adddetail&id=".$field[1];
		}else{
			$link = "detail-of-loan-other-legal-documents.php?id=".$field[0];
		}

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='".$link."' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$floandate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$field[5]</td>
			<td class='center'>$field[6]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT thloold.THLOOLD_ID, thloold.THLOOLD_LoanCode, thloold.THLOOLD_LoanDate, u.User_FullName,
 		  		  c.Company_Name, lc.LoanCategory_Name
		   FROM TH_LoanOfOtherLegalDocuments thloold, M_User u, M_Company c, M_LoanCategory lc
		   WHERE thloold.THLOOLD_Delete_Time is NULL
		   AND thloold.THLOOLD_CompanyID=c.Company_ID
		   AND thloold.THLOOLD_UserID=u.User_ID
		   AND u.User_ID='$mv_UserID'
		   AND lc.loanCategory_ID != '4' #sementara yg softcopy di hide
		   AND thloold.THLOOLD_LoanCategoryID=lc.LoanCategory_ID";
$num1 = mysql_num_rows(mysql_query($query1));

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1)
	$Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
    	if (($showPage == 1) && ($p != 2))
			$Pager.="...";
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))
			$Pager.="...";
        if ($p == $noPage)
			$Pager.="<b><u>$p</b></u> ";
        else
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";

		$showPage = $p;
	}
}

if ($noPage < $jumPage)
	$Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=loan-of-other-legal-documents.php'>";
}

elseif(isset($_POST['canceldetail'])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_LoanOfOtherLegalDocuments thloold
			   SET ct.CT_Delete_UserID='$mv_UserID',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$mv_UserID',ct.CT_Update_Time=sysdate(),
			       thloold.THLOOLD_Delete_UserID='$mv_UserID',thloold.THLOOLD_Delete_Time=sysdate(),
			       thloold.THLOOLD_Update_UserID='$mv_UserID',thloold.THLOOLD_Update_Time=sysdate()
			   WHERE thloold.THLOOLD_ID='$_POST[txtTDLOOLD_THLOOLD_ID]'
			   AND thloold.THLOOLD_LoanCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=loan-of-other-legal-documents.php'>";
	}
}

elseif(isset($_POST['addheader'])) {
	$regyear=date("Y");
	$rmonth=date("n");

	// Mengubah Bulan ke Romawi
	switch ($rmonth)	{
		case 1: $regmonth="I"; break;
		case 2: $regmonth="II"; break;
		case 3: $regmonth="III"; break;
		case 4: $regmonth="IV"; break;
		case 5: $regmonth="V"; break;
		case 6: $regmonth="VI"; break;
		case 7: $regmonth="VII"; break;
		case 8: $regmonth="VIII"; break;
		case 9: $regmonth="IX"; break;
		case 10: $regmonth="X"; break;
		case 11: $regmonth="XI"; break;
		case 12: $regmonth="XII"; break;
	}

	// Cari Kode Perusahaan
	$query = "SELECT *
			  FROM M_Company
			  WHERE Company_ID='$_POST[optTHLOOLD_CompanyID]'";
	$field = mysql_fetch_array(mysql_query($query));
	$Company_Code=$field['Company_Code'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup
			  WHERE DocumentGroup_ID ='$_POST[txtTHLOOLD_DocumentGroupID]'";
	$field = mysql_fetch_array(mysql_query($query));
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Permintaan Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='REQ'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$field = mysql_fetch_array(mysql_query($query));

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Permintaan Dokumen
	$CT_Code="$newnum/REQ/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode Permintaan dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','REQ','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$mv_UserID',sysdate(),'$mv_UserID',sysdate(),NULL,NULL)";
	if($mysqli->query($sql)) {
		//Insert Header Dokumen
		$info = str_replace("<br>", "\n", $_POST['txtTHLOOLD_Information']);
		if($_POST['optTHLOOLD_DocumentType'] == 'SOFTCOPY'){
            $_POST['optTHLOOLD_LoanCategoryID'] = '4';
        }
		$sql1= "INSERT INTO TH_LoanOfOtherLegalDocuments
				VALUES (NULL,'$CT_Code',sysdate(),'$mv_UserID',
                        '$_POST[optTHLOOLD_DocumentType]', '$_POST[optTHLOOLD_DocumentWithWatermarkOrNot]',
                        '$_POST[optTHLOOLD_LoanCategoryID]', '$_POST[txtTHLOOLD_SoftcopyReceiver]',
						'$_POST[optTHLOOLD_CompanyID]',
                        '$info', '0', NULL,
						'$mv_UserID', sysdate(),NULL,NULL)"; //Arief F - 21082018
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=loan-of-other-legal-documents.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST['adddetail'])) {
	$count=$_POST['countRow'];
	$txtTHLOOLD_Information=str_replace("<br>", "\n",$_POST['txtTHLOOLD_Information']);
	$A_TransactionCode = $_POST['txtTDLOOLD_THLOOLD_LoanCode'];
	$A_ApproverID=$mv_UserID;

	for ($i=1 ; $i<=$count ; $i++) {
		$txtTDLOOLD_DocumentCode=$_POST["txtTDLOOLD_DocumentCode".$i];
		$txtTDLOOLD_Information=str_replace("<br>", "\n",$_POST["txtTDLOOLD_Information".$i]);

		$sql1= "INSERT INTO TD_LoanOfOtherLegalDocuments
			VALUES (NULL,NULL,'$_POST[txtTDLOOLD_THLOOLD_ID]',
			'$txtTDLOOLD_DocumentCode','$txtTDLOOLD_Information', '0','$A_ApproverID', sysdate(),'$A_ApproverID', sysdate(),NULL,NULL)";
		$mysqli->query($sql1);

		switch ($_POST['optTHLOOLD_LoanCategoryID']) {
			case "1":
				$docStatus="2";
				break;
			case "2":
				$docStatus="2";
				break;
			case "3":
				$docStatus="1";
				break;
			default: $docStatus="1";
		}

		$sql5= "UPDATE M_DocumentsOtherLegal
			    SET DOL_Status ='$docStatus', DOL_Update_UserID='$A_ApproverID', DOL_Update_Time=sysdate()
			    WHERE DOL_DocCode='$txtTDLOOLD_DocumentCode'";
		$mysqli->query($sql5);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];

	$step = 0;
	foreach ($txtA_ApproverID as $k=>$v) {
		$step=$step+1;
		if ($txtA_ApproverID[$k]<>NULL) {
			if ($txtA_ApproverID[$k]<>$mv_UserID) {
				$appbefquery = "SELECT *
						FROM M_Approval
						WHERE A_TransactionCode='$A_TransactionCode'
						AND A_ApproverID='$txtA_ApproverID[$k]'
						AND A_Step='$step'";
				$numappbef = mysql_num_rows(mysql_query($appbefquery));

				if ($numappbef == '0') {
					$sql2 = "INSERT INTO M_Approval
						VALUES (NULL, '$A_TransactionCode', '$txtA_ApproverID[$k]', '$step',
						'1', NULL, '$A_ApproverID', sysdate(), '$A_ApproverID', sysdate(), NULL, NULL)";
					$mysqli->query($sql2);
					$sa_query = "SELECT *
							FROM M_Approval
							WHERE A_TransactionCode='$A_TransactionCode'
							AND A_ApproverID='$txtA_ApproverID[$k]'
							AND A_Step = '$step'
							AND A_Delete_Time IS NULL";
					$sa_arr = mysql_fetch_array(mysql_query($sa_query));
					$ARC_AID = $sa_arr['A_ID'];
					$str = rand(1,100);
					$RandomCode = crypt('T4pagri'.$str);
					$iSQL="INSERT INTO L_ApprovalRandomCode VALUES ('$ARC_AID', '$RandomCode')";
					$mysqli->query($iSQL);
				}
			}
		}
	}
	/*$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$mv_UserID){
				$appbefquery="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$_POST[txtTDLOOLD_THLOOLD_LoanCode]'
							  AND A_ApproverID='$txtA_ApproverID[$i]'";
				$appbefsql = mysql_query($appbefquery);
				$numappbef=mysql_num_rows($appbefsql);

				if ($numappbef=='0') {
					$step=$step+1;
					$sql2= "INSERT INTO M_Approval
							VALUES (NULL,'$_POST[txtTDLOOLD_THLOOLD_LoanCode]', '$txtA_ApproverID[$i]', '$step',
									'1',NULL,'$mv_UserID', sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
					$mysqli->query($sql2);
					$sa_query="SELECT *
							   FROM M_Approval
							   WHERE A_TransactionCode='$_POST[txtTDLOOLD_THLOOLD_LoanCode]'
							   AND A_ApproverID='$txtA_ApproverID[$i]'
							   AND A_Delete_Time IS NULL";
					$sa_sql=mysql_query($sa_query);
					$sa_arr=mysql_fetch_array($sa_sql);
					$ARC_AID=$sa_arr['A_ID'];
					$str=rand(1,100);
					$RandomCode=crypt('T4pagri'.$str);
					$iSQL="INSERT INTO L_ApprovalRandomCode
						   VALUES ('$ARC_AID','$RandomCode')";
					$mysqli->query($iSQL);
				}
			}
		}
	}*/

	// MENCARI JUMLAH APPROVAL
	$query = "SELECT MAX(A_Step) AS jStep
		FROM M_Approval
		WHERE A_TransactionCode='$A_TransactionCode'";
	$arr = mysql_fetch_array(mysql_query($query));
	$jStep=$arr['jStep'];

	for ($i=1; $i<=$jStep; $i++) {
		$query="SELECT A_Status, A_ApproverID
			FROM M_Approval
			WHERE A_TransactionCode='$A_TransactionCode'
				AND A_Step='$i'";
		$sql = mysql_query($query);
		$result = mysql_fetch_array($sql);

		if ($result['A_Status'] == '1') {
			$query = "UPDATE M_Approval
				SET A_Status = '2', A_Update_UserID = '$A_ApproverID', A_Update_Time = sysdate()
				WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_loan_doc($A_TransactionCode);
			}
			break;
		} else if ($result['A_Status'] == '2') {
			$query = "UPDATE M_Approval
				SET A_Status = '3', A_Update_UserID = '$A_ApproverID', A_ApprovalDate = sysdate(), A_Update_Time = sysdate()
				WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_notif_loan_doc($A_TransactionCode, $result['A_ApproverID'], 3);
			}
		}
	}

	// MENCARI JUMLAH APPROVAL
	// $query = "SELECT MAX(A_Step) AS jStep
	// 	FROM M_Approval
	// 	WHERE A_TransactionCode='$A_TransactionCode'";
	// $arr = mysql_fetch_array(mysql_query($query));
	// $jStep=$arr['jStep'];
	//
	// if ($_POST['txtGroupID'] == '1' && $_POST['optTHLOOLD_LoanCategoryID'] != '3') { $jenis = '1'; }
	// else if ($_POST['txtGroupID'] == '1' && $_POST['optTHLOOLD_LoanCategoryID'] == '3') { $jenis = '2'; }
	// else if ($_POST['txtGroupID'] == '2' && $_POST['optTHLOOLD_LoanCategoryID'] != '3') { $jenis = '3'; }
	// else if ($_POST['txtGroupID'] == '2' && $_POST['optTHLOOLD_LoanCategoryID'] == '3') { $jenis = '4'; }
	// else;
	//
	// for ($i=1; $i<=$jStep; $i++) {
	// 	$query ="
	// 		SELECT rads.RADS_StatusID, ma.A_ApproverID
	// 		FROM M_Approval ma
	// 		JOIN M_Role_ApproverDocStepStatus rads
	// 			ON ma.A_Step = rads.RADS_StepID
	// 		LEFT JOIN M_Role_Approver ra
	// 			ON rads.RADS_RA_ID = ra.RA_ID
	// 		WHERE ma.A_Step = '{$i}'
	// 			AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$_POST['txtCompArea']}')
	// 			AND ma.A_TransactionCode = '{$A_TransactionCode}'
	// 			AND rads.RADS_DocID = '{$jenis}'
	// 			AND rads.RADS_ProsesID = '2'
	// 	";
	// 	$result = mysql_fetch_array(mysql_query($query));
	//
	// 	if ($result['RADS_StatusID'] == '1') {
	// 		$query = "UPDATE M_Approval
	// 			SET A_Status = '2', A_Update_UserID = '$A_ApproverID', A_Update_Time = sysdate()
	// 			WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
	// 		if ($sql = mysql_query($query)) {
	// 			mail_loan_doc($A_TransactionCode);
	// 		}
	// 		break;
	// 	} else if ($result['RADS_StatusID'] == '2') {
	// 		$query = "UPDATE M_Approval
	// 			SET A_Status = '3', A_Update_UserID = '$A_ApproverID', A_ApprovalDate = sysdate(), A_Update_Time = sysdate()
	// 			WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
	// 		if ($sql = mysql_query($query)) {
	// 			mail_notif_loan_doc($A_TransactionCode, $result['A_ApproverID'], 3);
	// 		}
	// 	}
	// }

	/*$sql3= "UPDATE M_Approval
			SET A_Status='2', A_Update_UserID='$mv_UserID',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDLOOLD_THLOOLD_LoanCode]'
			AND A_Step='1'";*/

	$sql4= "UPDATE TH_LoanOfOtherLegalDocuments
		SET THLOOLD_Status='waiting', THLOOLD_Information='$txtTHLOOLD_Information',
		THLOOLD_Update_UserID='$A_ApproverID',THLOOLD_Update_Time=sysdate()
		WHERE THLOOLD_LoanCode='$A_TransactionCode'
		AND THLOOLD_Delete_Time IS NULL";
	$mysqli->query($sql4);

	/*if($mysqli->query($sql4)) {
		// Kirim Email ke Approver 1
		mail_loan_doc($_POST['txtTDLOOLD_THLOOLD_LoanCode']);
	}*/
	echo "<meta http-equiv='refresh' content='0; url=loan-of-other-legal-documents.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
function klik(ff){
showList(ff);
}

// TAMBAH BARIS
function addRowToTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	document.getElementById('countRow').value = (document.getElementById('countRow').value*1) + 1;
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);

	// KODE DOKUMEN
	var cell0 = row.insertCell(0);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDLOOLD_DocumentCode' + iteration;
	el.id = 'txtTDLOOLD_DocumentCode' + iteration;
	el.value= '';
	//el.setAttribute("onclick","showList("+iteration+")");
	el.onclick=function(){ showList(""+iteration+"");  };
	el.readOnly = true;
	cell0.appendChild(el);

	// INFORMASI PERMINTAAN
	var cell1 = row.insertCell(1);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDLOOLD_Information' + iteration;
	el.id = 'txtTDLOOLD_Information' + iteration;
	cell1.appendChild(el);
}

// HAPUS BARIS
function removeRowFromTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	if(document.getElementById('countRow').value > 1)
		document.getElementById('countRow').value -= 1;
	if (lastRow > 2)
		tbl.deleteRow(lastRow - 1);
}

$(document).ready(function(){
	$("#txtTHLOOLD_SoftcopyReceiver").hide();
	$("#optTHLOOLD_DocumentType").change(function(){
		$("#txtTHLOOLD_SoftcopyReceiver").hide();
		$("#optTHLOOLD_LoanCategoryID").show();
		if($(this).val()=="ORIGINAL"){
			$("#td-chg").html("Kategori Permintaan");
			$("#optTHLOOLD_LoanCategoryID option:first").nextAll().hide();
			$("#optTHLOOLD_LoanCategoryID option:contains('Peminjaman Dokumen')").show();
			$("#optTHLOOLD_LoanCategoryID option:contains('Pengolahan Dokumen')").show();
		}
		else if($(this).val()=="HARDCOPY"){
			$("#td-chg").html("Kategori Permintaan");
			$("#optTHLOOLD_LoanCategoryID option:first").nextAll().hide();
			$("#optTHLOOLD_LoanCategoryID option:contains('Fotocopy Dokumen')").show();
		}
		else if($(this).val()=="SOFTCOPY"){
			$("#td-chg").html("Email Penerima Dokumen");
			$("#optTHLOOLD_LoanCategoryID").hide();
			$("#txtTHLOOLD_SoftcopyReceiver").show();
		}
		else{
			$("#optTHLOOLD_LoanCategoryID option").show();
		}
	})
})
</script>
