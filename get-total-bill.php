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
	SELECT *
	FROM bill_info
	WHERE payer_id = ?;");
$statement->bind_param('i', $user_id);
$statement->execute();
$bill_select_results = $statement->get_result();
if (!$bill_select_results) {
	error_respond(401, $mysqli->error);
}
if ($bill_select_results->num_rows == 0) {
	error_respond(500, 'No Bill for this user. ');
}


$total_bill = 0;
foreach ($bill_select_results as $bill) {
	$total_bill += $bill['amount'];
}

$response = [
	'status_code' => 200,
	'total_bill' => $total_bill
];

echo json_encode($response);