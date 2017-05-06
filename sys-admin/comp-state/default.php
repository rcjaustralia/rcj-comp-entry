<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function formatTreasurer($row){
    if (!empty($row['treasurer_email'])){
      $result = 
        htmlspecialchars($row['treasurer_first_name']) . ' ' . 
        htmlspecialchars($row['treasurer_last_name']) . '<br><small>' .
        htmlspecialchars($row['treasurer_primary_org']) . '<br>' . 
        '<a href="mailto://' .
        htmlspecialchars($row['treasurer_email']) . '">' .
        htmlspecialchars($row['treasurer_email']) . '</a></small>';
      return $result;
    } else {
      return '-';
    }
  }
  
  if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
      exit(); //==>>
  }
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage Competition States');
  
  echo '<script>
         function editState(uidState){
           cePost(\'/comp-admin/comp-state-edit.php\', {uid: uidState, action: \'' . CE_EDIT . '\', return_to: \'/sys-admin/comp-state/\'});           
         }
       </script>';  
  echo '<h1>Manage the Robocup Competition States</h1>';
  echo '<p class="indent"><a href="/">Home</a> | <a href="javascript:CEPostNew()">Add new</a></p>';

  $sql = 
   'select        
      uid,        
      state,      
      treasurer_first_name,      
      treasurer_last_name,      
      treasurer_email,
      treasurer_primary_org,      
      account_name      
    from          
      v_comp_state  
    order by      
      state'; 

  echo "<table border='1'>
  <tr>
  <th>State</th>
  <th>Treasurer</th>
  <th>Bank account</th>
  <th>Action</th>
  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['state']) . '</td>';
    echo '<td>' . formatTreasurer($row) . '</td>';
    echo '<td>' . htmlspecialchars($row['account_name']) . '</td>';
    echo '<td><small>' .
         '<a href="javascript:editState(\'' . $row["uid"] . '\')">Edit</a> | ' .
         '<a href="javascript:CEPostDelete(\'' . $row["uid"] . '\')">Delete</a>' .
         '</small></td>';
    echo "</tr>";
  }

  echo "</table>";
  echo '<p class="indent"><a href="javascript:CEPostNew()">Add new</a></p>';
  CEWritePageFooter();
  
?>