<?php
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';
  require $_SERVER["DOCUMENT_ROOT"] . '/shared/connect-mysqli.php';

try {
  
  echo '<p><a href="./">Back to DB script index</a> | <a href="/">Home</a> | <a href="/recreate-database-00.php">Run again</a></p>';

  ceDropView($con,  'v_local_time');
  ceDropView($con,  'v_team_member');
  ceDropView($con,  'v_team');
  ceDropView($con,  'v_mentor_team');
  ceDropView($con,  'v_comp_division');
  ceDropView($con,  'v_comp_name');

  ceDropTable($con, 'team_member');
  ceDropTable($con, 'team');
  ceDropTable($con, 'mentor_team');
  ceDropTable($con, 'comp_division');
  ceDropTable($con, 'comp_name');
  ceDropTable($con, 'comp_state');
  ceDropTable($con, 'comp_year');
  ceDropTable($con, 'user');

  // Might want to allow for day light savings time corrections, but hey, what's an hour....
  ceExecSQL(
    $con, 
    'create view v_local_time as ' .
    'select CONVERT_TZ(UTC_TIMESTAMP(), "+00:00", "+10:00") as now from dual',
	'Created: v_local_time');  
  
  // Competition Year
  ceExecSQL(
    $con, 
    'create table comp_year ' .
    '(' .
    '  uid varchar(13) not null primary key, ' . 
    '  year varchar(4) unique key' .
    ')',
    'Created: comp_year');

  ceExecSQL(
    $con, 
    'insert into comp_year ' .
    '(uid, year) values ("0000000002015", "2015")',
    'Inserted: 2016');

  // Competition States
  ceExecSQL(
    $con, 
    'create table comp_state ' .
    '(' .
    '  uid varchar(13) not null primary key, ' . 
    '  state varchar(3) unique key' .
    ')',
    'Created: comp_state');

  ceExecSQL(
    $con, 
    'insert into comp_state ' .
    '(uid, state) values ("0000000000002", "VIC")',
    'Inserted: VIC');
  ceExecSQL(
    $con, 
    'insert into comp_state ' .
    '(uid, state) values ("0000000000003", "TAS")',
    'Inserted: TAS');
  ceExecSQL(
    $con, 
    'insert into comp_state ' .
    '(uid, state) values ("0000000000004", "SA")',
    'Inserted: SA');
  ceExecSQL(
    $con, 
    'insert into comp_state ' .
    '(uid, state) values ("0000000000005", "WA")',
    'Inserted: WA');
  ceExecSQL(
    $con, 
    'insert into comp_state ' .
    '(uid, state) values ("0000000000006", "QLD")',
    'Inserted: QLD');
  ceExecSQL(
    $con, 
    'insert into comp_state ' .
    '(uid, state) values ("0000000000007", "NSW")',
    'Inserted: NSW');
  ceExecSQL(
    $con, 
    'insert into comp_state ' .
    '(uid, state) values ("0000000000008", "ACT")',
    'Inserted: ACT');

  // Competition Name
  ceExecSQL(
    $con, 
    'create table comp_name ' .
    '(' .
    '  uid        varchar(13) not null primary key, ' . 
    '  uid_year   varchar(13) not null,             ' .
    '  uid_state  varchar(13) not null,             ' .
    '  comp_name  varchar(60) not null,             ' .
    '  start_date datetime    not null,             ' .
    '  end_date   datetime    not null              ' .
    ')',
    'Created: comp_name');

  ceExecSQL(
    $con, 
    'create unique index uk_comp_name on comp_name ' .
    '(uid_year, uid_state, comp_name)',
    'Index: uk_comp_name');

  ceExecSQL(
    $con, 
    'alter table comp_name add constraint fk_comp_name_year ' .
    'foreign key (uid_year) ' .
    'references comp_year(uid) ',
    'Index: fk_comp_name_year');

  ceExecSQL(
    $con, 
    'alter table comp_name add constraint fk_comp_name_state ' .
    'foreign key (uid_state) ' .
    'references comp_state(uid) ',
    'Index: fk_comp_name_state');

  ceExecSQL(
    $con, 
    'create view v_comp_name   ' .
    'as select                 ' .
    '  n.uid as uid_comp_name, ' .
    '  n.uid_year,             ' .
    '  n.uid_state,            ' .
    '  n.start_date,           ' .
    '  n.end_date,             ' .
    '  if(DATE(n.start_date) <= (select now from v_local_time) and (select now from v_local_time) < DATE_ADD(DATE(n.end_date), INTERVAL 1 DAY), 1, 0) as active, '.
    '  y.year,                 ' . 
    '  s.state,                ' . 
    '  n.comp_name             ' . 
    'from                      ' . 
    '  comp_name   n           ' . 
    'left join comp_state s    ' .
    '  on n.uid_state = s.uid  ' . 
    'left join comp_year y     ' .
    '  on n.uid_year = y.uid   ' . 
    'order by                  ' . 
    '  year,                   ' .
    '  state,                  ' .
    '  comp_name               ',
    'Created: v_comp_name');

  ceExecSQL(
    $con, 
    'insert into comp_name ' .
    '(uid, uid_year, uid_state, comp_name, start_date, end_date) values ("0000000000009", "0000000002015", "0000000000002", "Melbourne Regional", "2015-06-01", "2015-07-22")',
    'Inserted: 2015-Melbourne Regional');
  ceExecSQL(
    $con, 
    'insert into comp_name ' .
    '(uid, uid_year, uid_state, comp_name, start_date, end_date) values ("0000000000010", "0000000002015", "0000000000002", "Ballarat Regional", "2015-06-01", "2015-07-29")',
    'Inserted: 2015-Ballarat Regional');
  ceExecSQL(
    $con, 
    'insert into comp_name ' .
    '(uid, uid_year, uid_state, comp_name, start_date, end_date) values ("0000000000011", "0000000002015", "0000000000002", "Victorian State", "2015-07-01", "2015-08-19")',
    'Inserted: 2015-Victorian State');
	
  // Competition Divisions
  ceExecSQL(
    $con, 
    'create table comp_division ' .
    '(' .
    '  uid           varchar(13)  not null primary key, ' . 
    '  uid_comp_name varchar(13)  not null,             ' .
    '  disp_order    tinyint      not null,             ' .
    '  div_name      varchar(60)  not null              ' .
    ')',
    'Created: comp_division');

  ceExecSQL(
    $con, 
    'create unique index uk_comp_division on comp_division ' .
    '(uid_comp_name, div_name)',
    'Index: uk_comp_division');

  ceExecSQL(
    $con, 
    'alter table comp_division add constraint fk_comp_division_comp_name ' .
    'foreign key (uid_comp_name) ' .
    'references comp_name(uid) ',
    'Index: fk_comp_division_comp_name');

  ceExecSQL(
    $con, 
    'create view v_comp_division   ' .
    'as select                     ' .
    '  d.uid as uid_comp_division, ' .
    '  d.uid_comp_name,            ' .
    '  n.uid_state,                ' .
    '  y.year,                     ' .
    '  s.state,                    ' .
    '  n.comp_name,                ' .
    '  n.start_date,               ' .
    '  n.active,                   ' .
    '  n.end_date,                 ' .
    '  d.div_name                  ' .
    'from                          ' . 
    '  comp_division   d           ' . 
    'left join v_comp_name n       ' .
    '  on d.uid_comp_name = n.uid_comp_name  ' . 
    'left join comp_state s        ' .
    '  on n.uid_state = s.uid      ' . 
    'left join comp_year y         ' .
    '  on n.uid_year = y.uid       ' . 
    'order by                      ' . 
    '  year,                       ' .
    '  state,                      ' .
    '  comp_name,                  ' .
    '  disp_order                  ',
    'Created: v_comp_division');

// 09 = Melbourne
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A01", "0000000000009", 1, "Primary Dance")',
    'Inserted: Riley Rover Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A02", "0000000000009", 2, "Secondary Dance")',
    'Inserted: Primary Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A03", "0000000000009", 3, "Open Dance")',
    'Inserted: Secondary Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A04", "0000000000009", 4, "Riley Rover Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A05", "0000000000009", 5, "Primary Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A06", "0000000000009", 6, "Secondary Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A07", "0000000000009", 7, "Open Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A08", "0000000000009", 8, "Simple Simon Soccer")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A09", "0000000000009", 9, "Gen II SOccer")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A10", "0000000000009", 10, "Lightweight Soccer (Up to 1.1kg)")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000A11", "0000000000009", 11, "Open Soccer (Up to 2.5kg)")',
    'Inserted: Open Rescue');

