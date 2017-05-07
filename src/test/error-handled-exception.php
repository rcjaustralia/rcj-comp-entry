<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  try
    {
      throw new Exception('Testing a handled exception'); 
    }
  
  catch (Exception $e)
  {
    CEHandleException($e, '/');
  } 
 
  
?>
