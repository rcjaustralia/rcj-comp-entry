<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function UniqueCheck($con, $uid, $uid_user, $uid_comp_name, $organisation)
  {
    // ToDo: Must include organisation in the check for unique. 
	//       was removed to make the my-entries screen easier to code
	$sql = $con->prepare(
	  'select count(*) as count from mentor_team ' .
	  'where ' .
	  '  uid <> :uid and ' .
	  '  uid_user      = :uid_user and  ' .
	  '  uid_comp_name = :uid_comp_name');
	  // and ' .
	  // '  organisation  = :organisation');
    $sql->bindParam(':uid',           $uid);
    $sql->bindParam(':uid_user',      $uid_user);
    $sql->bindParam(':uid_comp_name', $uid_comp_name);
    // $sql->bindParam(':organisation',  $organisation);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  
  
  function Validate($con, $uid, 
    $uid_user,      &$message_user,
    $uid_comp_name, &$message_comp_name,
    $organisation,  &$message_organisation)
  {
    $message_user = '';
    $message_comp_name = '';
    $message_organisation = '';
    CECheckNotNull($uid_user,      $message_user,      'Please select a user.');
    CECheckNotNull($uid_comp_name, $message_comp_name, 'Please select a competition.');
    CECheckNotNull($organisation,  $message_organisation,  'Please enter the school or club this team is representing, or "Private" if you are not representing a school or team.');

    if (!UniqueCheck($con, $uid, $uid_user, $uid_comp_name, $organisation))
    {  
      // ToDo: Update the message when including organisation in the unique check.
      // $message_organisation = 'The combination of user + competition + organisation must be unique.'; 
      $message_organisation = 'The combination of user + competition must be unique.'; 
    }
  
    return 
      empty($message_user) and 
      empty($message_comp_name) and 
      empty($message_organisation);    
  }

  function GetValuesFromPK($con, $uid, &$uid_user, &$uid_comp_name, &$organisation){
	  $sql = $con->prepare('select uid_user, uid_comp_name, organisation from mentor_team where uid = :uid');
      $sql->bindParam(':uid', $uid);
      $sql->execute();
      $row            = $sql->fetch(PDO::FETCH_ASSOC);
      $uid_user       =  $row['uid_user'] ;
      $uid_comp_name  =  $row['uid_comp_name'] ;  
      $organisation  =  $row['organisation'] ;    
  }
  
  function WriteMentorTeamUserOnChangeEvent($con)
  {
	echo '<script>
	     function OnChangeUser()
	     { var LOrganisation = "";
		   var LUIDUser = document.getElementById("uid_user").value;
		   if (false) {}
		 ';

    $sql = ('select uid, primary_org from user order by primary_org');
    foreach($con->query($sql) as $row)
    {
	    echo 'else if (LUIDUser == "' . $row['uid'] . '") {LOrganisation = "' . $row['primary_org'] . '";}
		     ';   
	}
	echo 'else    {LOrganisation = "";}
		   document.getElementById("organisation").value = LOrganisation;
	     }
	     </script>
		 ';
  }

  function WriteHTML($con, $Heading, $FormAction, $uid, 
    $uid_user,      $message_user,
    $uid_comp_name, $message_comp_name,
    $organisation,  $message_organisation)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'mentor-team', 'edit.php');
    WriteMentorTeamUserOnChangeEvent($con);
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldDropDown(
      'uid_comp_name', 'Competition', $uid_comp_name, $con, 
      'select uid_comp_name as uid, concat(year, "-", state, " ", comp_name) as display from v_comp_name order by display',
      $message_comp_name);
    CEWriteFormFieldDropDown(
      'uid_user', 'User', $uid_user, $con, 
      'select uid, concat(first_name, " ", last_name) as display from user order by display',
      $message_comp_name, "OnChangeUser()");
    CEWriteFormFieldText('organisation', 'Organisation', $organisation, 60, $message_organisation);
    CEWriteFormEnd('/sys-admin/mentor-team');
    CEWritePageEnd();
  }

  function Save($con, $sql, $uid, $uid_user, $uid_comp_name, $organisation)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':uid_user',      $uid_user);
    $query->bindParam(':uid_comp_name', $uid_comp_name);
    $query->bindParam(':organisation',  $organisation);
    $result = $query->execute();
    header('location: /sys-admin/mentor-team');
  }

  
 try
 {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
    
    $action         = postFieldDefault('action');
    $uid            = postFieldDefault('uid');
    $uid_user       = trim(postFieldDefault('uid_user'));
    $uid_comp_name  = trim(postFieldDefault('uid_comp_name'));
    $organisation   = trim(postFieldDefault('organisation'));
    $message_user         = '';
    $message_comp_name    = '';
    $message_organisation = '';

    if ($action == CE_NEW)
    {
      WriteHTML(
        $con,
        'Add a new mentor-team', CE_INSERT, '', 
        $uid_user,      '',
        $uid_comp_name, '',
        $organisation,  '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $uid_user, $uid_comp_name, $organisation);
      WriteHTML(
        $con,
        'Edit a mentor-team', CE_UPDATE, $uid, 
        $uid_user,      '',
        $uid_comp_name, '',
        $organisation,  '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, 
                   $uid_user,      $message_user,
                   $uid_comp_name, $message_comp_name,
                   $organisation,  $message_organisation))
      {
        Save($con, 'insert into mentor_team (uid, uid_user, uid_comp_name, organisation) values (:uid, :uid_user, :uid_comp_name, :organisation)',
             $uid, $uid_user, $uid_comp_name, $organisation);
      }
      else
      {
        WriteHTML(
          $con,
          'Add a new mentor-team', CE_INSERT, $uid, 
          $uid_user,      $message_user,
          $uid_comp_name, $message_comp_name,
          $organisation,  $message_organisation);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, 
                   $uid_user,      $message_user,
                   $uid_comp_name, $message_comp_name,
                   $organisation,  $message_organisation))
      {
    	  Save($con, 'update mentor_team set uid_user = :uid_user, uid_comp_name = :uid_comp_name, organisation = :organisation where uid = :uid',
               $uid, $uid_user, $uid_comp_name, $organisation);
	  }
      else
      {
        WriteHTML(
          $con,
          'Edit a mentor-team ', CE_UPDATE, $uid, 
          $uid_user,      $message_user,
          $uid_comp_name, $message_comp_name,
          $organisation,  $message_organisation);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/mentor-team');
  }  
?>