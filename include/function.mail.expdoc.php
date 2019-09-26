<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Sep 2018																						=
= Update Terakhir	: 																									=
= Revisi			: 																									=
=========================================================================================================================
*/
include_once('./phpmailer/class.phpmailer.php');
include_once('./phpmailer/class.html2text.inc.php');
include_once ("./config/db_sql.php");
include_once ("./include/class.endencrp.php");
include_once("./include/class.helper.php");

function mail_exp_legal($docCode,$User_ID,$docList){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$grupDok = "Legal/Lisensi";

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
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';
	$mail->Subject  ='Notifikasi Masa Berlaku Dokumen '.$docCode;
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>Kode Dokumen : '.$docList[$i]['DocCode'].'<br>
						'.$docList[$i]["Company_Name"].'<br />
						'.$docList[$i]["DocumentCategory_Name"].'<br />
						'.$docList[$i]["DocumentType_Name"].'<br />
						No. Dokumen	: '.$docList[$i]["DL_NoDoc"].'<br />
						Tgl. Expired Dokumen : '.$docList[$i]["ExpTime"].'
					</TD>
				</TR>';
	}
	$bodyHeader = '
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa ada dokumen expired dengan detail sebagai berikut :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter = '
				</TABLE>
			</p>
			<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pembaharuan dokumen.<br /> Terima kasih.  </span><br />
			</p>
			<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
			</div>
			<p align=center style="margin-bottom: 7%;">
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.expdoc.php?act='.$decrp->encrypt('accept').'&uadm='.$decrp->encrypt($User_ID).'&gd='.$decrp->encrypt($grupDok).'">Diperbaharui</a>
				</span>
				<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.expdoc.php?act='.$decrp->encrypt('noneed').'&uadm='.$decrp->encrypt($User_ID).'&gd='.$decrp->encrypt($grupDok).'">Tidak Perlu Diperbaharui</a>
				</span><br />
			</p>
			</div>';
		$bodyFooter .= '
				</td>
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

function mail_exp_legal_update($responser, $User_ID){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$responser'";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

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
	$mail->Subject  ='Notifikasi Pembaharuan Masa Berlaku Dokumen Legal/Lisensi';
	$mail->AddBcc('system.administrator@tap-agri.com');

	$query = "SELECT mdl.DL_RegUserID User_ID,mdl.DL_DocCode DocCode,mdl.DL_NoDoc,
					DATE_FORMAT(mdl.DL_ExpDate,'%d %M %Y') ExpTime,
					mc.Company_Name,mdc.DocumentCategory_Name,mdt.DocumentType_Name,
					'Legal/Lisensi' AS GrupDok,
					u.User_FullName AS nama_pendaftar
				FROM M_DocumentLegal mdl
				LEFT JOIN M_DocumentCategory mdc
					ON mdl.DL_CategoryDocID=mdc.DocumentCategory_ID
				LEFT JOIN M_DocumentType mdt
					ON mdt.DocumentType_ID=mdl.DL_TypeDocID
				LEFT JOIN M_Company mc
					ON mc.Company_ID=mdl.DL_CompanyID
				LEFT JOIN M_User u
					ON u.User_ID = mdl.DL_RegUserID
				WHERE mdl.DL_ExpDate IS NOT NULL
					AND mdl.DL_RegUserID='$User_ID'
					AND mdl.DL_Delete_Time IS NULL
					AND mdl.DL_StatusReminderExpired IS NULL
					AND (
						(UPPER(TRIM(DocumentCategory_Name))='HGU' AND DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH)=CURDATE()
							AND
							(
								(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH)=CURDATE()) OR
								(DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
								(DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
								(DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
								(DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
								(DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
								(DATE_ADD(DATE_SUB(mdl.DL_ExpDate,INTERVAL 63 MONTH), INTERVAL 6 DAY)=CURDATE())
							)
						)
						OR
						(
							(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdl.DL_ExpDate, INTERVAL 1 MONTH), INTERVAL 6 DAY)=CURDATE())
						)
					)
				ORDER BY mdl.DL_RegUserID ASC";

	$sql = mysql_query($query);
	$i = 1;
	while($data = mysql_fetch_array($sql)){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>Kode Dokumen : '.$data['DocCode'].'<br>
						'.$data["Company_Name"].'<br />
						'.$data["DocumentCategory_Name"].'<br />
						'.$data["DocumentType_Name"].'<br />
						No. Dokumen	: '.$data["DL_NoDoc"].'<br />
						Tgl. Expired Dokumen : '.$data["ExpTime"].'
					</TD>
				</TR>';
		$nama_pendaftar = $data['nama_pendaftar'];
	}
	$data = null;
	$bodyHeader = '
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
		Bersama ini disampaikan bahwa '.$nama_pendaftar.', ingin memperharui dokumen dengan detail sebagai berikut :
	</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter .= '
				</TABLE>
			</p>
			<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pembaharuan dokumen.<br /> Terima kasih.  </span><br />
			</p>
			<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
			</div>
				</td>
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
