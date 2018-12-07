<?PHP
include 'config/database.php';
include 'config/connect.php';

/*verifier la correspondance du lien unique et du nom d'utilisateur, et passer le compte en confirme*/
if (isset($_GET['user']) && isset($_GET['hash']))
{
	$user = $_GET['user'];
	$hash = $_GET['hash'];
	$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :user AND link = '$hash';");
	$stmt->bindValue(':user', $user, PDO::PARAM_STR);
	$stmt->execute();
	if ($stmt->rowcount() == 1)
	{
		$_SESSION['loggued_on_user'] = $user;
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :user AND link = '$hash' AND confirmed = 'YES';");
		$stmt->bindValue(':user', $user, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowcount() == 1)
		{
			echo '<p id="message">Your email address has already been confirmed</p>
			<a href="index.php">home</a>
';
		}
		else
		{
			$stmt = $pdo->prepare("UPDATE users SET confirmed = 'YES' WHERE username = :user AND link = '$hash';");
			$stmt->bindValue(':user', $user, PDO::PARAM_STR);
			$stmt->execute();
			echo '<p id="message">Your email adress was confirmed. Welcome on Camagru!</p>
			<a href="index.php">home</a>
';
		}
	}
}
else
	header('location: index.php');
?>
