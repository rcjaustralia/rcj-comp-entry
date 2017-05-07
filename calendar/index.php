<?php 

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function getCompDivisions($con, $uid_comp_name){
    $query = $con->prepare('select div_name from comp_division where uid_comp_name = :uid_comp_name order by disp_order');
    $query->bindParam(':uid_comp_name', $uid_comp_name);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $result = '';
    foreach($query as $row){
      $result .= htmlspecialchars($row['div_name']) . '</br>';
    }
    return $result;
  }
  
  function writeEvents($con, $state){
    $sql = 
      'select ' .
      '  state, ' .	 
      '  comp_name, ' .	 
      '  uid_comp_name, ' .	 
      '  event_date, ' .	 
      '  start_date, ' .	 
      '  end_date, ' .	 
      '  custom_message, ' .	 
      '  venue, ' .	 
      '  enquiries_to_first_name, ' .	 
      '  enquiries_to_last_name, ' .	 
      '  enquiries_to_email ' .
      'from ' .
      '  v_comp_name ' .
      'where ' .
      '  active ';
    if (!empty($state)){
      $sql .= ' and state = :state ' ;
    }
      $sql .=
      'order by ' .
      '  event_date ';
  
    $query_result = $con->prepare($sql);
    if (!empty($state)){
      $query_result->bindParam(':state', $state);
    }
    $query_result->execute();
    $query_result->setFetchMode(PDO::FETCH_ASSOC);
    $onlineEntries = 'http://' . $_SERVER['HTTP_HOST'];
    $rowsExist = false;
    foreach($query_result as $row){
      $rowsExist = true;
      $html = '';
      $html .= '<h2 style="margin-left: 30px">' . htmlspecialchars($row['state']) . ' - ' . htmlspecialchars($row['comp_name']) . '</h2>';
      $html .= '<p style="margin-left: 60px">';
      $html .= '<b>Event date:</b> ' . date('d-M-Y', strtotime($row['event_date'])) . '</br>';
      $html .= '<b>Entries open:</b> ' . date('d-M-Y', strtotime($row['start_date'])) . '</br>';
      $html .= '<b>Entries close:</b> ' . date('d-M-Y', strtotime($row['end_date'])) . '</br>';
      $html .= '<b>Enter online:</b> <a href="' . $onlineEntries . '">' . $onlineEntries . '</a></br>';  
      $html .= '<b>Venue:</b> ' . $row['venue'] . '</br>';
      $html .= '<b>Enquiries to:</b> <a mailto="' . 
                 htmlspecialchars($row['enquiries_to_email']) . '">' . 
                 htmlspecialchars($row['enquiries_to_first_name']) . ' ' . 
                 htmlspecialchars($row['enquiries_to_last_name']) . ' &lt;' . 
                 htmlspecialchars($row['enquiries_to_email']) . '&gt;' .
                 '</a></br>';
      $html .= '</p>';
      $html .= '<p style="margin-left: 60px"><b>Divisions:</b></p><p style="margin-left: 90px">';
      $html .= getCompDivisions($con, $row['uid_comp_name']); 
      $html .= '</p>';
      if (!empty($row['custom_message'])){
        $html .= '<p style="margin-left: 60px"><b>Special note</b></p>';
        $html .= '<p style="margin-left: 90px">';
        $html .= $row['custom_message'];
        $html .= '</p>'; 
      }
     
      echo $html;
    }
    if (!$rowsExist){
      echo '<p style="margin-left: 60px">There are no competitions open at this stage.</p>';
    }
  }
  
  $state = strtoupper(getFieldDefault('state'));
  
  echo '
  <html>
    <head>
      <title>Robocup Junior Australia | Calendar of Events';
  if (!empty($state)){
    echo ' | ' . $state;
  }    
  echo '</title>
      <link rel="icon" type="image/ico" href="favicon.ico">
	  <link rel="stylesheet" type="text/css" href="/shared/style.css">
     </head>
      <body>
      <h1>Robocup Junior Australia | Calendar of Events';
  if (!empty($state)){
    echo ' | ' . $state;
  }    
  echo '</h1>';
  writeEvents($con, $state);  
  echo '</body>
  </html>';    

?>