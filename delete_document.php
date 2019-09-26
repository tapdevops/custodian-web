<TITLE>Hapus Dokumen</TITLE>
<?PHP 
include ("./config/config_db.php"); 

/* ----------- ASLI -----------
//WAJIB DIISI
$code="";							// diisi kode dokumen yang mau dihapus
$user="";							// diisi ID user yang mau hapus
$type="";							// diisi jenis dokumen "grl" atau "non_grl"
// END
*/

//WAJIB DIISI
$code="";			// diisi kode dokumen yang mau dihapus
$user="";						// diisi ID user yang mau hapus
$type="";						// diisi jenis dokumen "grl" atau "non_grl"
// END


echo"
	<table width='80%' border='1' cellpadding='0' cellspacing='0' style='font-family:verdana; font-size:12px;'>
	<tr bgcolor='#CCC'>
		<td width='40%' align='center'>KETERANGAN</td>
		<td width='20%' align='center'>KODE</td>
		<td width='20%' align='center'>USER</td>			
		<td width='20%' align='center'>WAKTU</td>
	</tr>";

if($code && $user && $type){
	//UNTUK GRL
	if ($type=="grl"){
		$query="SELECT DLA_ID, DLA_Location
				FROM M_DocumentLandAcquisition
				WHERE DLA_Code='$code'
				AND DLA_Delete_Time IS NULL";
		$sql = mysql_query($query);
		@$num = mysql_num_rows($sql);
		@$obj = mysql_fetch_object($sql);
		
		if($num){
			//hapus di daftar dokumen
			$del = "UPDATE M_DocumentLandAcquisition
					SET DLA_Update_UserID='$user',
						DLA_Update_Time=sysdate(),
						DLA_Delete_UserID='$user',
						DLA_Delete_Time=sysdate()
					WHERE DLA_Code='$code'";
			mysql_query($del);
			
			//hapus detail dokumen
			$del = "UPDATE M_DocumentLandAcquisitionAttribute
					SET DLAA_Update_UserID='$user',
						DLAA_Update_Time=sysdate(),
						DLAA_Delete_UserID='$user',
						DLAA_Delete_Time=sysdate()
					WHERE DLAA_DLA_ID='".$obj->DLA_ID."'";
			mysql_query($del);
			
			//set lokasi jadi "tersedia"
			$del = "UPDATE L_DocumentLocation
					SET DL_Update_UserID='$user',
						DL_Update_Time=sysdate(),
						DL_Status='1'
					WHERE DL_Code='".$obj->DLA_Location."'";
			mysql_query($del);
			
			$query="SELECT 'PENGHAPUSAN DOKUMEN GRL' tipe, DLA_Code kode,User_FullName user,DLA_Delete_Time waktu
					FROM M_DocumentLandAcquisition
					LEFT JOIN M_User
						ON DLA_Delete_UserID=User_ID
					WHERE DLA_Code='$code'
					
					UNION
					SELECT 'PENGHAPUSAN DETAIL KELENGKAPAN DOKUMEN GRL' tipe, DLAA_DocCode kode,User_FullName user,DLAA_Delete_Time waktu
					FROM M_DocumentLandAcquisitionAttribute
					LEFT JOIN M_DocumentLandAcquisition
						ON DLAA_DLA_ID=DLA_ID					
					LEFT JOIN M_User
						ON DLAA_Delete_UserID=User_ID
					WHERE DLA_Code='$code'
						
					UNION
					SELECT 'PENGHAPUSAN LOKASI TERPAKAI' tipe, DL_Code kode,User_FullName user,DL_Update_Time waktu
					FROM L_DocumentLocation
					LEFT JOIN M_DocumentLandAcquisition
						ON DL_Code=DLA_Location
					LEFT JOIN M_User
						ON DL_Update_UserID=User_ID
					WHERE DLA_Code='$code'";
			$sql = mysql_query($query);
			while($obj = mysql_fetch_object($sql)){
				echo"
					<tr>
						<td align='center'>".$obj->tipe."</td>
						<td align='center'>".$obj->kode."</td>
						<td align='center'>".$obj->user."</td>
						<td align='center'>".$obj->waktu."</td>
					</tr>		
				";	
			}
		}else{
			echo"
				<tr>
					<td align='center' colspan='4'>DOKUMEN TIDAK TERSEDIA.</td>
				</tr>		
			";			
		}
	}
	//UNTUK NON_GRL (LEGAL/LISENSI)
	else{
		$query="SELECT DL_Location
				FROM M_DocumentLegal
				WHERE DL_DocCode='$code'
				AND DL_Delete_Time IS NULL";
		$sql = mysql_query($query);
		@$num = mysql_num_rows($sql);
		@$obj = mysql_fetch_object($sql);
		
		if($num){
			//hapus di daftar dokumen
			$del = "UPDATE M_DocumentLegal
					SET DL_Update_UserID='$user',
						DL_Update_Time=sysdate(),
						DL_Delete_UserID='$user',
						DL_Delete_Time=sysdate()
					WHERE DL_DocCode='$code'";
			mysql_query($del);
			
			//set lokasi jadi "tersedia"
			$del = "UPDATE L_DocumentLocation
					SET DL_Update_UserID='$user',
						DL_Update_Time=sysdate(),
						DL_Status='1'
					WHERE DL_Code='".$obj->DL_Location."'";
			mysql_query($del);
			
			$query="SELECT 'PENGHAPUSAN DOKUMEN LEGAL/LISENSI' tipe, DL_DocCode kode,User_FullName user,DL_Delete_Time waktu
					FROM M_DocumentLegal
					LEFT JOIN M_User
						ON DL_Delete_UserID=User_ID
					WHERE DL_DocCode='$code'
					
					UNION
					SELECT 'PENGHAPUSAN LOKASI TERPAKAI' tipe, lokasi.DL_Code kode,User_FullName user,lokasi.DL_Update_Time waktu
					FROM L_DocumentLocation lokasi
					LEFT JOIN M_DocumentLegal doc
						ON lokasi.DL_Code=doc.DL_Location
					LEFT JOIN M_User
						ON lokasi.DL_Update_UserID=User_ID
					WHERE doc.DL_DocCode='$code'";
			$sql = mysql_query($query);
			while($obj = mysql_fetch_object($sql)){
				echo"
					<tr>
						<td align='center'>".$obj->tipe."</td>
						<td align='center'>".$obj->kode."</td>
						<td align='center'>".$obj->user."</td>
						<td align='center'>".$obj->waktu."</td>
					</tr>		
				";	
			}
		}else{
			echo"
				<tr>
					<td align='center' colspan='4'>DOKUMEN TIDAK TERSEDIA.</td>
				</tr>		
			";			
		}	
	}
}else{
	echo"
		<tr>
			<td align='center' colspan='4'>KODE DOKUMEN / TIPE DOKUMEN / USER ID PENGHAPUS DATA BELUM DIISI.<br>TIDAK ADA DOKUMEN YANG DIHAPUS.</td>
		</tr>";	
}
?>