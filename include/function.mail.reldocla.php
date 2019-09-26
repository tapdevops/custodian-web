<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 31 Mei 2012																						=
= Update Terakhir	: 18 Sep 2012																						=
= Revisi			: 18 Sep 2012 - REMINDER APPROVAL																	=
= 					  18 Sep 2012 - PERUBAHAN QUERY MENGGUNAKAN LEFT JOIN												=
=========================================================================================================================
*/
include_once('./phpmailer/class.phpmailer.php');
include_once('./phpmailer/class.html2text.inc.php');
include_once ("./config/db_sql.php");
include_once ("./include/class.endencrp.php");
include_once("./include/class.helper.php");

function mail_release_doc($relCode,$reminder=0){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='';
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query="	SELECT  User_ID,User_FullName,User_Email,DocumentGroup_Name,A_TransactionCode,
						ARC_AID,ARC_RandomCode,THRLOLAD_ReleaseDate,
						CASE WHEN THLOLAD_DocumentType = 'ORIGINAL'
							THEN 'Asli'
							ELSE CONCAT(UCASE(LEFT(THLOLAD_DocumentType, 1)),
								LCASE(SUBSTRING(THLOLAD_DocumentType, 2)))
						END AS Tipe_Dokumen,
						CASE THLOLAD_DocumentType
							WHEN 'HARDCOPY' THEN
								CASE THLOLAD_DocumentWithWatermarkOrNot
									WHEN '1' THEN ' dengan Watermark'
									WHEN '2' THEN ' tanpa Watermark'
								END
							WHEN 'SOFTCOPY' THEN
								CASE THLOLAD_DocumentWithWatermarkOrNot
									WHEN '1' THEN ' dengan Cap'
									WHEN '2' THEN ' tanpa Cap'
								END
							ELSE ''
						END AS Dengan_Cap,
						THLOLAD_Information AS Tujuan
				FROM TH_ReleaseOfLandAcquisitionDocument
				INNER JOIN TH_LoanOfLandAcquisitionDocument
					ON THRLOLAD_THLOLAD_Code=THLOLAD_LoanCode
				INNER JOIN M_Approval
					ON THRLOLAD_ReleaseCode=A_TransactionCode
					AND A_Delete_Time IS NULL
					AND A_Status='2'
				LEFT JOIN M_DocumentGroup
					ON DocumentGroup_ID='3'
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				INNER JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				WHERE THRLOLAD_ReleaseCode='$relCode'
				AND THRLOLAD_Delete_Time IS NULL";
	$handlae = mysql_query($e_query);
	$row = mysql_fetch_object($handlae);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';
	if ($reminder){
		$mail->Subject  ='[REMINDER] '.$testing.' Persetujuan Pengeluaran Dokumen '.$relCode.'';
	}else{
		$mail->Subject  =''.$testing.' Persetujuan Pengeluaran Dokumen '.$relCode.'';
	}
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

   		$ed_query="	SELECT DISTINCT TDLOLAD_ID,Company_Name,DLA_Phase,DLA_Period,
									DLA_Village,DLA_Block,DLA_Owner,User_FullName,
									DLA_AreaStatement,DLA_PlantTotalPrice,THLOLAD_UserID,
									DLA_GrandTotal,DLA_DocDate,THRLOLAD_Reason,
									db_master.M_Employee.Employee_Department,
									db_master.M_Employee.Employee_Division
					FROM TH_ReleaseOfLandAcquisitionDocument
					INNER JOIN TD_ReleaseOfLandAcquisitionDocument
						ON TDRLOLAD_THRLOLAD_ID=THRLOLAD_ID
					INNER JOIN TH_LoanOfLandAcquisitionDocument
						ON THRLOLAD_THLOLAD_Code=THLOLAD_LoanCode
					INNER JOIN TD_LoanOfLandAcquisitionDocument
						ON TDRLOLAD_TDLOLAD_ID=TDLOLAD_ID
					INNER JOIN M_DocumentLandAcquisition
						ON TDLOLAD_DocCode=DLA_Code
					LEFT JOIN M_Company
						ON Company_ID=THLOLAD_CompanyID
					LEFT JOIN M_User
						ON THLOLAD_UserID=User_ID
				    LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
					WHERE THRLOLAD_ReleaseCode='$relCode'
					AND THLOLAD_Delete_Time IS NULL";

		$ed_handlae = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handlae)) {
			$DLA_AreaStatement=number_format($ed_arr->DLA_AreaStatement,2,'.',',');
			$DLA_PlantTotalPrice=number_format($ed_arr->DLA_PlantTotalPrice,2,',','.');
			$DLA_GrandTotal=number_format($ed_arr->DLA_GrandTotal,2,',','.');
			$period=date("j M Y", strtotime($ed_arr->DLA_Period));
			$DocDate=date("j M Y", strtotime($ed_arr->DLA_DocDate));

		$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.' - Tahap '.$ed_arr->DLA_Phase.'<br />
								Periode GRL : '.$period.'<br />
								Desa : '.$ed_arr->DLA_Village.',  Blok : '.$ed_arr->DLA_Block.'<br />
								Pemilik : '.$ed_arr->DLA_Owner.'<br />
								'.$DLA_AreaStatement.' Ha - Rp '.$DLA_PlantTotalPrice.' - Rp '.$DLA_GrandTotal.'<br />
								Tgl. Dokumen : '.$DocDate.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$requester=ucwords(strtolower($ed_arr->User_FullName));
			$requester_dept=ucwords(strtolower($ed_arr->Employee_Department));
			$requester_div=ucwords(strtolower($ed_arr->Employee_Division));
		}
		$bodyHeader .= '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middlae; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pengeluaran dokumen '.$row->DocumentGroup_Name.' '.$row->Tipe_Dokumen.''.$row->Dengan_Cap.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> untuk tujuan '.$row->Tujuan.') dengan detail pengeluaran sebagai berikut, membutuhkan persetujuan Bapak/Ibu :</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Untuk itu dimohon Bapak/Ibu dapat memberikan persetujuan pengeluaran dokumen di atas. Terima kasih.  </span><br />
				</p>
				<p align=center>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a>
					</span>
					<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a>
					</span><br />
				</p>
				</div>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;margin-top:7%">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div></td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
