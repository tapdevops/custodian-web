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

function mail_ret_legal($relCode,$User_ID,$docList,$userData,$AtasanID=-1,$MD=-1,$lastReminder=-1){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;

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
	$mail->Subject  ='Notifikasi Pengembalian Dokumen '.$relCode;
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	$requester=ucwords(strtolower($userData["User_FullName"]));
	$requester_dept=ucwords(strtolower($userData["Employee_Department"]));
	$requester_div=ucwords(strtolower($userData["Employee_Division"]));
	$documentGroupName=ucwords(strtolower($userData["DocumentGroup_Name"]));
	$body="";
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$docList[$i]["Company_Name"].'<br />
						'.$docList[$i]["DocumentCategory_Name"].'<br />
						'.$docList[$i]["DocumentType_Name"].'<br />
						No. Dokumen		: '.$docList[$i]["DL_NoDoc"].'<br />
						Tgl.Pengeluaran	: '.$docList[$i]["RelTime"].'
					</TD>
				</TR>';
			$flag = $docList[$i]['flag'];
			$alasan = $docList[$i]['alasan'];
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$documentGroupName.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengembalian sebagai berikut, telah melewati batas waktu pengembalian :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter ='';
		if($MD == -1){
			if($AtasanID==-1){ //Si pengaju akan mengisi alasan
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pengembalian dokumen.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>';
					if($flag == NULL){
						$bodyFooter .= '
						<p align=center style="margin-bottom: 7%;">
							<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoc.php?ret='.$decrp->encrypt('masih').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Masih Dipinjam</a>
							</span>
							<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoc.php?ret='.$decrp->encrypt('tidak').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Dikembalikan</a>
							</span><br />
						</p>';
					}
					$bodyFooter .= '</div>';
			}
			else{
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk menginformasikan '.$requester.'.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>
					</div>';
			}

		}else{ //Email ke MD
			if($flag == "1"){
				$ket_to_md = "Namun masih ingin meminjam dokumen dengan alasan :<br>".$alasan.".<br>";
			}elseif($flag == "2"){
				$ket_to_md = "Namun tidak ingin mengembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
			}else{
				$ket_to_md = "Mohon kerjasamanya untuk menginformasikan ".$requester." untuk segera mengembalikan dokumen.<br />";
			}
			$bodyFooter = '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					'.$ket_to_md.' Terima kasih.
				</span><br />
				</p>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div>
				</div>';
		}
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

function mail_ret_land_acquisition($relCode,$User_ID,$docList,$userData,$AtasanID=-1,$MD=-1,$lastReminder=-1){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;

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
	$mail->Subject  ='Notifikasi Pengembalian Dokumen '.$relCode;
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	$requester=ucwords(strtolower($userData["User_FullName"]));
	$requester_dept=ucwords(strtolower($userData["Employee_Department"]));
	$requester_div=ucwords(strtolower($userData["Employee_Division"]));
	$documentGroupName=ucwords(strtolower($userData["DocumentGroup_Name"]));
	$body="";
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		$DLA_AreaStatement=number_format($docList[$i]["DLA_AreaStatement"],2,'.',',');
		$DLA_PlantTotalPrice=number_format($docList[$i]["DLA_PlantTotalPrice"],2,',','.');
		$DLA_GrandTotal=number_format($docList[$i]["DLA_GrandTotal"],2,',','.');

		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$docList[$i]["Company_Name"].' - Tahap '.$docList[$i]["DLA_Phase"].'<br />
						Periode GRL : '.$docList[$i]["DLA_Period"].'<br />
						Desa : '.$docList[$i]["DLA_Village"].',  Blok : '.$docList[$i]["DLA_Block"].'<br />
						Pemilik : '.$docList[$i]["DLA_Owner"].'<br />
						'.$DLA_AreaStatement.' Ha - Rp '.$DLA_PlantTotalPrice.' - Rp '.$DLA_GrandTotal.'<br />
						Tgl. Dokumen : '.$docList[$i]["RelTime"].'
					</TD>
				</TR>';
			$flag = $docList[$i]['flag'];
			$alasan = $docList[$i]['alasan'];
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
		<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$documentGroupName.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengeluaran sebagai berikut, telah melewati batas waktu pengembalian :</span></p>
		<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter ='';
		if($MD == -1){
			if($AtasanID==-1){ //Si pengaju akan mengisi alasan
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pengembalian dokumen.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>';
					if($flag == NULL){
						$bodyFooter .= '
						<p align=center style="margin-bottom: 7%;">
							<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?ret='.$decrp->encrypt('masih').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Masih Dipinjam</a>
							</span>
							<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?ret='.$decrp->encrypt('tidak').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Dikembalikan</a>
							</span><br />
						</p>';
					}
					$bodyFooter .= '</div>';
			}
			else{
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk menginformasikan '.$requester.'.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>
					</div>';
			}
		}else{ //Email ke MD
			if($flag == "1"){
				$ket_to_md = "Namun masih ingin meminjam dokumen dengan alasan :<br>".$alasan.".<br>";
			}elseif($flag == "2"){
				$ket_to_md = "Namun tidak ingin mengembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
			}else{
				$ket_to_md = "Mohon kerjasamanya untuk menginformasikan ".$requester." untuk segera mengembalikan dokumen.<br />";
			}
			$bodyFooter = '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					'.$ket_to_md.' Terima kasih.
				</span><br />
				</p>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div>
				</div>';
		}
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

function mail_ret_asset_ownership($relCode,$User_ID,$docList,$userData,$AtasanID=-1,$MD=-1,$lastReminder=-1){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;

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
	$mail->Subject  ='Notifikasi Pengembalian Dokumen '.$relCode;
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	$requester=ucwords(strtolower($userData["User_FullName"]));
	$requester_dept=ucwords(strtolower($userData["Employee_Department"]));
	$requester_div=ucwords(strtolower($userData["Employee_Division"]));
	$documentGroupName=ucwords(strtolower($userData["DocumentGroup_Name"]));
	$body="";
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>
						No. Polisi : '.$docList[$i]["DAO_NoPolisi"].'<br />
						Nama Pemilik : '.$docList[$i]["OwnerName"].'<br>
						Merk Kendaraan : '.$docList[$i]["VehicleBrand"].'<br>
						Masa Berlaku STNK : '.$docList[$i]["DAO_STNK_StartDate"].' s/d '.$docList[$i]["DAO_STNK_ExpiredDate"].'<br>
						Tanggal Pengeluaran : '.$docList[$i]["RelTime"].'<br>
					</TD>
				</TR>';
		$flag = $docList[$i]['flag'];
		$alasan = $docList[$i]['alasan'];
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$documentGroupName.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengeluaran sebagai berikut, telah melewati batas waktu pengembalian :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter ='';
		if($MD == -1){
			if($AtasanID==-1){ //Si pengaju akan mengisi alasan
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pengembalian dokumen.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>';
				if($flag == NULL){
					$bodyFooter .= '
					<p align=center style="margin-bottom: 7%;">
						<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
							<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocao.php?ret='.$decrp->encrypt('masih').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Masih Dipinjam</a>
						</span>
						<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
							<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocao.php?ret='.$decrp->encrypt('tidak').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Dikembalikan</a>
						</span><br />
					</p>';
				}
					$bodyFooter .= '</div>';
			}
			else{ //Email ke Atasan
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk menginformasikan '.$requester.'.<br /> Terima kasih. </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>
					</div>';
			}
		}else{ //Email ke MD
			if($flag == "1"){
				$ket_to_md = "Namun masih ingin meminjam dokumen dengan alasan :<br>".$alasan.".<br>";
			}elseif($flag == "2"){
				$ket_to_md = "Namun tidak ingin mengembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
			}else{
				$ket_to_md = "Mohon kerjasamanya untuk menginformasikan ".$requester." untuk segera mengembalikan dokumen.<br />";
			}
			$bodyFooter = '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					'.$ket_to_md.' Terima kasih.
				</span><br />
				</p>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div>
				</div>';
		}
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

function mail_ret_other_legal($relCode,$User_ID,$docList,$userData,$AtasanID=-1,$MD=-1,$lastReminder=-1){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;

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
	$mail->Subject  ='Notifikasi Pengembalian Dokumen '.$relCode;
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	$requester=ucwords(strtolower($userData["User_FullName"]));
	$requester_dept=ucwords(strtolower($userData["Employee_Department"]));
	$requester_div=ucwords(strtolower($userData["Employee_Division"]));
	$documentGroupName=ucwords(strtolower($userData["DocumentGroup_Name"]));
	$body="";
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		if(strpos($docList[$i]["DOL_TglTerbit"], '0000-00-00') !== false || strpos($docList[$i]["DOL_TglTerbit"], '1970-01-01') !== false){
			$tgl_terbit = "-";
		}else{
			$tgl_terbit = date('d/m/Y', strtotime($docList[$i]["DOL_TglTerbit"]));
		}
		if(strpos($docList[$i]["DOL_TglBerakhir"], '0000-00-00') !== false || strpos($docList[$i]["DOL_TglBerakhir"], '1970-01-01') !== false){
			$tgl_berakhir_dok = "-";
		}else{
			$tgl_berakhir_dok = date('d/m/Y', strtotime($docList[$i]["DOL_TglBerakhir"]));
		}
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$docList[$i]["DOL_NamaDokumen"].'<br />
						'.$docList[$i]["DocumentCategory_Name"].'<br />
						'.$docList[$i]["DOL_InstansiTerkait"].'<br />
						No. Dokumen : '.$docList[$i]["DOL_NoDokumen"].'<br />
						Tgl. Terbit Dokumen : '.$tgl_terbit.' s/d '.$tgl_berakhir_dok.'
					</TD>
				</TR>';
			$flag = $docList[$i]['flag'];
			$alasan = $docList[$i]['alasan'];
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$documentGroupName.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengeluaran sebagai berikut, telah melewati batas waktu pengembalian :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter ='';
		if($MD == -1){
			if($AtasanID==-1){ //Si pengaju akan mengisi alasan
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pengembalian dokumen.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>';
					if($flag == NULL){
						$bodyFooter .= '
						<p align=center style="margin-bottom: 7%;">
							<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocol.php?ret='.$decrp->encrypt('masih').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Masih Dipinjam</a>
							</span>
							<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocol.php?ret='.$decrp->encrypt('tidak').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Dikembalikan</a>
							</span><br />
						</p>';
					}
				$bodyFooter .= '</div>';
			}
			else{
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk menginformasikan '.$requester.'.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>
					</div>';
			}
		}else{ //Email ke MD
			if($flag == "1"){
				$ket_to_md = "Namun masih ingin meminjam dokumen dengan alasan :<br>".$alasan.".<br>";
			}elseif($flag == "2"){
				$ket_to_md = "Namun tidak ingin mengembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
			}else{
				$ket_to_md = "Mohon kerjasamanya untuk menginformasikan ".$requester." untuk segera mengembalikan dokumen.<br />";
			}
			$bodyFooter = '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					'.$ket_to_md.' Terima kasih.
				</span><br />
				</p>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div>
				</div>';
		}
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

function mail_ret_other_non_legal($relCode,$User_ID,$docList,$userData,$AtasanID=-1,$MD=-1,$lastReminder=-1){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;

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
	$mail->Subject  ='Notifikasi Pengembalian Dokumen '.$relCode;
	
	$mail->AddBcc('system.administrator@tap-agri.com');
	$requester=ucwords(strtolower($userData["User_FullName"]));
	$requester_dept=ucwords(strtolower($userData["Employee_Department"]));
	$requester_div=ucwords(strtolower($userData["Employee_Division"]));
	$documentGroupName=ucwords(strtolower($userData["DocumentGroup_Name"]));
	$body="";
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$docList[$i]['Company_Name'].'<br />
						Departemen : '.$docList[$i]['Department_Name'].'<br />
						Nama Dokumen : '.$docList[$i]['DONL_NamaDokumen'].'<br />
						No. Dokumen : '.$docList[$i]['DONL_NoDokumen'].'<br />
						Tahun Dokumen : '.date('Y', strtotime($docList[$i]['DONL_TahunDokumen'])).'
					</TD>
				</TR>';
			$flag = $docList[$i]['flag'];
			$alasan = $docList[$i]['alasan'];
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$documentGroupName.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengeluaran sebagai berikut, telah melewati batas waktu pengembalian :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter ='';
		if($MD == -1){
			if($AtasanID==-1){ //Si pengaju akan mengisi alasan
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pengembalian dokumen.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>';
					if($flag == NULL){
						$bodyFooter .= '
						<p align=center style="margin-bottom: 7%;">
							<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoconl.php?ret='.$decrp->encrypt('masih').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Masih Dipinjam</a>
							</span>
							<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
								<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoconl.php?ret='.$decrp->encrypt('tidak').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Dikembalikan</a>
							</span><br />
						</p>';
					}
				$bodyFooter .= '</div>';
			}
			else{
				$bodyFooter = '
						</TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk menginformasikan '.$requester.'.<br /> Terima kasih.  </span><br />
					</p>
					<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
					</div>
					</div>';
			}
		}else{ //Email ke MD
			if($flag == "1"){
				$ket_to_md = "Namun masih ingin meminjam dokumen dengan alasan :<br>".$alasan.".<br>";
			}elseif($flag == "2"){
				$ket_to_md = "Namun tidak ingin mengembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
			}else{
				$ket_to_md = "Mohon kerjasamanya untuk menginformasikan ".$requester." untuk segera mengembalikan dokumen.<br />";
			}
			$bodyFooter = '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					'.$ket_to_md.' Terima kasih.
				</span><br />
				</p>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div>
				</div>';
		}
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
