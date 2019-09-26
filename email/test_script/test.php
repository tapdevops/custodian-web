<?php
class phpmailerAppException extends Exception {
  public function errorMessage() {
    $errorMsg = '<strong>' . $this->getMessage() . "</strong><br>";
    return $errorMsg;
  }
}

try {
  $to = 'pitoyo.suharjo@tap-agri.com';
  if(filter_var($to, FILTER_VALIDATE_EMAIL) === FALSE) {
    throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!<br>");
  }
} catch (phpmailerAppException $e) {
  echo $e->errorMessage();
  return false;
}

require_once("../class.phpmailer.php");

$mail = new PHPMailer();

$body = 'Test 11';

$mail->IsSMTP();  // telling the class to use SMTP
$mail->SMTPDebug  = 1;
$mail->SMTPAuth   = true;
$mail->Port       = 25;
$mail->Host       ='smtp.tap-agri.com';
$mail->Username   = 'doni.romdoni@tap-agri.com';
$mail->Password   = 'tap123';
$mail->AddReplyTo('doni.romdoni@tap-agri.com','doni romdoni');

$mail->From       = 'doni.romdoni@tap-agri.com';
$mail->FromName   = 'doni romdoni';

$mail->AddAddress('pitoyo.suharjo@tap-agri.com','pitoyo');
$mail->AddCC('doni.romdoni@tap-agri.com');
$mail->Subject  =' Test 9 (PHPMailer test using SMTP)';

require_once('../class.html2text.inc.php');
$h2t =& new html2text($body);
$mail->AltBody = $h2t->get_text();
$mail->WordWrap   = 80; // set word wrap

$mail->MsgHTML($body);

$mail->AddAttachment("images/aikido.gif", "aikido.gif");  // optional name
$mail->AddAttachment("images/phpmailer.gif", "phpmailer.gif");  // optional name

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