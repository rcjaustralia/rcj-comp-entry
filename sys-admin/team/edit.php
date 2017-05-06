<?php
  
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require 'sql.php';
  
  function UniqueCheck($con, $uid, $uid_comp_division, $team_name)
  {
    $sql = $con->prepare(
	  'select count(*) as count from team ' .
	  'where ' .
	  '  uid <> :uid and ' .
	  '  uid_comp_division = :uid_comp_division and  ' .
	  '  team_name = :team_name');
    $sql->bindParam(':uid',       $uid);
    $sql->bindParam(':uid_comp_division', $uid_comp_division);
    $sql->bindParam(':team_name', $team_name);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function GetValuesFromPK($con, $uid, &$uid_mentor_team, &$uid_comp_division, &$team_name)
  {
    $sql = $con->prepare('select uid_mentor_team, uid_comp_division, team_name from team where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row               = $sql->fetch(PDO::FETCH_ASSOC);
    $uid_mentor_team   = $row['uid_mentor_team'] ;
    $uid_comp_division = $row['uid_comp_division'] ;
    $team_name         = $row['team_name'] ;
  }  

  function Validate($con, $uid, 
    $uid_mentor_team,   &$message_mentor_team,
    $uid_comp_division, &$message_comp_division,
    $team_name,         &$message_team_name)
  {
    $message_mentor_team    = '';
    $message_comp_division  = '';
    $message_team_name      = '';
    CECheckNotNull($uid_mentor_team,   $message_mentor_team,    'Please select a mentor-team.');
    CECheckNotNull($uid_comp_division, $message_comp_division, 'Please select a competition division.');
    CECheckNotNull($team_name,         $message_team_name,     'Please enter a team name.');

    if (!UniqueCheck($con, $uid, $uid_comp_division, $team_name))
    {  
      $message_team_name = 'The team name already exists for this for this competition. Please enter a unique name.'; 
    }
    
    return 
      empty($message_mentor_team) and 
      empty($message_comp_division) and 
      empty($message_team_name); 
   
  }
  
  function WriteHTML($con, $Heading, $FormAction, $uid, 
    $uid_mentor_team,   $message_mentor_team,
    $uid_comp_division, $message_comp_division,
    $team_name,         $message_team_name)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'team', 'edit.php');
    CEWriteLinkToJQueryLibrary();
    CEWriteSelectRefreshEvent('OnChangeMentorTeam', 'uid_mentor_team', 'team', '/team/refresh.php', 'uid_comp_division');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    
	CEWriteFormFieldDropDown(
      'uid_mentor_team', 'Mentor-Team', $uid_mentor_team, $con, 
      'select uid, concat(year, " - ", state, " - ", comp_name, " (", first_name, " ", last_name, " - ", organisation, ")") as display from v_mentor_team order by display',
      $message_mentor_team, 'OnChangeMentorTeam()');
    
	CEWriteFormFieldDropDown(
      'uid_comp_division', 'Competition Division', $uid_comp_division, $con, 
      CompSivisionSQL($uid_mentor_team),
      $message_comp_division);
	  
    CEWriteFormFieldTextAutofocus('team_name', 'Team Name', $team_name, 60, $message_team_name);
    CEWriteFormEnd('/sys-admin/team');
    CEWritePageEnd();
  }

  function Save($con, $sql, $uid, $uid_mentor_team, $uid_comp_division, $team_name)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',               $uid);
    $query->bindParam(':uid_mentor_team',   $uid_mentor_team);
    $query->bindParam(':uid_comp_division', $uid_comp_division);
    $query->bindParam(':team_name',         $team_name);
    $result = $query->execute();
    header('location: /sys-admin/team');
  }

 try
 {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
    
    $action                = postFieldDefault('action');
    $uid                   = postFieldDefault('uid');
    $uid_mentor_team       = postFieldDefault('uid_mentor_team');
    $uid_comp_division     = postFieldDefault('uid_comp_division');
    $team_name             = postFieldDefault('team_name');
    $message_mentor_team   = '';
    $message_comp_division = '';
    $message_team_name     = ''; 

    if ($action == CE_NEW)
    {
      WriteHTML(
        $con,
        'Add a new team', CE_INSERT, '', 
        $uid_mentor_team, '',
        $uid_comp_division, '',
        $team_name, '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $uid_mentor_team, $uid_comp_division, $team_name);
      WriteHTML(
        $con,
        'Edit a team', CE_UPDATE, $uid, 
        $uid_mentor_team, '',
        $uid_comp_division, '',
        $team_name, '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, 
        $uid_mentor_team,   $message_mentor_team,
        $uid_comp_division, $message_comp_division,
        $team_name,         $message_team_name))
      {
        // write sql
		Save($con, 'insert into team (uid, uid_mentor_team, uid_comp_division, team_name) values (:uid, :uid_mentor_team, :uid_comp_division, :team_name)',
             $uid, $uid_mentor_team, $uid_comp_division, $team_name);
      }
      else
      {
        WriteHTML(
          $con,
          'Add a new team', CE_INSERT, $uid, 
        $uid_mentor_team,   $message_mentor_team,
        $uid_comp_division, $message_comp_division,
        $team_name,         $message_team_name);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, 
        $uid_mentor_team,   $message_mentor_team,
        $uid_comp_division, $message_comp_division,
        $team_name,         $message_team_name))
      {
        // write sql
		Save($con, 'update team set uid_mentor_team = :uid_mentor_team, uid_comp_division = :uid_comp_division, team_name = :team_name where uid = :uid',
             $uid, $uid_mentor_team, $uid_comp_division, $team_name);
      }
      else
      {
        WriteHTML(
          $con,
          'Edit a team ', CE_UPDATE, $uid, 
          $uid_mentor_team,   $message_mentor_team,
          $uid_comp_division, $message_comp_division,
          $team_name,         $message_team_name);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/team');
  }  
?>