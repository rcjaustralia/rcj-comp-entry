<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
   
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
    exit(); //==>>
  }
	
  try
  {
  	
   
  if (!file_exists(C_MYSQLDUMP)){
	 	throw new Exception('Can not find MySQLDump: "' . C_MYSQLDUMP . '"');
	}

  date_default_timezone_set('Australia/Melbourne');
    $backup_folder_name = $_SERVER["DOCUMENT_ROOT"] . '/sys-admin/backup/history';
    $backup_file_name   = 'db-backup-' . date("Ymd-His") . '.sql';
	  $redirectTo = getFieldDefault('redirectTo');
    $uidReturnTo = getFieldDefault('uid_return_to');
    if (empty($redirectTo)){
      $redirectTo = '/sys-admin/backup';     
    } else {
      $redirectTo = 
        $redirectTo . 
        '&backupDownloadFile=' . $backup_folder_name . '/' . $backup_file_name .
        '&backupDownloadText=' .$backup_file_name . 
        '&uid_return_to=' . $uidReturnTo;        
    }
    
	if (!file_exists($backup_folder_name)){
		mkdir($backup_folder_name);
	}
	
	$cmd = 
	  C_MYSQLDUMP . 
	  ' --opt --user='. C_DB_USER_NAME . 
	  ' --host=' . C_DB_HOST . 
	  ' --password="' . C_DB_PASSWORD . '" ' . 
	  C_DB_NAME . 
	  ' > ' . $backup_folder_name . '/' . $backup_file_name;
	      
	exec($cmd, $output, $returnVar);
  
	header("location: " . $redirectTo);
	
  }
  catch (Exception $e)
  {
    CEHandleException($e, $redirectTo);
  }
  
?>