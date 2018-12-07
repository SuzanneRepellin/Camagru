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
<?php
session_start();
include("config/database.php");
include("config/connect.php");
if (isset($_SESSION['loggued_on_user'])) echo '
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
if (!isset($_SESSION['loggued_on_user']))
{
	if (isset($_GET['user']) && isset($_GET['hash']))
	{
		$user = $_GET['user'];
		$hash = $_GET['hash'];
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = '$user' AND reset_link = '$hash';");
		$stmt->execute();
		if ($stmt->rowcount() == 1)
		{
			if (isset($_POST['pwd']) && isset($_POST['checkpwd']) && $_POST['pwd'] === $_POST['checkpwd'])
			{
				$newpwd = hash('whirlpool', $_POST['pwd']);
				$stmt = $pdo->prepare("UPDATE users SET password = '$newpwd' WHERE username = '$user';");
				$stmt->execute();
				echo '<p>Your password was successfully changed!</p>';
			}
			else
			{
				if (isset($_POST['pwd']) && isset($_POST['checkpwd']) && $_POST['pwd'] !== $_POST['checkpwd'])
				{
					echo '<p class="message">Wrong password check</p>';
				}
				echo '
				<h2>Change password</h2>
				<form method="post">
				<p>New password:</p>
				<input type="password" name="pwd" value="" required/>
				<p>Check new password:</p>
				<input type="password" name="checkpwd" value="" required/>
				<input type="reset" value="Cancel">
				<form>
				<input type="submit" name="create" value="submit">
				</form>
				</form>
			';
			}
		}
	}
}
else
{
	echo 'You are not allowed to perform this action';
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
