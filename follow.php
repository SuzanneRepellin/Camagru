<?php

function	follow($pdo, $user, $id)
{
	$stmt = $pdo->prepare('SELECT follows FROM users WHERE username = :user;');
	$stmt->bindValue(':user', $user, PDO::PARAM_STR);
	$stmt->execute();
	$truc = $stmt->fetchall();
	if (!$user)
		echo 'You must be loggued on to follow a user';
	if (!isset($truc[0]))
	{
		$id = $id . ":";
		$stmt = $pdo->prepare('UPDATE users SET follows = :follow WHERE username = :user;');
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		$stmt->bindValue(':follow', $id, PDO::PARAM_STR);
		$stmt->execute();
	}
	else
	{
		$fol = $truc[0]['follows'];
		$str = $fol . $id . ":";
		$stmt = $pdo->prepare('UPDATE users SET follows = :follow WHERE username = :user;');
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		$stmt->bindValue(':follow', $str, PDO::PARAM_STR);
		$stmt->execute();
	}
}

function	unfollow($pdo, $user, $id)
{
	$stmt = $pdo->prepare('SELECT follows FROM users WHERE username = :user;');
	$stmt->bindValue(':user', $user, PDO::PARAM_STR);
	$stmt->execute();
	$str = $stmt->fetchall()[0]['follows'];
	$follows = explode(":", $str);
	foreach ($follows as $key => $value)
	{
		if ($value == $id)
		{
			$follows[$key] = "";
		}
	}
	$follows = array_filter($follows, "strlen");
	$str = implode(":", $follows);
	if (strlen($str))
		$str = $str . ":";
	else
		$str = NULL;
	$stmt = $pdo->prepare('UPDATE users SET follows = :follow WHERE username = :user;');
	$stmt->bindValue(':user', $user, PDO::PARAM_STR);
	$stmt->bindValue(':follow', $str, PDO::PARAM_STR);
	$stmt->execute();
}

function	follow_or_not($pdo, $user, $id)
{
	$fol = $pdo->prepare('SELECT id, follows FROM users WHERE username = :login;');
	$fol->bindValue(':login', $user, PDO::PARAM_STR);
	$fol->execute();
	if ($fol->rowcount() === 0)
		return FALSE;
	$str = $fol->fetchall()[0]['follows'];
	if ($str === NULL)
		return FALSE;
	if (preg_match('#:(' . $id . '):|^(' . $id . '):#', $str))
	{
		return TRUE;
	}
	return FALSE;
}
?>
