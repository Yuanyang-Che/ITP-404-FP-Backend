<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];

if (!isset($user_id) && trim($user_id) === '') {
	error_respond(401, 'No user_id provided.');
}


$mysqli = get_mysqli();

$result_budget = check_user_id($mysqli, $user_id);

$budget = $result_budget->fetch_assoc()['budget'];
if ($budget === null) {
	error_respond(401, 'Null Budget.');
}


$response = [
	'status_code' => 200,
	'message' => 'Success.',
	'budget' => $budget
];

echo json_encode($response);