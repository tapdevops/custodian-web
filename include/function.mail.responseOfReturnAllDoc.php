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

function mail_response_ret_legal($relCode, $User_ID){
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
	$mail->Subject  ='Persetujuan Belum Mengembalikan Dokumen '.$relCode;
	// 
	$mail->AddBcc('system.administrator@tap-agri.com');
	$body="";

    $queryLegal = "SELECT tdrold.TDROLD_ID,tdrold.TDROLD_Insert_UserID UserID,mdl.DL_DocCode DocCode,
					FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrold.TDROLD_LeadTime)/7)+1) ReminderLevel,
					CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrold.TDROLD_LeadTime)/7)+1) > 3
						THEN '3'
						ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrold.TDROLD_LeadTime)/7)+1)
					END	ReminderLevel,
					throld.THROLD_ReleaseCode RelCode,DATE_FORMAT(tdrold.TDROLD_Insert_Time, '%d %M %Y') RelTime,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDROLD_Insert_UserID) SupervisorID,
					mc.Company_Name,mdc.DocumentCategory_Name,
					mdt.DocumentType_Name,mdl.DL_NoDoc,mdg.DocumentGroup_Name,
					mu.User_FullName,me.Employee_Department,me.Employee_Division,
					throld.THROLD_ReminderReturn flag, throld.THROLD_ReasonOfLegalDocumentReturn alasan
				FROM TD_ReleaseOfLegalDocument tdrold
				LEFT JOIN TH_ReleaseOfLegalDocument throld
					ON tdrold.TDROLD_THROLD_ID=throld.THROLD_ID
					AND throld.THROLD_ApproveNotReturn IS NULL
					AND throld.THROLD_Delete_Time IS NULL
				LEFT JOIN TH_LoanOfLegalDocument thlold
					ON thlold.THLOLD_LoanCode=throld.THROLD_THLOLD_Code
				LEFT JOIN TD_LoanOfLegalDocument tdlold
					ON tdrold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				INNER JOIN M_User mu
					ON tdrold.TDROLD_Insert_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				INNER JOIN M_DocumentLegal mdl
					ON mdl.DL_DocCode=tdlold.TDLOLD_DocCode
					AND mdl.DL_Delete_Time IS NULL
				LEFT JOIN M_Company mc
					ON mc.Company_ID=mdl.DL_CompanyID
				LEFT JOIN M_DocumentCategory mdc
					ON mdc.DocumentCategory_ID=mdl.DL_CategoryDocID
				LEFT JOIN M_DocumentType mdt
					ON mdt.DocumentType_ID=mdl.DL_TypeDocID
				LEFT JOIN M_DocumentGroup mdg
					ON mdg.DocumentGroup_ID=mdl.DL_GroupDocID
				LEFT JOIN db_master.M_Employee me
					ON mu.User_ID = me.Employee_NIK
				LEFT JOIN TD_ReturnOfLegalDocument tdrtold
					ON tdlold.TDLOLD_DocCode=tdrtold.TDRTOLD_DocCode
					AND tdrtold.TDRTOLD_Delete_Time IS NULL
				WHERE throld.THROLD_Delete_Time IS NULL
					AND tdrtold.TDRTOLD_ID IS NULL
					AND tdrold.TDROLD_LeadTime NOT LIKE '%1970-01-01%'
					AND thlold.THLOLD_LoanCategoryID=1
					AND CURDATE()>=tdrold.TDROLD_LeadTime
				    AND throld.THROLD_ReleaseCode='$relCode'";
    $sqlLegal = mysql_query($queryLegal);
    $i = 0;
    while($data = mysql_fetch_array($sqlLegal)){

		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$data["Company_Name"].'<br />
						'.$data["DocumentCategory_Name"].'<br />
						'.$data["DocumentType_Name"].'<br />
						No. Dokumen		: '.$data["DL_NoDoc"].'<br />
						Tgl.Pengeluaran	: '.$data["RelTime"].'
					</TD>
				</TR>';
		$flag = $data['flag'];
		$alasan = $data['alasan'];
        $requester=ucwords(strtolower($data["User_FullName"]));
    	$requester_dept=ucwords(strtolower($data["Employee_Department"]));
    	$requester_div=ucwords(strtolower($data["Employee_Division"]));
    	$documentGroupName=ucwords(strtolower($data["DocumentGroup_Name"]));
	}
    if($flag == "1"){
        $ket_to_md = "masih ingin dipinjam dokumen dengan alasan :<br>".$alasan.".<br>";
    }elseif($flag == "2"){
        $ket_to_md = "tidak ingin dikembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
    }else{
        $ket_to_md = "";
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
        Bersama ini disampaikan bahwa dokumen '.$documentGroupName.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) '.$ket_to_md.' Dengan detail sebagai berikut :
    </span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter = '
				</TABLE>
			</p>
			<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Silahkan untuk menyetujui atau tidak dari permintaan tersebut.<br /> Terima kasih.  </span><br />
			</p>
			<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
			</div>
			<p align=center style="margin-bottom: 7%;">
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoc.php?noret='.$decrp->encrypt('confirm').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Setuju</a>
				</span>
				<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoc.php?noret='.$decrp->encrypt('reject').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Setuju</a>
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

function mail_response_ret_land_acquisition($relCode, $User_ID){
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
	$mail->Subject  ='Persetujuan Belum Mengembalikan Dokumen '.$relCode;
	// 
	$mail->AddBcc('system.administrator@tap-agri.com');
	$body="";

    $queryLandAcquisition = "SELECT tdrlolad.TDRLOLAD_ID,tdrlolad.TDRLOLAD_Insert_UserID UserID,thrlolad.THRLOLAD_ReleaseCode RelCode,mdla.DLA_Code DocCode,
					CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrlolad.TDRLOLAD_LeadTime)/7)+1) > 3
						THEN '3'
						ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdrlolad.TDRLOLAD_LeadTime)/7)+1)
					END	ReminderLevel,
					DATE_FORMAT(tdrlolad.TDRLOLAD_Insert_Time, '%d %M %Y') RelTime,
					COALESCE(mu.User_SPV2,mu.User_SPV1,tdrlolad.TDRLOLAD_Insert_UserID) SupervisorID,
					mdla.DLA_AreaStatement,mdla.DLA_PlantTotalPrice,mdla.DLA_GrandTotal,mdla.DLA_Phase,
					mdla.DLA_Period,mdla.DLA_Village,mdla.DLA_Block,mdla.DLA_Owner,mdla.DLA_DocDate,
					mc.Company_Name,mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division,
					thrlolad.THRLOLAD_ReminderReturn flag, thrlolad.THRLOLAD_ReasonOfDocumentReturn alasan
				FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad
				LEFT JOIN TH_ReleaseOfLandAcquisitionDocument thrlolad
					ON thrlolad.THRLOLAD_ID = tdrlolad.TDRLOLAD_THRLOLAD_ID
					AND thrlolad.THRLOLAD_ApproveNotReturn IS NULL
					AND thrlolad.THRLOLAD_Delete_Time IS NULL
				LEFT JOIN TD_LoanOfLandAcquisitionDocument tdlolad ON tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
					AND tdlolad.TDLOLAD_Delete_Time IS NULL
				INNER JOIN M_User mu
					ON tdrlolad.TDRLOLAD_Insert_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				INNER JOIN M_DocumentLandAcquisition mdla
					ON mdla.DLA_Code=tdlolad.TDLOLAD_DocCode
					AND mdla.DLA_Delete_Time IS NULL
				LEFT JOIN M_Company mc
					ON mc.Company_ID=mdla.DLA_CompanyID
				LEFT JOIN M_DocumentGroup mdg
					ON mdg.DocumentGroup_ID=3
				LEFT JOIN db_master.M_Employee me
					ON mu.User_ID = me.Employee_NIK
				LEFT JOIN TH_LoanOfLandAcquisitionDocument thlolad ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
					AND thlolad.THLOLAD_Delete_Time IS NULL
				LEFT JOIN TD_ReturnOfLandAcquisitionDocument tdrtolad
					ON tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
					AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
				WHERE TDRLOLAD_Delete_Time IS NULL
					AND tdrtolad.TDRTOLAD_ID IS NULL
					AND tdrlolad.TDRLOLAD_LeadTime NOT LIKE '%1970-01-01%'
					AND thlolad.THLOLAD_LoanCategoryID=1
					AND CURDATE()>=TDRLOLAD_LeadTime
				    AND thrlolad.THRLOLAD_ReleaseCode='$relCode'";
    $sqlLandAcquisition = mysql_query($queryLandAcquisition);
    $i = 0;
    while($data = mysql_fetch_array($sqlLandAcquisition)){
        $DLA_AreaStatement=number_format($data["DLA_AreaStatement"],2,'.',',');
		$DLA_PlantTotalPrice=number_format($data["DLA_PlantTotalPrice"],2,',','.');
		$DLA_GrandTotal=number_format($data["DLA_GrandTotal"],2,',','.');

		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$data["Company_Name"].' - Tahap '.$data["DLA_Phase"].'<br />
						Periode GRL : '.$data["DLA_Period"].'<br />
						Desa : '.$data["DLA_Village"].',  Blok : '.$data["DLA_Block"].'<br />
						Pemilik : '.$data["DLA_Owner"].'<br />
						'.$DLA_AreaStatement.' Ha - Rp '.$DLA_PlantTotalPrice.' - Rp '.$DLA_GrandTotal.'<br />
						Tgl. Dokumen : '.$data["RelTime"].'
					</TD>
				</TR>';
		$flag = $data['flag'];
		$alasan = $data['alasan'];
        $requester=ucwords(strtolower($data["User_FullName"]));
    	$requester_dept=ucwords(strtolower($data["Employee_Department"]));
    	$requester_div=ucwords(strtolower($data["Employee_Division"]));
    	// $documentGroupName=ucwords(strtolower($data["DocumentGroup_Name"]));
	}
    if($flag == "1"){
        $ket_to_md = "masih ingin dipinjam dokumen dengan alasan :<br>".$alasan.".<br>";
    }elseif($flag == "2"){
        $ket_to_md = "tidak ingin dikembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
    }else{
        $ket_to_md = "";
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
        Bersama ini disampaikan bahwa dokumen Pembebasan Lahan (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) '.$ket_to_md.' Dengan detail sebagai berikut :
    </span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter = '
				</TABLE>
			</p>
			<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Silahkan untuk menyetujui atau tidak dari permintaan tersebut.<br /> Terima kasih.  </span><br />
			</p>
			<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
			</div>
			<p align=center style="margin-bottom: 7%;">
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?noret='.$decrp->encrypt('confirm').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Setuju</a>
				</span>
				<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocla.php?noret='.$decrp->encrypt('reject').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Setuju</a>
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

function mail_response_ret_asset_ownership($relCode, $User_ID){
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
	$mail->Subject  ='Persetujuan Belum Mengembalikan Dokumen '.$relCode;
	// 
	$mail->AddBcc('system.administrator@tap-agri.com');
	$body="";

    $queryAssetOwnership = "SELECT tdroaod.TDROAOD_ID,
					CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroaod.TDROAOD_LeadTime)/7)+1) > 3
						THEN '3'
						ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroaod.TDROAOD_LeadTime)/7)+1)
					END	ReminderLevel,
					tdroaod.TDROAOD_Insert_UserID UserID, throaod.THROAOD_ReleaseCode RelCode,
					mdao.DAO_DocCode DocCode,
					DATE_FORMAT(tdroaod.TDROAOD_Insert_Time, '%d %M %Y') RelTime,
					COALESCE(mu.User_SPV2, mu.User_SPV1, TDROAOD_Insert_UserID) SupervisorID,
					DATE_FORMAT(mdao.DAO_STNK_StartDate, '%d %M %Y') DAO_STNK_StartDate,
					DATE_FORMAT(mdao.DAO_STNK_ExpiredDate, '%d %M %Y') DAO_STNK_ExpiredDate,
					mdao.DAO_NoPolisi,
					CASE WHEN mdao.DAO_Employee_NIK LIKE 'CO@%'
  					  THEN
  						(SELECT mc_on.Company_Name FROM M_Company mc_on WHERE mc_on.Company_code = REPLACE(mdao.DAO_Employee_NIK, 'CO@', ''))
  					  ELSE
  						(SELECT me_on.Employee_FullName FROM db_master.M_Employee me_on WHERE me_on.Employee_NIK=mdao.DAO_Employee_NIK)
  				  	END OwnerName,
					m_mk.MK_Name VehicleBrand,
					mu.User_FullName,
					me.Employee_Department,
					me.Employee_Division,
					throaod.THROAOD_ReminderReturn flag, throaod.THROAOD_ReasonOfDocumentReturn alasan
				FROM TD_ReleaseOfAssetOwnershipDocument tdroaod
				LEFT JOIN TH_ReleaseOfAssetOwnershipDocument throaod ON tdroaod.TDROAOD_THROAOD_ID=throaod.THROAOD_ID
					AND throaod.THROAOD_Delete_Time IS NULL
				LEFT JOIN TD_LoanOfAssetOwnershipDocument tdloaod ON tdroaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
					AND tdloaod.TDLOAOD_Delete_Time IS NULL
				INNER JOIN M_User mu
					ON tdloaod.TDLOAOD_Insert_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				INNER JOIN M_DocumentAssetOwnership mdao
					ON mdao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
					AND mdao.DAO_Delete_Time IS NULL
				LEFT JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
					AND thloaod.THLOAOD_Delete_Time IS NULL
				LEFT JOIN db_master.M_Employee me
					ON mu.User_ID = me.Employee_NIK
				LEFT JOIN db_master.M_MerkKendaraan m_mk
					ON mdao.DAO_MK_ID=m_mk.MK_ID
                LEFT JOIN TD_ReturnOfAssetOwnershipDocument tdrtoaod
					ON tdloaod.TDLOAOD_DocCode=tdrtoaod.TDRTOAOD_DocCode
					AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL
				WHERE tdroaod.TDROAOD_Delete_Time IS NULL
                    AND tdrtoaod.TDRTOAOD_ID IS NULL
					AND tdroaod.TDROAOD_LeadTime NOT LIKE '%1970-01-01%'
					AND thloaod.THLOAOD_LoanCategoryID=1
					AND tdroaod.TDROAOD_LeadTime<=CURDATE()
				    AND throaod.THROAOD_ReleaseCode='$relCode'";
    $sqlAssetOwnership = mysql_query($queryAssetOwnership);
    $i = 0;
    while($data = mysql_fetch_array($sqlAssetOwnership)){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>
						No. Polisi : '.$data["DAO_NoPolisi"].'<br />
						Nama Pemilik : '.$data["OwnerName"].'<br>
						Merk Kendaraan : '.$data["VehicleBrand"].'<br>
						Masa Berlaku STNK : '.$data["DAO_STNK_StartDate"].' s/d '.$data["DAO_STNK_ExpiredDate"].'<br>
						Tanggal Pengeluaran : '.$data["RelTime"].'<br>
					</TD>
				</TR>';
		$flag = $data['flag'];
		$alasan = $data['alasan'];
        $requester=ucwords(strtolower($data["User_FullName"]));
    	$requester_dept=ucwords(strtolower($data["Employee_Department"]));
    	$requester_div=ucwords(strtolower($data["Employee_Division"]));
    	// $documentGroupName=ucwords(strtolower($data["DocumentGroup_Name"]));
	}
    if($flag == "1"){
        $ket_to_md = "masih ingin dipinjam dokumen dengan alasan :<br>".$alasan.".<br>";
    }elseif($flag == "2"){
        $ket_to_md = "tidak ingin dikembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
    }else{
        $ket_to_md = "";
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
        Bersama ini disampaikan bahwa dokumen Kepemilikan Aset (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) '.$ket_to_md.' Dengan detail sebagai berikut :
    </span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter = '
				</TABLE>
			</p>
			<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Silahkan untuk menyetujui atau tidak dari permintaan tersebut.<br /> Terima kasih.  </span><br />
			</p>
			<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
			</div>
			<p align=center style="margin-bottom: 7%;">
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocao.php?noret='.$decrp->encrypt('confirm').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Setuju</a>
				</span>
				<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocao.php?noret='.$decrp->encrypt('reject').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Setuju</a>
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

function mail_response_ret_other_legal($relCode, $User_ID){
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
	$mail->Subject  ='Persetujuan Belum Mengembalikan Dokumen '.$relCode;
	// 
	$mail->AddBcc('system.administrator@tap-agri.com');
	$body="";

    $queryOtherLegal = "SELECT tdroold.TDROOLD_ID,
					CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroold.TDROOLD_LeadTime)/7)+1) > 3
						THEN '3'
						ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroold.TDROOLD_LeadTime)/7)+1)
					END	ReminderLevel,
					tdroold.TDROOLD_Insert_UserID UserID,throold.THROOLD_ReleaseCode RelCode,mdol.DOL_DocCode DocCode,
					DATE_FORMAT(tdroold.TDROOLD_Insert_Time, '%d %M %Y') RelTime,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDROOLD_Insert_UserID) SupervisorID,
					mdol.DOL_NamaDokumen,mdol.DOL_InstansiTerkait,mdol.DOL_NoDokumen,mdc.DocumentCategory_Name,
					DATE_FORMAT(mdol.DOL_TglTerbit, '%d %M %Y') DOL_TglTerbit,
					DATE_FORMAT(mdol.DOL_TglBerakhir, '%d %M %Y') DOL_TglBerakhir,
					mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division,
					throold.THROOLD_ReminderReturn flag, throold.THROOLD_ReasonOfDocumentReturn alasan
				FROM TD_ReleaseOfOtherLegalDocuments tdroold
				LEFT JOIN TH_ReleaseOfOtherLegalDocuments throold ON tdroold.TDROOLD_THROOLD_ID=throold.THROOLD_ID
					AND throold.THROOLD_Delete_Time IS NULL
				LEFT JOIN TD_LoanOfOtherLegalDocuments tdloold ON tdroold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
					AND tdloold.TDLOOLD_Delete_Time IS NULL
				INNER JOIN M_User mu
					ON tdroold.TDROOLD_Insert_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				INNER JOIN M_DocumentsOtherLegal mdol
					ON mdol.DOL_DocCode=tdloold.TDLOOLD_DocCode
					AND mdol.DOL_Delete_Time IS NULL
				LEFT JOIN TH_LoanOfOtherLegalDocuments thloold
					ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
					AND thloold.THLOOLD_Delete_Time IS NULL
				LEFT JOIN M_DocumentCategory mdc
					ON mdc.DocumentCategory_ID=mdol.DOL_CategoryDocID
				LEFT JOIN M_DocumentGroup mdg
					ON mdg.DocumentGroup_ID=mdol.DOL_GroupDocID
				LEFT JOIN db_master.M_Employee me
					ON mu.User_ID = me.Employee_NIK
				LEFT JOIN TD_ReturnOfOtherLegalDocuments tdrtoold
					ON tdloold.TDLOOLD_DocCode=tdrtoold.TDRTOOLD_DocCode
					AND tdrtoold.TDRTOOLD_Delete_Time IS NULL
				WHERE TDROOLD_Delete_Time IS NULL
					AND tdrtoold.TDRTOOLD_ID IS NULL
					AND tdroold.TDROOLD_LeadTime NOT LIKE '%1970-01-01%'
					AND thloold.THLOOLD_LoanCategoryID=1
					AND TDROOLD_LeadTime<=CURDATE()
				    AND throold.THROOLD_ReleaseCode='$relCode'";
    $sqlOtherLegal = mysql_query($queryOtherLegal);
    $i = 0;
    while($data = mysql_fetch_array($sqlOtherLegal)){
        if(strpos($data["DOL_TglTerbit"], '0000-00-00') !== false || strpos($data["DOL_TglTerbit"], '1970-01-01') !== false){
			$tgl_terbit = "-";
		}else{
			$tgl_terbit = date('d/m/Y', strtotime($data["DOL_TglTerbit"]));
		}
		if(strpos($data["DOL_TglBerakhir"], '0000-00-00') !== false || strpos($data["DOL_TglBerakhir"], '1970-01-01') !== false){
			$tgl_berakhir_dok = "-";
		}else{
			$tgl_berakhir_dok = date('d/m/Y', strtotime($data["DOL_TglBerakhir"]));
		}
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$data["DOL_NamaDokumen"].'<br />
						'.$data["DocumentCategory_Name"].'<br />
						'.$data["DOL_InstansiTerkait"].'<br />
						No. Dokumen : '.$data["DOL_NoDokumen"].'<br />
						Tgl. Terbit Dokumen : '.$tgl_terbit.' s/d '.$tgl_berakhir_dok.'
					</TD>
				</TR>';
		$flag = $data['flag'];
		$alasan = $data['alasan'];
        $requester=ucwords(strtolower($data["User_FullName"]));
    	$requester_dept=ucwords(strtolower($data["Employee_Department"]));
    	$requester_div=ucwords(strtolower($data["Employee_Division"]));
    	// $documentGroupName=ucwords(strtolower($data["DocumentGroup_Name"]));
	}
    if($flag == "1"){
        $ket_to_md = "masih ingin dipinjam dokumen dengan alasan :<br>".$alasan.".<br>";
    }elseif($flag == "2"){
        $ket_to_md = "tidak ingin dikembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
    }else{
        $ket_to_md = "";
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
        Bersama ini disampaikan bahwa Dokumen Lainnya (Legal) (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) '.$ket_to_md.' Dengan detail sebagai berikut :
    </span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter = '
				</TABLE>
			</p>
			<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Silahkan untuk menyetujui atau tidak dari permintaan tersebut.<br /> Terima kasih.  </span><br />
			</p>
			<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
			</div>
			<p align=center style="margin-bottom: 7%;">
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocol.php?noret='.$decrp->encrypt('confirm').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Setuju</a>
				</span>
				<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldocol.php?noret='.$decrp->encrypt('reject').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Setuju</a>
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

function mail_response_ret_other_non_legal($relCode, $User_ID){
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
	$mail->Subject  ='Persetujuan Belum Mengembalikan Dokumen '.$relCode;
	// 
	$mail->AddBcc('system.administrator@tap-agri.com');
	$body="";

    $queryOtherLegal = "SELECT tdroonld.TDROONLD_ID,
					CASE WHEN FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroonld.TDROONLD_LeadTime)/7)+1) > 3
						THEN '3'
						ELSE FLOOR(FLOOR(DATEDIFF(CURDATE(), tdroonld.TDROONLD_LeadTime)/7)+1)
					END	ReminderLevel,
					tdroonld.TDROONLD_Insert_UserID UserID,throonld.THROONLD_ReleaseCode RelCode,mdonl.DONL_DocCode DocCode,
					DATE_FORMAT(tdroonld.TDROONLD_Insert_Time, '%d %M %Y') RelTime,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDROONLD_Insert_UserID) SupervisorID,
					DATE_FORMAT(mdonl.DONL_TahunDokumen, '%d %M %Y') DONL_TahunDokumen,
					mdonl.DONL_NamaDokumen,mdonl.DONL_NoDokumen,mc.Company_Name,md.Department_Name,
					mdg.DocumentGroup_Name,mu.User_FullName,me.Employee_Department,me.Employee_Division,
					throonld.THROONLD_ReminderReturn flag, throonld.THROONLD_ReasonOfDocumentReturn alasan
				FROM TD_ReleaseOfOtherNonLegalDocuments tdroonld
				LEFT JOIN TH_ReleaseOfOtherNonLegalDocuments throonld ON tdroonld.TDROONLD_THROONLD_ID=throonld.THROONLD_ID
					AND throonld.THROONLD_Delete_Time IS NULL
				LEFT JOIN TD_LoanOfOtherNonLegalDocuments tdloonld ON tdroonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
					AND tdloonld.TDLOONLD_Delete_Time IS NULL
				INNER JOIN M_User mu ON tdroonld.TDROONLD_Insert_UserID=mu.User_ID
					AND mu.User_Delete_Time IS NULL
				INNER JOIN M_DocumentsOtherNonLegal mdonl
					ON mdonl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
					AND mdonl.DONL_Delete_Time IS NULL
				LEFT JOIN TH_LoanOfOtherNonLegalDocuments thloonld
					ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
					AND thloonld.THLOONLD_Delete_Time IS NULL
				LEFT JOIN M_DocumentGroup mdg
					ON mdg.DocumentGroup_ID=mdonl.DONL_GroupDocID
				LEFT JOIN M_Company mc
					ON mc.Company_ID=mdonl.DONL_CompanyID
				LEFT JOIN db_master.M_Employee me
					ON mu.User_ID = me.Employee_NIK
				LEFT JOIN db_master.M_Department md
					ON md.Department_Code=mdonl.DONL_Dept_Code
				LEFT JOIN TD_ReturnOfOtherNonLegalDocuments tdrtoonld
					ON tdloonld.TDLOONLD_DocCode=tdrtoonld.TDRTOONLD_DocCode
					AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
				WHERE TDROONLD_Delete_Time IS NULL
					AND tdrtoonld.TDRTOONLD_ID IS NULL
					AND tdroonld.TDROONLD_LeadTime NOT LIKE '%1970-01-01%'
					AND thloonld.THLOONLD_LoanCategoryID=1
					AND TDROONLD_LeadTime<=CURDATE()
				    AND throonld.THROONLD_ReleaseCode='$relCode'";
    $sqlOtherLegal = mysql_query($queryOtherLegal);
    $i = 0;
    while($data = mysql_fetch_array($sqlOtherLegal)){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$data['Company_Name'].'<br />
						Departemen : '.$data['Department_Name'].'<br />
						Nama Dokumen : '.$data['DONL_NamaDokumen'].'<br />
						No. Dokumen : '.$data['DONL_NoDokumen'].'<br />
						Tahun Dokumen : '.date('Y', strtotime($data['DONL_TahunDokumen'])).'
					</TD>
				</TR>';
		$flag = $data['flag'];
		$alasan = $data['alasan'];
        $requester=ucwords(strtolower($data["User_FullName"]));
    	$requester_dept=ucwords(strtolower($data["Employee_Department"]));
    	$requester_div=ucwords(strtolower($data["Employee_Division"]));
    	// $documentGroupName=ucwords(strtolower($data["DocumentGroup_Name"]));
	}
    if($flag == "1"){
        $ket_to_md = "masih ingin dipinjam dokumen dengan alasan :<br>".$alasan.".<br>";
    }elseif($flag == "2"){
        $ket_to_md = "tidak ingin dikembalikan dokumen dengan alasan :<br>".$alasan.".<br>";
    }else{
        $ket_to_md = "";
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
        Bersama ini disampaikan bahwa Dokumen Lainnya (Di Luar Legal) (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) '.$ket_to_md.' Dengan detail sebagai berikut :
    </span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter = '
				</TABLE>
			</p>
			<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Silahkan untuk menyetujui atau tidak dari permintaan tersebut.<br /> Terima kasih.  </span><br />
			</p>
			<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
			</div>
			<p align=center style="margin-bottom: 7%;">
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoconl.php?noret='.$decrp->encrypt('confirm').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Setuju</a>
				</span>
				<span style="border: 1px solid #d35400;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(248, 172, 86);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
					<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.reldoconl.php?noret='.$decrp->encrypt('reject').'&rlc='.$decrp->encrypt($relCode).'&uid='.$decrp->encrypt($User_ID).'" style="color: #111;" >Tidak Setuju</a>
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
