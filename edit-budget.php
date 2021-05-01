<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];
$budget = $data['budget'];


$message = '';
if (!isset($user_id) && trim($user_id) === '') {
	$message .= 'No user_id provided.';
}
if (!isset($budget) && trim($budget) === '') {
	$message .= 'No budget provided.';
}
if ($message !== '') {
	error_respond(401, $message);
}


$mysqli = get_mysqli();


//Check if user exists
$sql = "SELECT budget FROM user_info WHERE id = $user_id;";
$budget_select_result = $mysqli->query($sql);
if (!$budget_select_result) {
	error_respond(401, $mysqli->error);
}
if ($budget_select_result->num_rows == 0) {
	error_respond(401, 'No such User.');
}


//Update the budget
$statement = $mysqli->prepare("UPDATE user_info
		                            	SET budget = ?
										WHERE id = ?;");
$statement->bind_param('ii', $budget, $user_id);

$budget_update_result = $statement->execute();
if (!$budget_update_result) {
	error_respond(401, $mysqli->error);
}
if ($statement->affected_rows != 1) {
	error_respond(401, 'Update Budget Failed.');
}


$response = [
	'status_code' => 200,
	'message' => 'Update Budget Success.',
	'budget' => $budget
];

echo json_encode($response);