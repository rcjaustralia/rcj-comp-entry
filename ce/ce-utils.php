<?php

function ceExecSQL($con, $sql, $message = '')
{
  if (empty($message)){
    $message = $sql;
  }
  if (mysqli_query($con,$sql))
  {
    echo '<p>' . $message . '</p>';
  }
  else 
  {
    throw new Exception(
      '<p><b>Error running SQL statement</b></p>' .
      '<pre>' . $sql . '</pre>' .
      '<p><b>Message</b></p>' .
      '<pre>' . mysqli_error($con) . '</pre>');
  }  
}

function ceDropTable($con, $table)
{
  ceExecSQL($con, 'drop table if exists ' . $table, 'Dropped: ' . $table);
}

function ceDropView($con, $table)
{
  ceExecSQL($con, 'drop view if exists ' . $table, 'Dropped: ' . $table);
}

function ceNewUIDIfRequired(&$uid)
{
  if (empty($uid))
    $uid = strtoupper(uniqid('', false));
}

function ceLog($message){
  error_log($message, 0);
}

function CEHandleException($e, $back = '')
{
  error_log($e->getMessage(), 0);
  error_log($e->getMessage(), 1, 'peter.w.hinrichsen@gmail.com');
  echo '<html>';
  echo '<body>';
  echo '<p><b>Error on server:</b></p>';
    
  if (C_DEBUG == 'TRUE'){
    echo '<pre>' . $e->getMessage() . '</pre>';
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

// set_exception_handler('ceDefaultExceptionHandler');

function CECheckNotNull($value, &$return_message, $message)
{
  if (empty($value))
    $return_message = $message;
  else
    $return_message = '';
}

function CECheckMaxStrLength($value, $maxlen, &$return_message, $message)
{
  if (strlen($value) > $maxlen)
    $return_message = CEIIF(!empty($return_message), ' ', '') . $message;
}

function CEEchoP($text)
{
  echo '<p>' . $text . '</p>';
}

function CEEchoPage($text)
{
  echo '<html><p>' . $text . '</p></html>';
}

function CEIIF($Value, $ResultIfTrue, $ResultIfFalse)
{
  if ($Value)
    {return $ResultIfTrue;}	  
  else
    {return $ResultIfFalse;}	  
}

function CESendHTMLMail($to, $cc, $bcc, $from, $subject, $message)
{
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $headers .= 'From: ' . $from . "\r\n";
  if (!empty($cc)){
    $headers .= 'Cc: ' . $cc . "\r\n";
  }
  if (!empty($bcc)){
    $headers .= 'Bcc: ' . $bcc . "\r\n";
  }
  mail($to . "\r\n", $subject, $message, $headers);
}
  
/**
 * Function: sanitize
 * Returns a sanitized string, typically for URLs.
 *
 * Parameters:
 *     $string - The string to sanitize.
 *     $force_lowercase - Force the string to lowercase?
 *     $anal - If set to *true*, will remove all non-alphanumeric characters.
 */
function sanitize_file_name($string, $force_lowercase = true, $anal = false) {
    $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
    $clean = trim(str_replace($strip, "", strip_tags($string)));
    $clean = preg_replace('/\s+/', "-", $clean);
    $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
    return ($force_lowercase) ?
        (function_exists('mb_strtolower')) ?
            mb_strtolower($clean, 'UTF-8') :
            strtolower($clean) :
        $clean;
}  
  
?>