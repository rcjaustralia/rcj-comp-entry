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

  function GetValuesFromPK($con, $uid, &$div_name, &$uid_comp_name, &$disp_order)
  {
    $sql = $con->prepare('select div_name, uid_comp_name, div_name, disp_order from comp_division where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row            = $sql->fetch(PDO::FETCH_ASSOC);
    $div_name       =  $row['div_name'] ;
    $uid_comp_name  =  $row['uid_comp_name'] ;
    $disp_order     =  $row['disp_order'] ;
  }  

  function Validate($con, $uid, 
    $div_name,      &$message_div_name,
    $uid_comp_name, &$message_comp_name,
    $disp_order,    &$message_disp_order)
  {
    $message_div_name  = '';
    $message_comp_name  = '';
    $message_disp_order = '';
    CECheckNotNull($div_name,           $message_div_name,     'Please enter a division name.');
	  CECheckMaxStrLength($div_name, 60,  $message_div_name,     'Please enter a division name which is less than 60 characters.');
    CECheckNotNull($uid_comp_name,      $message_comp_name,    'Please select a competition.');
    CECheckNotNull($disp_order,         $message_disp_order,    'Please enter a display order.');

    if (!UniqueCheck($con, $uid, $uid_comp_name, $div_name))
    {  
      $message_div_name = 'The division name "' . 
	    $div_name . 
		  '" already exists for this competition. Please enter a unique name.'; 
    }
  
    return 
      empty($message_div_name) and 
      empty($message_comp_name) and 
      empty($message_disp_order);       
  }

  function WriteHTML($con, $Heading, $FormAction, $uid, 
    $div_name,      $message_div_name,
    $uid_comp_name, $message_comp_name,
    $disp_order,    $message_disp_order)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'comp-division-add', 'edit.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldTextAutofocus('div_name', 'Division Name', $div_name, 60, $message_div_name);
    CEWriteFormFieldDropDown(
      'uid_comp_name', 'Competition', $uid_comp_name, $con, 
      'select uid_comp_name as uid, concat(year, "-", state, " ", comp_name) as display from v_comp_name order by display',
      $message_comp_name);
    CEWriteFormFieldNumber('disp_order', 'Display Order', $disp_order, 0, 127, $message_disp_order);
    CEWriteFormEnd('/sys-admin/comp-division');
    CEWritePageEnd();    
  }

  function Save($con, $sql, $uid, $uid_comp_name, $div_name, $disp_order)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':uid_comp_name', $uid_comp_name);
    $query->bindParam(':div_name',      $div_name);
    $query->bindParam(':disp_order',    $disp_order);
    $result = $query->execute();
    header('location: /sys-admin/comp-division');
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
    $disp_order     = trim(postFieldDefault('disp_order'));
    $message_div_name  = '';
    $message_comp_name  = '';
    $message_disp_order = '';

    if ($action == CE_NEW)
    {
      WriteHTML(
        $con,
        'Add a new competition division', CE_INSERT, '', 
        $div_name, '',
        $uid_comp_name, '',
        $disp_order, '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $div_name, $uid_comp_name, $disp_order);
      WriteHTML(
        $con,
        'Edit a competition division', CE_UPDATE, $uid, 
        $div_name, '',
        $uid_comp_name, '',
        $disp_order, '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, 
                   $div_name,      $message_div_name,
                   $uid_comp_name, $message_comp_name,
                   $disp_order,    $message_disp_order))
      {
        Save($con, 'insert into comp_division (uid, uid_comp_name, div_name, disp_order) values (:uid, :uid_comp_name, :div_name, :disp_order)',
             $uid, $uid_comp_name, $div_name, $disp_order);
      }
      else
      {
        WriteHTML(
          $con,
          'Add a new competition division', CE_INSERT, $uid, 
          $div_name,      $message_div_name,
          $uid_comp_name, $message_comp_name,
          $disp_order,    $message_disp_order);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, 
                   $div_name,      $message_div_name,
                   $uid_comp_name, $message_comp_name,
                   $disp_order,    $message_disp_order))
      {
	    Save($con, 'update comp_division set uid_comp_name = :uid_comp_name, div_name = :div_name, disp_order = :disp_order where uid = :uid',
             $uid, $uid_comp_name, $div_name, $disp_order);
	  }
      else
      {
        WriteHTML(
          $con,
          'Edit a competition division ', CE_UPDATE, $uid, 
          $div_name, $message_div_name,
          $uid_comp_name, $message_comp_name,
          $disp_order,    $message_disp_order);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/comp-division');
  }  
?>