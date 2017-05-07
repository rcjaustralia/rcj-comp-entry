<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
function echoLog($fileName){
    
  echo "<h2 style='margin-left:30px'>$fileName</h2>";    
  echo '<p style="margin-left:30px"><a href="/">Home</a> | ' . 
       '<a href="/log">Refresh</a> | ' . 
       '<a href="javascript:cePost(\'/log/delete.php\', {fileName: \'' . $fileName . '\'})">Delete</a></p>';
  echo '<pre style="margin-left:60px">';
  echo file_get_contents($fileName);
  echo '</pre>';
    
}
  
  if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
      exit(); //==>>
  }

  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Error log');
    
  $path = realpath($_SERVER["DOCUMENT_ROOT"]);
  $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
  foreach($objects as $name => $object){
    if (basename($name) == 'error_log'){
      echoLog($name);
    }
  }

  echo '</html>';
?>
