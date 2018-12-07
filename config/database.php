<?php
$host = 'z2r2p4';
$db   = 'Camagru';
$user = 'root';
$pass = 'rootpass';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$opt = [
	PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES		=> false,
];
?>
