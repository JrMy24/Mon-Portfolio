<?php
$severname = "db";
$username = "root";
$password = "root";
$dbname = "Boulangerie";

$conn = new mysqli ($severname, $username,$password,$dbname);
if ($conn -> connect_error) {
    die("connection failed: " . $conn -> connect_error);
}
?>
