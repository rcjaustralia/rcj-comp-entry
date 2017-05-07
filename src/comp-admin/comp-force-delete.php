<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  try
  {
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }

    $uid = getFieldDefault('uid');
    $uidReturnTo = getFieldDefault('uid_return_to');
        
    $sql = 
      'delete from team_member where uid_team in (select uid from team where uid_mentor_team in (select uid from mentor_team where uid_comp_name = :uid)); ' .
      'delete from team where uid_mentor_team in (select uid from mentor_team where uid_comp_name = :uid); ' .
      'delete from mentor_team where uid_comp_name = :uid; ' . 
      'delete from comp_division where uid_comp_name = :uid; ' .
      'delete from comp_name where uid = :uid;';
        
    $query = $con->prepare($sql);
    $query->bindParam(':uid', $uid);
    $result = $query->execute();
    header("location: /comp-admin#" . $uidReturnTo);
      
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-admin#' . $uidReturnTo);
  }

?>