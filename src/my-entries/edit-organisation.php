<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function GetValuesFromPK($con, $uid_mentor_team, &$organisation)
  {
    $sql = $con->prepare('select organisation from mentor_team where uid = :uid');
    $sql->bindParam(':uid', $uid_mentor_team);
    $sql->execute();
    $row             = $sql->fetch(PDO::FETCH_ASSOC);
    $organisation = $row['organisation'] ;
  }  

  function WriteHTML($con, $Heading, $FormAction, $uid_mentor_team, 
        $organisation, $message_organisation)
  {
    WriteConnectUserDetails($con);
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    CEWriteFormStart($Heading, 'edit-organisation', 'edit-organisation.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid_mentor_team', $uid_mentor_team);
    CEWriteFormFieldTextAutofocus('organisation', 'Club or School', $organisation, 60, $message_organisation);
    CEWriteFormEnd('/my-entries/edit-comp.php');
    CEWritePageEnd();
  }

  function UniqueCheck($con, $uid_mentor_team, $organisation)
  {
    $sql = $con->prepare(
	  'select count(*) as count from mentor_team ' .
	  'where ' .
	  '  uid <> :uid_mentor_team and ' .
	  '  uid_comp_name in (select uid_comp_name from mentor_team where uid = :uid_mentor_team) and ' .
	  '  organisation = :organisation');
    $sql->bindParam(':uid_mentor_team', $uid_mentor_team);
    $sql->bindParam(':organisation', $organisation);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  
  
  function Validate($con, $uid_mentor_team, $organisation, &$message_organisation)
  {
    $message_organisation = '';
    
    CECheckNotNull($organisation, $message_organisation, 'Please enter a club or school name.');

    if (empty($message_organisation))
    {
      if (!UniqueCheck($con, $uid_mentor_team, $organisation))
      {  
        $message_organisation = 'This club or school name has already been used. Please enter a unique name.'; 
      }
    }
    
    return 
      empty($message_organisation); 
  }
  
  function Update($con, $uid_mentor_team, $organisation)
  {
    $query = $con->prepare('update mentor_team set organisation = :organisation where uid = :uid');
    $query->bindParam(':uid', $uid_mentor_team);
    $query->bindParam(':organisation', $organisation);
    $result = $query->execute();
    header('location: /my-entries/edit-comp.php');
  }
  
try
 {
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

	$uid_user        = $_SESSION['uid_logged_on_user'];
	$action          = postFieldDefault('action');
    $uid_mentor_team = postFieldDefault('uid_mentor_team');
    $organisation    = postFieldDefault('organisation');

    if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid_mentor_team, $organisation);
      WriteHTML(
        $con, 'Change the club or school my teams are competing for', CE_UPDATE, $uid_mentor_team, 
        $organisation, '');
    }
    else if ($action == CE_UPDATE)
    {
       if (Validate($con, $uid_mentor_team, $organisation, $message_organisation))
       {
		 Update($con, $uid_mentor_team, $organisation);
       }
       else
       {
        WriteHTML(
          $con, 'Change the club or school my teams are competing for', CE_UPDATE, $uid_mentor_team, 
          $organisation, $message_organisation);
       }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }

  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/my-entries/edit-comp.php');
  }  
  
?>