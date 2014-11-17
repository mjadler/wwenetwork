<?php require_once('../Connections/wweDB.php'); ?>
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

$thisEvent_getEventWorkers = "http://network.wwe.com/video/v31345263/";
if (isset($_GET['msEvent'])) {
    $thisEvent_getEventWorkers = $_GET['msEvent'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getEventWorkers = sprintf("SELECT workers.workerID, workers.workerName, workers.workerImage FROM workers, milestones WHERE workers.workerID = milestones.workerID AND INSTR(msEventLink, %s) GROUP BY workers.workerImage ASC ORDER BY milestones.msMatchLink ASC", GetSQLValueString($thisEvent_getEventWorkers, "text"));
$getEventWorkers = mysql_query($query_getEventWorkers, $wweDB) or die(mysql_error());
$row_getEventWorkers = mysql_fetch_assoc($getEventWorkers);
$totalRows_getEventWorkers = mysql_num_rows($getEventWorkers);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Untitled Document</title>
    <link href="css/playlist_builder.css" rel="stylesheet" type="text/css">
</head>

<body>
<strong>
    <span class="matchTitle">
    <?php if ($totalRows_getEventWorkers > 0) { // Show if recordset not empty ?>
        <br>
    Appeared at this event:<br />
    <?php } // Show if recordset not empty ?>
        <br />
    </span></strong>
<?php do { ?>
    <div id="event_workers">
        <div id="event_worker_image">
            <?php if($row_getEventWorkers['workerImage']!=""){?>
                <a href="../wwenetwork/getWorker.php?workerID=<?php echo $row_getEventWorkers['workerID']; ?>"><img src="workers/<?php echo $row_getEventWorkers['workerImage']; ?>" width="36" height="80" /></a>
            <?php } else if($row_getEventWorkers['workerID']!='') { ?>
                <img src="workers/empty.png" width="36" height="80" />
            <?php } ?>
        </div>
        <br />
        <a href="getWorker.php?workerID=<?php echo $row_getEventWorkers['workerID']; ?>" class="smalltext"><?php echo $row_getEventWorkers['workerName']; ?></a></div>
<?php } while ($row_getEventWorkers = mysql_fetch_assoc($getEventWorkers)); ?>
</body>
</html>
<?php
mysql_free_result($getEventWorkers);
?>
