<?php require_once('../Connections/wweDB.php'); ?>
<?php include("navigation.php"); ?>
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

// Take the wrestler connected to the supplied workerID and return all of the other wrestlers who share milestones with them, return the top 25 rivals in order of number of shared milestone links 
$thisWorker_getRivals = "450";
if (isset($_GET['workerID'])) {
    $thisWorker_getRivals = $_GET['workerID'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getRivals = sprintf("SELECT rio.workerID, n.workerName, COUNT(n.workerName) AS matchCount FROM ( SELECT milestones.msMatchLink, milestones.workerID FROM milestones WHERE milestones.workerID =  %s AND INSTR(milestones.msTitle, 'vs')) zigs JOIN milestones rio ON rio.msMatchLink = zigs.msMatchLink AND rio.workerID !=  %s JOIN workers n ON n.workerID = rio.workerID GROUP BY workerID ORDER BY COUNT( n.workerName ) DESC  LIMIT 25", GetSQLValueString($thisWorker_getRivals, "int"),GetSQLValueString($thisWorker_getRivals, "int"));
$getRivals = mysql_query($query_getRivals, $wweDB) or die(mysql_error());
$row_getRivals = mysql_fetch_assoc($getRivals);
$totalRows_getRivals = mysql_num_rows($getRivals);


// Get the basic information for this wrestler
$zigs_getWorker = "450";
if (isset($_GET['workerID'])) {
    $zigs_getWorker = $_GET['workerID'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getWorker = sprintf("SELECT * FROM workers WHERE workers.workerID = %s  ", GetSQLValueString($zigs_getWorker, "int"));
$getWorker = mysql_query($query_getWorker, $wweDB) or die(mysql_error());
$row_getWorker = mysql_fetch_assoc($getWorker);
$totalRows_getWorker = mysql_num_rows($getWorker);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $row_getWorker['workerName']; ?> | Partners &amp; Rivals</title>
</head>

<body>
<strong><?php echo $row_getWorker['workerName']; ?> Partners & Rivals</strong><br />
<?php do { ?>
    <a href="getRivalry.php?face=<?php echo $row_getWorker['workerID']; ?>&amp;heel=<?php echo $row_getRivals['workerID']; ?>"><?php echo $row_getRivals['workerName']; ?></a> <?php echo $row_getRivals['matchCount']; ?> Matches<br />
<?php } while ($row_getRivals = mysql_fetch_assoc($getRivals)); ?>
</body>
</html>
<?php
mysql_free_result($getRivals);

mysql_free_result($getWorker);
?>
