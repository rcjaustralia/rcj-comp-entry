<?php
  // The require below must be added before any require 'connect-pdo.php'
  // require 'server-settings.php';
  $con=mysqli_connect(C_DB_HOST, C_DB_USER_NAME, C_DB_PASSWORD, C_DB_NAME);
  if(mysqli_connect_errno()):
    echo "<p>Failed to connect to MySQL: " . mysqli_connect_error() . '</p>';
  endif;
?>