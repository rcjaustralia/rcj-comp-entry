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
    
    if (HasDependency($con, $uid))
    {  
      throw new exception('Sorry, this competition can not be deleted as it has associated competition divisions.'); 
    } 

    $query = $con->prepare('delete from comp_name where uid = :uid');
    $query->bindParam(':uid', $uid);
    $result = $query->execute();
    header("location: /sys-admin/comp-name");
    
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/comp-name');
  }

?>