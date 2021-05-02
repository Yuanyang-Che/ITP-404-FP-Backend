<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$payer_id = $data['payer_id'];
$receiver_id = $data['receiver_id'];
$amount = $data['amount'];


$message = '';
if (!isset($payer_id) || trim($payer_id) === '') {
	$message .= 'Missing payer_id. ';
}
if (!isset($receiver_id) || trim($receiver_id) === '') {
	$message .= 'Missing receiver_id. ';
}
if (!isset($amount) || trim($amount) === '') {
	$message .= 'Missing amount. ';
}
if ($message !== '') {
	error_respond(401, $message);
}


$mysqli = get_mysqli();

check_user_id($mysqli, $payer_id);
check_user_id($mysqli, $receiver_id);


$statement = $mysqli->prepare("SELECT * FROM debt_info WHERE payer_id = ? AND receiver_id = ?;");
$statement->bind_param('ii', $payer_id, $receiver_id);
$statement->execute();
$debt_select_result = $statement->get_result();
if (!$debt_select_result) {
	error_respond(401, $mysqli->error);
}

$new_amount = $amount;

//This is the first time this paying relationship is established
if ($debt_select_result->num_rows == 0) {
	//Create record
	$statement = $mysqli->prepare("
		INSERT INTO
		debt_info(payer_id, receiver_id, amount)
		VALUES (?, ?, ?);");
	$statement->bind_param('iid', $payer_id, $receiver_id, $new_amount);
	$debt_insert_result = $statement->execute();
	
	if (!$debt_insert_result) {
		error_respond(401, $mysqli->error);
	}
	if ($statement->affected_rows != 1) {
		error_respond(401, 'New Debt Failed.');
	}
}
//Update existing debt relationship
else {
	$new_amount += $debt_select_result->fetch_assoc()['amount'];
	
	//Update record
	$statement = $mysqli->prepare("
		UPDATE debt_info
		SET amount = ?
		WHERE payer_id = ? AND receiver_id = ?;");
	$statement->bind_param('dii', $new_amount, $payer_id, $receiver_id);
	$debt_update_result = $statement->execute();
	
	if (!$debt_update_result) {
		error_respond(401, $mysqli->error);
	}
}


$response = [
	'status_code' => 200,
	'message' => 'New Debt Success',
	'new_amount' => $new_amount
];

echo json_encode($response);