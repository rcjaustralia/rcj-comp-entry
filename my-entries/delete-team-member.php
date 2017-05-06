<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  try
  {
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }
    $uid = postFieldDefault('uid');
    $query = $con->prepare('delete from team_member where uid = :uid');
    $query->bindParam(':uid', $uid);
    $result = $query->execute();
    header("location: /my-entries/edit-comp.php");
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/my-entries/edit-comp.php');
  }

?>