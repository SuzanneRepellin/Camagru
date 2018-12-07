<?PHP
session_start();
include("config/database.php");
include("config/connect.php");

if ($_SERVER['REQUEST_METHOD'] === "POST")
{
	if ($_POST && isset($_POST['pic']) && $_POST['pic'])
	{
		$user = $_SESSION['loggued_on_user'];
		$result = explode(":", $_POST['pic']);
		if ($result[0] === "undefined")
		{
			$data = $result[1];
			$stmt = $pdo->prepare("INSERT INTO Photos (Data, Author) VALUES ('$data', :user);");
			$stmt->bindValue(':user', $user, PDO::PARAM_STR);
			$stmt->execute();
		}
		else
		{
			$data = $result[1];
			$filter = $result[0];
			$dir = "pics/";
			if (!file_exists($dir))
				mkdir($dir);
			$file1 = $dir . time() . ".png";
			$file2 = $filter . ".png";
			file_put_contents($file1, base64_decode($data));
			$img = imagecreatefrompng($file1);
			$fil = imagecreatefrompng($file2);
			imageAlphaBlending($fil, true);
			imageSaveAlpha($fil, true);
			imagecopy($img, $fil, 0, 0, 0, 0, 320, 240);
			ob_start();
			imagepng($img);
			$finalpic = base64_encode(ob_get_contents());
			ob_end_clean();
			$stmt = $pdo->prepare("INSERT INTO Photos (Data, Author, Filter) VALUES ('$finalpic', :user, '$filter');");
			$stmt->bindValue(':user', $user, PDO::PARAM_STR);
			$stmt->execute();
			unlink($file1);
		}
	}
	header('location: montage.php');
}
?>
