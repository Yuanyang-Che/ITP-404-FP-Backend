<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$bill_id = $data['bill_id'];
$is_favorite = $data['is_favorite'];

$message = '';
if (!isset($bill_id) || trim($bill_id) === '') {
	$message .= 'Missing bill_id. ';
}

if ($message !== '') {
	error_respond(401, $message);
}

$mysqli = get_mysqli();

check_bill_id($mysqli, $bill_id);

$statement = $mysqli->prepare("
	UPDATE bill_info
	SET favorite = ?
	WHERE id = ?;");
$statement->bind_param('ii', $is_favorite, $bill_id);
$update_bill_result = $statement->execute();
if (!$update_bill_result) {
	error_respond(401, $mysqli->error);
}
if ($statement->affected_rows != 1) {
	error_respond(500, 'Same Bill Info.');
}

$response = [
	'status_code' => 200,
	'message' => 'Toggle Bill Favorite Success.'
];

echo json_encode($response);