// 10 = Ballarat
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B01", "0000000000010", 1, "Primary Dance")',
    'Inserted: Riley Rover Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B02", "0000000000010", 2, "Secondary Dance")',
    'Inserted: Primary Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B03", "0000000000010", 3, "Open Dance")',
    'Inserted: Secondary Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B04", "0000000000010", 4, "Riley Rover Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B05", "0000000000010", 5, "Primary Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B06", "0000000000010", 6, "Secondary Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B07", "0000000000010", 7, "Open Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B08", "0000000000010", 8, "Simple Simon Soccer")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B09", "0000000000010", 9, "Gen II SOccer")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B10", "0000000000010", 10, "Lightweight Soccer (Up to 1.1kg)")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000B11", "0000000000010", 11, "Open Soccer (Up to 2.5kg)")',
    'Inserted: Open Rescue');
    
// 11 = Victorian
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C01", "0000000000011", 1, "Primary Dance")',
    'Inserted: Riley Rover Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C02", "0000000000011", 2, "Secondary Dance")',
    'Inserted: Primary Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C03", "0000000000011", 3, "Open Dance")',
    'Inserted: Secondary Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C04", "0000000000011", 4, "Riley Rover Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C05", "0000000000011", 5, "Primary Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C06", "0000000000011", 6, "Secondary Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C07", "0000000000011", 7, "Open Rescue")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C08", "0000000000011", 8, "Simple Simon Soccer")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C09", "0000000000011", 9, "Gen II SOccer")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C10", "0000000000011", 10, "Lightweight Soccer (Up to 1.1kg)")',
    'Inserted: Open Rescue');
  ceExecSQL(
    $con, 
    'insert into comp_division ' .
    '(uid, uid_comp_name, disp_order, div_name) values ("0000000000C11", "0000000000011", 11, "Open SOccer (Up to 2.5kg)")',
    'Inserted: Open Rescue');
    
