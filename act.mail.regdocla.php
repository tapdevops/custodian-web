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
include ("./include/function.mail.regdocla.php");
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
		$step=$arr['A_Step']; //Arief F - 30082018
		$AppDate=$arr['A_ApprovalDate'];
		$A_TransactionCode=$arr['A_TransactionCode'];
		$A_ApproverID=$arr['A_ApproverID'];

		$h_query="SELECT *
				  FROM TH_RegistrationOfLandAcquisitionDocument
				  WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
				  AND THRGOLAD_Delete_Time IS NULL";
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

					$qComp = "SELECT Company_Area FROM M_Company WHERE Company_ID = '{$h_arr['THRGOLAD_CompanyID']}'";
					$aComp = mysql_fetch_array(mysql_query($qComp));

					for ($i=$nStep; $i<=$jStep; $i++) {
						$j = $i + 1;
						$query = "
						SELECT rads.RADS_StatusID, ma.A_ApproverID
						FROM M_Approval ma
						JOIN M_Role_ApproverDocStepStatus rads
							ON ma.A_Step = rads.RADS_StepID
						LEFT JOIN M_Role_Approver ra
							ON rads.RADS_RA_ID = ra.RA_ID
						WHERE ma.A_Step = '{$i}'
							AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$aComp["Company_Area"]}')
							AND ma.A_TransactionCode = '{$A_TransactionCode}'
							AND rads.RADS_DocID = '10'
							AND rads.RADS_ProsesID = '1'
						"; //Arief F - 24082018
						$result = mysql_fetch_array(mysql_query($query));

						if ($result['RADS_StatusID'] == '1') {
							//echo 'Step : ' . $i . ' => Kirim Email Approval<br />';
							$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
							$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));
							if ($yquery['abc'] != '0') {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									$xquery = "UPDATE M_Approval
												SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
												WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
									if ($xsql = mysql_query($xquery)) {
										mail_registration_doc($A_TransactionCode);
									}
								}
							} else {
								$query = "UPDATE M_Approval
											SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									mail_registration_doc($A_TransactionCode);
								}
							}

							/************************************
							* Nicholas - 26 Sept 2018			*
							* Fix Bug skip approval				*
							************************************/
							
							/*if ($i == $jStep) {
								$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
									SET THRGOLAD_RegStatus='accept', THRGOLAD_Update_UserID='$A_ApproverID',
										THRGOLAD_Update_Time=sysdate()
									WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
									AND THRGOLAD_Delete_Time IS NULL";
								if ($sql = mysql_query($query)) {
									mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 3, 1 );
									mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
								}
							}*/
							break;
						} else if ($result['RADS_StatusID'] == '2') {
							//echo 'Step : ' . $i . ' => Kirim Email Notifikasi<br />';
							$zquery = mysql_fetch_array(mysql_query("SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode='{$A_TransactionCode}' AND A_Step='$i'"));
							$yquery = mysql_fetch_array(mysql_query("select count(*) as abc from M_Approval WHERE A_ApproverID = '{$zquery['A_ApproverID']}' AND A_Status = '3' AND A_TransactionCode='{$A_TransactionCode}'"));

							if ($yquery['abc'] != '0') {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_Update_UserID='$A_ApproverID', A_ApprovalDate=sysdate(), A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									$xquery = "UPDATE M_Approval
												SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
												WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$j'";
									$xsql = mysql_query($xquery);
								}
							} else {
								$query = "UPDATE M_Approval
											SET A_Status='3', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
											WHERE A_TransactionCode='$A_TransactionCode' AND A_Step='$i'";
								if ($sql = mysql_query($query)) {
									mail_notif_registration_doc($A_TransactionCode, $result['A_ApproverID'], 3);
								}
							}

							/************************************
							* Nicholas - 26 Sept 2018			*
							* Fix Bug skip approval				*
							************************************/
							
							/*if ($i == $jStep) {
								$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
									SET THRGOLAD_RegStatus='accept', THRGOLAD_Update_UserID='$A_ApproverID',
										THRGOLAD_Update_Time=sysdate()
									WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
									AND THRGOLAD_Delete_Time IS NULL";
								if ($sql = mysql_query($query)) {
									mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 3, 1 );
									mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
								}
							}*/
						} else;
					}

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

					/*$query = "UPDATE M_Approval
								SET A_Status='2', A_Update_UserID='$A_ApproverID', A_Update_Time=sysdate()
								WHERE A_TransactionCode='$A_TransactionCode'
								AND A_Step='$nStep'";
					if ($sql = mysql_query($query)) {
						// Kirim Email ke Approver selanjutnya
						mail_registration_doc($A_TransactionCode);
						if($step=='1'){
							mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 3 );
							mail_notif_registration_doc($A_TransactionCode, "cust0002", 3 );
						}
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
					}*/
				}
				else {
					//Query Get All Data Company per Transaction
					$query_get_company = "SELECT DISTINCT
							CASE WHEN td.TDRGOLAD_Core_CompanyID IS NOT NULL
								THEN td.TDRGOLAD_Core_CompanyID
								ELSE th.THRGOLAD_CompanyID
							END AS company_id,
							CASE WHEN td.TDRGOLAD_Core_CompanyID IS NOT NULL
								THEN (SELECT c.Company_Name FROM M_Company c
										WHERE c.Company_ID = td.TDRGOLAD_Core_CompanyID
									)
								ELSE (SELECT c.Company_Name FROM M_Company c
										WHERE c.Company_ID = th.THRGOLAD_CompanyID
									)
							END AS company_name,
							CASE WHEN td.TDRGOLAD_Core_CompanyID IS NOT NULL
								THEN td.TDRGOLAD_Core_Phase
								ELSE th.THRGOLAD_Phase
							END AS tahap,
							td.TDRGOLAD_Core_CompanyID AS td_core_companyid
						FROM TD_RegistrationOfLandAcquisitionDocument td
						LEFT JOIN TH_RegistrationOfLandAcquisitionDocument th
							ON td.TDRGOLAD_THRGOLAD_ID = th.THRGOLAD_ID
						WHERE td.TDRGOLAD_THRGOLAD_ID='$h_arr[THRGOLAD_ID]' AND td.TDRGOLAD_Delete_Time IS NULL";
					$sql_gc = mysql_query($query_get_company);
					$lokasi_dokumen_kosong = 0;
					
					while($arr_gc = mysql_fetch_array($sql_gc)){
						$company_id = $arr_gc['company_id'];
						$company_name = $arr_gc['company_name'];
						
						//JUMLAH ROW
						$additional_query = "";
						if( $arr_gc['td_core_companyid'] != null ){
							$additional_query = "AND tdrgolad.TDRGOLAD_Core_CompanyID='$company_id'";
						}
						$query_jumlah_row = "SELECT DISTINCT thrgolad.THRGOLAD_RegistrationDate, thrgolad.THRGOLAD_UserID,
													  thrgolad.THRGOLAD_CompanyID, thrgolad.THRGOLAD_Phase,
													  thrgolad.THRGOLAD_Period, tdrgolad.TDRGOLAD_ID,
													  tdrgolad.TDRGOLAD_ID, tdrgolad.TDRGOLAD_DocDate,
													  tdrgolad.TDRGOLAD_Block, tdrgolad.TDRGOLAD_Village,
													  tdrgolad.TDRGOLAD_Owner, tdrgolad.TDRGOLAD_AreaClass,
													  tdrgolad.TDRGOLAD_AreaPrice, tdrgolad.TDRGOLAD_AreaStatement,
													  tdrgolad.TDRGOLAD_AreaTotalPrice, tdrgolad.TDRGOLAD_PlantClass,
													  tdrgolad.TDRGOLAD_PlantQuantity, tdrgolad.TDRGOLAD_PlantPrice,
													  tdrgolad.TDRGOLAD_PlantTotalPrice, tdrgolad.TDRGOLAD_GrandTotal,
													  tdrgolad.TDRGOLAD_Information, tdrgolad.TDRGOLAD_Revision
							  FROM TD_RegistrationOfLandAcquisitionDocument tdrgolad,
								   TH_RegistrationOfLandAcquisitionDocument thrgolad
							  WHERE thrgolad.THRGOLAD_ID='$h_arr[THRGOLAD_ID]'
							  AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
							  $additional_query
							  AND thrgolad.THRGOLAD_Delete_Time IS NULL
							  AND tdrgolad.TDRGOLAD_Delete_Time IS NULL";
							$jumlahRow = mysql_num_rows(mysql_query($query_jumlah_row));
							
						// ACTION UNTUK MENENTUKAN JUMLAH LOKASI DOKUMEN YANG TERSEDIA
						$query = "SELECT *
							  FROM L_DocumentLocation
							  WHERE DL_Status='1'
							  AND DL_CompanyID='$company_id'
							  AND DL_DocGroupID='grl'
							  AND DL_Delete_Time is NULL";
						$avLoc = mysql_num_rows(mysql_query($query));
						
						if((!$avLoc)||($avLoc<$jumlahRow)){
							$lokasi_dokumen_kosong++;
							$array_company_name_kosong[] = $company_name;
							$array_banyak_ruang_kosong[] = $avLoc;
						}
					}

					if($lokasi_dokumen_kosong > 0){
						echo "
						<table border='0' align='center' cellpadding='0' cellspacing='0'>
						<tbody>
						<tr>
							<td class='header'>Persetujuan Gagal</td>
						</tr>
						<tr>
							<td>
								";
								for($z = 0; $z < count($array_company_name_kosong); $z++){
								echo "Lokasi Untuk Dokumen ".$array_company_name_kosong[$z]." Tidak Tersedia.<br>
								Lokasi yang Tersedia : ".$array_banyak_ruang_kosong[$z].".<br>";
								}
								echo "
								Hubungi Custodian System Administrator untuk Mengatur Lokasi dan Lakukan Persetujuan Ulang.<br>
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
					else{
						// UPDATE APPROVAL
						$query = "UPDATE M_Approval
									SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
										A_Update_Time=sysdate()
									WHERE A_ID='$A_ID'";
						//$sql = mysql_query($query);

						if ($sql = mysql_query($query)){
							$query1 = "SELECT A_ApproverID FROM M_Approval WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '1'";
							$sql1 = mysql_fetch_array(mysql_query($query1));
							//if($step=='1'){
								mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 3, 1 );
								mail_notif_registration_doc($A_TransactionCode, "cust0002", 3, 1 );
								mail_notif_registration_doc($A_TransactionCode, $sql1['A_ApproverID'], 3, 1);
							//}
						}

						$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
									SET THRGOLAD_RegStatus='accept', THRGOLAD_Update_UserID='$A_ApproverID',
										THRGOLAD_Update_Time=sysdate()
									WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
									AND THRGOLAD_Delete_Time IS NULL";
						if ($sql = mysql_query($query)) {
							// ACTION UNTUK GENERATE NO DOKUMEN
							$regyear=date("y");
							$regmonth=date("m");
							
							$sql_gc = mysql_query($query_get_company);
							while($arr_gc = mysql_fetch_array($sql_gc)){
								$Core_CompanyID = $arr_gc['company_id'];
								$Core_Phase = $arr_gc['tahap'];

								// Cari Kode Perusahaan
								$query = "SELECT *
											FROM M_Company
											WHERE Company_ID='$Core_CompanyID'";
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

								$additional_query = "";
								if( $arr_gc['td_core_companyid'] != null ){
									$additional_query = "AND tdrgolad.TDRGOLAD_Core_CompanyID='$Core_CompanyID'
										AND tdrgolad.TDRGOLAD_Core_Phase='$Core_Phase'";
								}
								$d_query="SELECT DISTINCT thrgolad.THRGOLAD_RegistrationDate, thrgolad.THRGOLAD_UserID,
														  thrgolad.THRGOLAD_CompanyID, thrgolad.THRGOLAD_Phase,
														  thrgolad.THRGOLAD_Period, tdrgolad.TDRGOLAD_ID,
														  tdrgolad.TDRGOLAD_ID, tdrgolad.TDRGOLAD_DocDate,
														  tdrgolad.TDRGOLAD_Block, tdrgolad.TDRGOLAD_Village,
														  tdrgolad.TDRGOLAD_Owner, tdrgolad.TDRGOLAD_AreaClass,
														  tdrgolad.TDRGOLAD_AreaPrice, tdrgolad.TDRGOLAD_AreaStatement,
														  tdrgolad.TDRGOLAD_AreaTotalPrice, tdrgolad.TDRGOLAD_PlantClass,
														  tdrgolad.TDRGOLAD_PlantQuantity, tdrgolad.TDRGOLAD_PlantPrice,
														  tdrgolad.TDRGOLAD_PlantTotalPrice, tdrgolad.TDRGOLAD_GrandTotal,
														  tdrgolad.TDRGOLAD_Information, tdrgolad.TDRGOLAD_Revision
										  FROM TD_RegistrationOfLandAcquisitionDocument tdrgolad,
											   TH_RegistrationOfLandAcquisitionDocument thrgolad
										  WHERE thrgolad.THRGOLAD_ID='$h_arr[THRGOLAD_ID]'
										  AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
										  AND thrgolad.THRGOLAD_Delete_Time IS NULL
										  AND tdrgolad.TDRGOLAD_Delete_Time IS NULL
										  $additional_query";
								$d_sql=mysql_query($d_query);

								while($d_arr=mysql_fetch_array($d_sql)){
									// ACTION UNTUK MENENTUKAN LOKASI DOKUMEN
									$query = "SELECT *
											  FROM L_DocumentLocation
											  WHERE DL_Status='1'
											  AND DL_CompanyID='$Core_CompanyID'
											  AND DL_DocGroupID='grl'
											  AND DL_Delete_Time is NULL
											  AND DL_ID=(SELECT MIN(DL_ID)
														 FROM L_DocumentLocation
														 WHERE DL_Status='1'
														 AND DL_CompanyID='$Core_CompanyID'
														 AND DL_DocGroupID='grl'
														 AND DL_Delete_Time is NULL)";
									$sql = mysql_query($query);
									$arr = mysql_fetch_array($sql);
									$DLIU_LocationCode=$arr['DL_Code'];

									$query = "UPDATE L_DocumentLocation
												SET DL_Status='0', 	DL_Update_UserID='$A_ApproverID',
													DL_Update_Time=sysdate()
												WHERE DL_Code='$DLIU_LocationCode';";
									$newnum=str_pad($nnum,4,"0",STR_PAD_LEFT);
									$CD_Code_H="$newnum$Company_Code$DocumentGroup_Code$regmonth$regyear";
									$sql2= "INSERT INTO M_CodeDocument
											VALUES ('$CD_Code_H','$nnum','$Company_Code','$DocumentGroup_Code','$regmonth',
													'$regyear','$A_ApproverID', sysdate(),'$A_ApproverID',
													sysdate(),NULL,NULL)";
									$mysqli->query($sql2);

									// Memindahkan Pendaftaran Dokumen ke M_DocumentLandAcquisition
									$info = str_replace("<br>", "\n", $d_arr['TDRGOLAD_Information']);
									$sql3= "INSERT INTO M_DocumentLandAcquisition
											VALUES (NULL,
													'$CD_Code_H',
													'$d_arr[THRGOLAD_UserID]',
													'$d_arr[THRGOLAD_RegistrationDate]',
													'$Core_CompanyID',
													'$Core_Phase',
													'$d_arr[THRGOLAD_Period]',
													'$d_arr[TDRGOLAD_Revision]',
													'$d_arr[TDRGOLAD_DocDate]',
													'$d_arr[TDRGOLAD_Block]',
													'$d_arr[TDRGOLAD_Village]',
													'$d_arr[TDRGOLAD_Owner]',
													'$d_arr[TDRGOLAD_AreaClass]',
													'$d_arr[TDRGOLAD_AreaStatement]',
													'$d_arr[TDRGOLAD_AreaPrice]',
													'$d_arr[TDRGOLAD_AreaTotalPrice]',
													'$d_arr[TDRGOLAD_PlantClass]',
													'$d_arr[TDRGOLAD_PlantQuantity]',
													'$d_arr[TDRGOLAD_PlantPrice]',
													'$d_arr[TDRGOLAD_PlantTotalPrice]',
													'$d_arr[TDRGOLAD_GrandTotal]',
													'$info',
													'$DLIU_LocationCode','1', NULL,
													'$A_ApproverID', sysdate(),'$A_ApproverID',
													sysdate(),NULL,NULL);";

									if(($mysqli->query($sql3)) && ($mysqli->query($query)) ){
										$s_sql="SELECT *
												FROM M_DocumentLandAcquisition
												WHERE DLA_ID='$CD_Code_H'";

										$s_query=mysql_query($s_sql);
										$s_arr=mysql_fetch_array($s_query);
										$DLA_ID=$s_arr['DLA_ID'];

										$dd_query="SELECT DISTINCT TDRGOLADD_AttibuteID,
																  TDRGOLADD_AttributeStatusID
												  FROM TD_RegistrationOfLandAcquisitionDocumentDetail
												  WHERE TDRGOLADD_TDRGOLAD_ID='$d_arr[TDRGOLAD_ID]'
												  AND TDRGOLADD_Delete_Time IS NULL";
										$dd_sql=mysql_query($dd_query);

										while ($dd_arr=mysql_fetch_array($dd_sql)){
											$dnewnum=str_pad($dd_arr['TDRGOLADD_AttibuteID'],2,"0",STR_PAD_LEFT);
											$CD_Code="$newnum$dnewnum$Company_Code$DocumentGroup_Code$regmonth$regyear";

											$i_sql="INSERT INTO M_DocumentLandAcquisitionAttribute
													VALUES (NULL,'$CD_Code','$DLA_ID','$dd_arr[TDRGOLADD_AttibuteID]',
															'$dd_arr[TDRGOLADD_AttributeStatusID]',
															'1','$A_ApproverID', sysdate(),'$A_ApproverID',
															sysdate(),NULL,NULL)";
											$mysqli->query($i_sql);
										}
									}
									$nnum=$nnum+1;
								}
							}
						}
						
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
if(isset($_GET['act'])) { //Arief F - 30082018
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
				<textarea name='txtTHRGOLAD_Reason' id='txtTHRGOLAD_Reason' rows='3'></textarea>
				<br>*Wajib Diisi Apabila Anda Tidak Menyetujui Registrasi Dokumen.<br>
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

if(isset($_POST['reject'])) { //Arief F - 30082018
	$A_Status='4';
	$A_ID=$_POST['A_ID'];
	$ARC_RandomCode=$_POST['ARC_RandomCode'];

	if (str_replace(" ", "", $_POST['txtTHRGOLAD_Reason'])==NULL){
		echo "<meta http-equiv='refresh' content='0; url=act.mail.regdocla.php?act=".$decrp->encrypt('reject').">";
	}
	else {
		$THRGOLAD_Reason=str_replace("<br>", "\n",$_POST['txtTHRGOLAD_Reason']);
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
						  FROM TH_RegistrationOfLandAcquisitionDocument
						  WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'
						  AND THRGOLAD_Delete_Time IS NULL";
				$h_sql=mysql_query($h_query);
				$h_arr=mysql_fetch_array($h_sql);

				$query = "UPDATE TH_RegistrationOfLandAcquisitionDocument
						  SET THRGOLAD_RegStatus='reject', THRGOLAD_RegStatusReason='$THRGOLAD_Reason',
							  THRGOLAD_Update_Time=sysdate(), THRGOLAD_Update_UserID='$A_ApproverID'
						  WHERE THRGOLAD_RegistrationCode='$A_TransactionCode'";

				// UPDATE APPROVAL
				$query1 = "UPDATE M_Approval
							SET A_Status='$A_Status', A_ApprovalDate=sysdate(), A_Update_UserID='$A_ApproverID',
								A_Update_Time=sysdate()
							WHERE A_ID='$A_ID'";

				$query2 = "UPDATE M_Approval
						   SET A_Update_Time=sysdate(), A_Update_UserID='$A_ApproverID',
							   A_Delete_Time=sysdate(), A_Delete_UserID='$A_ApproverID',
							   A_Status='$A_Status'
						   WHERE A_TransactionCode='$A_TransactionCode'
						   AND A_Step>='$step'";
				if (($sql = mysql_query($query)) && ($sql1 = mysql_query($query1)) && ($sql2 = mysql_query($query2))) {
					mail_notif_registration_doc($A_TransactionCode, $h_arr['THRGOLAD_UserID'], 4 );
					$e_query="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$A_TransactionCode'
							  AND A_Step<'$step' ";
					$e_sql=mysql_query($e_query);
					while ($e_arr=mysql_fetch_array($e_sql)){
						mail_notif_registration_doc($A_TransactionCode, $e_arr['A_ApproverID'], 4 );
					}
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
