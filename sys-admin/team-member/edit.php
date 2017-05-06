<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function UniqueCheck($con, $uid, $uid_team, $first_name, $last_name)
  {
    $sql = $con->prepare(
	  'select count(*) as count from team_member ' .
	  'where ' .
	  '  uid <> :uid and ' .
	  '  uid_team = :uid_team and  ' .
	  '  first_name = :first_name and  ' .
	  '  last_name = :last_name');
    $sql->bindParam(':uid',        $uid);
    $sql->bindParam(':uid_team',   $uid_team);
    $sql->bindParam(':first_name', $first_name);
    $sql->bindParam(':last_name',  $last_name);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function GetValuesFromPK($con, $uid, &$uid_team, &$first_name, &$last_name)
  {
    $sql = $con->prepare('select uid_team, first_name, last_name from team_member where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row        = $sql->fetch(PDO::FETCH_ASSOC);
    $uid_team   = $row['uid_team'] ;
    $first_name = $row['first_name'] ;
    $last_name  = $row['last_name'] ;
	}  

  function Validate($con, $uid, 
    $uid_team,   &$message_team,
    $first_name, &$message_first_name,
    $last_name,  &$message_last_name)
  {
    $message_team       = '';
    $message_first_name = '';
    $message_last_name  = '';
    CECheckNotNull($uid_team,   $message_team,    'Please select a team.');
    CECheckNotNull($first_name, $message_first_name, 'Please enter a first name.');
    CECheckNotNull($last_name,  $message_last_name,  'Please enter a last name.');

    if (!UniqueCheck($con, $uid, $uid_team, $first_name, $last_name))
    {  
      $message_first_name = 'The team member already exists in this team. Please enter a unique name.'; 
      $message_last_name  = 'The team member already exists in this team. Please enter a unique name.'; 
    }
    
    return 
      empty($message_team) and 
      empty($message_first_name) and 
      empty($message_last_name);    
  }
  
  function WriteHTML($con, $Heading, $FormAction, $uid, 
    $uid_team,   $message_team,
    $first_name, $message_first_name,
    $last_name,  $message_last_name)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'team-member', 'edit.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    
	CEWriteFormFieldDropDown(
      'uid_team', 'Team', $uid_team, $con, 
      'select uid, concat(year, " - ", state, ", ", comp_name, ", ", organisation, ", ", div_name, " (", team_name, ")") as display from v_team order by display',
      $message_team);
    	  
    CEWriteFormFieldText('first_name', 'First Name', $first_name, 60, $message_first_name);
    CEWriteFormFieldText('last_name', 'Last  Name', $last_name, 60, $message_last_name);
    CEWriteFormEnd('/sys-admin/team-member');
    CEWritePageEnd();
	
  }

  function Save($con, $sql, $uid, $uid_team, $first_name, $last_name)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',        $uid);
    $query->bindParam(':uid_team',   $uid_team);
    $query->bindParam(':first_name', $first_name);
    $query->bindParam(':last_name',  $last_name);
    $result = $query->execute();
    header('location: /sys-admin/team-member');
  }

 try
 {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
    
    $action                = postFieldDefault('action');
    $uid                   = postFieldDefault('uid');
    $uid_team              = postFieldDefault('uid_team');
    $first_name            = postFieldDefault('first_name');
    $last_name             = postFieldDefault('last_name');
    $message_team          = '';
    $message_first_name    = '';
    $message_last_name     = '';
	
    if ($action == CE_NEW)
    {
      WriteHTML(
        $con,
        'Add a new team member', CE_INSERT, '', 
        $uid_team,   '',
        $first_name, '',
        $last_name,  '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $uid_team, $first_name, $last_name);
      WriteHTML(
        $con,
        'Edit a team member', CE_UPDATE, $uid, 
        $uid_team,   '',
        $first_name, '',
        $last_name,  '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, 
        $uid_team,   $message_team,
        $first_name, $message_first_name,
        $last_name,  $message_last_name))
      {
        // write sql
		Save($con, 'insert into team_member (uid, uid_team, first_name, last_name) values (:uid, :uid_team, :first_name, :last_name)',
             $uid, $uid_team, $first_name, $last_name);
      }
      else
      {
        WriteHTML(
          $con,
          'Add a new team', CE_INSERT, $uid, 
          $uid_team,   $message_team,
          $first_name, $message_first_name,
          $last_name,  $message_last_name);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, 
            $uid_team,   $message_team,
            $first_name, $message_first_name,
            $last_name,  $message_last_name))
      {
        // write sql
		Save($con, 'update team_member set uid_team = :uid_team, first_name = :first_name, last_name = :last_name where uid = :uid',
             $uid, $uid_team, $first_name, $last_name);
      }
      else
      {
        WriteHTML(
          $con,
          'Edit a team member ', CE_UPDATE, $uid, 
          $uid_team,   $message_team,
          $first_name, $message_first_name,
          $last_name,  $message_last_name);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/team-member');
  }  
?>