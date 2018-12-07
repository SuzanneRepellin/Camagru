<?php
include 'config/database.php';
include_once 'config/setup.php';
include 'config/connect.php';
session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<link rel="stylesheet" type="text/css" href="camagru.css">
		<title>Camagrou</title>
	</head>
	<body>
		<div id="header">
			<div id="user">
				<?php if (isset($_SESSION['loggued_on_user'])) echo '
				<p> - Hi '.$_SESSION['loggued_on_user'].' - </p>
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
			<a id="takeapicture" href="montage.php"><br>Take a picture<br><br></a>
<div id="fb-root"></div>

<?php
$page = 1;
$limit = 5;
$p = 0;
$followings = NULL;
$stmt = $pdo->prepare("SELECT id FROM photos");
$stmt->execute();
if ($stmt->rowcount() > 0)
{
	if (!isset($_GET['rank']))
		$_GET['rank'] = "date";
	if (!isset($_GET['show']) || $_GET['show'] === "everyone")
	{
		$_GET['show'] = "everyone";
		$total = $stmt->rowcount() % 5 === 0 ? $stmt->rowcount() / 5 + 1 :  (int)($stmt->rowcount() / 5) + 2;
	}
	else if (isset($_SESSION['loggued_on_user']))
	{
		$stmt = $pdo->prepare('SELECT follows FROM users WHERE username = :user;');
		$stmt->bindValue(':user', $_SESSION['loggued_on_user'], PDO::PARAM_STR);
		$stmt->execute();
		if ($stmt->rowcount() > 0)
		{
			$followings = $stmt->fetchall()[0];
			if ($followings['follows'] === NULL)
			{
				$total = 1;
			}
			else
			{
	/* compter le nombre de photos lorsque l'affichage est trie par followings*/
				$followings = explode(":", $followings['follows']);
				$stmt = $pdo->prepare("SELECT id, Author FROM photos");
				$stmt->execute();
				$pho = $stmt->fetchall();
				foreach ($pho as $key => $value)
				{
					$a = 0;
					$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :author;');
					$stmt->bindValue(':author', $value['Author'], PDO::PARAM_STR);
					$stmt->execute();
					if ($stmt->rowcount() > 0)
					{
						$id = $stmt->fetchall()[0]['id'];
						foreach ($followings as $k => $v)
						{
							if ($id == $v)
								$a += 1;
						}
					}
					if ($a === 0)
						$pho[$key] = "";
				}
				$pho = array_filter($pho);
				$total = count($pho) % 5 === 0 ? count($pho) / 5 + 1 : (int)(count($pho) / 5) + 2;
			}
		}
		else
			$total = 1;
	}
	echo '<div id="rankby"><div>Rank pictures by:</div><a class="rankcell" id="datetaken" href="index.php?rank=date&show=' . $_GET['show'] . '">Date taken</a><a class="rankcell" id="mostliked" href="index.php?rank=likes&show=' . $_GET['show'] . '">Most liked</a><a class="rankcell" id="mostcommented" href="index.php?rank=comments&show=' . $_GET['show'] . '">Most commented</a> </div>';
	echo '<div id="showwho"><a id="everyone" href="index.php?rank=' . $_GET['rank'] . '&show=everyone">Everyone</a><a id="following" href="index.php?rank=' . $_GET['rank'] . '&show=following">People I follow</a></div>';
	echo '<script>
var rankcell = document.getElementsByClassName("rankcell");
var datetaken = document.getElementById("datetaken");
var mostliked = document.getElementById("mostliked");
var mostcommented = document.getElementById("mostcommented");';

	$idlike = 0;
	$idunlike = "u" . $idlike;

	/*trier les photos selon la preference selectionnee*/
	if (!isset($_GET['rank']) || $_GET['rank'] === "date")
	{
		$stmt = $pdo->prepare('SELECT id, Data, Author, Upload_date, com_nb, like_nb FROM Photos');
		echo '
		mostliked.style.background = "none";
		mostliked.style.color = "slateblue";
		mostcommented.style.background = "none";
		mostcommented.style.color = "slateblue";
	</script>';
}
	else if ($_GET['rank'] === "likes")
	{
	$stmt = $pdo->prepare('SELECT id, Data, Author, Upload_date, com_nb, like_nb FROM Photos ORDER BY like_nb');
		echo '
		datetaken.style.background = "none";
		datetaken.style.color = "slateblue";
		mostcommented.style.background = "none";
		mostcommented.style.color = "slateblue";
		mostliked.style.background = "lightblue";
		mostliked.style.color = "white";
		mostliked.style.borderRadius = "3px";
	</script>';
}
	else if ($_GET['rank'] === "comments")
	{
		$stmt = $pdo->prepare('SELECT id, Data, Author, Upload_date, com_nb, like_nb FROM Photos ORDER BY com_nb');
		echo '
		datetaken.style.background = "none";
		datetaken.style.color = "slateblue";
		mostliked.style.background = "none";
		mostliked.style.color = "slateblue";
		mostcommented.style.background = "lightblue";
		mostcommented.style.color = "white";
		mostcommented.style.borderRadius = "3px";
	</script>';
}
	else
	{
		$stmt = $pdo->prepare('SELECT id, Data, Author, Upload_date, com_nb, like_nb FROM Photos');
		echo '
		mostliked.style.background = "none";
		mostliked.style.color = "slateblue";
		mostcommented.style.background = "none";
		mostcommented.style.color = "slateblue";
		datetaken.style.background = "lightblue";
		datetaken.style.color = "white";
		datetaken.style.borderRadius = "3px";
	</script>';
	}
if ($_GET['show'] === "following")
{
	echo '<script>
var everyone = document.getElementById("everyone");
var following = document.getElementById("following");
	everyone.style.color = "slateblue";
	everyone.style.background = "none";
	following.style.color = "white";
	following.style.background = "pink";
	following.style.borderRadius = "3px";
</script>';
}
	$stmt->execute();
	$result = $stmt->fetchall();
	$i = 0;
	$photos = NULL;
	foreach ($result as $key => $value)
	{
		if ($_GET['show'] === 'following')
		{
			if ($followings)
			{
				$followings = array_filter($followings);
				$a = 0;
				$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :author;');
				$stmt->bindValue(':author', $value['Author'], PDO::PARAM_STR);
				$stmt->execute();
				if ($stmt->rowcount() > 0)
				{
					$id = $stmt->fetchall()[0]['id'];
					foreach ($followings as $k => $v)
					{
						if ($id == $v)
						{
							$a += 1;
						}
					}
				}
				if ($a !== 0)
				{
					$photos[$i] = $value;
					$i += 1;
				}
			}
		}
		else
		{
			$photos[$i] = $value;
			$i += 1;
		}
	}
	if (!$photos)
	{
		echo 'Follow some people to see their pictures here';
	}
	else
	{
		$photos = array_reverse($photos);
	/*afficher les photos, en fonction de la page*/
		foreach ($photos as $key => $value)
		{
			$p += 1;
			if ($p > $limit)
			{
				$page += 1;
				$p = 1;
			}
			if ((isset($_GET['page']) && $_GET['page'] == $page) || (!isset($_GET['page']) && $page === 1))
				{
			echo'<div><img class="galpic" src="data:image/png;base64,' . $value['Data'] . '"></div><br>' . '<p class="galtext">Taken by <b><a href="profile.php?login=' . $value['Author'] . '">' . $value['Author'] . '</a></b> on ' . $value['Upload_date'] . ' </p><p class="galtext"><a href="comment.php?pic_id=' . $value['id'] . '">' . $value['com_nb'] .  ' comment(s) and ' . $value['like_nb'] . ' like(s)<br>Write a comment</a></p>';
				if (isset($_SESSION['loggued_on_user']))
				{
					$user = $_SESSION['loggued_on_user'];
					if ($user === $value['Author'])
					{
						echo '<p class="galtext"><a href="delete.php?id=' . $value['id'] . '">Delete picture</a></p>';
					}
					$photo_id = $value['id'];
					$stmt = $pdo->prepare("SELECT id FROM likes WHERE photo_id = '$photo_id' AND user = :user;");
					$stmt->bindValue(':user', $user, PDO::PARAM_STR);
					$stmt->execute();
					if ($stmt->rowcount() === 1)
					{
						echo '<p class="unlike" id="' . $idunlike . '">♡</p>
							<p class="like" id="l' . $idlike . '" style="display:none">♡</p>
						<script>
						function likeornot(idlike, idunlike){
							var url = "like.php";
							var xml = new XMLHttpRequest();
							var unlike = document.getElementById(idunlike);
							var like = document.getElementById(idlike);
							unlike.addEventListener("click", unlikeHandler, false);
							like.addEventListener("click", likeHandler, false);
							function unlikeHandler() {
								unlike.style.display = "none";
								like.style.display = "block";
								xml.open("POST", url);
								xml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
								if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
								};
								xml.send("' . $photo_id . '");
							};
							function likeHandler() {
								like.style.display = "none";
								unlike.style.display = "block";
								xml.open("POST", url);
								xml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
								if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
								};
								xml.send("' . $photo_id . '");
							};
						};
						likeornot("l' . $idlike . '", "' . $idunlike . '");
						</script>
			';
						$idlike++;
						$idunlike = "u" . $idlike;
					}
					else
					{
						echo '<p class="like" id="l' . $idlike . '">♡</p>
							<p class="unlike" id="' . $idunlike . '" style="display:none">♡</p>
						<script>
						function likeornot(idlike, idunlike){
							var url = "like.php";
							var xml = new XMLHttpRequest();
							var unlike = document.getElementById(idunlike);
							var like = document.getElementById(idlike);
							unlike.addEventListener("click", unlikeHandler, false);
							like.addEventListener("click", likeHandler, false);
							function unlikeHandler() {
								unlike.style.display = "none";
								like.style.display = "block";
								xml.open("POST", url);
								xml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
								if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
								};
								xml.send("' . $photo_id . '");
							};
							function likeHandler() {
								like.style.display = "none";
								unlike.style.display = "block";
								xml.open("POST", url);
							xml.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
							if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
							};
							xml.send("' . $photo_id . '");
						};
					};
					likeornot("l' . $idlike . '", "' . $idunlike . '");
		</script>
		';
					$idlike++;
					$idunlike = "u" . $idlike;
				}
			}
		}
	}

		/*pagination*/
		$num = 1;
		$total = (int)($total - 1);
		 if ($total)
		 {
			echo '<div id="pagination"><div class="pagenum"><a href="index.php?rank=' . $_GET['rank'] . '&show=' . $_GET['show'] . '"><<</a></div>';
			while ($num <= $total)
			{
				if ((isset($_GET['page']) && $_GET['page'] == $num) || (!isset($_GET['page']) && $num === 1))
					echo '<div id="currentpage">' . $num . '</div>';
				else
				{
					if (isset($_GET['rank']))
					{
						echo '<div class="pagenum"><a href="index.php?rank=' . $_GET['rank'] . '&show=' . $_GET['show'] . '&page=' . $num . '">' . $num . '</a></div>';
					}
					else
						echo '<div class="pagenum"><a href="index.php?show=' . $_GET['show'] . '&page=' . $num . '">' . $num . '</a></div>';
				}
				$num += 1;
			}
			echo '<div class="pagenum"><a href="index.php?rank=' . $_GET['rank'] . '&show=' . $_GET['show'] . '&page=' . $total .'">>></a></div>';
			echo '</div>';
		}
	}
}
else
{
	echo '<br>Take some pictures to see theme here';
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
