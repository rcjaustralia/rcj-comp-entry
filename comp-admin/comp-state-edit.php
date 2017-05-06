<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function UniqueCheck($con, $uid, $comp_state)
  {
    $sql = $con->prepare('select count(*) as count from comp_state where uid <> :uid and state = :state');
    $sql->bindParam(':uid', $uid);
    $sql->bindParam(':state', $comp_state);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function GetValuesFromPK($con, $uid, 
    &$comp_state, &$uid_treasurer, &$account_name, &$account_bsb, &$account_number)
  {
    $sql = $con->prepare(
      'select state, uid_treasurer, account_name, account_bsb, 
       account_number from comp_state where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $comp_state     =  $row['state'] ;
    $uid_treasurer  =  $row['uid_treasurer'] ;
    $account_name   =  $row['account_name'] ;
    $account_bsb    =  $row['account_bsb'] ;
    $account_number =  $row['account_number'] ;
  }  

  function validateAccount(
    $account_name, $account_bsb, $account_number, &$account_name_message){
    $hasData = 
      (!empty($account_name) or
       !empty($account_bsb) or
       !empty($account_number));
    if (!$hasData){
      return true;
    }  else {
      if (empty($account_name) or
          empty($account_bsb) or
          empty($account_number)){
        $account_name_message = 'Looks like the account details are not complete.';
        return false;        
      } else {
        return true;
      }
    }
  }
  
  function Validate($con, $uid, 
    $comp_state,     &$message,
    $uid_treasurer,  &$uid_treasurer_message, 
    $account_name,   &$account_name_message, 
    $account_bsb,    &$account_bsb_message, 
    $account_number, &$account_number_message)
  {
    $message = '';
    $uid_treasurer_message = '';
    $account_name_message = '';
    $account_bsb_message = '';
    $account_number_message = '';

    if (strlen($comp_state) <> 3)
    {  
      $message = 'Please enter a three character state in the form "XXX".'; 
    } 

    if (!UniqueCheck($con, $uid, $comp_state))
    {  
      $message = 'The state "' . $comp_state . '" already exists. Please enter a unique state.'; 
    }
    
    if (empty($uid_treasurer))
    {  
      $uid_treasurer_message = 'Please select a treasurer.'; 
    } 

    if (strlen($account_name) > 100)
    {  
      $account_name_message = 'The account name must be shorter than 100 characters.'; 
    } 

    if (strlen($account_bsb) > 6)
    {  
      $account_bsb_message = 'The account BSB must be shorter than 6 characters.'; 
    } 

    if (strlen($account_number) > 9)
    {  
      $account_number_message = 'The account number must be shorter than 9 characters.'; 
    } 

    validateAccount($account_name, $account_bsb, $account_number, $account_name_message);
    
    return 
      empty($message) and 
      empty($uid_treasurer_message) and
      empty($account_name_message) and
      empty($account_bsb_message) and
      empty($account_number_message);
  
  }

  function WriteHTML($con, $Heading, $FormAction, $uid, $return_to, 
    $comp_state, $message,
    $uid_treasurer, $uid_treasurer_message, 
    $account_name, $account_name_message, 
    $account_bsb, $account_bsb_message, 
    $account_number, $account_number_message)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'comp-state-add', 'comp-state-edit.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldHidden('return_to', $return_to);
    echo '<span class="field_label">Required information</span>';
    CEWriteFormFieldTextAutofocus('comp_state', 'Competition state', $comp_state, 3, $message);
    echo '<hr>';
    echo '<span class="field_label">Optional information</span>';
    CEWriteFormFieldDropDown('uid_treasurer', 'Treasurer', $uid_treasurer, $con, 
      'select 
        uid, concat(first_name, " ", last_name, " (", primary_org, ")") as display 
      from 
        user
      where 
        access_level in ("COMP_ADMIN", "SYS_ADMIN", "SYS_DEV")      
      order by 
        first_name, last_name', 
      $uid_treasurer_message);
    CEWriteFormFieldText('account_name',   'Bank account name',   $account_name, 100, $account_name_message);
    CEWriteFormFieldText('account_bsb',    'Bank account BSB',    $account_bsb,    6, $account_bsb_message);
    CEWriteFormFieldText('account_number', 'Bank account number', $account_number, 9, $account_number_message);    
    CEWriteFormEnd($return_to);
    CEWritePageEnd();
  }

  function Save($con, $sql, 
    $uid, $return_to, $comp_state, 
    $uid_treasurer, $account_name, $account_bsb, $account_number){
    $query = $con->prepare($sql);
    $query->bindParam(':uid',            $uid);
    $query->bindParam(':comp_state',     $comp_state);
    $query->bindParam(':uid_treasurer',  $uid_treasurer);
    $query->bindParam(':account_name',   $account_name);
    $query->bindParam(':account_bsb',    $account_bsb);
    $query->bindParam(':account_number', $account_number);
    $result = $query->execute();
    header('location: ' . $return_to);
  }

  
 try
 {
    if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
        exit(); //==>>
    }

    $action = postFieldDefault('action');
    $uid                    = postFieldDefault('uid');
    $return_to              = postFieldDefault('return_to'); 
    $comp_state             = trim(postFieldDefault('comp_state'));
    $uid_treasurer          = postFieldDefault('uid_treasurer');
    $account_name           = postFieldDefault('account_name');
    $account_bsb            = postFieldDefault('account_bsb');
    $account_number         = postFieldDefault('account_number');
    $uid_treasurer_message  = '';
    $account_name_message   = '';
    $account_bsb_message    = '';
    $account_number_message = '';
    
    if ($action == CE_NEW)
    {
      WriteHTML($con,
        'Add a new competition state', CE_INSERT, '', $return_to, 
        $comp_state,     '',
        $uid_treasurer,  '', 
        $account_name,   '', 
        $account_bsb,    '', 
        $account_number, '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $comp_state,
        $uid_treasurer, $account_name, $account_bsb, $account_number);
      WriteHTML($con,
        'Add a new competition state', CE_UPDATE, $uid, $return_to, 
        $comp_state, '',
        $uid_treasurer, '', 
        $account_name, '', 
        $account_bsb, '', 
        $account_number, '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, 
        $comp_state, $message,
        $uid_treasurer, $uid_treasurer_message, 
        $account_name, $account_name_message, 
        $account_bsb, $account_bsb_message, 
        $account_number, $account_number_message))
      {
        Save($con, 'insert into comp_state (uid, state, uid_treasurer, account_name, account_bsb, account_number) ' .
                   'values (:uid, :comp_state, :uid_treasurer, :account_name, :account_bsb, :account_number)',
             $uid, $return_to, $comp_state, 
             $uid_treasurer, $account_name, $account_bsb, $account_number);
      }
      else
      {
        WriteHTML($con,
          'Add a new competition state', CE_INSERT, $uid, $return_to, 
          $comp_state,     $message,
          $uid_treasurer,  $uid_treasurer_message, 
          $account_name,   $account_name_message, 
          $account_bsb,    $account_bsb_message, 
          $account_number, $account_number_message);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, 
        $comp_state, $message,
        $uid_treasurer,  $uid_treasurer_message, 
        $account_name,   $account_name_message, 
        $account_bsb,    $account_bsb_message, 
        $account_number, $account_number_message))
      {
        Save($con, 
             'update comp_state set 
                state          = :comp_state,
                uid_treasurer  = :uid_treasurer, 
                account_name   = :account_name, 
                account_bsb    = :account_bsb, 
                account_number = :account_number
              where uid = :uid',
             $uid, $return_to, $comp_state, $uid_treasurer, $account_name, $account_bsb, $account_number);
      }
      else
      {
        WriteHTML($con,
          'Add a new competition state', CE_UPDATE, $uid, $return_to, 
          $comp_state,     $message,
          $uid_treasurer,  $uid_treasurer_message, 
          $account_name,   $account_name_message, 
          $account_bsb,    $account_bsb_message, 
          $account_number, $account_number_message);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, $return_to);
  }  
?>