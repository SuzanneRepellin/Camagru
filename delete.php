<?php
include 'config/database.php';
include 'config/connect.php';
@session_start();

if (isset($_SESSION['loggued_on_user']))
{
	$id = $_GET['id'];
	$user = $_SESSION['loggued_on_user'];
	if (isset($_GET['id']) && $_GET['id'] !== "")
		{
			$stmt = $pdo->prepare("DELETE FROM Photos WHERE id = :id AND Author = :user;");
			$stmt->bindValue(":id", $id, PDO::PARAM_INT);
			$stmt->bindValue(":user", htmlspecialchars($user), PDO::PARAM_STR);
			$stmt->execute();
		}
	if (isset($_GET['prev']) && ($_GET['prev'] === "gal"))
		header('location: gallery.php');
	else
		header('location: index.php');
}
else
	header('location: index.php');
