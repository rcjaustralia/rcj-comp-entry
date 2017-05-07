<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function HasDependency($con, $uid)
  {
    $sql = $con->prepare('select count(*) as count from comp_name where uid_state = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] > 0;
  }  

  try
  {
    if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
        exit(); //==>>
    }
    $uid = postFieldDefault('uid');
    
    if (HasDependency($con, $uid))
    {  
      throw new exception('Sorry, the state can not be deleted as it has associated competitions.'); 
    } 

    $sql = $con->prepare('delete from comp_state where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $result = $sql->execute();
    header("location: /sys-admin/comp-state");
    
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-state');
  }

?>
