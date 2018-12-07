<?php
function write_com($user, $text, $pic_id, $pdo, $author)
{
	$stmt = $pdo->prepare("INSERT INTO Coms (user, text, pic_id) VALUES (:user, :text, :pic_id);");
	$stmt->bindValue(':user', $user, PDO::PARAM_STR);
	$stmt->bindValue(':text', $text, PDO::PARAM_STR);
	$stmt->bindValue(':pic_id', $pic_id, PDO::PARAM_INT);
	$stmt->execute();
	$stmt = $pdo->prepare("UPDATE Photos SET com_nb = com_nb + 1 WHERE id = :pic_id;");
	$stmt->bindValue(':pic_id', $pic_id, PDO::PARAM_INT);
	$stmt->execute();
	$stmt = $pdo->prepare("SELECT usermail FROM users WHERE username = :author AND comment_mail = 'YES';");
	$stmt->bindValue(':author', $author, PDO::PARAM_STR);
	$stmt->execute();
	if ($stmt->rowcount() == 1)
	{
		$result = $stmt->fetchall();
		$email = implode($result[0]);
		$link = "http://localhost:8008/comment.php?pic_id=" . $pic_id;
		mail($email, 'New comment on one of your pictures!', "Dear $author,\nGreat news! Someone wrote a comment on one of your Camagru pictures.\n$user said: '$text'\nYou can see the picture and all its comments here: $link\nBest regards,\nThe Camagru Team.\n");
	}
}
