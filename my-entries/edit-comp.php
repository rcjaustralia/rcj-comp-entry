<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function GetValuesFromPK($con, $uid_comp_name, $uid_user, &$uid_mentor_team, &$year, &$state, &$comp_name, &$organisation, &$comp_message)
  {
    $sql = $con->prepare('select uid, year, state, comp_name, organisation, comp_message from v_mentor_team where uid_user = :uid_user and uid_comp_name = :uid_comp_name');
    $sql->bindParam(':uid_comp_name', $uid_comp_name);
    $sql->bindParam(':uid_user', $uid_user);
    $sql->execute();
    $row             = $sql->fetch(PDO::FETCH_ASSOC);
    $uid_mentor_team = $row['uid'] ;
    $year            = $row['year'] ;
    $state           = $row['state'] ;
    $comp_name       = $row['comp_name'] ;
    $organisation    = $row['organisation'] ;  
    $comp_message    = $row['comp_message'] ;  
  }  

  function WriteNewTeamLink($uid_mentor_team, $uid_comp_name){
    echo '<tr><td colspan="4"><a href="javascript:NewTeam(\'' . $uid_mentor_team . '\', \'' . $uid_comp_name . '\')">Add a team</a></td></tr>';      
  }
  
  
  function WriteHTML($con, $readOnly, $year, $state, $comp_name, $uid_mentor_team, $uid_comp_name, $organisation, $comp_message)
  {
    WriteConnectUserDetails($con);
    CEWritePageHeader(C_SITE_TITLE, 'Manage Competition Divisions');
    if ($readOnly != true){
      echo '<script>
              function NewTeamMember(AUIDTeam)
              {
                cePost("/my-entries/edit-team-member.php", {action: "NEW", uid_team: AUIDTeam} ); 
              }

              function EditTeamMember(AUIDTeam, AUIDTeamMember)
              {
                cePost("/my-entries/edit-team-member.php", {action: "EDIT", uid_team: AUIDTeam, uid_team_member: AUIDTeamMember} ); 
              }

              function DeleteTeamMember(AUIDTeamMember)
              {
                cePost("/my-entries/delete-team-member.php", {uid: AUIDTeamMember} ); 
              }

              function NewTeam(AUIDMentorTeam, AUIDCompName)
              {
                cePost("/my-entries/edit-team.php", {action: "NEW", uid_mentor_team: AUIDMentorTeam, uid_comp_name: AUIDCompName} ); 
              }              

              function EditTeam(AUIDCompName, AUIDCompDivision, AUIDTeam)
              {
                cePost("/my-entries/edit-team.php", {action: "EDIT", uid_comp_name: AUIDCompName, uid_comp_division: AUIDCompDivision, uid_team: AUIDTeam} ); 
              }              
                
              function ChangeOrganisation(AUIDMentorTeam)
              {
                cePost("/my-entries/edit-organisation.php", {action: "EDIT", uid_mentor_team: AUIDMentorTeam} ); 
              }              

            </script>';
    }

    if (!empty($comp_message)){
      echo '<p><a href="/">Home</a></p>';
      echo '<fieldset><legend>Competition message</legend>' . $comp_message . '</fieldset>';        
    }
    
    if ($readOnly != true){
      echo '<h2>Manage my ' .
           '<a href="javascript:ChangeOrganisation(\'' . $uid_mentor_team . '\')" style="font-size: 1.100em">' . 
           $organisation . '</a>' .
           ' entries in ' . $year . '-' . $state . ' ' . $comp_name . '</h2>';
    } else {
      echo '<h2>' .$organisation . 
           ' entries in ' . $year . '-' . $state . ' ' . $comp_name . '</h2>';
    }
    $sql = 
      'select ' .
      '  tm.uid_mentor_team, ' .
      '  tm.uid_comp_name, ' .
      '  tm.uid_comp_division, ' .
      '  tm.uid_team, ' .
      '  tm.uid_team_member, ' .
      '  tm.div_name, ' .
      '  tm.team_name, ' .
      '  concat(tm.team_member_first_name, " ", tm.team_member_last_name) as team_member_name, ' .
      '  c.count_team_member ' .
      'from ' .
      '  v_team_member tm, ' .
      '  (select uid_team, count(*) as count_team_member from team_member group by uid_team) c ' .
      'where ' .
      '  tm.uid_mentor_team = :uid_mentor_team and ' .
      '  c.uid_team = tm.uid_team ' .
      'order by ' .
      '  tm.div_disp_order, ' .
      '  tm.team_name, ' .
      '  tm.team_member_last_name, ' .
      '  tm.team_member_first_name';
  
    $query = $con->prepare($sql); 
    $query->bindParam(':uid_mentor_team', $uid_mentor_team);
    $query->execute();

    echo '<p><a href="/">Home</a></p>';
    echo '<table border="1">
          <tr>
          <th>Division</th>
          <th>Team Name</th>
          <th>Team Member Name</th>';
    if ($readOnly != true){
      echo '<th>Action</th>';
    }
    echo '</tr>';
    if ($readOnly != true){
      WriteNewTeamLink($uid_mentor_team, $uid_comp_name);
    }
    
    $last_div_name = '';
    $last_team_name = '';
    $write_action = false;
    $table_empty = true;
  
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
    {
      $table_empty = false;  
      echo '<tr>';
      echo '<td>'; 
      if ($row['div_name'] <> $last_div_name)
      {
        $last_div_name = $row['div_name'];
        $last_team_name = '';
        echo htmlspecialchars($row['div_name']);    
      } else {
        echo '&nbsp;';
      }
      echo '</td>';
      echo '<td>'; 
    
      if ($row['team_name'] <> $last_team_name)
      {
        $last_team_name = $row['team_name'];
        echo htmlspecialchars($row['team_name']);
        $write_action = true;    
      } else {
        echo '&nbsp;';
        $write_action = false;
      }
      echo '</td>';
      echo '<td>' . htmlspecialchars($row['team_member_name']);
      if ($readOnly != true){
        echo '<br><small>' .
             '<a href="javascript:EditTeamMember(\'' . $row["uid_team"] . '\', \'' . $row["uid_team_member"] . '\')" class="action">Edit</a>';
        if ($row['count_team_member'] > 1){
          echo '<span class="action"> | </span><a href="javascript:DeleteTeamMember(\'' . $row["uid_team_member"] . '\')" class="action">Delete</a>';
        }
        if ($row['count_team_member'] < 5){ // ToDo: Paramaterise the maximum size of a team      
          echo '<span class="action"> | </span><a href="javascript:NewTeamMember(\'' . $row["uid_team"] . '\')" class="action">Add new</a>';
        }
        echo '</small>';
      }
      echo '</td>';

      if ($readOnly != true){
        echo '<td>';
        if (($write_action)){
          echo '<small>';
          echo '<a href="javascript:EditTeam(\'' . $row["uid_comp_name"] . '\', \''  . $row["uid_comp_division"] . '\', \'' . $row["uid_team"] . '\')" class="action">Edit</a><span class="action"> | </span>' .
               '<a href="javascript:CEPostDelete(\'' . $row["uid_team"] . '\', \'/my-entries/delete-team.php\')" class="action">Delete</a>'; 
          echo '</small>';             
        } else {
          echo '&nbsp;';    
        }
        echo '</td>';
      }  
      echo '</tr>';
    }
    if ((!$table_empty) and ($readOnly != true)){
      WriteNewTeamLink($uid_mentor_team, $uid_comp_name);
    }

    echo '</table>';
    CEWritePageFooter();
  }

