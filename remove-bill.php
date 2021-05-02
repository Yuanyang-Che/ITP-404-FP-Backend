<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$bill_id = $data['bill_id'];

if (!isset($bill_id) && trim($bill_id) === '') {
	error_respond(401, 'No bill_id provided.');
}


$mysqli = get_mysqli();

$bill_select_result = check_bill_id($mysqli, $bill_id);

$bill = $bill_select_result->fetch_assoc();


$statement = $mysqli->prepare('
	DELETE FROM bill_info WHERE id = ?;');
$statement->bind_param('i', $bill_id);
$bill_delete_result = $statement->execute();
if (!$bill_delete_result) {
	error_respond(401, $mysqli->error);
}
if ($statement->affected_rows != 1) {
	error_respond(500, 'Delete Bill Failed. ');
}

$response = [
	'status_code' => 200,
	'message' => 'Remove Bill Success'
];

echo json_encode($response);