<?php require_once('../Connections/localhost.php');
	  require_once('includes/fpdf/fpdf.php');
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "admin";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
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

$colname_Recordset1 = "-1";
if (isset($_GET['id'])) {
  $colname_Recordset1 = $_GET['id'];
}
mysql_select_db($database_localhost, $localhost);
$query_Recordset1 = sprintf("SELECT * FROM reservations WHERE rsrv_id = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $localhost) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$colname_Recordset2 = "-1";
if (isset($_GET['id'])) {
  $colname_Recordset2 = $_GET['id'];
}
mysql_select_db($database_localhost, $localhost);
$query_Recordset2 = sprintf("SELECT rsrv_id, DATE_FORMAT(rsrv_start, '%%M %%d, %%Y') AS dateStart, DATE_FORMAT(rsrv_end, '%%M %%d, %%Y') AS dateEnd, DATEDIFF(rsrv_end, rsrv_start) AS diff FROM reservations WHERE reservations.rsrv_id = %s", GetSQLValueString($colname_Recordset2, "int"));
$Recordset2 = mysql_query($query_Recordset2, $localhost) or die(mysql_error());
$row_Recordset2 = mysql_fetch_assoc($Recordset2);
$totalRows_Recordset2 = mysql_num_rows($Recordset2);

class PDF extends FPDF
{
function Header()
{
    // Logo
    //$this->Image('logo.jpg',10,10,30);
    // Arial bold 15
    $this->SetFont('Arial','B',18);
    // Title
    $this->Cell(0,15,'Hans Guest House',0,0,'C');

    // Line break
    $this->Ln(10);
	$this->SetFont('Arial','',16);
	$this->Cell(0,20,'Reservation Slip',0,2,'C');
}
}

$rsrv_name =  ($row_Recordset1['rsrv_last_name']) .' '. ($row_Recordset1['rsrv_first_name']);
$rsrv_room = $row_Recordset1['rsrv_room'];

$rsrv_rm_price = 0;
	if ($rsrv_room == "Standard")
		{
			$rsrv_rm_price = 700;
		}
	elseif ($rsrv_room == "Deluxe")
		{
			$rsrv_rm_price = 900;
		}
	elseif  ($rsrv_room == "Family")
		{
			$rsrv_rm_price = 1000;
		}
		
$from = $row_Recordset2['dateStart'];
$to = $row_Recordset2['dateEnd'];
$diff = $row_Recordset2['diff'];
$tot_bed = ($row_Recordset1['rsrv_bed']) * 200;
$tot_towel = ($row_Recordset1['rsrv_towel']) * 50;
$tot_pillow = ($row_Recordset1['rsrv_pillow']) * 30;
$tot_kit = ($row_Recordset1['rsrv_kit']) * 20;
$tot_addons = $tot_bed + $tot_towel + $tot_pillow + $tot_kit;
$total_diff = $rsrv_rm_price * $diff;
$total = $tot_addons + $total_diff;

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Reservation Dates: '.$from.' - '.$to.'',0,2);
$pdf->Cell(0,8,'Number of Days: '.$diff.'',0,1);
$pdf->Cell(0,8,'Reservation Name: '.$rsrv_name.'',0,1);
$pdf->Cell(0,8,'Room Type: '.$rsrv_room.'',0,1);
$pdf->SetFont('Arial','',11);
$pdf->Cell(0,8,'Room Price: '.$rsrv_rm_price.'',0,1);
$pdf->Cell(0,8,'Room Reservation Total: '.$total_diff.'',0,1);
$pdf->Cell(0,8,'Total Addons: '.$tot_addons.'',0,1);
$pdf->SetFont('Arial','B',21);
$pdf->Cell(0,15,'TOTAL: '.$total.'',0,2);


$pdf->Output();
// default output set to I means to browser.

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hans Guest House - Reservation Slip</title>
<link href="style/sheet.css" rel="stylesheet"/>
</head>
<body>
</body>
</html>
<?php
mysql_free_result($Recordset1);

mysql_free_result($Recordset2);
?>
