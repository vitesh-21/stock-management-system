<?php
$servername = "sql101.infinityfree.com";
$username = "if0_41353191";
$password = "8900veshka1";
$dbname = "if0_41353191_lance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // On a live site, it's better to log this than to echo it
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for better compatibility (emojis, special chars)
$conn->set_charset("utf8mb4");
?>