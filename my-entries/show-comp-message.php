<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  // If this requires extension, use the rcjaComp object
  function GetValuesFromPK($con, $uid, &$comp_name, &$comp_message)
  {
    $sql = $con->prepare('select comp_name, custom_message from comp_name where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row              = $sql->fetch(PDO::FETCH_ASSOC);
    $comp_name    =  $row['comp_name'] ;
    $comp_message =  $row['custom_message'] ;
  }  

  
try
 {
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

    $uid_comp_name     = postFieldDefault('uid_comp_name');
    GetValuesFromPK($con, $uid_comp_name, $comp_name, $comp_message);
    messageForm(
      $con, 
      'Competition message for "' . $comp_name . '"', 
      $comp_message, 
      array('Go back'=>'/'));
  } 
  catch (Exception $e)
  {
    CEHandleException($e, '/my-entries/edit-comp.php');
  }  
  
?>