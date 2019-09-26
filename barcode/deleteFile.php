<? 

  // STILL INSECURE!!!!
  // Do not use in any public place without authentication.
  // Allows deletion of any file within /my/files
  // Usage: filename.php?file=filename 

  //$basedir = "/my/files";
  //$file_to_delete = "format_barcode".$_REQUEST["randomFile"].".txt";  

  $path = realpath($_REQUEST["randomFile"]);
  
  if (substr($path, 0, strlen($basedir)) != $basedir)
   die ("Access denied"); 
  unlink($path);

?>
