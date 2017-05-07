<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php'; 

  try
  {
  
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
    
    $fileName = postFieldDefault('file-name');
	  $filePathAndName = './history/' . $fileName;
    if (file_exists($filePathAndName)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . $fileName . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filePathAndName));
      readfile($filePathAndName);
          
    } else {
      throw new Exception('Can not find file: "' . $fileName . '"'); 
    }
 
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/backup');
  }

?>