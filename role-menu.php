<?PHP session_start();
include ("./include/mother-variable.php"); ?>
<title>Custodian System | Pengaturan Akses Menu</title>
<head>
<?PHP include ("./config/config_db.php"); ?>

<script type='text/javascript' src='js/jquery.min.js'></script>

<script type='text/javascript'>//<![CDATA[
//check unchek parent child cek box
$(window).load(function(){
$('#treeList :checkbox').change(function (){
    $(this).siblings('ul').find(':checkbox').prop('checked', this.checked);
    if (this.checked) {
        $(this).parentsUntil('#treeList', 'ul').siblings(':checkbox').prop('checked', true);
    } else {
        $(this).parentsUntil('#treeList', 'ul').each(function(){
            var $this = $(this);
            var childSelected = $this.find(':checkbox:checked').length;
            if (!childSelected) {
                $this.prev(':checkbox').prop('checked', false);
            }
        });
    }
});

});//]]>

</script>

<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT
function validateInput(elem) {
	var returnValue;
	returnValue = true;
	var notcheck = true;

	var optRole_ID = document.getElementById('optRole_ID').value;
	var Menu_ID = document.getElementsByName('Menu_ID[]');

	if (optRole_ID==0) {
		alert("Peran Belum Ditentukan!");
		returnValue = false;
	}

	for (var i = 0; i < Menu_ID.length; i++) {
		if (Menu_ID[i].checked) {
			notcheck = false;
		}
	}

	if (notcheck) {
		alert ("Belum Ada Menu Yang Dipilih!");
		returnValue = false;
	}
	return returnValue;
}

</script>

</head>
<body>


