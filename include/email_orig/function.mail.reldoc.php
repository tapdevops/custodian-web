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
		
function mail_release_doc($relCode,$reminder=0){ 
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	//$testing='TESTING';

	$e_query="	SELECT  User_ID,User_FullName,User_Email,DocumentGroup_Name,A_TransactionCode,
						ARC_AID,ARC_RandomCode,THROLD_ReleaseDate
				FROM TH_ReleaseOfLegalDocument
				LEFT JOIN TH_LoanOfLegalDocument
					ON THROLD_THLOLD_Code=THLOLD_LoanCode
				LEFT JOIN M_Approval
					ON THROLD_ReleaseCode=A_TransactionCode
					AND A_Delete_Time IS NULL
					AND A_Status='2'
				LEFT JOIN M_DocumentGroup
					ON THLOLD_DocumentGroupID=DocumentGroup_ID
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				WHERE THROLD_ReleaseCode='$relCode'
				AND THROLD_Delete_Time IS NULL";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	$mail->Host       ='10.20.10.3';
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
					
		$ed_query="	SELECT DISTINCT Company_Name,DocumentCategory_Name,DocumentType_Name, 
									DL_NoDoc,THROLD_Reason,THLOLD_UserID,User_FullName
					FROM TH_ReleaseOfLegalDocument
					LEFT JOIN TD_ReleaseOfLegalDocument
						ON TDROLD_THROLD_ID=THROLD_ID
					LEFT JOIN TH_LoanOfLegalDocument
						ON THLOLD_LoanCode=THROLD_THLOLD_Code
					LEFT JOIN TD_LoanOfLegalDocument
						ON TDROLD_TDLOLD_ID=TDLOLD_ID
					LEFT JOIN M_DocumentLegal
						ON DL_DocCode=TDLOLD_DocCode
					LEFT JOIN M_Company
						ON Company_ID=DL_CompanyID
					LEFT JOIN M_DocumentCategory
						ON DocumentCategory_ID=DL_CategoryDocID
					LEFT JOIN M_DocumentType
						ON DocumentType_ID=DL_TypeDocID
					LEFT JOIN M_User
						ON THLOLD_UserID=User_ID
					WHERE THROLD_ReleaseCode='$relCode'
					AND THROLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);	
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			
			$body .= '				
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">	
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								'.$ed_arr->DocumentCategory_Name.'<br />
								'.$ed_arr->DocumentType_Name.'<br />
								No. Dokumen : '.$ed_arr->DL_NoDoc.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$requester=$ed_arr->User_FullName;
		}
		$bodyHeader .= '	
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pengeluaran dokumen '.$row->DocumentGroup_Name.' (berdasarkan permintaan '.$requester.') dengan detail pengeluaran sebagai berikut, membutuhkan persetujuan Bapak/Ibu :</span></p>
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
				<p align=center><span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><a target="_BLANK" href="http://custodian.tap-agri.com/act.mail.reldoc.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a></span>
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><a target="_BLANK" href="http://custodian.tap-agri.com/act.mail.reldoc.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a></span><br />
				</p>
				</div>
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
	
	/*
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
	}*/
	
	if(!$mail->Send()){
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Pengiriman Email Gagal</td>
		</tr>
		<tr>
			<td>
				ERROR<br>
				Terjadi Gangguan Dalam Pengiriman Email.<br>
				Mohon maaf atas ketidaknyamanan ini.<br>
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

function mail_notif_release_doc($relCode, $User_ID, $status){ 
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	//$testing='TESTING';

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$User_ID'";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	$mail->Host       ='10.20.10.3';
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
					
		$ed_query="	SELECT DISTINCT Company_Name,DocumentCategory_Name,DocumentType_Name, 
									DL_NoDoc,THROLD_Reason,THLOLD_UserID,User_FullName
					FROM TH_ReleaseOfLegalDocument
					LEFT JOIN TD_ReleaseOfLegalDocument
						ON TDROLD_THROLD_ID=THROLD_ID
					LEFT JOIN TH_LoanOfLegalDocument
						ON THLOLD_LoanCode=THROLD_THLOLD_Code
					LEFT JOIN TD_LoanOfLegalDocument
						ON TDROLD_TDLOLD_ID=TDLOLD_ID
					LEFT JOIN M_DocumentLegal
						ON DL_DocCode=TDLOLD_DocCode
					LEFT JOIN M_Company
						ON Company_ID=DL_CompanyID
					LEFT JOIN M_DocumentCategory
						ON DocumentCategory_ID=DL_CategoryDocID
					LEFT JOIN M_DocumentType
						ON DocumentType_ID=DL_TypeDocID
					LEFT JOIN M_User
						ON THLOLD_UserID=User_ID
					WHERE THROLD_ReleaseCode='$relCode'
					AND THROLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);	
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			
			$body .= '				
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">	
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								'.$ed_arr->DocumentCategory_Name.'<br />
								'.$ed_arr->DocumentType_Name.'<br />
								No. Dokumen : '.$ed_arr->DL_NoDoc.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$reason=$ed_arr->THROLD_Reason;
			$regUser=$ed_arr->THLOLD_UserID;
			$requester=$ed_arr->User_FullName;
		}
		$bodyHeader .= '	
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pengeluaran dokumen (berdasarkan permintaan '.$requester.') dengan detail pengeluaran sebagai berikut :</span></p>
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
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui. Untuk itu Bapak/Ibu dapat mengambil dokumen di atas ke Departemen Custodian dengan membawa <u>Form Permintaan Dokumen</u>. Terima kasih.  </span><br />
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
	
	/*
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
	}*/
	
	if(!$mail->Send()){
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Pengiriman Email Gagal</td>
		</tr>
		<tr>
			<td>
				ERROR<br>
				Terjadi Gangguan Dalam Pengiriman Email.<br>
				Mohon maaf atas ketidaknyamanan ini.<br>
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

?>