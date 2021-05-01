<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'];
$password = $data['password'];


$message = '';

if (!isset($username) || trim($username) == '') {
	$message .= "No Username. ";
}
if (!isset($password) || trim($password) == '') {
	$message .= "No Password. ";
}
if ($message !== '') {
	error_respond(401, $message);
}


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
	error_respond(401, $mysqli->error);
}


$password = hash('sha256', $password);


$sql = "SELECT * FROM user_info WHERE username = '$username' AND password = '$password';";

$result_user = $mysqli->query($sql);
if (!$result_user) {
	error_respond(401, $mysqli->error);
}
if ($result_user->num_rows == 0) {
	error_respond(401, 'Incorrect User Name or Password');
}

//Success
$status_code = 200;
$message = 'Login Success.';
$response = [
	'status_code' => $status_code,
	'message' => $message,
	'user_id' => $result_user['id']
];

echo json_encode($response);

