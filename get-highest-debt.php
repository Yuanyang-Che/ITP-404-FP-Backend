<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];
if (!isset($user_id) && trim($user_id) === '') {
	error_respond(401, 'No user_id provided.');
}

$mysqli = get_mysqli();

check_user_id($mysqli, $user_id);


$statement = $mysqli->prepare("
	SELECT MAX(amount) AS amount
	FROM debt_info
	WHERE payer_id = ? OR receiver_id = ?;");
$statement->bind_param('ii', $user_id, $user_id);
$statement->execute();
$bill_select_results = $statement->get_result();
if (!$bill_select_results) {
	error_respond(401, $mysqli->error);
}
if ($bill_select_results->num_rows != 1) {
	error_respond(500, 'No Highest Debt. ');
}

$bill = $bill_select_results->fetch_assoc();

$response = [
	'status_code' => 200,
	'message' => 'Highest Debt Success',
	'user_id' => $user_id,
	'username' => $bill['username'],
	'amount' => $bill['amount']
];

echo json_encode($response);