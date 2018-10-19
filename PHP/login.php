<?php
/* login.php
 * Login form for QuizMatch
 * Users should be able to use either their email or username to sign in.
 * Creates a new session and redirects user to profile welcome page (user_welcome.php/html)
 */
 
 // Checks to see if USER is already signed into an account
session_start();
 
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
	// Forces USER back into user welcome page if they are already signed into an account
	header("location: user_welcome.php");
	exit;
}
 
// Beginning of True Login process
require_once "config.php";
 
$profile_err = $password_err = "";
$profile = $password = "";
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	// Verifies both fields are filled. Will output an error to USER if either are empty
	if(empty(trim($_POST["profile"])))
	{
		$profile_err = "Please enter your username or email.";
	}else{
		$profile = mysql_escape_string(trim($_POST["profile"]));
	}
	if(empty(trim($_POST["password"])))
	{
		$password_err = "Please enter your password.";
	}else{
		$password = mysql_escape_string(trim($_POST["password"]));
	}
	
	// Begins core login if and only if both fields have been filled out.
	if(empty($profile_err) && empty($password_err))
	{
		// Check to see if the profile field is an EMAIL. Assumes string is a USERNAME if not an EMAIL. Begins preparation to access Database
		if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", mysql_escape_string(trim($_POST["profile"]))))
		{
			// Is an EMAIL
			$sql = "SELECT id, username, password FROM users WHERE email = ?";
		}else{
			// Is a USERNAME
			$sql = "SELECT id, username, password FROM users WHERE username = ?";
		}
		if($stmt = mysqli_prepare($link, $sql))
		{
			mysqli_stmt_bind_param($stmt, "s", $param_profile);
			$param_profile = $profile;
			// Access Database
			if(mysqli_stmt_execute($stmt))
			{
				// Access granted; execution successful
				mysqli_stmt_store_result($stmt);
				if(mysqli_stmt_num_rows($stmt) == 1)
				{
					// Profile Found
					mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
					// Beginning of Password Verification
					if(mysqli_stmt_fetch($stmt))
					{
						if(password_verify($password, $hashed_password))
						{
							// Password matches recorded
							session_start();
							$_SESSION["loggedin"] = true;
							$_SESSION["id"] = $id;
							$_SESSION["username"] = $username;
							header("location: user_welcome.php");
						}else{
							// Password does not match recorded
							$password_err = "The password you entered was not valid.";
						}
					}
				}else{
					// Profile not found
					$profile_err = "No account was found with this username/email.";
				}
			}else{
				// Connection error
				echo "An Error has occured. Please try again later.";
			}
		}
		mysqli_stmt_close($stmt);
	}
	mysqli_close($link);
}
?>