<?PHP
session_start();
include("config/database.php");
include("config/connect.php");
include("write_com.php");
?>
<!DOCTYPE=html>
<html>
<head>
<meta charset="UTF-8"/>
<link rel="stylesheet" type="text/css" href="camagru.css">
<title>Comments</title>
</head>
<body>
<div id="header">
<div id="user">
<?php if (isset($_SESSION['loggued_on_user'])) echo '
<p> - Hi '. $_SESSION['loggued_on_user'].' - </p>
';
else
	echo '
<p> - Hello stranger - </p>
';?>
</div>
<div id="title">
<a href="index.php">camaGROU<a>
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
/*display des commentaires*/
if (isset($_GET['pic_id']) && $_GET['pic_id'] !== null)
{
	$i = 0;
	$pic_id = $_GET['pic_id'];
	$stmt = $pdo->prepare("SELECT id, Data, Author, Upload_date, com_nb FROM Photos WHERE id = '$pic_id';");
	$stmt->execute();
	if ($stmt->rowcount() > 0)
	{
		$pic = $stmt->fetchall()[0];
		echo'<div><img class="solopic" src="data:image/png;base64,' . $pic['Data'] . '"></div><br>' . '<p class="solotext">Taken by <b><a href="profile.php?login=' . $pic['Author'] . '">'  . $pic['Author'] . '</a></b> on ' . $pic['Upload_date'] . '<br>' . $pic['com_nb'] . ' comment(s)</p>';
		$author = $pic['Author'];
		$stmt = $pdo->prepare("SELECT text, user, date FROM Coms WHERE pic_id = '$pic_id'");
		$stmt->execute();
		if ($stmt->rowcount() > 0)
		{
			$i = 0;
			$result = $stmt->fetchall();
			foreach ($result as $key => $value)
			{
				$display[$i] = $value;
				$i += 1;
			}
			$display = array_reverse($display);
			foreach ($display as $key => $value)
			{
				echo '<p class="com"><b>by <a href="profile.php?login=' . $value['user'] .'">' . $value['user'] . '</a> on ' . $value['date'] . ':</b><br>' . $value['text'] . '</p>';
			}
		}
	/*formulaire de commentaire*/
		if (isset($_SESSION['loggued_on_user']))
		{
			if (isset($_POST['submit']) && $_POST['submit'] == 'submit')
			{
				echo '<a id="cheat" href="comment.php?pic_id=' . $pic_id . '" display="none">lol</a>';
				write_com($_SESSION['loggued_on_user'], htmlspecialchars($_POST['text']), $pic_id, $pdo, $author);
				echo '<script>
var cheat = document.getElementById("cheat");
	cheat.click();
</script>';
			}
		}
		if (isset($_SESSION['loggued_on_user']))
		{
			echo '
			<form method="post">
			<p>Your comment:</p>
			<textarea size="255" id="comfield" type="text" name="text" value="" required></textarea>
			<form>
			<input type="submit" name="submit" value="submit">
			</form>
			</form>
	';
		}
		else
			echo '<a href="login.php">Log in to write a comment</a>';
	}
	else
		echo 'We could not find the picture you are looking for';
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
