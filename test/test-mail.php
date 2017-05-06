<?php
  
  echo '<html><p>Testing PHP mail(): ' ;
  $result = mail('peter.w.hinrichsen@gmail.com', 'Testing PHP mail', 'Testing PHP Mail', 'from:peterh@enter-preprod.rcj.org.au');
  if ($result){
    echo 'Passed';
  } else{
    echo 'Failed';
  }
  echo '</p></html>';
  
?>