<!-- 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 31 Mei 2012																						=
= Update Terakhir	: 31 Mei 2012																						=
= Revisi			:																									=
========================================================================================================================
-->
<link href="./css/mobile.css" rel="stylesheet" type="text/css">
<?PHP
include ("./config/config_db.php"); 
include ("./include/function.mail.reldoc.php");
$decrp = new custodian_encryp;

if(($_GET['cfm'])&&($_GET['ati'])&&($_GET['rdm'])) {
	$A_Status="3";
	$A_ID=$decrp->decrypt($_GET['ati']);
	$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);
	
	$query = "SELECT *
			  FROM L_ApprovalRandomCode
			  WHERE ARC_AID='$A_ID'
			  AND ARC_RandomCode='$ARC_RandomCode'";
	$sql = mysql_query($query);
	$num = mysql_num_rows($sql);

	if ($num==1) {			
		// MENCARI TAHAP APPROVAL USER TERSEBUT
		$query = "SELECT *
				  FROM M_Approval
				  WHERE A_ID='$A_ID'";
		$sql = mysql_query($query);
		$arr = mysql_fetch_array($sql);
		$step=$arr[A_Step];
		$AppDate=$arr['A_ApprovalDate'];
		$A_TransactionCode=$arr['A_TransactionCode'];
		$A_ApproverID=$arr['A_ApproverID'];
		
		$h_query="SELECT *
				  FROM TH_ReleaseOfLegalDocument throld,TH_LoanOfLegalDocument thlold
				  WHERE throld.THROLD_ReleaseCode='$A_TransactionCode'
				  AND throld.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode 
				  AND throld.THROLD_Delete_Time IS NULL
				  AND thlold.THLOLD_Delete_Time IS NULL";
		$h_sql=mysql_query($h_query);
		$h_arr=mysql_fetch_array($h_sql);
						
		if ($AppDate==NULL) {
			// MENCARI JUMLAH APPROVAL
			$query = "SELECT MAX(A_Step) AS jStep
						FROM M_Approval
						WHERE A_TransactionCode='$A_TransactionCode'";
			$sql = mysql_query($query);
			$arr = mysql_fetch_array($sql);
			$jStep=$arr['jStep'];
			
			// UPDATE APPROVAL
			$query = "UPDATE M_Approval
						SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
							A_Update_Time=sysdate()
						WHERE A_ID='$A_ID'";
			$sql = mysql_query($query);
			
			// PROSES BILA "SETUJU"
			if ($A_Status=='3') {
				// CEK APAKAH MERUPAKAN APPROVAL FINAL
				if ($step <> $jStep) {
					$nStep=$step+1;
					$query = "UPDATE M_Approval
								SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_Step='$nStep'";
					if ($sql = mysql_query($query)) {
						// Kirim Email ke Approver selanjutnya
						mail_release_doc($A_TransactionCode);
						echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Custodian System</td>
		</tr>
		<tr>
			<td>
				Persetujuan Anda Telah Disimpan.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
					}
				}
				else {
					$query = "UPDATE TH_ReleaseOfLegalDocument
								SET THROLD_Status='accept', THROLD_Update_UserID='$A_ApproverID',
								    THROLD_Update_Time=sysdate()
								WHERE THROLD_ReleaseCode='$A_TransactionCode'
								AND THROLD_Delete_Time IS NULL";
					if ($sql = mysql_query($query)) {
						// ACTION UNTUK GENERATE NO DOKUMEN
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
									WHERE Company_ID='$h_arr[THLOLD_CompanyID]'";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
						$Company_Code=$field['Company_Code'];
						
						// Cari Kode Dokumen Grup
						$query = "SELECT *
									FROM M_DocumentGroup 
									WHERE DocumentGroup_ID ='$h_arr[THLOLD_DocumentGroupID]'";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
						$DocumentGroup_Code=$field['DocumentGroup_Code'];
			
						// Cari No Dokumen Terakhir
						$query = "SELECT MAX(CD_SeqNo) 
									FROM M_CodeDocument 
									WHERE CD_Year='$regyear' 
									AND CT_Action='DOUT'
									AND CD_GroupDocCode='$DocumentGroup_Code'
									AND CD_CompanyCode='$Company_Code'
									AND CD_Delete_Time is NULL";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
			
						if($field[0]==NULL)
							$maxnum=0;
						else
							$maxnum=$field[0];
						$nnum=$maxnum+1;
						
						$d_query="SELECT *
								  FROM TD_ReleaseOfLegalDocument tdrold, TD_LoanOfLegalDocument tdlold
								  WHERE tdrold.TDROLD_THROLD_ID='$h_arr[THROLD_ID]'
								  AND tdrold.TDROLD_Delete_Time IS NULL
								  AND tdrold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID";
						$d_sql=mysql_query($d_query);
						while($d_arr=mysql_fetch_array($d_sql)){
							$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
							// Kode Pengeluaran Dokumen	
							$CT_Code="$newnum/DOUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";
			
							switch ($h_arr[THLOLD_LoanCategoryID]) {
								case "1":
									$docStatus="4";
									$code="0";
									break;
								case "2":
									$docStatus="5";
									$code=NULL;
									break;
								case "3":
									$docStatus="1";
									$code=NULL;
									break;
							}
							$query1="UPDATE M_DocumentLegal
									 SET DL_Status='$docStatus', DL_Update_UserID='$A_ApproverID', 
									 	 DL_Update_Time=sysdate()
									 WHERE DL_DocCode='$d_arr[TDLOLD_DocCode]'";
							$query2="INSERT INTO M_CodeTransaction 
								   	 VALUES (NULL,'$CT_Code','$nnum','DOUT','$Company_Code','$DocumentGroup_Code',
											 '$rmonth','$regyear','$A_ApproverID',sysdate(),
											 '$A_ApproverID',sysdate(),NULL,NULL)";
							$query3="UPDATE TD_ReleaseOfLegalDocument
									 SET TDROLD_Code ='$CT_Code',TDROLD_ReturnCode='$code',
										 TDROLD_Update_UserID='$A_ApproverID', TDROLD_Update_Time=sysdate()
									 WHERE TDROLD_THROLD_ID='$h_arr[THROLD_ID]'
									 AND TDROLD_TDLOLD_ID='$d_arr[TDLOLD_ID]'";
							
							$mysqli->query($query1);
							$mysqli->query($query2);
							$mysqli->query($query3);
							$nnum=$nnum+1;
						}
						mail_notif_release_doc($A_TransactionCode, $h_arr['THLOLD_UserID'], 3 );
						mail_notif_release_doc($A_TransactionCode, "cust0002", 3 );
						
						echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Berhasil</td>
		</tr>
		<tr>
			<td>
				Persetujuan Anda Telah Disimpan.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
					}
				}		
			}
		}
		else {
			echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Gagal</td>
		</tr>
		<tr>
			<td>
				Anda tidak dapat melakukan persetujuan ini<br>
				karena Anda telah melakukan persetujuan sebelumnya.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";			
		}
	}
	else {
			echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Gagal</td>
		</tr>
		<tr>
			<td>
				Anda tidak dapat melakukan persetujuan ini<br>
				karena Anda tidak memiliki hak persetujuan untuk transaksi ini.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";			
	}
}
if($_GET['act']) {
	$act=$decrp->decrypt($_GET['act']);
	if ($act=='reject'){
		$A_ID=$decrp->decrypt($_GET['ati']);
		$ARC_RandomCode=$decrp->decrypt($_GET['rdm']);

		echo "
		<form name='reason' method='post' action='$PHP_SELF'>
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td style='text-align:left !important'; class='header'>Custodian System</td>
		</tr>
		<tr>
			<td><input type='hidden' name='A_ID' value='$A_ID'>
				<input type='hidden' name='ARC_RandomCode' value='$ARC_RandomCode'>
				<textarea name='txtTHROLD_Reason' id='txtTHROLD_Reason' rows='3'>$arr[THROLD_Reason]</textarea>
				<br>*Wajib Diisi Apabila Anda Tidak Menyetujui Pengeluaran Dokumen.<br>
			</td>
		</tr>
		<tr>
			<td>
				<center><input name='reject' type='submit' value='Tolak'/></center>
			</td>
		</tr>
		<tr>
			<td class='footer'>Powered By Custodian System</td>
		</tr>
		</tbody>
		</table>
		</form>";
	}
}

