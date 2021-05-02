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
$statement = $mysqli->prepare("SELECT * FROM user_info WHERE email = ?;");
$statement->bind_param('s', $email);
$statement->execute();
$email_select_result = $statement->get_result();
if (!$email_select_result) {
	error_respond(401, $mysqli->error);
}
if ($email_select_result->num_rows != 0) {
	error_respond(401, 'Email already exists.');
}


//Check for dup username
$statement = $mysqli->prepare("SELECT * FROM user_info WHERE username = ?;");
$statement->bind_param('s', $username);
$statement->execute();
$user_select_result = $statement->get_result();
if (!$user_select_result) {
	error_respond(401, $mysqli->error);
}
if ($user_select_result->num_rows != 0) {
	error_respond(401, 'Username already exists.');
}


//Insert the new user record
$statement = $mysqli->prepare("
		INSERT INTO
		user_info(email, username, password, is_admin, budget)
		VALUES(?, ?, ?, FALSE, NULL);");
$statement->bind_param('sss', $email, $username, $password);
$user_insert_result = $statement->execute();
if (!$user_insert_result) {
	error_respond(401, $mysqli->error);
}
if ($statement->affected_rows != 1) {
	error_respond(401, 'Register failed.');
}


//Get the id of the new user
$statement = $mysqli->prepare("SELECT id FROM user_info WHERE email = ? AND username = ?;");
$statement->bind_param('ss', $email, $username);
$statement->execute();
$user_select_result = $statement->get_result();
if (!$user_select_result) {
	error_respond(401, $mysqli->error);
}
if ($user_select_result->num_rows != 1) {
	error_respond(401, 'Register failed.');
}


$status_code = 200;
$message = 'Register Success.';
$response = [
	'status_code' => $status_code,
	'message' => $message,
	'user_id' => $user_select_result->fetch_assoc()['id']
];

echo json_encode($response);
