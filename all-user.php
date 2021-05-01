<?php

require 'config.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
	error_respond(401, $mysqli->error);
}

$sql = "SELECT id, username FROM user_info;";

$result_user = $mysqli->query($sql);
if (!$result_user) {
	error_respond(401, $mysqli->error);
}

$response = [
	'status_code' => 200,
	'message' => 'All User Success. ',
	'users' => ($result_user->fetch_all())
];

echo json_encode($response);
