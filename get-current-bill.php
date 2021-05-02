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
	SELECT id, amount, comment, people, date
	FROM bill_info
	WHERE payer_id = ?
	ORDER BY date DESC
	LIMIT 1;");
$statement->bind_param('i', $user_id);
$statement->execute();
$bill_select_results = $statement->get_result();
if (!$bill_select_results) {
	error_respond(401, $mysqli->error);
}
if ($bill_select_results->num_rows != 1) {
	error_respond(500, 'No Most Current Bill. ');
}

$bill = $bill_select_results->fetch_assoc();

$response = [
	'status_code' => 200,
	'message' => 'Most Current Bill Success. ',
	'bill_id' => $bill['id'],
	'bill_amount' => $bill['amount'],
	'comment' => $bill['comment'],
	'people' => $bill['people'],
	'date' => $bill['date'],
];

echo json_encode($response);