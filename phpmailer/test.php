<?php
class phpmailerAppException extends Exception {
  public function errorMessage() {
    $errorMsg = '<strong>' . $this->getMessage() . "</strong><br>";
    return $errorMsg;
  }
}

/*
try {
  $to = 'pitoyo.suharjo@tap-agri.com';
  if(filter_var($to, FILTER_VALIDATE_EMAIL) === FALSE) {
    throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!<br>");
  }
} catch (phpmailerAppException $e) {
  echo $e->errorMessage();
  return false;
}
*/

require_once("class.phpmailer.php");
include ("config/db_sql.php");
$mail = new PHPMailer();
$query = "select 
			headerLoan.THLOLD_UserID ,
			user.User_FullName, 
			user.user_email, 
			dokumen.DL_DocCode,
			kategori.DocumentCategory_Name,
			dokumen.DL_NoDoc,
			detilRelease.TDROLD_Code,
			DATEDIFF(detilRelease.tdrold_leadtime, now())*(-1) as keterlambatan 
			from TD_ReleaseOfLegalDocument detilRelease 
			left join TD_LoanOfLegalDocument detilLoan  on detilLoan.TDLOLD_THLOLD_ID = detilRelease.TDROLD_TDLOLD_ID
			left join TH_LoanOfLegalDocument headerLoan on headerLoan.THLOLD_ID = detilLoan.TDLOLD_THLOLD_ID
			left join M_User user on user.user_id = headerLoan.THLOLD_UserID
			left join M_DocumentLegal dokumen on dokumen.DL_DocCode = detilLoan.TDLOLD_DocCode
			left join M_DocumentCategory kategori on kategori.DocumentCategory_ID = dokumen.DL_CategoryDocID
			where detilRelease.tdrold_leadtime  < now()
			";
//$handle = mysql_query($sql);
//$row = mysql_fetch_object($handle);


$body = '

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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pendaftaran dokumen -Corporate Legal- berikut membutuhkan persetujuan Bapak/Ibu :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">															
			<TD width="58"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="400"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>
		<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">															
			<TD width="58"  style="font-size: 13px">1</TD>
			<TD width="400"  style="font-size: 13px">PT.Triputra Agro Persada<br>Pendirian Perusahaan Baru</TD>
		</TR>
		</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Untuk itu dimohon Bapak/Ibu dapat memberikan persetujuan pendaftaran dokumen di atas. Terima kasih.  </span><br />
				</p>
				<p align=center><span style="border: 1px solid #ffe222;padding: 5px;background-color: #c4df9b;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><a target="_BLANK" href="http://10.20.1.116/confirm=tolak">Setuju</a></span>
				<span style="border: 1px solid #ffe222;padding: 5px;background-color: #c4df9b;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><a target="_BLANK" href="http://10.20.1.116/confirm=tolak">Tolak</a></span><br />
				</p>
				</div>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br /><b>Departemen Custodian<br />PT Triputra Agro Persada</b>
				</div></td>           
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindak lanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
</table>

';

$mail->IsSMTP();  // telling the class to use SMTP
$mail->SMTPDebug  = 1;
$mail->SMTPAuth   = false;
$mail->Port       = 25;
$mail->Host       ='smtp.tap-agri.com';
$mail->Username   = 'doni.romdoni@tap-agri.com';
$mail->Password   = 'tap123';
$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');

$mail->From       = 'no-reply@tap-agri.com';
$mail->FromName   = 'Custodian System';

$mail->AddAddress('sabrina.davita@tap-agri.com','Sabrina');
//$mail->AddCC('pitoyo.suharjo@tap-agri.com');
$mail->Subject  ='Reminder Pengembalian Dokumen';

require_once('class.html2text.inc.php');
//$h2t =& new html2text($body);
//$mail->AltBody = $h2t->get_text();
//$mail->WordWrap   = 80; // set word wrap

$mail->MsgHTML($body);

//$mail->AddAttachment("images/aikido.gif", "aikido.gif");  // optional name
//$mail->AddAttachment("images/phpmailer.gif", "phpmailer.gif");  // optional name

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

?>