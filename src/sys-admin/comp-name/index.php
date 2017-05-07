<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage the Robocup Competitions');

  echo '<h1>Manage the Robocup Competitions</h1>';
  echo '<p class="indent"><a href="/">Home</a> | <a href="javascript:CEPostNew()">Add new</a></p>';

  $sql = 
    'select           ' . 
    '  uid_comp_name, ' . 
    '  year,          ' . 
    '  state,         ' . 
    '  comp_name,     ' . 
    '  start_date,    ' . 
    '  end_date,      ' . 
    '  active         ' . 
    'from             ' . 
    '  v_comp_name    '; 

  echo "<table>
  <tr>
  <th>Year</th>
  <th>State</th>
  <th>Competition Name</th>
  <th>Open Date</th>
  <th>Close Date</th>
  <th>Competition Open?</th>
  <th>Action</th>
  </tr>";

  date_default_timezone_set('Australia/Victoria');
  
  foreach($con->query($sql) as $row)
  {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['year']) . "</td>";
    echo "<td>" . htmlspecialchars($row['state']) . "</td>";
    echo "<td>" . htmlspecialchars($row['comp_name']) . "</td>";
    echo "<td>" . date('d-M-Y', strtotime($row['start_date'])) . "</td>";
    echo "<td>" . date('d-M-Y', strtotime($row['end_date'])) . "</td>";
    echo "<td>" . CEIIF($row['active'] == "0", "-", "Yes") . "</td>";
    echo '<td><small>' .
         '<a href="javascript:CEPostEdit(\'' . $row["uid_comp_name"] . '\')">Edit</a> | ' .
         '<a href="javascript:CEPostDelete(\'' . $row["uid_comp_name"] . '\')">Delete</a>' .
         '</small></td>';
    echo "</tr>";
  }

  echo "</table>";
  echo '<p class="indent"><a href="javascript:CEPostNew()">Add new</a></p>';
  CEWritePageFooter();
  
?>

</html>