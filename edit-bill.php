<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$bill_id = $data['bill_id'];
$amount = $data['amount'];
$comment = isset($data['comment']) && trim($data['comment']) !== '' ? $data['comment'] : null;
$people = isset($data['people']) && trim($data['people']) !== '' ? $data['people'] : null;
$date = $data['date'];

$message = '';
if (!isset($bill_id) || trim($bill_id) === '') {
	$message .= 'Missing bill_id. ';
}
if (!isset($amount) || trim($amount) === '') {
	$message .= 'Missing Amount. ';
}
if (!isset($date) || trim($date) === '') {
	$message .= 'Missing date. ';
}
if ($message !== '') {
	error_respond(401, $message);
}


$mysqli = get_mysqli();

check_bill_id($mysqli, $bill_id);


$statement = $mysqli->prepare("
	UPDATE bill_info
	SET amount = ?, comment = ?, people = ?, date = ?
	WHERE id = ?;");
$statement->bind_param('dsssi', $amount, $comment, $people, $date, $bill_id);
$update_bill_result = $statement->execute();
if (!$update_bill_result) {
	error_respond(401, $mysqli->error);
}
if ($statement->affected_rows != 1) {
	error_respond(500, 'Same Bill Info.');
}

$response = [
	'status_code' => 200,
	'message' => 'Edit Bill Success.'
];

echo json_encode($response);