<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($mv_UserID) || !(in_array ($path_parts['basename'],$mv_AccessPage))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	if($act=='add') {
$ActionContent ="
	<form name='add-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Tambah Pengaturan Akses Menu</th>
	<tr>
		<td width='30'>Nama Peran</td>
		<td width='70%'>
			<select name='optRole_ID' id='optRole_ID'>
				<option value='0'>--- Pilih Peran ---</option>";
		$query = "SELECT *
				  FROM M_Role
				  WHERE Role_Delete_Time is NULL
				  AND Role_ID NOT IN (select RM_RoleID FROM M_RoleMenu WHERE RM_Delete_Time IS NULL)
				  ORDER BY Role_ID ";
		$sql = mysql_query($query);
		while ($arr=mysql_fetch_array($sql)){
$ActionContent .="
				<option value='$arr[Role_ID]'>$arr[Role_Name]</option>
";
		}
$ActionContent .="
			</select>
		</td>
	</tr>
	<tr>
		<td valign='top'>Daftar Menu</td>
		<td>";
		$query = "SELECT *
				  FROM M_Menu
				  WHERE Menu_Delete_Time is NULL
				  and Menu_ParentID=0
				  ORDER BY Menu_Seq";

		$sql = mysql_query($query);
		$ActionContent .="<ul id='treeList'>";
		$i=0;
		while ($arr=mysql_fetch_array($sql)){
			$i++;
			$ActionContent .="<li  class='parent'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr[Menu_ID]' class='checkbox'>  $arr[Menu_Name]";

			//LEVEL 2
			$query2 ="SELECT *
					  FROM M_Menu
					  WHERE Menu_Delete_Time is NULL
					  and Menu_ParentID = $arr[Menu_ID]
					  ORDER BY Menu_Seq";
			$sql2 = mysql_query($query2);
			$num_rows2 = mysql_num_rows($sql2);
			$j=0;
			while ($arr2=mysql_fetch_array($sql2)){
				$j++;
				if ($j==1) $ActionContent .="<ul>";
				$ActionContent .="<li class='child1'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr2[Menu_ID]'  class='checkbox'>  $arr2[Menu_Name] ";

				//level 3
				$query3= "SELECT *
						  FROM M_Menu
						  WHERE Menu_Delete_Time is NULL
						  and Menu_ParentID = $arr2[Menu_ID]
						  ORDER BY Menu_Seq";
				$sql3 = mysql_query($query3);
				$num_rows3 = mysql_num_rows($sql3);
				$k=0;
				while ($arr3=mysql_fetch_array($sql3)){
					$k++;
					if ($k==1) $ActionContent .="<ul>";
					$ActionContent .="<li class='child2'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr3[Menu_ID]'  class='checkbox'>  $arr3[Menu_Name] </li>";
				}
				if (( $num_rows3) &&($k == $num_rows3)) $ActionContent .="</ul></li>";
			}
			if (( $num_rows2) &&($j == $num_rows2)) $ActionContent .="</ul>";
			$ActionContent .="</li>";
		}

$ActionContent .="</ul>";
$ActionContent .="
		</td>
	</tr>
	<th colspan=3>
		<input name='add' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>
			<td>
	</form>
";
	}

	elseif($act=='edit') {
	$Role_ID=$_GET["id"];

	$query = "SELECT *
			  FROM M_Role
			  WHERE Role_ID='$Role_ID'";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

$ActionContent ="
	<form name='edit-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Ubah Pengaturan Akses Menu</th>
	<tr>
		<td width='30'>Nama Peran</td>
		<td width='70%'><input name='txtRole_ID' type='hidden' value='$field[Role_ID]'/>$field[Role_Name]</td>
	</tr>
	<tr>
		<td valign='top'>Daftar Menu</td>
		<td>";
		$query = "SELECT *
				  FROM M_Menu
				  WHERE Menu_Delete_Time is NULL
				  and Menu_ParentID=0
				  ORDER BY Menu_Seq";

		$sql = mysql_query($query);
		$ActionContent .="<ul id='treeList'>";
		$i=0;
		while ($arr=mysql_fetch_array($sql)){
			$i++;
			$qselectednow="SELECT *
						   FROM M_RoleMenu rm, M_Menu m
						   WHERE rm.RM_Delete_Time is NULL
						   AND rm.RM_MenuID='$arr[Menu_ID]'
						   AND rm.RM_RoleID='$Role_ID'
						   AND rm.RM_MenuID=m.Menu_ID
						   AND m.Menu_Delete_Time IS NULL";
			$sselectednow=mysql_query($qselectednow);
			$valselectednow=mysql_num_rows($sselectednow);

			if ($valselectednow==1) {
			$ActionContent .="<li  class='parent'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox'  value='$arr[Menu_ID]' class='checkbox' checked=yes>  $arr[Menu_Name]";
			}
			else {
			$ActionContent .="<li  class='parent'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr[Menu_ID]' class='checkbox'>  $arr[Menu_Name]";
			}

			//LEVEL 2
			$query2 ="SELECT *
					  FROM M_Menu
					  WHERE Menu_Delete_Time is NULL
					  and Menu_ParentID = $arr[Menu_ID]
					  ORDER BY Menu_Seq";
			$sql2 = mysql_query($query2);
			$num_rows2 = mysql_num_rows($sql2);
			$j=0;
			while ($arr2=mysql_fetch_array($sql2)){
				$j++;
				if ($j==1) $ActionContent .="<ul>";

				$qselectednow="SELECT *
							   FROM M_RoleMenu rm, M_Menu m
							   WHERE rm.RM_Delete_Time is NULL
							   AND rm.RM_MenuID='$arr2[Menu_ID]'
							   AND rm.RM_RoleID='$Role_ID'
							   AND rm.RM_MenuID=m.Menu_ID
							   AND m.Menu_Delete_Time IS NULL"; //Arief F - 14082018
				$sselectednow=mysql_query($qselectednow);
				$valselectednow=mysql_num_rows($sselectednow);

				if ($valselectednow==1) {
				$ActionContent .="<li class='child1'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr2[Menu_ID]'  class='checkbox' checked=yes>  $arr2[Menu_Name] ";
				}
				else {
				$ActionContent .="<li class='child1'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr2[Menu_ID]'  class='checkbox'>  $arr2[Menu_Name] ";
				}

				//level 3
				$query3= "SELECT *
						  FROM M_Menu
						  WHERE Menu_Delete_Time is NULL
						  and Menu_ParentID = $arr2[Menu_ID]
						  ORDER BY Menu_Seq";
				$sql3 = mysql_query($query3);
				$num_rows3 = mysql_num_rows($sql3);
				$k=0;
				while ($arr3=mysql_fetch_array($sql3)){
					$k++;
					if ($k==1) $ActionContent .="<ul>";

					$qselectednow="SELECT *
								   FROM M_RoleMenu rm, M_Menu m
								   WHERE rm.RM_Delete_Time is NULL
								   AND rm.RM_MenuID='$arr3[Menu_ID]'
								   AND rm.RM_RoleID='$Role_ID'
								   AND rm.RM_MenuID=m.Menu_ID
								   AND m.Menu_Delete_Time IS NULL"; //Arief F - 14082018
					$sselectednow=mysql_query($qselectednow);
					$valselectednow=mysql_num_rows($sselectednow);

					if ($valselectednow==1) {
					$ActionContent .="<li class='child2'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr3[Menu_ID]'  class='checkbox' checked=yes>  $arr3[Menu_Name] </li>";
					}
					else {
					$ActionContent .="<li class='child2'><input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr3[Menu_ID]'  class='checkbox'>  $arr3[Menu_Name] </li>";
					}
				}
				if (( $num_rows3) &&($k == $num_rows3)) $ActionContent .="</ul></li>";
			}
			if (( $num_rows2) &&($j == $num_rows2)) $ActionContent .="</ul>";
			$ActionContent .="</li>";
		}

$ActionContent .="</ul>";
$ActionContent .="
		</td>
	</tr>
	<th colspan=3>
		<input name='edit' type='submit' value='Simpan' class='button' onclick='return validateInput(this);'/>
		<input name='cancel' type='submit' value='Batal' class='button'/>
	</th>
	</table>



	</form>
";
	}

	elseif($act=='delete')	{
	$Role_ID=$_GET["id"];
	$query0= "SELECT *
			  FROM M_Role
			  WHERE Role_ID='$Role_ID'";
	$sql0 = mysql_query($query0);
	$field0 = mysql_fetch_array($sql0);

$ActionContent ="
	<form name='delete-menu' method='post' action='$PHP_SELF'>
	<table width='100%' id='mytable' class='stripeMe'>
	<th colspan=3>Anda Ingin Menghapus Pengaturan Akses Menu Berikut?</th>
	<tr>
		<td width='30'>Nama Peran</td>
		<td width='70%'><input name='txtRole_ID' type='hidden' value='$field0[Role_ID]'/>$field0[Role_Name]</td>
	</tr>
	<tr>
		<td valign='top'>Daftar Menu</td>
		<td>";

		$query = "SELECT m.Menu_ID, m.Menu_Name
				  FROM M_Menu m, M_RoleMenu rm
				  WHERE rm.RM_Delete_Time is NULL
				  AND m.Menu_ID=rm.RM_MenuID
				  AND m.Menu_ParentID='0'
				  AND rm.RM_RoleID='$field0[Role_ID]'
				  ORDER BY m.Menu_Seq ";
		$sql = mysql_query($query);
$ActionContent .="
			<ul id='treeList'>";
			$i=0;
		while ($arr=mysql_fetch_array($sql)){
			$i++;
$ActionContent .="
			<li  class='parent'>
				<input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr[Menu_ID]' class='checkbox' checked=yes DISABLED> $arr[Menu_Name]";

			//level 2
			$query2 = "SELECT *
				  	   FROM M_Menu
				       WHERE Menu_Delete_Time is NULL
				  	   and Menu_ParentID = $arr[Menu_ID]
				  	   ORDER BY Menu_Seq";
			$sql2 = mysql_query($query2);
			$num_rows2 = mysql_num_rows($sql2);
			$j=0;
			while ($arr2=mysql_fetch_array($sql2)){
				$j++;
				if ($j==1) $ActionContent .="<ul>";
$ActionContent .="
					<li class='child1'>
					<input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr2[Menu_ID]'  class='checkbox' checked=yes DISABLED> $arr2[Menu_Name] ";

					//level 3
					$query3 = "SELECT *
							   FROM M_Menu
							   WHERE Menu_Delete_Time is NULL
							   and Menu_ParentID = $arr2[Menu_ID]
							   ORDER BY Menu_Seq";
					$sql3 = mysql_query($query3);
					$num_rows3 = mysql_num_rows($sql3);
					$k=0;
					while ($arr3=mysql_fetch_array($sql3)){
					$k++;
					if ($k==1) $ActionContent .="<ul>";
$ActionContent .="
							<li class='child2'>
								<input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr3[Menu_ID]'  class='checkbox' class='checkbox' checked=yes DISABLED> $arr3[Menu_Name]
								</li>";
					}
					if (( $num_rows3) &&($k == $num_rows3)) $ActionContent .="</ul></li>";
				}
				if (( $num_rows2) &&($j == $num_rows2)) $ActionContent .="</ul>";
				$ActionContent .="</li>";
			}
$ActionContent .="</ul>";
$ActionContent .="</td>";
$ActionContent .="
	</tr>
	<th colspan=3>
		<input name='delete' type='submit' value='Ya' class='button'/>
		<input name='cancel' type='submit' value='Tidak' class='button'/>
	</th>
	</table>
	</form>
";
	}
}

