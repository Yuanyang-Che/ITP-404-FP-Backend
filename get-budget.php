<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];

if (!isset($user_id) && trim($user_id) === '') {
	error_respond(401, 'No user_id provided.');
}


$mysqli = get_mysqli();

$sql = "SELECT budget FROM user_info WHERE id = $user_id;";
$result_budget = $mysqli->query($sql);
if (!$result_budget) {
	error_respond(401, $mysqli->error);
}
if ($result_budget->num_rows == 0) {
	error_respond(401, 'No such User.');
}


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