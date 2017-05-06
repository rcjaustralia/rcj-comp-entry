<?php
  // The require below must be added before any require 'connect-pdo.php'
  // require 'server-settings.php';
  
  $db_connection = 'mysql:dbname=' . C_DB_NAME . ';host=' . C_DB_HOST; 
  
  $con=new PDO($db_connection, C_DB_USER_NAME, C_DB_PASSWORD);
  $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if(mysqli_connect_errno()):
    echo "<p>Failed to connect to MySQL: " . mysqli_connect_error() . '</p>';
  endif;

?>