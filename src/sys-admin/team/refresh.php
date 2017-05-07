<?php
  require '../shared/connect-pdo.php';
  require '../ce/ce-form-utils.php';
  require 'sql.php';
  
  $uid_mentor_team = $_GET['uid_mentor_team'];
  $sql = CompSivisionSQL($uid_mentor_team);
  CEWriteJSONArrayFromQuery($con, $sql);
  
?>