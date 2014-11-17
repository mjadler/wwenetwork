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


// Return a list of the different page options in the network db 
mysql_select_db($database_wweDB, $wweDB);
$query_getPage = "SELECT network.page FROM network GROUP BY network.page ORDER BY network.fed";
$getPage = mysql_query($query_getPage, $wweDB) or die(mysql_error());
$row_getPage = mysql_fetch_assoc($getPage);
$totalRows_getPage = mysql_num_rows($getPage);


// Return the categories that fall under the page listing that was selected by the user in the navigation bar
$thisPage_getPageCategories = "0";
if (isset($_GET['page'])) {
    $thisPage_getPageCategories = $_GET['page'];
}
mysql_select_db($database_wweDB, $wweDB);
$query_getPageCategories = sprintf("SELECT network.category FROM network WHERE network.page = %s GROUP BY network.category", GetSQLValueString($thisPage_getPageCategories, "text"));
$getPageCategories = mysql_query($query_getPageCategories, $wweDB) or die(mysql_error());
$row_getPageCategories = mysql_fetch_assoc($getPageCategories);
$totalRows_getPageCategories = mysql_num_rows($getPageCategories);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="css/playlist_builder.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="https://ajax.microsoft.com/ajax/jQuery/jquery-1.4.2.min.js"></script>
    <style>
        body {
            background: url("CMPUNK_BG.jpg") no-repeat center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
    </style>

</head>

<body>
<span class="nav"><a href="index.php">Home</a> | <a href="wrestlers.php">Wrestlers</a> |
<!-- The first categories are static links to the home page and a link to the wrestler page, the next set of links are dynamically generated from the network db, then there is a static link to the playlists page and the randomizer page -->
    <?php do { ?>
        <?php if($_GET['page']==$row_getPage['page']){ echo "<b>["; } ?>
        <a href="index.php?page=<?php echo $row_getPage['page']; ?>"><?php echo $row_getPage['page']; ?></a>
        <?php if($_GET['page']==$row_getPage['page']){ echo "]</b>"; } else { echo "|"; } ?>
    <?php } while ($row_getPage = mysql_fetch_assoc($getPage)); ?>
    | <a href="playlists.php">Playlists</a> | <a href="randomizer.php">Randomizer</a> | <a href="updates.php">Updates</a> |

<!-- This iframe gives us a link back to the logged in users dashboard page or the option to login/create a new user if there is no active session -->
<?php echo "<iframe src=account.php scrolling=no height=25 width=200 frameborder=0 name='loginFrame'></iframe>";?> 
<br />
<!-- This include gives us the search functionality under the navigation bar -->
<?php include("search.php"); ?>
<br />
<!-- TODO -->
<!-- The page links will give us a sub navigation bar that will contain the subcategories -->
<?php if ($totalRows_getPageCategories > 0) { // Show if recordset not empty  

	do { ?>
    
		<li><a href="getCategory.php?category=<?php echo $row_getPageCategories['category']; ?>"><?php echo $row_getPageCategories['category']; ?></a></li>
        
    <?php } while ($row_getPageCategories = mysql_fetch_assoc($getPageCategories)); ?>
<?php } ?>
</span>
</body>
</html>
<?php
mysql_free_result($getPage);

mysql_free_result($getPageCategories);
?>
