<?php 
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  throw new Exception('A test exception that should be caught by the default exception handler.');
  echo '<html><p>Oops, looks like the exception was not handled.</p></html>';
?>