<?php
  session_start();
  $_SESSION['uid_logged_on_user'] = '';
  $_SESSION['display_text_logged_on_user'] = '';
  header('location: /');
  exit();  
?>
