<?php
$username = "root";
$pass = "";
$host = "localhost";
$db = "BDD_Projets";
$dsn = "mysql:host=$host;dbname=$db";

try {
    $cnx = new PDO($dsn, $username, $pass);
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>