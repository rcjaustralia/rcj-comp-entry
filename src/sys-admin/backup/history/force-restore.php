<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  $filePathAndName = 'rcja.clubengineer.org-20170219-075444.sql';
	
  if (file_exists($filePathAndName)) {
	
    $cmd = 
      C_MYSQL . 
      ' -h ' . C_DB_HOST . 
	  ' -u '. C_DB_USER_NAME . 
  	  ' -p' . C_DB_PASSWORD .  
	  ' ' . C_DB_NAME . 
	  ' < ' . $filePathAndName; 
    exec($cmd);
    echo '<html><p>Restore from "' . $filePathAndName . '" done.</p>
         <p><a href="/sys-admin/db/">Restructure database scripts</a></p>
         <p><a href="/">Home</a></p>    
         </html>';
  } else {
    echo '<html><p>Can not find the file to restore from "' . $filePathAndName . '".</p></html>';      
  }

?>  