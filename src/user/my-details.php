<?php

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-bom.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/user/user-shared.php';
  
  function WriteHTML($con, $FormAction, $user)
  {
    CEWritePageHeader(C_SITE_TITLE, 'Update my details');
    WriteConnectUserDetails($con);
    CEWriteFormStart('Update my details', 'my-details', '/user/my-details.php');
    CEWriteFormAction($FormAction);
    CEWriteFormFieldHidden('uid', $user->uid);
    echo '<fieldset><legend>Required information</legend>';
    CEWriteFormFieldTextAutofocus('email', 'Email Address', $user->email, 245, $user->email_message);
    CEWriteFormFieldText('first_name', 'First name', $user->first_name, 60, $user->first_name_message);
    CEWriteFormFieldText('last_name', 'Last name', $user->last_name, 60, $user->last_name_message);
    CEWriteFormFieldText('primary_org', 'Organisation', $user->primary_org, 60, $user->primary_org_message);
    echo '</fieldset><p>';
    ceWriteSaveAndCancelButtons('/'); 
    writeHTMLRCJAMembership($user);
    ceWriteSaveAndCancelButtons('/'); 
    echo '<br><fieldset><legend>Optional information</legend>';
    writeAddressHTML($user);
    echo '</fieldset><p>';  
    // To Do: Go back to the previous page, not the main menu
    CEWriteFormEnd('/');
    CEWritePageEnd();
  }

  function Save(
    $con, $sql, $user)
  {
    $query = $con->prepare($sql);
    $query->bindParam(':uid',                $user->uid);
    $query->bindParam(':email',              $user->email);
    $query->bindParam(':first_name',         $user->first_name);
    $query->bindParam(':last_name',          $user->last_name);
    $query->bindParam(':primary_org',        $user->primary_org);
    $query->bindParam(':adrs_line_1',        $user->adrs_line_1);
    $query->bindParam(':adrs_line_2',        $user->adrs_line_2);
    $query->bindParam(':suburb',             $user->suburb);
    $query->bindParam(':postcode',           $user->postcode);
    $query->bindParam(':state',              $user->state);
    $query->bindParam(':rcja_member',        ceBoolToInt($user->rcja_member));
    $query->bindParam(':mailing_list',       ceBoolToInt($user->mailing_list));
    $query->bindParam(':share_with_sponsor', ceBoolToInt($user->share_with_sponsor));
    
    $result = $query->execute();
    header('location: /');
  }

  
 try
 {
    session_start();
    $action             = postFieldDefault('action');
    $user              = new rcjaUser();
    $user->uid                = $_SESSION['uid_logged_on_user'];
    $user->email              = postFieldDefault('email');
    $user->first_name         = postFieldDefault('first_name');
    $user->last_name          = postFieldDefault('last_name');
    $user->primary_org        = postFieldDefault('primary_org');
    $user->access_level       = '';
    $user->adrs_line_1        = postFieldDefault('adrs_line_1');
    $user->adrs_line_2        = postFieldDefault('adrs_line_2');
    $user->suburb             = postFieldDefault('suburb');
    $user->postcode           = postFieldDefault('postcode');
    $user->state              = postFieldDefault('state');
    $user->rcja_member        = postFieldDefault('rcja_member');
    $user->mailing_list       = postFieldDefault('mailing_list');
    $user->share_with_sponsor = postFieldDefault('share_with_sponsor');

    if (empty($action))
    {
      GetValuesFromPK($con, $user);
      WriteHTML($con, CE_UPDATE, $user);
    }
    else if ($action == CE_UPDATE)
    {
      if (validateUserUpdateSelf($con, $user))
      {
        Save($con, 'update user ' .
		           'set email              = :email,       ' .
                   '    first_name         = :first_name,  ' . 
                   '    last_name          = :last_name,   ' . 
                   '    primary_org        = :primary_org, ' . 
                   '    adrs_line_1        = :adrs_line_1, ' . 
                   '    adrs_line_2        = :adrs_line_2, ' . 
                   '    suburb             = :suburb,      ' . 
                   '    postcode           = :postcode,    ' . 
                   '    state              = :state,    ' . 
                   '    rcja_member        = :rcja_member,        ' . 
                   '    mailing_list       = :mailing_list,        ' . 
                   '    share_with_sponsor = :share_with_sponsor        ' . 
				   'where uid = :uid',
             $user);
      }
      else
      {
  
        WriteHTML($con, CE_UPDATE, $user);
      }    
    }
    else
    {
      throw new Exception('Invalid form action: "' . $action . '"'); 
    }
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/sys-admin/user');
  }  
?>