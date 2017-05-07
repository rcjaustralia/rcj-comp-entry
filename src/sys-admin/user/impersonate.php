<?php

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/password-utils.php';

  try
  {
    
    if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
        exit(); //==>>
    }

    $uid_user = postFieldDefault('uid');
    session_start();
	$_SESSION['uid_logged_on_user'] = $uid_user;
    header('location: /');
	exit();         

  } 
  
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/user');
  } 
 
?>