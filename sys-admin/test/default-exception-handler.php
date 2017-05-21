<?php 
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  throw new Exception('Testing the default exception handler');
  echo "<html><p>Not Executed\n</p></html>";

?>