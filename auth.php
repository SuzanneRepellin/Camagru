<?PHP

function auth($login, $pwd, $pdo)
{
	if ($login !== "" && $pwd != "")
	{
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :login AND password = '$pwd';");
		$stmt->bindValue(':login', $login, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowcount() > 0)
			return TRUE;
	}
	return FALSE;
}
?>
