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

$request = 1;
if(isset($_POST['request'])){
    $request = $_POST['request'];
}


// DataTable data
if($request == 1){
    ## Read value
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc

    $searchValue = mysqli_escape_string($con,$_POST['search']['value']); // Search value

    ## Search 
    $searchQuery = " ";
    if($searchValue != ''){
        $searchQuery = " and (firstname like '%".$searchValue."%' or 
            lastname like '%".$searchValue."%' or 
            concat(firstname,' ', lastname) like'%".$searchValue."%' ) ";
    }

    ## Total number of records without filtering
    $sel = mysqli_query($con,"select count(*) as allcount from staff");
    $records = mysqli_fetch_assoc($sel);
    $totalRecords = $records['allcount'];

    ## Total number of records with filtering
    $sel = mysqli_query($con,"select count(*) as allcount from staff WHERE 1 ".$searchQuery);
    $records = mysqli_fetch_assoc($sel);
    $totalRecordwithFilter = $records['allcount'];

    ## Fetch records
    $staffQuery = "select * from staff WHERE 1 ".$searchQuery." order by ".$columnName." ".$columnSortOrder." limit ".$row.",".$rowperpage;
    $staffRecords = mysqli_query($con, $staffQuery);
    $data = array();


    while ($row = mysqli_fetch_assoc($staffRecords)) {

        // Update Button
        $updateButton = "<button class='btn btn-sm btn-info updateUser' data-id=".$row['id']." data-toggle='modal' data-target='#updateModal' >Update</button>";

       
               
        $action = $updateButton;

        $data[] = array(
        		"id" => $row['id'],
                "firstname" => $row['firstname'],
                "lastname" => $row['lastname'],
                "dob" => $row['dob'],
                "created" => $row['created'],
                "last_updated" => $row['last_updated'],
                "is_user" => $row['is_user'],
                "action" => $action
            );
    }

    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);
    exit;
}

// Fetch user details
if($request == 2){
    $id = 0;

    if(isset($_POST['id'])){
        $id = mysqli_escape_string($con,$_POST['id']);
    }

    $record = mysqli_query($con,"SELECT * FROM staff WHERE id=".$id);
    $recordUser = mysqli_query($con,"SELECT * FROM users WHERE id=".$id);

    $response = array();

    if(mysqli_num_rows($record) > 0){
        $row = mysqli_fetch_assoc($record);
        $rowUser = mysqli_fetch_assoc($recordUser);
        $response = array(
        	"id" => $row['id'],
			"firstname" => $row['firstname'],
			"lastname" => $row['lastname'],
			"dob" => $row['dob'],
			"created" => $row['created'],
			"last_updated" => $row['last_updated'],
			"is_user" => $row['is_user'],
			"username" => $rowUser['username'],
			"password" => $rowUser['password']

        );

        echo json_encode( array("status" => 1,"data" => $response) );
        exit;
    }

    else{
		echo json_encode( array("status" => 0) );
		exit;
    }
}


// Update user
if($request == 3){
    $id = 0;

    if(isset($_POST['id'])){
        $id = mysqli_escape_string($con,$_POST['id']);
    }

    // Check id
    $record = mysqli_query($con,"SELECT id FROM staff WHERE id=".$id);
    $recordUser = mysqli_query($con,"SELECT id FROM users WHERE id=".$id);

    if(mysqli_num_rows($record) > 0 ){
		$id = mysqli_escape_string($con,trim($_POST['id']));
        $firstname = mysqli_escape_string($con,trim($_POST['firstname']));
        $lastname = mysqli_escape_string($con,trim($_POST['lastname']));
        $dob = mysqli_escape_string($con,trim($_POST['dob']));
        $is_user = mysqli_escape_string($con,trim($_POST['is_user']));
        $username = mysqli_escape_string($con,trim($_POST['username']));
        $password = mysqli_escape_string($con,trim($_POST['password']));
        $encryptPW = password_hash($password, PASSWORD_DEFAULT);

        if( $firstname != '' && $lastname != '' && $dob != '' && $is_user != ''){

            mysqli_query($con,"UPDATE staff SET firstname='".$firstname."',lastname='".$lastname."',dob='".$dob."',is_user='".$is_user."' WHERE id=".$id);
            //mysqli_query($con,"UPDATE users SET username='".$username."',password='".$password."' WHERE id=".$id);
            // mysqli_query($con,"INSERT INTO users(id, username, password, ) VALUES( (SELECT id FROM staff WHERE id = $id ),'$username', '$password')");

            mysqli_query($con, "INSERT INTO users (id, username, password) VALUES ((SELECT id FROM staff WHERE id = $id ),'$username', '$encryptPW') ON DUPLICATE KEY UPDATE username = '$username', password = '$encryptPW' ");

            echo json_encode( array("status" => 1,"message" => "Record updated.") );
            exit;
        }

        else{
            echo json_encode( array("status" => 0,"message" => "Please fill all fields.") );
            exit;
        }
        
    }
    else{
        echo json_encode( array("status" => 0,"message" => "Invalid ID.") );
        exit;
    }
}


?>



