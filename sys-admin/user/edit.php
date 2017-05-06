<?php

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-bom.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-shared.php';

  function ValidateInsert($con, $user)
  {
    $password_message = ''; 
    $password_again_message = '';     
    $validateUserUpdate = validateUserUpdateSysAdmin(
      $con, $user);
    
    CECheckNotNull($user->password_again, $user->password_again_message, 'Please re-enter the password.');
    if (empty($user->password_message) and (strlen($user->password) < 6))
    {
      $user->password_message = 'Please enter a password of 6 - 20 characters.'; 
    }
    else
    {
      if ($user->password <> $user->password_again)
      {
        $user->password_again_message = 'Passwords did not match.';
      }
    }
    
    return 
      $validateUserUpdate and 
      empty($user->password_message) and 
      empty($user->password_again_message);

  }

  function WriteHTMLStart($con,
    $heading, $action, $user)
  {
    CEWritePageHeader(C_SITE_TITLE, $heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($heading, 'user-add', 'edit.php');
    CEWriteFormAction($action);
    CEWriteFormFieldHidden('uid', $user->uid);
    echo '<fieldset><legend>Required information</legend>';
    CEWriteFormFieldTextAutofocus('email', 'Email Address', $user->email, 245, $user->email_message);        
  }
  
  function WriteHTMLEnd($user)
  {
    CEWriteFormFieldText('first_name', 'First name', $user->first_name, 60, $user->first_name_message);
    CEWriteFormFieldText('last_name', 'Last name', $user->last_name, 60, $user->last_name_message);
    CEWriteFormFieldText('primary_org', 'Organisation', $user->primary_org, 60, $user->primary_org_message);
    CEWriteFormFieldDropDownHardCoded('access_level', 'Access level', $user->access_level, 
      array('MENTOR' => 'Mentor', 
            'COMP_ADMIN' => 'Competition admin', 
            'SYS_ADMIN' => 'System admin'), $user->access_level_message);
    echo '</fieldset></p>';
    ceWriteSaveAndCancelButtons('/sys-admin/user'); 
    echo '</br>';
    writeHTMLRCJAMembership($user);
    ceWriteSaveAndCancelButtons('/sys-admin/user'); 
    echo '</br><fieldset><legend>Optional information</legend>';
    writeAddressHTML($user);
    echo '</fieldset><p>';
    
    CEWriteFormEnd('/sys-admin/user');
    CEWritePageEnd();
  } 
  
  function WriteHTMLInsert($con, $user)
  {
    WriteHTMLStart($con,
      'Add a new user of the Robocup registration system', CE_INSERT, $user);
      CEWriteFormFieldPassword('password', 'Password', $user->password, 60, $user->password_message);
      CEWriteFormFieldPassword('password_again', 'Password Again', $user->password_again, 60, $user->password_again_message);
    
    WriteHTMLEnd($user); 
  }

  function WriteHTMLUpdate($con, $user)
    
  {
    WriteHTMLStart($con,
      'Edit a user of the Robocup registration system', CE_UPDATE, $user);
    WriteHTMLEnd($user);
      
  }

  function SaveInsert($con, $user)
  {

    $query = $con->prepare(
      'insert into user 
        (uid, email, password_hash, first_name, last_name, primary_org, access_level,
         adrs_line_1, adrs_line_2, suburb, postcode, state, rcja_member, mailing_list, share_with_sponsor) 
       values 
        (:uid, :email, :password_hash, :first_name, :last_name, :primary_org, :access_level,
         :adrs_line_1, :adrs_line_2, :suburb, :postcode, :state, :rcja_member, :mailing_list, :share_with_sponsor)'
        );
        
    $passwordHash =  sha1($user->password);   
    $query->bindParam(':uid',                $user->uid);
    $query->bindParam(':email',              $user->email);
    $query->bindParam(':password_hash',      $passwordHash);
    $query->bindParam(':first_name',         $user->first_name);
    $query->bindParam(':last_name',          $user->last_name);
    $query->bindParam(':primary_org',        $user->primary_org);
    $query->bindParam(':access_level',       $user->access_level);
    $query->bindParam(':adrs_line_1',        $user->adrs_line_1);
    $query->bindParam(':adrs_line_2',        $user->adrs_line_2);
    $query->bindParam(':suburb',             $user->suburb);
    $query->bindParam(':postcode',           $user->postcode);
    $query->bindParam(':state',              $user->state);
    $query->bindParam(':rcja_member',        ceBoolToInt($user->rcja_member));
    $query->bindParam(':mailing_list',       ceBoolToInt($user->mailing_list));
    $query->bindParam(':share_with_sponsor', ceBoolToInt($user->share_with_sponsor));
    $result = $query->execute();
    
    header('location: /sys-admin/user');
  }

  function SaveUpdate($con, $user){
    $query = $con->prepare(
      'update user ' .
      'set email              = :email,' .
      '    first_name         = :first_name,' . 
      '    last_name          = :last_name,' . 
      '    primary_org        = :primary_org,' . 
      '    access_level       = :access_level, ' .           
      '    adrs_line_1        = :adrs_line_1, ' .           
      '    adrs_line_2        = :adrs_line_2, ' .           
      '    suburb             = :suburb, ' .           
      '    postcode           = :postcode, ' .           
      '    state              = :state, ' .           
      '    rcja_member        = :rcja_member, ' .           
      '    mailing_list       = :mailing_list, ' .           
      '    share_with_sponsor = :share_with_sponsor ' .           
    'where uid = :uid');
    $query->bindParam(':uid',                $user->uid);
    $query->bindParam(':email',              $user->email);
    $query->bindParam(':first_name',         $user->first_name);
    $query->bindParam(':last_name',          $user->last_name);
    $query->bindParam(':primary_org',        $user->primary_org);
    $query->bindParam(':access_level',       $user->access_level);
    $query->bindParam(':adrs_line_1',        $user->adrs_line_1);
    $query->bindParam(':adrs_line_2',        $user->adrs_line_2);
    $query->bindParam(':suburb',             $user->suburb);
    $query->bindParam(':postcode',           $user->postcode);
    $query->bindParam(':state',              $user->state);
    $query->bindParam(':rcja_member',        ceBoolToInt($user->rcja_member));
    $query->bindParam(':mailing_list',       ceBoolToInt($user->mailing_list));
    $query->bindParam(':share_with_sponsor', ceBoolToInt($user->share_with_sponsor));
    $result = $query->execute();
    header('location: /sys-admin/user');
  }

 try
 {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }

    $action                   = postFieldDefault('action');
    $user                     = new rcjaUser(); 
    $user->uid                = postFieldDefault('uid');
    $user->email              = trim(postFieldDefault('email'));
    $user->password           = trim(postFieldDefault('password'));
    $user->password_again     = trim(postFieldDefault('password_again'));
    $user->first_name         = trim(postFieldDefault('first_name'));
    $user->last_name          = trim(postFieldDefault('last_name'));
    $user->primary_org        = trim(postFieldDefault('primary_org'));
    $user->access_level       = trim(postFieldDefault('access_level'));
    $user->adrs_line_1        = trim(postFieldDefault('adrs_line_1'));
    $user->adrs_line_2        = trim(postFieldDefault('adrs_line_2'));
    $user->suburb             = trim(postFieldDefault('suburb'));
    $user->postcode           = trim(postFieldDefault('postcode'));
    $user->state              = trim(postFieldDefault('state'));
    $user->rcja_member        = postFieldDefault('rcja_member');
    $user->mailing_list       = postFieldDefault('mailing_list');
    $user->share_with_sponsor = postFieldDefault('share_with_sponsor');

    if ($action == CE_NEW)
    {
      WriteHTMLInsert($con, $user);
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $user);
      WriteHTMLUpdate($con, $user);
        
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($user->uid);
      if (ValidateInsert($con, $user))
      {
        SaveInsert($con, $user);
      }
      else
      {
        WriteHTMLInsert($con, $user);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (validateUserUpdateSysAdmin($con, $user))
      {
        SaveUpdate($con, $user);          
      }
      else
      {
       WriteHTMLUpdate($con, $user);
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