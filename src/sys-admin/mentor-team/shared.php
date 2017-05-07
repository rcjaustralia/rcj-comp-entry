<?php
   function GetValuesFromPK($con, $uid, &$uid_user, &$uid_comp_name, &$organisation)
  {
    $sql = $con->prepare('select uid_user, uid_comp_name, organisation from mentor_team where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row            = $sql->fetch(PDO::FETCH_ASSOC);
    $uid_user       =  $row['uid_user'] ;
    $uid_comp_name  =  $row['uid_comp_name'] ;
    $organisation   =  $row['organisation'] ;
  }  
?>