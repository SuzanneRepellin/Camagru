<?php
include 'config/database.php';
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

<?php
if (isset($_SESSION['loggued_on_user']))
{
	echo '
<div id="bigbloc">
	<div id="blocvideocanv">
		<video id="video" autoplay="autoplay"></video>
		<div><img  class="filter"id="flowers" src="flowers.png"></div>
		<div><img  class="filter"id="stars" src="stars.png"></div>
		<div><img  class="filter"id="cats" src="cats.png"></div>
		<div><img  class="filter" id="beer" src="beer.png"></div>
		<div><img  class="filter" id="pikachu" src="pikachu.png"></div>
		<div><img  class="filter" id="crown" src="crown.png"></div>
		<div><img  class="filter" id="101" src="101.png"></div>
		<div><img  class="filter" id="catsears" src="catsears.png"></div>
		<button id="startbutton">Take Picture</button>
		<canvas id="canvas"></canvas>
		<button id="savepicture" style="display:none">Save Picture</button>
		<input type="file" id="uploadpic" name="upload"></input>
	</div>
	<div id="filterslist">
<p id="selectfilter"> Select your filter</p>
		<table id="filterstab">
			<tr>
				<td>
					<img class="filtersap" id="flowersap" src="flowers.png">
				</td>
				<td>
					<img class="filtersap" id="catsap" src="cats.png">
				</td>
				<td>
					<img class="filtersap" id="starsap" src="stars.png">
				</td>
			</tr>
			<tr>
				<td>
					<img class="filtersap" id="beerap" src="beer.png">
				</td>
				<td>
					<img class="filtersap" id="pikachuap" src="pikachu.png">
				</td>
				<td>
					<img class="filtersap" id="crownap" src="crown.png">
				</td>
			</tr>
			<tr>
				<td>
					<img class="filtersap" id="101ap" src="101.png">
				</td>
				<td>
					<img class="filtersap" id="catsearsap" src="catsears.png">
				</td>
				<td>
					<div class="filtersap" id="nofilter">none</div>
				</td>
			</tr>
		</table>
	</div>
</div>
	<div id="minigal">';
$user = $_SESSION['loggued_on_user'];
$stmt = $pdo->prepare("SELECT Data, id FROM Photos WHERE Author = :user;");
$stmt->bindValue(':user', $user, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->rowcount() === 0)
{
	echo 'Take some pictures to see them here!';
}
else
{
	$photonb = $stmt->rowcount();
	$result = array_reverse($stmt->fetchall());
	foreach ($result as $key => $value)
	{
		echo '<a href="http://localhost:8008/comment.php?pic_id=' . $value['id'] . '"><img class="minipic" src="data:image/png;base64,' . $value['Data'] . '"></a>';
	}
}
echo '	</div>
<script>
		var video = document.getElementById("video"),
			cover = document.getElementById("cover"),
			canvas = document.getElementById("canvas"),
			photo = document.getElementById("photo"),
			uploadpic = document.getElementById("uploadpic"),
			startbutton = document.getElementById("startbutton"),
			savepicture = document.getElementById("savepicture"),
			minigal = document.getElementById("minigal"),
			filters = Array.prototype.slice.call(document.getElementsByClassName("filter"));
			window.filter = null;
			window.filtertmp = null;
			nofilter = document.getElementById("nofilter");
		filters.forEach(function(element) {
			var ap = document.getElementById(element.id + "ap");
			ap.addEventListener("click", function() {
				filters.forEach(function(el) {
				document.getElementById(el.id + "ap").style.border = "2px solid white";
				el.style.display = "none";
				nofilter.style.border = "2px solid white";
				});
			window.filtertmp = element;
			ap.style.border = "2px solid red";
			element.style.display = "block";
			canvas.style.position = "relative";
			canvas.style.top = "-280px";
			startbutton.style.position = "relative";
			startbutton.style.top = "-280px";
			savepicture.style.position = "relative";
			savepicture.style.top = "-280px";
			uploadpic.style.position = "relative";
			uploadpic.style.top = "-280px";
			minigal.style.position = "relative";
			minigal.style.top = "-280px";
		}, false);
	});
	nofilter.addEventListener("click", function() {
		filters.forEach(function(el) {
			document.getElementById(el.id + "ap").style.border = "2px solid white";
			el.style.display = "none";
		});
		window.filtertmp = "nofilter";
		window.filter = "nofilter";
		canvas.style.position = "static";
		startbutton.style.position = "static";
		savepicture.style.position = "static";
		uploadpic.style.position = "static";
		minigal.style.position = "static";
		nofilter.style.border = "2px solid red";
});
</script>
</div>
	<script type="text/javascript" charset="utf-8">
		(function () {
		var Go = false;
		var streaming = false,
			width = 320,
			height = 240;
			var uploadpic = document.getElementById("uploadpic");
			var ctx = canvas.getContext("2d");
			navigator.mediaDevices.getUserMedia(
			{
				video: true,
				audio: false
			})
			.then(function(stream) {
				video.srcObject = stream;
				video.play();
				startbutton.style.display = "block";
			})
			.catch(function(err) {
				console.log(err.name + ":" + err.message);
			});
			video.addEventListener("canplay", function(ev) {
				if (!streaming) {
					height = video.videoHeight / (video.videoWidth/width);
					video.setAttribute("width", width);
					video.setAttribute("height", height);
					canvas.setAttribute("width", width);
					canvas.setAttribute("height", height);
				}
			}, false);
			function clickhandler(ev) {
				takepicture();
				ev.preventDefault();
				savepicture.style.display = "block";
				uploadpic.style.marginTop = "20px";
			};
			function takepicture() {
			canvas.width = width;
			canvas.height = height;
			ctx.drawImage(video, 0, 0, width, height);
			window.Pic = canvas.toDataURL("image/png");
			window.Pic = Pic.replace(/^data:image\/(png|jpg);base64,/, "");
			if (window.filtertmp !== null && window.filtertmp !== "nofilter")
			{
				window.filter = window.filtertmp;
				ctx.drawImage(window.filter, 0, 0, width, height);
			}
		}
		startbutton.addEventListener("click", clickhandler, false);
		uploadpic.addEventListener("change", handlePic, false);
		function handlePic(e){
			var img = new Image();
			img.onload = draw;
			img.src = URL.createObjectURL(this.files[0]);
			savepicture.style.display = "block";
			video.style.display = "none";
			startbutton.style.display = "none";
			window.filter = window.filtertmp;
			filters.forEach(function(element) {
				element.style.top = "8px";
			});
		}
		function draw() {
			var canvas = document.getElementById("canvas");
			canvas.width = width;
			canvas.height = height;
			ctx.drawImage(this, 0, 0, width, height);
			window.Pic = canvas.toDataURL("image/png");
			window.Pic = Pic.replace(/^data:image\/(png|jpg);base64,/, "");
			if (window.filtertmp !== null && window.filtertmp !== "nofilter" && uploadpic.style.marginTop !== "20px")
			{
				window.filter = window.filtertmp;
				ctx.drawImage(window.filter, 0, 0, width, height);
			}
			savepicture.style.display = "block";
			uploadpic.style.marginTop = "20px";
		}
		savepicture.addEventListener("click", function(ev) {
					if ((window.filter === null && startbutton.style.display === "block") || (startbutton.style.display === "none" && window.filtertmp === null)) {
						alert("Select a filter before saving your picture");
						return (0);
					}
					if (startbutton.style.display === "none") {
						window.filter = window.filtertmp;
					}
					var str = `<form id="savepic" action="save_pic.php" method="post" style="display:none"><input type="text" name="pic" value="` + window.filter.id + `:` + window.Pic + `"></form>`;
					document.write(str);
					document.getElementById("savepic").submit();
		})
	})();
	</script>
';
}
else
echo'<a href="login.php">You must be loggued on to take a picture</a>';
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
