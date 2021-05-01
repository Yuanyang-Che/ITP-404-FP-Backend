<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$data = $_GET;

$user_id = $data['user_id'];
if (!isset($user_id) && trim($user_id) === '') {
	error_respond(401, 'No user_id provided.');
}

$mysqli = get_mysqli();

check_user_id($mysqli, $user_id);


$statement = $mysqli->prepare("SELECT bill_amount FROM bill_info WHERE payer_id = ?;");
$statement->bind_param('i', $user_id);
$statement->execute();
$bill_select_results = $statement->get_result();
if (!$bill_select_results) {
	error_respond(401, $mysqli->error);
}

$total_bill = 0;
foreach ($bill_select_results as $row) {
	$total_bill += $row['bill_amount'];
}

$response = [
	'status_code' => 200,
	'message' => 'Get total bill Success.',
	'total_bill' => $total_bill
];

echo json_encode($response);