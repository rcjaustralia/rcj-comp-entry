<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  

  if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
      exit(); //==>>
  }

  $fileName = postFieldDefault('fileName');
  if (!empty($fileName) and file_exists($fileName)){
    unlink($fileName);
  }
  header("location: /sys-admin/log");
?>
