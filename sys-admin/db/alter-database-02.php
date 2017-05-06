<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/connect-mysqli.php';

try {

  echo '<p><a href="../">Back to DB script index</a> | <a href="/">Home</a></p>';


  // ceExecSQL(
    // $con, 
    // 'create table next_invoice_number (invoice_number varchar(5))',
    // 'Created: next_invoice_number');

  // ceExecSQL(
    // $con, 
    // 'insert into next_invoice_number (invoice_number) values ("00089")',
    // 'Default next_invoice_number');

  // ceExecSQL(
    // $con, 
    // 'alter table next_invoice_number modify invoice_number varchar(5) not null',
    // 'Index: Added nn constraint to next_invoice_number.invoice_number');

  ceExecSQL($con, 
    'alter table comp_name add event_date datetime null', 
     'Added comp_name.event_date column');

  ceExecSQL($con, 
    'update comp_name set event_date = end_date + 14', 
     'Assign field event_date');     
    
  ceExecSQL($con, 
    'alter table comp_name modify event_date datetime not null', 
     'Set event_date to not null');
     
  ceExecSQL($con, 
    'alter table comp_name
     add custom_message varchar(1000) null', 
     'Added comp_name.custom_message column');

	ceExecSQL($con, 
    'alter table comp_name
     add uid_enquiries_to varchar(13) null', 
     'Added comp_name.enquiries_to column');
 
	ceExecSQL($con, 
    'alter table comp_name
     add venue varchar(500) null', 
     'Added comp_name.venue column');

	ceExecSQL($con, 
    'alter table comp_name
     add invoice_address varchar(500) null', 
     'Added comp_name.invoice_address column');
  
  ceExecSQL($con, 
    'alter table comp_name
     add invoice_message varchar(1000) null', 
     'Added comp_name.invoice_message column');     
     
  ceExecSQL($con, 
    'alter table team_member add year_at_school varchar(7) null',     
    'Added team_member.year_at_school');

  ceExecSQL($con, 
    'alter table team_member add gender varchar(7) null',     
    'Added team_member.gender');
    
  ceExecSQL($con, 'update team_member set gender = "UNKNOWN", year_at_school = "UNKNOWN"');
  ceExecSQL($con, 'alter table team_member modify year_at_school varchar(7) not null');
  ceExecSQL($con, 'alter table team_member modify gender varchar(7) not null');

  ceExecSQL($con, 'alter table user add rcja_member bit null');  
  ceExecSQL($con, 'alter table user add mailing_list bit null');
  ceExecSQL($con, 'alter table user add share_with_sponsor bit null'); 
    
} 

catch (Exception $e) 

{
    echo $e->getMessage();
}
  
  echo '<p><a href="./">Back to DB script index</a> | <a href="/">Home</a></p>';
  echo '</html>';
      
?>