<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function HasDependency($con, $uid)
  {
    $sql = 
      'select count(*) as count from team where uid_mentor_team = :uid ';
    
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
      throw new exception('Sorry, this mentor-team can not be deleted as it has associated entries.'); 
    } 

    $query = $con->prepare('delete from mentor_team where uid = :uid');
    $query->bindParam(':uid', $uid);
    $result = $query->execute();
    header("location: /sys-admin/mentor-team");
    
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/mentor-team');
  }

?>
