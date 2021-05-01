<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'];
$password = $data['password'];
$email = $data['email'];

$message = '';

if (!isset($email) || trim($email) == '') {
	$status_code = 401;
	$message .= 'No Email. ';
}
if (!isset($username) || trim($username) == '') {
	$status_code = 401;
	$message .= 'No Username. ';
}
if (!isset($password) || trim($password) == '') {
	$status_code = 401;
	$message .= 'No Password. ';
}
if ($message !== '') {
	error_respond(401, $message);
}


$mysqli = get_mysqli();


$password = hash('sha256', $password);


//Check for dup email
$sql = "SELECT * FROM user_info WHERE email = '$email';";
$result_email = $mysqli->query($sql);
if (!$result_email) {
	error_respond(401, $mysqli->error);
}
if ($result_email->num_rows != 0) {
	error_respond(401, 'Email already exists.');
}


//Check for dup username
$sql = "SELECT * FROM user_info WHERE username = '$username';";

$result_user = $mysqli->query($sql);
if (!$result_user) {
	error_respond(401, $mysqli->error);
}
if ($result_user->num_rows != 0) {
	error_respond(401, 'Username already exists.');
}


$sql = "INSERT INTO
    	user_info(email, username, password, is_admin, budget)
		VALUES('$email', '$username', '$password', FALSE, NULL);";
$result_insert = $mysqli->query($sql);
if (!$result_insert) {
	error_respond(401, $mysqli->error);
}


$sql = "SELECT id FROM user_info WHERE email = '$email' AND username = '$username';";
$result = $mysqli->query($sql);
if (!$result) {
	error_respond(401, $mysqli->error);
}


$status_code = 200;
$message = 'Register Success.';
$response = [
	'status_code' => $status_code,
	'message' => $message,
	'user_id' => $result_user['id']
];

echo json_encode($response);
exit();

