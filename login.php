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


$mysqli = get_mysqli();

$password = hash('sha256', $password);


$statement = $mysqli->prepare("SELECT * FROM user_info WHERE username = ? AND password = ?;");
$statement->bind_param('ss', $username, $password);
$statement->execute();
$user_select_result = $statement->get_result();
if (!$user_select_result) {
	error_respond(401, $mysqli->error);
}
if ($user_select_result->num_rows != 1) {
	error_respond(401, 'Incorrect User Name or Password');
}


//Success
$status_code = 200;
$message = 'Login Success.';
$response = [
	'status_code' => $status_code,
	'message' => $message,
	'user_id' => $user_select_result->fetch_assoc()['id']
];

echo json_encode($response);
