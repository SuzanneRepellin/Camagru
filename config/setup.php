<?php
include 'database.php';

$dsn = "mysql:host=$host;charset=$charset";
try {
	$pdo = new PDO($dsn, $user, $pass, $opt);
} catch (\PDOException $e) {
	throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
$stmt = $pdo->prepare('CREATE DATABASE IF NOT EXISTS `Camagru`;
');
$stmt->execute();
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
include 'connect.php';
$stmt = $pdo->prepare('CREATE TABLE IF NOT EXISTS `Coms` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user` char(255) NOT NULL,
  `text` char(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
$stmt->execute();
$stmt = $pdo->prepare('CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `photo_id` int(11) NOT NULL,
  `user` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
$stmt->execute();
$stmt = $pdo->prepare('CREATE TABLE IF NOT EXISTS `Photos` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `Data` mediumtext,
  `Filter` mediumtext,
  `Author` char(255) DEFAULT NULL,
  `Upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `com_nb` int(10) UNSIGNED NOT NULL DEFAULT "0",
  `like_nb` int(11) NOT NULL DEFAULT "0"
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
$stmt->execute();
$stmt = $pdo->prepare('CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE KEY,
  `password` varchar(255) NOT NULL,
  `usermail` varchar(50) NOT NULL UNIQUE KEY,
  `confirmed` char(3) DEFAULT "NO",
  `link` varchar(535) DEFAULT NULL,
  `reset_link` char(255) DEFAULT NULL,
  `comment_mail` char(3) NOT NULL DEFAULT "YES",
  `follows` TEXT NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;');
$stmt->execute();
?>