if(isset($_POST[reject])) {
	$A_Status='4';
	$A_ID=$_POST['A_ID'];
	$ARC_RandomCode=$_POST['ARC_RandomCode'];
	
	if (str_replace(" ", "", $_POST['txtTHROLD_Reason'])==NULL){
		echo "<meta http-equiv='refresh' content='0; url=act.mail.reldoc.php?act=".$decrp->encrypt('reject').">";
	}
	else {
		$THROLD_Reason=str_replace("<br>", "\n",$_POST['txtTHROLD_Reason']);	
		$query = "SELECT *
				  FROM L_ApprovalRandomCode
				  WHERE ARC_AID='$A_ID'
				  AND ARC_RandomCode='$ARC_RandomCode'";
		$sql = mysql_query($query);
		$num = mysql_num_rows($sql);
	
		if ($num==1) {	
		
			$query = "SELECT *
				  	  FROM M_Approval
				  	  WHERE A_ID='$A_ID'";
			$sql = mysql_query($query);
			$arr = mysql_fetch_array($sql);
			$step=$arr[A_Step];
			$AppDate=$arr['A_ApprovalDate'];
			$A_TransactionCode=$arr['A_TransactionCode'];
			$A_ApproverID=$arr['A_ApproverID'];
			
			if ($AppDate==NULL) {

				$h_query="SELECT *
						  FROM TH_ReleaseOfLegalDocument throld,TH_LoanOfLegalDocument thlold
						  WHERE throld.THROLD_ReleaseCode='$A_TransactionCode'
						  AND throld.THROLD_Delete_Time IS NULL";
				$h_sql=mysql_query($h_query);
				$h_arr=mysql_fetch_array($h_sql);
	
				$query1="UPDATE TH_ReleaseOfLegalDocument
						 SET THROLD_Status='reject', THROLD_Reason='$THROLD_Reason',
						  	 THROLD_Update_Time=sysdate(), THROLD_Update_UserID='$A_ApproverID'
						 WHERE THROLD_ReleaseCode='$A_TransactionCode'";
							
				// UPDATE APPROVAL
				$query2="UPDATE M_Approval
						 SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
							 A_Update_Time=sysdate()
						 WHERE A_ID='$A_ID'";
				
				$query3="UPDATE M_Approval
						 SET A_Update_Time=sysdate(), A_Update_UserID='$A_ApproverID', 
						     A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID', 
							 A_Status='$A_Status'
						 WHERE A_TransactionCode='$A_TransactionCode'
						 AND A_Step>='$step'";
				$mysqli->query($query1);
				$mysqli->query($query2);
				$mysqli->query($query3);
						   
				$d_query="SELECT *
						  FROM TD_ReleaseOfLegalDocument tdrold, TD_LoanOfLegalDocument tdlold
						  WHERE tdrold.TDROLD_THROLD_ID='$h_arr[THROLD_ID]'
						  AND tdrold.TDROLD_Delete_Time IS NULL
						  AND tdrold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID";
				$d_sql=mysql_query($d_query);
				while($d_arr=mysql_fetch_array($d_sql)){
					$query="UPDATE M_DocumentLegal
						    SET DL_Status='1', DL_Update_UserID='$A_ApproverID', DL_Update_Time=sysdate()
						    WHERE DL_Code='$d_arr[TDLOLD_DocCode]'";	
					$mysqli->query($query);		
				}
				mail_notif_release_doc($A_TransactionCode, $h_arr['THLOLD_UserID'], 4 );
				mail_notif_release_doc($A_TransactionCode, $h_arr['THROLD_UserID'], 4 );
				echo "
				<table border='0' align='center' cellpadding='0' cellspacing='0'>
				<tbody>
				<tr>
					<td class='header'>Persetujuan Berhasil</td>
				</tr>
				<tr>
					<td>
						Persetujuan Anda Telah Disimpan.<br>
						Terima kasih.<br><br>
						Hormat Kami,<br />Departemen Custodian<br />
						PT Triputra Agro Persada
					</td>
				</tr>
				<tr>
					<td class='footer'>Powered By Custodian System </td>
				</tr>
				</tbody>
				</table>";
			}
			else {
				echo "
			<table border='0' align='center' cellpadding='0' cellspacing='0'>
			<tbody>
			<tr>
				<td class='header'>Persetujuan Gagal</td>
			</tr>
			<tr>
				<td>
					Anda tidak dapat melakukan persetujuan ini<br>
					karena Anda telah melakukan persetujuan sebelumnya.<br>
					Terima kasih.<br><br>
					Hormat Kami,<br />Departemen Custodian<br />
					PT Triputra Agro Persada
				</td>
			</tr>
			<tr>
				<td class='footer'>
				Powered By Custodian System </td>
			</tr>
			</tbody>
			</table>";			
			}
		}
		else {
						echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Gagal</td>
		</tr>
		<tr>
			<td>
				Anda tidak dapat melakukan persetujuan ini<br>
				karena Anda tidak memiliki hak persetujuan untuk transaksi ini.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";			
		}
	}
}
?>