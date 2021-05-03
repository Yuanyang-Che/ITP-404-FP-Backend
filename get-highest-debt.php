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
	SELECT amount, username
	FROM debt_info
		LEFT JOIN user_info ui ON ui.id = debt_info.receiver_id
	WHERE payer_id = ?;");
$statement->bind_param('i', $user_id);
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
	$debt = [
		'amount' => $row['amount'],
		'username' => $row['username']
	];
	array_push($debts, $debt);
}


$statement = $mysqli->prepare("
	SELECT amount
	FROM debt_info
	WHERE receiver_id = ?;");
$statement->bind_param('i', $user_id);
$statement->execute();
$debt_select_result = $statement->get_result();
if (!$debt_select_result) {
	error_respond(401, $mysqli->error);
}
if ($debt_select_result->num_rows == 0) {
	error_respond(500, 'No Debt. ');
}


for ($i = 0; $i < count($debts); $i++) {
	$debts[$i]['amount'] -= $debt_select_result->fetch_assoc()['amount'];
}

$max_index = 0;
for ($i = 0; $i < count($debts); $i++) {
	if ($debts[$i]['amount'] > $debts[$max_index]['amount']) {
		$max_index = $i;
	}
}

$response = [
	'status_code' => 200,
	'message' => 'Highest Debt Success',
	'username' => $debts[$max_index]['username'],
	'amount' => $debts[$max_index]['amount']
];

echo json_encode($response);