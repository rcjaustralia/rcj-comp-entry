<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage the Competition Mentor / Teams');
  echo '<h1>Manage the Robocup Competition Mentor / Teams</h1>';  
  echo '<p class="indent"><a href="/">Home</a> | <a href="javascript:CEPostNew()">Add new</a></p>';
  
  $sql = 
    'select          ' . 
    '  uid,          ' . 
    '  year,         ' . 
    '  state,        ' . 
    '  comp_name,    ' . 
    '  first_name,   ' .
    '  last_name,    ' .
    '  organisation  ' .
    'from            ' . 
    '  v_mentor_team ' . 
    'order by        ' . 
    '  year,         ' . 
    '  state,        ' . 
    '  comp_name,    ' . 
    '  first_name,   ' .
    '  last_name,    ' .
    '  organisation  ';

  echo '<table border="1">
        <tr>
        <th>Year</th>
        <th>State</th>
        <th>Competition Name</th>
        <th>Mentor First Name</th>
        <th>Mentor Last Name</th>
        <th>Mentor Organisation</th>
        <th>Action</th>
        </tr>';

  foreach($con->query($sql) as $row)
  {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['year']) . '</td>';
    echo '<td>' . htmlspecialchars($row['state']) . '</td>';
    echo '<td>' . htmlspecialchars($row['comp_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['organisation']) . '</td>';
    echo '<td><small>' .
         '<a href="javascript:CEPostEdit(\'' . $row["uid"] . '\')">Edit</a> | ' .
         '<a href="javascript:CEPostDelete(\'' . $row["uid"] . '\')">Delete</a>' .
         '</small></td>';
    echo '</tr>';
  }

  echo '</table>';

  echo '<p class="indent"><a href="javascript:CEPostNew()">Add new</a></p>';
  
  CEWritePageFooter();
  
?>