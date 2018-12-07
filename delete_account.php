<html>
<head>
<meta charset="UTF-8"/>
<link rel="stylesheet" href="camagru.css"/>
<title>Delete account</title>
</head>
<body>
<div id="header">
<div id="user">
<?php
@session_start();
include 'config/database.php';
include 'config/connect.php';
include 'auth.php';
if (isset($_SESSION['loggued_on_user'])) echo '
<p> - Hi '.htmlspecialchars($_SESSION['loggued_on_user']).' - </p>
';
else
echo '
<p> - Hello stranger - </p>
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
<form action='delete_account.php' method='post'>
<p>Mot de passe :</p>
<input type='password' name='pwd' value='' />
<input type='submit' name='submit' value='supprimer mon compte' />
</form>
<?PHP


if (isset($_SESSION['loggued_on_user']) && $_SESSION['loggued_on_user'] !== "" && $_SESSION['loggued_on_user'] !== NULL && isset($_POST['pwd']) && $_POST['pwd'] !== "")
	{
		$login = $_SESSION['loggued_on_user'];
		if (isset($_POST['pwd']) && auth($login, hash('whirlpool', $_POST['pwd']), $pdo) === TRUE)
		{
			$stmt = $pdo->prepare("DELETE FROM users WHERE username = :login;");
			$stmt->bindValue(':login', $login, PDO::PARAM_STR);
			$stmt->execute();
			$stmt = $pdo->prepare("DELETE FROM Photos WHERE Author = :login;");
			$stmt->bindValue(':login', $login, PDO::PARAM_STR);
			$stmt->execute();
			echo '<p class="message">Your account was successfully deleted</p>';
			unset($_SESSION['loggued_on_user']);
			header('location: index.php');
		}
		else
		{
			echo '<p class="message">Wrong password</p>';
			exit();
		}
	}
else if (isset($_SESSION['loggued_on_user']) && $_SESSION['loggued_on_user'] !== "")
	echo 'Please write your password';
else
	echo 'You are not loggued on';
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