// Users
  ceExecSQL(
    $con, 
    'create table user ' .
    '(' .
    '  uid           varchar(13)   not null primary key, ' . 
    '  email         varchar(254)  not null unique key,  ' .
    '  password_hash varchar(40)   not null,             ' .
    '  first_name    varchar(60)   not null,             ' .
    '  last_name     varchar(60)   not null,             ' .
    '  primary_org   varchar(60),                        ' .
    '  access_level  varchar(60)   not null              ' .
    ')',
    'Created: user');

  ceExecSQL(
    $con, 
    'create unique index uk_user on user ' .
    '(email)',
   'Index: uk_user');

  // Check constraint
  ceExecSQL(
    $con, 
    'create trigger trg_user_access_level_insert before insert on user ' . 
    '  for each row ' . 
    '  begin ' .  
    '    if (new.access_level <> "MENTOR") and ' . 
    '       (new.access_level <> "COMP_ADMIN") and ' .
    '       (new.access_level <> "SYS_ADMIN") then ' .
    '       SIGNAL SQLSTATE "45000" ' .   
    '       SET MESSAGE_TEXT = "Invalid access_level"; ' .
    '    end if; ' . 
    '  end; ', 
    'Created constraint');
  ceExecSQL(
    $con, 
    'create trigger trg_user_access_level_update before update on user ' . 
    '  for each row ' . 
    '  begin ' .  
    '    if (new.access_level <> "MENTOR") and ' . 
    '       (new.access_level <> "COMP_ADMIN") and ' .
    '       (new.access_level <> "SYS_ADMIN") then ' .
    '       SIGNAL SQLSTATE "45000" ' .   
    '       SET MESSAGE_TEXT = "Invalid access_level"; ' .
    '    end if; ' . 
    '  end; ', 
    'Created constraint');

  ceExecSQL(
    $con, 
    'insert into user ' .
    '(uid, email, password_hash, first_name, last_name, primary_org, access_level) ' .
    'values ' .
    '("0000000000016", "sys.admin@clubengineer.org", "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8", "Sys Admin", "Test User", "Club Engineer", "SYS_ADMIN")',
    'Inserted: sys.admin@clubengineer.org');

  ceExecSQL(
    $con, 
    'insert into user ' .
    '(uid, email, password_hash, first_name, last_name, primary_org, access_level) ' .
    'values ' .
    '("0000000000017", "comp.admin@clubengineer.org", "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8", "Comp Admin", "Test User", "Club Engineer", "COMP_ADMIN")',
    'Inserted: comp.admin@clubengineer.org');

  ceExecSQL(
    $con, 
    'insert into user ' .
    '(uid, email, password_hash, first_name, last_name, primary_org, access_level) ' .
    'values ' .
    '("0000000000018", "mentor@clubengineer.org", "5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8", "Mentor", "Test User", "Club Engineer", "MENTOR")',
    'Inserted: mentor@clubengineer.org');

  // Mentor Teams
  ceExecSQL(
    $con, 
    'create table mentor_team ' .
    '(' .
    '  uid             varchar(13)  not null primary key, ' . 
    '  uid_user        varchar(13)  not null,             ' .
    '  uid_comp_name   varchar(13)  not null,             ' .
    '  organisation    varchar(60)  not null              ' .
    ')',
    'Created: mentor_team');

    // ToDo: Allow one mentor to represent multiple organisations
   ceExecSQL(
    $con, 
    'create unique index uk_mentor_team on mentor_team ' .
    '(uid_user, uid_comp_name)',
    'Index: uk_mentor_team');
	// ceExecSQL(
    // $con, 
    // 'create unique index uk_mentor_team on mentor_team ' .
    // '(uid_user, uid_comp_name, organisation)',
   // 'Index: uk_mentor_team');
   
  ceExecSQL(
    $con, 
    'alter table mentor_team add constraint fk_mentor_user ' .
    'foreign key (uid_user) ' .
    'references user(uid) ',
    'Index: fk_mentor_user');

  ceExecSQL(
    $con, 
    'alter table mentor_team add constraint fk_mentor_comp_name ' .
    'foreign key (uid_comp_name) ' .
    'references comp_name(uid) ',
   'Index: fk_mentor_comp_name');

  ceExecSQL(
    $con, 
    'create view v_mentor_team     ' .
    'as select                     ' .
    '  mt.uid,                     ' . 
    '  mt.uid_user,                ' .
    '  mt.uid_comp_name,           ' .
    '  mt.organisation,            ' .
    '  c.year,                     ' . 
    '  c.state,                    ' . 
    '  c.comp_name,                ' . 
    '  c.start_date,               ' .
    '  c.end_date,                 ' .
    '  c.active,                   ' .
    '  u.first_name,               ' .
    '  u.last_name                 ' .
    'from                          ' . 
    '  mentor_team  mt             ' . 
    'left join user u              ' .
    '  on mt.uid_user = u.uid      ' . 
    'left join v_comp_name c       ' .
    '  on mt.uid_comp_name = c.uid_comp_name ' .
    'order by                      ' .
    '  year,                       ' . 
    '  state,                      ' . 
    '  comp_name,                  ' . 
    '  primary_org,                ' .
    '  first_name,                 ' .
    '  last_name                   ',
    'Created: v_mentor_team');

  // ceExecSQL(
    // $con, 
    // 'insert into mentor_team ' .
    // '(uid, uid_user, uid_comp_name, organisation) ' .
    // 'values ' .
    // '("0000000000019", "0000000000018", "0000000000009", "Wesley College")',
    // 'Inserted: Caroline Ferguson / Melbourne Regional');

  // Team table
  ceExecSQL(
    $con, 
    'create table team ' .
    '(' .
    '  uid               varchar(13)  not null primary key, ' . 
    '  uid_mentor_team   varchar(13)  not null,             ' .
    '  uid_comp_division varchar(13)  not null,             ' .
    '  team_name         varchar(60)  not null              ' .
    ')',
    'Created: team');

  ceExecSQL(
    $con, 
    'create unique index uk_team on team ' .
    '(uid_comp_division, team_name)',
   'Index: uk_team');
 
  ceExecSQL(
    $con, 
    'alter table team add constraint fk_team_mentor_team ' .
    'foreign key (uid_mentor_team) ' .
    'references mentor_team(uid) ',
   'Index: fk_team_mentor_team');
   
    ceExecSQL(
    $con, 
    'alter table team add constraint fk_team_comp_division ' .
    'foreign key (uid_comp_division) ' .
    'references comp_division(uid) ',
   'Index: fk_team_comp_division');
   
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
    '  div_name,                       ' .
    '  team_name                       ',
    'Created: v_team');

  // ceExecSQL(
    // $con, 
    // 'insert into team ' .
    // '(uid, uid_mentor_team, uid_comp_division, team_name) values ("0000000000021", "0000000000020", "0000000000014", "TMO-343")',
    // 'Inserted: TMO-343');

  // Student team_member
  ceExecSQL(
    $con, 
    'create table team_member ' .
    '(' .
    '  uid           varchar(13) not null primary key, ' . 
    '  uid_team      varchar(13) not null,             ' .
    '  first_name    varchar(60) not null,             ' .
    '  last_name     varchar(60) not null              ' .
    ')',
    'Created: team_member');

  ceExecSQL(
    $con, 
    'alter table team_member add constraint fk_team_member_team ' .
    'foreign key (uid_team) ' .
    'references team(uid) ',
    'Index: fk_team_member_team');
    
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
    '  div_name,                     ' .
    '  team_name,                    ' .
    '  team_member_last_name,        ' .
    '  team_member_first_name        ',
    'Created: v_team');

  // ceExecSQL(
    // $con, 
    // 'insert into team_member ' .
    // '(uid, uid_team, first_name, last_name) values ("0000000000025", "0000000000021", "Harry", "at Club Engineer")',
    // 'Inserted: TMO-343');
	
} 

catch (Exception $e) 

{
    echo $e->getMessage();
}
 
  //$con->close;
  
  echo '<p><a href="./">Back to DB script index</a> | <a href="/">Home</a> | <a href="/recreate-database-00.php">Run again</a></p>';
  echo '</html>';
      
?>