<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function writeRestoreDoneHTML($con, $fileName){

	WriteConnectUserDetails($con); 
    CEWritePageHeader(C_SITE_TITLE, 'Backup and restore database');	
	echo '    <p class="indent">Restore from "' . $fileName . '" done.</p>
		      <p><a href="/sys-admin/backup">Back<a> | <a href="/">Home<a>
            </body>
          </html>';		   
  }
  
  try
  {
  
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
    
    $upload   = postFieldDefault('upload');
	
	if (!empty($upload)){
      $fileName = basename($_FILES["file-name"]["name"]);
	  $filePathAndName = './history/' . $fileName;
	  move_uploaded_file($_FILES["file-name"]["tmp_name"], $filePathAndName);	
	}else{
      $fileName = postFieldDefault('file-name');
	  $filePathAndName = './history/' . $fileName;
	}
	
    if (file_exists($filePathAndName)) {
	
	  $cmd = 
  	  C_MYSQL . 
	    ' -h ' . C_DB_HOST . 
	    ' -u '. C_DB_USER_NAME . 
  	    ' -p' . C_DB_PASSWORD .  
	    ' ' . C_DB_NAME . 
	    ' < ' . $filePathAndName; 
 	
      exec($cmd);
     
	  writeRestoreDoneHTML($con, $fileName);
	  
    } else {
      throw new Exception('Can not find file: "' . $filePathAndName . '"'); 
    }
 
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/backup');
  }
  
?>