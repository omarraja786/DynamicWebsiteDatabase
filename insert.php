
<?php
session_start();
include_once('_class/database.class.php');
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if(isset($_POST["firstname"], $_POST["lastname"], $_POST["dob"], $_POST["is_user"] ))
{
 $firstname = mysqli_real_escape_string($con, $_POST["firstname"]);
 $lastname = mysqli_real_escape_string($con, $_POST["lastname"]);
 $dob = mysqli_real_escape_string($con, $_POST["dob"]);
 $is_user = mysqli_real_escape_string($con, $_POST["is_user"]);

 $query = "INSERT INTO staff(firstname, lastname, dob, is_user) VALUES('$firstname', '$lastname', '$dob', '$is_user')";

 if(mysqli_query($con, $query))
 {
  echo 'Data Inserted';
 }
}
?>