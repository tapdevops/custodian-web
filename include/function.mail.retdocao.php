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

function mail_return_doc($retDoc, $reminder=0){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='';
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query="	SELECT  User_ID,User_FullName,User_Email,DocumentGroup_Name,A_TransactionCode,
						ARC_AID,ARC_RandomCode
				FROM TD_ReturnOfAssetOwnershipDocument
				LEFT JOIN M_DocumentAssetOwnership
					ON TDRTOAOD_DocCode=DAO_DocCode
				LEFT JOIN M_Approval
					ON TDRTOAOD_ReturnCode=A_TransactionCode
					AND A_Delete_Time IS NULL
					AND A_Status='2'
				LEFT JOIN M_DocumentGroup
					ON DocumentGroup_ID='4'
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				WHERE TDRTOAOD_ReturnCode='$retDoc'
				AND TDRTOAOD_Delete_Time IS NULL";
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
	if ($reminder){
		$mail->Subject  ='[REMINDER] '.$testing.' Persetujuan Pengembalian Dokumen '.$retDoc.'';
	}else{
		$mail->Subject  =''.$testing.' Persetujuan Pengembalian Dokumen '.$retDoc.'';
	}
	
	$mail->AddBcc('system.administrator@tap-agri.com');

		$ed_query="	SELECT DISTINCT Company_Name,
							TDRTOAOD_UserID,User_FullName,
							db_master.M_Employee.Employee_Department,
							db_master.M_Employee.Employee_Division,
							CASE WHEN DAO_Employee_NIK LIKE 'CO@%'
		  					  THEN
		  						(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(DAO_Employee_NIK, 'CO@', ''))
		  					  ELSE
		  						(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=DAO_Employee_NIK)
		  				  	END nama_pemilik,
	 					    m_mk.MK_Name merk_kendaraan, DAO_NoPolisi,
							CASE WHEN DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
								WHEN DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(DAO_STNK_StartDate, '%d/%m/%Y')
							END AS start_stnk,
							CASE WHEN DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
								WHEN DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
								ELSE DATE_FORMAT(DAO_STNK_ExpiredDate, '%d/%m/%Y')
							END AS expired_stnk
					FROM TD_ReturnOfAssetOwnershipDocument
					LEFT JOIN M_DocumentAssetOwnership
						ON DAO_DocCode=TDRTOAOD_DocCode
					LEFT JOIN M_Company
						ON Company_ID=DAO_CompanyID
					LEFT JOIN M_User
						ON TDRTOAOD_UserID=User_ID
					LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
					LEFT JOIN db_master.M_MerkKendaraan m_mk
                        ON DAO_MK_ID=m_mk.MK_ID
					WHERE TDRTOAOD_ReturnCode='$retDoc'
					AND TDRTOAOD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								No. Polisi : '.$ed_arr->DAO_NoPolisi.'<br />
								Nama Pemilik : '.$ed_arr->nama_pemilik.'<br>
								Merk Kendaraan : '.$ed_arr->merk_kendaraan.'<br>
								Masa Berlaku STNK : '.$ed_arr->start_stnk.' s/d
								'.$ed_arr->expired_stnk.'
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
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
	</tr>
	<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
	<tbody>
	<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pengembalian dokumen '.$row->DocumentGroup_Name.' (berdasarkan pengajuan dari <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengembalian sebagai berikut, membutuhkan persetujuan Bapak/Ibu :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

		$bodyFooter .= '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Untuk itu dimohon Bapak/Ibu dapat memberikan persetujuan pengembalian dokumen di atas. Terima kasih.  </span><br />
				</p>
				<p align=center>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.retdocao.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a>
					</span>
					<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.retdocao.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a>
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

function mail_notif_return_doc($retCode, $User_ID, $status){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='';
	$body='';
    $bodyHeader='';
    $bodyFooter='';

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
	if ($status=='3'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Pengembalian Dokumen '.$retCode;
	}
	if ($status=='4'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Pengembalian Dokumen '.$retCode;
	}
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

		$ed_query="	SELECT DISTINCT Company_Name, TDRTOAOD_Information,
						TDRTOAOD_Reason, TDRTOAOD_UserID,TDRTOAOD_ID,User_FullName,
						CASE WHEN DAO_Employee_NIK LIKE 'CO@%'
						  THEN
						   	 (SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(DAO_Employee_NIK, 'CO@', ''))
						  ELSE
							 (SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=DAO_Employee_NIK)
						END nama_pemilik,
						DAO_Employee_NIK,
 					    m_mk.MK_Name merk_kendaraan, DAO_NoPolisi,
						CASE WHEN DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
							WHEN DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
							ELSE DATE_FORMAT(DAO_STNK_StartDate, '%d/%m/%Y')
						END AS start_stnk,
						CASE WHEN DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
							WHEN DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
							ELSE DATE_FORMAT(DAO_STNK_ExpiredDate, '%d/%m/%Y')
						END AS expired_stnk
					FROM TD_ReturnOfAssetOwnershipDocument
					LEFT JOIN M_DocumentAssetOwnership
						ON DAO_DocCode=TDRTOAOD_DocCode
					LEFT JOIN M_Company
						ON Company_ID=DAO_CompanyID
					LEFT JOIN M_User
						ON TDRTOAOD_UserID=User_ID
					LEFT JOIN db_master.M_MerkKendaraan m_mk
                        ON DAO_MK_ID=m_mk.MK_ID
					WHERE TDRTOAOD_ReturnCode='$retCode'
					AND TDRTOAOD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								No. Polisi : '.$ed_arr->DAO_NoPolisi.'<br />
								Nama Pemilik : '.$ed_arr->nama_pemilik.'<br>
								Merk Kendaraan : '.$ed_arr->merk_kendaraan.'<br>
								Masa Berlaku STNK : '.$ed_arr->start_stnk.' s/d
								'.$ed_arr->expired_stnk.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$info=$ed_arr->TDRTOAOD_Information;
			$docID=$ed_arr->TDRTOAOD_ID;
			$reason=$ed_arr->TDRTOAOD_Reason;
			$regUser=$ed_arr->TDRTOAOD_UserID;
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen di bawah ini (berdasarkan pengajuan dari '.$requester.' dengan keterangan '.$info.') dengan detail pengembalian dokumen sebagai berikut :</span></p>
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
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui. User yang bersangkutan telah mengembalikan dokumen di atas ke Departemen Custodian. Terima kasih. </span><br />
				</p>
				</div>';
	}
	if (($status=='3')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Dokumen di atas telah disetujui dan diterima oleh Departemen Custodian. Terima kasih.  </span><br />
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