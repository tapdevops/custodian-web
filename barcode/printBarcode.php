
<html>
   <head><!--META HTTP-EQUIV="REFRESH" CONTENT="10"--><title>jZebra Demo</title>
   
   <script type="text/javascript" src="jzebra.js"></script>
   <script type="text/javascript">
      var sleepCounter = 0;
   
    function print(file) {		
         var applet = document.jZebra;
         if (applet != null) {
			//find printer
            applet.findPrinter("zebra");
			monitorFinding();
			
			//applet.appendFile(getPath() + "format_barcode.txt");
//alert (file);
			applet.appendFile(getPath() + file);
	        applet.print();		
			monitorPrinting();
			var t=setTimeout("deleteFile('"+file+"')",3000);
		}	 
    }
	
	function deleteFile(randomFile)
	{
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
		}
	  }
	xmlhttp.open("GET","deleteFile.php?randomFile="+randomFile,true);
	xmlhttp.send();
	}
	
     function getPath() {
          var path = window.location.href;
          return path.substring(0, path.lastIndexOf("/")) + "/";
      }
	  
    function monitorPrinting() {
		var applet = document.jZebra;
		if (applet != null) {
		   if (!applet.isDonePrinting()) {
			  window.setTimeout('monitorPrinting()', 100);
		   } else {
			  var e = applet.getException();
			 // alert(e == null ? "Printed Successfully" : "Exception occured: " + e.getLocalizedMessage());
		   }
		} else {
				alert("Applet not loaded!");
			}
    }
      
    function monitorFinding() {
		var applet = document.jZebra;
		var printer ;
		if (applet != null) {
		   if (!applet.isDoneFinding()) {
			  window.setTimeout('monitorFinding()', 100);
		   } else {
			  printer = applet.getPrinterName();
		     //alert(printer == null ? "Printer tidak tersedia" : "Printer \"" + printer + "\" tersedia");
			 //if (printer == null ) alert( "Printer tidak tersedia" );
		   }
		} else {
				alert("Applet not loaded!");
		}
		return printer;
    }




   </script>
   </head>
   
<?php

if ($handle = opendir('/var/www/html/custodian/barcode')) {
    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
		$pos = strstr($entry, 'format'); 
		if ($pos) $file = $entry;
    }
	echo "$file<br>";
  
    closedir($handle);
}
?>
   <body bgcolor="#F0F0F0" onload="print('<?=$file?>')" >
   
   
   <applet name="jZebra" code="jzebra.PrintApplet.class" archive="./jzebra.jar" width="100" height="100">
      <param name="printer" value="zebra">
      <!-- <param name="sleep" value="200"> -->
   </applet><br><br>
	<a href="./jzebra.jar">click me</a>
   <input type=button  value="Print"><br>

</html>


