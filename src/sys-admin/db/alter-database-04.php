<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/connect-mysqli.php';

try {

  echo '<p><a href="../">Back to DB script index</a> | <a href="/">Home</a></p>';
  
  ceExecSQL($con, 'update user set rcja_member = 0'); 
  ceExecSQL($con, 'alter table user modify rcja_member bit not null');
  
  ceExecSQL($con, 'update user set mailing_list = 0'); 
  ceExecSQL($con, 'alter table user modify mailing_list bit not null');

  ceExecSQL($con, 'update user set share_with_sponsor = 0'); 
  ceExecSQL($con, 'alter table user modify share_with_sponsor bit not null');
    
} 

catch (Exception $e) 

{
    echo $e->getMessage();
}
  
  echo '<p><a href="./">Back to DB script index</a> | <a href="/">Home</a></p>';
  echo '</html>';
      
?>

