<?php

  // ToDo: Require confirmation before deleting all the entries in a comp
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  try
  {
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

	$uid_user = $_SESSION['uid_logged_on_user'];
    $uid_comp_name = postFieldDefault('uid');
    $query = $con->prepare(
      'delete from team_member where uid_team in ' .
      '(select uid from team where uid_mentor_team in ' .
      '(select uid from mentor_team where uid_user = :uid_user and uid_comp_name = :uid_comp_name)); ' .
      'delete from team where uid_mentor_team in ' .
      '(select uid from mentor_team where uid_user = :uid_user and uid_comp_name = :uid_comp_name); ' .
      'delete from mentor_team where uid_user = :uid_user and uid_comp_name = :uid_comp_name');
    $query->bindParam(':uid_user', $uid_user);
    $query->bindParam(':uid_comp_name', $uid_comp_name);
    $result = $query->execute();

    header('location: /my-entries');
   
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/my-entries');
  }

?>