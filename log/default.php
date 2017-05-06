<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  

  if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
      exit(); //==>>
  }

  $logFileName = $_SERVER["DOCUMENT_ROOT"] . '/log/error.log';
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Error log');
  
  echo '<p><a href="/">Home</a> | <a href="/log">Refresh</a> | <a href="/log/delete.php">Empty log</a></p>';
  echo '<pre style="margin-left:30px">';
  if (file_exists($logFileName)){
    echo file_get_contents($logFileName);
  }else{
    echo 'No log file to view.';
  }  
  echo '</pre>';
  
  echo '<p><a href="/">Home</a> | <a href="/log">Refresh</a> | <a href="/log/delete.php">Empty log</a></p>';
  echo '</html>';
?>
