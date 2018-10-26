<?php

// Setup the Headers
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("/home/shiba009933/chriscarini.com/includes/functions.inc.php");

/* Connect to MySQL */
$mysqlLink = connectToMySQL("organizedphotodb");

if($_REQUEST['num'])
{
	$sql = "SELECT * FROM `photos` WHERE id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `photos` ) AND display = '0' ORDER BY id LIMIT ".$_REQUEST['num'].";";
	$sql = "SELECT * FROM `photos` WHERE display = '0' ORDER BY id DESC LIMIT ".$_REQUEST['num'].";";
	//$sql = "SELECT * FROM `photos` WHERE display = '0' ORDER BY rand() DESC LIMIT ".$_REQUEST['num'].";";
	//$sql = "SELECT * FROM `photos` WHERE display = '0' ORDER BY fullPath DESC LIMIT 5;";
}
else
{
	$sql = "SELECT * FROM `photos` WHERE id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `photos` ) AND display = '0' ORDER BY id LIMIT 1;";
	$sql = "SELECT * FROM `photos` WHERE display = '0' ORDER BY id DESC LIMIT 1;";
	$sql = "SELECT * FROM `photos` WHERE display = '0' ORDER BY rand() DESC LIMIT 1;";
	//$sql = "SELECT * FROM `photos` WHERE display = '0' ORDER BY fullPath DESC LIMIT 1;";
}
$result = mysql_query($sql, $mysqlLink);

// Close MySQL Connection
mysql_close($mysqlLink);

if($_REQUEST['num'])
{
	$x = 1;
	while($row = mysql_fetch_array($result))
	{
		echo "<img class='returnedImage' id='returnedImage".$x++."' width='" . $_REQUEST['width'] . "px' src='" . str_replace(".JPG","_800px.gif",$row['fullPath']) . "' />|";
	}
}
else
{
	// There is a row with EXIF Data, pull from there and return that...
	$row = mysql_fetch_array($result);

	if($_REQUEST['index'] == "null"){$index = "";}else{$index = $_REQUEST['index'];}
	echo "<img id='returnedImage".$index."' width='" . $_REQUEST['width'] . "px' src='" . str_replace(".JPG","_800px.gif",$row['fullPath']) . "' />";
}
?>