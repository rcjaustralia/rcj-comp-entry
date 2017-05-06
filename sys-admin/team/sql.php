<?php

function CompSivisionSQL($uid_mentor_team)
{
  return
    'select ' .
    '  uid_comp_division as uid, ' .
    '  concat(year, " - ", state, " - ", comp_name, " (", div_name, ")") as display ' .
    'from ' .
    '  v_comp_division ' .
    'where ' .
    '  uid_comp_name in (SELECT uid_comp_name FROM mentor_team where uid = "' . $uid_mentor_team . '")';		 
}

?>