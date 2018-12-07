<?php
session_start();
include("config/database.php");
include("config/connect.php");
include("auth.php");
?>
<!DOCTYPE=html>
<html>
<head>
<meta charset="UTF-8"/>
<link rel="stylesheet" type="text/css" href="camagru.css">
<title>Login</title>
</head>
<body>
<div id="header">
<div id="user">
<?php if (isset($_SESSION['loggued_on_user'])) echo '
<p> - Bonjour '.$_SESSION['loggued_on_user'].' - </p>
';
else
	echo '
<p> - Bonjour visiteur - </p>
';?>
</div>
<div id="title">
<a href="index.php">camaGROU</a>
</div>
<div id="login">
<?php
if (!isset($_SESSION['loggued_on_user']))
{
	echo '
<a href="login.php">login</a>';
}
else
{
	echo '<a href="account.php">account</a>';
}
?>
</div>
</div>
<?PHP


/*L'utilisateur fournit son login ou son email*/
if (!isset($_SESSION['loggued_on_user']) && (!isset($_POST['login']) || !isset($_POST['email'])))
{
	echo '
	<form method="post">
	<p>Username:</p>
	<input type="text" name="login" value="" required/>
	<p>Email:</p>
	<input type="text" name="email" value="" required/>
	<input type="reset" value="Cancel">
	<form>
	<input type="submit" name="account" value="send email">
	</form>
	</form>
	';
}
else
{
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
/*pour verifier le nom d'utilisateur ou le mail et que le compte est confirme*/
		$usrn = $_POST['login'];
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :usrn AND confirmed = 'YES';");
		$stmt->bindValue(':usrn', $usrn, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowcount() > 0)
		{
/*creer un lien de reinitialisation du mdp*/
			if (isset($_POST['email']) && isset($_POST['login']))
			{
				$usrmail = $_POST['email'];
				$result = $pdo->prepare("SELECT id FROM users WHERE usermail = :usrmail AND username = '$usrn';");
				$result->bindValue(':usrmail', $usrmail, PDO::PARAM_STR);
				$result->execute();
				if ($result->rowcount() === 0)
				{
					echo'<p class="message">Wrong username or email adress</p>';
				}
				else
				{
		/*generer un lien unique pour l'utilisateur*/
					$reset_link_rand = md5(gettimeofday(TRUE));
					$reset_link = "http://localhost:8008/set_new_pwd.php?method=GET&user=" . $usrn . "&hash=" . $reset_link_rand;
					$stmt = $pdo->prepare("UPDATE users SET reset_link = '$reset_link_rand' WHERE username = '$usrn';");
					$stmt->execute();
					mail($usrmail, 'Reset the password of your Camagru account', "Dear $usrn,\nYou have asked for a mail to reset your password. If you did not, you can ignore this message and log in with your usual password.\nTo reset your password, please click on the following link:\n$reset_link\nBest regards,\nThe Camagru Team.\n");
					echo'<p>A link was sent to your emailbox in order to reset your password</p>';
					}
				}
		}
		else
		{
			echo'<p class="message">Wrong username</p>';
		}
	}
}
?>
<div id="footer">
<table id="footertext">
<tr>
<div id="logout">
<?php
if (isset($_SESSION['loggued_on_user']))
{
	echo '<td class="footercell"><a href="logout.php">logout</a></td>';
}
?>
</div>
<td class="footercell"><a href=index.php>home</a></td>
<td class="footercell">© srepelli 2018</td>
</tr>
</table>
</div>
</body>
</html>
