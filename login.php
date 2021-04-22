<?php
require 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];

//$username = 'test';
//$password = '123';

$status_code = 200;
$message = '';
$response = [];

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

$sql = "SELECT * FROM user_info WHERE username = '$username' AND password = '$password';";

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


if ($result_user->num_rows == 0) {
	$status_code = 401;
	$message = 'Incorrect User Name or Password';
	$response = [
		'status_code' => $status_code,
		'message' => $message,
	];
	echo json_encode($response);
	exit();
}


$status_code = 200;
$message = 'Login Success.';
$response = [
	'status_code' => $status_code,
	'message' => $message,
];

echo json_encode($response);

