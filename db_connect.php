<?php
$servername = "sql212.infinityfree.com";       //your hosting DB host (usually localhost)
$username   = "if0_42991926";            //your hosting DB username
$password   = "KYuZTWnTl0FpY";                //your hosting DB password
$dbname     = "if0_42991926_XXX";    //your DB name

//create mysqli connection
$conn = new mysqli($servername, $username, $password, $dbname);

//check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
