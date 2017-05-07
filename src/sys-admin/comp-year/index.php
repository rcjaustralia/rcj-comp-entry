<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
      exit(); //==>>
  }
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage Competition Years');

  echo '<h1>Manage the Robocup Competition Years</h1>';
  echo '<p class="indent"><a href="/">Home</a> | <a href="javascript:CEPostNew()">Add new</a></p>';

  $sql = 
    'select       ' . 
    '  uid,       ' . 
    '  year       ' . 
    'from         ' . 
    '  comp_year  ' . 
    'order by     ' . 
    '  year       '; 

  echo "<table>
  <tr>
  <th>Year</th>
  <th>Action</th>
  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['year']) . '</td>';
    echo '<td><small>' .
         '<a href="javascript:CEPostEdit(\'' . $row["uid"] . '\')">Edit</a> | ' .
         '<a href="javascript:CEPostDelete(\'' . $row["uid"] . '\')">Delete</a>' .
         '</small></td>';
    echo "</tr>";
  }

  echo "</table>";
  echo '<p class="indent"><a href="javascript:CEPostNew()">Add new</a></p>';
  CEWritePageFooter();
  
?>