$dataPerPage = 20;
if(isset($_GET['page'])){
    $noPage = $_GET['page'];
}
else $noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$query0 ="SELECT DISTINCT r.Role_Name, r.Role_ID
		  FROM M_Menu m, M_RoleMenu rm, M_Role r
		  WHERE rm.RM_Delete_Time is NULL
		  AND r.Role_ID=rm.RM_RoleID
		  AND r.Role_Delete_Time IS NULL
		  AND m.Menu_Delete_Time IS NULL
		  LIMIT $offset, $dataPerPage";
$sql0 = mysql_query($query0);
$num0 = mysql_num_rows($sql0);

if ($num0==NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>Peran</th>
		<th>Menu</th>
	</tr>
	<tr>
		<td colspan=3 align='center'>Belum Ada Data</td>
	</tr>
";
}

if ($num0<>NULL){
$MainContent ="
	<table width='100%' border='1' class='stripeMe'>
	<tr>
		<th>Peran</th>
		<th>Menu</th>
		<th></th>
	</tr>
";

	while ($field0 = mysql_fetch_array($sql0)){
$MainContent .="
	<tr>
		<td class='center' valign='top'>$field0[Role_Name]</td>
		<td>";

		$query = "SELECT m.Menu_ID, m.Menu_Name
				  FROM M_Menu m, M_RoleMenu rm
				  WHERE rm.RM_Delete_Time is NULL
				  AND m.Menu_ID=rm.RM_MenuID
				  AND m.Menu_ParentID='0'
				  AND rm.RM_RoleID='$field0[Role_ID]'
				  AND m.Menu_Delete_Time IS NULL
				  ORDER BY m.Menu_Seq ";
		$sql = mysql_query($query);
$MainContent .="
			<ul id='treeList'>";
			$i=0;
		while ($arr=mysql_fetch_array($sql)){
			$i++;
$MainContent .="
			<li  class='parent'>
				<input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr[Menu_ID]' class='checkbox' checked=yes DISABLED> $arr[Menu_Name]";

			//level 2
			// $query2 = "SELECT *
			// 	  	   FROM M_Menu
			// 	       WHERE Menu_Delete_Time is NULL
			// 	  	   and Menu_ParentID = $arr[Menu_ID]
			// 	  	   ORDER BY Menu_Seq";
           $query2 = "SELECT *
				  	   FROM M_Menu m, M_RoleMenu rm
				       WHERE m.Menu_Delete_Time is NULL
				  	   AND m.Menu_ParentID = $arr[Menu_ID]
                       AND rm.RM_MenuID = m.Menu_ID
                       AND rm.RM_RoleID='$field0[Role_ID]'
                       AND rm.RM_Delete_Time is NULL
				  	   ORDER BY m.Menu_Seq"; //Arief F - 14082018
			$sql2 = mysql_query($query2);
			$num_rows2 = mysql_num_rows($sql2);
			$j=0;
			while ($arr2=mysql_fetch_array($sql2)){
				$j++;
				if ($j==1) $MainContent .="<ul>";
$MainContent .="
					<li class='child1'>
					<input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr2[Menu_ID]'  class='checkbox' checked=yes DISABLED> $arr2[Menu_Name] ";

					//level 3
					// $query3 = "SELECT *
					// 		   FROM M_Menu
					// 		   WHERE Menu_Delete_Time is NULL
					// 		   and Menu_ParentID = $arr2[Menu_ID]
					// 		   ORDER BY Menu_Seq";
                   $query3 = "SELECT *
        				  	   FROM M_Menu m, M_RoleMenu rm
        				       WHERE m.Menu_Delete_Time is NULL
        				  	   AND m.Menu_ParentID = $arr2[Menu_ID]
                               AND rm.RM_MenuID = m.Menu_ID
                               AND rm.RM_RoleID='$field0[Role_ID]'
                               AND rm.RM_Delete_Time is NULL
        				  	   ORDER BY m.Menu_Seq"; //Arief F - 14082018
					$sql3 = mysql_query($query3);
					$num_rows3 = mysql_num_rows($sql3);
					$k=0;
					while ($arr3=mysql_fetch_array($sql3)){
					$k++;
					if ($k==1) $MainContent .="<ul>";
$MainContent .="
							<li class='child2'>
								<input id='Menu_ID[]' name='Menu_ID[]' type='checkbox' value='$arr3[Menu_ID]'  class='checkbox' class='checkbox' checked=yes DISABLED> $arr3[Menu_Name]
								</li>";
					}
					if (( $num_rows3) &&($k == $num_rows3)) $MainContent .="</ul></li>";
				}
				if (( $num_rows2) &&($j == $num_rows2)) $MainContent .="</ul>";
				$MainContent .="</li>";
			}
$MainContent .="</ul>";
$MainContent .="</td>";
$MainContent .="
		<td class='center' valign='top'>
			<b><a href='$PHP_SELF?act=edit&id=$field0[Role_ID]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
			<a href='$PHP_SELF?act=delete&id=$field0[Role_ID]'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a></b>
		</td>
	</tr>
";
 	}
}
$MainContent .="
	</table>

