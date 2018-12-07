<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<link rel="stylesheet" type="text/css" href="camagru.css">
<title>My account</title>
</head>
<body>
<div id="header">
<div id="user">
<?php 
include 'config/database.php';
include 'config/connect.php';
include 'is_secure_mdp.php';
@session_start();

if (isset($_SESSION['loggued_on_user']))
echo '
<p> - Hi '. $_SESSION['loggued_on_user'].' - </p>
';
else
echo '
<p> - Hello stranger - </p>
';?>
</div>
<div id="title">
<a href=index.php">camaGROU</a>
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
	echo '<a href="account.php">my account</a>';
}
?>
</div>
</div>
<?php

$login = $_SESSION['loggued_on_user'];
if (isset($_POST['change']) && $_POST['change'] === "confirm")
{
	if (strlen($_POST['oldpwd']) !== 0 && strlen($_POST['newpwd']) !== 0)
	{
		if ($_POST['oldpwd'] !== $_POST['newpwd'])
		{
			$oldpwd = hash('whirlpool', $_POST['oldpwd']);
			$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :login AND password = '$oldpwd';");
			$stmt->bindValue(':login', $login, PDO::PARAM_STR);
			$stmt->execute();
			if ($stmt->rowcount() === 0)
			{
				echo '<p>Wrong password</p>';
				echo '<a href="account.php">Return</a>';
			}
			else
			{
				if (is_secure_mdp($_POST['newpwd']))
				{
					$newpwd = hash('whirlpool', $_POST['newpwd']);
					$stmt = $pdo->prepare("UPDATE users SET password = '$newpwd' WHERE username = :login;");
					$stmt->bindValue(':login', $login, PDO::PARAM_STR);
					$stmt->execute();
					echo '<p>Your password was changed</p>';
					echo '<a href="account.php">Return</a>';
				}
				else
				{
					echo '<p>Your password is not strong enough (must be at least 8 characters long, with letters and numbers)</p>';
					echo '<a href="account.php">Return</a>';
				}
			}
		}
	}
	if (strlen($_POST['email']) !== 0)
	{
		$email = $_POST['email'];
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			echo '<p>Not a valid email adress</p>';
			echo '<a href="account.php">Return</a>';
		}
		else
		{
			$stmt = $pdo->prepare('SELECT usermail FROM users WHERE username = :login;');
			$stmt->bindValue(':login', $login, PDO::PARAM_STR);
			$stmt->execute();
			if ($stmt->fetchall()[0]['usermail'] !== $email)
			{
				$stmt = $pdo->prepare("SELECT username FROM users WHERE usermail = :email;");
				$stmt->bindValue(':email', $email, PDO::PARAM_STR);
				$stmt->execute();
				if ($stmt->rowcount())
				{
					echo '<p>This email adress is already linked to a Camagru account</p>';
					echo '<a href="account.php">Return</a>';
				}
				else
				{
					$stmt = $pdo->prepare("UPDATE users SET usermail = :email WHERE username = :login;");
					$stmt->bindValue(':login', $login, PDO::PARAM_STR);
					$stmt->bindValue(':email', $email, PDO::PARAM_STR);
					$stmt->execute();
					echo '<p>Your email adress was changed</p>';
					echo '<a href="account.php">Return</a>';
				}
			}
			else if (strlen($_POST['oldpwd']) === 0 && strlen($_POST['newpwd']) === 0 && (strlen($_POST['username']) === 0 || $login === $_POST['username']))
				header('location: account.php');
		}
	}
	if (strlen($_POST['username']) !== 0 && htmlspecialchars($_POST['username']) !== $login)
	{
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :user;");
		$stmt->bindValue(':user', htmlspecialchars($_POST['username']), PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowcount())
		{
			echo '<p>This username is already taken</p>';
			echo '<a href="account.php">Return</a>';
		}
		else
		{
			$newlogin = $_POST['username'];
			$stmt = $pdo->prepare("UPDATE users SET username = :newlogin WHERE username = :login;");
			$stmt->bindValue(':newlogin', htmlspecialchars($newlogin), PDO::PARAM_STR);
			$stmt->bindValue(':login', $login, PDO::PARAM_STR);
			$stmt->execute();
			$_SESSION['loggued_on_user'] = htmlspecialchars($newlogin);
			echo '<p>Your username was changed</p>';
			echo '<a href="account.php">Return</a>';
		}
	}
}
else if (isset($_POST['change']) && $_POST['change'] === "update")
{
	$com = $_POST['comment_mail'];
	$stmt = $pdo->prepare("UPDATE users SET comment_mail = '$com' WHERE username = :login;");
	$stmt->bindValue(':login', $login, PDO::PARAM_STR);
	$stmt->execute();
	echo'<p>Your preferences were updated</p>';
	echo '<a href="account.php">Return</a>';
}
else
	header("location: account.php");
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
