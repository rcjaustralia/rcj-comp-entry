<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function getDispOrderThis($con, $uidThis, &$dispOrderThis){
    $sql = 
      'select disp_order from comp_division where uid = :uid'; 
    $query = $con->prepare($sql);
    $query->bindParam(':uid', $uidThis);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    $dispOrderThis = $row['disp_order'];
  }
    
  function getSwapWith($con, $direction, $uidThis, &$uidSwapWith, &$dispOrderSwapWith){
    $side = CEIIF(($direction == 'UP'), '<', '>');
    $orderBy = CEIIF(($direction == 'UP'), 'desc', 'asc'); 
    
    $sql = 
      'select 
         uid, disp_order 
       from 
             comp_division where uid_comp_name = (select uid_comp_name from comp_division where uid = :uid)
         and disp_order ' . $side . ' (select disp_order from comp_division where uid = :uid)
       order by disp_order ' . $orderBy;

    $query = $con->prepare($sql);
    $query->bindParam(':uid', $uidThis);
    $query->execute();
    $row = $query->fetch(PDO::FETCH_ASSOC);
    if ($query->rowCount() > 0){
      $uidSwapWith = $row['uid'];
      $dispOrderSwapWith = $row['disp_order'];
      return true;
    } else {
      return false;
    }  
  }
    
  function update($con, $uid, $disp_order)
  {
    $query = $con->prepare('update comp_division set disp_order = :disp_order where uid = :uid');
    $query->bindParam(':uid',           $uid);
    $query->bindParam(':disp_order',    $disp_order);
    $result = $query->execute();
  }
  
  try
  {
  
    if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
        exit(); //==>>
    }
    
    $uidThis     = postFieldDefault('uid');
    $uidReturnTo = postFieldDefault('uid_return_to');
    $direction   = postFieldDefault('direction');

    $dispOrderThis     = '';
    $uidSwapWith       = '';
    $dispOrderSwapWith = '';
    
    getDispOrderThis($con, $uidThis, $dispOrderThis);
    if (getSwapWith($con, $direction, $uidThis, $uidSwapWith, $dispOrderSwapWith)){
      update($con, $uidThis, $dispOrderSwapWith);
      update($con, $uidSwapWith, $dispOrderThis);
    }
    header('location: /comp-admin#' . $uidReturnTo);    
        
  }
  catch (Exception $e)
  {
    CEHandleException($e, '/comp-admin#' . $uidReturnTo);
  }

?>