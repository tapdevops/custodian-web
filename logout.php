<?PHP
session_start();
include ("./include/mother-variable.php");
include ("./config/config_db.php");
$ip=$_SERVER['REMOTE_ADDR'];

//Catat Action ke Logs
$logs_query="INSERT INTO Logs VALUES ('$mv_UserID',sysdate(),'$ip','logout','logout')";
$mysqli->query($logs_query);

unset($_COOKIE);
setcookie('User_ID', null, -1, "/"); //remove coookie
setcookie('Access_Page', null, -1, "/"); //remove coookie
// session_destroy();
echo "<meta http-equiv='refresh' content='0; url=index.php'>";
?>
