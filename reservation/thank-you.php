<?php require_once('../Connections/localhost.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  global $localhost;
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($localhost, $theValue) : mysqli_escape_string($localhost, $theValue);

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

$colname_submit = "-1";
if (isset($_POST['rsrv_id'])) {
  $colname_submit = $_POST['rsrv_id'];
}
// mysql_select_db($database_localhost, $localhost);
$query_submit = sprintf("SELECT * FROM reservations WHERE rsrv_id = %s", GetSQLValueString($colname_submit, "int"));
$submit = mysqli_query($localhost, $query_submit) or die(mysql_error());
$row_submit = mysqli_fetch_assoc($submit);
$totalRows_submit = mysqli_num_rows($submit);

$tot_bed = ($row_submit['rsrv_bed']) * 200;
$tot_towel = ($row_submit['rsrv_towel']) * 50;
$tot_pillow = ($row_submit['rsrv_pillow']) * 30;
$tot_kit = ($row_submit['rsrv_kit']) * 20;
$tot_addons = $tot_bed + $tot_towel + $tot_pillow + $tot_kit;
$total = $rsrv_rm_price + $tot_addons;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    	<title>Hans Guest House</title>
<link href="../stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div class="main_container" >
        <a href="#"><img src="../images/logo.png" align="left" /></a>
            <div id="headr_img"></div>
   <div id="content_container">
<h1>THANK YOU!</h1>
<h2>Your informtion has been submitted.</h2>

<h3>Please contact us within 24 hours for confirmation on your transaction.</h3><br />
<p>Click <span><a href="http://localhost/reservasi">here</a></span>, to go back to the main page.</p>
      </div>
         <div id="footer_container">
         	Copyright © 2014 Hans Guest House
      </div>
</div>
</body>
</html>
<?php
mysql_free_result($submit);
?>
