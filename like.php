<?php
include 'config/database.php';
include 'config/connect.php';
@session_start();

if (isset($_SESSION['loggued_on_user']) && $_POST)
{
	$_POST = array_flip($_POST);
	if (isset($_POST['']));
	{
		$id = $_POST[''];
		$user = $_SESSION['loggued_on_user'];
		$stmt = $pdo->prepare("SELECT id FROM likes WHERE photo_id = '$id' AND user = :user;");
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowcount() > 0)
		{
			$unlike = $pdo->prepare("DELETE FROM likes WHERE user = :user AND photo_id = '$id';");
			$unlike->bindValue(':user', $user, PDO::PARAM_STR);
			$unlike->execute();
			$stmt = $pdo->prepare("UPDATE Photos SET like_nb = like_nb - 1 WHERE id = '$id';");
			$stmt->execute();
		}
		else
		{
			$like = $pdo->prepare("INSERT INTO likes (user, photo_id) VALUES (:user, '$id');");
			$like->bindValue(':user', $user, PDO::PARAM_STR);
			$like->execute();
			$stmt = $pdo->prepare("UPDATE Photos SET like_nb = like_nb + 1 WHERE id = '$id';");
			$stmt->execute();
		}
	}
}
?>
