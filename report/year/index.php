<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';  

function DateTimeAsFileNameString($con){
  $sql = $con->prepare('select now from v_local_time');
  $sql->execute();
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  $return = preg_replace("/\W|_/", "", $row['now']);
  return $return;      
}

function UIDCompNameToFileName($con, $uid_year, $report_name){
  $time = DateTimeAsFileNameString($con);
  $sql = $con->prepare('select year from comp_year where uid = :uid_year');
  $sql->bindParam(':uid_year', $uid_year);
  $sql->execute();
  $row            = $sql->fetch(PDO::FETCH_ASSOC);
  $return = sanitize_file_name(
    $row['year'] . '-' . $report_name . '-' . $time) . '.csv';
  return $return;
}   

 
function QueryToCSVDownload($con, $sql, $file_name){
  $result = $con->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $num_fields = $result->columnCount();
  $headers = array();
  for ($i = 0; $i < $num_fields; $i++) {
      $col = $result->getColumnMeta($i);
      $headers[] = $col['name'];
  }
  $fp = fopen('php://output', 'w');
  if ($fp && $result) {
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="' . $file_name . '"');
      header('Pragma: no-cache');
      header('Expires: 0');
      fputcsv($fp, $headers);
      foreach($result as $row){
          fputcsv($fp, array_values($row));
      }
  }
 
}
 
function QueryToHTMLTable($con, $sql){
  $result = $con->query($sql);
  $result->setFetchMode(PDO::FETCH_ASSOC);
  $num_fields = $result->columnCount();
  $return = '<table><tr>';
  for ($i = 0; $i < $num_fields; $i++) {
      $col = $result->getColumnMeta($i);
      $col_name = $col['name'];
      $return .= '<th>' . $col_name . '</th>';
  }
  $return .=  '</tr>';
  foreach($result as $row){
    $return .= '<tr>';
    for ($i = 0; $i < $num_fields; $i++) {
        $col = $result->getColumnMeta($i);
        $col_name = $col['name'];
        $return .= '<td>' . htmlspecialchars($row[$col_name]) . '</td>';
    }
    $return .= '</tr>';
  }
  $return .= '</table>';
  return $return;
}    

function QueryToPage($con, $sql, $title, $uid_year, $report_format){
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, $title);
  $table = QueryToHTMLTable($con, $sql);
  echo '<script>' .
       '  function do_back(){' .
       '    cePost("/report/year/", '.
       '           {'.
       '             uid_year: "' . $uid_year . '",' . 
       '             report_format: "' . $report_format . '",' . 
       '           });' .
       '  }'.
       '</script>';
  echo '<h1>' . $title . '</h1>';
  echo '<p><a href="/">Home</a> | <a href="javascript:do_back();">Back</a></p>';  
  echo $table;
  CEWritePageEnd('javascript:do_back()');  
}
 
    
function WriteHTML($con, 
  $uid_year, $message_year,
  $report_format, $report_format_message){
    
  $sql = 
    'select ' .
    '  uid  as uid, ' .
    '  year as display ' .
    'from ' .
    '  comp_year ' .
    'order by ' .
    '  display';
  
  CEWritePageHeader(C_SITE_TITLE, 'Download Robocup Data by Year');
  WriteConnectUserDetails($con);
  CEWriteFormStart('Download Robocup Data by Year', 'report', '/report/year/');
  CEWriteFormAction('execute');
  CEWriteFormFieldDropDown('uid_year', 'Competition', $uid_year, $con, $sql, $message_year);
  CEWriteFormFieldDropDownHardCoded('report_format', 'Report Format', $report_format, 
    array('BROWSER' => 'Display in Browser', 'DOWNLOAD' => 'Download'),
    $report_format_message);
  echo '  <input type="submit" value="Run Report">';
  echo '  <input type="button" value="Cancel" onclick="window.location=\'/\';">';
  echo '</fieldset>';
  echo '</form>';   
  CEWritePageEnd();
}

function IsValid(
  $uid_year, &$message_year,
  $report_format, &$report_format_message){

  $message_year          = ''; 
  $report_format_message = '';
  
  if (empty($uid_year)){
      $message_year = 'Please select year';
  }

  if (empty($report_format)){
      $report_format_message = 'Please select a report format';
  }
  
  return (empty($year_message) and empty($report_format_message));
}

function GetReportDefinition(&$sql, &$title){
  $title = 'Full listing by year';
  $sql = 
    'select ' . 
    '  year, ' .
    '  state, ' .
    '  comp_name, ' .
    '  organisation, ' .
    '  mentor_first_name, ' .
    '  mentor_last_name, ' .
    '  div_name, ' .
    '  team_name, ' .
    '  team_member_first_name, ' .
    '  team_member_last_name ' .
    'from ' .
    '  v_team_member ' .
    'order by ' .
    '  year, ' .
    '  state, ' .
    '  comp_name, ' .
    '  div_name, ' .
    '  organisation, ' .
    '  team_name, ' .
    '  team_member_last_name, ' .
    '  team_member_first_name ';
}

function RunReport($con, $uid_year, $report_format){
  GetReportDefinition($sql, $report_name);
  
  if ($report_format == 'DOWNLOAD'){
    $file_name = UIDCompNameToFileName($con, $uid_year, $report_name);  
    QueryToCSVDownload($con, $sql, $file_name);
  } else {
    $title = $report_name;  
    QueryToPage($con, $sql, $title, $uid_year, $report_format);
  }
}
    
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }
    
 $action                = postFieldDefault('action'); 
 $uid_year              = postFieldDefault('uid_year'); 
 $report_format         = postFieldDefault('report_format'); 
 $message_year          = '';
 $message_format        = '';
 
 if (($action == 'execute') and IsValid($uid_year, $message_year, $report_format, $message_format)){
   RunReport($con, $uid_year, $report_format);
 } else{
   if (empty($report_format)){$report_format = 'BROWSER';} 
   WriteHTML($con, $uid_year, $message_year, $report_format, $message_format);
 }
      
?>