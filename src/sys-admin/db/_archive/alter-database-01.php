<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/connect-mysqli.php';

try {

  echo '<p><a href="../">Back to DB script index</a> | <a href="/">Home</a> | <a href="alter-database-01.php">Run again</a></p>';

  ceDropView($con,  'v_comp_division');
  ceDropView($con,  'v_team');
  ceDropView($con,  'v_team_member');
  ceDropTable($con, 'next_invoice_number');
  ceDropView($con,  'v_comp_state');
  
  ceExecSQL(
    $con, 
    'create view v_comp_division   ' .
    'as select                     ' .
    '  y.uid as uid_year,          ' .
    '  d.uid as uid_comp_division, ' .
    '  n.uid_comp_name,            ' .
    '  n.uid_state,                ' .
    '  y.year,                     ' .
    '  n.state,                    ' .
    '  n.comp_name,                ' .
    '  n.start_date,               ' .
    '  n.active,                   ' .
    '  n.end_date,                 ' .
    '  d.disp_order,               ' .
    '  d.div_name                  ' .
    'from                          ' . 
    '  comp_division   d           ' . 
    'right join v_comp_name n       ' .
    '  on d.uid_comp_name = n.uid_comp_name  ' . 
    'right join comp_year y                  ' .
    '  on n.uid_year = y.uid                ' . 
    'order by                      ' . 
    '  year,                       ' .
    '  state,                      ' .
    '  comp_name,                  ' .
    '  disp_order                  ',
    'Created: v_comp_division');

  ceExecSQL(
    $con, 
    'create view v_team                ' .
    'as select                         ' .
    '  t.uid,                          ' .
    '  c.uid_comp_name,                ' .
    '  cd.uid as uid_division,         ' .
    '  mt.uid_user,                    ' .
    '  c.year,                         ' . 
    '  c.state,                        ' . 
    '  c.comp_name,                    ' . 
    '  c.start_date,                   ' .
    '  c.end_date,                     ' .
    '  c.active,                       ' .
    '  mt.organisation,                ' .
    '  u.first_name as mentor_first_name, ' .
    '  u.last_name as mentor_last_name,   ' .
    '  u.email as mentor_email,           ' . 
    '  cd.disp_order as div_disp_order,   ' .
    '  cd.div_name,                       ' .
    '  t.team_name                     ' .
    'from                              ' . 
    '  team t                          ' .
    'left join comp_division cd        ' .
    '  on t.uid_comp_division = cd.uid ' . 
    'left join mentor_team mt          ' .
    '  on t.uid_mentor_team = mt.uid   ' . 
    'left join user u                  ' .
    '  on mt.uid_user = u.uid          ' . 
    'left join v_comp_name c           ' .
    '  on cd.uid_comp_name = c.uid_comp_name ' .
    'order by                          ' .
    '  year,                           ' . 
    '  state,                          ' . 
    '  comp_name,                      ' . 
    '  organisation,                   ' .
    '  mentor_first_name,              ' .
    '  mentor_last_name,               ' .
    '  div_disp_order,                 ' .
    '  team_name                       ',
    'Created: v_team');    
    
  ceExecSQL(
    $con, 
    'create view v_team_member            ' .
    'as select                            ' .
    '  tm.uid as uid_team_member,         ' .
    '  t.uid  as uid_team,                ' .
    '  mt.uid  as uid_mentor_team,        ' .
    '  cd.uid as uid_comp_division,       ' .
    '  c.uid_comp_name as uid_comp_name,  ' .
    '  u.uid as uid_mentor,               ' .
    '  c.year,                            ' . 
    '  c.state,                           ' . 
    '  c.comp_name,                       ' . 
    '  c.start_date,                      ' .
    '  c.end_date,                        ' .
    '  c.active,                          ' .
    '  mt.organisation,                   ' .
    '  u.first_name as mentor_first_name, ' .
    '  u.last_name as mentor_last_name,   ' .
    '  cd.div_name,                       ' .
    '  cd.disp_order as div_disp_order,   ' .
    '  t.team_name,                       ' .
    '  tm.first_name as team_member_first_name, ' .
    '  tm.last_name as team_member_last_name    ' .
    'from                                 ' . 
    '  team_member tm                     ' .
    'left join team t                     ' .
    '  on tm.uid_team = t.uid             ' . 
    'left join comp_division cd           ' .
    '  on t.uid_comp_division = cd.uid    ' . 
    'left join mentor_team mt             ' .
    '  on t.uid_mentor_team = mt.uid      ' . 
    'left join user u                     ' .
    '  on mt.uid_user = u.uid             ' . 
    'left join v_comp_name c              ' .
    '  on cd.uid_comp_name = c.uid_comp_name ' .
    'order by                        ' .
    '  year,                         ' . 
    '  state,                        ' . 
    '  comp_name,                    ' . 
    '  organisation,                 ' .
    '  mentor_first_name,            ' .
    '  mentor_last_name,             ' .
    '  div_disp_order,               ' .
    '  team_name,                    ' .
    '  team_member_last_name,        ' .
    '  team_member_first_name        ',
    'Created: v_team');

  ceExecSQL($con, 'update comp_division set disp_order = 1 where uid = "5604E59B1574B"', 'updated disp_order 01');
  ceExecSQL($con, 'update comp_division set disp_order = 2 where uid = "5604E5A6C3C69"', 'updated disp_order 02');
  ceExecSQL($con, 'update comp_division set disp_order = 3 where uid = "5604E5B30D18F"', 'updated disp_order 03');
  ceExecSQL($con, 'update comp_division set disp_order = 4 where uid = "5604E57263C6B"', 'updated disp_order 04');
  ceExecSQL($con, 'update comp_division set disp_order = 5 where uid = "5604E57FD1138"', 'updated disp_order 05');
  ceExecSQL($con, 'update comp_division set disp_order = 6 where uid = "5604E58DA4D7F"', 'updated disp_order 06');
  ceExecSQL($con, 'update comp_division set disp_order = 7 where uid = "5604E5C383E59"', 'updated disp_order 07');

  ceExecSQL($con, 'update comp_division set disp_order = 1 where uid = "566BD5179725E"', 'updated disp_order 08');
  ceExecSQL($con, 'update comp_division set disp_order = 2 where uid = "566BD522EF0D7"', 'updated disp_order 09');
  ceExecSQL($con, 'update comp_division set disp_order = 3 where uid = "566BD4EC33C70"', 'updated disp_order 10');
  ceExecSQL($con, 'update comp_division set disp_order = 4 where uid = "566BD4FE053BB"', 'updated disp_order 11');
  ceExecSQL($con, 'update comp_division set disp_order = 5 where uid = "566BD508BC9BD"', 'updated disp_order 12');
  ceExecSQL($con, 'update comp_division set disp_order = 6 where uid = "566BD531EFE93"', 'updated disp_order 13');
  ceExecSQL($con, 'update comp_division set disp_order = 7 where uid = "566BD5414F6C7"', 'updated disp_order 14');

  ceExecSQL($con, 'update comp_division set disp_order = 1 where uid = "57116998C2ADE"', 'updated disp_order 15');
  ceExecSQL($con, 'update comp_division set disp_order = 2 where uid = "571169A39E533"', 'updated disp_order 16');
  ceExecSQL($con, 'update comp_division set disp_order = 3 where uid = "571169AB9C68D"', 'updated disp_order 17');
  ceExecSQL($con, 'update comp_division set disp_order = 4 where uid = "571169B50E1B5"', 'updated disp_order 18');
  ceExecSQL($con, 'update comp_division set disp_order = 5 where uid = "571169BD2714F"', 'updated disp_order 19');
  ceExecSQL($con, 'update comp_division set disp_order = 6 where uid = "571169C734FFC"', 'updated disp_order 20');
  ceExecSQL($con, 'update comp_division set disp_order = 7 where uid = "571169D92EFB2"', 'updated disp_order 21');
  ceExecSQL($con, 'update comp_division set disp_order = 8 where uid = "5715FE6685F56"', 'updated disp_order 22');
  ceExecSQL($con, 'update comp_division set disp_order = 9 where uid = "571169E42D081"', 'updated disp_order 23');
  ceExecSQL($con, 'update comp_division set disp_order = 10 where uid = "571169F7770CE"', 'updated disp_order 24');
  ceExecSQL($con, 'update comp_division set disp_order = 11 where uid = "571169EEA0879"', 'updated disp_order 25');
  ceExecSQL($con, 'update comp_division set disp_order = 12 where uid = "571169FFE4046"', 'updated disp_order 26');

  ceExecSQL($con, 'update comp_division set disp_order = 1 where uid = "57116A22A0142"', 'updated disp_order 27');
  ceExecSQL($con, 'update comp_division set disp_order = 2 where uid = "57116A2C08411"', 'updated disp_order 28');
  ceExecSQL($con, 'update comp_division set disp_order = 3 where uid = "57116A374AE8C"', 'updated disp_order 29');
  ceExecSQL($con, 'update comp_division set disp_order = 4 where uid = "57116A40435FA"', 'updated disp_order 30');
  ceExecSQL($con, 'update comp_division set disp_order = 5 where uid = "57116A47E7BB9"', 'updated disp_order 31');
  ceExecSQL($con, 'update comp_division set disp_order = 6 where uid = "57116A4F60108"', 'updated disp_order 32');
  ceExecSQL($con, 'update comp_division set disp_order = 7 where uid = "57116A5A14234"', 'updated disp_order 33');
  ceExecSQL($con, 'update comp_division set disp_order = 8 where uid = "5715FE6E2BECC"', 'updated disp_order 34');
  ceExecSQL($con, 'update comp_division set disp_order = 9 where uid = "57116A63AEFA6"', 'updated disp_order 35');
  ceExecSQL($con, 'update comp_division set disp_order = 10 where uid = "57116A7173509"', 'updated disp_order 36');
  ceExecSQL($con, 'update comp_division set disp_order = 11 where uid = "57116A6A41ABA"', 'updated disp_order 37');
  ceExecSQL($con, 'update comp_division set disp_order = 12 where uid = "57116A7D6BB85"', 'updated disp_order 38');

  ceExecSQL($con, 'update comp_division set disp_order = 1 where uid = "57116A89AA6D9"', 'updated disp_order 39');
  ceExecSQL($con, 'update comp_division set disp_order = 2 where uid = "57116A90BB99B"', 'updated disp_order 40');
  ceExecSQL($con, 'update comp_division set disp_order = 3 where uid = "57116A9979BA9"', 'updated disp_order 41');
  ceExecSQL($con, 'update comp_division set disp_order = 4 where uid = "57116AA6CA3CC"', 'updated disp_order 42');
  ceExecSQL($con, 'update comp_division set disp_order = 5 where uid = "57116AB6C24FD"', 'updated disp_order 43');
  ceExecSQL($con, 'update comp_division set disp_order = 6 where uid = "57116AAEAB4D2"', 'updated disp_order 44');
  ceExecSQL($con, 'update comp_division set disp_order = 7 where uid = "57116ABDE1E2E"', 'updated disp_order 45');

  ceExecSQL($con, 'update comp_division set disp_order = 1 where uid = "57116ACA1A6BD"', 'updated disp_order 46');
  ceExecSQL($con, 'update comp_division set disp_order = 2 where uid = "57116AD32F35E"', 'updated disp_order 47');
  ceExecSQL($con, 'update comp_division set disp_order = 3 where uid = "57116ADBC3E99"', 'updated disp_order 48');
  ceExecSQL($con, 'update comp_division set disp_order = 4 where uid = "57116AE2AAA2D"', 'updated disp_order 49');
  ceExecSQL($con, 'update comp_division set disp_order = 5 where uid = "5715FE7746182"', 'updated disp_order 50');

  ceExecSQL($con, 'update comp_division set disp_order = 1 where uid = "57116AF42339D"', 'updated disp_order 51');
  ceExecSQL($con, 'update comp_division set disp_order = 2 where uid = "57116AFC1810D"', 'updated disp_order 52');
  ceExecSQL($con, 'update comp_division set disp_order = 3 where uid = "57116B028E157"', 'updated disp_order 53');
  ceExecSQL($con, 'update comp_division set disp_order = 4 where uid = "57116B0A2EAC4"', 'updated disp_order 54');
  ceExecSQL($con, 'update comp_division set disp_order = 5 where uid = "57116B1147EA7"', 'updated disp_order 55');
  ceExecSQL($con, 'update comp_division set disp_order = 6 where uid = "57116B17AF89A"', 'updated disp_order 56');
  ceExecSQL($con, 'update comp_division set disp_order = 7 where uid = "57116B1FE77BC"', 'updated disp_order 57');
  ceExecSQL($con, 'update comp_division set disp_order = 8 where uid = "57116B2866DE9"', 'updated disp_order 58');
  ceExecSQL($con, 'update comp_division set disp_order = 9 where uid = "57116B321EA8A"', 'updated disp_order 59');
  ceExecSQL($con, 'update comp_division set disp_order = 10 where uid = "57116B3F705BE"', 'updated disp_order 60');
  ceExecSQL($con, 'update comp_division set disp_order = 11 where uid = "57116B38CE443"', 'updated disp_order 61');
  ceExecSQL($con, 'update comp_division set disp_order = 12 where uid = "57116B4602DA5"', 'updated disp_order 62');
  
	ceExecSQL($con, 
    'alter table mentor_team
     add invoice_number varchar(5) null unique key', 
     'Added mentor_team.invoice_number column');

	ceExecSQL($con, 
    'alter table comp_state
     add uid_treasurer varchar(13) null', 
     'Added comp_state.uid_treasurer column');
  
	ceExecSQL($con, 
    'alter table comp_state add account_name varchar(100) null', 
     'Added comp_state.account_name column');

	ceExecSQL($con, 
    'alter table comp_state add account_bsb varchar(6) null', 
     'Added comp_state.bsb column');

	ceExecSQL($con, 
    'alter table comp_state add account_number varchar(9) null', 
     'Added comp_state.account_number column');     
        
	ceExecSQL($con, 
    'alter table user
     add adrs_line_1 varchar(100) null', 
     'Added user.adrs_line_1 column');

	ceExecSQL($con, 
    'alter table user
     add adrs_line_2 varchar(100) null', 
     'Added user.adrs_line_2 column');

	ceExecSQL($con, 
    'alter table user
     add suburb varchar(100) null', 
     'Added user.suburb column');
     
	ceExecSQL($con, 
    'alter table user
     add postcode varchar(4) null', 
     'Added user.postcode column');
     
	ceExecSQL($con, 
    'alter table user
     add state varchar(3) null', 
     'Added user.state column');

  ceExecSQL(
    $con, 
    'insert into user 
       (uid, email, first_name, last_name, primary_org, access_level, password_hash) 
     values 
       ("5733F959ED453", 
        "treasurer@rcja.org.au", 
        "Treasurer", 
        "Robocup Junior Australia", 
        "Robocup Junior Australia",  
        "COMP_ADMIN", 
        "ae6c08463b6567092277a90a0e6868a69982b0c5")',
    'insert treasurer@rcja.org.au into user');    

  ceExecSQL($con, 
    'update comp_state set 
       uid_treasurer = "5733F959ED453"',
    'Default comp_state.treasurer');

  ceExecSQL($con, 
    'update comp_state set 
       uid_treasurer = "55A272D3AB66A",
       account_name = "RoboCupJunior Australia Inc. (Victoria)",
       account_bsb  = "063834",
       account_number = "10177846"
     where 
       state = "VIC"',
    'Set treasurer of Vic to Tommo');
     
  ceExecSQL(
    $con, 
    'alter table comp_state modify uid_treasurer varchar(13) not null',
    'Index: Added nn constraint to comp_state.uid_treasurer');
   
  ceExecSQL(
    $con, 
    'alter table comp_state add constraint fk_comp_state_treasurer ' .
    'foreign key (uid_treasurer) ' .
    'references user(uid) ',
    'Index: fk_comp_state_treasurer');    
    
  ceExecSQL($con, 
    'update user set 
       adrs_line_1 = "117 Clifton Springs Rd",
       suburb = "Drysdale",
       state  = "VIC",
       postcode = "3222"
     where 
       uid = "55A272D3AB66A"',
    'Update Tommos address');
     
	ceExecSQL($con, 
    'alter table comp_name
     add entry_fee numeric(6,2) null', 
     'Added comp_name.entry_fee column');
    
	ceExecSQL($con, 
    'update comp_name set entry_fee = 25',
    'Set entry_fee of all comp_name(s) to 25.00');
    
	ceExecSQL($con, 
    'update comp_name set entry_fee = 60 where uid = "571168A4C7BB2"',
    'Set entry_fee of the Victorian state comp to  to 60.00');
    
  ceExecSQL(
    $con, 
    'alter table comp_name modify entry_fee numeric(6,2) not null',
    'Index: Added nn constraint to comp_name.entry_fee');
    
  ceExecSQL(
    $con, 
    'create table next_invoice_number (invoice_number varchar(5))',
    'Created: next_invoice_number');

  ceExecSQL(
    $con, 
    'insert into next_invoice_number (invoice_number) values ("00001")',
    'Default next_invoice_number');

  ceExecSQL(
    $con, 
    'alter table next_invoice_number modify invoice_number varchar(5) not null',
    'Index: Added nn constraint to next_invoice_number.invoice_number');
         
  ceExecSQL(
    $con, 
    'drop trigger if exists trg_user_access_level_insert',
    'drop trigger trg_user_access_level_insert');

  ceExecSQL(
    $con, 
    'create trigger trg_user_access_level_insert before insert on user ' . 
    '  for each row ' . 
    '  begin ' .  
    '    if (new.access_level <> "MENTOR") and ' . 
    '       (new.access_level <> "COMP_ADMIN") and ' .
    '       (new.access_level <> "SYS_ADMIN") and ' .
    '       (new.access_level <> "SYS_DEV") then ' .
    '       SIGNAL SQLSTATE "45000" ' .   
    '       SET MESSAGE_TEXT = "Invalid access_level"; ' .
    '    end if; ' . 
    '  end; ', 
    'Created constraint');
    
  ceExecSQL(
    $con, 
    'drop trigger if exists trg_user_access_level_update',
    'drop trigger trg_user_access_level_update');

  ceExecSQL(
    $con, 
    'create trigger trg_user_access_level_update before update on user ' . 
    '  for each row ' . 
    '  begin ' .  
    '    if (new.access_level <> "MENTOR") and ' . 
    '       (new.access_level <> "COMP_ADMIN") and ' .
    '       (new.access_level <> "SYS_ADMIN") and ' .
    '       (new.access_level <> "SYS_DEV") then ' .
    '       SIGNAL SQLSTATE "45000" ' .   
    '       SET MESSAGE_TEXT = "Invalid access_level"; ' .
    '    end if; ' . 
    '  end; ', 
    'Created constraint');  
  
  ceExecSQL(
    $con, 
    'insert into user 
       (uid, email, first_name, last_name, primary_org, access_level, password_hash) 
     values 
       ("0000000000019", 
        "sys.dev@clubengineer.org", 
        "Developer", 
        "Test User", 
        "Club Engineer",  
        "SYS_DEV", 
        "ae6c08463b6567092277a90a0e6868a69982b0c5")',
    'insert developer@clubengineer.org into user');    
    
  ceExecSQL(
    $con, 
    'create view v_comp_state as
     (
       select 
         s.uid,
         s.state,
         u.first_name as treasurer_first_name,
         u.last_name as treasurer_last_name,
         u.email as treasurer_email,
         u.primary_org as treasurer_primary_org,
         u.adrs_line_1 as treasurer_adrs_line_1,
         u.adrs_line_2 as treasurer_adrs_line_2,
         u.suburb as treasurer_suburb,
         u.postcode as treasurer_postcode,
         u.state as treasurer_state,
         s.account_name,
         s.account_bsb,
         s.account_number
       from 
         comp_state s
       left join user u on u.uid = s.uid_treasurer)',
     'View v_comp_state created');  


     
} 

catch (Exception $e) 

{
    echo $e->getMessage();
}
  
  echo '<p><a href="../">Back to DB script index</a> | <a href="/">Home</a> | <a href="alter-database-01.php">Run again</a></p>';
  echo '</html>';
      
?>