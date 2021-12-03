<?php
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

$debt_id = $data['debt_id'];

if (!isset($debt_id) && trim($debt_id) === '') {
	error_respond(401, 'No debt_id provided.');
}

$mysqli = get_mysqli();


$statement = $mysqli->prepare("
	SELECT receiver_id, amount
	FROM debt_info
	WHERE id = ?;");
$statement->bind_param('i', $debt_id);
$statement->execute();
$debt_select_result = $statement->get_result();
if (!$debt_select_result) {
	error_respond(401, $mysqli->error);
}
if ($debt_select_result->num_rows !== 1) {
	error_respond(500, 'No Such Debt. ');
}

$response = [
	'status_code' => 200,
	'debts' => $debt_select_result->fetch_assoc(),
];

echo json_encode($response);