<?php
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Nicholas Budihardja																				=
= Dibuat Tanggal	: April 2012																						=
= Update Terakhir	: April 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
class custodian_encryp 
{
	function encrypt($sData, $sKey='custTAP123'){ 
		$sResult = ''; 
		for($i=0;$i<strlen($sData);$i++){ 
			$sChar    = substr($sData, $i, 1); 
			$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1); 
			$sChar    = chr(ord($sChar) + ord($sKeyChar)); 
			$sResult .= $sChar; 
		} 
		return $this->encode_base64($sResult); 
	} 
	
	function decrypt($sData, $sKey='custTAP123'){ 
		$sResult = ''; 
		$sData   = $this->decode_base64($sData); 
		for($i=0;$i<strlen($sData);$i++){ 
			$sChar    = substr($sData, $i, 1); 
			$sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1); 
			$sChar    = chr(ord($sChar) - ord($sKeyChar)); 
			$sResult .= $sChar; 
		} 
		return $sResult; 
	} 
	
	function encode_base64($sData){
		$sBase64 = base64_encode($sData);
		return str_replace('=', '', strtr($sBase64, '+/', '-_'));
	}
	
	function decode_base64($sData){
		$sBase64 = strtr($sData, '-_', '+/');
		return base64_decode($sBase64.'==');
	}
}
?>