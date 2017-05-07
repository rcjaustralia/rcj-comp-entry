<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function HasDependency($con, $uid)
  {
    $sql = 'select count(*) as count from comp_name where uid_year = :uid';    
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
   
    if (!HasDependency($con, $uid)){        
      $query = $con->prepare('delete from comp_year where uid = :uid');
      $query->bindParam(':uid', $uid);
      $result = $query->execute();
      header("location: /comp-admin");

    } else {  

      messageForm(
        $con,
        'Warning: Child records found',
        'This year has associated competitions, and perhaps entries.<br><br>' . 
        'Before deleting this year, please delete the assoicated competitions.<br><br>',
        array('Back' => '/comp-admin')
        );        
    }  
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-admin');
  }

?>