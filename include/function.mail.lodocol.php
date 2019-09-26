<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 2.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 4 Sep 2018																						=
= Update Terakhir	: -																									=
= Revisi			: -																									=
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
    $body='';
    $bodyHeader='';
    $bodyFooter='';

	$e_query ="	SELECT User_ID, User_FullName, User_Email, A_TransactionCode,
					   ARC_AID,ARC_RandomCode, THLOOLD_Information, THLOOLD_LoanDate, THLOOLD_LoanCategoryID,
					   THLOOLD_DocumentType, THLOOLD_DocumentWithWatermarkOrNot,
					   LoanCategory_Name, A_Step, A_ApproverID
				FROM TH_LoanOfOtherLegalDocuments
				LEFT JOIN M_Approval
					ON THLOOLD_LoanCode=A_TransactionCode
					AND A_Status='2'
					AND A_Delete_Time IS NULL
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				LEFT JOIN M_LoanCategory
					ON THLOOLD_LoanCategoryID = LoanCategory_ID
				WHERE THLOOLD_LoanCode='$loanCode'
				AND THLOOLD_Delete_Time IS NULL";
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


		$ed_query="	SELECT DISTINCT	Company_Name,
						DOL_NamaDokumen, DOL_InstansiTerkait, DOL_NoDokumen,
                        DocumentCategory_Name,
						DOL_TglTerbit, DOL_TglBerakhir,
						DOL_RegTime, THLOOLD_Reason,THLOOLD_UserID,
                        User_FullName,
						db_master.M_Employee.Employee_Department,
						db_master.M_Employee.Employee_Division
					FROM TH_LoanOfOtherLegalDocuments
					LEFT JOIN TD_LoanOfOtherLegalDocuments
						ON TDLOOLD_THLOOLD_ID=THLOOLD_ID
					LEFT JOIN M_Company
						ON Company_ID=THLOOLD_CompanyID
					LEFT JOIN M_DocumentsOtherLegal
						ON TDLOOLD_DocCode=DOL_DocCode
					LEFT JOIN M_User
						ON THLOOLD_UserID=User_ID
				    LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
                    LEFT JOIN db_master.M_DocumentCategory m_dc
                        ON DOL_CategoryDocID=m_dc.DocumentCategory_ID
					WHERE THLOOLD_LoanCode='$loanCode'
					AND THLOOLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;

		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			if(strpos($ed_arr->DOL_TglTerbit, '0000-00-00') !== false || strpos($ed_arr->DOL_TglTerbit, '1970-01-01') !== false){
				$tgl_terbit = "-";
			}else{
				$tgl_terbit = date('d/m/Y', strtotime($ed_arr->DOL_TglTerbit));
			}
			if(strpos($ed_arr->DOL_TglBerakhir, '0000-00-00') !== false || strpos($ed_arr->DOL_TglBerakhir, '1970-01-01') !== false){
				$tgl_berakhir_dok = "-";
			}else{
				$tgl_berakhir_dok = date('d/m/Y', strtotime($ed_arr->DOL_TglBerakhir));
			}

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
                                Nama Dokumen : '.$ed_arr->DOL_NamaDokumen.'<br />
                                '.$ed_arr->DocumentCategory_Name.'<br />
                                Instansi Terkait : '.$ed_arr->DOL_InstansiTerkait.'<br />
                                No. Dokumen : '.$ed_arr->DOL_NoDokumen.'<br />
                                Tgl. Terbit Dokumen : '.$tgl_terbit.' s/d '.$tgl_berakhir_dok.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$requester=ucwords(strtolower($ed_arr->User_FullName));
			$requester_dept=ucwords(strtolower($ed_arr->Employee_Department));
			$requester_div=ucwords(strtolower($ed_arr->Employee_Division));
		}
		$cap_atau_watermark = "Cap/Watermark";
		if($row->THLOOLD_DocumentType == "ORIGINAL" ){
			$tipe_dokumen = "Asli";
		}elseif($row->THLOOLD_DocumentType == "SOFTCOPY"){
			$tipe_dokumen = ucfirst(strtolower($row->THLOOLD_DocumentType));
			$cap_atau_watermark = "Cap";
		}elseif($row->THLOOLD_DocumentType == "HARDCOPY"){
			$tipe_dokumen = ucfirst(strtolower($row->THLOOLD_DocumentType));
			$cap_atau_watermark = "Watermark";
		}else{
			if( $row->THLOOLD_LoanCategoryID == '1' or $row->THLOOLD_LoanCategoryID == '2' ) $tipe_dokumen = "Asli";
			elseif( $row->THLOOLD_LoanCategoryID == '3') $tipe_dokumen = "Hardcopy";
			elseif( $row->THLOOLD_LoanCategoryID == '4') $tipe_dokumen = "Softcopy";
			else $tipe_dokumen = "";
		}
		$dengan_cap = "";
		if( $tipe_dokumen == "Hardcopy" || $tipe_dokumen == "Softcopy" ){
			if($tipe_dokumen == "Hardcopy") $cap_atau_watermark = "Watermark";
			elseif($tipe_dokumen == "Softcopy") $cap_atau_watermark = "Cap";
			if( $row->THLOOLD_DocumentWithWatermarkOrNot == "1" ){ //Arief F - 07092018
				$dengan_cap = " dengan ".$cap_atau_watermark; //Arief F - 07092018
			}elseif( $row->THLOOLD_DocumentWithWatermarkOrNot == "2" ){ //Arief F - 07092018
				$dengan_cap = " tanpa ".$cap_atau_watermark; //Arief F - 07092018
			}
		}
		//$asli = ($row->THLOOLD_LoanCategoryID != '3') ? ' Asli ' : '';
		$keteranganPermintaan = "";
		if( $row->THLOOLD_Information != null or $row->THLOOLD_Information != "" ){
			$keteranganPermintaan = "(tujuan permintaan dokumen adalah ".$row->THLOOLD_Information.")";
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
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">
        Yth '.$row->User_FullName.',
    </div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
		Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$row->LoanCategory_Name.')</b>
		oleh <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut, membutuhkan persetujuan Bapak/Ibu :
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
				<p align=center><span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;"><a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodocol.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a></span>
				<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;"><a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodocol.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a></span><br />
				</p>
				</div>';

		// // approval history
		// $sql ="	SELECT user.User_FullName, emp.Employee_Department, emp.Employee_Division, app.A_ApprovalDate
		// 		FROM M_Approval app
		// 		LEFT JOIN M_User user
		// 		ON app.A_ApproverID = user.User_ID
		// 		LEFT JOIN db_master.M_Employee emp
		// 		ON user.User_ID = emp.Employee_NIK
		// 		WHERE app.A_TransactionCode='".$loanCode."'
		// 		AND app.A_Status NOT IN ('1','2','4')
		// 		GROUP BY app.A_Update_UserID
		// 		ORDER BY app.A_Step";
		// $sql_handle = mysql_query($sql);
		// $app_history = mysql_num_rows($sql_handle);
		// if($app_history){
		// 	$bodyFooter .= '
		// 		<div style="margin-bottom: 15px">
		// 		<p>
		// 			Approval History :
		// 			<ol>
		// 		';
        //
		// 	while ($obj = mysql_fetch_object($sql_handle)) {
		// 			$bodyFooter .= '
		// 				<li><b>'.ucwords(strtolower($obj->User_FullName)).'</b><BR>
		// 				Dept : '.ucwords(strtolower($obj->Employee_Department)).'<BR>
		// 				Div : '.ucwords(strtolower($obj->Employee_Division)).'<BR>
		// 				Tanggal Persetujuan : '.date('d/m/Y H:i:s', strtotime($obj->A_ApprovalDate)).'.</li>
		// 			';
		// 	}
        //
		// 	$bodyFooter .= '
		// 			</ol>
		// 		</p>
		// 		</div>';
		// }

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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
		Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$row->LoanCategory_Name.')</b>
		oleh <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b> '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut, membutuhkan persetujuan Bapak/Ibu :
	</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center" style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%" style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%" style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
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
    $body='';
    $bodyHeader='';
    $bodyFooter='';
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

		// $ed_query="	SELECT DISTINCT	Company_Name,THLOOLD_LoanCategoryID, DocumentCategory_Name,DocumentType_Name,
		// 							DOL_NoDoc,DOL_RegTime,THLOOLD_Reason,THLOOLD_UserID,User_FullName,
		// 							THLOOLD_Information,Company_ID, LoanCategory_Name,DocumentGroup_Name
		// 			FROM TH_LoanOfOtherLegalDocuments
		// 			LEFT JOIN TD_LoanOfOtherLegalDocuments
		// 				ON TDLOOLD_THLOOLD_ID=THLOOLD_ID
		// 			LEFT JOIN M_LoanCategory
		// 				ON THLOOLD_LoanCategoryID = LoanCategory_ID
		// 			LEFT JOIN M_DocumentGroup
		// 				ON THLOOLD_DocumentGroupID=DocumentGroup_ID
		// 			LEFT JOIN M_Company
		// 				ON Company_ID=THLOOLD_CompanyID
		// 			LEFT JOIN M_DocumentsOtherLegal
		// 				ON TDLOOLD_DocCode=DOL_DocCode
		// 			LEFT JOIN M_DocumentCategory
		// 				ON DocumentCategory_ID=TDLOOLD_DocumentCategoryID
		// 			LEFT JOIN M_DocumentType
		// 				ON DocumentType_ID=DOL_TypeDocID
		// 			LEFT JOIN M_User
		// 				ON THLOOLD_UserID=User_ID
		// 			WHERE THLOOLD_LoanCode='$loanCode'
		// 			AND THLOOLD_Delete_Time IS NULL";

		$ed_query="	SELECT DISTINCT	Company_Name, THLOOLD_LoanCategoryID,
						THLOOLD_DocumentType, THLOOLD_DocumentWithWatermarkOrNot,
						DOL_NamaDokumen, DOL_InstansiTerkait, DOL_NoDokumen,
                        DocumentCategory_Name,
						DOL_TglTerbit, DOL_TglBerakhir,
						DOL_RegTime, THLOOLD_Reason, THLOOLD_UserID, User_FullName,
						THLOOLD_Information, Company_ID, LoanCategory_Name
					FROM TH_LoanOfOtherLegalDocuments
					LEFT JOIN TD_LoanOfOtherLegalDocuments
						ON TDLOOLD_THLOOLD_ID=THLOOLD_ID
					LEFT JOIN M_LoanCategory
						ON THLOOLD_LoanCategoryID = LoanCategory_ID
					LEFT JOIN M_Company
						ON Company_ID=THLOOLD_CompanyID
					LEFT JOIN M_DocumentsOtherLegal
						ON TDLOOLD_DocCode=DOL_DocCode
					LEFT JOIN M_User
						ON THLOOLD_UserID=User_ID
					LEFT JOIN db_master.M_DocumentCategory m_dc
                        ON DOL_CategoryDocID=m_dc.DocumentCategory_ID
					WHERE THLOOLD_LoanCode='$loanCode'
					AND THLOOLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			if(strpos($ed_arr->DOL_TglTerbit, '0000-00-00') !== false || strpos($ed_arr->DOL_TglTerbit, '1970-01-01') !== false){
				$tgl_terbit = "-";
			}else{
				$tgl_terbit = date('d/m/Y', strtotime($ed_arr->DOL_TglTerbit));
			}
			if(strpos($ed_arr->DOL_TglBerakhir, '0000-00-00') !== false || strpos($ed_arr->DOL_TglBerakhir, '1970-01-01') !== false){
				$tgl_berakhir_dok = "-";
			}else{
				$tgl_berakhir_dok = date('d/m/Y', strtotime($ed_arr->DOL_TglBerakhir));
			}

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								Nama Dokumen : '.$ed_arr->DOL_NamaDokumen.'<br />
								'.$ed_arr->DocumentCategory_Name.'<br />
								Instansi Terkait : '.$ed_arr->DOL_InstansiTerkait.'<br />
								No. Dokumen : '.$ed_arr->DOL_NoDokumen.'<br />
								Tgl. Terbit Dokumen : '.$tgl_terbit.' s/d '.$tgl_berakhir_dok.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$reason=$ed_arr->THLOOLD_Reason;
			$regUser=$ed_arr->THLOOLD_UserID;
			$requester=$ed_arr->User_FullName;
			$company=$ed_arr->Company_ID;
			$info=$ed_arr->THLOOLD_Information;
			$loanName = $ed_arr->LoanCategory_Name;
			//$asli = ($ed_arr->THLOOLD_LoanCategoryID != '3') ? ' Asli ' : '';
			if($ed_arr->THLOOLD_DocumentType == "ORIGINAL" ){
				$tipe_dokumen = "Asli";
			}elseif($ed_arr->THLOOLD_DocumentType == "HARDCOPY" or $ed_arr->THLOOLD_DocumentType == "SOFTCOPY"){
				$tipe_dokumen = ucfirst(strtolower($ed_arr->THLOOLD_DocumentType));
			}else{
				if( $ed_arr->THLOOLD_LoanCategoryID == '1' or $ed_arr->THLOOLD_LoanCategoryID == '2' ) $tipe_dokumen = "Asli";
				elseif( $ed_arr->THLOOLD_LoanCategoryID == '3') $tipe_dokumen = "Hardcopy";
				elseif( $ed_arr->THLOOLD_LoanCategoryID == '4') $tipe_dokumen = "Softcopy";
				else $tipe_dokumen = "";
			}
			$dengan_cap = "";
			if( $tipe_dokumen == "Hardcopy" || $tipe_dokumen == "Softcopy" ){
				if($tipe_dokumen == "Hardcopy") $cap_atau_watermark = "Watermark";
				elseif($tipe_dokumen == "Softcopy") $cap_atau_watermark = "Cap";
				if( $ed_arr->THLOOLD_DocumentWithWatermarkOrNot == "1" ){ //Arief F - 07092018
					$dengan_cap = " dengan ".$cap_atau_watermark; //Arief F - 07092018
				}elseif( $ed_arr->THLOOLD_DocumentWithWatermarkOrNot == "2" ){ //Arief F - 07092018
					$dengan_cap = " tanpa ".$cap_atau_watermark; //Arief F - 07092018
				}
			}
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
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
		Bersama ini disampaikan bahwa permintaan dokumen '.$tipe_dokumen.''.$dengan_cap.' <b>('.$loanName.')</b>
		oleh '.$requester.' (tujuan permintaan dokumen adalah '.$info.') dengan detail permintaan sebagai berikut :
	</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

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
