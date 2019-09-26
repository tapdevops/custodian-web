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
session_start(); 
?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title>Custodian System</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico">
<script src="./js/MenuBar.js" type="text/javascript"></script>
<link href="./css/MenuBarVertical.css" rel="stylesheet" type="text/css" />
<link href="./css/style.css" rel="stylesheet" type="text/css">
<script src="js/jquery.min.js" type="text/javascript" ></script>
<script src="js/jquery.js" type="text/javascript" ></script>
<script src="js/ajaxfileupload.js" type="text/javascript"></script>
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
	var $ActionContent;
	var $Pager;
	
	function Content($NewMainContent){
	$this->MainContent=$NewMainContent;
	}

	function ActContent($NewActionContent){
	$this->ActionContent=$NewActionContent;
	}
	
	function Pagers($NewPager){
	$this->Pager=$NewPager;
	}

	function show_childMenu($Menu_ID) {
		$s_Result = "";
		if ($Menu_ID<>NULL) {
			$temquery = "SELECT DISTINCT m.Menu_ID,m.Menu_Name,m.Menu_Link,m.Menu_Attribute1
					  FROM M_Menu m, M_RoleMenu rm, M_UserRole ur
					  WHERE m.Menu_Delete_Time is NULL  
					  AND rm.RM_Delete_Time is NULL
					  AND m.Menu_ParentID='$Menu_ID'  
					  AND m.Menu_ID=rm.RM_MenuID
					  AND rm.RM_RoleID=ur.MUR_RoleID
					  AND ur.MUR_UserID='$_COOKIE[User_ID]'
					  ORDER BY m.Menu_Seq, m.Menu_ID";
			$temsql = mysql_query($temquery);
			$temnum = mysql_num_rows($temsql);
			if ($temnum>0) {
				$s_Result="<ul style='background:#FFC;'>";
				while ($temrow = mysql_fetch_array($temsql)) 
				{
					$s_Result=$s_Result."<li style='background:#FFC;'><a href=$temrow[Menu_Link]>$temrow[Menu_Name]</a>";
					$s_Result=$s_Result.$this->show_childMenu($temrow['Menu_ID']);
					$s_Result=$s_Result."</li>";
				}
				$s_Result=$s_Result."</ul>";
			}
		}
		return $s_Result;
	}
	
	function Show() {
		$this->ShowHeader();
		$this->ShowContent();
		$this->ShowFooter();
	}

	function ShowWTopMenu() {
		$this->ShowHeader();
		$this->ShowContentWTopMenu();
		$this->ShowFooter();
	}

	function ShowHeader() {
	$qUser="SELECT * FROM M_User WHERE User_ID='$_COOKIE[User_ID]'";
	$sUser=mysql_query($qUser);
	$obj=mysql_fetch_object($sUser);
	echo("
		<div id='header'>
			<div id='header-inside'>
				<div id='welcomemsg'>
					Selamat Datang,<br /><b>".$obj->User_FullName."</b>
				</div>
			</div>
		</div>
	");
	}
	
	function ShowContent() {
	echo("
		<div id='content'>
			<div id='content-inside'>
				<div id='sidebar-left'>
					<div class='main-menu-title'>Menu Utama</div>
					<ul id='MenuBar1' class='MenuBarVertical'>");
  	$temquery = "SELECT DISTINCT m.Menu_ID,m.Menu_Name,m.Menu_Link,m.Menu_Attribute1
			  FROM M_Menu m, M_RoleMenu rm, M_UserRole ur
			  WHERE m.Menu_Delete_Time is NULL  
			  AND rm.RM_Delete_Time is NULL
			  AND m.Menu_ParentID='0' 
			  AND m.Menu_ID=rm.RM_MenuID
			  AND rm.RM_RoleID=ur.MUR_RoleID
			  AND ur.MUR_UserID='$_COOKIE[User_ID]'
			  ORDER BY m.Menu_Seq, m.Menu_ID";
	$temsql = mysql_query($temquery);
	while ($temfield = mysql_fetch_array($temsql)){
		echo("<li>");
		$s_tmp_child = $this->show_childMenu($temfield['Menu_ID']);
		if ($s_tmp_child=="")
			echo("<a ");
		else
			echo("<a class=MenuBarItemSubmenu ");
			
		if ($temfield['Menu_Attribute1']=="")
			echo("href='$temfield[Menu_Link]'>$temfield[Menu_Name]</a>");
		else
			echo("href='$temfield[Menu_Link]'><img src='./images/$temfield[Menu_Attribute1]' class='icon-menu'>$temfield[Menu_Name]</a>");
		echo ("$s_tmp_child");
		echo("</li>");
	}
			  echo("</ul>
			  		<script type='text/javascript'>
					var MenuBar1 = new Spry.Widget.MenuBar('MenuBar1', {imgRight:'../images/right-arrow-hover.png'});
					</script>
				</div>
				<div id='main-area'>
					<div id='top-menu'>
						<a href='$PHP_SELF?act=add' class='add-icon'>Tambah Baru</a>
					</div>
					<div id='action-area'>");
				echo("$this->ActionContent");	
				echo("</div><div id='content-area'>");
				echo("$this->MainContent");
				echo("<div id='pager-area'>");
				echo("$this->Pager");
				echo("</div></div></div>
			</div>
		</div>
	");
	}
	
	function ShowContentWTopMenu() {
	echo("
		<div id='content'>
			<div id='content-inside'>
				<div id='sidebar-left'>
					<div class='main-menu-title'>Menu Utama</div>
					<ul id='MenuBar1' class='MenuBarVertical'>");
  	$temquery = "SELECT DISTINCT m.Menu_ID,m.Menu_Name,m.Menu_Link,m.Menu_Attribute1
			  FROM M_Menu m, M_RoleMenu rm, M_UserRole ur
			  WHERE m.Menu_Delete_Time is NULL  
			  AND rm.RM_Delete_Time is NULL
			  AND m.Menu_ParentID='0' 
			  AND m.Menu_ID=rm.RM_MenuID
			  AND rm.RM_RoleID=ur.MUR_RoleID
			  AND ur.MUR_UserID='$_COOKIE[User_ID]'
			  ORDER BY m.Menu_Seq, m.Menu_ID";
			  
	$temsql = mysql_query($temquery);
	while ($temfield = mysql_fetch_array($temsql)){
		echo("<li>");
		$s_tmp_child = $this->show_childMenu($temfield['Menu_ID']);
		if ($s_tmp_child=="")
			echo("<a ");
		else
			echo("<a class=MenuBarItemSubmenu ");
			
		if ($temfield['Menu_Attribute1']=="")
			echo("href='$temfield[Menu_Link]'>$temfield[Menu_Name]</a>");
		else
			echo("href='$temfield[Menu_Link]'><img src='./images/$temfield[Menu_Attribute1]' class='icon-menu'>$temfield[Menu_Name]</a>");
		echo ("$s_tmp_child");
		echo("</li>");
	}
			  echo("</ul>
			  		<script type='text/javascript'>
					var MenuBar1 = new Spry.Widget.MenuBar('MenuBar1', {imgRight:'../images/right-arrow-hover.png'});
					</script>
				</div>
				<div id='main-area'>
					<div id='action-area'>");
				echo("$this->ActionContent");	
				echo("</div><div id='content-area'>");
				echo("$this->MainContent");
				echo("<div id='pager-area'>");
				echo("$this->Pager");
				echo("</div></div></div>
			</div>
		</div>
	");
	}

	function ShowFooter() {
	echo"
		<div id='footer'>
			<div id='footer-inside'>
				Version ".$_COOKIE['version']."<br>
				(C) 2018 PT Triputra Agro Persada.
			</div>
		</div>
	";
	}
}
?>
</body>
</html>