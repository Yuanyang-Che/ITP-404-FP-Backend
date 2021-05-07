<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];

if (!isset($user_id) && trim($user_id) === '') {
	error_respond(401, 'No user_id provided.');
}

$mysqli = get_mysqli();

check_user_id($mysqli, $user_id);


//The money user wants back in total
$total_debt = 0.0;

//How much does user gives out
$statement = $mysqli->prepare("
	SELECT amount
	FROM debt_info
	WHERE payer_id = ?;");
$statement->bind_param('i', $user_id);
$statement->execute();
$debt_select_result = $statement->get_result();
if (!$debt_select_result) {
	error_respond(401, $mysqli->error);
}
if ($debt_select_result->num_rows == 0) {
	error_respond(500, 'No Payer Debt. ');
}
foreach ($debt_select_result as $debt) {
	$total_debt += $debt['amount'];
}

//How much user takes in
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
	error_respond(500, 'No Receiver Debt. ');
}
foreach ($debt_select_result as $debt) {
	$total_debt -= $debt['amount'];
}


$response = [
	'status_code' => 200,
	'message' => 'Get Total Debt Success. ',
	'total_debt' => $total_debt
];

echo json_encode($response);
