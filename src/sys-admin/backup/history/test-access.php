<?php

function getFilePermissions($fileName){
  $perms = fileperms($fileName);

  switch ($perms & 0xF000) {
      case 0xC000: // socket
          $info = 's';
          break;
      case 0xA000: // symbolic link
          $info = 'l';
          break;
      case 0x8000: // regular
          $info = 'r';
          break;
      case 0x6000: // block special
          $info = 'b';
          break;
      case 0x4000: // directory
          $info = 'd';
          break;
      case 0x2000: // character special
          $info = 'c';
          break;
      case 0x1000: // FIFO pipe
          $info = 'p';
          break;
      default: // unknown
          $info = 'u';
  }

  // Owner
  $info .= ' ';
  $info .= (($perms & 0x0100) ? 'r' : '-');
  $info .= (($perms & 0x0080) ? 'w' : '-');
  $info .= (($perms & 0x0040) ?
              (($perms & 0x0800) ? 's' : 'x' ) :
              (($perms & 0x0800) ? 'S' : '-'));

  // Group
  $info .= ' ';
  $info .= (($perms & 0x0020) ? 'r' : '-');
  $info .= (($perms & 0x0010) ? 'w' : '-');
  $info .= (($perms & 0x0008) ?
              (($perms & 0x0400) ? 's' : 'x' ) :
              (($perms & 0x0400) ? 'S' : '-'));

  // World
  $info .= ' ';
  $info .= (($perms & 0x0004) ? 'r' : '-');
  $info .= (($perms & 0x0002) ? 'w' : '-');
  $info .= (($perms & 0x0001) ?
              (($perms & 0x0200) ? 't' : 'x' ) :
              (($perms & 0x0200) ? 'T' : '-'));

  return $info;
}

?>


<html>
  <p>PHP Version is: "<?php echo phpversion(); ?>"</p>
  <p>The Apache user is: "<?php echo exec('whoami'); ?>"</p>
  <p>The current directory is: "<?php echo getcwd(); ?>"</p>
  <p>The file permissions are: "<?php echo getFilePermissions(getcwd()); ?>"</p>
  
  <?php
  try {
  
    $testReadFileName = getcwd() . '/read-test.txt';
    $testWriteFileName = getcwd() . '/write-test.txt';

    echo '<p>Test read file: "' . $testReadFileName . '</p>';
    echo '<p>Test write file: "' . $testWriteFileName . '</p>';
       
    if (file_exists($testWriteFileName)){
      echo '<p>Can not perform test. "' . $testWriteFileName . '" exists and should be deleted before continuing.';
      exit;
    }
    
    if (!file_exists($testReadFileName)){
      echo '<p>Can not perform test. "' . $testReadFileName . '" does not exists. Copy file to webserver deleted before continuing.';
      exit;
    }

    $fr = fopen($testReadFileName, "r");
    $length = 9;
    $ls = fread($fr, $length);
    
    echo '<p>Testing read access....</p>';
    if ($ls == 'test text'){
        echo '<p style="margin-left:30px">Read access IS set correctly.</p>';        
    } else {
        echo '<p style="margin-left:30px">Read access IS NOT set correctly.</p>';        
    }
    
    try {
      echo '<p>Testing write access #1....</p>';
      error_clear_last();
      $fw = fopen($testWriteFileName, "w");
      if(error_get_last()['message'] == ''){
        fwrite($fw, 'Cats chase mice');
        fclose($fw);
        if (file_exists($testWriteFileName)){
            unlink($testWriteFileName);
            echo '<p style="margin-left:30px">Write access IS set correctly.</p>';
        } else{
            echo '<p style="margin-left:30px">Write access IS NOT (a) set correctly.</p>';
        }
      } else {
          echo '<p style="margin-left:30px">Write access IS NOT (b) set correctly: "'. error_get_last()['message'] . '</p>';
      }
    } catch (Exception $e) {
        echo '<p style="margin-left:30px">Testing write access #1 failed: ',  $e->getMessage(), "</p>\n";
    }    
    
  } catch (Exception $e) {
      echo '<p>Caught exception: ',  $e->getMessage(), "</p>\n";
  }    
  
  ?>   
  
</html>  