";

$query1= "SELECT DISTINCT r.Role_Name, r.Role_ID
		  FROM M_Menu m, M_RoleMenu rm, M_Role r
		  WHERE rm.RM_Delete_Time is NULL
		  AND r.Role_ID=rm.RM_RoleID
		  AND r.Role_Delete_Time IS NULL
		  AND m.Menu_Delete_Time IS NULL";
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);
$prev=$noPage-1;
$next=$noPage+1;
if ($noPage > 1) $Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";

for($p=1; $p<=$jumPage; $p++){
         if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {
            if (($showPage == 1) && ($p != 2))  $Pager.="...";
            if (($showPage != ($jumPage - 1)) && ($p == $jumPage))  $Pager.="...";
            if ($p == $noPage) $Pager.="<b><u>$p</b></u> ";
            else $Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";
            $showPage = $p;
         }
}
if ($noPage < $jumPage) $Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a>";

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=role-menu.php'>";
}

elseif(isset($_POST[add])) {
	$Menu_ID=$_POST[Menu_ID];
	$jumlah=count($Menu_ID);

	for ($i=0; $i<$jumlah; $i++) {
		$sql= "INSERT INTO M_RoleMenu
			   VALUES (NULL,'$_POST[optRole_ID]','$Menu_ID[$i]','$mv_UserID',
					   sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
		$mysqli->query($sql);
	}
	echo "<meta http-equiv='refresh' content='0; url=role-menu.php'>";
}

elseif(isset($_POST[edit])) {
	$sql= "UPDATE M_RoleMenu
		   SET RM_Delete_UserID='$mv_UserID', RM_Delete_Time=sysdate()
		   WHERE RM_RoleID='$_POST[txtRole_ID]'";
	if($mysqli->query($sql)) {
		$Menu_ID=$_POST[Menu_ID];
		$jumlah=count($Menu_ID);

		for ($i=0; $i<$jumlah; $i++) {
			$sql= "INSERT INTO M_RoleMenu
				   VALUES (NULL,'$_POST[txtRole_ID]','$Menu_ID[$i]','$mv_UserID',
						   sysdate(),'$mv_UserID', sysdate(),NULL,NULL)";
			$mysqli->query($sql);
		}
		echo "<meta http-equiv='refresh' content='0; url=role-menu.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Perubahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[delete])) {
	$sql= "UPDATE M_RoleMenu
		   SET RM_Delete_UserID='$mv_UserID', RM_Delete_Time=sysdate()
		   WHERE RM_RoleID='$_POST[txtRole_ID]'";
	if($mysqli->query($sql)) {
		echo "<meta http-equiv='refresh' content='0; url=role-menu.php'>";
	}
	else {
		$ActionContent .="<div class='warning'>Penghapusan Data Gagal.</div>";
	}
}


$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
</body>
