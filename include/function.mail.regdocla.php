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

function mail_registration_doc($regCode,$reminder=0){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";
	$testing='';

	$e_query ="	SELECT 	User_ID,User_FullName,User_Email,DocumentGroup_Name,A_TransactionCode,
						ARC_AID,ARC_RandomCode,THRGOLAD_ID,THRGOLAD_RegistrationDate
				FROM TH_RegistrationOfLandAcquisitionDocument
				LEFT JOIN M_Approval
					ON THRGOLAD_RegistrationCode=A_TransactionCode
					AND A_Status='2'
					AND A_Delete_Time IS NULL
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				LEFT JOIN M_DocumentGroup
					ON DocumentGroup_ID='3'
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				WHERE THRGOLAD_RegistrationCode='$regCode'
				AND THRGOLAD_Delete_Time IS NULL";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	// Cek apakah Staff Custodian atau bukan.
	// Staff Custodian memiliki wewenang untuk print registrasi dokumen.
	$cs_query = "SELECT *
				 FROM M_DivisionDepartmentPosition ddp, M_Department d
				 WHERE ddp.DDP_DeptID=d.Department_ID
				 AND ddp.DDP_UserID='$row->User_ID'
				 AND d.Department_Name LIKE '%Custodian%'";
	$cs_sql = mysql_query($cs_query);
	$custodian = mysql_num_rows($cs_sql);

	//setting email header
	/* Config Lokal */
	/*$mail->Username = 'admin@oncom.local';
	$mail->Password = '';
	$mail->From       = 'admin@oncom.local';
	$mail->FromName   = 'Custodian System';*/

	/* Config Server */
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	//$mail->Host       ='10.20.10.5';
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';

	if ($reminder){
		$mail->Subject  ='[REMINDER] '.$testing.' Persetujuan Pendaftaran Dokumen '.$regCode.'';
	}else{
		$mail->Subject  =''.$testing.' Persetujuan Pendaftaran Dokumen '.$regCode.'';
	}
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

		$ed_query="	SELECT DISTINCT TDRGOLAD_ID, CASE WHEN TDRGOLAD_Core_CompanyID IS NOT NULL
							THEN TDRGOLAD_Core_CompanyID
							ELSE THRGOLAD_CompanyID
						END AS core_company_id,
						CASE WHEN TDRGOLAD_Core_CompanyID IS NOT NULL
							THEN (SELECT Company_Name FROM M_Company
									WHERE Company_ID = TDRGOLAD_Core_CompanyID
								)
							ELSE (SELECT Company_Name FROM M_Company
									WHERE Company_ID = THRGOLAD_CompanyID
								)
						END AS core_company_name,
						CASE WHEN TDRGOLAD_Core_CompanyID IS NOT NULL
							THEN TDRGOLAD_Core_Phase
							ELSE THRGOLAD_Phase
						END AS tahap, TDRGOLAD_Village,TDRGOLAD_Block,TDRGOLAD_Owner,
						TDRGOLAD_AreaStatement,TDRGOLAD_PlantTotalPrice,TDRGOLAD_GrandTotal,TDRGOLAD_DocDate,User_FullName,
						db_master.M_Employee.Employee_Department,
						db_master.M_Employee.Employee_Division
					FROM TH_RegistrationOfLandAcquisitionDocument
					LEFT JOIN TD_RegistrationOfLandAcquisitionDocument
						ON TDRGOLAD_THRGOLAD_ID=THRGOLAD_ID
					LEFT JOIN M_User
						ON THRGOLAD_UserID=User_ID
				    LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
					WHERE THRGOLAD_RegistrationCode='$regCode'
					AND THRGOLAD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {

		$TDRGOLAD_AreaStatement=number_format($ed_arr->TDRGOLAD_AreaStatement,2,'.',',');
		$TDRGOLAD_PlantTotalPrice=number_format($ed_arr->TDRGOLAD_PlantTotalPrice,2,',','.');
		$TDRGOLAD_GrandTotal=number_format($ed_arr->TDRGOLAD_GrandTotal,2,',','.');
		$DocDate=date("j M Y", strtotime($ed_arr->TDRGOLAD_DocDate));

		$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->core_company_name.'</TD>
							<TD>'.$ed_arr->tahap.'</TD>
							<TD>'.$ed_arr->TDRGOLAD_Village.'</TD>
							<TD>'.$ed_arr->TDRGOLAD_Block.'</TD>
							<TD>'.$ed_arr->TDRGOLAD_Owner.'</TD>
							<TD>'.$TDRGOLAD_AreaStatement.' Ha</TD>
							<TD>Rp '.$TDRGOLAD_PlantTotalPrice.'</TD>
							<TD>Rp '.$TDRGOLAD_GrandTotal.'</TD>
							<TD>'.$DocDate.'</TD>
							<TD valign="top">';
							$TDRGOLAD_ID=$ed_arr->TDRGOLAD_ID;
							$dd_query="SELECT laa.LAA_Acronym
			  					  	   FROM TD_RegistrationOfLandAcquisitionDocumentDetail tdrgoladd,
									   		M_LandAcquisitionAttribute laa
									   WHERE tdrgoladd.TDRGOLADD_TDRGOLAD_ID='$TDRGOLAD_ID'
									   AND tdrgoladd.TDRGOLADD_Delete_Time IS NULL
									   AND tdrgoladd.TDRGOLADD_AttributeStatusID='1'
									   AND laa.LAA_ID=tdrgoladd.TDRGOLADD_AttibuteID";
							$dd_sql=mysql_query($dd_query);
							while ($dd_arr=mysql_fetch_object($dd_sql)){
								$body .= ''.$dd_arr->LAA_Acronym.'; ';
							}
		$body .= '
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pendaftaran dokumen '.$row->DocumentGroup_Name.' oleh <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> dengan detail pendaftaran sebagai berikut, membutuhkan persetujuan Bapak/Ibu :</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD style="font-size: 13px" rowspan="2"><strong>No.</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Perusahaan</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Tahap</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Desa</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Blok</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Nama Pemilik</strong></TD>
			<TD style="font-size: 13px" colspan="3"><strong>Lahan</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Tgl. Dokumen</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Kelengkapan</strong></TD>
		</TR>
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD style="font-size: 13px"><strong>Ha</strong></TD>
			<TD style="font-size: 13px"><strong>Rp/Ha</strong></TD>
			<TD style="font-size: 13px"><strong>Nilai (Rp)</strong></TD>
		</TR>';

		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Untuk itu dimohon Bapak/Ibu dapat memberikan persetujuan pendaftaran dokumen di atas. Terima kasih.  </span><br />
				</p>
				<p align=center>
				<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 12%;width: 20%;border-radius: 10px;"><a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.regdocla.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a></span>';
				if ($custodian==1){
			$bodyFooter .= '
				<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: left;margin-left: 4%;margin-right:4%;width: 20%;border-radius: 10px;"><a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.regdocla.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a></span>
				<span style="border: 1px solid blue;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(94, 83, 244);color: #111;float: right;margin-right: 12%;width: 20%;border-radius: 10px;"><a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/detail-of-registration-land-acquisition-document.php?act='.$decrp->encrypt('approve').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'&id='.$decrp->encrypt($row->THRGOLAD_ID).'">Revisi</a></span><br />';
				}
				else {
			$bodyFooter .= '
				<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 12%;width: 20%;border-radius: 10px;"><a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.regdocla.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a></span>
				<br>';
				}
			$bodyFooter .= '
				</p>
				</div>';

		// approval history
		$sql ="	SELECT user.User_FullName, emp.Employee_Department, emp.Employee_Division, app.A_ApprovalDate
				FROM M_Approval app
				LEFT JOIN M_User user
				ON app.A_ApproverID = user.User_ID
				LEFT JOIN db_master.M_Employee emp
				ON user.User_ID = emp.Employee_NIK
				WHERE app.A_TransactionCode='".$regCode."'
				AND app.A_Status NOT IN ('1','2','4')
				ORDER BY app.A_Step";
		$sql_handle = mysql_query($sql);
		$app_history = mysql_num_rows($sql_handle);
		if($app_history){
			$bodyFooter .= '
				<div style="margin-bottom: 15px;margin-top:7%;">
				<p>
					Approval History :
					<ol>
				';

			while ($obj = mysql_fetch_object($sql_handle)) {
					$bodyFooter .= '
						<li><b>'.ucwords(strtolower($obj->User_FullName)).'</b><BR>
						Dept : '.ucwords(strtolower($obj->Employee_Department)).'<BR>
						Div : '.ucwords(strtolower($obj->Employee_Division)).'<BR>
						Tanggal Persetujuan : '.date('d/m/Y H:i:s', strtotime($obj->A_ApprovalDate)).'.</li>
					';
			}

			$bodyFooter .= '
					</ol>
				</p>
				</div>';
		}

			$bodyFooter .= '
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;margin-top:7%;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
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

function mail_notif_registration_doc($regCode, $User_ID, $status, $attr){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";
	$testing='';

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$User_ID'";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	/* Config Lokal */
	/*$mail->Username = 'admin@oncom.local';
	$mail->Password = '';
	$mail->From       = 'admin@oncom.local';
	$mail->FromName   = 'Custodian System';*/

	/* Config Server */
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	//$mail->Host       ='10.20.10.5';
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';

	if ($status=='3'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Pendaftaran Dokumen '.$regCode;
	}
	if ($status=='4'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Pendaftaran Dokumen '.$regCode;
	}
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

		$ed_query="	SELECT DISTINCT CASE WHEN TDRGOLAD_Core_CompanyID IS NOT NULL
							THEN TDRGOLAD_Core_CompanyID
							ELSE THRGOLAD_CompanyID
						END AS core_company_id,
						CASE WHEN TDRGOLAD_Core_CompanyID IS NOT NULL
							THEN (SELECT Company_Name FROM M_Company
									WHERE Company_ID = TDRGOLAD_Core_CompanyID
								)
							ELSE (SELECT Company_Name FROM M_Company
									WHERE Company_ID = THRGOLAD_CompanyID
								)
						END AS core_company_name,
						CASE WHEN TDRGOLAD_Core_CompanyID IS NOT NULL
							THEN TDRGOLAD_Core_Phase
							ELSE THRGOLAD_Phase
						END AS tahap, TDRGOLAD_Village,TDRGOLAD_Block,TDRGOLAD_Owner,
									TDRGOLAD_AreaStatement,TDRGOLAD_PlantTotalPrice,TDRGOLAD_GrandTotal,TDRGOLAD_DocDate,TDRGOLAD_ID,
									THRGOLAD_RegStatusReason,THRGOLAD_UserID,User_FullName
					FROM TH_RegistrationOfLandAcquisitionDocument
					LEFT JOIN TD_RegistrationOfLandAcquisitionDocument
						ON TDRGOLAD_THRGOLAD_ID=THRGOLAD_ID
					LEFT JOIN M_User
						ON THRGOLAD_UserID=User_ID
					WHERE THRGOLAD_RegistrationCode='$regCode'
					AND THRGOLAD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			$TDRGOLAD_AreaStatement=number_format($ed_arr->TDRGOLAD_AreaStatement,2,'.',',');
			$TDRGOLAD_PlantTotalPrice=number_format($ed_arr->TDRGOLAD_PlantTotalPrice,2,',','.');
			$TDRGOLAD_GrandTotal=number_format($ed_arr->TDRGOLAD_GrandTotal,2,',','.');
			$DocDate=date("j M Y", strtotime($ed_arr->TDRGOLAD_DocDate));

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->core_company_name.'</TD>
							<TD>'.$ed_arr->tahap.'</TD>
							<TD>'.$ed_arr->TDRGOLAD_Village.'</TD>
							<TD>'.$ed_arr->TDRGOLAD_Block.'</TD>
							<TD>'.$ed_arr->TDRGOLAD_Owner.'</TD>
							<TD>'.$TDRGOLAD_AreaStatement.' Ha</TD>
							<TD>Rp '.$TDRGOLAD_PlantTotalPrice.'</TD>
							<TD>Rp '.$TDRGOLAD_GrandTotal.'</TD>
							<TD>'.$DocDate.'</TD>
							<TD valign="top">';
							$TDRGOLAD_ID=$ed_arr->TDRGOLAD_ID;
							$dd_query="SELECT laa.LAA_Acronym
			  					  	   FROM TD_RegistrationOfLandAcquisitionDocumentDetail tdrgoladd,
									   		M_LandAcquisitionAttribute laa
									   WHERE tdrgoladd.TDRGOLADD_TDRGOLAD_ID='$TDRGOLAD_ID'
									   AND tdrgoladd.TDRGOLADD_Delete_Time IS NULL
									   AND tdrgoladd.TDRGOLADD_AttributeStatusID='1'
									   AND laa.LAA_ID=tdrgoladd.TDRGOLADD_AttibuteID";
							$dd_sql=mysql_query($dd_query);
							while ($dd_arr=mysql_fetch_object($dd_sql)){
								$body .= ''.$dd_arr->LAA_Acronym.'; ';
							}
		$body .= '
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$reason=$ed_arr->THRGOLAD_RegStatusReason;
			$regUser=$ed_arr->THRGOLAD_UserID;
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pendaftaran dokumen oleh '.$requester.' dengan detail pendaftaran sebagai berikut :</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD style="font-size: 13px" rowspan="2"><strong>No.</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Perusahaan</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Tahap</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Desa</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Blok</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Nama Pemilik</strong></TD>
			<TD style="font-size: 13px" colspan="3"><strong>Lahan</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Tgl. Dokumen</strong></TD>
			<TD style="font-size: 13px" rowspan="2"><strong>Kelengkapan</strong></TD>
		</TR>
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD style="font-size: 13px"><strong>Ha</strong></TD>
			<TD style="font-size: 13px"><strong>Rp/Ha</strong></TD>
			<TD style="font-size: 13px"><strong>Nilai (Rp)</strong></TD>
		</TR>';

	if (($status=='3')&&($row->User_ID<>$regUser)){
		if ($attr == '1') {
			$bodyFooter .= '
	                    </TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Dokumen di atas telah diserahkan dan disetujui oleh Departemen Custodian. Terima kasih.  </span><br />
					</p>
					</div>';
		} else {
			$bodyFooter .= '
	                    </TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Sedang dalam proses persetujuan dari Departemen Custodian. Terima kasih.  </span><br />
					</p>
					</div>';
		}
	}

	if (($status=='3')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p>
					<span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui dan Diterima oleh Tim Custodian dengan baik dan lengkap. Terima kasih.  </span><br />
				</p>
				</div>';
	}
	if (($status=='4')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Ditolak dengan alasan : '.$reason.'<br>Untuk itu dimohon Bapak/Ibu dapat memeriksa kembali dokumen di atas dan melakukan registrasi ulang. Terima kasih.  </span><br />
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
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;margin-top:7%;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
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