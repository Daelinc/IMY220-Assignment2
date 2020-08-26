<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

	if(isset($_FILES["picToUpload"])){
		$count = count($_FILES['picToUpload']['name']);
		
		for($i = 0; $i < $count; $i++){
			$size = $_FILES["picToUpload"]["size"][$i];
			$type = $_FILES["picToUpload"]["type"][$i];
			$name = $_FILES["picToUpload"]['name'][$i];
			$location = "gallery/".$name;
			$saveName = $_FILES["picToUpload"]["tmp_name"][$i];
			if($size < 1048576 && $type == "image/jpeg"){
				move_uploaded_file($saveName, $location);

				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$user_id = $row["user_id"];	
				}
				$query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$user_id','$name');";
				mysqli_query($mysqli, $query);
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Daelin Campleman">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					$user_id = $row["user_id"];	
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form method='POST' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='hidden' name='loginEmail' value='" . $email . "' />
									<input type='hidden' name='loginPass' value='" . $pass . "' />
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' accept='.jpg, .jpeg' multiple /><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";
					
					$query = "SELECT * FROM tbgallery WHERE user_id = '$user_id'";
					$res = $mysqli->query($query);
					echo 	"<h1>Image Gallery</h1>
							<div class='row imageGallery'>";
					
					while($row = mysqli_fetch_array($res)){
						$image = $row["filename"];
						$link = "gallery/" . $image;
						echo	"<div class='col-3' style='background-image: url($link)'></div>";
					}
					
					echo	"</div>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>