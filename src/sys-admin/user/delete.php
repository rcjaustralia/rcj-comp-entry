<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function HasDependency($con, $uid)
  {
    $sql = $con->prepare('
      select sum(count) as count from
      (
        select count(*) as count from mentor_team where uid_user = :uid
        union all
        select count(*) as count from comp_state where uid_treasurer = :uid
      ) t');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
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
      throw new exception('Sorry, this user can not be deleted as they have entered one or more teams, or they are marked as a RCJA state treasurer.'); 
    } 

    $sql = $con->prepare('delete from user where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $result = $sql->execute();
    header('location: /sys-admin/user');
    
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/user');
  }

?>