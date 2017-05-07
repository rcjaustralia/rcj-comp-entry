<?php

    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function HasDependency($con, $uid)
  {
    $sql = 
      'select count(*) as count from team_member where uid_team = :uid ';    
    
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
      throw new exception('Sorry, this team can not be deleted as it has associated team members.'); 
    } 

    $query = $con->prepare('delete from team where uid = :uid');
    $query->bindParam(':uid', $uid);
    $result = $query->execute();
    header("location: /sys-admin/team");
    
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/team');
  }

?>