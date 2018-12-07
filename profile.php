<?PHP
session_start();
include 'config/database.php';
include 'config/connect.php';
include 'auth.php';
include 'follow.php';
?>
<!DOCTYPE=html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" href="Camagru.css"/>
		<title><?php

if (isset($_GET['login']))
{
	echo $_GET['login'];
}
else
	echo 'profile';
?>
</title>
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
	</div>
</div>
<?php
if (isset($_SESSION['loggued_on_user']))
	$user = $_SESSION['loggued_on_user'];
else
	$user = NULL;
if (isset($_GET['login']))
{
	$fon = false;
	$login = $_GET['login'];
	$check = $pdo->prepare('SELECT id FROM users WHERE username = :login;');
	$check->bindValue(':login', htmlspecialchars($login), PDO::PARAM_STR);
	$check->execute();
	if ($check->rowcount() === 0)
		echo 'We could not find the user you are looking for';
	else
	{
		$id = $check->fetchall()[0]['id'];
		$fon = follow_or_not($pdo, $user, $id);
		if ($fon === true)
		{
			$fodis = 'style="display:none"';
			$unfodis = 'style="display:block"';
		}
		else if ($fon === false)
		{
			$fodis = 'style="display:block"';
			$unfodis = 'style="display:none"';
		}
		echo '<div id="profileparent"><div id="uprofile"><br><a href="profile.php?login=' . $login .'">' . htmlspecialchars($login) . '\'s profile</a><br><br></div><br><br>';
		echo '
			<a href="profile.php?login=' . $login .'&display=pictures" id="upictures"> ' . htmlspecialchars($login) . '\'s Pictures </a><br>';
		echo '<a href="profile.php?login=' . $login .'&display=likes" id="ulikedpictures"> ' . htmlspecialchars($login) . '\'s Liked Pictures </a><br>';
		echo '<form method="post"><button type=input name="follow" value="yes" id="follow" ' . $fodis . '>' .'Follow ' . htmlspecialchars($login) . '</button></form><br>';
		echo '<form method="post"><button type=input name="follow" value="no" id="unfollow"' . $unfodis . '>' .'Unfollow ' . htmlspecialchars($login) . '</button></form><br></div>';
		if (isset($_POST['follow']))
		{
			if ($_POST['follow'] === "yes" && $fon === false)
			{
				if (!isset($_SESSION['loggued_on_user']))
					echo '<p>You must be loggued on to follow a user</p>';
				else
				{
					follow($pdo, $user, $id);
					$fon = true;
					header('Refresh: 0');
				}
			}
			else if ($_POST['follow'] === "no" && $fon === true)
			{
				unfollow($pdo, $user, $id);
				$fon = false;
				header("Refresh: 0");
			}
		}
		if (isset($_GET['display']) && $_GET['display'] === "pictures")
		{
			$pics = $pdo->prepare("SELECT id, Data, Author, Upload_date, com_nb FROM Photos WHERE Author = :login;");
			$pics->bindValue(':login', htmlspecialchars($login), PDO::PARAM_STR);
			$pics->execute();
			if ($pics->rowcount() > 0)
			{
				$display = array_reverse($pics->fetchall());
				foreach ($display as $key => $value)
					{
						echo'<div><img class="galpic" src="data:image/png;base64,' . $value['Data'] . '"></div><br>' . '<p class="galtext">Taken by ' . htmlspecialchars($login) .' on ' . $value['Upload_date'] . ' </p><p class="galtext"><a href="comment.php?pic_id=' . $value['id'] . '">' . $value['com_nb'] .  ' comment(s)<br>Write a comment</a></p>';
						if ($login === $user)
							echo '<p class="galtext"><a href="delete.php?id=' . $value['id'] . '&prev=gal">Delete picture</a></p>';
				}
			}
			else
				echo 'There\'s nothing to see here (yet!)';
		}
		else if (isset($_GET['display']) && $_GET['display'] === "likes")
		{
			$likes = $pdo->prepare("SELECT photo_id FROM likes WHERE user = :login;");
			$likes->bindValue(':login', htmlspecialchars($login), PDO::PARAM_STR);
			$likes->execute();
			if ($likes->rowcount() > 0)
			{
				$result = array_reverse($likes->fetchall());
				foreach ($result as $key => $value)
				{
					$id = $value['photo_id'];
					$pics = $pdo->prepare("SELECT id, Data, Author, Upload_date, com_nb FROM Photos WHERE id = '$id';");
					$pics->bindValue(':login', htmlspecialchars($login), PDO::PARAM_STR);
					$pics->execute();
					if ($pics->rowcount())
					{
						$display = array_reverse($pics->fetchall())[0];
						echo'<div><img class="galpic" src="data:image/png;base64,' . $display['Data'] . '"></div><br>' . '<p class="galtext">Taken by ' . $display['Author'] . ' on ' . $display['Upload_date'] . ' </p><p class="galtext"><a href="comment.php?pic_id=' . $display['id'] . '">' . $display['com_nb'] .  ' comment(s)<br>Write a comment</a></p>';
						if ($display['Author'] === $user)
							echo '<p class="galtext"><a href="delete.php?id=' . $display['id'] . '&prev=gal">Delete picture</a></p>';
					}
				}
			}
			else
				echo 'There\'s nothing to see here (yet!)';
		}
	}
}
else
	echo 'We could not find the user you are looking for';
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