</table>';

	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);

	try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		//echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		//echo $thisError . ': ' . $value;
	  }
	}
}

function mail_notif_release_doc($relCode, $User_ID, $status){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='';
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$User_ID'";
	$handlae = mysql_query($e_query);
	$row = mysql_fetch_object($handlae);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';
	if ($status=='3'){
		$mail->Subject  =''.$testing.' Pengeluaran Dokumen '.$relCode.' Disetujui';
	}
	if ($status=='4'){
		$mail->Subject  =''.$testing.' Pengeluaran Dokumen '.$relCode.' Ditolak';
	}
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

   		$ed_query="	SELECT DISTINCT TDLOLAD_ID,Company_Name,DLA_Phase,DLA_Period,
							DLA_Village,DLA_Block,DLA_Owner,User_FullName,
							DLA_AreaStatement,DLA_PlantTotalPrice,THLOLAD_UserID,
							DLA_GrandTotal,DLA_DocDate,
							THRLOLAD_Reason,THRLOLAD_Information,THRLOLAD_ID,
							CASE WHEN THLOLAD_DocumentType = 'ORIGINAL'
								THEN 'Asli'
								ELSE CONCAT(UCASE(LEFT(THLOLAD_DocumentType, 1)),
	                 			LCASE(SUBSTRING(THLOLAD_DocumentType, 2)))
							END AS Tipe_Dokumen,
							CASE THLOLAD_DocumentType
								WHEN 'HARDCOPY' THEN
									CASE THLOLAD_DocumentWithWatermarkOrNot
										WHEN '1' THEN ' dengan Watermark'
										WHEN '2' THEN ' tanpa Watermark'
									END
								WHEN 'SOFTCOPY' THEN
									CASE THLOLAD_DocumentWithWatermarkOrNot
										WHEN '1' THEN ' dengan Cap'
										WHEN '2' THEN ' tanpa Cap'
									END
								ELSE ''
							END AS Dengan_Cap
					FROM TH_ReleaseOfLandAcquisitionDocument
					INNER JOIN TD_ReleaseOfLandAcquisitionDocument
						ON TDRLOLAD_THRLOLAD_ID=THRLOLAD_ID
					INNER JOIN TH_LoanOfLandAcquisitionDocument
						ON THRLOLAD_THLOLAD_Code=THLOLAD_LoanCode
					INNER JOIN TD_LoanOfLandAcquisitionDocument
						ON TDRLOLAD_TDLOLAD_ID=TDLOLAD_ID
					INNER JOIN M_DocumentLandAcquisition
						ON TDLOLAD_DocCode=DLA_Code
					LEFT JOIN M_Company
						ON Company_ID=THLOLAD_CompanyID
					LEFT JOIN M_User
						ON THLOLAD_UserID=User_ID
					WHERE THRLOLAD_ReleaseCode='$relCode'
					AND THLOLAD_Delete_Time IS NULL";
		$ed_handlae = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handlae)) {
			$DLA_AreaStatement=number_format($ed_arr->DLA_AreaStatement,2,'.',',');
			$DLA_PlantTotalPrice=number_format($ed_arr->DLA_PlantTotalPrice,2,',','.');
			$DLA_GrandTotal=number_format($ed_arr->DLA_GrandTotal,2,',','.');
			$period=date("j M Y", strtotime($ed_arr->DLA_Period));
			$DocDate=date("j M Y", strtotime($ed_arr->DLA_DocDate));

		$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.' - Tahap '.$ed_arr->DLA_Phase.'<br />
								Periode GRL : '.$period.'<br />
								Desa : '.$ed_arr->DLA_Village.',  Blok : '.$ed_arr->DLA_Block.'<br />
								Pemilik : '.$ed_arr->DLA_Owner.'<br />
								'.$DLA_AreaStatement.' Ha - Rp '.$DLA_PlantTotalPrice.' - Rp '.$DLA_GrandTotal.'<br />
								Tgl. Dokumen : '.$DocDate.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$edNum=$edNum+1;
			$tipe_dokumen=$ed_arr->Tipe_Dokumen;
			$dengan_cap=$ed_arr->Dengan_Cap;
			$info=$ed_arr->THRLOLAD_Information;
			$docID=$ed_arr->THRLOLAD_ID;
			$reason=$ed_arr->THRLOLAD_Reason;
			$regUser=$ed_arr->THLOLAD_UserID;
			$requester=$ed_arr->User_FullName;
		}

		$bodyHeader .= '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middlae; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$tipe_dokumen.''.$dengan_cap.' (berdasarkan permintaan '.$requester.' untuk tujuan '.$info.') dengan detail permintaan dokumen sebagai berikut :</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

	if (($status=='3')&&($row->User_ID<>$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui. User yang bersangkutan akan mengambil dokumen di atas ke Departemen Custodian. Terima kasih. </span><br />
				</p>
				</div>';
	}
	if (($status=='3')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p>
					<span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
						Telah Disetujui. Untuk itu Bapak/Ibu dapat mengambil dokumen di atas ke Departemen Custodian.
						Lakukan Konfirmasi segera setelah Bapak/Ibu menerima Dokumen. Terima kasih.
					</span><br />
				</p>
				<p align=center style="margin-bottom: 7%;">
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?act='.$decrp->encrypt('confirm').'&user='.$decrp->encrypt($regUser).'&doc='.$decrp->encrypt($docID).'&rel='.$decrp->encrypt($relCode).'">Sudah Diterima</a>
					</span>
					<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?act='.$decrp->encrypt('cancel').'&user='.$decrp->encrypt($regUser).'&doc='.$decrp->encrypt($docID).'&rel='.$decrp->encrypt($relCode).'">Batal</a>
					</span><br />
				</p>
				</div>';
	}
	if (($status=='4')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Ditolak dengan alasan : '.$reason.'<br>Terima kasih.  </span><br />
				</p>
				</div>';
	}
	if (($status=='4')&&($row->User_ID<>$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Ditolak dengan alasan : '.$reason.'<br>Terima kasih.  </span><br />
				</p>
				</div>';
	}
		$bodyFooter .= '
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div></td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
</table>';

	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);

	try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		//echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		//echo $thisError . ': ' . $value;
	  }
	}
}
function mail_notif_reception_release_doc($relCode, $User_ID, $status,$acceptor=0){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='';
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$User_ID'";
	$handlae = mysql_query($e_query);
	$row = mysql_fetch_object($handlae);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';
	if ($status=='3'){
		$mail->Subject  =''.$testing.' Pengeluaran Dokumen '.$relCode.' Disetujui';
	}
	if ($status=='4'){
		$mail->Subject  =''.$testing.' Pengeluaran Dokumen '.$relCode.' Ditolak';
	}
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

   		$ed_query="	SELECT DISTINCT TDLOLAD_ID,Company_Name,DLA_Phase,DLA_Period,
							DLA_Village,DLA_Block,DLA_Owner,User_FullName,
							DLA_AreaStatement,DLA_PlantTotalPrice,DLA_GrandTotal,DLA_DocDate,
							THLOLAD_UserID,THRLOLAD_Reason,THRLOLAD_Information,
							THLOLAD_DocumentType, THRLOLAD_ReasonOfDocumentCancel,
							CASE THLOLAD_DocumentType
								WHEN 'HARDCOPY' THEN
									CASE THLOLAD_DocumentWithWatermarkOrNot
										WHEN '1' THEN ' dengan Watermark'
										WHEN '2' THEN ' tanpa Watermark'
									END
								WHEN 'SOFTCOPY' THEN
									CASE THLOLAD_DocumentWithWatermarkOrNot
										WHEN '1' THEN ' dengan Cap'
										WHEN '2' THEN ' tanpa Cap'
									END
								ELSE ''
							END AS Dengan_Cap
					FROM TH_ReleaseOfLandAcquisitionDocument
					INNER JOIN TD_ReleaseOfLandAcquisitionDocument
						ON TDRLOLAD_THRLOLAD_ID=THRLOLAD_ID
					INNER JOIN TH_LoanOfLandAcquisitionDocument
						ON THRLOLAD_THLOLAD_Code=THLOLAD_LoanCode
					INNER JOIN TD_LoanOfLandAcquisitionDocument
						ON TDRLOLAD_TDLOLAD_ID=TDLOLAD_ID
					INNER JOIN M_DocumentLandAcquisition
						ON TDLOLAD_DocCode=DLA_Code
					LEFT JOIN M_Company
						ON Company_ID=THLOLAD_CompanyID
					LEFT JOIN M_User
						ON THLOLAD_UserID=User_ID
					WHERE THRLOLAD_ReleaseCode='$relCode'
					AND THLOLAD_Delete_Time IS NULL";
		$ed_handlae = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handlae)) {
			$DLA_AreaStatement=number_format($ed_arr->DLA_AreaStatement,2,'.',',');
			$DLA_PlantTotalPrice=number_format($ed_arr->DLA_PlantTotalPrice,2,',','.');
			$DLA_GrandTotal=number_format($ed_arr->DLA_GrandTotal,2,',','.');
			$period=date("j M Y", strtotime($ed_arr->DLA_Period));
			$DocDate=date("j M Y", strtotime($ed_arr->DLA_DocDate));

		$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.' - Tahap '.$ed_arr->DLA_Phase.'<br />
								Periode GRL : '.$period.'<br />
								Desa : '.$ed_arr->DLA_Village.',  Blok : '.$ed_arr->DLA_Block.'<br />
								Pemilik : '.$ed_arr->DLA_Owner.'<br />
								'.$DLA_AreaStatement.' Ha - Rp '.$DLA_PlantTotalPrice.' - Rp '.$DLA_GrandTotal.'<br />
								Tgl. Dokumen : '.$DocDate.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$edNum=$edNum+1;
			$info=$ed_arr->THRLOLAD_Information;
			$docType=$ed_arr->THLOLAD_DocumentType;
			$dengan_cap=$ed_arr->Dengan_Cap;
			if($docType == "ORIGINAL"){
				$docType = "Asli";
			}else{
				$docType = ucwords(strtolower($docType));
			}
			$reason=$ed_arr->THRLOLAD_Reason;
			$regUser=$ed_arr->THLOLAD_UserID;
			$requester=$ed_arr->User_FullName;
			$reasonCancelAcceptDoc = $ed_arr->THRLOLAD_ReasonOfDocumentCancel;
		}

		$bodyHeader .= '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middlae; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">';
	if($acceptor){
	$bodyHeader .= '<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pengeluaran dokumen '.$docType.''.$dengan_cap.' (berdasarkan permintaan '.$requester.' untuk tujuan '.$info.') dengan detail permintaan dokumen sebagai berikut :</span></p>';
	}
	else{
		$bodyHeader .= '<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$docType.''.$dengan_cap.' (berdasarkan permintaan '.$requester.' untuk tujuan '.$info.') dengan detail permintaan dokumen sebagai berikut :</span></p>';
	}
	$bodyHeader .= '<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
	if($status == 3 && (empty($acceptor) || $acceptor == 0)){
		$bodyFooter .= '
				</TABLE>
			</p>
			<p>
				<span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					Telah diambil oleh user yang bersangkutan dari Custodian Departemen. Terima kasih.
				</span><br />
			</p>
			</div>';
	}
	if($status == 3 && (!empty($acceptor) || $acceptor != 0)){
		$bodyFooter .= '
				</TABLE>
			</p>
			<p>
				<span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					Telah diterima lengkap dan sesuai. Terima kasih.
				</span><br />
			</p>
			</div>';
	}
	if($status == 4 && (empty($acceptor) || $acceptor == 0)){
		$bodyFooter .= '
				</TABLE>
			</p>
			<p>
				<span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					Telah batal menerima dokumen, dengan alasan :<br>
					'.$reasonCancelAcceptDoc.'
				</span><br />
			</p>
			</div>';
	}
	if($status == 4 && (!empty($acceptor) || $acceptor != 0)){
		$bodyFooter .= '
				</TABLE>
			</p>
			<p>
				<span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					Anda telah batal menerima dokumen, dengan alasan :<br>
					'.$reasonCancelAcceptDoc.'
				</span><br />
			</p>
			</div>';
	}
		$bodyFooter .= '
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div></td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
</table>';

	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);

	try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		//echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		//echo $thisError . ': ' . $value;
	  }
	}
}

?>