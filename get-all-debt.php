<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];
if (!isset($user_id) && trim($user_id) === '') {
	error_respond(401, 'No user_id provided.');
}

$mysqli = get_mysqli();

$user_select_result = check_user_id($mysqli, $user_id);
$username = $user_select_result->fetch_assoc()['username'];

$statement = $mysqli->prepare("
	SELECT debt_info.id AS id, amount, u1.username AS payer_name, u2.username AS receiver_name
	FROM debt_info
		LEFT JOIN user_info u1 ON u1.id = debt_info.payer_id
		LEFT JOIN user_info u2 ON u2.id = debt_info.receiver_id
	WHERE payer_id = ?
	   OR receiver_id = ?;");
$statement->bind_param('ii', $user_id, $user_id);
$statement->execute();
$debt_select_result = $statement->get_result();
if (!$debt_select_result) {
	error_respond(401, $mysqli->error);
}
if ($debt_select_result->num_rows == 0) {
	error_respond(500, 'No Debt. ');
}


$debts = [];
foreach ($debt_select_result as $row) {
	$name = $row['payer_name'] != $username ? $row['payer_name'] : $row['receiver_name'];
	$debt = [
		'id' => $row['id'],
		'amount' => $row['amount'],
		'username' => $name,
	];
	array_push($debts, $debt);
}

$response = [
	'status_code' => 200,
	'debts' => $debts,
];

echo json_encode($response);