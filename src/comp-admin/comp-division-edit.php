<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function UniqueCheck($con, $uid, $uid_comp_name, $div_name)
  {
    $sql = $con->prepare(
	  'select count(*) as count from comp_division ' .
	  'where ' .
	  '  uid <> :uid and ' .
	  '  uid_comp_name = :uid_comp_name and  ' .
	  '  div_name = :div_name');
    $sql->bindParam(':uid',       $uid);
    $sql->bindParam(':uid_comp_name', $uid_comp_name);
    $sql->bindParam(':div_name', $div_name);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function GetValuesFromPK($con, $uid, &$div_name, &$uid_comp_name)
  {
    $sql = $con->prepare('select div_name, uid_comp_name, div_name from comp_division where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row            = $sql->fetch(PDO::FETCH_ASSOC);
    $div_name       =  $row['div_name'] ;
    $uid_comp_name  =  $row['uid_comp_name'] ;
  }  

  function Validate($con, $uid, 
    $div_name,      &$message_div_name,
    $uid_comp_name)
  {
    $message_div_name  = '';
    CECheckNotNull($div_name,           $message_div_name,     'Please enter a division name.');
	  CECheckMaxStrLength($div_name, 60,  $message_div_name,     'Please enter a division name which is less than 60 characters.');

    if (!UniqueCheck($con, $uid, $uid_comp_name, $div_name))
    {  
      $message_div_name = 'The division name "' . 
	    $div_name . 
		  '" already exists for this competition. Please enter a unique name.'; 
    }
  
    return 
      empty($message_div_name);    
  }

  function WriteHTML($con, $Heading, $FormAction, $uid, $uidReturnTo, 
    $div_name,      $message_div_name,
    $uid_comp_name)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'comp-division-add', '/comp-admin/comp-division-edit.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldHidden('uid_return_to', $uidReturnTo);
    CEWriteFormFieldHidden('uid_comp_name', $uid_comp_name);
    CEWriteFormFieldTextAutofocus('div_name', 'Division Name', $div_name, 60, $message_div_name);
    CEWriteFormEnd('/comp-admin#' . $uidReturnTo);
    CEWritePageEnd();    
  }

  function Save($con, $sql, $uid, $uidReturnTo, $uid_comp_name, $div_name)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':uid_comp_name', $uid_comp_name);
    $query->bindParam(':div_name',      $div_name);
    $result = $query->execute();
    header('location: /comp-admin#' . $uidReturnTo);
  }

  
 try
 {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }

    $action         = postFieldDefault('action');
    $uid            = postFieldDefault('uid');
    $div_name       = trim(postFieldDefault('div_name'));
    $uid_comp_name  = trim(postFieldDefault('uid_comp_name'));
    $uidReturnTo    = trim(postFieldDefault('uid_return_to'));
    $message_div_name  = '';

    if ($action == CE_NEW)
    {
      WriteHTML(
        $con,
        'Add a new competition division', CE_INSERT, '', $uidReturnTo,
        $div_name, '',
        $uid_comp_name, '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $div_name, $uid_comp_name);
      WriteHTML(
        $con,
        'Edit a competition division', CE_UPDATE, $uid, $uidReturnTo, 
        $div_name, '',
        $uid_comp_name, '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, 
                   $div_name,      $message_div_name,
                   $uid_comp_name))
      {
        $sqlText = 
          'insert into comp_division (uid, uid_comp_name, disp_order, div_name) ' .
          'select :uid, :uid_comp_name, ifnull(max(disp_order), 0)+1, :div_name ' .
          'from comp_division where uid_comp_name = :uid_comp_name';
        Save($con, $sqlText,
             $uid, $uidReturnTo, $uid_comp_name, $div_name);
      }
      else
      {
        WriteHTML(
          $con,
          'Add a new competition division', CE_INSERT, $uid, $uidReturnTo, 
          $div_name,      $message_div_name,
          $uid_comp_name);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, 
                   $div_name,      $message_div_name,
                   $uid_comp_name))
      {
	    Save($con, 'update comp_division set uid_comp_name = :uid_comp_name, div_name = :div_name where uid = :uid',
             $uid, $uidReturnTo, $uid_comp_name, $div_name);
	  }
      else
      {
        WriteHTML(
          $con,
          'Edit a competition division ', CE_UPDATE, $uid, $uidReturnTo, 
          $div_name, $message_div_name,
          $uid_comp_name);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-admin#' . $uid_comp_name);
  }  
?>