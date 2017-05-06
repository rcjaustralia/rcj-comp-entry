<?php

  function GetUserDetails($con, $uid_user, &$display_text, &$primary_org, &$access_level)
  {
	$sql = $con->prepare('select first_name, last_name, primary_org, access_level from user where uid = :uid');
    $sql->bindParam(':uid', $uid_user);
    $sql->execute();
    $row          = $sql->fetch(PDO::FETCH_ASSOC);
    $display_text = $row['first_name'] . ' ' . $row['last_name']; 
    $primary_org  = $row['primary_org']; 
    $access_level = $row['access_level']; 
  }
  
  function CanAccessPage($con, $uid_user, $page_access_level){
    $display_text = '';
    $primary_org  = ''; 
    $access_level = '';
    $result       = false;
    GetUserDetails($con, $uid_user, $display_text, $primary_org, $access_level);
    
    if (($access_level == C_SYS_DEV) and 
        (($page_access_level == C_SYS_DEV) or
         ($page_access_level == C_SYS_ADMIN) or
         ($page_access_level == C_COMP_ADMIN) or
         ($page_access_level == C_MENTOR))) {
             $result = true;
         }
    else if (($access_level == C_SYS_ADMIN) and 
        (($page_access_level == C_SYS_ADMIN) or
         ($page_access_level == C_COMP_ADMIN) or
         ($page_access_level == C_MENTOR))) {
             $result = true;
         }
    else if (($access_level == C_COMP_ADMIN) and 
             (($page_access_level == C_COMP_ADMIN) or
              ($page_access_level == C_MENTOR))) {
             $result = true;
         }
    else if (($access_level == C_MENTOR) and 
             (($page_access_level == C_MENTOR))) {
             $result = true;
         }
    else {
        $result = false;
    }
    return $result;
  }
  
  function StartSessionConfirmPageAccess($con, $page_access_level){
    session_start();
    $uid_logged_on_user = $_SESSION['uid_logged_on_user'];
    if (!CanAccessPage($con, $uid_logged_on_user, $page_access_level)){
      header('location: /page-not-found.htm');
      return false;  
    }      
    else {
      return true;
    }
  }
  
  function WriteConnectUserDetails($con){

    $uid_logged_on_user = $_SESSION['uid_logged_on_user'];
    $display_text_logged_on_user = '';
    $primary_org_logged_on_user  = '';
    $access_level_logged_on_user = '';

    GetUserDetails($con, $uid_logged_on_user, 
      $display_text_logged_on_user, 
  	  $primary_org_logged_on_user, 
	    $access_level_logged_on_user);
  
    echo '<p>You are logged in as <a href="/user/my-details.php">' . htmlspecialchars($display_text_logged_on_user) . '</a>' .
         ' | <a href="/user/my-password.php">Change password</a>' .
         ' | <a href="/login/logout.php">Log out</a></p>';
      
      
  }
 
?>