<?php

function formatBacktrace()
{
    $result = '';

    foreach (debug_backtrace() as $trace)
    {
        if ($trace['function'] ==__FUNCTION__)
            continue;

            $parameters = '';
            foreach ($trace['args'] as $parameter)
                $parameters .= $parameter . ', ';

                if (substr($parameters, -2) == ', ')
                    $parameters = substr($parameters, 0, -2);

                    if (array_key_exists('class', $trace))
                        $result .= sprintf("%s:%s %s::%s(%s)" . PHP_EOL, $trace['file'], $trace['line'],  $trace['class'], $trace['function'],  $parameters);
                    else // ToDo: Tidy up - there are times when file and line are not found
                        $result .= sprintf("%s:%s %s(%s)" . PHP_EOL, $trace['file'], $trace['line'], $trace['function'], $parameters);
    }

    return $result;
}

function formatExceptionMessage($e){
  $message = formatBacktrace() . PHP_EOL;
  
  //$message = $e->getTraceAsString() . PHP_EOL;
  if (isset($_SESSION['uid_logged_on_user'])){
    $uid_logged_on_user = $_SESSION['uid_logged_on_user'];
  } else {
    session_start();
    $uid_logged_on_user = $_SESSION['uid_logged_on_user'];    
  }
  $message .= 'User UID: "' . $uid_logged_on_user . '"'; 
  return $message;
}

function CEHandleException($e, $back = '')
{
  $exceptionMessage = formatExceptionMessage($e);
  error_log($exceptionMessage, 0) ;   
  error_log($exceptionMessage, 1, 'peter.w.hinrichsen@gmail.com');
  error_log($exceptionMessage, 1, ' evan@baileyfinance.com.au');

  echo '<html>';
  echo '<body>';
  echo '<h1>Error on server:</h1>';
  echo '<p>We are sorry. Something has gone wrong on the server. This should not happen and is probably the result of a programming error we have made.</p>';
  echo '<p>An email has been automatically sent with details of the error to the developer of the system (Peter).</p>';
  echo '<p>Could you please do one thing to help us track down the problem and prevent it from happening again?</p>';
  echo '</p>Please send an email to <a href="mailto://peter@clubengineer.org">peter@clubengineer.org</a> describing what you were doing when the error occurred.</p>';
  echo '<p>Many thanks you for your help and patience.</p>';
  echo '<p><b>Peter</b></p>';
  
  
  if (C_DEBUG == 'TRUE'){
    echo '<hr><pre>' . $exceptionMessage . '</pre>';
  }
  echo '<p>';
  if ($back != ''){
    echo '<a href="' . $back . '">Back</a> | ';
  }
  
  echo '<a href="\">Home</a></p>';
  echo '</body>';
  echo '</html>';
}

function ceDefaultExceptionHandler($e)
{
  CEHandleException($e);
}

set_exception_handler('ceDefaultExceptionHandler');


?>