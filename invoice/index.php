<?php

  require $_SERVER["DOCUMENT_ROOT"] . '/shared/require.php';

  function GetValuesFromPKMentor($con, $uid, &$mentorName, &$mentorOrg)
  {
    $sql = $con->prepare('select first_name, last_name, primary_org from user where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row         = $sql->fetch(PDO::FETCH_ASSOC);
    $mentorName =  $row['first_name'] . ' ' . $row['last_name'] ;
    $mentorOrg  =  $row['primary_org'] ;
  }  
  
  function GetValuesFromPKComp($con, $uid, &$compName, &$rate, &$invoice_message, &$invoice_address, &$entry_closing_date)
  {
    $sql = $con->prepare('select comp_name, entry_fee, invoice_message, invoice_address, end_date as entry_closing_date from comp_name where uid = :uid');
    $sql->bindParam(':uid', $uid);
    $sql->execute();
    $row                = $sql->fetch(PDO::FETCH_ASSOC);
    $compName           =  $row['comp_name'];
    $rate               =  $row['entry_fee'];
    $invoice_message    =  $row['invoice_message'];
    $invoice_address    =  $row['invoice_address'];
    $entry_closing_date =  date('d-M-Y', strtotime($row['entry_closing_date']));
  }  

  function GetInvoiceDetails($con, $uidCompName, 
    &$accountName, &$accountNumber, &$accountBSB, &$stateTreasurerEmail, &$stateTreasurerAddress){

    $sql = $con->prepare('
      select 
        treasurer_first_name, treasurer_last_name, 
        treasurer_email, 
        treasurer_adrs_line_1, treasurer_adrs_line_2,
        treasurer_suburb, treasurer_postcode, treasurer_state,        
        account_name, account_bsb, 
        account_number 
      from 
        v_comp_state 
      where 
        uid in (select uid_state from comp_name where uid = :uid)');
    $sql->bindParam(':uid', $uidCompName);
    $sql->execute();
    $row             = $sql->fetch(PDO::FETCH_ASSOC);

    if (empty($row) or empty($row['account_name']) or empty($row['account_bsb']) or 
        empty($row['account_number']) or empty($row['treasurer_email']) or empty($row['treasurer_first_name']) or
        empty($row['treasurer_last_name']) or empty($row['treasurer_adrs_line_1']) or empty($row['treasurer_suburb']) or
        empty($row['treasurer_state']) or empty($row['treasurer_postcode'])){
      throw new exception('Sorry, There was a problem generating your invoice at this time. <br><br>Some of the background data that must be setup by the system administrator is missing.'); 
    }  

    $accountName   = $row['account_name'];
    $accountBSB    = $row['account_bsb'];
    $accountNumber = $row['account_number'];
    $stateTreasurerEmail   = $row['treasurer_email'];
    $stateTreasurerAddress = $row['treasurer_first_name'] . ' ' . $row['treasurer_last_name'] . '<br>';
    $stateTreasurerAddress = $stateTreasurerAddress . $row['treasurer_adrs_line_1'] . '<br>';
    if (!empty($row['treasurer_adrs_line_2'])){
      $stateTreasurerAddress = $stateTreasurerAddress . $row['treasurer_adrs_line_2'] . '<br>';      
    }
    $stateTreasurerAddress = 
      $stateTreasurerAddress . $row['treasurer_suburb'] . '&nbsp;&nbsp;' . 
      $row['treasurer_state'] . '&nbsp;&nbsp;' . 
      $row['treasurer_postcode'];      
  }

  function getNextInvoiceNumber($con){
    $sql = $con->prepare('select invoice_number from next_invoice_number');
    $sql->execute();
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $result = $row['invoice_number'];
    $next = sprintf('%05d', $result + 1);
    $sql = $con->prepare('update next_invoice_number set invoice_number = :invoice_number');
    $sql->bindParam(':invoice_number', $next);
    $sql->execute();
    return $result;    
  }
  
  function updateInvoiceNumber($con, $uidUser, $uidCompName, $invoiceRef){
    $sql = $con->prepare('
      update mentor_team set invoice_number = :invoice_number
      where uid_user = :uid_user and uid_comp_name = :uid_comp_name'); 
    $sql->bindParam(':uid_user', $uidUser);
    $sql->bindParam(':uid_comp_name', $uidCompName);
    $sql->bindParam(':invoice_number', $invoiceRef);
    $sql->execute();
  }
  
  function GetInvoiceRef($con, $uidUser, $uidCompName, &$invoiceRef){
    
    // Try reading the InvoiceRef from the mentor_team table
    $sql = $con->prepare('
      select invoice_number from mentor_team 
      where uid_user = :uid_user and uid_comp_name = :uid_comp_name'); 
    $sql->bindParam(':uid_user', $uidUser);
    $sql->bindParam(':uid_comp_name', $uidCompName);
    $sql->execute();
    $row             = $sql->fetch(PDO::FETCH_ASSOC);
    
    if (empty($row) or empty($row['invoice_number'])){
      $invoiceRef = getNextInvoiceNumber($con);
      updateInvoiceNumber($con, $uidUser, $uidCompName, $invoiceRef);
    } else {
      $invoiceRef = $row['invoice_number'];
    }
  }

  
  function getInvoiceQuerySQL($uidUser, $uidCompName){
    return 
      'select 
        div_name, 
        count(uid) as entry_count 
       from 
         v_team 
       where 
             uid_comp_name = "' . $uidCompName . '" 
         and uid_user = "' . $uidUser . '" 
       group by 
         div_name 
       order by 
         div_disp_order';    
  }
 
  try{
    if (!StartSessionConfirmPageAccess($con, C_MENTOR)){
        exit(); //==>>
    }

    $uidUser     = getFieldDefault('uid_user');
    $uidCompName = getFieldDefault('uid_comp_name');
           
    $invoiceQueryResult = $con->query(getInvoiceQuerySQL($uidUser, $uidCompName));
    $countOfEntries = 0;

    GetValuesFromPKComp($con, $uidCompName, $compName, $rate, $invoice_message, $invoice_address, $entry_closing_date);
    GetValuesFromPKMentor($con, $uidUser, $mentorName, $mentorOrg);
    GetInvoiceDetails($con, $uidCompName, $accountName, $accountNumber, $accountBSB, $stateTreasurerEmail, $stateTreasurerAddress);
    GetInvoiceRef($con, $uidUser, $uidCompName, $invoiceRef);
    $invoice_message = str_replace('[INVOICE_REF]', $invoiceRef, $invoice_message);
    $invoice_message = str_replace('[ENTRY_CLOSING_DATE]', $entry_closing_date, $invoice_message);
    
    date_default_timezone_set('Australia/Melbourne');
    $invoiceDate = date('d/m/Y');
  }    
  catch (Exception $e)
  {
    CEHandleException($e, '/');
    exit;
  }  
// 297, 270, 260  
?>

<html>
<head>
  <title>RCJA Invoice</title>
  <!-- <link rel="stylesheet" type="text/css" href="/shared/style.css"> -->
  <!-- A height: property of 259mm will cause two pages to be printed. 
       258mm will result in just 1 page. Tested on Chrome 50.0.2661.102 m.
       Perhpas output to PDF will be more reliable.-->
  <style>
    html,body{
      height:258mm;
      width:180mm;
      font-family: Verdana, Geneva, sans-serif;
      margin: 0px 15px 15px 0px;
    }

    p {
        font-size: 12px;
        line-height: 160%;
    }
    
    table {
	    font-size: 12px;
      text-align:left;
  }

  td, th{
    padding: 5px 15px 5px 15px;
  }    
  .simple_border {
	    border-collapse: collapse;
	    border: 1px solid black;
    }
    
  </style>
</head>
<body>
  <!-- Robocup Address -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="45%">
        <img src="/images/rcja-logo-170x140.png" width="170" height="140" alt="RCJA Logo">
      </td>
      <td width="13%">&nbsp;</td>
      <td width="42%">
        RoboCupJunior Australia<br>ABN: 72 592 462 493<br><br>
        <?php echo $invoice_address;?>
      </td>
    </tr>
  </table>

  <!-- Mentor address -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="58%">
        <strong>Invoice To: </strong><br>
        <?php echo $mentorName; ?><br>
        <?php echo $mentorOrg; ?>
      </td>
      <td width="42%">
        <strong>Date: </strong><?php echo $invoiceDate ?> <br>
          <strong>Invoice Reference: </strong> <?php echo $invoiceRef; ?> 
      </td>
    </tr>
  </table>
  <hr>
  
  <!-- Invoice payment message -->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <?php echo $invoice_message;?>
      </td>
    </tr>
  </table>
  <p></p>
  
  <!-- Invoice table of data -->
  <table width="100%" border="1" cellspacing="0" cellpadding="0" class="simple_border">
    <tr>
      <th>Division</th>
      <th align="right">Entries</th>
      <th align="right">Rate</th>
      <th align="right">Line Total</th>
    </tr>
    <tr>
      <td colspan="4"><strong><?php echo $compName;?></strong></td>
    </tr>
    <?php 
      foreach($invoiceQueryResult as $row){
			  $countOfEntries = $countOfEntries + $row[1];
			  echo '<tr>
              <td>' . $row['div_name'] . '</td>';
			  echo '<td align="right">' . $row['entry_count'] . '</td>';
			  echo '<td align="right">$ ' . number_format($rate, 2) . '</td>';
			  echo '<td align="right">$ ' . number_format($rate*$row['entry_count'], 2) . '</td>';
			  echo '</tr>';
      }
      // Writing blank invoice rows....
      // 15 Rows will cause a single page invoice to be output. 
      // Tested on Chrome 50.0.2661.102 m.
      // 16 Rows will result in a messy multi page invoice
      for ($i=$countOfEntries; $i<=15; $i++){
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';  
      }
        
    ?>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><strong>Total:</strong></td>
      <td align="right">$ <strong><?php echo number_format($rate*$countOfEntries, 2)?></strong></td>
    </tr>
  </table>
</body>
</html>
