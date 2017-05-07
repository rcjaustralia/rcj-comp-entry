<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function HasDependency($con, $uid)
  {
    $sql = 
      'select count(*) as count from team where uid_comp_division = :uid ';
    
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
    $uidReturnTo = postFieldDefault('uid_return_to');
    if (!HasDependency($con, $uid))
    {  
      $query = $con->prepare('delete from comp_division where uid = :uid');
      $query->bindParam(':uid', $uid);
      $result = $query->execute();
      header("location: /comp-admin#" . $uidReturnTo );
    } else {
      messageForm(
        $con,
        'Child records found',
        'Sorry, this competition division can not be deleted as it has associated entries.',
        array('Go back' => '/comp-admin#' . $uidReturnTo)
        );        
    }
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-admin#' . $uidReturnTo);
  }

?>