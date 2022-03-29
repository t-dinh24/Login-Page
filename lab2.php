<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<title>Lab 2</title>

	<style type="text/css">
		#outerBox{
			border: 1px solid black;
			padding: 10px;
		}

	</style>

	<script>
		var togglecheck = false;

		function toggle()
		{
			if(togglecheck == false)
			{
				document.body.style.backgroundColor = "lightblue";
			}
			else
			{
				document.body.style.backgroundColor = "white";
			}
			togglecheck = !togglecheck;
		}
	</script>
	</head>
	<body>
<!-- ---------------------------------------------------------------------------------- -->
<!-- Database -->
<?php
	$db_host = 'localhost';
	$db_user = 'root';
	$db_password = 'root';
	$db_db = 'Lab2';	// the database to
						// connect to on this server
	$db_port = 8889;

	$mysqli = new mysqli(
		$db_host,
		$db_user,
	    $db_password,
	    $db_db,
	    $db_port	// be careful...you need this!
	);
			
	if ($mysqli->connect_error) 
	{
		echo 'Errno: '.$mysqli->connect_errno;
		echo '<br>';
		echo 'Error: '.$mysqli->connect_error;
	    exit();
	}
//----------------------------------------------------------------------------------
//Check page number if page = 1 load login forms, else load welcome message or errors
	$pageNum = 0;
	//checks if the page value is already set if it is it will go to this value, if not the page will be page 1
	if(isset($_GET["page"]))
	{
		$pageNum = $_GET["page"];
		settype($pageNum, "integer");
	}
	else
	{
		$pageNum =1;
	}
	if($pageNum == 1)
	{
?>
<!-- -------------------------------------------------------------------------------------- -->
<!-- The login form  -->
		<!-- $_SERVER['PHP_SELF'] will call itself and load content based on which page it is on -->
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<!--input will change the value of page to 2 and will load the next page when subit button is pressed-->
			<input type="hidden" name="page" value="2">
			<div id = "outerBox">
				<strong>Create Account</strong><br>
					<label> Username:
						<input type="text" name="newUsername" id="newUsername" value="">
					</label>
					<br><br>
					<label> Password:
						<input type="password" name="newPassword" id="newPassword" value="">
					</label>
					<br><br>
			</div>
			<br>
			<div id = "outerBox">
				<strong>Sign In</strong><br>
					<label> Username:
						<input type="text" name="oldUsername" id="oldUsername" value="">
					</label>
					<br><br>
					<label> Password:
						<input type="password" name="oldPassword" id="oldPassword" value="">
					</label>
					<br><br>
			</div>
			<br>
			<input type="submit" >
		</form>
<?php 
//--------------------------------------------------------------------------------------
//page 2
	}// closing if page == 1
	else
	{
		$newUsername = "";
		$oldUsername = "";
		$newPassword = "";
		$oldPassword = "";
//----------------------------------------------------------------------------------
//CREATING account
		if(isset($_GET["newUsername"]) && $_GET["newUsername"] != "" && isset($_GET["newPassword"]) && $_GET["newPassword"] !="")
		{
			$newUsername = $_REQUEST["newUsername"];
			$newPassword = $_REQUEST["newPassword"];

			$sql = "SELECT username FROM Users WHERE username LIKE '$newUsername%'";
			$stmt = $mysqli->prepare($sql);
			$stmt->execute();
			$result = $stmt->get_result();

			//check the database if the username already exist
			foreach($result as $resultRow)
			{	
				$username =  $resultRow["username"];
				$password =   $resultRow["password"];

				if ($username == $newUsername && $newUsername != "Administrator") 
				{
?>
		<p><strong>Error! This username: <?php echo $newUsername ?> already exists. Please try again with another username</strong></p>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="page" value="1">
			<p><input type="submit" value="back"></p>
		</form>
<?php
					echo ("</body></html");
					die();
				}
				
			}

			//check if username = Administrator, if yes give error saying username cannot be used
			if($newUsername == "Administrator")
			{
?>
		<p><strong>You cannot create an account with username: <?php echo $newUsername ?></strong></p>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="page" value="1">
			<p><input type="submit" value="back"></p>
		</form>
<?php
			echo ("</body></html");
			die();
			}
			//if username does not exist and not Administrator add to database and show welcome message
			else
			{
				$sql = "INSERT INTO Users(username, password) VALUES (?,?)";
				$stmt = $mysqli->prepare($sql);
				$stmt->bind_param("ss", $newUsername, $newPassword);
				$stmt->execute();
?>
		<h1>Welcome!</h1>
		<p>Hello <?php echo $newUsername; ?>, thanks for making an account. Hope to see you back soon!</p>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="page" value="1">
			<input type="button" onclick="toggle()" value="toggle">
			<p><input type="submit" value="Log out"></p>
		</form>
<?php
			}
		}


//----------------------------------------------------------------------------------
//RETURNING user
		else if(isset($_GET["oldUsername"]) && $_GET["oldUsername"] != "" && isset($_GET["oldPassword"]) && $_GET["oldPassword"] != "")
		{
			$usercheck = false;
			$passcheck = false;
			$oldUsername = $_REQUEST["oldUsername"];
			$oldPassword = $_REQUEST["oldPassword"];

			$sql = "SELECT username, password FROM Users WHERE username LIKE '$oldUsername%'";
			$stmt = $mysqli->prepare($sql);
			$stmt->execute();
			$result = $stmt->get_result();

			//check if $oldusername already exists in the database
			foreach($result as $resultRow)
			{	
			//	print("<tr><td>" . $resultRow["artistID"] . "</td>" 
			//		. "<td>" . $resultRow["name"] . "</td></tr>");
				$username =  $resultRow["username"];
				$password =   $resultRow["password"];

				if ($oldUsername == $username) 
				{
					$usercheck = true;
					if ($oldPassword == $password) {
						$passcheck = true;
					}
				}
				
			}
			if($oldUsername == "Administrator" && $usercheck && $passcheck)
			{
?>
		<!-- Printing the Administrator table -->
		<table border="1">
			<thead>
				<tr>
					<td>Username</td>
					<td>Password</td>
				</tr>
			</thead>
			<tbody>
<?php
				$sql = "SELECT username, password FROM Users ORDER BY username ASC";
				$stmt = $mysqli->prepare($sql);
				$stmt->execute();
				$result = $stmt->get_result();

				foreach($result as $resultRow)
				{	
				$username=  $resultRow["username"];
				$password =   $resultRow["password"];
					print("<tr><td>$username</td>" . "<td>$password</td></tr>");
				}
?>		
		
		
			</tbody>
		</table>

		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="page" value="1">
			<p><input type="submit" value="Log out"></p>
		</form>
<?php
				}
				//if username and password match database give welcome message
				else if($usercheck && $passcheck)
				{
?>
		<h1>Welcome Back!</h1>
		<p>Hello <?php echo $oldUsername; ?>, nothing new to report just yet. Check back soon!</p>
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="hidden" name="page" value="1">
			<input type="button" onclick="toggle()" value="toggle">
			<p><input type="submit" value="Log out"></p>
		</form>
<?php
				}
				else if($usercheck && !$passcheck)
				{
					echo ("<p><strong>Password Incorrect.</strong></p></body></html");
					die();
				}
				else
				{
					echo ("<p><strong>The username does not exist.</strong></p></body></html");
					die();
				}
		}
		//if both fields are blank
		else
		{
			echo ("<p><strong>Error! Please create an account or sign if you already have an account.</strong></p></body></html");
			die();
		}
	}//closing else page = 2
  	$mysqli->close();
?>
	</body>
</html>