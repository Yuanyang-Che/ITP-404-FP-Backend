<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'];

if (!isset($user_id) && trim($user_id) === '') {
	error_respond(401, 'No user_id provided.');
}

$mysqli = get_mysqli();

$user_select_result = check_user_id($mysqli, $user_id);


$statement = $mysqli->prepare("
	SELECT amount
	FROM debt_info
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


$total_debt = 0.0;
foreach ($debt_select_result as $debt) {
	$total_debt += $debt['amount'];
}

$response = [
	'status_code' => 200,
	'message' => 'Get Total Debt Success. ',
	'total_bill' => $total_debt
];

echo json_encode($response);
