<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/my-entries/student-bom.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/my-entries/student-gui.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/my-entries/student-svr.php';
  
    
  function GetValuesFromPK($con, $uid_team, &$uid_comp_division, &$team_name)
  {
    $sql = $con->prepare('select uid_comp_division, team_name from team where uid = :uid');
    $sql->bindParam(':uid', $uid_team);
    $sql->execute();
    $row             = $sql->fetch(PDO::FETCH_ASSOC);
    $uid_comp_division = $row['uid_comp_division'] ;
    $team_name  = $row['team_name'] ;
  }  

  function WriteHTMLEdit($con, $Heading, $FormAction, 
        $uid_team, $uid_comp_name, $uid_mentor_team, 
        $uid_comp_division, $uid_comp_division_message, 
        $team_name,  $message_team_name)
  {
    WriteConnectUserDetails($con);
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    CEWriteFormStart($Heading, 'edit-team', 'edit-team.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid_team', $uid_team);
    CEWriteFormFieldHidden('uid_comp_name', $uid_comp_name);
    CEWriteFormFieldHidden('uid_mentor_team', $uid_mentor_team);
    CEWriteFormFieldDropDown('uid_comp_division', 'Competition Division', $uid_comp_division, 
      $con, 'select uid, div_name as display from comp_division where uid_comp_name = "' . $uid_comp_name . '" order by disp_order', $uid_comp_division_message);
    CEWriteFormFieldText('team_name', 'Team Name', $team_name, 60, $message_team_name);
    CEWriteFormEnd('/my-entries/edit-comp.php');
    CEWritePageEnd();
  }
  
  function WriteHTMLInsert($con, $Heading, $FormAction, 
        $uid_team, $uid_comp_name, $uid_mentor_team,  
        $uid_comp_division, $uid_comp_division_message, 
        $team_name,  $message_team_name,
        $student1 = null, $student2 = null,
        $student3 = null, $student4 = null,
        $student5 = null)
  {
    WriteConnectUserDetails($con);
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    CEWriteFormStart($Heading, 'edit-team', 'edit-team.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid_team', $uid_team);
    CEWriteFormFieldHidden('uid_comp_name', $uid_comp_name);
    CEWriteFormFieldHidden('uid_mentor_team', $uid_mentor_team);
    
    CEWriteFormFieldDropDown('uid_comp_division', 'Competition Division', $uid_comp_division, 
      $con, 'select uid, div_name as display from comp_division where uid_comp_name = "' . $uid_comp_name . '" order by disp_order', $uid_comp_division_message);
    CEWriteFormFieldText('team_name', 'Team Name', $team_name, 60, $message_team_name);
    
    writeHTMLStudent($student1, '1');
    echo '<p>';    
    ceWriteSaveAndCancelButtons('/my-entries/edit-comp.php'); 
    
    writeHTMLStudent($student2, '2');
    echo '<p>';    
    ceWriteSaveAndCancelButtons('/my-entries/edit-comp.php'); 

    writeHTMLStudent($student3, '3');
    echo '<p>';    
    ceWriteSaveAndCancelButtons('/my-entries/edit-comp.php'); 
    
    writeHTMLStudent($student4, '4');    
    echo '<p>';    
    ceWriteSaveAndCancelButtons('/my-entries/edit-comp.php'); 
    
    writeHTMLStudent($student5, '5');
    echo '<p>';
    
    CEWriteFormEnd('/my-entries/edit-comp.php');
    CEWritePageEnd();
  }

  function UniqueCheckTeam($con, $uid_team, $uid_comp_division, $team_name)
  {
    $sql = $con->prepare(
	  'select count(*) as count from team ' .
	  'where ' .
	  '  uid <> :uid_team and ' .
	  '  uid_comp_division = :uid_comp_division and ' .
	  '  team_name  = :team_name');
    $sql->bindParam(':uid_team', $uid_team);
    $sql->bindParam(':uid_comp_division',   $uid_comp_division);
    $sql->bindParam(':team_name', $team_name);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function ValidateEdit($con, $uid_team, $uid_comp_name, 
        $uid_comp_division, &$message_uid_comp_division, 
        $team_name,  &$message_team_name)
  {
    $message_uid_comp_division    = '';
    $message_team_name  = '';

    CECheckNotNull($uid_comp_division, $message_uid_comp_division, 'Please select a competition division.');
    CECheckNotNull($team_name,  $message_team_name,  'Please enter a team name.');

    if (empty($message_uid_comp_division) and empty($message_team_name))
    {
      if (!UniqueCheckTeam($con, $uid_team, $uid_comp_division, $team_name))
      {  
        $message_team_name = 'This team name has already been used in this division. Please enter a unique team name.'; 
      }
    }
    
    return 
      empty($message_uid_comp_division) and 
      empty($message_team_name); 
   
  }

  function clearMessages($student){
    $student->firstNameMessage = '';
    $student->lastNameMessage = '';  
  }
  
  function validateStudent(
    $thisStudent,
    $otherStudent1, $otherStudent2,
    $otherStudent3, $otherStudent4){
    
    clearMessages($thisStudent);
 
    if (!$thisStudent->isEmpty()){
      $thisStudent->isValid();
        
      if ($thisStudent->namesEqual($otherStudent1) or
          $thisStudent->namesEqual($otherStudent2) or
          $thisStudent->namesEqual($otherStudent3) or
          $thisStudent->namesEqual($otherStudent4)){
        $thisStudent->firstNameMessage = 'Please enter a unique name';        
      }    
    }  
  }
  
  function validateTeam($con, $uid_team, $uid_comp_name, 
        $uid_comp_division, &$message_uid_comp_division, 
        $team_name,  &$message_team_name,
        $student1, $student2, $student3, $student4, $student5)
  {
    clearMessages($student1);
    clearMessages($student2);
    clearMessages($student3);
    clearMessages($student4);
    clearMessages($student4);

    $result = ValidateEdit($con, $uid_team, $uid_comp_name,  
        $uid_comp_division, $message_uid_comp_division, 
        $team_name,  $message_team_name);
           
    validateStudent($student1, $student2, $student3, $student4, $student5);
    validateStudent($student2, $student1, $student3, $student4, $student5);
    validateStudent($student3, $student1, $student2, $student4, $student5);
    validateStudent($student4, $student1, $student2, $student3, $student5);
    validateStudent($student5, $student1, $student2, $student3, $student4);
    $student1->isValid();
    
    return 
      ($result and 
       !$student1->hasInvalidMessage() and
       !$student2->hasInvalidMessage() and
       !$student3->hasInvalidMessage() and
       !$student4->hasInvalidMessage() and
       !$student5->hasInvalidMessage()); 
  }
  
  function Update($con, $uid_team, $uid_comp_division, $team_name)
  {
    $query = $con->prepare('update team set uid_comp_division = :uid_comp_division, team_name = :team_name where uid = :uid');
    $query->bindParam(':uid', $uid_team);
    $query->bindParam(':uid_comp_division', $uid_comp_division);
    $query->bindParam(':team_name', $team_name);
    $result = $query->execute();
    header('location: /my-entries/edit-comp.php');
  }
   
   function Insert($con, $uid_team, $uid_mentor_team, $uid_comp_division, $team_name,  
     $student1, $student2, $student3, $student4, $student5)
  {
    $query = $con->prepare(
      'insert into team ' .
      '(uid, uid_mentor_team, uid_comp_division, team_name) ' .
      'values ' .
      '(:uid_team, :uid_mentor_team, :uid_comp_division, :team_name)');
    $query->bindParam(':uid_team', $uid_team);
    $query->bindParam(':uid_mentor_team', $uid_mentor_team);
    $query->bindParam(':uid_comp_division', $uid_comp_division);
    $query->bindParam(':team_name', $team_name);
    $result = $query->execute();
    
    insertStudentIfValid($con, $uid_team, $student1);
    insertStudentIfValid($con, $uid_team, $student2);
    insertStudentIfValid($con, $uid_team, $student3);
    insertStudentIfValid($con, $uid_team, $student4);
    insertStudentIfValid($con, $uid_team, $student5);

    header('location: /my-entries/edit-comp.php');
  }
  
try
 {
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

  	$uid_user          = $_SESSION['uid_logged_on_user'];
	  $action            = postFieldDefault('action');
    
    $uid_team          = postFieldDefault('uid_team');
    $uid_comp_name     = postFieldDefault('uid_comp_name');
    $uid_mentor_team   = postFieldDefault('uid_mentor_team');

    $team_name         = postFieldDefault('team_name'); 
    $uid_comp_division = postFieldDefault('uid_comp_division');
    $student1 = new rcjStudent();
    $student2 = new rcjStudent();
    $student3 = new rcjStudent();
    $student4 = new rcjStudent();
    $student5 = new rcjStudent();
    
    $message_team_name = '';
    readStudentFromHTML($student1, '1');
    readStudentFromHTML($student2, '2');
    readStudentFromHTML($student3, '3');
    readStudentFromHTML($student4, '4');
    readStudentFromHTML($student5, '5');
    
    $team_name_message           = ''; 
    $uid_comp_division_message   = '';
      
    if ($action == CE_NEW)
    {
      WriteHTMLInsert(
        $con, 'Add a team', CE_INSERT, $uid_team, $uid_comp_name, $uid_mentor_team, 
        $uid_comp_division, $uid_comp_division_message, 
        $team_name,  $message_team_name);          
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid_team, $uid_comp_division, $team_name);
      WriteHTMLEdit(
        $con, 'Edit a team', CE_UPDATE, $uid_team, $uid_comp_name, $uid_mentor_team, 
        $uid_comp_division, '', 
        $team_name,  '');        
    }
    else if ($action == CE_INSERT)
    {
       ceNewUIDIfRequired($uid_team);
       if (validateTeam($con, $uid_team, $uid_comp_name,  
        $uid_comp_division, $message_uid_comp_division, 
        $team_name,  $message_team_name,
        $student1, $student2, $student3, $student4, $student5))
        {
          Insert($con, $uid_team, $uid_mentor_team, $uid_comp_division, $team_name,  
            $student1, $student2, $student3, $student4, $student5);
        }
        else
        {
          WriteHTMLInsert(
            $con, 'Edit a team', CE_INSERT, $uid_team, $uid_comp_name, $uid_mentor_team, 
            $uid_comp_division, $message_uid_comp_division, 
            $team_name,  $message_team_name,
            $student1, $student2, $student3, $student4, $student5);
        }    
    }     
    else if ($action == CE_UPDATE)
    {
       if (ValidateEdit($con, $uid_team, $uid_comp_name, 
        $uid_comp_division, $message_uid_comp_division, 
        $team_name,  $message_team_name))
       {
		 Update($con, $uid_team, $uid_comp_division, $team_name);
       }
       else
       {
      WriteHTMLEdit(
        $con, 'Edit a team', CE_UPDATE, $uid_team, $uid_comp_name, $uid_mentor_team, 
        $uid_comp_division, $message_uid_comp_division, 
        $team_name,  $message_team_name);        
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