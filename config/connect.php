<?php
include 'database.php';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
$pdo = new PDO($dsn, $user, $pass, $opt);
} catch (\PDOException $e) {
throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
