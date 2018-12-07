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
include("auth.php");
include 'is_secure_mdp.php';
if (isset($_SESSION['loggued_on_user'])) echo '
<p> - Hi ' . $_SESSION['loggued_on_user'] .' - </p>
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
<div id="loginpage">
<?PHP

/*pour verifier le mot de passe, le nom d'utilisateur et que le compte est confirme*/
if (isset($_POST['account']) && $_POST['account'] == 'login')
{
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$usrn = htmlspecialchars($_POST['login']);
		$pwd = hash('whirlpool', $_POST['pwd']);
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :usrn AND confirmed = 'YES';");
		$stmt->bindValue(':usrn', $usrn, PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowcount() > 0)
		{
			if (auth($usrn, $pwd, $pdo) === TRUE)
			{
				$_SESSION['loggued_on_user'] = $usrn;
				header('location: index.php');
			}
			else
			{
				echo '<div>Wrong username or passworld</div>';
			}
		}
		else
			echo '<p>This username does not exist</p>';
	}
}
/*creer un compte et envoyer un mail de validation*/
else if (isset($_GET['account']) && $_GET['account'] == "create")
{
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if (strstr($_POST['login'], "<") || strstr($_POST['login'], ">") || strstr($_POST['login'], "&"))
		{
			echo '<p>Your username cannot have "<", ">" and "&" in it</p>';
		}
		else
		{
			$usrn = htmlspecialchars($_POST['login']);
			$usrmail = $_POST['email'];
			$pwd = $_POST['pwd'];
			$result = $pdo->prepare("SELECT id FROM users WHERE username = :usrn OR usermail = :usrmail");
			$result->bindValue(':usrn', $usrn, PDO::PARAM_STR);
			$result->bindValue(':usrmail', $usrmail, PDO::PARAM_STR);
			$result->execute();
			if (!filter_var($usrmail, FILTER_VALIDATE_EMAIL))
			{
				print("Not a valid email adress\n");
			}
			else if (!isset($_POST['pwd']) || !isset($_POST['checkpwd']) || $_POST['pwd'] != $_POST['checkpwd'])
			{
				print("Bad password check\n");
			}
			else if (is_secure_mdp($pwd) === FALSE)
			{
				echo 'Your password is not strong enough (at list 8 characters with numbers et letters)';
			}
			else if ($result->rowcount() > 0)
			{
				print("This username or email address is already taken\n");
			}
			else
			{
	/*generer un lien unique pour l'utilisateur*/
				$conf_link_rand = md5(gettimeofday(TRUE));
				$conf_link = "http://localhost:8008/confirm.php?method=GET&user=" . $usrn . "&hash=" . $conf_link_rand;
				$pwd = hash('whirlpool', $_POST['pwd']);
				$newuser = $pdo->prepare("INSERT INTO users (username, password, usermail, link) VALUES (:usrn, '$pwd', :usrmail, '$conf_link_rand');");
				$newuser->bindValue(':usrn', $usrn, PDO::PARAM_STR);
				$newuser->bindValue(':usrmail', $usrmail, PDO::PARAM_STR);
				$newuser->execute();
				mail($usrmail, 'Email validation for your Camagru account', "Dear $usrn,\nThank you for joining the Camagru family. To confirm your email address, please click on the following link:\n$conf_link\nBest regards,\nThe Camagru Team.\n");
				print("A link was sent to your emailbox in order to confirm your email adress\n");
				header('location: index.php');
			}
		}
	}
}
/*formulaire de connexion*/
if (!isset($_SESSION['loggued_on_user']) && !isset($_GET['account']))
{
	echo '
	<h2>Login</h2>
	<form method="post">
	<p>Username:</p>
	<input type="text" name="login" value="" required/>
	<p>Password:</p>
	<input type="password" name="pwd" value="" required/>
	<input type="reset" value="Cancel">
	<form>
	<input type="submit" name="account" value="login">
	</form>
	</form>

	<form method="get">
	<p>Créer un compte</p>
	<input type="submit" name="account" value="create">
	</form>
	<p><a href="reset_pwd.php">Forgot your password?</a></p>
	';
}
/*formulaire de creation de compte*/
else if (isset($_GET['account']) && $_GET['account'] = "create")
{
	echo '
	<h2>Create account</h2>
	<form method="post">
	<p>Email:</p>
	<input type="text" name="email" value="" required/>
	<p>Username:</p>
	<input type="text" name="login" value="" required/>
	<p>Password:</p>
	<input type="password" name="pwd" value="" required/>
	<p>Check password:</p>
	<input type="password" name="checkpwd" value="" required/>
	<input type="reset" value="Cancel">
	<form>
	<input type="submit" name="create" value="submit">
	</form>
	</form>
';
}
else
	header('location: index.php');
?>
</div>
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
