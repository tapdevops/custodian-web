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
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query ="	SELECT User_ID,User_FullName,User_Email,A_TransactionCode,
					   ARC_AID,ARC_RandomCode,THLOAOD_Information,THLOAOD_LoanDate,THLOAOD_LoanCategoryID,
					   THLOAOD_DocumentType, THLOAOD_DocumentWithWatermarkOrNot,
					   LoanCategory_Name,A_Step,A_ApproverID
				FROM TH_LoanOfAssetOwnershipDocument
				LEFT JOIN M_Approval
					ON THLOAOD_LoanCode=A_TransactionCode
					AND A_Status='2'
					AND A_Delete_Time IS NULL
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				LEFT JOIN M_LoanCategory
					ON THLOAOD_LoanCategoryID = LoanCategory_ID
				WHERE THLOAOD_LoanCode='$loanCode'
				AND THLOAOD_Delete_Time IS NULL";
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


		$ed_query="	SELECT DISTINCT	DAO_NoPolisi,
						CASE WHEN DAO_Employee_NIK LIKE 'CO@%'
						  THEN
							(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(DAO_Employee_NIK, 'CO@', ''))
						  ELSE
							(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=DAO_Employee_NIK)
						END nama_pemilik,
 					    m_mk.MK_Name merk_kendaraan,
						CASE WHEN DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
							WHEN DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
							ELSE DATE_FORMAT(DAO_STNK_StartDate, '%d/%m/%Y')
						END AS start_stnk,
						CASE WHEN DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
							WHEN DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
							ELSE DATE_FORMAT(DAO_STNK_ExpiredDate, '%d/%m/%Y')
						END AS expired_stnk,
						DAO_RegTime,THLOAOD_Reason,THLOAOD_UserID,User_FullName,
						db_master.M_Employee.Employee_Department,
						db_master.M_Employee.Employee_Division
					FROM TH_LoanOfAssetOwnershipDocument
					LEFT JOIN TD_LoanOfAssetOwnershipDocument
						ON TDLOAOD_THLOAOD_ID=THLOAOD_ID
					LEFT JOIN M_DocumentAssetOwnership
						ON TDLOAOD_DocCode=DAO_DocCode
					LEFT JOIN M_User
						ON THLOAOD_UserID=User_ID
					LEFT JOIN db_master.M_MerkKendaraan m_mk
                        ON DAO_MK_ID=m_mk.MK_ID
				    LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
					WHERE THLOAOD_LoanCode='$loanCode'
					AND THLOAOD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;

		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>
								No. Polisi : '.$ed_arr->DAO_NoPolisi.'<br />
								Nama Pemilik : '.$ed_arr->nama_pemilik.'<br>
                                Merk Kendaraan : '.$ed_arr->merk_kendaraan.'<br>
								Masa Berlaku STNK : '.$ed_arr->start_stnk.' s/d
								'.$ed_arr->expired_stnk.'<br>
								Tgl. Terbit : '.date('d/m/Y H:i:s', strtotime($ed_arr->DAO_RegTime)).'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$requester=ucwords(strtolower($ed_arr->User_FullName));
			$requester_dept=ucwords(strtolower($ed_arr->Employee_Department));
			$requester_div=ucwords(strtolower($ed_arr->Employee_Division));
		}
		if($row->THLOAOD_DocumentType == "ORIGINAL" ){
			$tipe_dokumen = "Asli";
		}elseif($row->THLOAOD_DocumentType == "SOFTCOPY"){
			$tipe_dokumen = ucfirst(strtolower($row->THLOAOD_DocumentType));
		}elseif($row->THLOAOD_DocumentType == "HARDCOPY"){
			$tipe_dokumen = ucfirst(strtolower($row->THLOAOD_DocumentType));
		}else{
			if( $row->THLOAOD_LoanCategoryID == '1' or $row->THLOAOD_LoanCategoryID == '2' ) $tipe_dokumen = "Asli";
			elseif( $row->THLOAOD_LoanCategoryID == '3') $tipe_dokumen = "Hardcopy";
			elseif( $row->THLOAOD_LoanCategoryID == '4') $tipe_dokumen = "Softcopy";
			else $tipe_dokumen = "";
		}
		$dengan_cap = "";
		if( $tipe_dokumen == "Hardcopy" || $tipe_dokumen == "Softcopy" ){
			if($tipe_dokumen == "Hardcopy") $cap_atau_watermark = "Watermark";
			elseif($tipe_dokumen == "Softcopy") $cap_atau_watermark = "Cap";
			if( $row->THLOAOD_DocumentWithWatermarkOrNot == "1" ){ //Arief F - 07092018
				$dengan_cap = " dengan ".$cap_atau_watermark; //Arief F - 07092018
			}elseif( $row->THLOAOD_DocumentWithWatermarkOrNot == "2" ){ //Arief F - 07092018
				$dengan_cap = " tanpa ".$cap_atau_watermark; //Arief F - 07092018
			}
		}
		//$asli = ($row->THLOAOD_LoanCategoryID != '3') ? ' Asli ' : '';
		$keteranganPermintaan = "";
		if( $row->THLOAOD_Information != null or $row->THLOAOD_Information != "" ){
			$keteranganPermintaan = "(tujuan permintaan dokumen adalah ".$row->THLOAOD_Information.")";
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
				<p align=center>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: #111;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodocao.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a>
					</span>
					<span style="border: 1px solid red;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(239, 100, 100);color: #111;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: #111;" href="http://'.$_SERVER['HTTP_HOST'].'/act.mail.lodocao.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a>
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

		$ed_query="	SELECT DISTINCT THLOAOD_LoanCategoryID,
						THLOAOD_DocumentType, THLOAOD_DocumentWithWatermarkOrNot,
						DAO_RegTime,THLOAOD_Reason,THLOAOD_UserID,User_FullName,
						THLOAOD_Information, LoanCategory_Name,
						CASE WHEN DAO_STNK_StartDate LIKE '%0000-00-00%' THEN '-'
							WHEN DAO_STNK_StartDate LIKE '%1970-01-01%' THEN '-'
							ELSE DATE_FORMAT(DAO_STNK_StartDate, '%d/%m/%Y')
						END AS start_stnk,
						CASE WHEN DAO_STNK_ExpiredDate LIKE '%0000-00-00%' THEN '-'
							WHEN DAO_STNK_ExpiredDate LIKE '%1970-01-01%' THEN '-'
							ELSE DATE_FORMAT(DAO_STNK_ExpiredDate, '%d/%m/%Y')
						END AS expired_stnk,
						DAO_NoPolisi,
						CASE WHEN DAO_Employee_NIK LIKE 'CO@%'
						  THEN
							(SELECT mc.Company_Name FROM M_Company mc WHERE mc.Company_code = REPLACE(DAO_Employee_NIK, 'CO@', ''))
						  ELSE
							(SELECT me.Employee_FullName FROM db_master.M_Employee me WHERE me.Employee_NIK=DAO_Employee_NIK)
						END nama_pemilik,
 					    m_mk.MK_Name merk_kendaraan
					FROM TH_LoanOfAssetOwnershipDocument
					LEFT JOIN TD_LoanOfAssetOwnershipDocument
						ON TDLOAOD_THLOAOD_ID=THLOAOD_ID
					LEFT JOIN M_LoanCategory
						ON THLOAOD_LoanCategoryID = LoanCategory_ID
					LEFT JOIN M_DocumentAssetOwnership
						ON TDLOAOD_DocCode=DAO_DocCode
					LEFT JOIN M_User
						ON THLOAOD_UserID=User_ID
					LEFT JOIN db_master.M_MerkKendaraan m_mk
						ON DAO_MK_ID=m_mk.MK_ID
					WHERE THLOAOD_LoanCode='$loanCode'
					AND THLOAOD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {
			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>
								No. Polisi : '.$ed_arr->DAO_NoPolisi.'<br />
								Nama Pemilik : '.$ed_arr->nama_pemilik.'<br>
                                Merk Kendaraan : '.$ed_arr->merk_kendaraan.'<br>
								Masa Berlaku STNK : '.$ed_arr->start_stnk.' s/d
								'.$ed_arr->expired_stnk.'<br>
								Tgl. Terbit : '.date('d/m/Y H:i:s', strtotime($ed_arr->DAO_RegTime)).'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$reason=$ed_arr->THLOAOD_Reason;
			$regUser=$ed_arr->THLOAOD_UserID;
			$requester=$ed_arr->User_FullName;
			$company=$ed_arr->Company_ID;
			$info=$ed_arr->THLOAOD_Information;
			$loanName = $ed_arr->LoanCategory_Name;
			//$asli = ($ed_arr->THLOAOD_LoanCategoryID != '3') ? ' Asli ' : '';
			if($ed_arr->THLOAOD_DocumentType == "ORIGINAL" ){
				$tipe_dokumen = "Asli";
			}elseif($ed_arr->THLOAOD_DocumentType == "HARDCOPY" or $ed_arr->THLOAOD_DocumentType == "SOFTCOPY"){
				$tipe_dokumen = ucfirst(strtolower($ed_arr->THLOAOD_DocumentType));
			}else{
				if( $ed_arr->THLOAOD_LoanCategoryID == '1' or $ed_arr->THLOAOD_LoanCategoryID == '2' ) $tipe_dokumen = "Asli";
				elseif( $ed_arr->THLOAOD_LoanCategoryID == '3') $tipe_dokumen = "Hardcopy";
				elseif( $ed_arr->THLOAOD_LoanCategoryID == '4') $tipe_dokumen = "Softcopy";
				else $tipe_dokumen = "";
			}
			$dengan_cap = "";
			if( $tipe_dokumen == "Hardcopy" || $tipe_dokumen == "Softcopy" ){
				if($tipe_dokumen == "Hardcopy") $cap_atau_watermark = "Watermark";
				elseif($tipe_dokumen == "Softcopy") $cap_atau_watermark = "Cap";
				if( $ed_arr->THLOAOD_DocumentWithWatermarkOrNot == "1" ){ //Arief F - 07092018
					$dengan_cap = " dengan ".$cap_atau_watermark; //Arief F - 07092018
				}elseif( $ed_arr->THLOAOD_DocumentWithWatermarkOrNot == "2" ){ //Arief F - 07092018
					$dengan_cap = " tanpa ".$cap_atau_watermark; //Arief F - 07092018
				}
			}
			$keteranganPermintaan = "";
			if( $ed_arr->THLOAOD_Information != null or $ed_arr->THLOAOD_Information != "" ){
				$keteranganPermintaan = "(tujuan permintaan dokumen adalah ".$info.")";
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
		oleh '.$requester.' '.$keteranganPermintaan.' dengan detail permintaan sebagai berikut :
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
