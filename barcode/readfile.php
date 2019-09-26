<?php

if ($handle = opendir('/var/www/html/custodian/barcode')) {
    echo "Directory handle: $handle\n";
    echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
		$pos = strstr($entry, 'format'); 
		if ($pos) echo "$pos -- $entry<br>";
    }

  
    closedir($handle);
}
?>
