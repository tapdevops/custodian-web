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
include ("./include/function.mail.reldocla.php");
$decrp = new custodian_encryp;

if( !empty($_GET['cfm']) && !empty($_GET['ati']) && !empty($_GET['rdm']) ) {
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
		$step=$arr['A_Step'];
		$AppDate=$arr['A_ApprovalDate'];
		$A_TransactionCode=$arr['A_TransactionCode'];
		$A_ApproverID=$arr['A_ApproverID'];

		$h_query="SELECT *
				  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad,TH_LoanOfLandAcquisitionDocument thlolad
				  WHERE thrlolad.THRLOLAD_ReleaseCode='$A_TransactionCode'
				  AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				  AND thlolad.THLOLAD_Delete_Time IS NULL
				  AND thrlolad.THRLOLAD_Delete_Time IS NULL";
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
					$query = "UPDATE TH_ReleaseOfLandAcquisitionDocument
								SET THRLOLAD_Status='accept', THRLOLAD_Update_UserID='$A_ApproverID',
								    THRLOLAD_Update_Time=sysdate()
								WHERE THRLOLAD_ReleaseCode='$A_TransactionCode'
								AND THRLOLAD_Delete_Time IS NULL";
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
									WHERE Company_ID='$h_arr[THLOLAD_CompanyID]'";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
						$Company_Code=$field['Company_Code'];

						// Cari Kode Dokumen Grup
						$query = "SELECT *
									FROM M_DocumentGroup
									WHERE DocumentGroup_ID ='3'";
						$sql = mysql_query($query);
						$field = mysql_fetch_array($sql);
						$DocumentGroup_Code=$field['DocumentGroup_Code'];

						// Cari No Dokumen Terakhir
						$query = "SELECT MAX(CD_SeqNo)
									FROM M_CodeDocument
									WHERE CD_Year='$regyear'
									-- AND CT_Action='DOUT'
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
								  FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad,
								  	   TD_LoanOfLandAcquisitionDocument tdlolad
								  WHERE tdrlolad.TDRLOLAD_THRLOLAD_ID='$h_arr[THRLOLAD_ID]'
								  AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
								  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID";
						$d_sql=mysql_query($d_query);
						while($d_arr=mysql_fetch_array($d_sql)){
							$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);
							// Kode Pengeluaran Dokumen
							$CT_Code="$newnum/DOUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

							switch ($h_arr['THLOLAD_LoanCategoryID']) {
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
							$query1="UPDATE M_DocumentLandAcquisition
									 SET DLA_Status='$docStatus', DLA_Update_UserID='$A_ApproverID',
									 	 DLA_Update_Time=sysdate()
									 WHERE DLA_Code='$d_arr[TDLOLAD_DocCode]'";
							$query2="INSERT INTO M_CodeTransaction
								   	 VALUES (NULL,'$CT_Code','$nnum','DOUT','$Company_Code','$DocumentGroup_Code',
											 '$rmonth','$regyear','$A_ApproverID',sysdate(),
											 '$A_ApproverID',sysdate(),NULL,NULL)";
							$query3="UPDATE TD_ReleaseOfLandAcquisitionDocument
									 SET TDRLOLAD_Code='$CT_Code', TDRLOLAD_ReturnCode='$code',
										 TDRLOLAD_Update_UserID='$A_ApproverID', TDRLOLAD_Update_Time=sysdate()
									 WHERE TDRLOLAD_THRLOLAD_ID='$h_arr[THRLOLAD_ID]'
									 AND TDRLOLAD_TDLOLAD_ID='$d_arr[TDLOLAD_ID]'";

							$mysqli->query($query1);
							$mysqli->query($query2);
							$mysqli->query($query3);
							$nnum=$nnum+1;
						}
						mail_notif_release_doc($A_TransactionCode, $h_arr['THLOLAD_UserID'], 3 );
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
if(isset($_GET['act'])) {
	$act=$decrp->decrypt($_GET['act']);

	if ($act=='confirm'){
		$userID=$decrp->decrypt($_GET['user']);
		$docID=$decrp->decrypt($_GET['doc']);
		$relCode=$decrp->decrypt($_GET['rel']);
		$query = "UPDATE TH_ReleaseOfLandAcquisitionDocument
					SET THRLOLAD_DocumentReceived='1', THRLOLAD_Update_UserID='$userID', THRLOLAD_Update_Time=sysdate()
					WHERE THRLOLAD_ID='$docID'
					AND THRLOLAD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		if($sql){
			mail_notif_reception_release_doc($relCode, $userID, 3 ,1);
			mail_notif_reception_release_doc($relCode, "cust0002", 3 );
			echo "<meta http-equiv='refresh' content='0; url=detail-of-release-land-acquisition-document.php?id=$docID'>";
		}else{
			$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
		}
	}
	else if ($act=='reject'){
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
				<textarea name='txtTHRLOLAD_Reason' id='txtTHRLOLAD_Reason' rows='3'>$arr[THRLOLAD_Reason]</textarea>
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

	else if ($act=='cancel'){
		$userID=$decrp->decrypt($_GET['user']);
		$docID=$decrp->decrypt($_GET['doc']);
		$relCode=$decrp->decrypt($_GET['rel']);

		echo "
		<form name='reason' method='post' action='$PHP_SELF'>
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td style='text-align:left !important'; class='header'>Custodian System</td>
		</tr>
		<tr>
			<td>
				<input type='hidden' name='user_id' value='$userID'>
				<input type='hidden' name='doc_id' value='$docID'>
				<input type='hidden' name='rel_code' value='$relCode'>
				<textarea name='reject_reason' id='reject_reason' rows='3'></textarea>
				<br>*Wajib Diisi Apabila Anda Batal Menerima Dokumen.<br>
			</td>
		</tr>
		<tr>
			<td>
				<center><input name='cancel' type='submit' value='Batal Terima'/></center>
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

if(isset($_POST['cancel'])) {
	$rejectedFlag='2';
	$userID=$_POST['user_id'];
	$docID=$_POST['doc_id'];
	$relCode=$_POST['rel_code'];

	if (str_replace(" ", "", $_POST['reject_reason'])==NULL){
		echo "<meta http-equiv='refresh' content='0; url=act.mail.reldocla.php?act=".$decrp->encrypt('reject').
		"&user=".$decrp->encrypt($userID)."&doc=".$decrp->encrypt($docID)."&rel=".$decrp->encrypt($relCode).">";
	}
	else {
		$rejectReason=str_replace("<br>", "\n",$_POST['reject_reason']);

		$query = "UPDATE TH_ReleaseOfLandAcquisitionDocument
					SET THRLOLAD_DocumentReceived='$rejectedFlag',THRLOLAD_ReasonOfDocumentCancel='$rejectReason', THRLOLAD_Update_UserID='$userID', THRLOLAD_Update_Time=sysdate()
					WHERE THRLOLAD_ID='$docID'
					AND THRLOLAD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		if($sql){
			mail_notif_reception_release_doc($relCode, $userID, 4 ,1);
			mail_notif_reception_release_doc($relCode, "cust0002", 4 );
			echo "<meta http-equiv='refresh' content='0; url=detail-of-release-land-acquisition-document.php?id=$docID'>";
		}else{
			$ActionContent .="<div class='warning'>Konfirmasi Penerimaan Dokumen Gagal. Terjadi kesalahan</div>";
		}
	}
}

if(isset($_POST['reject'])) {
	$A_Status='4';
	$A_ID=$_POST['A_ID'];
	$ARC_RandomCode=$_POST['ARC_RandomCode'];

	if (str_replace(" ", "", $_POST['txtTHRLOLAD_Reason'])==NULL){
		echo "<meta http-equiv='refresh' content='0; url=act.mail.reldocla.php?act=".$decrp->encrypt('reject').">";
	}
	else {
		$THRLOLAD_Reason=str_replace("<br>", "\n",$_POST['txtTHRLOLAD_Reason']);
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
						  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad,TH_LoanOfLandAcquisitionDocument thlolad
						  WHERE thrlolad.THRLOLAD_ReleaseCode='$A_TransactionCode'
						  AND thrlolad.THRLOLAD_Delete_Time IS NULL";
				$h_sql=mysql_query($h_query);
				$h_arr=mysql_fetch_array($h_sql);

				$query1="UPDATE TH_ReleaseOfLandAcquisitionDocument
						 SET THRLOLAD_Status='reject', THRLOLAD_Reason='$THRLOLAD_Reason',
						  	 THRLOLAD_Update_Time=sysdate(), THRLOLAD_Update_UserID='$A_ApproverID'
						 WHERE THRLOLAD_ReleaseCode='$A_TransactionCode'";

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
						  FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad, TD_LoanOfLandAcquisitionDocument tdlolad
						  WHERE tdrlolad.TDRLOLAD_THRLOLAD_ID='$h_arr[THRLOLAD_ID]'
						  AND tdrlolad.TDRLOLAD_Delete_Time IS NULL
						  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID";
				$d_sql=mysql_query($d_query);
				while($d_arr=mysql_fetch_array($d_sql)){
					$query="UPDATE M_DocumentLandAcquisition
						    SET DLA_Status='1', DLA_Update_UserID='$A_ApproverID', DLA_Update_Time=sysdate()
						    WHERE DLA_Code='$d_arr[TDLOLAD_DocCode]'";
					$mysqli->query($query);
				}
				mail_notif_release_doc($A_TransactionCode, $h_arr['THLOLAD_UserID'], 4 );
				mail_notif_release_doc($A_TransactionCode, $h_arr['THRLOLAD_UserID'], 4 );
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

if(!empty($_GET['ret']) && !empty($_GET['rlc']) && !empty($_GET['uid'])){
	$ret=$decrp->decrypt($_GET['ret']);
	if($ret == "masih"){
		$ket = "*Wajib Diisi Apabila Anda Masih Ingin Meminjam Dokumen.";
	}elseif($ret == "tidak"){
		$ket = "*Wajib Diisi Apabila Anda Tidak Ingin Mengembalikan Dokumen.";
	}
	$relCode=$decrp->decrypt($_GET['rlc']);
	$User_ID=$decrp->decrypt($_GET['uid']);

	if(!empty($_POST['reason_reminder_return_doc'])){
		$rejectedFlag='2';
		$userID=$_POST['user_id'];
		$docNeed=$_POST['docNeed'];
		if($docNeed == "masih"){
			$flag = '1';
		}elseif($docNeed == "tidak"){
			$flag = '2';
		}
		$relCode=$_POST['relCode'];

		if (str_replace(" ", "", $_POST['txt_Reason'])==NULL){
			echo "<meta http-equiv='refresh' content='0; url=act.mail.reldocla.php?ret=".$decrp->encrypt($docNeed)."&rlc=".$decrp->encrypt($relCode)."&uid=".$decrp->encrypt($User_ID).">";
		}
		else {
			$reason=str_replace("<br>", "\n",$_POST['txt_Reason']);

			$query = "UPDATE TH_ReleaseOfLandAcquisitionDocument
						SET THRLOLAD_ReminderReturn='$flag', THRLOLAD_ReasonOfDocumentReturn='$reason',
							THRLOLAD_Update_UserID='$userID', THRLOLAD_Update_Time=sysdate()
						WHERE THRLOLAD_ReleaseCode='$relCode'
						AND THRLOLAD_Delete_Time IS NULL";
			$sql = mysql_query($query);
			if($sql){
				$query_get_MD = "SELECT mu.User_ID, mu.User_Name
					FROM M_Role_Approver mra
					INNER JOIN M_Approver ma
						ON mra.RA_ID=ma.Approver_RoleID
						AND ma.Approver_Delete_Time IS NULL
					INNER JOIN M_User mu
						ON ma.Approver_UserID=mu.User_ID
					WHERE mra.RA_Name LIKE 'MD Downstream'
						AND mra.RA_Delete_Time IS NULL";
				$sql_get_MD = mysql_query($query_get_MD);
				$d_MD = mysql_fetch_array($sql_get_MD);
				$MD_ID = $d_MD['User_ID'];

				include ("./include/function.mail.responseOfReturnAllDoc.php");
				mail_response_ret_land_acquisition($relCode, $MD_ID);
				// mail_ret_land_acquisition($relCode);
				echo "
				<table border='0' align='center' cellpadding='0' cellspacing='0'>
				<tbody>
				<tr>
					<td class='header'>Berhasil</td>
				</tr>
				<tr>
					<td>
						Alasan Anda Telah Disimpan.<br>
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
			}else{
				echo "<div class='warning'>Gagal. Terjadi kesalahan<br>Ulangi beberapa saat lagi</div>";
			}
		}
	}else{
		echo "
		<form name='reason' method='post' action='$PHP_SELF'>
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td style='text-align:left !important'; class='header'>Custodian System</td>
		</tr>
		<tr>
			<td><input type='hidden' name='relCode' value='$relCode'>
				<input type='hidden' name='docNeed' value='$ret'>
				<input type='hidden' name='user_id' value='$User_ID'>
				<textarea name='txt_Reason' id='txt_Reason' rows='3'></textarea>
				<br>$ket<br>
			</td>
		</tr>
		<tr>
			<td>
				<center><input name='reason_reminder_return_doc' type='submit' value='Simpan'/></center>
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

if(!empty($_GET['noret']) && !empty($_GET['rlc']) && !empty($_GET['uid'])){
	$noret=$decrp->decrypt($_GET['noret']);
	$relCode=$decrp->decrypt($_GET['rlc']);
	$User_ID=$decrp->decrypt($_GET['uid']);

	if($noret == "confirm"){
		$query="UPDATE TH_ReleaseOfLandAcquisitionDocument
				SET THRLOLAD_ApproveNotReturn='1', THRLOLAD_Update_UserID='$User_ID', THRLOLAD_Update_Time=sysdate()
				WHERE THRLOLAD_ReleaseCode='$relCode'";
		$mysqli->query($query);

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
	}else{
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Persetujuan Berhasil</td>
		</tr>
		<tr>
			<td>
				Anda Telah Menolak Disimpan.<br>
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
?>