<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage Competition Divisions');
  echo '<h1>Manage the Robocup Competition Divisions</h1>';
  echo '<p class="indent"><a href="/">Home</a> | <a href="javascript:CEPostNew()">Add new</a></p>';

  $sql = 
    'select               ' . 
    '  uid_comp_division, ' . 
    '  year,              ' . 
    '  state,             ' . 
    '  comp_name,         ' . 
    '  disp_order,        ' . 
    '  div_name           ' . 
    'from                 ' . 
    '  v_comp_division    ' .
    'where ' .
    '  uid_comp_division is not null'; 


  echo '<table border="1">
        <tr>
        <th>Year</th>
        <th>State</th>
        <th>Competition Name</th>
        <th>Display Order</th>
        <th>Competition Division</th>
        <th>Action</th>
        </tr>';

  foreach($con->query($sql) as $row)
  {
    echo '<tr>' .
         '<td>' . htmlspecialchars($row['year']) . '</td>' .
         '<td>' . htmlspecialchars($row['state']) . '</td>' .
         '<td>' . htmlspecialchars($row['comp_name']) . '</td>' .
         '<td>' . $row['disp_order'] . '</td>' .
         '<td>' . htmlspecialchars($row['div_name']) . '</td>' .
         '<td><small>' .
         '<a href="javascript:CEPostEdit(\'' . $row["uid_comp_division"] . '\')">Edit</a> | ' .
         '<a href="javascript:CEPostDelete(\'' . $row["uid_comp_division"] . '\')">Delete</a>' .
         '</small></td>' .
         "</tr>";
  }

  echo "</table>";
  echo '<p class="indent"><a href="javascript:CEPostNew()">Add new</a></p>';
  CEWritePageFooter();
  
?>

</html>