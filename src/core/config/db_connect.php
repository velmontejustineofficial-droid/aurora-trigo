<?php

$servername = "sql306.infinityfree.com";       //your hosting DB host (usually localhost)
$username   = "if0_43329317";            //your hosting DB username
$password   = "TcVjH5C9sKeqW2";                //your hosting DB password
$dbname     = "if0_43329317_aurora_trigo";    //your DB name

//create mysqli connection
$conn = new mysqli($servername, $username, $password, $dbname);

//check connection
if ($conn->connect_error) {
    die("Connection failed:");
}

echo "connected";