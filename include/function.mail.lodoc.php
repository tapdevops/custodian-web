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
=					  18 Sep 2012 - PENAMBAHAN APPROVAL UNTUK PAK TODDY													=
=========================================================================================================================
*/
include_once('./phpmailer/class.phpmailer.php');
include_once('./phpmailer/class.html2text.inc.php');
include_once ("./config/db_sql.php");
include_once ("./include/class.endencrp.php");
include_once("./include/class.helper.php");

function mail_loan_doc($loanCode,$reminder=0){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='';
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query ="	SELECT User_ID,User_FullName,User_Email,DocumentGroup_Name,A_TransactionCode,
				   ARC_AID,ARC_RandomCode,THLOLD_Information,THLOLD_LoanDate,THLOLD_LoanCategoryID,
				   THLOLD_DocumentType, THLOLD_DocumentWithWatermarkOrNot, # Arief F - 07092018
				   LoanCategory_Name,A_Step,A_ApproverID
				FROM TH_LoanOfLegalDocument
				LEFT JOIN M_Approval
					ON THLOLD_LoanCode=A_TransactionCode
					AND A_Status='2'
					AND A_Delete_Time IS NULL
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				LEFT JOIN M_DocumentGroup
					ON THLOLD_DocumentGroupID=DocumentGroup_ID
				LEFT JOIN M_LoanCategory
					ON THLOLD_LoanCategoryID = LoanCategory_ID
				WHERE THLOLD_LoanCode='$loanCode'
				AND THLOLD_Delete_Time IS NULL";
	//echo $e_query; die;
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
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';

	if ($reminder){
		$mail->Subject  ='[REMINDER] '.$testing.' Persetujuan Permintaan Dokumen '.$loanCode.'';
	}else{
		$mail->Subject  =''.$testing.' Persetujuan Permintaan Dokumen '.$loanCode.'';
	}
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name


		$ed_query="	SELECT DISTINCT	Company_Name,DocumentCategory_Name,DocumentType_Name,
									DL_NoDoc,DL_RegTime,THLOLD_Reason,THLOLD_UserID,User_FullName,
									db_master.M_Employee.Employee_Department,
									db_master.M_Employee.Employee_Division
					FROM TH_LoanOfLegalDocument
					LEFT JOIN TD_LoanOfLegalDocument
						ON TDLOLD_THLOLD_ID=THLOLD_ID
					LEFT JOIN M_Company
						ON Company_ID=THLOLD_CompanyID
					LEFT JOIN M_DocumentLegal
						ON TDLOLD_DocCode=DL_DocCode
					LEFT JOIN M_DocumentCategory
						ON DocumentCategory_ID=TDLOLD_DocumentCategoryID
					LEFT JOIN M_DocumentType
						ON DocumentType_ID=DL_TypeDocID
					LEFT JOIN M_User
						ON THLOLD_UserID=User_ID
				    LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
					WHERE THLOLD_LoanCode='$loanCode'
					AND THLOLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;

		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			$fpubdate=(strpos($ed_arr->DL_RegTime, '0000-00-00') !== false || strpos($ed_arr->DL_RegTime, '1970-01-01') !== false)?"-":date("j M Y", strtotime($ed_arr->DL_RegTime));

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								'.$ed_arr->DocumentCategory_Name.'<br />
								'.$ed_arr->DocumentType_Name.'<br />
								No. Dokumen : '.$ed_arr->DL_NoDoc.'<br />
								Tgl. Terbit : '.$fpubdate.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$requester=ucwords(strtolower($ed_arr->User_FullName));
			$requester_dept=ucwords(strtolower($ed_arr->Employee_Department));
			$requester_div=ucwords(strtolower($ed_arr->Employee_Division));
		}
		if($row->THLOLD_DocumentType == "ORIGINAL" ){ //Arief F - 07092018
			$tipe_dokumen = "Asli"; //Arief F - 07092018
		}elseif($row->THLOLD_DocumentType == "SOFTCOPY"){ //Arief F - 07092018
			$tipe_dokumen = ucfirst(strtolower($row->THLOLD_DocumentType)); //Arief F - 07092018
		}elseif($row->THLOLD_DocumentType == "HARDCOPY"){ //Arief F - 07092018
			$tipe_dokumen = ucfirst(strtolower($row->THLOLD_DocumentType)); //Arief F - 07092018
		}else{ //Arief F - 07092018
			if( $row->THLOLD_LoanCategoryID == '1' or $row->THLOLD_LoanCategoryID == '2' ) $tipe_dokumen = "Asli";
			elseif( $row->THLOLD_LoanCategoryID == '3') $tipe_dokumen = "Hardcopy";
			elseif( $row->THLOLD_LoanCategoryID == '4') $tipe_dokumen = "Softcopy";
			else $tipe_dokumen = ""; //Arief F - 07092018
		} //Arief F - 07092018
		$dengan_cap = "";
		if( $tipe_dokumen == "Hardcopy" || $tipe_dokumen == "Softcopy" ){
			if($tipe_dokumen == "Hardcopy") $cap_atau_watermark = "Watermark";
			elseif($tipe_dokumen == "Softcopy") $cap_atau_watermark = "Cap";
			if( $row->THLOLD_DocumentWithWatermarkOrNot == "1" ){ //Arief F - 07092018
				$dengan_cap = " dengan ".$cap_atau_watermark; //Arief F - 07092018
			}elseif( $row->THLOLD_DocumentWithWatermarkOrNot == "2" ){ //Arief F - 07092018
				$dengan_cap = " tanpa ".$cap_atau_watermark; //Arief F - 07092018
			}
		}

		// $asli = ($row->THLOLD_LoanCategoryID != '3') ? ' Asli ' : '';
		$keteranganPermintaan = ""; //Arief F - 07092018
		if( $row->THLOLD_Information != null or $row->THLOLD_Information != "" ){ //Arief F - 07092018
			$keteranganPermintaan = "(tujuan permintaan dokumen adalah ".$row->THLOLD_Information.")"; //Arief F - 07092018
		} //Arief F - 07092018
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">';
		// Bersama ini disampaikan bahwa permintaan dokumen '.$row->DocumentGroup_Name.' <b>('.$row->LoanCategory_Name.$asli.' - '.$row->DocumentGroup_Name.')</b>-->
		$bodyHeader .= 'Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$row->LoanCategory_Name.')</b>'; // Arief F - 07092018
		$bodyHeader .= ' oleh <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut, membutuhkan persetujuan Bapak/Ibu :
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
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Untuk itu dimohon Bapak/Ibu dapat memberikan persetujuan permintaan dokumen di atas. Terima kasih.  </span><br />
				</p>
				<p align=center>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodoc.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a>
					</span>
					<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodoc.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a>
					</span><br />
				</p>
				</div>';

		// approval history
		$sql ="	SELECT user.User_FullName, emp.Employee_Department, emp.Employee_Division, app.A_ApprovalDate
				FROM M_Approval app
				LEFT JOIN M_User user
				ON app.A_ApproverID = user.User_ID
				LEFT JOIN db_master.M_Employee emp
				ON user.User_ID = emp.Employee_NIK
				WHERE app.A_TransactionCode='".$loanCode."'
				AND app.A_Status NOT IN ('1','2','4')
				GROUP BY app.A_Update_UserID
				ORDER BY app.A_Step";
		$sql_handle = mysql_query($sql);
		$app_history = mysql_num_rows($sql_handle);
		if($app_history){
			$bodyFooter .= '
				<div style="margin-bottom: 15px;margin-top:7%">
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
	//echo $row->user_email.$emailContent;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);


	/*try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}
	  print_r ($errorMsg);

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


	//Approval ke Pak Arif, pararel approval ke Pak Rianto
	if ($row->A_ApproverID=='00000001'){
	//if ($row->A_ApproverID=='00000948'){
		/* EDIT THIS PART (userPararelApp = USER ID PARAREL) */
		$userPararelApp='00000005'; //Pak Rianto
		$query_pararel="SELECT User_FullName, User_Email
						FROM M_User
						WHERE User_ID='$userPararelApp'";
		$sql_pararel=mysql_query($query_pararel);
		$obj_pararel=mysql_fetch_object($sql_pararel);
		/* ======================== */
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
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$obj_pararel->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$row->LoanCategory_Name.' - '.$row->DocumentGroup_Name.')</b>  oleh <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut, membutuhkan persetujuan Bapak/Ibu :</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

		$emailContent=$bodyHeader.$body.$bodyFooter;
		//echo $row->user_email.$emailContent;
		$mail->ClearAddresses();
		$mail->AddAddress($obj_pararel->User_Email,$obj_pararel->User_FullName);
		//$mail->AddAddress('sabrina.davita@tap-agri.com','Sabrina ID');
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
}

