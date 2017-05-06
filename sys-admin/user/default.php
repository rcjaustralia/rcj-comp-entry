<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function AccessLevelToDisplayText($key)
  {
	if ($key == C_MENTOR)
	  return 'Mentor';
	else if ($key == C_COMP_ADMIN)
	  return 'Competition Administrator';
	else if ($key == C_SYS_ADMIN)
      return 'System Administrator';
	else if ($key == C_SYS_DEV)
      return 'System Developer';
	else 
      throw new Exception('Invalid AccessLevel: ""' . $key . '"'); 
  }
  
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
  
  function formatAddress($row){
    $return = 
      '<a href="mailto://' . htmlspecialchars($row['email']) . '">' . 
      htmlspecialchars($row['email']) . '</a>';
    if (!empty($row['adrs_line_1'])){
      $adrs = '<small>' . htmlspecialchars($row['adrs_line_1']) . '<br>';
      if (!empty($row['adrs_line_2'])){
        $adrs = $adrs . htmlspecialchars($row['adrs_line_2']) . '<br>'; 
      }
      $adrs = $adrs . 
        htmlspecialchars($row['suburb']) . '&nbsp;&nbsp' . 
        htmlspecialchars($row['state']) . '&nbsp;&nbsp' . 
        htmlspecialchars($row['postcode']) . '</small>';
      $return = $return . '<br>' . $adrs;  
    } 
    return $return; 
  }
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage users of the Robocup registration system');
  echo '<script>
          function ResetPassword(AUID)
          {
            cePost("admin-reset-password.php", {uid: AUID} ); 
          }  
          function Impersonate(AUID)
          {
            cePost("impersonate.php", {uid: AUID} ); 
          }  
        </script>
       ';
       
  echo '<h1>Manage users of the Robocup registration system</h1>';
  
  echo '<p class="indent"><a href="/">Home</a> | <a href="javascript:CEPostNew()">Add new</a></p>';

  $sql = 
    'select           
       uid,           
       email,         
       last_name,     
       first_name,    
       primary_org,   
       access_level,  
       adrs_line_1,   
       adrs_line_2,   
       suburb,        
       postcode,      
       state          
     from             
       user          
     where          
       access_level <> "SYS_DEV"     
     order by         
       last_name,    
       first_name,   
       primary_org'; 

  echo "<table border='1'>
  <tr>
  <th>Last Name</th>
  <th>First Name</th>
  <th>EMail</th>
  <th>Primary Organisation</th>
  <th>Access Level</th>
  <th>Action</th>
  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
    echo '<td>' . formatAddress($row) . '</td>';
    echo '<td>' . htmlspecialchars($row['primary_org']) . '</td>';
    echo '<td>' . AccessLevelToDisplayText($row['access_level']) . '</td>';
    echo '<td><small>' .
         '<a href="javascript:CEPostEdit(\'' . $row["uid"] . '\')">Edit</a> | ' .
         '<a href="javascript:CEPostDelete(\'' . $row["uid"] . '\')">Delete</a> | ' .
         '<a href="javascript:ResetPassword(\'' . $row["uid"] . '\')">Reset Pwd</a> | ' .
         '<a href="javascript:Impersonate(\'' . $row["uid"] . '\')">Impersonate</a>' .
         '</small></td>';
    echo "</tr>";
  }

  echo "</table>";
  echo '<p class="indent"><a href="javascript:CEPostNew()">Add new</a></p>';
  CEWritePageFooter();
  
?>