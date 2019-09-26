<?php 
error_reporting(E_ALL);
//$sessdir = dirname(dirname(__FILE__)).'/session_dir';
//$sessdir = '../../tmp';
//ini_set('session.save_path', $sessdir); 

session_start(); ?>
<form action='#' method='post'>
	<input name='nama' required /><br>
	<input type='submit'>
</form><br>
<?php if(!empty($_POST['nama'])){
	$_SESSION['nama'] = $_POST['nama'];
	echo "post : ".$_POST['nama']."<br>";
}

echo $_SESSION['nama']."<br>";

if(!empty($_SESSION['nama'])){
	echo "session : ".$_SESSION['nama'];
}

echo "<hr>";
phpinfo();
?>
