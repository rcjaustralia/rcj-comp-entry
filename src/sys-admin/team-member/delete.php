<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  try
  {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }

    $uid = postFieldDefault('uid');
    
    $query = $con->prepare('delete from team_member where uid = :uid');
    $query->bindParam(':uid', $uid);
    $result = $query->execute();
    header("location: /sys-admin/team-member");
    
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/team-member');
  }

?>
