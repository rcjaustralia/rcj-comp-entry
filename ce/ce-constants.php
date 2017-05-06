<?php
  // NEW: Called when a user clicks a <New> link. 
  // Draws the form with "Submit" button pointing 
  // back to the same form, but with ACTION = INSERT
  define("CE_NEW",    "NEW");
  
  // EDIT: Called when a user clicks a <Edit> link. 
  // Draws the form with "Submit" button pointing 
  // back to the same form, but with ACTION = UPDATE
  define("CE_EDIT",   "EDIT");

  // INSERT: Called from a <New> form when the user
  // clicks "Submit". 
  define("CE_INSERT", "INSERT");

  // UPDATE: Called from an <Edit> form when the user
  // clicks "Submit". 
  define("CE_UPDATE", "UPDATE");
  
?>