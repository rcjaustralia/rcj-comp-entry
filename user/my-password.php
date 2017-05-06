<?php

    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function WriteHTML($uid, $current_password, $current_password_message, 
    $new_password1, $new_password1_message, $new_password2, $new_password2_message)
  {
    CEWritePageHeader(C_SITE_TITLE, 'Change your password');
    CEWriteFormStart('Change your password', 'my-password', 'my-password.php');
    CEWriteFormAction(CE_UPDATE);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldPasswordAutoFocus('current_password', 'Current Password', $current_password, 60, $current_password_message);
    CEWriteFormFieldPassword('new_password1', 'New Password', $new_password1, 60, $new_password1_message);
    CEWriteFormFieldPassword('new_password2', 'New Password Again', $new_password2, 60, $new_password2_message);

    CEWriteFormEnd('/');
    CEWritePageEnd();
  }  
  
  function ValidateUser($con, $user_id, $password)
  {
    $password_hash = sha1($password);
    $sql = $con->prepare(
	  'select count(*) as count from user ' .
	  'where ' .
	  '  uid = :uid and password_hash = :password_hash');
    $sql->bindParam(':uid', $user_id);
    $sql->bindParam(':password_hash', $password_hash);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return ($row['count'] == 1);
  }      

  function Validate($con, $uid_user,
             $current_password, &$current_password_message,          
             $new_password1,    &$new_password1_message,          
             $new_password2,    &$new_password2_message)
  {
    $current_password_message = '';
    $new_password1_message    = '';          
    $new_password2_message    = '';
    
    CECheckNotNull($current_password, $current_password_message, 'Please enter your current password.');

    if (empty($current_password_message) and !ValidateUser($con, $uid_user, $current_password)){
      $current_password_message = 'Invalid current password.';
    }

    CECheckNotNull($new_password2, $new_password2_message, 'Please re-enter the password.');
    
    if (strlen($new_password1) < 6)
    {
      $new_password1_message = 'Please enter a password of 6 - 20 characters.'; 
    }
    else
    {
      if ($new_password1 <> $new_password2)
      {
        $new_password2_message = 'Passwords did not match.';
      }
    }

    return empty($current_password_message) and   
           empty($new_password1_message) and      
           empty($new_password2_message);    
  }
 
  function Save($con, $uid, $password){
    $password_hash = sha1($password);
    $query = $con->prepare(
      'update user set password_hash = :password_hash where uid = :uid');
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':password_hash', $password_hash);
    $result = $query->execute();      
  }

  try
  {
    
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

    $uid_user = $_SESSION['uid_logged_on_user'];
    $action   = postFieldDefault('action');
    
    if (empty($action))
    {
      WriteHTML($uid_user, '', '', '', '', '', '');
    }
    else if ($action == CE_UPDATE)
    {
       $current_password = postFieldDefault('current_password');
       $new_password1    = postFieldDefault('new_password1');
       $new_password2    = postFieldDefault('new_password2');

       $current_password_message = ''; 
       $new_password1_message    = ''; 
       $new_password2_message    = '';
       if (Validate($con, $uid_user,
         $current_password, $current_password_message, 
         $new_password1, $new_password1_message, 
         $new_password2, $new_password2_message))
       {
         Save($con,
              $uid_user,
              $new_password1);
         header('location: /');
	     exit();      
       }
       else
       {
         WriteHTML($uid_user, $current_password, $current_password_message, 
                   $new_password1, $new_password1_message, 
                   $new_password2, $new_password2_message);
       }
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/');
  } 
 
?>