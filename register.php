<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$username = $data["username"];
$password = $data["password"];
$email = $data['email'];

//$email = 'test@usc.edu';
//$username = 'test';
//$password = 123;

$status_code = 200;
$message = '';
$response = [];

if (!isset($email) || trim($email) == '') {
	$status_code = 401;
	$message .= 'No Email.\n';
}
if (!isset($username) || trim($username) == '') {
	$status_code = 401;
	$message .= 'No Username.\n';
}
if (!isset($password) || trim($password) == '') {
	$status_code = 401;
	$message .= 'No Password.\n';
}

if ($status_code != 200) {
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
	$status_code = 401;
	$message = $mysqli->connect_error;
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}


$password = hash('sha256', $password);


//Check for dup email
$sql = "SELECT * FROM user_info WHERE email = '$email';";

$result_email = $mysqli->query($sql);

if (!$result_email) {
	$status_code = 401;
	$message = $mysqli->error;
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}

if ($result_email->num_rows != 0) {
	$status_code = 401;
	$message = 'Email already exists.';
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}


//Check for dup username
$sql = "SELECT * FROM user_info WHERE username = '$username';";

$result_user = $mysqli->query($sql);

if (!$result_user) {
	$status_code = 401;
	$message = $mysqli->error;
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}

if ($result_user->num_rows != 0) {
	$status_code = 401;
	$message = 'Username already exists.';
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}


$sql = "INSERT INTO
    	user_info(email, username, password, is_admin, budget)
		VALUES('$email', '$username', '$password', FALSE, 0);";


$result_insert = $mysqli->query($sql);

if (!$result_insert) {
	$status_code = 401;
	$message = $mysqli->error;
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}


$status_code = 200;
$message = 'Register Success.';
$response = [
	'status_code' => $status_code,
	'message' => $message,
];

echo json_encode($response);
exit();

