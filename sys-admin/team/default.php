<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage Competition Teams');
  
  echo '<h1>Manage the Robocup Competition Teams</h1>';
  echo '<p class="indent"><a href="/">Home</a> | <a href="javascript:CEPostNew()">Add new</a></p>';
  
  $sql = 
    'select               ' . 
    '  uid,               ' . 
    '  year,              ' . 
    '  state,             ' . 
    '  comp_name,         ' . 
    '  mentor_first_name, ' .
    '  mentor_last_name,  ' .
    '  organisation,      ' .
    '  div_name,          ' .
    '  team_name          ' .
    'from                 ' . 
    '  v_team '; 

  echo "<table>
  <tr>
  <th>Year</th>
  <th>State</th>
  <th>Competition Name</th>
  <th>Mentor Organisation</th>
  <th>Mentor First Name</th>
  <th>Mentor Last Name</th>
  <th>Division</th>
  <th>Team Name</th>
  <th>Action</th>
  </tr>";

  foreach($con->query($sql) as $row)
  {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['year']) . "</td>";
    echo "<td>" . htmlspecialchars($row['state']) . "</td>";
    echo "<td>" . htmlspecialchars($row['comp_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['organisation']) . "</td>";
    echo "<td>" . htmlspecialchars($row['mentor_first_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['mentor_last_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['div_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['team_name']) . "</td>";
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