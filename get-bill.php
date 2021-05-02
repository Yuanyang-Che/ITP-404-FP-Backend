<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

//$bill_id = $data['bill_id'];
$bill_id = 1;
if (!isset($bill_id) && trim($bill_id) === '') {
	error_respond(401, 'No bill_id provided.');
}

$mysqli = get_mysqli();

$bill_select_result = check_bill_id($mysqli, $bill_id);

$bill = $bill_select_result->fetch_assoc();

$response = [
	'status_code' => 200,
	'message' => 'Get Bill Success',
	'bill' => $bill
];

echo json_encode($response);