function InsertMentorTeam($con, &$uid_mentor_team, $uid_comp_name, $uid_user)
{
  ceNewUIDIfRequired($uid_mentor_team);
  $query = $con->prepare(
    'insert into mentor_team ' .
    '(uid, uid_user, uid_comp_name, organisation) ' .
    'values ' .
    '(:uid, :uid_user, :uid_comp_name, (select primary_org from user where uid = :uid_user))');
  $query->bindParam(':uid', $uid_mentor_team);
  $query->bindParam(':uid_comp_name', $uid_comp_name);
  $query->bindParam(':uid_user', $uid_user);
  $result = $query->execute();      
}

  try
  {
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

   $action = postFieldDefault('action');
   $readOnly = postFieldDefault('read_only', false);
   if (empty($action)) {$action = 'EDIT';} // To handle the case when there is a redirect to this form.
   $uid_user      = $_SESSION['uid_logged_on_user'];
   $uid_comp_name = postFieldDefault('uid_comp_name');
  
   if (!empty($uid_comp_name)) 
     {setcookie('uid_comp_name', $uid_comp_name);}
   else
     {$uid_comp_name = $_COOKIE['uid_comp_name'];}
  
   $uid_mentor_team = '';
   $year = '';
   $state = '';
   $comp_name = '';
   $organisation = '';
   $comp_message = '';
  
   GetValuesFromPK($con, $uid_comp_name, $uid_user, $uid_mentor_team, $year, $state, $comp_name, $organisation, $comp_message);
   if (empty($uid_mentor_team))
   {
     InsertMentorTeam($con, $uid_mentor_team, $uid_comp_name, $uid_user);
     GetValuesFromPK($con, $uid_comp_name, $uid_user, $uid_mentor_team, $year, $state, $comp_name, $organisation, $comp_message);
   }
    
   WriteHTML(
     $con,
     $readOnly,
     $year,
     $state,
     $comp_name,
     $uid_mentor_team,
     $uid_comp_name,
     $organisation,
     $comp_message);
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/team');
  }  
  
?>