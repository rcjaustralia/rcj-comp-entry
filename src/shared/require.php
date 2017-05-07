<?php

  $incDirShared = $_SERVER["DOCUMENT_ROOT"]. "/shared/";
  $incDirCE     = $_SERVER["DOCUMENT_ROOT"]. "/ce/";
  
  require $incDirShared . 'server-settings.php';
  require $incDirCE     . 'ce-constants.php';
  require $incDirCE     . 'ce-utils.php';
  require $incDirCE     . 'ce-form-utils.php';
  require $incDirShared . 'connect-pdo.php';
  require $incDirShared . 'constants.php';
  require $incDirShared . 'user-utils.php';
  require $incDirShared . 'form-utils.php';
?>