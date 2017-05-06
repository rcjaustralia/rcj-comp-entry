<?php
    require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  
  function writeServerFileList(){
    echo '<table border="1">
          <tr>
          <th>File name</th>
          <th>Action</th>
          </tr>';
		  
	// Sort Order constants became available in PHP 5.4.0
	if (defined('SCANDIR_SORT_DESCENDING')) {
	  $sortOrder = SCANDIR_SORT_DESCENDING;}
	else {
	  $sortOrder = 1;
	}
	
  $fileList = scandir('./history', $sortOrder);
	foreach ($fileList as $key => $filename){
    if (!in_array($filename, array('.', '..', 'default.php', 'test-access.php', 'read-test.txt'))){
  	 echo '<tr>';
	    echo '<td>' . basename($filename) . '</td>';
		  echo '<td><small>';
      echo '<a href="javascript:cePost(\'download.php\', {\'file-name\': \'' . $filename . '\'})">Download</a> | ';
      echo '<a href="javascript:cePost(\'restore.php\', {\'file-name\': \'' . $filename . '\'})">Restore</a> | ';
      echo '<a href="javascript:cePost(\'delete.php\', {\'file-name\': \'' . $filename . '\'})">Delete</a>';
		  echo '</small></td>';
		  echo '</tr>';
	  }	
  }	
    echo '</table>';		  
}

  function writeUploadAndRestoreHTML(){
	echo '<form action="restore.php" method="post" enctype="multipart/form-data">
	      <fieldset><legend>Select a file to restore from</legend>
          <input type="hidden" name="upload" id="upload" value="true">
          <p><input type="file" name="file-name" id="file-name"></p>
          <p><input type="submit" value="Restore database to this file" name="submit"></p>
		  </fieldset>
          </form>';		
  }

  
  if (!StartSessionConfirmPageAccess($con, C_COMP_ADMIN)){
      exit(); //==>>
  }

  $action = postFieldDefault('action');
  
  
  WriteConnectUserDetails($con);
  CEWritePageHeader(C_SITE_TITLE, 'Backup and restore database');
  
  echo '<h1>Backup and restore database</h1>';
  echo '<h2>Backup database</h2>';
  echo '<p class="indent"><a href="backup.php">Backup database now</a></p>';
  
  echo '<h2>List of backup files on server</h2>';
  writeServerFileList();
  
  echo '<h2>Upload and restore from another file</h2>';
  writeUploadAndRestoreHTML();
  
  CEWritePageFooter();
  
?>

</html>