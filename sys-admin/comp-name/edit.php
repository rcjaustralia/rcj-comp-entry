<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function UniqueCheck($con, $uid, $uid_year, $uid_state, $comp_name)
  {
    $sql = $con->prepare(
	  'select count(*) as count from comp_name ' .
	  'where ' .
	  '  uid <> :uid and ' .
	  '  uid_year = :uid_year and ' .
	  '  uid_state = :uid_state and  ' .
	  '  comp_name = :comp_name');
    $sql->bindParam(':uid',       $uid);
    $sql->bindParam(':uid_year',  $uid_year);
    $sql->bindParam(':uid_state', $uid_state);
    $sql->bindParam(':comp_name', $comp_name);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function GetValuesFromPK($con, $uid, &$comp_name, &$uid_comp_year, &$uid_comp_state, &$start_date, &$end_date)
  {
    $sql = $con->prepare('select comp_name, uid_year, uid_state, start_date, end_date from comp_name where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row            = $sql->fetch(PDO::FETCH_ASSOC);
    $comp_name      =  $row['comp_name'] ;
    $uid_comp_year  =  $row['uid_year'] ;
    $uid_comp_state =  $row['uid_state'] ;
    $start_date     =  $row['start_date'] ;
    $end_date       =  $row['end_date'] ;
  }  

  function Validate($con, $uid, 
    $comp_name,      &$message_comp_name,
    $uid_comp_year,  &$message_comp_year,
    $uid_comp_state, &$message_comp_state,
    $start_date,     &$message_start_date,
    $end_date,       &$message_end_date)
  {
    $message_comp_name  = '';
    $message_comp_year  = '';
    $message_comp_state = '';
    CECheckNotNull($comp_name,          $message_comp_name,     'Please enter a competition name.');
	CECheckMaxStrLength($comp_name, 60, $message_comp_name,     'Please enter a competition name which is less than 60 characters.');
    CECheckNotNull($uid_comp_year,      $message_comp_year,     'Please select a competition year.');
    CECheckNotNull($uid_comp_state,     $message_comp_state,    'Please select a competition state.');

    if (!UniqueCheck($con, $uid, $uid_comp_year, $uid_comp_state, $comp_name))
    {  
      $message_comp_name = 'The competition name "' . 
	    $comp_name . 
		'" already exists for this year & state combination. Please enter a unique name.'; 
    }
    
	// ToDo: Need some validation for start_date & end_date
	
    return 
      empty($message_comp_name) and 
      empty($message_comp_year) and 
      empty($message_comp_state) and 
	  empty($message_start_date) and 
	  empty($message_end_date); 
   
  }

  function WriteHTML($con, $Heading, $FormAction, $uid, 
    $comp_name,      $message_comp_name,
    $uid_comp_year,  $message_comp_year,
    $uid_comp_state, $message_comp_state,
    $start_date,     $message_start_date,
    $end_date,       $message_end_date)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'comp-name-add', 'edit.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldTextAutofocus('comp_name', 'Competition Name', $comp_name, 60, $message_comp_name);
    CEWriteFormFieldDropDown(
      'uid_comp_year', 'Competition Year', $uid_comp_year, $con, 
      'select uid, year as display from comp_year order by display',
      $message_comp_year);
    CEWriteFormFieldDropDown(
      'uid_comp_state', 'Competition State', $uid_comp_state, $con, 
      'select uid, state as display from comp_state order by display',
      $message_comp_state);
    CEWriteFormFieldDate('start_date', 'Entries Open Date', $start_date, $message_start_date);
    CEWriteFormFieldDate('end_date', 'Entries Close Date', $end_date, $message_end_date);
    CEWriteFormEnd('/sys-admin/comp-name');
    CEWritePageEnd();
  }

  function Save($con, $sql, $uid, $comp_name, $uid_comp_year, $uid_comp_state, $start_date, $end_date)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',        $uid);
    $query->bindParam(':comp_name',  $comp_name);
    $query->bindParam(':uid_year',   $uid_comp_year);
    $query->bindParam(':uid_state',  $uid_comp_state);
    $query->bindParam(':start_date', $start_date);
    $query->bindParam(':end_date',   $end_date);
    $result = $query->execute();
    header('location: /sys-admin/comp-name');
  }

  
 try
 {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
  
    date_default_timezone_set('Australia/Victoria');
    
    $action         = postFieldDefault('action');
    $uid            = postFieldDefault('uid');
    $comp_name      = trim(postFieldDefault('comp_name'));
    $uid_comp_year  = trim(postFieldDefault('uid_comp_year'));
    $uid_comp_state = trim(postFieldDefault('uid_comp_state'));
    $start_date     = trim(postFieldDefault('start_date'));
    $end_date       = trim(postFieldDefault('end_date'));
    $message_comp_name  = '';
    $message_comp_year  = '';
    $message_comp_state = ''; 
    $message_start_date = ''; 
    $message_end_date   = ''; 

    if ($action == CE_NEW)
    {
      WriteHTML(
        $con,
        'Add a new competition', CE_INSERT, '', 
        $comp_name, '',
        $uid_comp_year, '',
        $uid_comp_state, '',
		$start_date, '',
		$end_date, '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $comp_name, $uid_comp_year, $uid_comp_state, $start_date, $end_date);
      WriteHTML(
        $con,
        'Edit a competition', CE_UPDATE, $uid, 
        $comp_name, '',
        $uid_comp_year, '',
        $uid_comp_state, '',
		$start_date, '',
		$end_date, '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, 
            $comp_name,      $message_comp_name,
            $uid_comp_year,  $message_comp_year,
            $uid_comp_state, $message_comp_state,
            $start_date,     $message_start_date,
            $end_date,       $message_end_date))
      {
        Save($con, 'insert into comp_name (uid, uid_year, uid_state, comp_name, start_date, end_date) values (:uid, :uid_year, :uid_state, :comp_name, :start_date, :end_date)',
             $uid, $comp_name, $uid_comp_year, $uid_comp_state, $start_date, $end_date);
      }
      else
      {
        WriteHTML(
          $con,
          'Add a new competition', CE_INSERT, $uid, 
           $comp_name,      $message_comp_name,
           $uid_comp_year,  $message_comp_year,
           $uid_comp_state, $message_comp_state,
           $start_date,     $message_start_date,
           $end_date,       $message_end_date);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, 
            $comp_name,      $message_comp_name,
            $uid_comp_year,  $message_comp_year,
            $uid_comp_state, $message_comp_state,
            $start_date,     $message_start_date,
            $end_date,       $message_end_date))
      {
        Save($con, 'update comp_name set comp_name = :comp_name, uid_year = :uid_year, uid_state = :uid_state, start_date = :start_date, end_date = :end_date where uid = :uid',
             $uid, $comp_name, $uid_comp_year, $uid_comp_state, $start_date, $end_date);
      }
      else
      {
        WriteHTML(
          $con,
          'Edit a competition ', CE_UPDATE, $uid, 
          $comp_name,      $message_comp_name,
          $uid_comp_year,  $message_comp_year,
          $uid_comp_state, $message_comp_state,
          $start_date,     $message_start_date,
          $end_date,       $message_end_date);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/comp-name');
  }  
?>