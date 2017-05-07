<?php

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/password-utils.php';

  function GetValuesFromPK($con, $uid, &$user_name, &$email)
  {
    $sql = $con->prepare('select first_name, last_name, email from user where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row        = $sql->fetch(PDO::FETCH_ASSOC);
    $user_name  = $row['first_name'] . ' ' . $row['last_name'] ;
    $email      = $row['email'];
  }  

  function WriteHTML($con, $uid, 
    $new_password1, $new_password1_message, $new_password2, $new_password2_message,
    $email_password)
  {
    GetValuesFromPK($con, $uid, $user_name, $email);
    CEWritePageHeader(C_SITE_TITLE, 'Change the password for ' . $user_name);
    CEWriteFormStart('Change the password for ' . $user_name, 'admin-reset-password', 'admin-reset-password.php');
    CEWriteFormAction(CE_UPDATE);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldPassword('new_password1',  'New Password', $new_password1, 60, $new_password1_message);
    CEWriteFormFieldPassword('new_password2',  'New Password Again', $new_password2, 60, $new_password2_message);
    CEWriteFormFieldCheckBox('email_password', 'Email Password?', $email_password);
    CEWriteFormEnd('/sys-admin/user');
    CEWritePageEnd();
  }  
  
 
  function Validate(
             $new_password1,    &$new_password1_message,          
             $new_password2,    &$new_password2_message)
  {
    $new_password1_message    = '';          
    $new_password2_message    = '';

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

    return empty($new_password1_message) and      
           empty($new_password2_message);    
  }
 
  function Save($con, $uid, $password, $email_password){
    $password_hash = sha1($password);
    $query = $con->prepare(
      'update user set password_hash = :password_hash where uid = :uid');
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':password_hash', $password_hash);
    $result = $query->execute();  

    GetValuesFromPK($con, $uid, $user_name, $email);
    if ($email_password){
        EmailPassword($email, $password);
    }        
  }

  function WriteHTMLDone($con, $uid, $email_password){
    GetValuesFromPK($con, $uid, $user_name, $email);
    CEWritePageHeader(C_SITE_TITLE, 'Password changed for ' . $user_name);
    echo '<p>Password changed for ' . $user_name . '</p>';
    if ($email_password){
      echo '<p>New password emailed to ' . $email . '</p>';
    }    
    CEWritePageEnd('/sys-admin/user');
  }
  
  try
  {
    
    if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
        exit(); //==>>
    }

    $uid    = postFieldDefault('uid');
    $action = postFieldDefault('action');
    
    if (empty($action))
    {
      $new_password = RandomPassword();
      WriteHTML($con, $uid, $new_password, '', $new_password, '', true);
    }
    else if ($action == CE_UPDATE)
    {
       $new_password1    = postFieldDefault('new_password1');
       $new_password2    = postFieldDefault('new_password2');
       $email_password   = postFieldDefault('email_password');

       $new_password1_message    = ''; 
       $new_password2_message    = '';
       if (Validate(
         $new_password1, $new_password1_message, 
         $new_password2, $new_password2_message))
       {
         Save($con,
              $uid,
              $new_password1,
              $email_password);
         WriteHTMLDone($con, $uid, $email_password);
       }
       else
       {
         WriteHTML($con, $uid, 
                   $new_password1, $new_password1_message, 
                   $new_password2, $new_password2_message,
                   $email_password);
       }
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/user');
  } 
 
?>