function mail_notif_loan_doc($loanCode, $User_ID, $status, $attr){
	$helper = new helper();
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='';
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

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
	$mail->Host       = $helper->host_email();
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';

	if ($status=='3'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Permintaan Dokumen '.$loanCode;
	}
	if ($status=='4'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Permintaan Dokumen '.$loanCode;
	}
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

		$ed_query="	SELECT DISTINCT	Company_Name,THLOLD_LoanCategoryID, DocumentCategory_Name,DocumentType_Name,
						DL_NoDoc,DL_RegTime,THLOLD_Reason,THLOLD_UserID,User_FullName,
						THLOLD_DocumentType, THLOLD_DocumentWithWatermarkOrNot, # Arief F - 07092018
						THLOLD_Information,Company_ID, LoanCategory_Name,DocumentGroup_Name
					FROM TH_LoanOfLegalDocument
					LEFT JOIN TD_LoanOfLegalDocument
						ON TDLOLD_THLOLD_ID=THLOLD_ID
					LEFT JOIN M_LoanCategory
						ON THLOLD_LoanCategoryID = LoanCategory_ID
					LEFT JOIN M_DocumentGroup
						ON THLOLD_DocumentGroupID=DocumentGroup_ID
					LEFT JOIN M_Company
						ON Company_ID=THLOLD_CompanyID
					LEFT JOIN M_DocumentLegal
						ON TDLOLD_DocCode=DL_DocCode
					LEFT JOIN M_DocumentCategory
						ON DocumentCategory_ID=TDLOLD_DocumentCategoryID
					LEFT JOIN M_DocumentType
						ON DocumentType_ID=DL_TypeDocID
					LEFT JOIN M_User
						ON THLOLD_UserID=User_ID
					WHERE THLOLD_LoanCode='$loanCode'
					AND THLOLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			$fpubdate=(strpos($ed_arr->DL_RegTime, '0000-00-00') !== false || strpos($ed_arr->DL_RegTime, '1970-01-01') !== false)?"-":date("j M Y", strtotime($ed_arr->DL_RegTime));

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								'.$ed_arr->DocumentCategory_Name.'<br />
								'.$ed_arr->DocumentType_Name.'<br />
								No. Dokumen : '.$ed_arr->DL_NoDoc.'<br />
								Tgl. Terbit : '.$fpubdate.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$reason=$ed_arr->THLOLD_Reason;
			$regUser=$ed_arr->THLOLD_UserID;
			$requester=$ed_arr->User_FullName;
			$company=$ed_arr->Company_ID;
			$info=$ed_arr->THLOLD_Information;
			$loanName = $ed_arr->LoanCategory_Name;
			$docName = $ed_arr->DocumentGroup_Name;

			if($ed_arr->THLOLD_DocumentType == "ORIGINAL" ){ //Arief F - 07092018
				$tipe_dokumen = "Asli"; //Arief F - 07092018
			}elseif($ed_arr->THLOLD_DocumentType == "HARDCOPY" or $ed_arr->THLOLD_DocumentType == "SOFTCOPY"){ //Arief F - 07092018
				$tipe_dokumen = ucfirst(strtolower($ed_arr->THLOLD_DocumentType)); //Arief F - 07092018
			}else{ //Arief F - 07092018
				if( $ed_arr->THLOLD_LoanCategoryID == '1' or $ed_arr->THLOLD_LoanCategoryID == '2' ) $tipe_dokumen = "Asli";
				elseif( $ed_arr->THLOLD_LoanCategoryID == '3') $tipe_dokumen = "Hardcopy";
				elseif( $ed_arr->THLOLD_LoanCategoryID == '4') $tipe_dokumen = "Softcopy";
				else $tipe_dokumen = ""; //Arief F - 07092018
			} //Arief F - 07092018
			$dengan_cap = "";
			if( $tipe_dokumen == "Hardcopy" || $tipe_dokumen == "Softcopy" ){
				if($tipe_dokumen == "Hardcopy") $cap_atau_watermark = "Watermark";
				elseif($tipe_dokumen == "Softcopy") $cap_atau_watermark = "Cap";
				if( $row->THLOLD_DocumentWithWatermarkOrNot == "1" ){ //Arief F - 07092018
					$dengan_cap = " dengan ".$cap_atau_watermark; //Arief F - 07092018
				}elseif( $row->THLOLD_DocumentWithWatermarkOrNot == "2" ){ //Arief F - 07092018
					$dengan_cap = " tanpa ".$cap_atau_watermark; //Arief F - 07092018
				}
			}
			// $asli = ($row->THLOLD_LoanCategoryID != '3') ? ' Asli ' : '';
			$keteranganPermintaan = ""; //Arief F - 07092018
			if( $ed_arr->THLOLD_Information != null or $ed_arr->THLOLD_Information != "" ){ //Arief F - 07092018
				$keteranganPermintaan = "(tujuan permintaan dokumen adalah ".$info.")"; //Arief F - 07092018
			} //Arief F - 07092018
		}
	if (($status=='3')&&($row->User_ID<>$regUser)){
		if ($attr == '1') {
			$bodyFooter .= '
	                    </TABLE>
					</p>
					<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah disetujui oleh Departemen Custodian. Terima kasih.  </span><br />
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
		// CC UNTUK CFO
		$query_cc="	SELECT User_FullName, User_Email
					FROM M_User
					LEFT JOIN M_DivisionDepartmentPosition
						ON DDP_UserID=User_ID
					LEFT JOIN M_Position
						ON DDP_PosID=Position_ID
					WHERE Position_Name = 'CHIEF FINANCIAL OFFICER - AMP'";
		$sql_cc=mysql_query($query_cc);
		$obj_cc=mysql_fetch_object($sql_cc);

		//$mail->AddCC($obj_cc->User_Email,$obj_cc->User_FullName);
		$mail->addCustomHeader("CC: {$obj_cc->User_FullName} <{$obj_cc->User_Email}>");

		//CC UNTUK CEO REGION
		$ceo_query="SELECT User_FullName, User_Email
					FROM M_User
					LEFT JOIN M_DivisionDepartmentPosition
						ON DDP_UserID=User_ID
					LEFT JOIN M_Position
						ON DDP_PosID=Position_ID
					LEFT JOIN M_Company
						ON Company_ID='$company'
					WHERE Position_Name=CONCAT('CEO - ',Company_Area)";
		$ceo_handle=mysql_query($ceo_query);
		$ceo_obj=mysql_fetch_object($ceo_handle);
		if(!empty($ceo_obj->User_Email)){
			//$mail->AddCC($ceo_obj->User_Email,$ceo_obj->User_FullName);
			$mail->addCustomHeader("CC: {$ceo_obj->User_FullName} <{$ceo_obj->User_Email}>");
		}

		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui. Permintaan dokumen Bapak/Ibu sedang diproses oleh Tim Custodian. Terima kasih.  </span><br />
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

		// approval history
		$sql ="	SELECT user.User_FullName, emp.Employee_Department, emp.Employee_Division, app.A_ApprovalDate
				FROM M_Approval app
				LEFT JOIN M_User user
				ON app.A_ApproverID = user.User_ID
				LEFT JOIN db_master.M_Employee emp
				ON user.User_ID = emp.Employee_NIK
				WHERE app.A_TransactionCode='".$loanCode."'
				AND app.A_Status NOT IN ('1','2','4')
				GROUP BY app.A_Update_UserID
				ORDER BY app.A_Step";
		$sql_handle = mysql_query($sql);
		$app_history = mysql_num_rows($sql_handle);
		if($app_history){
			$bodyFooter .= '
				<div style="margin-bottom: 15px">
				<p>
					Approval History :
					<ol>
				';

			$i=1;
			while ($obj = mysql_fetch_object($sql_handle)) {
				//if ($i < $app_history) {
					$bodyFooter .= '
						<li><b>'.ucwords(strtolower($obj->User_FullName)).'</b><BR>
						Dept : '.ucwords(strtolower($obj->Employee_Department)).'<BR>
						Div : '.ucwords(strtolower($obj->Employee_Division)).'<BR>
						Tanggal Persetujuan : '.date('d/m/Y H:i:s', strtotime($obj->A_ApprovalDate)).'.</li>
					';
				//}
				$i++;
			}

			$bodyFooter .= '
					</ol>
				</p>
				</div>';
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">';
		// $bodyHeader .= 'Bersama ini disampaikan bahwa permintaan dokumen  <b>('.$loanName.$asli.' - '.$docName.')</b>';
		$bodyHeader .= 'Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$loanName.')</b>'; // Arief F - 07092018
		$bodyHeader .= ' oleh '.$requester.' '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut :
	</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

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
