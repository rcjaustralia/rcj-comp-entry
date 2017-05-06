<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  try
  {
  
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
    
    $fileName = postFieldDefault('file-name');
    unlink('./history/' . $fileName);
    header("location: /sys-admin/backup");
 
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/backup');
  }

?>
