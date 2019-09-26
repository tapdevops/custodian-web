<?php
class phpmailerAppException extends Exception {
  public function errorMessage() {
    $errorMsg = '<strong>' . $this->getMessage() . "</strong><br>";
    return $errorMsg;
  }
}

require_once("phpmailer/class.phpmailer.php");
require_once('phpmailer/class.html2text.inc.php');
include ("config/config_db.php");
$mail = new PHPMailer();


//setting email header
$mail->IsSMTP();  // telling the class to use SMTP
$mail->SMTPDebug  = 1;
$mail->SMTPAuth   = false;
$mail->Port       = 25;
$mail->Host       ='smtp.tap-agri.com';
//$mail->Username   = '@tap-agri.com';
//$mail->Password   = '';
$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
$mail->From       = 'no-reply@tap-agri.com';
$mail->FromName   = 'Custodian System';
$mail->Subject  ='Reminder Pengembalian Dokumen';
$mail->AddBcc('system.administrator@tap-agri.com');
//$mail->AddAttachment("images/aikido.gif", "aikido.gif");  // optional name
//$mail->AddAttachment("images/phpmailer.gif", "phpmailer.gif");  // optional name

$query = "
SELECT DISTINCT headerLoan.THLOLD_UserID UserID,user.User_FullName FullName,user.User_Email Email
FROM TD_ReleaseOfLegalDocument detilRelease 
LEFT JOIN TD_LoanOfLegalDocument detilLoan ON detilLoan.TDLOLD_ID = detilRelease.TDROLD_TDLOLD_ID
LEFT JOIN TH_LoanOfLegalDocument headerLoan ON headerLoan.THLOLD_ID = detilLoan.TDLOLD_THLOLD_ID
LEFT JOIN M_User user ON user.user_id = headerLoan.THLOLD_UserID
WHERE detilRelease.tdrold_leadtime  < now()
AND detilRelease.tdrold_leadtime <> '0000-00-00 00:00:00'
UNION
SELECT DISTINCT headerLoan.THLOLAD_UserID UserID, user.User_FullName FullName, user.User_Email Email
FROM TD_ReleaseOfLandAcquisitionDocument detilRelease
LEFT JOIN TD_LoanOfLandAcquisitionDocument detilLoan ON detilLoan.TDLOLAD_ID = detilRelease.TDRLOLAD_TDLOLAD_ID
LEFT JOIN TH_LoanOfLandAcquisitionDocument headerLoan ON headerLoan.THLOLAD_ID = detilLoan.TDLOLAD_THLOLAD_ID
LEFT JOIN M_User user ON user.user_id = headerLoan.THLOLAD_UserID
WHERE detilRelease.tdrlolad_leadtime < now( )
AND detilRelease.tdrlolad_leadtime <> '0000-00-00 00:00:00'
";
$handle = mysql_query($query);
$numRows = mysql_num_rows($handle);

if ($numRows){		
	while ($row = mysql_fetch_object($handle)) {
		/*try {
		  if(filter_var($row->User_Email, FILTER_VALIDATE_EMAIL) === FALSE) {
			throw new phpmailerAppException("Email address " . $row->User_Email . " is invalid -- aborting!<br>");
		  }
		} catch (phpmailerAppException $e) {
		  echo $e->errorMessage();
		  return false;
		}*/
		$body = '';
		//header email
		$body .= '	
		<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
			<tbody>
			<tr>
				<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
			</tr>
			<tr>
				<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
			<tbody>
			<tr>
				<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->FullName.',</div>
				<div style="margin-bottom: 15px">
				<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa Bapak/Ibu belum mengembalikan beberapa dokumen berikut : </span></p>
				<p>
					<TABLE  width="458" >
					<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">															
						<TD width="40%"  style="font-size: 13px"><strong>Dokumen</strong></TD>
						<TD width="30%"  style="font-size: 13px"><strong>Grup Dokumen</strong></TD>
						<TD width="30%"  style="font-size: 13px"><strong>Keterlambatan</strong></TD>
					</TR>
		'; 
			
			
		$query2="SELECT dokumen.DL_DocCode KodeDokumen, grup.DocumentGroup_Name KetDokumen,DATEDIFF(detilRelease.tdrold_leadtime, now())*(-1) as Keterlambatan 
				 FROM TD_ReleaseOfLegalDocument detilRelease 
				 LEFT JOIN TD_LoanOfLegalDocument detilLoan  ON detilLoan.TDLOLD_THLOLD_ID = detilRelease.TDROLD_TDLOLD_ID
				 LEFT JOIN TH_LoanOfLegalDocument headerLoan ON headerLoan.THLOLD_ID = detilLoan.TDLOLD_THLOLD_ID
				 LEFT JOIN M_User user ON user.user_id = headerLoan.THLOLD_UserID
				 LEFT JOIN M_DocumentLegal dokumen ON dokumen.DL_DocCode = detilLoan.TDLOLD_DocCode
				 LEFT JOIN M_DocumentGroup grup ON grup.DocumentGroup_ID=dokumen.DL_GroupDocID
				 WHERE detilRelease.tdrold_leadtime  < now()
				 AND detilRelease.tdrold_leadtime <> '0000-00-00 00:00:00'
				 AND headerLoan.THLOLD_UserID = ".$row->UserID."
				 UNION
				 SELECT dokumen.DLA_Code KodeDokumen, grup.DocumentGroup_Name KetDokumen,DATEDIFF(detilRelease.tdrlolad_leadtime, now())*(-1) as Keterlambatan 
				 FROM TD_ReleaseOfLandAcquisitionDocument detilRelease 
				 LEFT JOIN TD_LoanOfLandAcquisitionDocument detilLoan  ON detilLoan.TDLOLAD_THLOLAD_ID = detilRelease.TDRLOLAD_TDLOLAD_ID
				 LEFT JOIN TH_LoanOfLandAcquisitionDocument headerLoan ON headerLoan.THLOLAD_ID = detilLoan.TDLOLAD_THLOLAD_ID
				 LEFT JOIN M_User user ON user.user_id = headerLoan.THLOLAD_UserID
				 LEFT JOIN M_DocumentLandAcquisition dokumen ON dokumen.DLA_Code = detilLoan.TDLOLAD_DocCode
				 LEFT JOIN M_DocumentGroup grup ON grup.DocumentGroup_ID='3'
				 WHERE detilRelease.tdrlolad_leadtime  < now()
				 AND detilRelease.tdrlolad_leadtime <> '0000-00-00 00:00:00'
				 AND headerLoan.THLOLAD_UserID = ".$row->UserID."				 
				 ORDER BY Keterlambatan DESC, KodeDokumen";
		$handle2 = mysql_query($query2);
		while ($row2 = mysql_fetch_object($handle2)) {
			$body .= '
						<TR  style=" font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD width="40%" style="font-size: 13px" align=center>'.$row2->KodeDokumen.'</TD>
							<TD width="30%" style="font-size: 13px" align=center>'.$row2->KetDokumen.'</TD>
							<TD width="30%" style="font-size: 13px" align=center>'.$row2->Keterlambatan.' Hari</TD>
						</TR>
			';
		}
	
		//footer email
		$body .= '	</TABLE>
							</p>
							<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Diharapkan Bapak/Ibu dapat segera mengembalikan dokumen tersebut demi kelancaran kegiatan perusahaan. Terima kasih. </span><br />
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
						<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah dikembalikan.<br />
						<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
					</tr>
				</tbody>
			</table>
		
		';
	//echo $row->Email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->Email,$row->FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($body);
	
	/*
	try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		echo $thisError . ': ' . $value;
	  }
	}
	*/
	
	}
}

?>