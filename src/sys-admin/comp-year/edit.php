<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function UniqueCheck($con, $uid, $comp_year)
  {
    $sql = $con->prepare('select count(*) as count from comp_year where uid <> :uid and year = :year');
    $sql->bindParam(':uid', $uid);
    $sql->bindParam(':year', $comp_year);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    return $row['count'] == 0;
  }  

  function GetValuesFromPK($con, $uid, &$comp_year)
  {
    $sql = $con->prepare('select year from comp_year where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $comp_year =  $row['year'] ;
  }  

  function Validate($con, $uid, $comp_year, &$message)
  {
    $message = '';
    if (strlen($comp_year) <> 4 or (!ctype_digit($comp_year)))
    {  
      $message = 'Please enter a four digit year in the form "2015".'; 
    } 

    if (!UniqueCheck($con, $uid, $comp_year))
    {  
      $message = 'The year "' . $comp_year . '" already exists. Please enter a unique year.'; 
    }
    
    return empty($message); 
  
  }

  function WriteHTML($con, $Heading, $FormAction, $uid, $comp_year, $message)
  {
    CEWritePageHeader(C_SITE_TITLE, $Heading);
    WriteConnectUserDetails($con);
    CEWriteFormStart($Heading, 'comp-year-add', 'edit.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $uid);
    CEWriteFormFieldTextAutofocus('comp_year', 'Competition year', $comp_year, 4, $message);
    CEWriteFormEnd('/sys-admin/comp-year');
    CEWritePageEnd();
  }

  function Save($con, $sql, $uid, $comp_year)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid', $uid);
    $query->bindParam(':comp_year', $comp_year);
    $result = $query->execute();
    header('location: /sys-admin/comp-year');
  }

  
 try
 {
    if (!StartSessionConfirmPageAccess($con, C_SYS_ADMIN)){
        exit(); //==>>
    }

    $action = postFieldDefault('action');
    $uid = postFieldDefault('uid');
    $comp_year = trim(postFieldDefault('comp_year'));

    if ($action == CE_NEW)
    {
      WriteHTML($con,
        'Add a new competition year', CE_INSERT, '', 
        $comp_year, '');
    }
    else if ($action == CE_EDIT)
    {
      GetValuesFromPK($con, $uid, $comp_year);
      WriteHTML($con,
        'Edit a competition year', CE_UPDATE, $uid, 
        $comp_year, '');
    }
    else if ($action == CE_INSERT)
    {
      ceNewUIDIfRequired($uid);
      if (Validate($con, $uid, $comp_year, $message))
      {
        Save($con, 'insert into comp_year (uid, year) values (:uid, :comp_year)',
             $uid, $comp_year);
      }
      else
      {
        WriteHTML($con,
          'Add a new competition year', CE_INSERT, $uid, 
          $comp_year, $message);
      }
    }     
    else if ($action == CE_UPDATE)
    {
      if (Validate($con, $uid, $comp_year, $message))
      {
        Save($con, 'update comp_year set year = :comp_year where uid = :uid',
             $uid, $comp_year);
      }
      else
      {
        WriteHTML($con,
          'Add a new competition year', CE_UPDATE, $uid, 
          $comp_year, $message);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/comp-year');
  }  
?>