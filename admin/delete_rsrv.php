<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "access_denied.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php require_once('../Connections/localhost.php'); ?>

<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

if ((isset($_POST['form1'])) && ($_POST['form1'] != "") && (isset($_GET['rsrv_id']))) {
  $deleteSQL = sprintf("DELETE FROM reservations WHERE rsrv_id=%s",
                       GetSQLValueString($_POST['form1'], "int"));

  mysql_select_db($database_localhost, $localhost);
  $Result1 = mysql_query($deleteSQL, $localhost) or die(mysql_error());

  $deleteGoTo = "records.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

mysql_select_db($database_localhost, $localhost);
$query_user = "SELECT * FROM `user`";
$user = mysql_query($query_user, $localhost) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);

$maxRows_records = 5;
$pageNum_records = 0;
if (isset($_GET['pageNum_records'])) {
  $pageNum_records = $_GET['pageNum_records'];
}
$startRow_records = $pageNum_records * $maxRows_records;

$colname_records = "-1";
if (isset($_GET['rsrv_id'])) {
  $colname_records = $_GET['rsrv_id'];
}
mysql_select_db($database_localhost, $localhost);
$query_records = sprintf("SELECT * FROM reservations WHERE rsrv_id = %s", GetSQLValueString($colname_records, "int"));
$query_limit_records = sprintf("%s LIMIT %d, %d", $query_records, $startRow_records, $maxRows_records);
$records = mysql_query($query_limit_records, $localhost) or die(mysql_error());
$row_records = mysql_fetch_assoc($records);

if (isset($_GET['totalRows_records'])) {
  $totalRows_records = $_GET['totalRows_records'];
} else {
  $all_records = mysql_query($query_records);
  $totalRows_records = mysql_num_rows($all_records);
}
$totalPages_records = ceil($totalRows_records/$maxRows_records)-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style/stylesheet.css" />
<link rel="stylesheet" type="text/css" media="all" href="style/includes/jquery/jquery-ui-custom.css" />
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet" />
<script src="style/includes/jquery/jquery-1.10.2.js"></script>
<script src="style/includes/jquery/jquery-ui-custom.js"></script>
<script src="style/includes/jquery/maskedinput.js"></script>
<script src="style/includes/bootstrap/js/bootstrap.js"></script>
<script>
  jQuery(document).ready(function($) {var dateToday = new Date();
var dates = $("#dateStart, #dateEnd").datepicker({
    defaultDate: "+1w",
	dateFormat: 'yy-mm-dd',
    changeMonth: true,
    numberOfMonths: 1,
    minDate: dateToday,
    onSelect: function(selectedDate) {
        var option = this.id == "dateStart" ? "minDate" : "maxDate",
            instance = $(this).data("datepicker"),
            date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker.settings.dateFormat, selectedDate, instance.settings);
        dates.not(this).datepicker("option", option, date);
   	 }
	});
});
  </script>
<script type="text/javascript">
	jQuery(function($){
   $("#rsrv_contact").mask("99999999999");
	});
	</script>
<title>Admin Panel - Hans Guest House</title>
</head>
<body>
<div class="top_container">
    	<span id="panel_name">Admin Panel</span>
<span id="user">Welcome, <?php echo $row_user['user_full']; ?><br/><a href="<?php echo $logoutAction ?>">Log out</a> </span></div>
<ul id="menu">
    	<li>
       	  <a href="admin_panel.php"><i class="fa fa-home"></i>  Home</a>
  </li>
    	<li>	
        	<a href="records.php"><i class="fa fa-list-alt"></i>  Records</a>
        </li>
        <li>
        	<a href="users.php"><i class="fa fa-users"></i>  User Management</a>
        </li>
    </ul>
<div id="container">
 <h2>Delete Record</h2>
<form method="POST" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">First Name:</td>
      <td><input name="rsrv_first_name" type="text" value="<?php echo $row_records['rsrv_first_name']; ?>" size="32" disabled="disabled" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Last Name:</td>
      <td><input name="rsrv_last_name" type="text" value="<?php echo $row_records['rsrv_last_name']; ?>" size="32" disabled="disabled" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email:</td>
      <td><input name="rsrv_email" type="text" value="<?php echo $row_records['rsrv_email']; ?>" size="32" disabled="disabled"/></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Contact Numbers:</td>
      <td><input name="rsrv_contact" type="text" value="<?php echo $row_records['rsrv_contact']; ?>" size="32" disabled="disabled" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Check In:</td>
      <td><input name="rsrv_start" type="text" id="dateStart" value="<?php echo $row_records['rsrv_start']; ?>" size="32" disabled="disabled" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Check Out:</td>
      <td><input name="rsrv_end" type="text" id="dateEnd" value="<?php echo $row_records['rsrv_end']; ?>" size="32" disabled="disabled" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Notes:</td>
      <td><textarea name="rsrv_notes" disabled="disabled"><?php echo $row_records['rsrv_notes']; ?></textarea></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Delete Record" /></td>
    </tr>
  </table>
  <input type="hidden" name="rsrv_id" value=<?php echo $row_records['rsrv_id']; ?> />
  <input type="hidden" name="rsrv_timestamp" />
</form>
 <p>&nbsp;</p>
</div>
</body>
</html>
<?php
mysql_free_result($user);

mysql_free_result($records);
?>
