<?PHP
session_start();
include 'config/database.php';
include 'config/connect.php';
include 'auth.php';
?>
<!DOCTYPE=html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" href="Camagru.css"/>
		<title>My account</title>
	</head>
	<body>
		<div id="header">
			<div id="user">
				<?php if (isset($_SESSION['loggued_on_user'])) echo '
				<p> - Hi '. $_SESSION['loggued_on_user'] .' - </p>
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
<?php
if (!isset($_SESSION['loggued_on_user']))
	header("location: index.php");
$login = $_SESSION['loggued_on_user'];
$stmt = $pdo->prepare("SELECT usermail FROM users WHERE username = :login;");
$stmt->bindValue(':login', $login, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetchall();
$email = $result[0]['usermail'];
?>
			</div>
		</div>
		<a href='profile.php?login=<?php echo $login;?>' id="myprofile"> My Profile </a><br><br>
	<a href='gallery.php' id="mypictures"> My Pictures </a><br><br>
	<a href='likedpictures.php' id="likedpictures"> Liked Pictures </a><br>
	<form method="post" action="change_data.php">
	<p>Username: 
	<input type="text" name="username" value="<?php echo $_SESSION['loggued_on_user'];?>">
	</p>
	<p>Email: 
	<input type="text" name="email" value="<?php echo $email;?>">
	</p>
	<p>Old password: 
	<input type="password" name="oldpwd" value="">
	</p>
	<p>New password: 
	<input type="password" name="newpwd" value="">
	</p>
	<input type="submit" name="change" value="confirm">
	</form>
	<form method="post" action="change_data.php">
	<form>
	<p>Send an email each time someone comments on one of your pictures:</p>
	<input type="radio" name="comment_mail" value="YES" checked> Yes<br>
	<input type="radio" name="comment_mail" value="NO"> No<br>
	<input type="submit" name="change" value="update">
	</form>
	<p><a href="delete_account.php">Delete your account</a></p>
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
