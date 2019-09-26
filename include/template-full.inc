<?PHP
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 04 Mei 2012																						=
= Update Terakhir	: 04 Mei 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
error_reporting(0);
include ("./config/config_db.php"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Custodian System</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico">
<script src="./js/MenuBar.js" type="text/javascript"></script>
<link href="./css/MenuBarVertical.css" rel="stylesheet" type="text/css" />
<link href="./css/style.css" rel="stylesheet" type="text/css">
<script src="js/jquery.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
   	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
   	$(".stripeMe tr:even").addClass("alt");
 	});
//disable enter
function stopRKey(evt) { 
  var evt = (evt) ? evt : ((event) ? event : null); 
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
} 
document.onkeypress = stopRKey; 
</script>
</head>

<body>
<?PHP
class Template {
	var $MainContent;
	var $Warning;
	
	function Content($NewMainContent){
	$this->MainContent=$NewMainContent;
	}

	function Warnings($NewWarning){
	$this->Warning=$NewWarning;
	}

	function Show() {
		$this->ShowHeader();
		$this->ShowContent();
		$this->ShowFooter();
	}

	function ShowHeader() {
	echo("
		<div id='header'>
			<div id='header-inside'>
			</div>
		</div>
	");
	}
	
	function ShowContent() {
	echo("
		<div id='content'>
			<div id='content-inside'>");
				echo("$this->MainContent");
				echo("$this->Warning");
				echo("
			</div>
		</div>
	");
	}

	function ShowFooter() {
	echo"
		<div id='footer'>
			<div id='footer-inside'>
				Version 2.0.0<br>
				(C) 2018 PT Triputra Agro Persada.
			</div>
		</div>
	";
	}
}
?>
</body>
</html>