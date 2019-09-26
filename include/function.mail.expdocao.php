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

function mail_exp_asset_ownership($grupDok,$User_ID,$docList){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

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
	$mail->Subject  ='Notifikasi Masa Berlaku Dokumen '.$grupDok;
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>
						Kode Dokumen : '.$docList[$i]['DocCode'].'<br>
						No. Polisi : '.$docList[$i]["DAO_NoPolisi"].'<br />
						Nama Pemilik : '.$docList[$i]["nama_pemilik"].'<br>
						Merk Kendaraan : '.$docList[$i]["MK_Name"].'<br>
						Masa Berlaku STNK : '.$docList[$i]["STNKExpTime"].'<br>
						Masa Berlaku Pajak : '.$docList[$i]["PajakExpTime"].'<br>
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
					<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.expdocao.php?act='.$decrp->encrypt('accept').'&uadm='.$decrp->encrypt($User_ID).'&gd='.$decrp->encrypt($grupDok).'">Diperbaharui</a>
				</span>
				<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.expdocao.php?act='.$decrp->encrypt('noneed').'&uadm='.$decrp->encrypt($User_ID).'&gd='.$decrp->encrypt($grupDok).'">Tidak Perlu Diperbaharui</a>
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

function mail_exp_asset_ownership_update($responser, $Admin_Region){
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
	$mail->Subject  ='Notifikasi Pembaharuan Masa Berlaku Dokumen Kepemilikan Aset';
	$mail->AddBcc('system.administrator@tap-agri.com');

	$query_get_user = "SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$Admin_Region'";
	$sql_gu = mysql_query($query_get_user);
	$du = mysql_fetch_array($sql_gu);
	$nama_admin = $du['User_FullName'];

	$query = "SELECT mdao.DAO_DocCode DocCode,
					mdao.DAO_NoPolisi,
					ma.Approver_UserID User_ID,
					m_mk.MK_Name,
					CASE WHEN co.Company_Area = 'KALTIM'
						THEN CASE
							WHEN co.Company_Code IN ('EBL', 'NPN', 'DLJ')
								THEN 'ADMIN - KALTIM 1'
							WHEN co.Company_Code IN ('PTA', 'KAM', 'SAWA', 'KSD', 'HPM', 'MSL')
								THEN 'ADMIN - KALTIM 2'
							ELSE 'ADMIN - KALTIM 3'
							END
						ELSE
							CONCAT('ADMIN - ', co.Company_Area)
					END Admin_Region,
					CASE WHEN mdao.DAO_Employee_NIK LIKE 'CO@%'
					  THEN
					  	(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(mdao.DAO_Employee_NIK, 'CO@', ''))
					  ELSE
						(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=mdao.DAO_Employee_NIK)
					END nama_pemilik,
					CASE WHEN mdao.DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
						WHEN mdao.DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
						ELSE DATE_FORMAT(mdao.DAO_STNK_ExpiredDate, '%d/%m/%Y')
					END AS STNKExpTime,
					CASE WHEN mdao.DAO_Pajak_ExpiredDate LIKE '%0000-00-00%' THEN '-'
						WHEN mdao.DAO_Pajak_ExpiredDate LIKE '%1970-01-01%' THEN '-'
						ELSE DATE_FORMAT(mdao.DAO_Pajak_ExpiredDate, '%d/%m/%Y')
					END AS PajakExpTime,
					'Kepemilikan Aset' AS GrupDok
				FROM M_DocumentAssetOwnership mdao
				INNER JOIN db_master.M_MerkKendaraan m_mk
					ON mdao.DAO_MK_ID=m_mk.MK_ID
				INNER JOIN M_Company co
					ON co.Company_ID = mdao.DAO_CompanyID
				INNER JOIN M_Role_Approver mra
					ON (co.Company_Area!='-' AND mra.RA_Name = CASE WHEN co.Company_Area = 'KALTIM'
						THEN CASE
							WHEN co.Company_Code IN ('EBL', 'NPN', 'DLJ')
								THEN 'ADMIN - KALTIM 1'
							WHEN co.Company_Code IN ('PTA', 'KAM', 'SAWA', 'KSD', 'HPM', 'MSL')
								THEN 'ADMIN - KALTIM 2'
							ELSE 'ADMIN - KALTIM 3'
							END
						ELSE
							CONCAT('ADMIN - ', co.Company_Area)
						END)
					AND mra.RA_Delete_Time IS NULL
				INNER JOIN M_Approver ma
					ON mra.RA_ID=ma.Approver_RoleID
					AND ma.Approver_UserID = '$Admin_Region'
					AND ma.Approver_Delete_Time IS NULL
				WHERE mdao.DAO_Delete_Time IS NULL
					AND mdao.DAO_StatusReminderExpired IS NULL
					AND (
						(
							(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_STNK_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 6 DAY)=CURDATE())
						)
						OR
						(
							(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 1 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 2 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 3 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 4 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 5 DAY)=CURDATE()) OR
							(DATE_ADD(DATE_SUB(mdao.DAO_Pajak_ExpiredDate, INTERVAL 1 MONTH), INTERVAL 6 DAY)=CURDATE())
						)
					)
				ORDER BY ma.Approver_UserID ASC";

	$sql = mysql_query($query);
	$i = 1;
	while($data = mysql_fetch_array($sql)){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>
						Kode Dokumen : '.$data['DocCode'].'<br>
						No. Polisi : '.$data["DAO_NoPolisi"].'<br />
						Nama Pemilik : '.$data["nama_pemilik"].'<br>
						Merk Kendaraan : '.$data["MK_Name"].'<br>
						Masa Berlaku STNK : '.$data["STNKExpTime"].'<br>
						Masa Berlaku Pajak : '.$data["PajakExpTime"].'<br>
					</TD>
				</TR>';
		$Admin_Region = $data['Admin_Region'];
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
		Bersama ini disampaikan bahwa '.$nama_admin.' sebagai '.$Admin_Region.', ingin memperharui dokumen dengan detail sebagai berikut :
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
