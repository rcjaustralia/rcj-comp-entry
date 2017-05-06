<?php

  function EmailPassword($email, $password){
    // ToDo: Requre exception handling
    $email_from = 'Robocup Junior Australia <peter@clubengineer.org>';
    $email_subject = 'Robocup Junior Australia | Password Reset'; 
    $url = 'http://'.$_SERVER['HTTP_HOST'];
    
    $message = '<html><body>' .
    $message .= '<h1>Robocup Junior Australia | Password Reset</h1>';
    $message .= '<p>Your password has been reset:</p>';
    $message .= '<p style="margin-left: 30px;">' . $password . '</p>';
    $message .= '<p>You can login using your new password here:</p>';
    $message .= '<p style="margin-left: 30px;"><a href="' . $url . '">' . $url . '</a></p>';
    $message .= '<p>Good luck with your entries in the next round of competition.</p>';
    $message .= '<p><b>Peter Hinrichsen</b><br>' .
                '<small>RCJA Online Entry Administrator<br>' .
                'peter@clubengineer.org</small></br></p>';

    $message .= '</body></html>';
              
    $to = $email . "\r\n";
    $subject = $email_subject;
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: ' . $email_from . "\r\n";

    mail($to, $subject, $message, $headers);

  }
  
  function RandomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789-!@#$%&";
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  } 
  
?>