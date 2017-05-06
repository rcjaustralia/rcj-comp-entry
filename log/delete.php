<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  

  if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
      exit(); //==>>
  }

  $logFileName = $_SERVER["DOCUMENT_ROOT"] . '/log/error.log';
  if (file_exists($logFileName)){
    unlink($logFileName);
  }
  header("location: /log");
?>
