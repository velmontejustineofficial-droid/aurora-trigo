<?php
$servername = "sql110.infinityfree.com";       //your hosting DB host (usually localhost)
$username   = "if0_41474999";            //your hosting DB username
$password   = "wsXlfNdXcIt";                //your hosting DB password
$dbname     = "if0_41474999_aurora_trigo";    //your DB name

//create mysqli connection
$conn = new mysqli($servername, $username, $password, $dbname);

//check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
