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
    $redirectTo = postFieldDefault('redirectTo', '/sys-admin/comp-division');
    if (HasDependency($con, $uid))
    {  
      throw new exception('Sorry, this competition division can not be deleted as it has associated entries.'); 
    } 

    $query = $con->prepare('delete from comp_division where uid = :uid');
    $query->bindParam(':uid', $uid);
    $result = $query->execute();
    header("location: " . $redirectTo);
    
  }
  catch (Exception $e)
  {
    CEHandleException($e, $redirectTo);
  }

?>