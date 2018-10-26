<?php
/* config.php
 * Configuration file in which can be used across
 * various PHP files to connect to Database.
 * Allows for simpler edits if Database details change.
 * Also includes a variable for Password Length for
 * any files that require a new password to be entered.
 */
define('DB_SERVER', 'quizuser');
define('DB_USERNAME', 'match');
define('DB_PASSWORD', 'localhost');
define('DB_NAME', 'QuizMatch');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
$min_pw_len = 5;

if($link === false)
{
	die("ERROR: Unable to connect. " . mysqli_connect_error());
}
?>
