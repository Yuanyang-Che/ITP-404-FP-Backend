<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$payer_id = $data['user_id'];
$amount = $data['amount'];
$comment = isset($data['comment']) && trim($data['comment']) !== '' ? $data['comment'] : null;
$people = isset($data['people']) && trim($data['people']) !== '' ? $data['people'] : null;
$date = $data['date'];


$message = '';
if (!isset($payer_id) || trim($payer_id) === '') {
	$message .= 'Missing payer_id. ';
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

check_user_id($mysqli, $payer_id);


$statement = $mysqli->prepare("
	INSERT INTO bill_info(amount, payer_id, comment, people, date)
	VALUES (?, ?, ?, ?, ?);
");
$statement->bind_param('idsss', $payer_id, $amount, $comment, $peoplem, $date);
$bill_INSERT_result = $statement->execute();
if (!$bill_INSERT_result) {
	error_respond(401, $mysqli->error);
}

$response = [
	'status_code' => 200,
	'message' => 'New Bill Success. '
];

echo json_encode($response);