<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function writeJavaScript(){
    echo '<script>
      function addNewYear(){
          cePost(\'comp-year-edit.php\', {action: \'' . CE_NEW . '\'});
      }

      function editState(uidState){
          cePost(\'comp-state-edit.php\', {uid: uidState, action: \'' . CE_EDIT . '\', return_to: \'/comp-admin/\'});
      }

      function addNewCompetition(uidYear){
          cePost(\'comp-edit.php\', {uid_comp_year: uidYear, action: \'' . CE_NEW . '\'});
      }
     
      function editCompetition(uidComp, uidReturnTo){
          cePost(\'comp-edit.php\', {uid: uidComp, uid_return_to: uidReturnTo, action: \'' . CE_EDIT . '\'});
      } 
      
      function deleteCompetition(uidComp, uidReturnTo){
          cePost(\'comp-delete.php\', {uid: uidComp, uid_return_to: uidReturnTo});
      }
      
      function addNewDivision(uidCompName, uidReturnTo){
          cePost(\'comp-division-edit.php\', {uid_comp_name: uidCompName, uid_return_to: uidReturnTo, action: \'' . CE_NEW . '\'});
      }
      
      function editDivision(uidDivision, uidReturnTo){
          cePost(\'comp-division-edit.php\', {uid: uidDivision, uid_return_to: uidReturnTo, action: \'' . CE_EDIT . '\'});
      }
      
      function deleteDivision(uidDivision, uidReturnTo){
          cePost(\'comp-division-delete.php\', {uid: uidDivision, uid_return_to: uidReturnTo});
      }
      
      function deleteYear(uidYear){
          cePost(\'year-delete.php\', {uid: uidYear});
      }
      
      function moveDivisionUp(uidDivision, uidReturnTo){
          cePost(\'comp-division-move.php\', {direction: \'UP\', uid: uidDivision, uid_return_to: uidReturnTo});
      }
      
      function moveDivisionDown(uidDivision, uidReturnTo){
          cePost(\'comp-division-move.php\', {direction: \'DOWN\', uid: uidDivision, uid_return_to: uidReturnTo});
      }
      
      </script>';
  }

  function writeNewCompetitionDivisionHTML($uidPreviousYear, $uidPreviousCompName, $uidReturnTo){
    echo '<tr>';
    if (!empty($uidPreviousCompName)){
      echo '<td><a name="' . $uidPreviousCompName . '"></a></td>';
    } else {
      echo '<td></td>';      
    }
    echo '<td colspan="4"><small>' . 
         '<a href="javascript:addNewCompetition(\'' . $uidPreviousYear . '\')">Add new competition</a>' .
         '</small></td>';        
    if (!empty($uidPreviousCompName)){
      echo '<td colspan="2"><small>' .
           '<a href="javascript:addNewDivision(\'' . $uidPreviousCompName . '\', \'' . $uidReturnTo . '\')">Add new division</a>' .
           '</small></td>';        
    }else{
      echo '<td colspan="2"></td>';        
    }	
    echo '</tr>';
  }

  function writeEditDivisionHTML($uidCompDivision, $uidReturnTo, $firstRow){
    if ($firstRow){
      $upLink = '<img border="0" alt="Up" src="/images/blank-16x16.png" width="16" height="16"> | ';
    } else {
      $upLink = '<a href="javascript:moveDivisionUp(\''   . $uidCompDivision . '\', \'' . $uidReturnTo . '\')"><img border="0" alt="Up" src="/images/up-16x16.png" width="16" height="16"></a> | ';
    }
    // ToDo: Write a blank image when we are at the bottom of a competition.
    //       But this is difficult as we do not know we are on the last record until we move to the next record.
    //       Iterate over the SQL result set as an array, rather than using a foreach on the results set.
    //       This will make it possible to look ahead to the next record.    
    echo '<small>';
    echo '<a href="javascript:editDivision(\'' . $uidCompDivision . '\', \'' . $uidReturnTo . '\')">Edit</a> | ';
    echo '<a href="javascript:deleteDivision(\'' . $uidCompDivision . '\', \'' . $uidReturnTo . '\')">Delete</a> | ';
    echo $upLink;
    echo '<a href="javascript:moveDivisionDown(\'' . $uidCompDivision . '\', \'' . $uidReturnTo . '\')"><img border="0" alt="Down" src="/images/down-16x16.png" width="16" height="16"></a>';
    echo '</small>'; 
  }
  
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }

  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Manage the Competition Mentor / Teams');
  writeJavaScript();
  echo '<h1>Manage the Robocup Competitions and Divisions</h1>';  
  echo '<p class="indent"><a href="/">Home</a></p>';
  
  $sql = 
    'select               ' . 
    '  uid_year,          ' .
  	'  uid_comp_division, ' .
    '  uid_comp_name,     ' .
    '  uid_state,         ' .
    '  year,              ' .
    '  state,             ' .
    '  comp_name,         ' .
    '  start_date,        ' .
    '  active,            ' .
    '  end_date,          ' .
    '  div_name, disp_order           ' .
	'from                 ' .
	'  v_comp_division    ' .
	'where                ' .
//	'  end_date >= CURDATE() + INTERVAL 30 DAY  ' .
    ' year = (select year(now) from v_local_time) ' .
	'order by             ' .
	'  end_date ASC       ';
	
  echo '<table border="1">
        <tr>
        <th>Year</th>
        <th>State</th>
        <th>Competition Name</th>
        <th>Action</th>
        <th>Open?</th>
        <th>Division Name</th>
        <th>Action</th>
        </tr>';
  
  $year = '';
  $lastYear = '';
  $state = '';
  $lastState = '';
  $compName = '';
  $uidCompName = '';
  $lastCompName = '';
  $open = '';
  $hasData = False;
  $compTitleRow = False;
  $firstRow = True;
  $uidPreviousCompName = '';
  $uidPreviousYear = '';
  $yearDeleteHTML = '';
  $uidReturnTo = '';
  
  foreach($con->query($sql) as $row)
  {

    $hasData = True;
    if ($lastYear != $row['year']){
      if (!empty($yearDeleteHTML)){
          $yearDeleteHTML = $yearDeleteHTML . ' | ';
      }  
      $yearDeleteHTML = $yearDeleteHTML . '<a href="javascript:deleteYear(\'' . $row['uid_year'] . '\')">Delete ' . $row['year'] . '</a>';     
    }
    
	if (($lastYear != $row['year']) || ($lastState != $row['state']) || ($lastCompName != $row['comp_name'])){
	  $compTitleRow = True;
    $lastYear = $row['year'];
	  $year = $lastYear;
	  $lastState = $row['state'];
	  $state = $lastState;
	  $compName = $row['comp_name'];
	  $lastCompName = $compName;
    if ((!$firstRow)){
      writeNewCompetitionDivisionHTML($uidPreviousYear, $uidPreviousCompName, $uidReturnTo);
    } 
    $uidReturnTo = $uidCompName;
	  $uidCompName = $row['uid_comp_name'];    
	  if($row['active']){$open = 'Yes';} else {$open = 'No';}
	} else{
    $compTitleRow = False;  
	  $year = '';
	  $state = '';
    $compName = '';
	  $open = '';
	}

    $firstRow = False;
    
    echo '<tr>';
    // Competition title (Year, State, Name....)
    if ($compTitleRow){
      echo '<td>' . htmlspecialchars($year) . '</td>';
      if (empty($row['uid_state'])){
        echo '&nbsp';// '<td><a href="javascript:editState(\'\')">Add new state</a></td>';
      } else {
        echo '<td>' . htmlspecialchars($state) . ' <a href="javascript:editState(\'' . $row['uid_state'] . '\')">Edit</a></td>';
      }
      echo '<td>' . htmlspecialchars($compName) . '</td>';
      
      if (!empty($compName)){
      echo '<td><small>' .
           '<a href="javascript:editCompetition(\''   . $uidCompName . '\', \'' . $uidReturnTo . '\')">Edit</a> | ' .
           '<a href="javascript:deleteCompetition(\'' . $uidCompName . '\', \'' . $uidReturnTo . '\')">Delete</a>' .
           '</small></td>';        
      echo '<td>' . $open . '</td>';
      }else{
        //Edit or delete the year here
        echo '<td></td><td></td>';        
      }
    }else{
      echo '<td></td><td></td><td></td><td></td><td></td>';
    }
    
    // Competition division
    echo '<td>' . htmlspecialchars($row['div_name']) . '</td>' .
         '<td>';

    if ($row['div_name'] != null){
      writeEditDivisionHTML($row["uid_comp_division"], $uidReturnTo, $compTitleRow);
    }
    echo '</td></tr>';

    $uidPreviousCompName = $row["uid_comp_name"];
    $uidPreviousYear = $row["uid_year"];
    
  }

  if ($hasData){
    writeNewCompetitionDivisionHTML($uidPreviousYear, $uidPreviousCompName, $uidReturnTo);
  }
  echo '<tr><td colspan=7><a href="javascript:addNewYear()">Add new year</a>';
  if (!empty($yearDeleteHTML)){
      echo ' | ' . $yearDeleteHTML;
  }
  echo '</td></tr>';
  echo '</table>';  
  CEWritePageFooter();
  
?>