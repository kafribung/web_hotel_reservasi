<?php require_once('../Connections/localhost.php'); ?>
<?php require_once('../Connections/localhost.php'); ?>
<?php
require_once('../Connections/localhost.php');
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

mysql_select_db($database_localhost, $localhost);
$query_user = "SELECT * FROM `user`";
$user = mysql_query($query_user, $localhost) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);

$maxRows_report = 10;
$pageNum_report = 0;
if (isset($_GET['pageNum_report'])) {
  $pageNum_report = $_GET['pageNum_report'];
}
$startRow_report = $pageNum_report * $maxRows_report;

$colname_report = "-1";
if (isset($_POST['month'])) {
  $colname_report = $_POST['month'];
}
mysql_select_db($database_localhost, $localhost);
$query_report = sprintf("SELECT reservations.rsrv_id, reservations.rsrv_timestamp, reservations.rsrv_first_name, reservations.rsrv_last_name, reservations.rsrv_email, reservations.rsrv_contact, reservations.rsrv_room, reservations.rsrv_bed, reservations.rsrv_pillow, reservations.rsrv_towel, reservations.rsrv_kit, reservations.rsrv_start, reservations.rsrv_end, reservations.rsrv_guest, reservations.rsrv_notes, DATEDIFF(rsrv_end, rsrv_start) AS diff FROM reservations WHERE MONTH(rsrv_start) = %s ORDER BY rsrv_timestamp ASC", GetSQLValueString($colname_report, "date"));
$query_limit_report = sprintf("%s LIMIT %d, %d", $query_report, $startRow_report, $maxRows_report);
$report = mysql_query($query_limit_report, $localhost) or die(mysql_error());
$row_report = mysql_fetch_assoc($report);

if (isset($_GET['totalRows_report'])) {
  $totalRows_report = $_GET['totalRows_report'];
} else {
  $all_report = mysql_query($query_report);
  $totalRows_report = mysql_num_rows($all_report);
}
$totalPages_report = ceil($totalRows_report/$maxRows_report)-1;

$room = $row_report['rsrv_room'];
$room_price = 0;

if ($room =='Standard')
{
	$room_price == 700;
}
elseif ($room =='Deluxe')
{
	$room_price == 900;
}
elseif($room =='Family')
{
	$room_price == 1000;
}


$diff = $row_report['diff'];
$tot_bed =($row_report['rsrv_bed']) * 200;
$tot_pillow = ($row_report['rsrv_pillow']) * 30;
$tot_towel = ($row_report['rsrv_towel']) * 50;
$tot_kit = ($row_report['rsrv_kit']) * 20;
$tot_addons = $tot_bed + $tot_kit + $tot_pillow + $tot_towel;
$tot_room = $diff * $room_price;



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style/stylesheet.css" />
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<title>Admin Panel - Hans Guest House</title>
</head>
<body>
<div class="top_container">
    	<span id="panel_name">Admin Panel</span>
  <span id="user">Welcome, <?php echo $row_user['user_full']; ?><br/><a href="<?php echo $logoutAction ?>">Log out</a></span>
</div>
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
     <h2><i class="fa fa-bars"></i> Monthly Reports</h2>
     <form id="POST" name="POST" method="POST" action="report.php">
       <label>Select Month: 
         <select name="month" id="month" >
      	<option value="0">-Select-</option>
           	<option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">April</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
         </select>
       </label>
      <!-- <label>Year: <?php
$current_year = date("Y");
$range = range($current_year, ($current_year - 20));
echo '<select name="year" id="contact-year" tabindex="7">';
 
//Now we use a foreach loop and build the option tags
foreach($range as $r)
{
echo '<option value="'.$r.'">'.$r.'</option>';
}
 
//Echo the closing select tag
echo '</select>';
?></label> -->
     <input type="submit" name="submit" id="submit"  value="Go" />
     </form><br/>
     <table border="0" cellpadding="0" cellspacing="0" class="data_table">
       <tr id="tr">
         <td>Submitted</td>
         <td>First Name</td>
         <td>Last Name</td>
         <td>Email</td>
         <td>Contact</td>
         <td>Room</td>
         <td>Bed</td>
         <td>Pillow</td>
         <td>Towel</td>
         <td>Guest Kit</td>
         <td>Check In</td>
         <td>Check Ou</td>
         <td>Guest</td>
         <td>Amount</td>
       </tr>
       <?php do { ?>
         <tr>
           <td><?php echo $row_report['rsrv_timestamp']; ?></td>
           <td><?php echo $row_report['rsrv_first_name']; ?></td>
           <td><?php echo $row_report['rsrv_last_name']; ?></td>
           <td><?php echo $row_report['rsrv_email']; ?></td>
           <td><?php echo $row_report['rsrv_contact']; ?></td>
           <td><?php echo $row_report['rsrv_room']; ?></td>
           <td><?php echo $row_report['rsrv_bed']; ?></td>
           <td><?php echo $row_report['rsrv_pillow']; ?></td>
           <td><?php echo $row_report['rsrv_towel']; ?></td>
           <td><?php echo $row_report['rsrv_kit']; ?></td>
           <td><?php echo $row_report['rsrv_start']; ?></td>
           <td><?php echo $row_report['rsrv_end']; ?></td>
           <td><?php echo $row_report['rsrv_guest']; ?></td>
           <td><?php echo $diff ?></td>
         </tr>
         <?php } while ($row_report = mysql_fetch_assoc($report)); ?>
     </table>
     </div>
     
</body>
</html>
<?php
mysql_free_result($user);

mysql_free_result($report);
?>
