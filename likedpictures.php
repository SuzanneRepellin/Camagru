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
		<title>Pictures liked by me</title>
	</head>
	<body>
		<div id="header">
			<div id="user">
				<?php if (isset($_SESSION['loggued_on_user'])) echo '
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
				echo '<a href="account.php">account </a>';
		}
?>
</div>
</div>
<?php
if (isset($_SESSION['loggued_on_user']))
{
	$i = 0;
	$login = $_SESSION['loggued_on_user'];
	$stmt = $pdo->prepare("SELECT photo_id FROM likes WHERE user = :login;");
	$stmt->bindValue(':login', $login, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchall();
	$photos = array_reverse($result);
	foreach ($photos as $key => $value)
	{
		$id = $value['photo_id'];
		$stmt = $pdo->prepare("SELECT Data, id, Upload_date, com_nb, author FROM Photos WHERE id = '$id';");
		$stmt->execute();
		$result = $stmt->fetchall();
		$display = $result;
		if ($stmt->rowcount() === 1)
		{
			echo'<div><img class="galpic" src="data:image/png;base64,' . $display[0]['Data'] . '"></div><br>' . '<p class="galtext">Taken by <a href="profile.php?login=' .$display[0]['author']  .  '">' . $display[0]['author'] . '</a> on ' . $display[0]['Upload_date'] . ' </p><p class="galtext"><a href="comment.php?pic_id=' . $display[0]['id'] . '">' . $display[0]['com_nb'] .  ' comment(s)<br>Write a comment</a></p>';
			echo '<p class="galtext"><a href="delete.php?id=' . $display[0]['id'] . '&prev=gal">Delete picture</a></p>';
		}
	}
}
else
	echo '<a href=login.php>You must be loggued on to see your pictures</a>';
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
