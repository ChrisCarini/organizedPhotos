<?php

// Setup the Headers
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

include("/home/shiba009933/chriscarini.com/includes/functions.inc.php");


if($_REQUEST['decision'] == '1' or $_REQUEST['decision'] == '-1')
{
	/* Connect to MySQL */
	$mysqlLink = connectToMySQL("organizedphotodb");

	$sql = sprintf("UPDATE `organizedphotodb`.`photos` SET `display` = '%s' WHERE `photos`.`fullPath` = '%s';",
            mysql_real_escape_string($_REQUEST['decision']),
            mysql_real_escape_string($_REQUEST['photo']));

	echo $sql;
	$result = mysql_query($sql, $mysqlLink);
	echo "result:".$result;

	// Close MySQL Connection
	mysql_close($mysqlLink);
}
else
{
	echo "FAIL";
}
?>