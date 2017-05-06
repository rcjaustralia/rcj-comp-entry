<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function HasDependency($con, $uid)
  {
    $sql = 
      'select sum(count) as count from ' .
      '(select count(*) as count from comp_division where uid_comp_name = :uid ' .
      'union ' . 
      'select count(*) as count from mentor_team where uid_comp_name = :uid) t';    
    
    $query = $con->prepare($sql);
    $query->bindParam(':uid', $uid);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    return $row['count'] > 0;
  }  

  try
  {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }

    $uid = postFieldDefault('uid');
    if (empty($uid)){
        $uid = getFieldDefault('uid');
    }
    
    $uidReturnTo = postFieldDefault('uid_return_to');
    if (empty($uidReturnTo)){
        $uidReturnTo = getFieldDefault('uid_return_to');
    }

    $backupDownloadFile = getFieldDefault('backupDownloadFile');
    $backupDownloadText = getFieldDefault('backupDownloadText');
   
   
    if (!HasDependency($con, $uid)){        
      $query = $con->prepare('delete from comp_name where uid = :uid');
      $query->bindParam(':uid', $uid);
      $result = $query->execute();
      header("location: /comp-admin#" . $uidReturnTo);

    } else {  

      if (empty($backupDownloadFile)){
        $backupMessage =         
          'You may want to make a copy of the database before deleting by clicking ' .
          '<a href="/sys-admin/backup/backup.php?redirectTo=/comp-admin/comp-delete.php?uid=' . $uid .
          '&uid_return_to=' . $uidReturnTo .
          '">here</a>.';
      } else {
        $backupMessage = 'The database has been backed up to ' . 
        '<a href="javascript:cePost(\'/sys-admin/backup/download.php\', {\'file-name\': \'' . $backupDownloadText . '\'})">' . $backupDownloadText . '</a>.<br>' .
        'Before deleting, you may want to download a copy of this backup file from ' .
        '<a href="javascript:cePost(\'/sys-admin/backup/download.php\', {\'file-name\': \'' . $backupDownloadText . '\'})">here</a>.';  
      } 
      messageForm(
        $con,
        'Warning: Child records found',
        'This competition has associated competition divisions, and perhaps entries.<br><br>' . 
        'If you select "Delete", the action can not be undone.<br><br>' .
        $backupMessage,
        array('Take me back to safety' => '/comp-admin#' . $uidReturnTo, 
              'I know what I\'m doing. Delete the comp!' => 
                '/comp-admin/comp-force-delete.php?uid=' . $uid .
                '&uid_return_to=' . $uidReturnTo)
        );        
    }  
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-admin#' . $uidReturnTo);
  }

?>