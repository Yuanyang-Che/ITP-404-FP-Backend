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
	SELECT u1.id AS payer_id, u1.username AS payer_name, u2.id AS receiver_id, u2.username AS receiver_name, amount
	FROM debt_info
	LEFT JOIN user_info u1
	    ON u1.id = debt_info.payer_id
	LEFT JOIN user_info u2
	    ON u2.id = debt_info.receiver_id
	WHERE payer_id = ?
		OR receiver_id = ?
	ORDER BY amount DESC
	LIMIT 1;");
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
	'username' => $bill['payer_id'] == $user_id ? $bill['receiver_name'] : $bill['payer_name'],
	'amount' => $bill['amount']
];

echo json_encode($response);